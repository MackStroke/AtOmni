<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = Newsletter::query();

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }

        if ($search = $request->input('search')) {
            $query->where('email', 'like', "%{$search}%");
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $subscribers = $query->paginate(25)->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        return redirect()->route('admin.newsletter.index')->with('success', 'Subscriber removed.');
    }
}
