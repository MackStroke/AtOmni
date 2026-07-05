<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function ping(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
        ]);

        $postId = $request->input('post_id');
        $post = Post::find($postId);

        if ($post) {
            // Increment reading time by 10 seconds (the beacon interval)
            $post->increment('total_reading_time_seconds', 10);

            // Track unique users for this post
            $sessionKey = 'post_unique_seen_' . $postId;
            if (!$request->session()->has($sessionKey)) {
                $request->session()->put($sessionKey, true);
                $post->increment('unique_users_count');
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
