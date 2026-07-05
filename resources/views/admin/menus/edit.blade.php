@extends('admin.layouts.app')

@section('title', 'Edit Menu — ' . $menu->name)
@section('page-title', 'Edit: ' . $menu->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Back link + header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center gap-1.5 text-sm text-text-muted hover:text-text-primary transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                All Menus
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-text-primary">{{ $menu->name }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <span class="px-2.5 py-1 rounded-lg bg-navy-950/50 text-[10px] font-mono font-bold text-text-muted uppercase tracking-wider border border-navy-700/20">
                    {{ $menu->location }}
                </span>
                @if($menu->is_active)
                    <span class="inline-flex items-center gap-1 text-xs text-emerald-400"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active</span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs text-rose-400"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Inactive</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Main form --}}
    <form action="{{ route('admin.menus.update', $menu) }}" method="POST" id="menuEditForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="items_json" id="items_json_input">

        {{-- Menu properties --}}
        <div class="glass-card rounded-2xl p-6 border border-navy-700/30 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-text-muted uppercase tracking-wider ml-1">Menu Name</label>
                    <input type="text" name="name" value="{{ $menu->name }}" required
                           class="w-full px-4 py-2.5 bg-navy-950/40 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-accent-blue/50 transition-all">
                </div>
                <div class="flex items-end gap-4">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }}
                               class="w-5 h-5 rounded-md bg-navy-950/40 border-navy-700/30 text-accent-blue focus:ring-accent-blue/50 cursor-pointer">
                        <span class="text-sm text-text-secondary font-medium">Active</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Menu Items Builder --}}
        <div class="glass-card rounded-2xl p-6 border border-navy-700/30">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-text-primary flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-accent-blue/10 text-accent-blue flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </div>
                    Menu Items
                </h2>
                <span id="item-count" class="px-3 py-1 bg-navy-950/40 text-text-muted text-[10px] font-bold uppercase rounded-full border border-navy-700/20">0 Items</span>
            </div>

            {{-- Item list --}}
            <div id="items-container" class="space-y-4 min-h-[80px]"></div>

            {{-- Add item buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <button type="button" onclick="addItem()" class="flex-1 py-3.5 border-2 border-dashed border-navy-700/20 rounded-2xl text-text-muted hover:text-text-primary hover:border-accent-blue/40 hover:bg-accent-blue/5 transition-all flex items-center justify-center text-xs font-bold uppercase tracking-widest">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Add Item
                </button>
                <button type="button" onclick="addItem(null, true)" class="flex-1 py-3.5 border-2 border-dashed border-navy-700/20 rounded-2xl text-text-muted hover:text-text-primary hover:border-purple-500/40 hover:bg-purple-500/5 transition-all flex items-center justify-center text-xs font-bold uppercase tracking-widest">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Add from Categories
                </button>
            </div>
        </div>

        {{-- Category picker modal --}}
        <div id="category-picker-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[999] hidden items-center justify-center p-4" onclick="if(event.target===this) closeCategoryPicker()">
            <div class="bg-navy-900 light:bg-white rounded-2xl border border-navy-700/50 light:border-slate-200 shadow-2xl w-full max-w-md max-h-[70vh] flex flex-col">
                <div class="p-5 border-b border-navy-700/30 flex items-center justify-between">
                    <h3 class="font-bold text-text-primary">Select Categories</h3>
                    <button type="button" onclick="closeCategoryPicker()" class="p-1.5 text-text-muted hover:text-text-primary rounded-lg hover:bg-navy-800/60 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-3 space-y-1" id="category-list"></div>
                <div class="p-4 border-t border-navy-700/30 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeCategoryPicker()" class="px-4 py-2 text-sm text-text-muted hover:text-text-primary transition-colors rounded-xl">Cancel</button>
                    <button type="button" onclick="addSelectedCategories()" class="px-6 py-2 bg-accent-blue hover:bg-accent-blue-hover text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-accent-blue/20">
                        Add Selected
                    </button>
                </div>
            </div>
        </div>

        {{-- Save button --}}
        <div class="flex items-center justify-end mt-8">
            <button type="submit" class="group inline-flex items-center justify-center px-10 py-4 text-sm font-bold tracking-widest uppercase text-white transition-all duration-500 rounded-2xl bg-accent-blue hover:bg-accent-blue-hover shadow-2xl shadow-accent-blue/30 focus:outline-none">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Menu
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    // ── Data ──────────────────────────────────────────────────────
    @php
        $itemsData = $menu->items->map(function($parent) {
            return [
                'title' => $parent->title,
                'url' => $parent->url,
                'target' => $parent->target,
                'icon' => $parent->icon,
                'css_class' => $parent->css_class,
                'is_active' => $parent->is_active,
                'children' => $parent->children->map(function($child) {
                    return [
                        'title' => $child->title,
                        'url' => $child->url,
                        'target' => $child->target,
                        'icon' => $child->icon,
                        'css_class' => $child->css_class,
                        'is_active' => $child->is_active,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $categoriesData = \App\Models\Category::with('children')->whereNull('parent_id')->orderBy('name')->get();
    @endphp

    let items = {!! json_encode($itemsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

    const categories = {!! json_encode($categoriesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

    // ── Render ────────────────────────────────────────────────────
    function renderItemHTML(item, parentIdx, childIdx = null) {
        const isChild = childIdx !== null;
        const dataAttr = isChild ? `data-parent="${parentIdx}" data-child="${childIdx}"` : `data-parent="${parentIdx}"`;
        const indent = isChild ? 'ml-8 border-l-2 border-accent-blue/20 pl-4' : '';
        const accentColor = isChild ? 'purple-500' : 'accent-blue';

        return `
            <div class="menu-item ${indent} flex flex-col gap-3 bg-navy-950/40 border border-navy-700/20 p-4 rounded-2xl hover:bg-navy-950/60 transition-all group/item shadow-lg shadow-black/5" ${dataAttr}>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="drag-handle cursor-grab active:cursor-grabbing text-text-muted hover:text-text-primary p-1.5 bg-navy-900/80 rounded-lg transition-colors border border-navy-700/30">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16"></path></svg>
                        </div>
                        ${isChild ? '<span class="text-[9px] font-bold text-purple-400 uppercase tracking-widest bg-purple-500/10 px-2 py-0.5 rounded-md">Sub</span>' : ''}
                    </div>
                    <div class="flex items-center gap-1">
                        <label class="relative inline-flex items-center cursor-pointer" title="${item.is_active ? 'Active' : 'Inactive'}">
                            <input type="checkbox" ${item.is_active ? 'checked' : ''} class="sr-only peer"
                                   onchange="toggleActive(${parentIdx}, ${childIdx}, this.checked)">
                            <div class="w-8 h-4 bg-navy-700 peer-checked:bg-emerald-500 rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                        <button type="button" onclick="removeItem(${parentIdx}, ${childIdx})" class="text-rose-500/50 hover:text-rose-500 p-1.5 hover:bg-rose-500/10 rounded-xl transition-all focus:outline-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[9px] font-bold text-text-muted uppercase tracking-wider ml-1">Title</label>
                        <input type="text" value="${escapeHtml(item.title)}" placeholder="Link label"
                               class="w-full px-4 py-2.5 bg-navy-900/60 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-${accentColor}/50 placeholder-text-muted/20 font-medium transition-all"
                               oninput="updateItem(${parentIdx}, ${childIdx}, 'title', this.value)">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-bold text-text-muted uppercase tracking-wider ml-1">URL</label>
                        <input type="text" value="${escapeHtml(item.url)}" placeholder="/about"
                               class="w-full px-4 py-2.5 bg-navy-900/60 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-${accentColor}/50 placeholder-text-muted/20 font-mono transition-all"
                               oninput="updateItem(${parentIdx}, ${childIdx}, 'url', this.value)">
                    </div>
                </div>
                ${!isChild ? `
                <div class="flex items-center gap-2 pt-2">
                    <button type="button" onclick="addChildItem(${parentIdx})" class="text-[10px] font-bold text-purple-400 hover:text-purple-300 uppercase tracking-widest flex items-center gap-1 hover:bg-purple-500/10 px-3 py-1.5 rounded-lg transition-all">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Add Sub-item
                    </button>
                    <span class="text-[9px] text-text-muted">${(item.children || []).length} sub-items</span>
                </div>
                ` : ''}
            </div>
        `;
    }

    function renderAll() {
        const container = document.getElementById('items-container');
        if (items.length === 0) {
            container.innerHTML = `
                <div class="py-16 px-6 border-2 border-dashed border-navy-700/10 rounded-3xl flex flex-col items-center justify-center text-center bg-navy-950/20">
                    <div class="w-14 h-14 rounded-2xl bg-navy-950/40 text-text-muted flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.826L10.242 10.242"></path></svg>
                    </div>
                    <p class="text-base font-bold text-text-primary mb-1">No items yet</p>
                    <p class="text-sm text-text-muted max-w-sm">Click "Add Item" below to add links, or "Add from Categories" to quickly import your category structure.</p>
                </div>`;
        } else {
            let html = '';
            items.forEach((item, i) => {
                html += renderItemHTML(item, i);
                if (item.children && item.children.length > 0) {
                    item.children.forEach((child, j) => {
                        html += renderItemHTML(child, i, j);
                    });
                }
            });
            container.innerHTML = html;
        }

        // Update counter
        let total = items.length;
        items.forEach(item => { total += (item.children || []).length; });
        document.getElementById('item-count').innerText = `${total} Items`;

        initSortable();
    }

    // ── Sortable ──────────────────────────────────────────────────
    let sortableInstance = null;

    function initSortable() {
        if (sortableInstance) sortableInstance.destroy();
        const container = document.getElementById('items-container');
        if (items.length > 0) {
            sortableInstance = new Sortable(container, {
                animation: 200,
                ghostClass: 'opacity-10',
                handle: '.drag-handle',
                forceFallback: true,
                onEnd: function() {
                    // Rebuild items from DOM order (only top-level reordering)
                    const els = container.querySelectorAll('.menu-item[data-parent]:not([data-child])');
                    const newItems = [];
                    els.forEach(el => {
                        const idx = parseInt(el.dataset.parent);
                        if (items[idx]) newItems.push({...items[idx]});
                    });
                    // Preserve children with their parents
                    items = newItems;
                    renderAll();
                }
            });
        }
    }

    // ── CRUD ──────────────────────────────────────────────────────
    window.addItem = function(data = null, showCategoryPicker = false) {
        if (showCategoryPicker) {
            openCategoryPicker();
            return;
        }
        items.push(data || { title: '', url: '#', target: '_self', icon: null, css_class: null, is_active: true, children: [] });
        renderAll();
    };

    window.addChildItem = function(parentIdx) {
        if (!items[parentIdx].children) items[parentIdx].children = [];
        items[parentIdx].children.push({ title: '', url: '#', target: '_self', icon: null, css_class: null, is_active: true });
        renderAll();
    };

    window.updateItem = function(parentIdx, childIdx, key, value) {
        if (childIdx !== null && childIdx !== 'null') {
            items[parentIdx].children[childIdx][key] = value;
        } else {
            items[parentIdx][key] = value;
        }
    };

    window.toggleActive = function(parentIdx, childIdx, checked) {
        if (childIdx !== null && childIdx !== 'null') {
            items[parentIdx].children[childIdx].is_active = checked;
        } else {
            items[parentIdx].is_active = checked;
        }
    };

    window.removeItem = function(parentIdx, childIdx) {
        if (childIdx !== null && childIdx !== 'null') {
            items[parentIdx].children.splice(childIdx, 1);
        } else {
            items.splice(parentIdx, 1);
        }
        renderAll();
    };

    // ── Category Picker ──────────────────────────────────────────
    function openCategoryPicker() {
        const modal = document.getElementById('category-picker-modal');
        const list = document.getElementById('category-list');
        let html = '';

        categories.forEach(cat => {
            html += `
                <label class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-navy-800/60 light:hover:bg-slate-100 cursor-pointer transition-colors">
                    <input type="checkbox" value="${cat.id}" data-name="${escapeHtml(cat.name)}" data-slug="${cat.slug}"
                           class="cat-checkbox w-4 h-4 rounded bg-navy-950/40 border-navy-700/30 text-accent-blue focus:ring-accent-blue/50 cursor-pointer">
                    <span class="text-sm text-text-primary font-medium">${escapeHtml(cat.name)}</span>
                    ${cat.children && cat.children.length > 0 ? `<span class="ml-auto text-[10px] text-text-muted">${cat.children.length} sub</span>` : ''}
                </label>`;
            if (cat.children && cat.children.length > 0) {
                cat.children.forEach(child => {
                    html += `
                        <label class="flex items-center gap-3 px-3 py-1.5 pl-10 rounded-xl hover:bg-navy-800/60 light:hover:bg-slate-100 cursor-pointer transition-colors">
                            <input type="checkbox" value="${child.id}" data-name="${escapeHtml(child.name)}" data-slug="${child.slug}" data-parent-slug="${cat.slug}"
                                   class="cat-checkbox w-4 h-4 rounded bg-navy-950/40 border-navy-700/30 text-purple-500 focus:ring-purple-500/50 cursor-pointer">
                            <span class="text-sm text-text-secondary">${escapeHtml(child.name)}</span>
                        </label>`;
                });
            }
        });

        list.innerHTML = html;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.closeCategoryPicker = function() {
        const modal = document.getElementById('category-picker-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.addSelectedCategories = function() {
        const checkboxes = document.querySelectorAll('#category-list .cat-checkbox:checked');
        // Group children under parents
        const parentMap = {};
        const topLevel = [];

        checkboxes.forEach(cb => {
            const parentSlug = cb.dataset.parentSlug;
            const entry = {
                title: cb.dataset.name,
                url: '/category/' + cb.dataset.slug,
                target: '_self',
                icon: null,
                css_class: null,
                is_active: true,
            };

            if (parentSlug) {
                if (!parentMap[parentSlug]) parentMap[parentSlug] = [];
                parentMap[parentSlug].push(entry);
            } else {
                entry.children = [];
                topLevel.push(entry);
                // Check if any of its children are also checked
            }
        });

        // Attach children to parents
        topLevel.forEach(parent => {
            const slug = parent.url.replace('/category/', '');
            if (parentMap[slug]) {
                parent.children = parentMap[slug];
            }
        });

        // Also add orphan children as top-level
        Object.keys(parentMap).forEach(slug => {
            const parentExists = topLevel.find(p => p.url === '/category/' + slug);
            if (!parentExists) {
                parentMap[slug].forEach(child => {
                    child.children = [];
                    topLevel.push(child);
                });
            }
        });

        topLevel.forEach(item => items.push(item));
        closeCategoryPicker();
        renderAll();
    };

    // ── Helpers ───────────────────────────────────────────────────
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ── Form Submit ──────────────────────────────────────────────
    document.getElementById('menuEditForm').addEventListener('submit', function() {
        document.getElementById('items_json_input').value = JSON.stringify(items);
    });

    // ── Init ─────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', renderAll);
</script>

<style>
    .menu-item { cursor: default; }
    .sortable-fallback { opacity: 0.8; transform: scale(1.02); }
</style>
@endsection
