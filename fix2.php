<?php
$filePath = 'resources/views/admin/settings/global.blade.php';
$content = file_get_contents($filePath);

// Find the first Typography Card
$typographyStart = strpos($content, '<!-- Typography Card -->');

// Find the first font_family block inside the Typography Card
$fontFamilyBlockStr = <<<HTML
                        <p class="text-[11px] text-text-muted mt-2">These fonts are automatically loaded from the Google Fonts library.</p>
                        @error('font_family')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ \$message }}</p>
                        @enderror
                    </div>
HTML;

$fontFamilyPos = strpos($content, $fontFamilyBlockStr, $typographyStart);

if ($typographyStart !== false && $fontFamilyPos !== false) {
    // Keep everything up to the end of the font family block
    $cutPos = $fontFamilyPos + strlen($fontFamilyBlockStr);
    $validContent = substr($content, 0, $cutPos);
    
    // Add the rest of the Typography Card and close the form/page correctly
    $closingContent = <<<HTML

                    
                    <div class="flex-1 flex flex-col">
                        <div class="block text-sm font-bold text-text-secondary mb-2 mt-4">Live Font Preview</div>
                        <div class="flex-1 p-6 rounded-xl bg-navy-950/60 border border-navy-700/30 min-h-[120px] flex flex-col justify-center text-center">
                            <h4 id="font_preview_heading" class="text-xl sm:text-3xl font-bold text-text-primary mb-2">The quick brown fox</h4>
                            <p id="font_preview_body" class="text-sm text-text-muted mt-2 leading-relaxed">Jumps over the lazy dog. 1234567890 !@#$%^&*()</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('admin-assets/js/theme-customizer.js') }}"></script>
@endsection
HTML;

    file_put_contents($filePath, $validContent . $closingContent);
    echo "Fixed layout distortion.\n";
} else {
    echo "Could not find Typography Card or font family block.\n";
}
