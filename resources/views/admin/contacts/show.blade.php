@extends('admin.layouts.app')
@section('title', 'Contact Query')
@section('page-title', 'Contact Query')
@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.contacts.index') }}" class="inline-flex items-center gap-1 text-sm text-text-muted hover:text-text-primary transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
        Back to all queries
    </a>

    <div class="glass-card rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-navy-700/30 flex items-center justify-between">
            <div>
                <h2 class="font-heading font-semibold text-text-primary">{{ data_get($contact, 'subject', 'No Subject') }}</h2>
                <p class="text-xs text-text-muted mt-0.5">Received {{ \Carbon\Carbon::parse(data_get($contact, 'created_at'))->format('F d, Y \a\t g:i A') }}</p>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide
                {{ data_get($contact, 'status') === 'new' ? 'bg-electric/15 text-electric' : (data_get($contact, 'status') === 'replied' ? 'bg-success/15 text-success' : 'bg-navy-600/30 text-text-muted') }}">
                {{ data_get($contact, 'status') }}
            </span>
        </div>

        <div class="px-6 py-5 space-y-5">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-text-muted uppercase tracking-wider mb-1">Name</p>
                    <p class="text-sm text-text-primary">{{ data_get($contact, 'name') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-text-muted uppercase tracking-wider mb-1">Email</p>
                    <a href="mailto:{{ data_get($contact, 'email') }}" class="text-sm text-electric hover:text-electric-light transition-colors">{{ data_get($contact, 'email') }}</a>
                </div>
            </div>

            <div>
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider mb-2">Message</p>
                <div class="p-4 rounded-lg bg-navy-800/30 border border-navy-700/20">
                    <p class="text-sm text-text-secondary leading-relaxed whitespace-pre-wrap">{{ data_get($contact, 'message') }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-navy-700/30 flex items-center justify-between">
            <a href="mailto:{{ data_get($contact, 'email') }}?subject=Re: {{ data_get($contact, 'subject') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-6 9 6v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8z"/></svg>
                Reply via Email
            </a>
            <form method="POST" action="{{ route('admin.contacts.destroy', data_get($contact, 'id', 1)) }}" onsubmit="return confirm('Delete this query?')">
                @csrf @method('DELETE')
                <button class="px-3 py-2 rounded-lg text-sm text-text-muted hover:text-alert-red hover:bg-alert-red/10 transition-colors">Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection
