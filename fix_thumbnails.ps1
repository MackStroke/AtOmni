$files = Get-ChildItem -Path "resources\views" -Recurse -Filter "*.blade.php"
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $pattern = '<div class="w-full h-full bg-navy-800 flex items-center justify-center">\s*<svg class="w-\d+ h-\d+ text-navy-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4\.586-4\.586a2 2 0 012\.828 0L16 16m-2-2l1\.586-1\.586a2 2 0 012\.828 0L20 14m-6-6h\.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>\s*</div>'
    $replacement = '<img src="{{ asset(''images/atomni-placeholder.svg'') }}" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">'
    if ($content -match $pattern) {
        $content = $content -replace $pattern, $replacement
        Set-Content -Path $file.FullName -Value $content
        Write-Host "Updated $($file.Name)"
    }
}
