<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        // Auto-sync untracked files
        $this->syncUntrackedFiles();

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Media::with('uploader');

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }

        // Authors only see their own uploads
        if (auth()->user()->role === 'author') {
            $query->where('user_id', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->whereRaw('(file_name LIKE ? OR alt_text LIKE ?)', ["%{$search}%", "%{$search}%"]);
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $media = $query->paginate(24)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        $viewType = $request->input('view', 'grid');

        return view('admin.media.index', compact('media', 'viewType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,webm,mp3,wav,pdf,doc,docx|max:30720',
        ]);

        if (!$request->hasFile('files')) {
            // For ajax, return json, otherwise back
            if ($request->ajax() || $request->wantsJson()) return response()->json(['error' => 'No files selected.'], 400);
            return back()->with('error', 'No files selected.');
        }

        $manager = new ImageManager(new Driver());
        $count = 0;
        $uploadedMedia = [];

        foreach ($request->file('files') as $file) {
            $mimeType = $file->getMimeType();
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $safeName = Str::slug($filenameWithoutExt) . '-' . time() . '-' . Str::random(4);
            $isImage = Str::startsWith($mimeType, 'image/');
            $isSvgOrGif = in_array(strtolower($extension), ['svg', 'gif']);

            if ($isImage && !$isSvgOrGif) {
                // Process image via Intervention
                $image = $manager->read($file);

                // Auto-convert to webp exclusively and save
                $encodedWebp = $image->toWebp(85); // High quality webp
                $webpPath = 'images/' . $safeName . '.webp';
                \Illuminate\Support\Facades\Storage::disk('public')->put($webpPath, (string) $encodedWebp);
                
                $finalFilePath = $webpPath;
                $finalWebpPath = null;
                $finalMime = 'image/webp';
                $finalExt = 'webp';

                // We calculate size from the file_path we saved
                $sizeKb = \Illuminate\Support\Facades\Storage::disk('public')->size($finalFilePath) / 1024;
                
                $mediaRecord = Media::create([
                    'user_id'   => auth()->id(),
                    'file_path' => $finalFilePath,
                    'webp_path' => $finalWebpPath,
                    'file_name' => str_replace('.' . $extension, '.' . $finalExt, $originalName),
                    'mime_type' => $finalMime,
                    'size_kb'   => $sizeKb,
                    'width'     => $image->width(),
                    'height'    => $image->height(),
                ]);

            } else {
                // Video, audio, document, svg, gif
                $folder = 'uploads';
                if (Str::startsWith($mimeType, 'video/')) $folder = 'videos';
                elseif (Str::startsWith($mimeType, 'audio/')) $folder = 'audio';
                elseif (in_array($extension, ['svg', 'gif']) || Str::startsWith($mimeType, 'image/')) $folder = 'images';

                $path = $file->storeAs($folder, $safeName . '.' . $extension, 'public');
                $sizeKb = $file->getSize() / 1024;

                $mediaRecord = Media::create([
                    'user_id' => auth()->id(),
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'mime_type' => $mimeType,
                    'size_kb' => $sizeKb,
                ]);
            }
            
            $uploadedMedia[] = $mediaRecord;
            $count++;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} file(s) uploaded successfully.",
                'media' => $uploadedMedia
            ]);
        }

        return back()->with('success', "{$count} media file(s) uploaded successfully.");
    }

    public function update(Request $request, Media $medium)
    {
        $validated = $request->validate([
            'file_name' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $medium->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $medium]);
        }

        return back()->with('success', 'Media metadata updated.');
    }

    public function destroy(Media $medium)
    {
        // Authors can only delete their own uploads
        if (auth()->user()->role === 'author' && $medium->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own media.');
        }

        // Don't delete placeholders
        if (Str::contains($medium->file_path, 'placeholder')) {
            return back()->with('error', 'Cannot delete system placeholder assets.');
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($medium->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($medium->file_path);
        }
        if ($medium->webp_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($medium->webp_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($medium->webp_path);
        }

        $medium->delete();

        return back()->with('success', 'Media file entirely deleted successfully.');
    }

    public function destroyOriginal(Media $medium)
    {
        // Must have both paths and they must be different
        if ($medium->file_path && $medium->file_path !== $medium->webp_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($medium->file_path)) {
            // Delete original file
            \Illuminate\Support\Facades\Storage::disk('public')->delete($medium->file_path);
            
            // Update media record to only rely on WebP
            $medium->update([
                'file_path' => $medium->webp_path, // Fallback file_path to webp_path so it still exists
                'webp_path' => null, // Clear the secondary path so we know we only have one
                'mime_type' => 'image/webp', // Ensure format reads as WEBP
                'file_name' => pathinfo($medium->file_name, PATHINFO_FILENAME) . '.webp'
            ]);
            
            return back()->with('success', 'Original uncompressed file deleted. WebP version kept to save space.');
        }

        return back()->with('error', 'Original file could not be deleted or is already WebP only.');
    }

    /**
     * Helper to sync untracked files into the DB so old uploads don't disappear.
     */
    private function syncUntrackedFiles()
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $allFiles = $disk->allFiles();
        
        $existingPaths = Media::pluck('file_path')->toArray();

        foreach ($allFiles as $file) {
            if (Str::startsWith(basename($file), '.') || in_array($file, $existingPaths)) continue;

            // Insert placeholder record
            Media::create([
                'user_id' => auth()->id() ?: 1,
                'file_path' => $file,
                'file_name' => basename($file),
                'mime_type' => $disk->mimeType($file),
                'size_kb' => $disk->size($file) / 1024,
            ]);
        }
    }
}
