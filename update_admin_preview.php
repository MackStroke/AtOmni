<?php
$file = 'resources/views/admin/homepage-sections/index.blade.php';
$content = file_get_contents($file);

// Replace `<div class="glass-card rounded-2xl overflow-hidden mt-3">` with a grid wrapper
$gridWrapper = <<<EOD
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-3">
    <!-- Left: Table -->
    <div class="glass-card rounded-2xl overflow-hidden">
EOD;

$content = str_replace('<div class="glass-card rounded-2xl overflow-hidden mt-3">', $gridWrapper, $content);

// Find the end of the glass-card to inject the right iframe
$iframeHtml = <<<EOD
    </div>

    <!-- Right: Live Preview -->
    <div class="glass-card rounded-2xl overflow-hidden flex flex-col h-[700px] border border-navy-700/50">
        <div class="bg-navy-800/50 px-4 py-3 border-b border-navy-700/30 flex items-center justify-between">
            <h3 class="font-bold text-text-primary flex items-center gap-2">
                <svg class="w-4 h-4 text-electric" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Live Preview
            </h3>
            <span class="text-xs text-text-muted bg-navy-900 px-2 py-1 rounded">Updates automatically</span>
        </div>
        <iframe id="preview-iframe" src="{{ route('home') }}#dynamic-sections" class="w-full flex-1 bg-white" frameborder="0"></iframe>
    </div>
</div>
EOD;

$content = preg_replace('/<\/table>\s*<\/div>\s*<\/div>/is', "</table>\n    </div>\n" . $iframeHtml, $content, 1);

// Update JS
$jsReplace = <<<EOD
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
EOD;

$content = preg_replace('/fetch\(\'\{\{ route\("admin\.homepage-sections\.update-order"\) \}\}\'.*?console\.log\(\'Order saved\'\);\s*\}\s*\}\);/is', $jsReplace, $content);

file_put_contents($file, $content);
echo "Done";
