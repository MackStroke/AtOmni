@extends('admin.layouts.app')
@section('title', 'Edit Post')
@section('page-title', 'Edit Post')
@section('content')

<form method="POST" action="{{ route('admin.posts.update', $post) }}" id="post-form" autocomplete="off">
    @csrf @method('PUT')

    <div class="editor-layout">

        {{-- ═══════════════════════════════════════════════════════
             LEFT: Main Editor Canvas (Expanding)
             ═══════════════════════════════════════════════════════ --}}
        <div class="editor-main space-y-5">
            {{-- Title --}}
            <div>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                       class="w-full px-5 py-3.5 rounded-xl bg-navy-800/50 border border-navy-700/50 text-text-primary text-xl font-heading font-semibold placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors"
                       placeholder="Post title…">
                @error('title') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            {{-- TL;DR (Answer Nugget) --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="tldr" class="block text-sm font-medium text-text-secondary">TL;DR (Answer Nugget)</label>
                    <span id="tldr-counter" class="text-xs text-text-muted">{{ strlen(old('tldr', $post->tldr)) }} / 300</span>
                </div>
                <textarea id="tldr" name="tldr" rows="2" maxlength="300"
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary text-sm placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors resize-none"
                          placeholder="In short: what is the main takeaway?..."
                          oninput="document.getElementById('tldr-counter').innerText = this.value.length + ' / 300'">{{ old('tldr', $post->tldr) }}</textarea>
                @error('tldr') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            {{-- Excerpt --}}
            <div>
                <textarea id="excerpt" name="excerpt" rows="2"
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary text-sm placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors resize-none"
                          placeholder="Short summary (excerpt)…">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            {{-- Content Editor --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="content" class="block text-sm font-medium text-text-secondary">Content</label>
                    <p class="text-xs text-text-muted">Created {{ $post->created_at->format('M d, Y') }} · {{ number_format($post->views_count) }} views</p>
                </div>
                {{-- TinyMCE replaces this textarea; rows kept for JS fallback --}}
                <textarea id="content" name="content" rows="20"
                          class="w-full px-4 py-3 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm leading-relaxed"
                          placeholder="Write your article content…">{{ old('content', $post->content) }}</textarea>
                @error('content') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            {{-- FAQs --}}
            <div class="glass-card rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-semibold text-text-primary text-lg">FAQs</h3>
                    <button type="button" onclick="suggestFaqs()" id="suggest-faqs-btn" class="px-3 py-1.5 rounded-lg bg-electric/10 hover:bg-electric/20 text-electric text-sm font-medium transition-colors flex items-center gap-1.5">
                        ✨ Suggest FAQs with AI
                    </button>
                </div>
                <div id="faq-repeater" class="space-y-4">
                    @php $oldFaqs = old('faqs', $post?->faqs ?? []); @endphp
                    @if(is_array($oldFaqs) && count($oldFaqs) > 0)
                        @foreach($oldFaqs as $index => $faq)
                        <div class="faq-item bg-navy-900/50 p-4 rounded-lg border border-navy-700/50 relative">
                            <button type="button" aria-label="Remove FAQ item" onclick="this.closest('.faq-item').remove()" class="absolute top-2 right-2 text-rose-400 hover:text-rose-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <input type="text" name="faqs[{{ $index }}][question]" value="{{ $faq['question'] ?? '' }}" placeholder="Question" class="w-full px-3 py-2 rounded-md bg-navy-800 border border-navy-700 text-sm text-text-primary mb-2 focus:border-electric focus:outline-none">
                            <textarea name="faqs[{{ $index }}][answer]" rows="2" placeholder="Answer" class="w-full px-3 py-2 rounded-md bg-navy-800 border border-navy-700 text-sm text-text-primary focus:border-electric focus:outline-none">{{ $faq['answer'] ?? '' }}</textarea>
                        </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" onclick="addFaqItem()" class="mt-4 text-sm font-medium text-text-muted hover:text-text-primary transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Question
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             RIGHT: Sticky Sidebar
             ═══════════════════════════════════════════════════════ --}}
        {{-- On mobile: full-width below editor. On lg+: fixed 320px sticky sidebar. --}}
        <div class="editor-sidebar space-y-4" id="editor-sidebar">

            {{-- Featured Image (Top) --}}
            <div class="glass-card rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-text-primary text-xs uppercase tracking-wider">Featured Image</h3>
                    <button type="button" onclick="openMediaSelector({onSelect: setFeaturedImage})" class="text-[11px] text-electric hover:text-electric-light font-medium px-2 py-0.5 rounded bg-electric/10 hover:bg-electric/20 transition-colors">
                        Select
                    </button>
                </div>
                <div id="featured-image-preview" class="w-full h-32 rounded-lg bg-navy-900 border-2 border-dashed border-navy-700/50 flex flex-col items-center justify-center relative overflow-hidden group">
                    <div id="featured-image-placeholder" class="text-center {{ old('featured_image', $post->featured_image) ? 'hidden' : '' }}">
                        <svg class="w-6 h-6 text-navy-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] text-text-muted">No image</span>
                    </div>
                    <img loading="lazy" id="featured-image-img" alt="Featured image preview" src="{{ old('featured_image', $post->featured_image) ? (\Illuminate\Support\Str::startsWith(old('featured_image', $post->featured_image), 'http') ? old('featured_image', $post->featured_image) : asset('storage/' . old('featured_image', $post->featured_image))) : '' }}" 
                         class="absolute inset-0 w-full h-full object-cover {{ old('featured_image', $post->featured_image) ? '' : 'hidden' }}">
                    <button type="button" aria-label="Remove featured image" onclick="removeFeaturedImage()" id="featured-image-remove" class="absolute top-1.5 right-1.5 p-1 bg-navy-950/80 text-rose-400 hover:text-rose-300 rounded-md backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none {{ old('featured_image', $post->featured_image) ? '' : 'hidden' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <input type="hidden" name="featured_image" id="featured_image" value="{{ old('featured_image', $post->featured_image) }}">
            </div>

            {{-- Publish + Author (Combined compact card) --}}
            <div class="glass-card rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-semibold text-text-primary text-xs uppercase tracking-wider">Publish</h3>
                    <div class="flex gap-1.5">
                        <button type="button" onclick="openPreviewModal()" class="text-[11px] text-amber-400 hover:text-amber-300 font-medium px-2 py-0.5 rounded bg-amber-400/10 hover:bg-amber-400/20 transition-colors flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="status" class="block text-[10px] font-medium text-text-muted mb-0.5">Status</label>
                        <select id="status" name="status" class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors">
                            <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="scheduled" {{ old('status', $post->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        </select>
                    </div>
                    <div>
                        <label for="author_id" class="block text-[10px] font-medium text-text-muted mb-0.5">Author</label>
                        <select id="author_id" name="author_id" class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors">
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ old('author_id', $post->author_id) == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="publish-date-picker" class="{{ old('status', $post->status) === 'scheduled' ? '' : 'hidden' }}">
                    <label for="published_at" class="block text-[10px] font-medium text-text-muted mb-0.5">Schedule Date & Time</label>
                    <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors {{ $errors->has('published_at') ? 'border-red-500' : '' }}">
                    @error('published_at') <p class="mt-1 text-[10px] text-alert-red">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                           class="w-3.5 h-3.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30"
                           onchange="document.getElementById('featured_until_container').classList.toggle('hidden', !this.checked)">
                    <label for="is_featured" class="text-xs text-text-secondary">Featured</label>
                </div>
                <div id="featured_until_container" class="{{ old('is_featured', $post->is_featured) ? '' : 'hidden' }}">
                    <label for="featured_until" class="block text-[10px] font-medium text-text-muted mb-0.5">Featured Until (Leave blank for default 2 days)</label>
                    <input type="datetime-local" id="featured_until" name="featured_until" value="{{ old('featured_until', $post->featured_until ? $post->featured_until->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors {{ $errors->has('featured_until') ? 'border-red-500' : '' }}">
                    @error('featured_until') <p class="mt-1 text-[10px] text-alert-red">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 border-t border-navy-700/50 space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="kill_switch" name="kill_switch" value="1" {{ old('kill_switch', $post->kill_switch) ? 'checked' : '' }}
                               class="w-3.5 h-3.5 rounded border-navy-700 bg-navy-800 text-amber focus:ring-amber/30">
                        <label for="kill_switch" class="text-xs text-amber font-medium">Kill Switch (Takedown)</label>
                    </div>
                    <div id="redirect_url_container" class="{{ old('kill_switch', $post->kill_switch) ? '' : 'hidden' }}">
                        <label for="redirect_url" class="block text-[10px] font-medium text-text-muted mb-0.5">Redirect URL (Optional)</label>
                        <input type="url" id="redirect_url" name="redirect_url" value="{{ old('redirect_url', $post->redirect_url) }}" placeholder="https://..."
                               class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors">
                    </div>
                </div>

                @error('author_id') <p class="text-alert-red text-xs">{{ $message }}</p> @enderror
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                    Update Post
                </button>
            </div>

            {{-- Editor Publishing Checklist --}}
            <div class="glass-card rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between cursor-pointer group" onclick="document.getElementById('checklist-content').classList.toggle('hidden')">
                    <h3 class="font-heading font-semibold text-text-primary text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Publishing Checklist
                    </h3>
                    <svg class="w-4 h-4 text-text-muted group-hover:text-text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div id="checklist-content" class="hidden space-y-2 pt-2 border-t border-navy-700/50">
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>Title is catchy and &lt; 60 chars</span>
                    </label>
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>Excerpt (TL;DR) is filled out</span>
                    </label>
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>Headings use H2/H3 (No H1 in body)</span>
                    </label>
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>External links open in new tab</span>
                    </label>
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>Primary category & tags selected</span>
                    </label>
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>Featured image is optimized</span>
                    </label>
                    <label class="flex items-start gap-2 text-xs text-text-secondary cursor-pointer hover:text-text-primary transition-colors">
                        <input type="checkbox" class="mt-0.5 rounded border-navy-700 bg-navy-800 text-electric focus:ring-electric/30">
                        <span>Inline images have Alt text</span>
                    </label>
                </div>
            </div>

            {{-- Taxonomy Card --}}
            <div class="glass-card rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-heading font-semibold text-text-primary text-xs uppercase tracking-wider">Taxonomy</h3>
                    <button type="button" onclick="autoFillTaxonomy()" class="text-[11px] text-emerald-400 hover:text-emerald-300 font-medium px-2 py-0.5 rounded bg-emerald-400/10 hover:bg-emerald-400/20 transition-colors flex items-center gap-1">
                        ✨ Auto-fill
                    </button>
                </div>
                <div>
                    <label for="category_id" class="block text-[10px] font-medium text-text-muted mb-0.5 uppercase tracking-wider">Category</label>
                    <select name="category_id" id="category_id" class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="">Select…</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tags" class="block text-[10px] font-medium text-text-muted mb-0.5 uppercase tracking-wider">Tags</label>
                    @php $postTagNames = $post->tags->pluck('name')->toArray(); @endphp
                    <select name="tags[]" id="tags" multiple class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="">Add tags…</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}" {{ in_array($tag->name, old('tags', $postTagNames)) ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="locations" class="block text-[10px] font-medium text-text-muted mb-0.5 uppercase tracking-wider">Locations</label>
                    @php $postLocationIds = $post->locations->pluck('id')->toArray(); @endphp
                    <select name="locations[]" id="locations" multiple class="w-full px-2 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="">Select geographic locations…</option>
                        @foreach($locations as $country)
                            <optgroup label="{{ $country->name }}">
                                <option value="{{ $country->id }}" {{ in_array($country->id, old('locations', $postLocationIds)) ? 'selected' : '' }}>{{ $country->name }} (Country)</option>
                                @foreach($country->children as $state)
                                    <option value="{{ $state->id }}" {{ in_array($state->id, old('locations', $postLocationIds)) ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Analysis & Optimization Card --}}
            <div class="glass-card rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-heading font-semibold text-text-primary text-xs uppercase tracking-wider">Analysis & Optimization</h3>
                    <button type="button" onclick="analyzeContent()" id="analyze-content-btn" class="text-[11px] text-electric hover:text-electric-light font-medium px-2 py-0.5 rounded bg-electric/10 hover:bg-electric/20 transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Analyze Now
                    </button>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-center" id="scores-container">
                    <div class="p-2 rounded bg-navy-900 border border-navy-700/50">
                        <div class="text-[10px] text-text-muted uppercase tracking-wider mb-1">SEO</div>
                        <div class="text-lg font-bold text-emerald-400" id="display-seo-score">{{ $post?->seo_score ?? '—' }}</div>
                    </div>
                    <div class="p-2 rounded bg-navy-900 border border-navy-700/50">
                        <div class="text-[10px] text-text-muted uppercase tracking-wider mb-1">AEO</div>
                        <div class="text-lg font-bold text-amber-400" id="display-aeo-score">{{ $post?->aeo_score ?? '—' }}</div>
                    </div>
                    <div class="p-2 rounded bg-navy-900 border border-navy-700/50">
                        <div class="text-[10px] text-text-muted uppercase tracking-wider mb-1">GEO</div>
                        <div class="text-lg font-bold text-blue-400" id="display-geo-score">{{ $post?->geo_score ?? '—' }}</div>
                    </div>
                </div>

                <div id="analysis-suggestions" class="hidden mt-3 pt-3 border-t border-navy-700/50 space-y-3 text-sm">
                    <!-- Suggestions will be injected here -->
                </div>
            </div>

            {{-- Legacy Content Analysis (Sticky in sidebar) --}}
            @include('admin.partials.content-analysis')
        </div>
    </div>
</form>

{{-- ═══ PREVIEW MODAL ═══ --}}
<div id="preview-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePreviewModal()"></div>
    <div class="relative mx-auto mt-8 mb-8 w-full max-w-3xl max-h-[calc(100vh-64px)] overflow-y-auto bg-white rounded-2xl shadow-2xl">
        <div class="sticky top-0 z-10 bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between rounded-t-2xl">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                <span class="text-xs text-slate-400 ml-2 font-mono">Preview</span>
            </div>
            <button type="button" aria-label="Close preview modal" onclick="closePreviewModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-8 py-8" id="preview-body">
            <div id="preview-category" class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-3"></div>
            <h1 id="preview-title" class="text-3xl font-bold text-slate-900 leading-tight mb-4">Untitled Post</h1>
            <div class="flex items-center gap-3 text-sm text-slate-500 mb-6 pb-6 border-b border-slate-100">
                <span id="preview-author"></span>
                <span>·</span>
                <span id="preview-date"></span>
            </div>
            <div id="preview-featured-img" class="hidden mb-6 rounded-xl overflow-hidden">
                <img loading="lazy" id="preview-img-tag" alt="Preview image" src="" class="w-full h-auto object-cover" style="max-height:400px">
            </div>
            <div id="preview-content" class="prose prose-slate max-w-none text-[15px] leading-relaxed">
                <p class="text-slate-400 italic">Start writing to see preview…</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<style>
    .ts-control, .ts-wrapper.single.input-active .ts-control {
        background-color: rgba(30, 41, 59, 0.5) !important;
        border: 1px solid rgba(51, 65, 85, 0.5) !important;
        color: #e2e8f0 !important;
        border-radius: 0.5rem;
        font-size: 12px !important;
        min-height: 32px !important;
        padding: 2px 8px !important;
    }
    .ts-dropdown, .ts-control, .ts-dropdown.form-control, .ts-dropdown.form-select {
        background-color: #1e293b !important;
        border: 1px solid rgba(51, 65, 85, 0.5) !important;
        color: #e2e8f0 !important;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    .ts-control input { color: #e2e8f0 !important; font-size: 12px !important; }
    .ts-control .item { background-color: #3b82f6 !important; color: white !important; border-radius: 4px; padding: 1px 5px; font-size: 11px !important; }
    
    /* ── Editor layout containment ── */
    .tox-tinymce { max-width: 100% !important; }
    .tox.tox-tinymce { width: 100% !important; }
    #post-form { max-width: 100%; overflow: hidden; }

    /* ── Desktop two-column layout fix ──
       Ensures sidebar stays at 320px and editor fills remaining space.
       Self-contained — works without Tailwind asset recompilation. */
    @media (min-width: 1024px) {
        #editor-sidebar {
            width: 320px !important;
            min-width: 320px !important;
            max-width: 320px !important;
            flex-shrink: 0 !important;
        }
        .editor-layout {
            display: flex !important;
            flex-direction: row !important;
            align-items: flex-start !important;
        }
        .editor-main {
            flex: 1 1 0% !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }
    }

    #editor-sidebar {
        margin-top: 0 !important;
    }

    #editor-sidebar::-webkit-scrollbar { width: 3px; }
    #editor-sidebar::-webkit-scrollbar-thumb { background: rgba(100,116,139,0.3); border-radius: 9px; }
    #editor-sidebar::-webkit-scrollbar-track { background: transparent; }

    .prose h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
    .prose h2 { font-size: 1.375rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.5rem; }
    .prose p { margin-bottom: 1rem; color: #334155; }
    .prose a { color: #2563eb; text-decoration: underline; }
    .prose ul, .prose ol { margin-left: 1.25rem; margin-bottom: 1rem; }
    .prose li { margin-bottom: 0.25rem; color: #334155; }
    .prose blockquote { border-left: 3px solid #3b82f6; padding-left: 1rem; color: #64748b; font-style: italic; margin: 1rem 0; }
    .prose img { border-radius: 0.75rem; margin: 1rem 0; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.tsCategory = new TomSelect('#category_id', { create: true, sortField: { field: "text", direction: "asc" } });
        window.tsTags = new TomSelect('#tags', { create: true, maxItems: null, plugins: ['remove_button'] });
        window.tsLocations = new TomSelect('#locations', { create: false, maxItems: null, plugins: ['remove_button'] });

        const statusSelect = document.getElementById('status');
        const datePicker = document.getElementById('publish-date-picker');
        function toggleDatePicker() {
            datePicker.classList.toggle('hidden', statusSelect.value !== 'scheduled');
        }
        statusSelect.addEventListener('change', toggleDatePicker);
        toggleDatePicker();

        const killSwitch = document.getElementById('kill_switch');
        const redirectContainer = document.getElementById('redirect_url_container');
        if (killSwitch && redirectContainer) {
            killSwitch.addEventListener('change', function() {
                redirectContainer.classList.toggle('hidden', !this.checked);
            });
        }
    });

    function setFeaturedImage(media) {
        document.getElementById('featured_image').value = media.file_path;
        const img = document.getElementById('featured-image-img');
        const placeholder = document.getElementById('featured-image-placeholder');
        const removeBtn = document.getElementById('featured-image-remove');
        img.src = media.url;
        img.classList.remove('hidden');
        placeholder.classList.add('hidden');
        removeBtn.classList.remove('hidden');
    }

    function removeFeaturedImage() {
        document.getElementById('featured_image').value = '';
        document.getElementById('featured-image-img').classList.add('hidden');
        document.getElementById('featured-image-placeholder').classList.remove('hidden');
        document.getElementById('featured-image-remove').classList.add('hidden');
    }

    function openPreviewModal() {
        const modal = document.getElementById('preview-modal');
        const title = document.getElementById('title').value || 'Untitled Post';
        const content = document.getElementById('content').value || '<p class="text-slate-400 italic">Start writing to see preview…</p>';
        const featuredImg = document.getElementById('featured-image-img').src;
        const categorySelect = document.getElementById('category_id');
        const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text || '';
        const authorSelect = document.querySelector('[name="author_id"]');
        const authorText = authorSelect.options[authorSelect.selectedIndex]?.text || '';

        document.getElementById('preview-title').textContent = title;
        document.getElementById('preview-content').innerHTML = content;
        document.getElementById('preview-category').textContent = categoryText;
        document.getElementById('preview-author').textContent = authorText;
        document.getElementById('preview-date').textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

        const imgContainer = document.getElementById('preview-featured-img');
        if (featuredImg && !document.getElementById('featured-image-img').classList.contains('hidden')) {
            document.getElementById('preview-img-tag').src = featuredImg;
            imgContainer.classList.remove('hidden');
        } else {
            imgContainer.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePreviewModal() {
        document.getElementById('preview-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function autoFillTaxonomy(force = true) {
        const title = document.getElementById('title').value || '';
        let content = '';
        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
            content = tinymce.activeEditor.getContent();
        } else {
            content = document.getElementById('content').value || '';
        }

        const currentCategory = window.tsCategory.getValue();
        const currentTags = window.tsTags.getValue();
        const currentLocations = window.tsLocations.getValue();

        // If not forcing, and all fields are already filled, don't do anything
        if (!force && currentCategory && currentTags.length > 0 && currentLocations.length > 0) {
            return;
        }

        const btn = document.querySelector('button[onclick="autoFillTaxonomy()"]');
        let originalText = '';
        if (btn) {
            originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 w-3 h-3 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
            btn.disabled = true;
        }

        fetch('{{ route('admin.posts.auto-taxonomy') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ title: title, content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.category_id && (!currentCategory || force)) {
                window.tsCategory.setValue(data.category_id);
            }
            if (data.tags && data.tags.length > 0 && (currentTags.length === 0 || force)) {
                data.tags.forEach(t => window.tsTags.addOption({value: t, text: t}));
                window.tsTags.setValue(data.tags);
            }
            if (data.locations && data.locations.length > 0 && (currentLocations.length === 0 || force)) {
                window.tsLocations.setValue(data.locations);
            }
        })
        .catch(err => console.error(err))
        .finally(() => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        if (titleInput) {
            titleInput.addEventListener('blur', () => autoFillTaxonomy(false));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePreviewModal();
    });

    // ── FAQ Repeater Logic ──
    let faqCount = document.querySelectorAll('.faq-item').length;
    function addFaqItem(question = '', answer = '') {
        const index = faqCount++;
        const html = `
            <div class="faq-item bg-navy-900/50 p-4 rounded-lg border border-navy-700/50 relative">
                <button type="button" aria-label="Remove FAQ item" onclick="this.closest('.faq-item').remove()" class="absolute top-2 right-2 text-rose-400 hover:text-rose-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <input type="text" name="faqs[${index}][question]" value="${question}" placeholder="Question" class="w-full px-3 py-2 rounded-md bg-navy-800 border border-navy-700 text-sm text-text-primary mb-2 focus:border-electric focus:outline-none">
                <textarea name="faqs[${index}][answer]" rows="2" placeholder="Answer" class="w-full px-3 py-2 rounded-md bg-navy-800 border border-navy-700 text-sm text-text-primary focus:border-electric focus:outline-none">${answer}</textarea>
            </div>
        `;
        document.getElementById('faq-repeater').insertAdjacentHTML('beforeend', html);
    }

    function suggestFaqs() {
        const title = document.getElementById('title').value;
        let content = '';
        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
            content = tinymce.activeEditor.getContent();
        } else {
            content = document.getElementById('content').value;
        }

        if (!title || !content) {
            alert('Please fill out the title and content first to generate FAQs.');
            return;
        }

        const btn = document.getElementById('suggest-faqs-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';
        btn.disabled = true;

        fetch('{{ route('admin.posts.suggest-faqs') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ title: title, content: content })
        })
        .then(res => res.json())
        .then(data => {
            if (data.faqs && Array.isArray(data.faqs)) {
                data.faqs.forEach(faq => addFaqItem(faq.question, faq.answer));
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(err => alert('Failed to generate FAQs.'))
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    if (faqCount === 0) {
        addFaqItem();
    }

    function analyzeContent() {
        const title = document.getElementById('title').value;
        const excerpt = document.getElementById('excerpt').value;
        let content = '';
        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
            content = tinymce.activeEditor.getContent();
        } else {
            content = document.getElementById('content').value;
        }

        if (!title || !content) {
            alert('Please provide at least a title and content to analyze.');
            return;
        }

        const btn = document.getElementById('analyze-content-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin w-3 h-3 mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Analyzing...';
        btn.disabled = true;

        fetch('{{ route('admin.posts.analyze-content', $post?->id ?? 0) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ title: title, content: content, excerpt: excerpt })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('display-seo-score').innerText = data.seo_score;
            document.getElementById('display-aeo-score').innerText = data.aeo_score;
            document.getElementById('display-geo-score').innerText = data.geo_score;

            const suggContainer = document.getElementById('analysis-suggestions');
            suggContainer.innerHTML = '';
            
            let hasSuggestions = false;
            ['headline', 'content', 'aeo', 'geo'].forEach(type => {
                if (data.suggestions[type] && data.suggestions[type].length > 0) {
                    hasSuggestions = true;
                    const typeLabel = type.charAt(0).toUpperCase() + type.slice(1);
                    let html = `<div class="bg-navy-800/50 p-2.5 rounded border border-navy-700/50">
                                    <div class="text-[10px] uppercase font-bold text-electric mb-1">${typeLabel} Suggestions</div>
                                    <ul class="list-disc pl-4 space-y-1 text-xs text-text-secondary">`;
                    data.suggestions[type].forEach(s => {
                        html += `<li>${s}</li>`;
                    });
                    html += `</ul></div>`;
                    suggContainer.insertAdjacentHTML('beforeend', html);
                }
            });

            if (hasSuggestions) {
                suggContainer.classList.remove('hidden');
            } else {
                suggContainer.classList.add('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to analyze content.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@include('admin.partials.editor-scripts')
@endsection

