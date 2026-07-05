<!DOCTYPE html>
<?php $theme_color = \App\Models\Setting::get('theme_color', 'blue'); ?>
<html lang="en" class="scroll-smooth theme-<?php echo e($theme_color); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to Atomni">

    <title><?php echo $__env->yieldContent('title', 'Login - Atomni'); ?></title>

    
    <?php $favicon = \App\Models\Setting::get('site_favicon', ''); ?>
    <?php if($favicon && file_exists(public_path('storage/' . $favicon))): ?>
        <link rel="icon" href="<?php echo e(\Illuminate\Support\Facades\Storage::url($favicon)); ?>">
    <?php else: ?>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📰</text></svg>">
    <?php endif; ?>

    <script>
        (function() {
            var t = localStorage.getItem('atomni-theme');
            if (t === 'light') document.documentElement.classList.add('light');
        })();
    </script>

    
    <?php
        $theme_type = \App\Models\Setting::get('theme_type', 'preset');
        $font_family = \App\Models\Setting::get('font_family', 'Inter');
        // Handle legacy font keys
        if ($font_family === 'inter') $font_family = 'Inter';
        if ($font_family === 'roboto') $font_family = 'Roboto';
        if ($font_family === 'dm_sans') $font_family = 'DM Sans';
        
        $encoded_font = str_replace(' ', '+', $font_family);
        $font_css = "'{$font_family}', sans-serif";
        
        $primary = \App\Models\Setting::get('theme_manual_primary', '#2D7FF9');
        $secondary = \App\Models\Setting::get('theme_manual_secondary', '#1A5FD1');
    ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link id="app-font" href="https://fonts.googleapis.com/css2?family=<?php echo e($encoded_font); ?>:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-sans: <?php echo $font_css; ?> !important;
            --font-heading: <?php echo $font_css; ?> !important;
        }
        <?php if($theme_type === 'manual'): ?>
        :root {
            --color-electric: <?php echo e($primary); ?> !important;
            --color-accent-blue: <?php echo e($primary); ?> !important;
            --color-accent-blue-hover: <?php echo e($secondary); ?> !important;
            --color-cyan-glow: <?php echo e($secondary); ?> !important;
            --color-brand-primary: <?php echo e($primary); ?> !important;
            --color-brand-secondary: <?php echo e($secondary); ?> !important;
            --color-electric-dark: <?php echo e($secondary); ?> !important;
            --color-electric-light: color-mix(in srgb, <?php echo e($primary); ?> 70%, white) !important;
        }
        <?php endif; ?>
        /* Base hidden states */
        img.logo-light, .logo-light { display: none !important; }
        img.logo-dark, .logo-dark  { display: none !important; }
        
        /* Dark Theme Roles (Default) */
        html:not(.light) img.logo-dark, html:not(.light) .logo-dark { display: block !important; }
        
        /* Light Theme Roles */
        html.light img.logo-light, html.light .logo-light { display: block !important; }
    </style>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-navy-950 text-text-primary font-sans antialiased min-h-screen transition-colors duration-300 flex flex-col">
    <?php echo $__env->yieldContent('content'); ?>
    <?php echo $__env->make('partials.adblock-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/layouts/guest.blade.php ENDPATH**/ ?>