@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $comments */
@endphp
@extends('admin.layouts.app')
@section('title', 'Comments')
@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h1 class="page-title text-3xl font-bold tracking-tight text-text-primary">Comments</h1>
</div>

{{-- Status filter chips --}}
<div class="chip-row mb-5">
    <a href="{{ route('admin.comments.index') }}"
       class="shrink-0 px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap
              {{ $status === 'all' ? 'bg-navy-900/80 text-text-primary shadow border border-navy-700/30' : 'text-text-muted hover:text-text-primary hover:bg-navy-900/40' }}">
        All
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
       class="shrink-0 px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap
              {{ $status === 'pending' ? 'bg-amber/20 text-amber shadow border border-amber/30' : 'text-text-muted hover:text-text-primary hover:bg-navy-900/40' }}">
        Pending
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
       class="shrink-0 px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap
              {{ $status === 'approved' ? 'bg-accent-blue text-white shadow' : 'text-text-muted hover:text-text-primary hover:bg-navy-900/40' }}">
        Approved
    </a>
</div>

{{-- ═══════════════════════════════
     MOBILE: Card list (hidden md+)
═══════════════════════════════ --}}
<div class="space-y-2.5 md:hidden">
    @if(is_countable($comments) && count($comments) > 0 || !is_countable($comments) && $comments)
        @foreach($comments as $comment)
        <div class="glass-card rounded-xl overflow-hidden">
            {{-- Author + meta row --}}
            <div class="flex items-start gap-3 p-3 min-w-0">
                {{-- Avatar --}}
                <div class="shrink-0 w-9 h-9 rounded-xl bg-accent-blue/10 text-accent-blue flex items-center justify-center font-bold text-sm">
                    {{ substr($comment->user?->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0 overflow-hidden">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-text-primary truncate">{{ $comment->user?->name ?? 'Anonymous' }}</span>
                        @if($comment->post && $comment->user_id && $comment->user_id === $comment->post->author_id)
                            <span class="shrink-0 text-[9px] font-bold px-1.5 py-px rounded bg-accent-blue/20 text-accent-blue uppercase">Author</span>
                        @endif
                        @if(!$comment->is_approved)
                            <span class="shrink-0 text-[9px] font-bold px-1.5 py-px rounded bg-amber/15 text-amber uppercase">Pending</span>
                        @else
                            <span class="shrink-0 text-[9px] font-bold px-1.5 py-px rounded bg-success/15 text-success uppercase">Approved</span>
                        @endif
                    </div>
                    {{-- Comment text --}}
                    <p class="text-xs text-text-secondary mt-1 line-clamp-2 italic">"{{ $comment->comment_text }}"</p>
                    {{-- Post link --}}
                    @if($comment->post)
                        <p class="text-[10px] text-text-muted mt-1 truncate">
                            on: <span class="text-electric">{{ $comment->post->title }}</span>
                        </p>
                    @else
                        <p class="text-[10px] text-text-muted mt-1 italic">Post deleted</p>
                    @endif
                    <p class="text-[9px] text-text-muted mt-0.5 cursor-help" title="{{ $comment->created_at->format('M d, Y h:i A') }}">{{ $comment->created_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Action grid --}}
            <div class="grid grid-cols-2 border-t border-navy-700/30 divide-x divide-navy-700/30 w-full">
                {{-- Approve / Unapprove --}}
                <form action="{{ route('admin.comments.toggle-approve', $comment) }}" method="POST" class="contents">
                    @csrf
                    <button class="flex flex-col items-center justify-center gap-0.5 py-2.5 w-full transition-colors
                                   {{ $comment->is_approved ? 'text-amber hover:bg-amber/10' : 'text-success hover:bg-success/10' }}">
                        @if($comment->is_approved)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[9px] font-medium">Unapprove</span>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[9px] font-medium">Approve</span>
                        @endif
                    </button>
                </form>

                {{-- Delete --}}
                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="contents"
                      onsubmit="return confirm('Delete this comment?')">
                    @csrf @method('DELETE')
                    <button class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-alert-red hover:bg-alert-red/10 transition-colors w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span class="text-[9px] font-medium">Delete</span>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    @else
        <div class="glass-card rounded-xl p-10 text-center">
            <p class="text-text-muted text-sm">No comments found.</p>
        </div>
    @endif
</div>

{{-- ═══════════════════════════════
     DESKTOP: Table (hidden below md)
═══════════════════════════════ --}}
<div class="hidden md:block">
    <x-admin.bulk-actions resource="comments" :actions="['delete' => 'Delete', 'approve' => 'Approve', 'unapprove' => 'Unapprove']" />
    <div class="glass-card rounded-2xl overflow-hidden mt-3">
        <div class="table-scroll-container">
            <table class="w-full text-sm text-left">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th scope="col" class="px-6 py-4 font-bold w-1/4"><x-admin.sort-header column="user_id" label="Author" /></th>
                        <th scope="col" class="px-6 py-4 font-bold">Comment</th>
                        <th scope="col" class="px-6 py-4 font-bold w-1/4"><x-admin.sort-header column="post_id" label="Post" /></th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/10">
                @if(is_countable($comments) && count($comments) > 0 || !is_countable($comments) && $comments)
                    @foreach($comments as $comment)
                    <tr class="transition-colors hover:bg-navy-950/30 group">
                        <td class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $comment->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-2xl bg-accent-blue/10 text-accent-blue flex items-center justify-center font-bold relative overflow-hidden">
                                    <div class="absolute inset-0 bg-accent-blue/5 animate-pulse"></div>
                                    <span class="relative z-10">{{ substr($comment->user?->name ?? 'A', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-bold text-text-primary">{{ $comment->user?->name ?? 'Anonymous' }}</p>
                                        @if($comment->post && $comment->user_id && $comment->user_id === $comment->post->author_id)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-accent-blue/10 text-accent-blue border border-accent-blue/20">Post Author</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-text-muted uppercase tracking-tight mt-0.5 cursor-help" title="{{ $comment->created_at->format('M d, Y h:i A') }}">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/3">
                            <p class="text-text-secondary line-clamp-2 italic text-sm">"{{ $comment->comment_text }}"</p>
                            <div class="mt-3">
                                @if(!$comment->is_approved)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                        <span class="w-1 h-1 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500 mr-1.5"></span> Approved
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($comment->post)
                                <a href="{{ route('frontend.article', $comment->post->slug) }}" target="_blank"
                                   class="text-accent-blue hover:underline font-bold line-clamp-2">
                                    {{ $comment->post->title }}
                                </a>
                            @else
                                <span class="text-text-muted italic text-xs">Post Deleted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <form action="{{ route('admin.comments.toggle-approve', $comment) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" title="{{ $comment->is_approved ? 'Unapprove' : 'Approve' }}"
                                            class="p-2 rounded-xl transition-all {{ $comment->is_approved ? 'text-amber-500/70 hover:text-amber-500 hover:bg-amber-500/10' : 'text-emerald-500/70 hover:text-emerald-500 hover:bg-emerald-500/10' }}">
                                        @if($comment->is_approved)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Delete this comment?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-rose-500/70 hover:text-rose-500 hover:bg-rose-500/10 transition-all" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-16 h-16 mb-6 flex items-center justify-center rounded-2xl bg-navy-950/40 text-text-muted border border-navy-700/20">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                <p class="text-xl font-bold text-text-primary">No comments found</p>
                                <p class="text-sm text-text-muted mt-2 leading-relaxed">Once visitors comment on your posts, they'll appear here for review.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
    <div class="px-6 py-5 bg-navy-950/20 border-t border-navy-700/30">
        {{ $comments->links() }}
    </div>
    @endif
</div>
</div>

{{-- Mobile pagination --}}
@if($comments->hasPages())
<div class="mt-4 md:hidden">{{ $comments->links() }}</div>
@endif

@endsection
