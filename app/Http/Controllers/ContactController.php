<?php

namespace App\Http\Controllers;

use App\Models\ContactQuery;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Handle the incoming contact/sponsorship request.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'ad_type' => 'nullable|string|max:255',
        ]);

        // Append ad_type to message if present (e.g., from Advertise page)
        $messageBody = $validated['message'];
        if (!empty($validated['ad_type'])) {
            $messageBody = "**Interested Ad Area:** " . $validated['ad_type'] . "\n\n" . $messageBody;
        }

        ContactQuery::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? 'General Inquiry',
            'message' => $messageBody,
        ]);

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will get back to you shortly.');
    }
}
