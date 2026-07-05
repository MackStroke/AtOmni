<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('email')
                ], 422);
            }
            return back()->with('subscribe_error', $validator->errors()->first('email'));
        }

        $email = $request->input('email');
        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'unsubscribed') {
                $subscriber->update(['status' => 'active', 'ip_address' => $request->ip()]);
                $message = 'Welcome back! You have been re-subscribed to our newsletter.';
            } else {
                $message = 'You are already subscribed to our newsletter!';
            }
        } else {
            Subscriber::create([
                'email' => $email,
                'status' => 'active',
                'ip_address' => $request->ip(),
            ]);
            $message = 'Successfully subscribed to our newsletter!';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('subscribe_success', $message);
    }
}
