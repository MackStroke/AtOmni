<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = JobPosting::where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('closing_date')
                      ->orWhere('closing_date', '>', now());
            })
            ->latest()
            ->get();

        return view('pages.careers.index', compact('jobs'));
    }

    public function show(JobPosting $job)
    {
        if ($job->status !== 'published' || ($job->closing_date && $job->closing_date <= now())) {
            abort(404, 'Job posting not found or no longer active.');
        }

        return view('pages.careers.show', compact('job'));
    }

    public function apply(Request $request, JobPosting $job)
    {
        if ($job->status !== 'published' || ($job->closing_date && $job->closing_date <= now())) {
            return back()->with('error', 'This job posting is no longer active.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'portfolio_url' => 'nullable|url|max:255',
            'cover_letter' => 'nullable|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        $path = $request->file('resume')->store('private/resumes', 'local');

        JobApplication::create([
            'job_posting_id' => $job->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'portfolio_url' => $validated['portfolio_url'],
            'cover_letter' => $validated['cover_letter'],
            'resume_path' => $path,
            'status' => 'new',
        ]);

        return redirect()->route('careers.index')->with('success', 'Your application has been submitted successfully! We will be in touch soon.');
    }
}
