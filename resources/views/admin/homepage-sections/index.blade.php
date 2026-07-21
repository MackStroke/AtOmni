@extends('admin.layouts.app')

@section('title', 'Homepage Sections')

@section('content')

<div id="alert-container" class="mb-4">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 flex items-center gap-2 font-bold text-sm shadow-sm mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif
</div>

{{-- Page Header --}}
<div class="page-header mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title truncate">Homepage Sections</h1>
        <p class="text-sm text-text-muted mt-1">Manage the dynamic layout blocks on your homepage.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.homepage-sections.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white transition-all bg-gradient-to-r from-electric to-cyan-glow hover:opacity-90 rounded-lg shadow-lg shadow-electric/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-electric focus:ring-offset-navy-900 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Section
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-3">
    <!-- Left: Table -->
    <div class="glass-card rounded-2xl overflow-hidden">
    <div class="table-scroll-container overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-navy-800/50 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30">
                <tr>
                    <th scope="col" class="px-4 py-4 w-10 text-center">Drag</th>
                    <th scope="col" class="px-4 py-4 font-bold">Title</th>
                    <th scope="col" class="px-4 py-4 font-bold">Layout Template</th>
                    <th scope="col" class="px-4 py-4 font-bold">Content Source</th>
                    <th scope="col" class="px-4 py-4 font-bold">Status</th>
                    <th scope="col" class="px-4 py-4 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-sections" class="divide-y divide-navy-700/10">
                @forelse($sections as $section)
                <tr class="transition-colors hover:bg-navy-950/30 group" data-id="{{ $section->id }}">
                    <td class="px-4 py-4 w-10 text-center align-middle">
                        <div class="cursor-move text-navy-400 hover:text-electric transition-colors handle">
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                        </div>
                    </td>
                    <td class="px-4 py-4 align-middle">
                        <div class="font-bold text-electric text-[15px]">{{ $section->title }}</div>
                    </td>
                    <td class="px-4 py-4 align-middle">
                        <span class="px-2 py-1 bg-navy-800 border border-navy-700 rounded-md text-xs font-mono text-text-secondary">
                            {{ $section->layout_type }}
                        </span>
                    </td>
                    <td class="px-4 py-4 align-middle text-sm text-text-muted">
                        @if($section->category)
                            Category: <span class="text-electric">{{ $section->category->name }}</span><br>
                        @endif
                        @if($section->tag)
                            Tag: <span class="text-electric">{{ $section->tag->name }}</span><br>
                        @endif
                        Limit: {{ $section->post_limit }} posts
                    </td>
                    <td class="px-4 py-4 align-middle">
                        @if($section->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wide bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wide bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 align-middle text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.homepage-sections.edit', $section) }}" class="text-electric hover:underline font-medium text-sm">Edit</a>
                            <form action="{{ route('admin.homepage-sections.destroy', $section) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this section? This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button class="text-alert-red hover:underline font-medium text-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                            <div class="w-16 h-16 mb-4 flex items-center justify-center rounded-2xl bg-navy-950/40 text-text-muted border border-navy-700/20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            </div>
                            <p class="text-xl font-bold text-text-primary">No sections found</p>
                            <p class="text-sm text-text-muted mt-2">Click "Add New Section" to start building your homepage.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    <!-- Right: Live Preview -->
    <div class="glass-card rounded-2xl flex flex-col border border-navy-700/50 sticky top-6" id="preview-container" style="height: calc(100vh - 48px); min-height: 600px;">
        <div class="bg-navy-800/50 px-4 py-3 border-b border-navy-700/30 flex flex-wrap items-center justify-between shrink-0 gap-3">
            <h3 class="font-bold text-text-primary flex items-center gap-2">
                <svg class="w-4 h-4 text-electric" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Live Preview
            </h3>
            
            <!-- Device Toggles -->
            <div class="flex items-center gap-1 bg-navy-900/50 p-1 rounded-lg border border-navy-700">
                <button onclick="setPreviewWidth('100%')" class="preview-btn active px-3 py-1.5 rounded-md text-xs font-semibold text-text-muted hover:text-white hover:bg-navy-700 transition-all flex items-center gap-1.5" data-device="desktop">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">Desktop</span>
                </button>
                <button onclick="setPreviewWidth('1024px')" class="preview-btn px-3 py-1.5 rounded-md text-xs font-semibold text-text-muted hover:text-white hover:bg-navy-700 transition-all flex items-center gap-1.5" data-device="laptop">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    <span class="hidden sm:inline">Laptop</span>
                </button>
                <button onclick="setPreviewWidth('768px')" class="preview-btn px-3 py-1.5 rounded-md text-xs font-semibold text-text-muted hover:text-white hover:bg-navy-700 transition-all flex items-center gap-1.5" data-device="tablet">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">Tablet</span>
                </button>
                <button onclick="setPreviewWidth('375px')" class="preview-btn px-3 py-1.5 rounded-md text-xs font-semibold text-text-muted hover:text-white hover:bg-navy-700 transition-all flex items-center gap-1.5" data-device="phone">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">Phone</span>
                </button>
            </div>
        </div>
        
        <div class="w-full flex-1 bg-navy-950/30 rounded-b-2xl relative overflow-hidden flex justify-center items-start" id="preview-bounds">
            <div id="iframe-wrapper" class="absolute origin-top transition-all duration-300 shadow-2xl bg-white border border-navy-700 rounded-lg overflow-hidden pointer-events-none" style="width: 100%;">
                <iframe id="preview-iframe" src="{{ route('home') }}" 
                        onload="resizeIframe(this)"
                        class="w-full bg-white" frameborder="0" scrolling="no">
                </iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('sortable-sections');
        if (el) {
            var sortable = Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function (evt) {
                    var order = [];
                    el.querySelectorAll('tr').forEach(function(row) {
                        order.push(row.dataset.id);
                    });

                                        fetch('{{ route("admin.homepage-sections.update-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order: order })
                    }).then(response => {
                        if(response.ok) {
                            console.log('Order saved');
                            document.getElementById('preview-iframe').contentWindow.location.reload();
                        }
                    });
                }
            });
        }
    });

    function setPreviewWidth(width) {
        // Change the wrapper width
        const wrapper = document.getElementById('iframe-wrapper');
        wrapper.style.width = width;
        
        // Let it layout, then recalculate scale
        setTimeout(() => {
            const iframe = document.getElementById('preview-iframe');
            if (iframe.contentWindow) {
                resizeIframe(iframe);
            }
        }, 50);

        // Update active button state
        document.querySelectorAll('.preview-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-navy-700', 'text-white');
            btn.classList.add('text-text-muted');
            
            if (
                (width === '100%' && btn.dataset.device === 'desktop') ||
                (width === '1024px' && btn.dataset.device === 'laptop') ||
                (width === '768px' && btn.dataset.device === 'tablet') ||
                (width === '375px' && btn.dataset.device === 'phone')
            ) {
                btn.classList.add('active', 'bg-navy-700', 'text-white');
                btn.classList.remove('text-text-muted');
            }
        });
    }

    function resizeIframe(obj) {
        try {
            // Attempt to auto-resize iframe to its internal content height
            var h = obj.contentWindow.document.documentElement.scrollHeight;
            if (h > 500) {
                obj.style.height = h + 'px';
            }
            
            // Calculate scale to fit container height
            const wrapper = document.getElementById('iframe-wrapper');
            const bounds = document.getElementById('preview-bounds');
            
            const boundsH = bounds.clientHeight;
            // Add a little padding (e.g. 40px)
            const scaleY = (boundsH - 40) / h;
            
            // Apply scale so the whole height is visible
            // Restrict scale between 0.1 and 1
            const scale = Math.max(0.1, Math.min(1, scaleY));
            
            wrapper.style.transform = 'scale(' + scale + ')';
            
            // If scale makes the width smaller than bounds width, center it horizontally by calculating left offset
            // Actually origin-top takes care of it, we just need to use flex justify-center on the parent.
            // But since it's absolute, flex justify won't work on it easily. We can use left: 50% and transform translateX(-50%)
            wrapper.style.left = '50%';
            wrapper.style.transform = 'translateX(-50%) scale(' + scale + ')';
            wrapper.style.transformOrigin = 'top center';
            
        } catch (e) {
            console.log("Error resizing iframe", e);
        }
    }
</script>

@endsection
