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

        // Extract unique upload month-years
        $dates = Media::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as value, DATE_FORMAT(created_at, '%M %Y') as label")
            ->groupBy('value', 'label')
            ->orderBy('value', 'desc')
            ->get();

        // Apply filters
        if ($dateFilter = $request->input('date')) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$dateFilter]);
        }

        if ($typeFilter = $request->input('type')) {
            if ($typeFilter === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($typeFilter === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($typeFilter === 'audio') {
                $query->where('mime_type', 'like', 'audio/%');
            } elseif ($typeFilter === 'document') {
                $query->where(function($q) {
                    $q->where('mime_type', 'like', 'application/%')
                      ->orWhere('mime_type', 'like', 'text/%')
                      ->orWhere(function($sub) {
                          $sub->where('mime_type', 'not like', 'image/%')
                              ->where('mime_type', 'not like', 'video/%')
                              ->where('mime_type', 'not like', 'audio/%');
                      });
                });
            }
        }

        // Apply search
        if ($search = $request->input('search')) {
            $query->whereRaw('(file_name LIKE ? OR alt_text LIKE ?)', ["%{$search}%", "%{$search}%"]);
        }

        // Authors only see their own uploads
        if (auth()->user()->role === 'author') {
            $query->where('user_id', auth()->id());
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'size_desc':
                $query->orderBy('size_kb', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('size_kb', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('file_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('file_name', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $media = $query->paginate(24)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        $viewType = $request->input('view', 'grid');

        return view('admin.media.index', compact('media', 'viewType', 'dates'));
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
            $isSvg = strtolower($extension) === 'svg' || $mimeType === 'image/svg+xml';

            if ($isImage && !$isSvg) {
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
                // Video, audio, document, svg
                $folder = 'uploads';
                if (Str::startsWith($mimeType, 'video/')) $folder = 'videos';
                elseif (Str::startsWith($mimeType, 'audio/')) $folder = 'audio';
                elseif ($extension === 'svg' || Str::startsWith($mimeType, 'image/')) $folder = 'images';

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

        $oldFileName = $medium->file_name;
        $newFileName = trim($validated['file_name']);
        
        $medium->alt_text = $validated['alt_text'];

        if ($newFileName && $newFileName !== $oldFileName) {
            // Sanitize extension
            $oldExt = pathinfo($oldFileName, PATHINFO_EXTENSION);
            $newExt = pathinfo($newFileName, PATHINFO_EXTENSION);
            
            // Force the original extension if missing or different
            if (strtolower($newExt) !== strtolower($oldExt)) {
                $newFileName = pathinfo($newFileName, PATHINFO_FILENAME) . '.' . $oldExt;
            }
            
            // Clean name
            $newNameWithoutExt = Str::slug(pathinfo($newFileName, PATHINFO_FILENAME));
            $finalNewName = $newNameWithoutExt . '.' . $oldExt;
            
            // Determine directory
            $dir = dirname($medium->file_path);
            if ($dir === '.' || $dir === '/') {
                $dir = 'uploads';
                if (Str::startsWith($medium->mime_type, 'image/')) $dir = 'images';
                elseif (Str::startsWith($medium->mime_type, 'video/')) $dir = 'videos';
                elseif (Str::startsWith($medium->mime_type, 'audio/')) $dir = 'audio';
            }
            
            $newFilePath = ($dir ? $dir . '/' : '') . $finalNewName;
            
            // Move original file physically on disk
            $oldFilePath = $medium->file_path;
            $oldWebpPath = $medium->webp_path;
            $newWebpPath = null;

            if ($newFilePath !== $oldFilePath) {
                if (Storage::disk('public')->exists($oldFilePath)) {
                    if (Storage::disk('public')->exists($newFilePath)) {
                        $finalNewName = $newNameWithoutExt . '-' . time() . '.' . $oldExt;
                        $newFilePath = ($dir ? $dir . '/' : '') . $finalNewName;
                    }
                    Storage::disk('public')->move($oldFilePath, $newFilePath);
                }
                $medium->file_path = $newFilePath;
                $medium->file_name = $finalNewName;
            }
            
            // Move WebP version if it exists
            if ($oldWebpPath) {
                $webpDir = dirname($oldWebpPath);
                $newWebpPath = ($webpDir === '.' ? '' : $webpDir . '/') . $newNameWithoutExt . '.webp';
                if ($newWebpPath !== $oldWebpPath) {
                    if (Storage::disk('public')->exists($oldWebpPath)) {
                        if (Storage::disk('public')->exists($newWebpPath)) {
                            $newWebpPath = ($webpDir === '.' ? '' : $webpDir . '/') . $newNameWithoutExt . '-' . time() . '.webp';
                        }
                        Storage::disk('public')->move($oldWebpPath, $newWebpPath);
                    }
                    $medium->webp_path = $newWebpPath;
                }
            }

            // Sync post references in DB
            $this->updatePostReferences($oldFilePath, $medium->file_path, $oldWebpPath, $medium->webp_path);
        }

        $medium->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $medium]);
        }

        return back()->with('success', 'Media metadata updated.');
    }

    public function crop(Request $request, Media $medium)
    {
        $request->validate([
            'image' => 'required|string', // base64 representation of cropped canvas
        ]);

        if (!str_starts_with($medium->mime_type, 'image/')) {
            return response()->json(['error' => 'Media is not an image.'], 400);
        }

        $base64Data = $request->input('image');
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }
        $decodedData = base64_decode($base64Data);

        if (!$decodedData) {
            return response()->json(['error' => 'Invalid image data.'], 400);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($decodedData);
        
        $extension = pathinfo($medium->file_path, PATHINFO_EXTENSION);
        $isWebp = strtolower($extension) === 'webp';
        
        if ($isWebp) {
            $encoded = $image->toWebp(85);
        } else {
            $encoded = match(strtolower($extension)) {
                'png' => $image->toPng(),
                'gif' => $image->toGif(),
                default => $image->toJpeg(85),
            };
        }

        // Overwrite file_path
        Storage::disk('public')->put($medium->file_path, (string) $encoded);

        // Overwrite webp_path if it exists separately
        if ($medium->webp_path && $medium->webp_path !== $medium->file_path) {
            $encodedWebp = $image->toWebp(85);
            Storage::disk('public')->put($medium->webp_path, (string) $encodedWebp);
        }

        // Recalculate size and dimensions
        $sizeKb = Storage::disk('public')->size($medium->file_path) / 1024;
        
        $medium->update([
            'size_kb' => (int) $sizeKb,
            'width' => $image->width(),
            'height' => $image->height(),
        ]);

        return response()->json([
            'success' => true,
            'media' => $medium,
            'url' => asset('storage/' . $medium->optimizedPath()) . '?v=' . time(),
        ]);
    }

    public function usage(Request $request, Media $medium)
    {
        $filePath = $medium->file_path;
        $webpPath = $medium->webp_path;

        // Search for posts containing this media in featured_image or content
        $postsQuery = \App\Models\Post::select('id', 'title', 'slug', 'featured_image', 'content')
            ->where(function($query) use ($filePath, $webpPath) {
                $query->where('featured_image', $filePath)
                    ->orWhere('featured_image', 'storage/' . $filePath)
                    ->orWhere('featured_image', '/' . $filePath)
                    ->orWhere('featured_image', '/storage/' . $filePath);

                if ($webpPath) {
                    $query->orWhere('featured_image', $webpPath)
                        ->orWhere('featured_image', 'storage/' . $webpPath)
                        ->orWhere('featured_image', '/' . $webpPath)
                        ->orWhere('featured_image', '/storage/' . $webpPath);
                }
            })
            ->orWhere('content', 'like', "%{$filePath}%");

        if ($webpPath) {
            $postsQuery->orWhere('content', 'like', "%{$webpPath}%");
        }

        $posts = $postsQuery->get()->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'edit_url' => route('admin.posts.edit', $post->id),
            ];
        });

        return response()->json([
            'success' => true,
            'posts' => $posts,
        ]);
    }

    private function updatePostReferences($oldPath, $newPath, $oldWebpPath = null, $newWebpPath = null)
    {
        // 1. Update featured_image references
        \App\Models\Post::where('featured_image', $oldPath)
            ->orWhere('featured_image', 'storage/' . $oldPath)
            ->orWhere('featured_image', '/' . $oldPath)
            ->orWhere('featured_image', '/storage/' . $oldPath)
            ->update(['featured_image' => $newPath]);

        if ($oldWebpPath && $newWebpPath) {
            \App\Models\Post::where('featured_image', $oldWebpPath)
                ->orWhere('featured_image', 'storage/' . $oldWebpPath)
                ->orWhere('featured_image', '/' . $oldWebpPath)
                ->orWhere('featured_image', '/storage/' . $oldWebpPath)
                ->update(['featured_image' => $newWebpPath]);
        }

        // 2. Update content HTML body references
        $posts = \App\Models\Post::where('content', 'like', "%{$oldPath}%")
            ->orWhere(function($query) use ($oldWebpPath) {
                if ($oldWebpPath) {
                    $query->where('content', 'like', "%{$oldWebpPath}%");
                }
            })->get();

        foreach ($posts as $post) {
            $updatedContent = str_replace($oldPath, $newPath, $post->content);
            if ($oldWebpPath && $newWebpPath) {
                $updatedContent = str_replace($oldWebpPath, $newWebpPath, $updatedContent);
            }

            $oldUrl = asset('storage/' . $oldPath);
            $newUrl = asset('storage/' . $newPath);
            $updatedContent = str_replace($oldUrl, $newUrl, $updatedContent);

            if ($oldWebpPath && $newWebpPath) {
                $oldWebpUrl = asset('storage/' . $oldWebpPath);
                $newWebpUrl = asset('storage/' . $newWebpPath);
                $updatedContent = str_replace($oldWebpUrl, $newWebpUrl, $updatedContent);
            }

            $post->update(['content' => $updatedContent]);
        }
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
