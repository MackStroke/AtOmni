@extends('layouts.app')

@php
    $siteName = \App\Models\Setting::get('site_name', 'Atomni');
@endphp
@section('title', 'AI Client Intake Automation | ' . $siteName)

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Service",
    "serviceType": "AI Client Intake Automation",
    "provider": {
        "@@type": "Organization",
        "name": "{{ e($siteName) }}"
    },
    "description": "Automate client intake workflows with AI, extracting data from emails and forms into your CRM."
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [{
        "@@type": "Question",
        "name": "How does the AI handle non-standard intake forms?",
        "acceptedAnswer": {
            "@@type": "Answer",
            "text": "Our AI can extract and normalize data even from unstructured emails, PDFs, and bespoke forms, mapping them consistently to your CRM fields."
        }
    }, {
        "@@type": "Question",
        "name": "What happens if the AI isn't sure about the extracted data?",
        "acceptedAnswer": {
            "@@type": "Answer",
            "text": "Atomni features a human-in-the-loop system. Low-confidence extractions are flagged for a team member to review and approve before hitting your CRM."
        }
    }]
}
</script>
@endsection

@section('content')

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="mb-4">
        <a href="{{ route('home') }}" class="text-electric hover:text-electric-light text-sm font-medium">&larr; Back to Home</a>
    </div>
    
    <h1 class="font-heading font-extrabold text-3xl sm:text-5xl text-text-primary leading-tight mb-6">AI Client Intake Automation</h1>
    
    <div class="glass-card rounded-2xl p-8 mb-12 border border-navy-700">
        <p class="text-lg text-text-secondary leading-relaxed">
            <strong>Direct Answer:</strong> AI client intake automation replaces manual data entry by automatically extracting information from incoming emails, PDFs, and web forms, validating the data, and syncing it directly to your CRM.
        </p>
    </div>

    <div class="prose prose-invert prose-lg max-w-none text-text-secondary mb-12">
        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">Who This Is For</h2>
        <p>Service businesses, law firms, real estate agencies, and consulting firms that receive a high volume of unstructured client inquiries and spend hours daily copying data into their CRM.</p>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">The Manual Workflow Problem</h2>
        <p>Manually processing client intake is slow and error-prone. A typical team member spends 15-20 minutes per inquiry opening emails, downloading attachments, identifying the relevant details, opening the CRM, creating a contact, and notifying the sales team.</p>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">How Atomni Automates the Workflow</h2>
        <p>Atomni acts as an intelligent layer between your inbox and your CRM:</p>
        <ul>
            <li><strong>Ingestion:</strong> Atomni monitors a dedicated intake inbox (e.g., <em>intake@yourcompany.com</em>).</li>
            <li><strong>Extraction:</strong> When a new email or document arrives, Atomni's AI extracts names, phone numbers, addresses, and specific case/project details.</li>
            <li><strong>Validation:</strong> The AI normalizes the data to match your CRM's required formats.</li>
            <li><strong>Human-in-the-loop:</strong> If confidence is below 95%, it goes to an approval queue. Otherwise, it pushes directly to your CRM and alerts your team on Slack.</li>
        </ul>

        <div class="bg-navy-900 rounded-xl p-6 border border-navy-700 my-8">
            <h3 class="text-xl font-bold text-text-primary mb-4">Before & After</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <h4 class="font-semibold text-red-400 mb-2">Before (Manual)</h4>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Read email</li>
                        <li>Open PDF attachment</li>
                        <li>Copy/paste details to CRM</li>
                        <li>Manually assign to team</li>
                    </ol>
                    <p class="mt-2 text-text-muted">Time: 15 mins per lead</p>
                </div>
                <div>
                    <h4 class="font-semibold text-green-400 mb-2">After (Atomni)</h4>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Email arrives</li>
                        <li>AI extracts & syncs data</li>
                        <li>Team gets instant alert</li>
                    </ol>
                    <p class="mt-2 text-text-muted">Time: 30 seconds per lead</p>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">Expected Outcomes</h2>
        <ul>
            <li>Save 10+ hours per week per intake coordinator.</li>
            <li>Reduce lead response time from hours to minutes.</li>
            <li>Zero manual data entry errors.</li>
        </ul>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">Frequently Asked Questions</h2>
        <div class="space-y-6 mt-6">
            <div>
                <h3 class="font-bold text-lg text-text-primary">How does the AI handle non-standard intake forms?</h3>
                <p>Our AI can extract and normalize data even from unstructured emails, PDFs, and bespoke forms, mapping them consistently to your CRM fields.</p>
            </div>
            <div>
                <h3 class="font-bold text-lg text-text-primary">What happens if the AI isn't sure about the extracted data?</h3>
                <p>Atomni features a human-in-the-loop system. Low-confidence extractions are flagged for a team member to review and approve before hitting your CRM.</p>
            </div>
        </div>
    </div>
    
    <div class="mt-12 text-center">
        <h3 class="text-2xl font-bold text-text-primary mb-4">Ready to automate your client intake?</h3>
        <a href="{{ route('contact') }}" class="inline-block px-8 py-4 rounded-xl bg-electric hover:bg-electric-light text-white font-bold text-lg shadow-lg transition-all hover:-translate-y-1">Request a Custom Demo</a>
    </div>
</section>

@endsection
