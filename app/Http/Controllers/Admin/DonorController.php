<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        $query = Donor::query();
        
        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->orderBy('sort_order')->orderByDesc('donated_at');
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $donors = $query->get();
        return view('admin.donors.index', compact('donors'));
    }

    public function create()
    {
        return view('admin.donors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'social_link' => 'nullable|url|max:255',
            'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:4096',
            'sort_order' => 'nullable|integer',
            'donated_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('donors', 'public');
            
            try {
                $manager = new ImageManager(new Driver());
                $img = $manager->read(public_path('storage/' . $path));
                $img->cover(300, 300); // Masonry/Avatars
                $img->save(public_path('storage/' . $path));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Donor image resize failed for $path: " . $e->getMessage());
            }

            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        unset($validated['image']);

        Donor::create($validated);

        return redirect()->route('admin.donors.index')->with('success', 'Donor added successfully.');
    }

    public function edit(Donor $donor)
    {
        return view('admin.donors.edit', compact('donor'));
    }

    public function update(Request $request, Donor $donor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'social_link' => 'nullable|url|max:255',
            'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:4096',
            'sort_order' => 'nullable|integer',
            'donated_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($donor->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($donor->image_path);
            }
            
            $path = $request->file('image')->store('donors', 'public');
            
            try {
                $manager = new ImageManager(new Driver());
                $img = $manager->read(public_path('storage/' . $path));
                $img->cover(300, 300);
                $img->save(public_path('storage/' . $path));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Donor image resize failed for $path: " . $e->getMessage());
            }

            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        unset($validated['image']);

        $donor->update($validated);

        return redirect()->route('admin.donors.index')->with('success', 'Donor updated successfully.');
    }

    public function destroy(Donor $donor)
    {
        if ($donor->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($donor->image_path);
        }
        $donor->delete();

        return redirect()->route('admin.donors.index')->with('success', 'Donor deleted successfully.');
    }
}
