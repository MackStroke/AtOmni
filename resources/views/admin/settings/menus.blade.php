@extends('admin.layouts.app')

@section('title', 'Manage Menus')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-text-primary">Menu Builder</h1>
    <p class="text-text-muted mt-2">Manage your website's main navigation and footer links with drag-and-drop simplicity.</p>
</div>

<!-- Main Builder Layout -->
<form action="{{ route('admin.settings.menus') }}" method="POST" id="menuForm" class="space-y-8">
    @csrf
    @method('PUT')
    
    <input type="hidden" name="navbar_links" id="navbar_links_input">
    <input type="hidden" name="footer_links" id="footer_links_input">
    <input type="hidden" name="mega_menu_links" id="mega_menu_links_input">
    <input type="hidden" name="explore_links" id="explore_links_input">

    <div class="flex items-center justify-end mb-4">
        <button type="submit" class="group inline-flex items-center justify-center px-10 py-5 text-sm font-bold tracking-widest uppercase text-white transition-all duration-500 rounded-3xl bg-accent-blue hover:bg-accent-blue-hover shadow-2xl shadow-accent-blue/40 focus:outline-none">
            Save Menu Structure
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Navbar Sub-Menu -->
        <div class="glass-card rounded-3xl p-6 flex flex-col min-h-[400px]">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-text-primary flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-accent-blue/10 text-accent-blue flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </div>
                    Header Navigation
                </h2>
                <span id="nav-count" class="px-3 py-1 bg-navy-950/40 text-text-muted text-[10px] font-bold uppercase rounded-full border border-navy-700/20">0 Links</span>
            </div>
            <div id="navbar-container" class="space-y-3 flex-1 min-h-[100px]"></div>
            <button type="button" onclick="addLink('navbar')" class="mt-8 w-full py-4 border-2 border-dashed border-navy-700/20 rounded-2xl text-text-muted hover:text-text-primary hover:border-accent-blue/40 hover:bg-accent-blue/5 transition-all flex items-center justify-center text-xs font-bold uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> Add Header Link
            </button>
        </div>

        <!-- Footer Sub-Menu -->
        <div class="glass-card rounded-3xl p-6 flex flex-col min-h-[400px]">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-text-primary flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                    Footer Navigation
                </h2>
                <span id="foot-count" class="px-3 py-1 bg-navy-950/40 text-text-muted text-[10px] font-bold uppercase rounded-full border border-navy-700/20">0 Links</span>
            </div>
            <div id="footer-container" class="space-y-3 flex-1 min-h-[100px]"></div>
            <button type="button" onclick="addLink('footer')" class="mt-8 w-full py-4 border-2 border-dashed border-navy-700/20 rounded-2xl text-text-muted hover:text-text-primary hover:border-purple-500/40 hover:bg-purple-500/5 transition-all flex items-center justify-center text-xs font-bold uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> Add Footer Link
            </button>
        </div>

        <!-- Mega Menu Sub-Menu -->
        <div class="glass-card rounded-3xl p-6 flex flex-col min-h-[400px]">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-text-primary flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                    </div>
                    Mega Menu Top Links
                </h2>
                <span id="mega-count" class="px-3 py-1 bg-navy-950/40 text-text-muted text-[10px] font-bold uppercase rounded-full border border-navy-700/20">0 Links</span>
            </div>
            <div id="mega-container" class="space-y-3 flex-1 min-h-[100px]"></div>
            <button type="button" onclick="addLink('mega')" class="mt-8 w-full py-4 border-2 border-dashed border-navy-700/20 rounded-2xl text-text-muted hover:text-text-primary hover:border-emerald-500/40 hover:bg-emerald-500/5 transition-all flex items-center justify-center text-xs font-bold uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> Add Mega Menu Link
            </button>
        </div>

        <!-- Explore Links Sub-Menu -->
        <div class="glass-card rounded-3xl p-6 flex flex-col min-h-[400px]">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-text-primary flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    Explore Every Corner
                </h2>
                <span id="explore-count" class="px-3 py-1 bg-navy-950/40 text-text-muted text-[10px] font-bold uppercase rounded-full border border-navy-700/20">0 Links</span>
            </div>
            <div class="mb-4">
                <p class="text-xs text-text-muted">These links appear below the mega menu context or at the footer base as a large tag grid.</p>
            </div>
            <div id="explore-container" class="space-y-3 flex-1 min-h-[100px]"></div>
            <button type="button" onclick="addLink('explore')" class="mt-8 w-full py-4 border-2 border-dashed border-navy-700/20 rounded-2xl text-text-muted hover:text-text-primary hover:border-amber-500/40 hover:bg-amber-500/5 transition-all flex items-center justify-center text-xs font-bold uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> Add Explore Link
            </button>
        </div>

    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    // Data initialized from Blade
    let navbarLinks = {!! json_encode($navbarLinks) !!};
    let footerLinks = {!! json_encode($footerLinks) !!};
    let megaMenuLinks = {!! json_encode($megaMenuLinks) !!};
    let exploreLinks = {!! json_encode($exploreLinks) !!};
    let pages = {!! json_encode($pages) !!};
    let categories = {!! json_encode($categories) !!};

    const lists = {
        navbar: navbarLinks,
        footer: footerLinks,
        mega: megaMenuLinks,
        explore: exploreLinks
    };

    function renderItem(menu, item, index) {
        let optionsHTML = `<optgroup label="Pages">`;
        pages.forEach(page => {
            optionsHTML += `<option value="/${page.slug}">${page.title}</option>`;
        });
        optionsHTML += `</optgroup><optgroup label="Categories">`;
        categories.forEach(cat => {
            optionsHTML += `<option value="/category/${cat.slug}">${cat.name}</option>`;
        });
        optionsHTML += `</optgroup>`;

        return `
            <div class="menu-item flex flex-col gap-3 bg-navy-950/40 border border-navy-700/20 p-4 rounded-2xl hover:bg-navy-950/60 transition-all group/item shadow-lg shadow-black/5" 
                 data-menu="${menu}" data-index="${index}">
                <div class="flex items-center justify-between">
                    <div class="cursor-grab active:cursor-grabbing text-text-muted hover:text-text-primary p-1.5 bg-navy-900/80 rounded-lg transition-colors border border-navy-700/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16"></path></svg>
                    </div>
                    <button type="button" onclick="removeLink('${menu}', ${index})" class="text-rose-500/70 hover:text-rose-500 p-2 hover:bg-rose-500/10 rounded-xl transition-all focus:outline-none group/del">
                        <svg class="w-4 h-4 group-hover/del:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-bold text-text-muted uppercase tracking-wider ml-1">Label</label>
                        <input type="text" value="${item.label}" placeholder="e.g., Home" 
                               class="w-full px-4 py-2.5 bg-navy-900/60 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-accent-blue/50 placeholder-text-muted/20 font-medium transition-all" 
                               oninput="updateLinkData('${menu}', ${index}, 'label', this.value)">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-bold text-text-muted uppercase tracking-wider ml-1">Redirect URL</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="url-${menu}-${index}" value="${item.url}" placeholder="e.g., /about" 
                                   class="flex-1 px-4 py-2.5 bg-navy-900/60 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-accent-blue/50 placeholder-text-muted/20 font-mono transition-all" 
                                   oninput="updateLinkData('${menu}', ${index}, 'url', this.value)">
                            
                            <select class="w-8 flex-none px-1 py-2.5 bg-navy-900/60 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-accent-blue/50 cursor-pointer text-ellipsis overflow-hidden bg-none border-l pl-0"
                                    onchange="selectPageForLink('${menu}', ${index}, this.value)" title="Select Page or Category">
                                <option value="" disabled selected>📄</option>
                                ${optionsHTML}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper to auto-fill URL and label
    window.selectPageForLink = (menu, index, value) => {
        if (!value) return;
        const list = lists[menu];
        
        // Auto-fill label if empty
        if (!list[index].label) {
            let itemName = pages.find(p => '/'+p.slug === value)?.title;
            if (!itemName) itemName = categories.find(c => '/category/'+c.slug === value)?.name;
            if (itemName) list[index].label = itemName;
        }
        
        list[index].url = value;
        renderAll();
    };

    function renderEmpty(menu) {
        return `
            <div class="py-12 px-6 border-2 border-dashed border-navy-700/10 rounded-3xl flex flex-col items-center justify-center text-center bg-navy-950/20">
                <div class="w-12 h-12 rounded-2xl bg-navy-950/40 text-text-muted flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.826L10.242 10.242m-4.242 4.242l4.242-4.242"></path></svg>
                </div>
                <p class="text-sm font-bold text-text-primary">No links configured</p>
            </div>
        `;
    }

    function updateCounters() {
        ['navbar', 'footer', 'mega', 'explore'].forEach(menu => {
            document.getElementById(menu + '-count').innerText = `${lists[menu].length} Links`;
        });
    }

    function renderAll() {
        ['navbar', 'footer', 'mega', 'explore'].forEach(menu => {
            const container = document.getElementById(menu + '-container');
            if (container) {
                container.innerHTML = lists[menu].length 
                    ? lists[menu].map((it, i) => renderItem(menu, it, i)).join('') 
                    : renderEmpty(menu);
            }
        });
        updateCounters();
        initSortable();
    }

    let sortables = {};

    function initSortable() {
        ['navbar', 'footer', 'mega', 'explore'].forEach(menu => {
            if (sortables[menu]) sortables[menu].destroy();
            
            if (lists[menu].length) {
                const container = document.getElementById(menu + '-container');
                if (container) {
                    sortables[menu] = new Sortable(container, {
                        animation: 250,
                        ghostClass: 'opacity-10',
                        handle: '.cursor-grab',
                        forceFallback: true,
                        onEnd: function() {
                            const newOrder = [];
                            container.querySelectorAll('.menu-item').forEach(el => {
                                newOrder.push(lists[menu][parseInt(el.dataset.index)]);
                            });
                            lists[menu] = newOrder;
                            renderAll();
                        }
                    });
                }
            }
        });
    }

    window.addLink = (menu) => {
        lists[menu].push({ label: '', url: '' });
        renderAll();
    };

    window.updateLinkData = (menu, index, key, value) => {
        lists[menu][index][key] = value;
    };

    window.removeLink = (menu, index) => {
        lists[menu].splice(index, 1);
        renderAll();
    };

    document.getElementById('menuForm').addEventListener('submit', (e) => {
        ['navbar', 'footer', 'mega', 'explore'].forEach(menu => {
            const el = document.getElementById(menu + '_links_input');
            if(el) {
                const filtered = lists[menu].filter(l => l.label.trim() || l.url.trim());
                el.value = JSON.stringify(filtered);
            }
        });
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        renderAll();
    });
</script>

<style>
    .menu-item {
        cursor: default;
    }
    .sortable-fallback {
        opacity: 0.8;
        transform: scale(1.02);
    }
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
</style>
@endsection
