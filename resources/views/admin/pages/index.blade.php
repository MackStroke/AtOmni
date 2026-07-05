@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
<div class="page-header mb-6">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title truncate">Pages</h1>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <form method="GET" action="{{ route('admin.pages.index') }}" class="flex items-center w-full hidden sm:flex">
            <select name="status" onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-secondary focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors appearance-none pr-8 relative">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </form>
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-electric focus:ring-offset-navy-900 shadow-lg shadow-electric/20 whitespace-nowrap">
            <svg class="w-5 h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden sm:inline">New Page</span>
        </a>
    </div>
</div>

<div class="mb-6 sm:hidden">
    <form method="GET" action="{{ route('admin.pages.index') }}" class="flex items-center w-full">
        <select name="status" onchange="this.form.submit()"
                class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-secondary focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors appearance-none pr-8 relative">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
    </form>
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden mb-6">
    @if(is_countable($pages) && count($pages) > 0 || !is_countable($pages) && $pages)
        @foreach($pages as $page)
        <div class="glass-card rounded-xl p-4 flex flex-col gap-3 relative overflow-hidden">
            <div class="flex justify-between items-start gap-2">
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-text-primary text-sm truncate">{{ $page->title }}</h3>
                    <p class="text-xs text-text-muted mt-0.5 truncate">{{ $page->slug }}</p>
                </div>
                @if($page->is_published)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-success/15 text-success shrink-0">Published</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-navy-600/30 text-text-muted shrink-0">Draft</span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2 pt-3 border-t border-navy-700/30">
                <a href="{{ route('admin.pages.edit', $page) }}" class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-electric hover:bg-electric/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </a>
                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this page?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-alert-red hover:bg-alert-red/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    @else
        <div class="glass-card rounded-xl p-8 text-center text-text-muted">No pages found.</div>
    @endif
</div>

<div class="hidden md:block">
    <x-admin.bulk-actions resource="pages" :actions="['delete' => 'Delete', 'draft' => 'Draft', 'publish' => 'Publish']" />
    <div class="glass-card rounded-2xl overflow-hidden mb-6 mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="title" label="Title" /></th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="slug" label="Slug" /></th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="is_published" label="Status" /></th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/10">
                @if(is_countable($pages) && count($pages) > 0 || !is_countable($pages) && $pages)
                    @foreach($pages as $page)
                    <tr class="transition-colors hover:bg-navy-950/30 group">
                        <td class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $page->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-6 py-4 font-bold text-text-primary text-base">{{ $page->title }}</td>
                        <td class="px-6 py-4 text-text-muted font-mono text-sm">{{ $page->slug }}</td>
                        <td class="px-6 py-4">
                            @if($page->is_published)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 shadow-sm">Published</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-navy-900/60 text-text-muted border border-navy-700/30 shadow-sm">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="p-2 text-text-muted hover:text-accent-blue hover:bg-accent-blue/10 transition-all rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-blue" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500/70 hover:text-rose-500 hover:bg-rose-500/10 transition-all rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-xl font-bold text-text-primary">No pages found</p>
                                <p class="text-sm text-text-muted mt-2 leading-relaxed">Your site hasn't got any custom pages yet. Create your first page to get started with content management.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>

@if($pages->hasPages())
<div class="mt-6 flex justify-center">
    {{ $pages->links() }}
</div>
@endif

@endsection
