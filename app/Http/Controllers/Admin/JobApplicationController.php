<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobApplication::with('jobPosting');

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }
        
        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }
        
        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $applications = $query->paginate(20);
        return view('admin.careers.applications.index', compact('applications'));
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $application)
    {
        $application->load('jobPosting');
        return view('admin.careers.applications.show', compact('application'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,reviewing,interviewing,rejected,hired',
        ]);

        $application->update($validated);

        return back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplication $application)
    {
        $application->delete();

        return redirect()->route('admin.careers.applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    /**
     * Download the applicant's resume.
     */
    public function downloadResume(JobApplication $application)
    {
        if (!$application->resume_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($application->resume_path)) {
            abort(404, 'Resume file not found.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($application->resume_path);
    }
}
