<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobPosting::query();

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $posts = $query->paginate(20);
        return view('admin.careers.jobs.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.careers.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string|in:full-time,part-time,contract',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'status' => 'required|string|in:draft,published,closed',
            'closing_date' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();

        JobPosting::create($validated);

        return redirect()->route('admin.careers.jobs.index')
            ->with('success', 'Job posting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPosting $job)
    {
        return view('admin.careers.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPosting $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string|in:full-time,part-time,contract',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'status' => 'required|string|in:draft,published,closed',
            'closing_date' => 'nullable|date',
        ]);

        $job->update($validated);

        return redirect()->route('admin.careers.jobs.index')
            ->with('success', 'Job posting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPosting $job)
    {
        $job->delete();

        return redirect()->route('admin.careers.jobs.index')
            ->with('success', 'Job posting deleted successfully.');
    }
}
