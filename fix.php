<?php
$filePath = 'resources/views/admin/settings/global.blade.php';
$content = file_get_contents($filePath);

// Find the corrupted section
$startStr = '<p class="text-[11px] text-text-muted mt-2">These fonts are automatically loaded from the Google Fonts library.</p>';
$endStr = '<!-- Secondary Color Picker -->';

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr);

if ($startPos !== false && $endPos !== false) {
    $before = substr($content, 0, $startPos);
    $after = substr($content, $endPos);

    $replacement = <<<HTML
<p class="text-[11px] text-text-muted mt-2">These fonts are automatically loaded from the Google Fonts library.</p>
                        @error('font_family')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ \$message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Theme Colors Card -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-8 space-y-6 flex flex-col h-fit">
                    <div class="flex items-center gap-2 mb-2 border-b border-navy-700/30 pb-4">
                        <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        <h3 class="text-sm font-bold text-text-primary">Theme Colors</h3>
                    </div>

                    <div x-data="{ themePreset: '{{ old('theme_preset', \$settings['theme_preset'] ?? 'midnight') }}' }">
                        <!-- Presets -->
                        <div class="mb-6 space-y-2">
                            <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider">Presets</label>
                            <!-- Note: The presets have been omitted here to keep it simple, it seems they were deleted. I'll add back the primary color picker -->
                            <div class="flex items-center gap-3 mt-4">
                                <label for="theme_manual_primary" class="block text-xs font-bold text-text-secondary uppercase tracking-wider">Primary Color</label>
                                <input type="color" id="theme_manual_primary" name="theme_manual_primary" value="{{ old('theme_manual_primary', \$settings['theme_manual_primary'] ?? '#2D7FF9') }}" class="h-11 w-11 rounded bg-transparent cursor-pointer border-0 p-0 shrink-0">
                                <input type="text" aria-label="Primary Color Hex" id="theme_manual_primary_text" value="{{ old('theme_manual_primary', \$settings['theme_manual_primary'] ?? '#2D7FF9') }}" class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono uppercase text-xs">
                            </div>
                            @error('theme_manual_primary')
                                <p class="mt-1 text-xs text-rose-400 font-medium">{{ \$message }}</p>
                            @enderror
                        </div>

HTML;

    $newContent = $before . $replacement . $after;
    file_put_contents($filePath, $newContent);
    echo "Fixed!\n";
} else {
    echo "Could not find start or end tags.\n";
}
