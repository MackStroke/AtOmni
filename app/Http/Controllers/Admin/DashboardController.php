<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Newsletter;
use App\Models\ContactQuery;
use App\Models\Comment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAuthor = $user->role === 'author';

        // Stats scoped to role
        if ($isAuthor) {
            $stats = [
                'total_posts'      => Post::where('author_id', $user->id)->count(),
                'published_posts'  => Post::where('author_id', $user->id)->where('status', 'published')->count(),
                'draft_posts'      => Post::where('author_id', $user->id)->where('status', 'draft')->count(),
                'total_views'      => Post::where('author_id', $user->id)->sum('views_count'),
                'subscribers'      => null,
                'new_contacts'     => null,
                'pending_comments' => null,
            ];
            $recentPosts    = Post::with('category')->where('author_id', $user->id)->latest()->take(5)->get();
            $recentContacts = collect();
            $recentComments = collect();
        } else {
            $stats = [
                'total_posts'      => Post::count(),
                'published_posts'  => Post::where('status', 'published')->count(),
                'draft_posts'      => Post::where('status', 'draft')->count(),
                'total_views'      => Post::sum('views_count'),
                'subscribers'      => Newsletter::count(),
                'new_contacts'     => ContactQuery::where('status', 'new')->count(),
                'pending_comments' => Comment::where('is_approved', false)->count(),
            ];
            $recentPosts    = Post::with('category')->latest()->take(5)->get();
            $recentContacts = ContactQuery::latest()->take(5)->get();
            $recentComments = Comment::with('post')->latest()->take(5)->get();
        }

        return view('admin.dashboard', compact('stats', 'recentPosts', 'recentContacts', 'recentComments'));
    }
}
