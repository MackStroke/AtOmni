<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamMember::query();

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->orderBy('order_column');
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $members = $query->get();
        return view('admin.team-members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.team-members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:4096',
            'order_column' => 'nullable|integer',
            'is_active' => 'boolean',
            'is_founding_member' => 'boolean',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $path = $request->file('photo')->store('team', 'public');
            
            try {
                // Optionally process standard size
                $manager = new ImageManager(new Driver());
                $img = $manager->read(public_path('storage/' . $path));
                $img->cover(400, 400); // square format for team
                $img->save(public_path('storage/' . $path));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Team image resize failed for $path: " . $e->getMessage());
            }

            $validated['photo_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_founding_member'] = $request->has('is_founding_member');
        unset($validated['photo']);

        TeamMember::create($validated);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member added successfully.');
    }

    public function edit(TeamMember $teamMember)
    {
        return view('admin.team-members.edit', compact('teamMember'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:4096',
            'order_column' => 'nullable|integer',
            'is_active' => 'boolean',
            'is_founding_member' => 'boolean',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($teamMember->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($teamMember->photo_path);
            }
            
            $path = $request->file('photo')->store('team', 'public');
            
            try {
                $manager = new ImageManager(new Driver());
                $img = $manager->read(public_path('storage/' . $path));
                $img->cover(400, 400);
                $img->save(public_path('storage/' . $path));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Team image resize failed for $path: " . $e->getMessage());
            }

            $validated['photo_path'] = $path;
        }

        // Handle checkbox which might be missing if unchecked
        $validated['is_active'] = $request->has('is_active');
        $validated['is_founding_member'] = $request->has('is_founding_member');
        unset($validated['photo']);

        $teamMember->update($validated);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member updated successfully.');
    }

    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($teamMember->photo_path);
        }
        $teamMember->delete();

        return redirect()->route('admin.team-members.index')->with('success', 'Team member deleted successfully.');
    }
}
