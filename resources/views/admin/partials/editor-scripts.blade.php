{{-- TinyMCE Editor Script (free self-hosted via jsDelivr, no API key needed) --}}
<style>
    .tox-tinymce { max-width: 100% !important; }
    .tox.tox-tinymce { width: 100% !important; }
</style>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        plugins: 'advlist autolink lists link charmap anchor pagebreak searchreplace wordcount visualblocks code fullscreen insertdatetime media nonbreaking table emoticons codesample autoresize',

        // Primary toolbar — clean and mobile-friendly
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | atomni_image link | bullist numlist | align | code fullscreen',
        toolbar_mode: 'sliding',   // overflowing buttons slide into a drawer on mobile

        min_height: 420,
        max_height: 850,
        autoresize_bottom_margin: 32,

        menubar: false,   // hide menu bar — all actions are in toolbar
        statusbar: false, // hide status bar to fix ARIA accessibility errors
        branding: false,
        promotion: false,
        resize: true,

        // Always use dark skin to match admin theme
        skin: 'oxide-dark',
        content_css: 'dark',
        content_style: [
            'body {',
            '  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;',
            '  font-size: 16px;',
            '  line-height: 1.75;',
            '  padding: 16px 20px;',
            '  color: #e2e8f0;',
            '}',
            'img { max-width: 100%; height: auto; border-radius: 8px; margin: 8px 0; }',
        ].join(''),

        // ── Image paste / drag-drop upload handler ────────────────────────
        // Fires whenever an image blob is dropped or pasted into the editor.
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var formData = new FormData();
                formData.append('files[]', blobInfo.blob(), blobInfo.filename());
                var csrf = document.querySelector('meta[name="csrf-token"]');
                if (csrf) formData.append('_token', csrf.content);

                fetch('{{ route("admin.media.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                })
                .then(function (r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function (data) {
                    // Response: { success, media: [{ file_path, ... }] }
                    var items = data.media || [];
                    var item  = Array.isArray(items) ? items[0] : items;
                    if (item && item.file_path) {
                        var storageBase = '{{ asset("storage") }}/';
                        resolve(storageBase + (item.file_path || item.webp_path));
                    } else {
                        reject({ message: 'Upload succeeded but no URL returned.', remove: true });
                    }
                })
                .catch(function (err) {
                    reject({ message: 'Upload failed: ' + err.message, remove: true });
                });
            });
        },

        // ── File picker — opens the Atomni Media Library ──────────────────
        // Used by the native TinyMCE link/image dialogs.
        file_picker_types: 'image media',
        file_picker_callback: function (tinyCallback, value, meta) {
            if (typeof openMediaSelector !== 'function') {
                alert('Media Library is not available.');
                return;
            }
            // Store the TinyMCE callback so the media selector can invoke it
            // after the user confirms their selection.
            openMediaSelector({
                onSelect: function (media) {
                    tinyCallback(media.url, {
                        alt: media.alt_text || media.file_name || ''
                    });
                }
            });
        },

        setup: function (editor) {
            // ── Sync textarea on every meaningful change ──────────────────
            editor.on('change input NodeChange Undo Redo', function () {
                editor.save();   // pushes HTML into the hidden <textarea #content>
            });

            // ── Auto-fill taxonomy on blur ──────────────────────────────
            editor.on('blur', function () {
                if (typeof autoFillTaxonomy === 'function') {
                    autoFillTaxonomy(false);
                }
            });

            // ── Custom toolbar button: "Insert from Media Library" ────────
            // Bypasses TinyMCE's own image dialog entirely — much more
            // reliable on mobile and avoids the iframe-on-top-of-modal issue.
            editor.ui.registry.addButton('atomni_image', {
                icon: 'image',
                tooltip: 'Insert image from Media Library',
                onAction: function () {
                    if (typeof openMediaSelector !== 'function') return;
                    openMediaSelector({
                        onSelect: function (media) {
                            var alt = media.alt_text || media.file_name || '';
                            editor.insertContent(
                                '<img src="' + media.url + '" alt="' + alt + '" style="max-width:100%;height:auto;border-radius:8px;margin:8px 0;">'
                            );
                        }
                    });
                }
            });
        }
    });
</script>
