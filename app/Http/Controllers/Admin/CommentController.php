<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Comment::with(['post', 'user']);

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }

        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $comments = $query->paginate(20)->withQueryString();
        
        return view('admin.comments.index', compact('comments', 'status'));
    }

    public function toggleApprove(Comment $comment)
    {
        $comment->update(['is_approved' => !$comment->is_approved]);
        $status = $comment->is_approved ? 'approved' : 'unapproved';
        return back()->with('success', "Comment has been {$status}.");
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }
}
