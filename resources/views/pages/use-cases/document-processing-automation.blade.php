@extends('layouts.app')

@php
    $siteName = \App\Models\Setting::get('site_name', 'Atomni');
@endphp
@section('title', 'AI Document Processing Automation | ' . $siteName)

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Service",
    "serviceType": "AI Document Processing Automation",
    "provider": {
        "@@type": "Organization",
        "name": "{{ e($siteName) }}"
    },
    "description": "Automate document review, extraction, and summarization with AI."
}
</script>
@endsection

@section('content')

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="mb-4">
        <a href="{{ route('home') }}" class="text-electric hover:text-electric-light text-sm font-medium">&larr; Back to Home</a>
    </div>
    
    <h1 class="font-heading font-extrabold text-3xl sm:text-5xl text-text-primary leading-tight mb-6">AI Document Processing Automation</h1>
    
    <div class="glass-card rounded-2xl p-8 mb-12 border border-navy-700">
        <p class="text-lg text-text-secondary leading-relaxed">
            <strong>Direct Answer:</strong> AI document processing automation uses large language models and vision AI to read, understand, and extract structured data from complex documents like contracts, invoices, and legal briefs.
        </p>
    </div>

    <div class="prose prose-invert prose-lg max-w-none text-text-secondary mb-12">
        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">Who This Is For</h2>
        <p>Operations teams, legal departments, and financial services firms that spend substantial time reviewing documents, verifying clauses, or extracting key terms.</p>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">The Manual Workflow Problem</h2>
        <p>Extracting data from 50-page PDFs requires intense human focus and is highly susceptible to fatigue-induced errors. It creates massive bottlenecks in operations and delays client deliverables.</p>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">How Atomni Automates the Workflow</h2>
        <p>Atomni converts unstructured documents into structured, actionable data:</p>
        <ul>
            <li><strong>Upload & OCR:</strong> Documents are securely uploaded. Scanned documents are parsed via advanced OCR.</li>
            <li><strong>Semantic Extraction:</strong> The AI extracts specific clauses, amounts, dates, and parties based on custom rules, not just rigid templates.</li>
            <li><strong>Summarization:</strong> A concise summary of the document is generated for quick review.</li>
            <li><strong>Human Review:</strong> Team members verify the extracted data side-by-side with the original document in our review UI before finalizing.</li>
        </ul>

        <h2 class="text-2xl font-bold text-text-primary mt-8 mb-4">Expected Outcomes</h2>
        <ul>
            <li>Process documents 10x faster.</li>
            <li>Standardize data extraction across all team members.</li>
            <li>Free up senior staff from tedious review tasks.</li>
        </ul>
    </div>
    
    <div class="mt-12 text-center">
        <h3 class="text-2xl font-bold text-text-primary mb-4">Stop manually reading every page.</h3>
        <a href="{{ route('contact') }}" class="inline-block px-8 py-4 rounded-xl bg-electric hover:bg-electric-light text-white font-bold text-lg shadow-lg transition-all hover:-translate-y-1">Request a Custom Demo</a>
    </div>
</section>

@endsection
