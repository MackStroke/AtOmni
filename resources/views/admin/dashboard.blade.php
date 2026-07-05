@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">Total Posts</p>
        <p class="stat-number text-text-primary">{{ number_format($stats['total_posts']) }}</p>
        <p class="text-xs text-text-muted mt-1 truncate">{{ $stats['published_posts'] }} published · {{ $stats['draft_posts'] }} drafts</p>
    </div>
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">Total Views</p>
        <p class="stat-number text-text-primary">{{ number_format($stats['total_views']) }}</p>
    </div>
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">Subscribers</p>
        <p class="stat-number text-electric">{{ number_format($stats['subscribers']) }}</p>
    </div>
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">New Contacts</p>
        <p class="stat-number {{ $stats['new_contacts'] > 0 ? 'text-alert-red' : 'text-text-primary' }}">{{ $stats['new_contacts'] }}</p>
        @if($stats['pending_comments'] > 0)
            <p class="text-xs text-amber mt-1">{{ $stats['pending_comments'] }} comments pending</p>
        @endif
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    {{-- Recent Posts --}}
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-navy-700/30">
            <h2 class="font-heading font-semibold text-text-primary">Recent Posts</h2>
            <a href="{{ route('admin.posts.index') }}" class="text-xs text-electric hover:text-electric-light transition-colors focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-800 rounded-sm">View All →</a>
        </div>
        <div class="divide-y divide-navy-700/20">
            @if(is_countable($recentPosts) && count($recentPosts) > 0 || !is_countable($recentPosts) && $recentPosts)
                @foreach($recentPosts as $post)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-text-primary truncate">{{ $post->title }}</p>
                        <p class="text-xs text-text-muted">{{ $post->category?->name ?? 'Uncategorized' }} · {{ $post->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide
                        {{ $post->status === 'published' ? 'bg-success/15 text-success' : ($post->status === 'scheduled' ? 'bg-amber/15 text-amber' : 'bg-navy-600/30 text-text-muted') }}">
                        {{ $post->status }}
                    </span>
                </div>
                @endforeach
            @else
                <p class="px-5 py-6 text-sm text-text-muted text-center">No posts yet.</p>
            @endif
        </div>
    </div>

    {{-- Recent Contact Queries --}}
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-navy-700/30">
            <h2 class="font-heading font-semibold text-text-primary">Contact Queries</h2>
            <a href="{{ route('admin.contacts.index') }}" class="text-xs text-electric hover:text-electric-light transition-colors focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-800 rounded-sm">View All →</a>
        </div>
        <div class="divide-y divide-navy-700/20">
            @if(is_countable($recentContacts) && count($recentContacts) > 0 || !is_countable($recentContacts) && $recentContacts)
                @foreach($recentContacts as $contact)
                <a href="{{ route('admin.contacts.show', data_get($contact, 'id', 1)) }}" class="block px-5 py-3 hover:bg-navy-800/30 transition-colors focus:outline-none focus:bg-navy-800/50 focus:ring-2 focus:ring-inset focus:ring-electric">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-text-primary truncate">{{ data_get($contact, 'subject', 'No Subject') }}</p>
                            <p class="text-xs text-text-muted">{{ data_get($contact, 'name') }} · {{ \Carbon\Carbon::parse(data_get($contact, 'created_at'))->diffForHumans() }}</p>
                        </div>
                        @if(data_get($contact, 'status') === 'new')
                            <span class="shrink-0 w-2 h-2 rounded-full bg-electric animate-pulse"></span>
                        @endif
                    </div>
                </a>
                @endforeach
            @else
                <p class="px-5 py-6 text-sm text-text-muted text-center">No queries yet.</p>
            @endif
        </div>
    </div>
</div>

{{-- Recent Comments --}}
<div class="glass-card rounded-xl overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-navy-700/30">
        <h2 class="font-heading font-semibold text-text-primary">Recent Comments</h2>
    </div>
    <div class="divide-y divide-navy-700/20">
        @if(is_countable($recentComments) && count($recentComments) > 0 || !is_countable($recentComments) && $recentComments)
            @foreach($recentComments as $comment)
            <div class="px-5 py-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-medium text-text-primary">{{ $comment->displayName() }}</span>
                    <span class="text-xs text-text-muted">on "{{ \Illuminate\Support\Str::limit($comment->post?->title ?? 'Deleted', 40) }}"</span>
                    @unless($comment->is_approved)
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber/15 text-amber uppercase">Pending</span>
                    @endunless
                </div>
                <p class="text-sm text-text-secondary line-clamp-1">{{ $comment->comment_text }}</p>
            </div>
            @endforeach
        @else
            <p class="px-5 py-6 text-sm text-text-muted text-center">No comments yet.</p>
        @endif
    </div>
</div>

@endsection
