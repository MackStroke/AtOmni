@extends('layouts.app')

@php
    $siteName = \App\Models\Setting::get('site_name', 'Atomni');
@endphp
@section('title', 'Atomni vs Zapier for AI Workflow Automation | ' . $siteName)

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Atomni vs Zapier for AI Workflow Automation",
    "description": "A detailed comparison of Atomni and Zapier for business process automation, focusing on unstructured data and human-in-the-loop workflows."
}
</script>
@endsection

@section('content')

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="mb-4">
        <a href="{{ route('home') }}" class="text-electric hover:text-electric-light text-sm font-medium">&larr; Back to Home</a>
    </div>
    
    <h1 class="font-heading font-extrabold text-3xl sm:text-5xl text-text-primary leading-tight mb-6">Atomni vs Zapier for AI Workflow Automation</h1>
    
    <div class="glass-card rounded-2xl p-8 mb-12 border border-navy-700">
        <p class="text-lg text-text-secondary leading-relaxed">
            <strong>Comparison Summary:</strong> Zapier is the industry standard for moving structured data between APIs (e.g., "When a Stripe charge succeeds, post to Slack"). Atomni is built for complex, unstructured service workflows (e.g., "Read this 10-page PDF, extract these 5 specific clauses, verify them against our CRM, and ask a human if the confidence is low").
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="glass-card rounded-2xl p-8 border border-navy-700">
            <h2 class="text-2xl font-bold text-text-primary mb-4">Who Zapier is Best For</h2>
            <ul class="space-y-2 text-text-secondary list-disc list-inside">
                <li>Solo founders and marketers.</li>
                <li>Simple data syncing between SaaS tools.</li>
                <li>Workflows that don't require human review.</li>
                <li>Companies with structured, predictable data.</li>
            </ul>
        </div>
        <div class="glass-card rounded-2xl p-8 border border-electric/50 bg-electric/5">
            <h2 class="text-2xl font-bold text-text-primary mb-4">Who Atomni is Best For</h2>
            <ul class="space-y-2 text-text-secondary list-disc list-inside">
                <li>Service businesses, agencies, and law firms.</li>
                <li>Workflows involving emails, PDFs, and unstructured text.</li>
                <li>Processes that require human-in-the-loop validation.</li>
                <li>Teams looking for a managed automation partner.</li>
            </ul>
        </div>
    </div>

    <div class="prose prose-invert prose-lg max-w-none text-text-secondary mb-12">
        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-6">Feature Comparison</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-navy-700">
                        <th class="py-4 px-4 font-bold text-text-primary">Feature</th>
                        <th class="py-4 px-4 font-bold text-text-primary">Atomni</th>
                        <th class="py-4 px-4 font-bold text-text-primary">Zapier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-700 text-sm">
                    <tr>
                        <td class="py-4 px-4 font-medium text-text-primary">Best Data Type</td>
                        <td class="py-4 px-4 text-green-400">Unstructured (Emails, PDFs, Free-text)</td>
                        <td class="py-4 px-4 text-text-secondary">Structured (API JSON, Webhooks)</td>
                    </tr>
                    <tr>
                        <td class="py-4 px-4 font-medium text-text-primary">Human-in-the-Loop Review</td>
                        <td class="py-4 px-4 text-green-400">Built-in UI for review queues</td>
                        <td class="py-4 px-4 text-red-400">Requires third-party tools or email steps</td>
                    </tr>
                    <tr>
                        <td class="py-4 px-4 font-medium text-text-primary">Implementation</td>
                        <td class="py-4 px-4 text-green-400">Managed (Done For You)</td>
                        <td class="py-4 px-4 text-text-secondary">DIY (Do It Yourself)</td>
                    </tr>
                    <tr>
                        <td class="py-4 px-4 font-medium text-text-primary">Complex Branching Logic</td>
                        <td class="py-4 px-4 text-green-400">Native AI decision making</td>
                        <td class="py-4 px-4 text-text-secondary">Rigid path rules</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="text-2xl font-bold text-text-primary mt-12 mb-4">Workflow Complexity & Limitations</h2>
        <p>Zapier excels at volume and breadth, connecting over 5,000 apps. However, when a workflow involves reading context, making a subjective decision, and verifying accuracy, Zapier breaks down. You end up stringing together OpenAI steps, formatter steps, and email steps that are incredibly fragile.</p>
        <p>Atomni is designed specifically for this complexity. We handle the edge cases gracefully by routing uncertain AI decisions to your team's review dashboard, ensuring that automation never degrades your client experience.</p>
    </div>
    
    <div class="mt-12 text-center glass-card p-12 rounded-2xl border border-electric/30">
        <h3 class="text-2xl font-bold text-text-primary mb-4">Ready to handle complex automation?</h3>
        <p class="text-text-secondary mb-8 max-w-2xl mx-auto">Stop fighting with fragile Zaps. Let Atomni build reliable, intelligent workflows for your service business.</p>
        <a href="{{ route('contact') }}" class="inline-block px-8 py-4 rounded-xl bg-electric hover:bg-electric-light text-white font-bold text-lg shadow-lg transition-all hover:-translate-y-1">Request a Custom Demo</a>
    </div>
</section>

@endsection
