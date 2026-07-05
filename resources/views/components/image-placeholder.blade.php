@props(['text' => 'Atomni', 'class' => ''])

<div class="relative flex flex-col items-center justify-center w-full h-full min-h-[120px] bg-navy-900 overflow-hidden {{ $class }}">
    <!-- Background Pattern -->
    <svg class="absolute inset-0 w-full h-full opacity-[0.03] text-white" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="grid-pattern-{{ \Illuminate\Support\Str::random(5) }}" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="2" fill="none"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid-pattern-{{ \Illuminate\Support\Str::random(5) }})"/>
    </svg>
    
    <!-- Central Icon/Text -->
    <div class="relative z-10 flex flex-col items-center justify-center space-y-2 opacity-60">
        <svg class="w-8 h-8 text-accent-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="font-heading font-medium tracking-widest uppercase text-[10px] text-navy-300">{{ $text }}</span>
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-tr from-navy-950/80 to-transparent pointer-events-none"></div>
</div>
