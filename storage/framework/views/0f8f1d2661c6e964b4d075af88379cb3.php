
<div id="media-selector-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="media-selector-title" role="dialog" aria-modal="true">
    
    <div class="fixed inset-0 bg-navy-950/80 backdrop-blur-sm transition-opacity opacity-0" id="media-selector-backdrop"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-navy-900 border border-navy-700/50 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-5xl opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="media-selector-panel">
                
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-navy-700/50 bg-navy-900/80 backdrop-blur-md">
                    <h3 class="text-xl font-bold text-text-primary" id="media-selector-title">Select Media</h3>
                    <div class="flex items-center gap-3">
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-electric/15 text-electric hover:bg-electric/25 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Upload
                            <input type="file" aria-label="Upload Media" id="media-selector-upload" accept="image/*,video/*" class="hidden" onchange="handleMediaSelectorUpload(this)">
                        </label>
                        <input type="text" aria-label="Search media" id="media-selector-search" placeholder="Search..." class="px-3 py-1.5 rounded-lg bg-navy-800 border border-navy-700/50 text-sm focus:border-electric focus:ring-1 focus:ring-electric w-48 transition-all">
                        <button type="button" aria-label="Close media selector" onclick="closeMediaSelector()" class="text-text-muted hover:text-text-primary hover:bg-navy-800 p-2 rounded-lg transition-colors focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                
                <div class="flex h-[60vh] overflow-hidden">
                    
                    <div class="flex-1 overflow-y-auto p-6 bg-navy-950/40 relative" id="media-selector-grid-container">
                        <div id="media-selector-loading" class="absolute inset-0 bg-navy-950/80 flex items-center justify-center z-10 hidden">
                            <svg class="animate-spin h-8 w-8 text-electric" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <div id="media-selector-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            
                        </div>
                        
                        <div id="media-selector-pagination" class="mt-8 flex justify-center gap-2 hidden">
                            <button id="ms-prev-page" class="px-3 py-1 bg-navy-800 border border-navy-700/50 rounded hover:bg-navy-700 text-sm disabled:opacity-50">Prev</button>
                            <span id="ms-page-info" class="text-sm py-1"></span>
                            <button id="ms-next-page" class="px-3 py-1 bg-navy-800 border border-navy-700/50 rounded hover:bg-navy-700 text-sm disabled:opacity-50">Next</button>
                        </div>

                        <div id="media-selector-empty" class="hidden flex flex-col items-center justify-center h-full text-center">
                            <div class="w-16 h-16 rounded-full bg-navy-800/50 flex flex-col items-center justify-center text-navy-600 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-text-secondary font-medium">No media found.</p>
                        </div>
                    </div>
                </div>

                
                <div class="bg-navy-900 border-t border-navy-700/50 px-6 py-4 flex items-center justify-between">
                    <p class="text-xs text-text-muted">Selected: <span id="media-selector-selected-name" class="font-bold text-electric">None</span></p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeMediaSelector()" class="btn-secondary">Cancel</button>
                        <button type="button" id="media-selector-confirm" class="btn-primary" disabled>Insert Media</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let mediaSelectorConfig = {
        onSelect: null,
        isOpen: false,
        currentPage: 1,
        searchQuery: '',
        selectedMedia: null,
        storageUrl: '<?php echo e(asset("storage")); ?>/'
    };

    const msModal = document.getElementById('media-selector-modal');
    const msBackdrop = document.getElementById('media-selector-backdrop');
    const msPanel = document.getElementById('media-selector-panel');
    const msGrid = document.getElementById('media-selector-grid');
    const msLoading = document.getElementById('media-selector-loading');
    const msEmpty = document.getElementById('media-selector-empty');
    const msConfirmBtn = document.getElementById('media-selector-confirm');
    const msSearchInput = document.getElementById('media-selector-search');
    
    // Pagination elements
    const msPagination = document.getElementById('media-selector-pagination');
    const msPrevBtn = document.getElementById('ms-prev-page');
    const msNextBtn = document.getElementById('ms-next-page');
    const msPageInfo = document.getElementById('ms-page-info');

    window.openMediaSelector = function(options = {}) {
        mediaSelectorConfig.onSelect = options.onSelect || null;
        mediaSelectorConfig.isOpen = true;
        
        // Reset state
        mediaSelectorConfig.selectedMedia = null;
        msConfirmBtn.disabled = true;
        document.getElementById('media-selector-selected-name').textContent = 'None';
        
        // Show modal
        msModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Animate in
        setTimeout(() => {
            msBackdrop.classList.replace('opacity-0', 'opacity-100');
            msPanel.classList.replace('opacity-0', 'opacity-100');
            msPanel.classList.replace('translate-y-4', 'translate-y-0');
            msPanel.classList.replace('sm:scale-95', 'sm:scale-100');
        }, 10);

        fetchMedia(1);
    }

    window.closeMediaSelector = function() {
        mediaSelectorConfig.isOpen = false;
        
        // Animate out
        msBackdrop.classList.replace('opacity-100', 'opacity-0');
        msPanel.classList.replace('opacity-100', 'opacity-0');
        msPanel.classList.replace('translate-y-0', 'translate-y-4');
        msPanel.classList.replace('sm:scale-100', 'sm:scale-95');
        
        setTimeout(() => {
            msModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    async function fetchMedia(page = 1) {
        mediaSelectorConfig.currentPage = page;
        msLoading.classList.remove('hidden');
        msGrid.innerHTML = '';
        msEmpty.classList.add('hidden');
        msPagination.classList.add('hidden');

        try {
            const query = new URLSearchParams({
                page: page,
                search: mediaSelectorConfig.searchQuery
            });

            const response = await fetch(`<?php echo e(route('admin.media.index')); ?>?${query.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Failed to load media');

            const data = await response.json();
            
            if (data.data && data.data.length > 0) {
                renderGrid(data.data);
                
                // Setup pagination if needed
                if (data.last_page > 1) {
                    msPagination.classList.remove('hidden');
                    msPageInfo.textContent = `Page ${data.current_page} of ${data.last_page}`;
                    
                    msPrevBtn.disabled = data.current_page === 1;
                    msPrevBtn.onclick = () => fetchMedia(data.current_page - 1);
                    
                    msNextBtn.disabled = data.current_page === data.last_page;
                    msNextBtn.onclick = () => fetchMedia(data.current_page + 1);
                }
            } else {
                msEmpty.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error fetching media:', error);
            msEmpty.classList.remove('hidden');
            msEmpty.innerHTML = `<p class="text-rose-400">Error loading media library.</p>`;
        } finally {
            msLoading.classList.add('hidden');
        }
    }

    function renderGrid(items) {
        msGrid.innerHTML = items.map(item => {
            const isImage = item.mime_type.startsWith('image/');
            const displayUrl = item.webp_path ? item.webp_path : item.file_path;
            const fullUrl = mediaSelectorConfig.storageUrl + displayUrl;
            
            let previewContent = '';
            if (isImage) {
                previewContent = `<img loading="lazy" src="${fullUrl}" alt="${item.file_name}" class="w-full h-full object-contain p-2">`;
            } else if (item.mime_type.startsWith('video/')) {
                previewContent = `<svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>`;
            } else {
                previewContent = `<svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;
            }

            return `
                <div class="ms-item cursor-pointer group relative bg-navy-900 border border-navy-700/50 rounded-xl overflow-hidden hover:border-electric transition-all aspect-square flex flex-col"
                     data-media-id="${item.id}"
                     data-media-json='${JSON.stringify({...item, url: fullUrl}).replace(/'/g, "&#39;")}'>
                    
                    <div class="flex-1 overflow-hidden bg-navy-950/50 flex items-center justify-center relative">
                        ${previewContent}
                        <div class="ms-check absolute inset-0 bg-electric/20 flex items-center justify-center opacity-0 transition-opacity backdrop-blur-[2px]">
                            <div class="bg-electric text-white rounded-full p-1 transform scale-50 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </div>
                    <div class="px-2 py-1.5 border-t border-navy-700/50 bg-navy-900">
                        <p class="text-[10px] text-text-secondary truncate text-center">${item.file_name}</p>
                    </div>
                </div>
            `;
        }).join('');

        // Attach click events
        document.querySelectorAll('.ms-item').forEach(el => {
            el.addEventListener('click', function() {
                // Deselect all
                document.querySelectorAll('.ms-item').forEach(i => {
                    i.classList.remove('ring-2', 'ring-electric', 'border-electric');
                    i.querySelector('.ms-check').classList.replace('opacity-100', 'opacity-0');
                    i.querySelector('.ms-check div').classList.replace('scale-100', 'scale-50');
                });
                
                // Select current
                this.classList.add('ring-2', 'ring-electric', 'border-electric');
                this.querySelector('.ms-check').classList.replace('opacity-0', 'opacity-100');
                this.querySelector('.ms-check div').classList.replace('scale-50', 'scale-100');

                // Update state
                const mediaData = JSON.parse(this.getAttribute('data-media-json'));
                mediaSelectorConfig.selectedMedia = mediaData;
                document.getElementById('media-selector-selected-name').textContent = mediaData.file_name;
                msConfirmBtn.disabled = false;
            });
        });
    }

    // Search input bounce
    let searchTimeout;
    msSearchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            mediaSelectorConfig.searchQuery = e.target.value;
            fetchMedia(1);
        }, 500);
    });

    // Confirm selection
    msConfirmBtn.addEventListener('click', () => {
        if (mediaSelectorConfig.selectedMedia && mediaSelectorConfig.onSelect) {
            mediaSelectorConfig.onSelect(mediaSelectorConfig.selectedMedia);
        }
        closeMediaSelector();
    });

    // Upload from media selector
    async function handleMediaSelectorUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const formData = new FormData();
        formData.append('files[]', file);
        formData.append('_token', '<?php echo e(csrf_token()); ?>');

        msLoading.classList.remove('hidden');
        try {
            const response = await fetch('<?php echo e(route("admin.media.store")); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) {
                const err = await response.json().catch(() => ({}));
                throw new Error(err.message || 'HTTP ' + response.status);
            }
            const data = await response.json();
            // Response shape: { success: true, message: '...', media: [MediaRecord] }
            if (!data.success) throw new Error(data.message || 'Upload failed');

            // Refresh grid, then auto-select the freshly uploaded item
            await fetchMedia(1);
            const firstItem = msGrid.querySelector('.ms-item');
            if (firstItem) firstItem.click();
        } catch (error) {
            console.error('Upload error:', error);
            alert('Upload failed: ' + error.message);
        } finally {
            msLoading.classList.add('hidden');
            input.value = ''; // Reset file input so same file can be re-selected
        }
    }
</script>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/components/admin/media-selector.blade.php ENDPATH**/ ?>