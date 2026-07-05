<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterSubscribeController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->input('email');

        // Check if already subscribed
        $existing = Newsletter::where('email', $email)->first();
        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are already subscribed!'], 200);
            }
            return back()->with('info', 'You are already subscribed!');
        }

        Newsletter::create(['email' => $email]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Successfully subscribed! Welcome aboard.'], 201);
        }

        return back()->with('success', 'Successfully subscribed!');
    }
}
