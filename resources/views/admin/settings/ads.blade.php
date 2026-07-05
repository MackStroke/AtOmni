@extends('admin.layouts.app')

@section('title', 'Ad Controls')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-text-primary">Ad Controls</h1>
    <p class="text-text-muted mt-2">Manage your custom ad placements across the frontend. These areas will collapse automatically if no ad code is provided.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
    <div class="lg:col-span-8 space-y-8">
        <form action="{{ route('admin.settings.ads') }}" method="POST" enctype="multipart/form-data" id="ads-settings-form">
            @csrf
            @method('PUT')

            <div class="glass-card rounded-2xl overflow-hidden p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-6 border-b border-navy-700/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex shrink-0 items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-text-primary">Custom Ad Placements</h2>
                            <p class="text-[11px] sm:text-xs text-text-muted font-medium mt-0.5">Paste HTML/JS tags for your direct sponsorships or upload an image with a link.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary shrink-0">
                        Save Ad Controls
                    </button>
                </div>

                <div class="space-y-6">

                    {{-- Header Leaderboard --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-text-secondary">Header Leaderboard (728x90)</h3>
                        <p class="text-[10px] text-text-muted mb-2">Displays globally at the top of the page, below the navigation header.</p>
                        
                        <label for="ad_header_leaderboard" class="block text-xs font-semibold text-text-secondary">Option A: Raw Script/HTML</label>
                        <textarea name="ad_header_leaderboard" id="ad_header_leaderboard" rows="3"
                                  placeholder="<script>...</script>"
                                  class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono text-xs resize-y">{{ old('ad_header_leaderboard', $settings['ad_header_leaderboard'] ?? '') }}</textarea>
                        @error('ad_header_leaderboard')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                                  
                        <div class="flex items-center gap-4 py-1">
                            <div class="h-px bg-navy-700/30 flex-1"></div>
                            <span class="text-[10px] text-text-muted font-bold uppercase tracking-wider">OR</span>
                            <div class="h-px bg-navy-700/30 flex-1"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ad_header_leaderboard_image" class="block text-xs font-semibold text-text-secondary mb-1">Option B: Upload Image</label>
                                <input type="file" name="ad_header_leaderboard_image" id="ad_header_leaderboard_image" accept="image/*"
                                       class="block w-full text-xs text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-electric/10 file:text-electric hover:file:bg-electric/20 cursor-pointer">
                                <p class="text-[10px] text-text-muted mt-1">Uploading an image will overwrite the raw script above.</p>
                                @error('ad_header_leaderboard_image')
                                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                                @enderror

                                @if(!empty($settings['ad_header_leaderboard_image_source']))
                                    <div id="current_img_ad_header_leaderboard" class="mt-3 flex items-center justify-between bg-navy-900/50 p-2.5 rounded-xl border border-navy-700/30">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['ad_header_leaderboard_image_source']) }}" class="h-10 w-16 rounded border border-navy-700 object-cover" alt="Current Banner">
                                            <div class="text-[10px] text-text-muted">
                                                <p class="font-bold text-text-secondary truncate max-w-[150px]">{{ basename($settings['ad_header_leaderboard_image_source']) }}</p>
                                                <p>Active banner image</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearPlacementBanner('ad_header_leaderboard')" class="p-1.5 rounded-lg bg-navy-800 hover:bg-rose-500/20 text-text-muted hover:text-rose-500 transition-colors" title="Delete current banner">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="ad_header_leaderboard_link" class="block text-xs font-semibold text-text-secondary mb-1">Ad Link (URL)</label>
                                <input type="url" name="ad_header_leaderboard_link" id="ad_header_leaderboard_link" placeholder="https://example.com"
                                       value="{{ old('ad_header_leaderboard_link', $settings['ad_header_leaderboard_link'] ?? '') }}"
                                       class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all text-xs">
                                @error('ad_header_leaderboard_link')
                                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-navy-700/30">

                    {{-- In-Article Body --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-text-secondary">In-Article Body (Responsive)</h3>
                        <p class="text-[10px] text-text-muted mb-2">Injected automatically within the content of every article (around the 3rd paragraph).</p>
                        
                        <label for="ad_in_article" class="block text-xs font-semibold text-text-secondary">Option A: Raw Script/HTML</label>
                        <textarea name="ad_in_article" id="ad_in_article" rows="3"
                                  placeholder="<script>...</script>"
                                  class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono text-xs resize-y">{{ old('ad_in_article', $settings['ad_in_article'] ?? '') }}</textarea>
                        @error('ad_in_article')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                                  
                        <div class="flex items-center gap-4 py-1">
                            <div class="h-px bg-navy-700/30 flex-1"></div>
                            <span class="text-[10px] text-text-muted font-bold uppercase tracking-wider">OR</span>
                            <div class="h-px bg-navy-700/30 flex-1"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ad_in_article_image" class="block text-xs font-semibold text-text-secondary mb-1">Option B: Upload Image</label>
                                <input type="file" name="ad_in_article_image" id="ad_in_article_image" accept="image/*"
                                       class="block w-full text-xs text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-electric/10 file:text-electric hover:file:bg-electric/20 cursor-pointer">
                                <p class="text-[10px] text-text-muted mt-1">Uploading an image will overwrite the raw script above.</p>
                                @error('ad_in_article_image')
                                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                                @enderror

                                @if(!empty($settings['ad_in_article_image_source']))
                                    <div id="current_img_ad_in_article" class="mt-3 flex items-center justify-between bg-navy-900/50 p-2.5 rounded-xl border border-navy-700/30">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['ad_in_article_image_source']) }}" class="h-10 w-16 rounded border border-navy-700 object-cover" alt="Current Banner">
                                            <div class="text-[10px] text-text-muted">
                                                <p class="font-bold text-text-secondary truncate max-w-[150px]">{{ basename($settings['ad_in_article_image_source']) }}</p>
                                                <p>Active banner image</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearPlacementBanner('ad_in_article')" class="p-1.5 rounded-lg bg-navy-800 hover:bg-rose-500/20 text-text-muted hover:text-rose-500 transition-colors" title="Delete current banner">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="ad_in_article_link" class="block text-xs font-semibold text-text-secondary mb-1">Ad Link (URL)</label>
                                <input type="url" name="ad_in_article_link" id="ad_in_article_link" placeholder="https://example.com"
                                       value="{{ old('ad_in_article_link', $settings['ad_in_article_link'] ?? '') }}"
                                       class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all text-xs">
                                @error('ad_in_article_link')
                                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-navy-700/30">

                    {{-- Sidebar Sticky --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-text-secondary">Sidebar Sticky (300x250 / 300x600)</h3>
                        <p class="text-[10px] text-text-muted mb-2">Displays in the right sidebar on the homepage and article pages.</p>
                        
                        <label for="ad_sidebar" class="block text-xs font-semibold text-text-secondary">Option A: Raw Script/HTML</label>
                        <textarea name="ad_sidebar" id="ad_sidebar" rows="3"
                                  placeholder="<script>...</script>"
                                  class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono text-xs resize-y">{{ old('ad_sidebar', $settings['ad_sidebar'] ?? '') }}</textarea>
                        @error('ad_sidebar')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center gap-4 py-1">
                            <div class="h-px bg-navy-700/30 flex-1"></div>
                            <span class="text-[10px] text-text-muted font-bold uppercase tracking-wider">OR</span>
                            <div class="h-px bg-navy-700/30 flex-1"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ad_sidebar_image" class="block text-xs font-semibold text-text-secondary mb-1">Option B: Upload Image</label>
                                <input type="file" name="ad_sidebar_image" id="ad_sidebar_image" accept="image/*"
                                       class="block w-full text-xs text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-electric/10 file:text-electric hover:file:bg-electric/20 cursor-pointer">
                                <p class="text-[10px] text-text-muted mt-1">Uploading an image will overwrite the raw script above.</p>
                                @error('ad_sidebar_image')
                                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                                @enderror

                                @if(!empty($settings['ad_sidebar_image_source']))
                                    <div id="current_img_ad_sidebar" class="mt-3 flex items-center justify-between bg-navy-900/50 p-2.5 rounded-xl border border-navy-700/30">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['ad_sidebar_image_source']) }}" class="h-10 w-16 rounded border border-navy-700 object-cover" alt="Current Banner">
                                            <div class="text-[10px] text-text-muted">
                                                <p class="font-bold text-text-secondary truncate max-w-[150px]">{{ basename($settings['ad_sidebar_image_source']) }}</p>
                                                <p>Active banner image</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearPlacementBanner('ad_sidebar')" class="p-1.5 rounded-lg bg-navy-800 hover:bg-rose-500/20 text-text-muted hover:text-rose-500 transition-colors" title="Delete current banner">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="ad_sidebar_link" class="block text-xs font-semibold text-text-secondary mb-1">Ad Link (URL)</label>
                                <input type="url" name="ad_sidebar_link" id="ad_sidebar_link" placeholder="https://example.com"
                                       value="{{ old('ad_sidebar_link', $settings['ad_sidebar_link'] ?? '') }}"
                                       class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all text-xs">
                                @error('ad_sidebar_link')
                                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <!-- Live Preview Partition -->
    <div class="lg:col-span-4 static lg:sticky top-[90px] space-y-6">
        <div class="glass-card rounded-2xl overflow-hidden border border-navy-700/30">
            <div class="bg-navy-900/50 px-5 py-4 border-b border-navy-700/30 flex items-center gap-2">
                <svg class="w-4 h-4 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                <h3 class="font-bold text-text-primary text-sm">Live Previews</h3>
            </div>
            
            <div class="p-5 space-y-6">
                <!-- Header Preview -->
                <div class="space-y-2">
                    <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">Header Leaderboard</div>
                    <div class="w-full min-h-[70px] bg-navy-950/60 rounded-xl border border-navy-800/40 overflow-hidden flex items-center justify-center relative p-3 shadow-inner shadow-black/20" id="preview_container_ad_header_leaderboard">
                        <div id="preview_ad_header_leaderboard" class="w-full flex justify-center">
                            @if(!empty($settings['ad_header_leaderboard']))
                                <div class="scale-[0.95] origin-center max-w-full flex justify-center transition-all duration-300">
                                    {!! $settings['ad_header_leaderboard'] !!}
                                </div>
                            @else
                                <span class="text-text-muted text-xs font-semibold">No ad placed</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar Preview -->
                <div class="space-y-2">
                    <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">Sidebar Sticky</div>
                    <div class="w-full min-h-[120px] bg-navy-950/60 rounded-xl border border-navy-800/40 overflow-hidden flex items-center justify-center relative p-3 shadow-inner shadow-black/20" id="preview_container_ad_sidebar">
                        <div id="preview_ad_sidebar" class="w-full flex justify-center">
                            @if(!empty($settings['ad_sidebar']))
                                <div class="scale-[0.95] origin-center max-w-full flex justify-center transition-all duration-300">
                                    {!! $settings['ad_sidebar'] !!}
                                </div>
                            @else
                                <span class="text-text-muted text-xs font-semibold">No ad placed</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- In-Article Preview -->
                <div class="space-y-2">
                    <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">In-Article Body</div>
                    <div class="w-full min-h-[90px] bg-navy-950/60 rounded-xl border border-navy-800/40 overflow-hidden flex items-center justify-center relative p-3 shadow-inner shadow-black/20" id="preview_container_ad_in_article">
                        <div id="preview_ad_in_article" class="w-full flex justify-center">
                            @if(!empty($settings['ad_in_article']))
                                <div class="scale-[0.95] origin-center max-w-full flex justify-center transition-all duration-300">
                                    {!! $settings['ad_in_article'] !!}
                                </div>
                            @else
                                <span class="text-text-muted text-xs font-semibold">No ad placed</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-navy-900/30 px-5 py-3 border-t border-navy-700/30">
                <p class="text-[10px] text-text-muted leading-relaxed">
                    Previews are styled to match the dark theme and container spacing of the frontend. Actual rendering of external scripts may vary.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const placements = ['ad_header_leaderboard', 'ad_in_article', 'ad_sidebar'];

    placements.forEach(function(placement) {
        const textarea = document.getElementById(placement);
        const fileInput = document.getElementById(placement + '_image');
        const linkInput = document.getElementById(placement + '_link');
        const previewDiv = document.getElementById('preview_' + placement);
        
        // Resolve initial image URL if it exists
        let activeImageUrl = '';
        const initialImg = previewDiv.querySelector('img');
        if (initialImg) {
            activeImageUrl = initialImg.getAttribute('src');
        }

        function updatePreview(html) {
            if (!html || html.trim() === '') {
                previewDiv.innerHTML = '<span class="text-text-muted text-xs font-semibold">No ad placed</span>';
            } else {
                previewDiv.innerHTML = '<div class="scale-[0.95] origin-center max-w-full flex justify-center transition-all duration-300">' + html + '</div>';
            }
        }

        // Live update from Option A (textarea)
        textarea.addEventListener('input', function() {
            updatePreview(textarea.value);
            // If user manually clears Option A, reset Option B inputs
            if (textarea.value.trim() === '') {
                fileInput.value = '';
                linkInput.value = '';
                activeImageUrl = '';
                const currentImgPreview = document.getElementById('current_img_' + placement);
                if (currentImgPreview) {
                    currentImgPreview.remove();
                }
            }
        });

        // Helper to regenerate HTML based on Option B inputs
        function syncOptionB() {
            const link = linkInput.value.trim() || '#';
            const target = link === '#' ? '' : ' target="_blank" rel="sponsored"';
            
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const dataUrl = e.target.result;
                    const generatedHtml = `<a href="${link}"${target} class="block hover:opacity-90 transition-opacity"><img src="${dataUrl}" alt="Advertisement" class="w-full h-auto rounded-lg mx-auto"></a>`;
                    textarea.value = generatedHtml;
                    updatePreview(generatedHtml);
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else if (activeImageUrl) {
                const generatedHtml = `<a href="${link}"${target} class="block hover:opacity-90 transition-opacity"><img src="${activeImageUrl}" alt="Advertisement" class="w-full h-auto rounded-lg mx-auto"></a>`;
                textarea.value = generatedHtml;
                updatePreview(generatedHtml);
            }
        }

        // Live update from Link Input
        linkInput.addEventListener('input', function() {
            if (fileInput.files.length > 0 || activeImageUrl) {
                syncOptionB();
            }
        });

        // Live update from File Input
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                syncOptionB();
                const currentImgPreview = document.getElementById('current_img_' + placement);
                if (currentImgPreview) {
                    currentImgPreview.style.opacity = '0.5';
                }
            }
        });
    });

    // Global clear function accessible by inline onclick attributes
    window.clearPlacementBanner = function(placement) {
        const textarea = document.getElementById(placement);
        const fileInput = document.getElementById(placement + '_image');
        const linkInput = document.getElementById(placement + '_link');
        const currentImgPreview = document.getElementById('current_img_' + placement);

        textarea.value = '';
        fileInput.value = '';
        linkInput.value = '';
        
        if (currentImgPreview) {
            currentImgPreview.remove();
        }
        
        // Trigger live preview update
        textarea.dispatchEvent(new Event('input'));
    };
});
</script>
@endsection
