{{-- Ad-blocker Bait Element --}}
<div id="adsbox" class="adsbox bg-transparent" style="position: absolute; left: -9999px; width: 1px; height: 1px;"></div>

{{-- Ad-blocker Modal --}}
<div id="adblock-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4" role="dialog" aria-modal="true" tabindex="-1" aria-labelledby="adblock-title" aria-describedby="adblock-desc">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-navy-950/80 backdrop-blur-md transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-2xl bg-navy-900 border border-navy-700/50 rounded-2xl shadow-2xl shadow-black/60 overflow-hidden animate-in fade-in zoom-in duration-300">
        
        <!-- Subtle top gradient accent -->
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-electric via-cyan-glow to-electric opacity-80"></div>

        <!-- Close button (top right) -->
        <button id="adblock-close" class="absolute top-4 right-4 text-text-muted hover:text-text-primary focus:outline-none transition-colors hidden z-10 w-8 h-8 flex items-center justify-center rounded-full hover:bg-navy-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="px-8 py-10 md:px-12 md:py-12 flex flex-col md:flex-row gap-8 items-center">
            
            <div class="flex-1">
                <!-- Logo badge + Heading -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-electric to-cyan-glow text-white font-bold text-lg rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-electric/30">
                        {{ substr(\App\Models\Setting::get('site_name', 'Atomni'), 0, 1) }}
                    </div>
                    <h2 id="adblock-title" class="text-2xl md:text-3xl font-bold text-text-primary leading-tight tracking-tight">
                        You can't put a price on truth — but it still needs paying for.
                    </h2>
                </div>

                <!-- Body text -->
                <div id="adblock-desc" class="space-y-3 mb-8 text-text-muted text-base leading-relaxed">
                    <p>Journalism like ours takes time, rigour, and resources. Please disable your ad blocker or consider supporting us.</p>
                    <p class="font-semibold text-text-secondary">That's how real journalism survives.</p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col gap-3 w-full max-w-md">
                    @php
                        $donateLink = \App\Models\Setting::get('donation_link', '#');
                    @endphp
                    <a href="{{ $donateLink }}" onclick="dismissAdblockModal();"
                       class="w-full bg-gradient-to-r from-electric to-cyan-glow hover:from-electric-light hover:to-cyan-glow text-white font-bold text-center py-3.5 px-6 rounded-xl transition-all duration-200 text-base shadow-lg shadow-electric/25 hover:shadow-electric/40 hover:-translate-y-0.5">
                        ✦ &nbsp;Donate to support
                    </a>
                    
                    <button id="adblock-dismiss-btn"
                            class="w-full bg-navy-800 hover:bg-navy-700 text-text-muted hover:text-text-primary border border-navy-700/60 hover:border-navy-600 font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 text-base">
                        I don't want to support
                    </button>
                </div>
            </div>

            <!-- Illustration (right side) -->
            <div class="hidden md:flex shrink-0 w-44 h-44 relative justify-center items-center">
                <!-- Glow blobs -->
                <div class="absolute w-28 h-28 bg-electric/20 rounded-full blur-2xl top-0 right-0"></div>
                <div class="absolute w-20 h-20 bg-cyan-500/15 rounded-full blur-xl bottom-4 -left-2"></div>

                <!-- Browser Window -->
                <div class="relative z-10 w-36 h-36 bg-navy-800 border border-navy-600/60 rounded-lg shadow-xl overflow-hidden flex flex-col">
                    <!-- Browser Top Bar -->
                    <div class="h-6 border-b border-navy-600/60 bg-navy-900 flex items-center px-1.5 gap-1 shrink-0">
                        <div class="w-2.5 h-2.5 rounded-full border border-navy-600 bg-navy-700"></div>
                        <div class="w-2.5 h-2.5 rounded-full border border-navy-600 bg-navy-700"></div>
                        <div class="w-3 h-3 ml-auto border border-navy-600 rounded-sm bg-navy-700"></div>
                    </div>
                    <!-- Browser Content -->
                    <div class="flex-1 p-3 flex flex-col bg-navy-800/80 relative">
                        <div class="w-full h-[3px] mb-2 bg-navy-600 rounded"></div>
                        <div class="w-full h-[3px] mb-2 bg-navy-600 rounded"></div>
                        <div class="w-3/4 h-[3px] mb-2 bg-navy-600 rounded"></div>
                        <div class="w-1/2 h-[3px] mb-2 bg-navy-600 rounded"></div>

                        <span class="text-[2.75rem] leading-none font-black text-electric italic drop-shadow-md absolute top-4 left-3"
                              style="font-family: 'Inter', sans-serif; text-shadow: 2px 2px 0px rgba(0,0,0,0.5);">AD</span>

                        <!-- Electric Cursor Arrow -->
                        <svg class="w-10 h-10 text-cyan-400 absolute top-12 left-16 z-20 drop-shadow-lg transform -rotate-12"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 2l12 11.2-5.8.5 3.3 7.3-2.2 1-3.2-7.4-4.4 4.8z" stroke="#0A0E27" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>

                <!-- Warning Triangle -->
                <div class="absolute -bottom-1 -right-4 w-[4.5rem] h-[4.5rem] text-electric z-30 transform rotate-6"
                     style="filter: drop-shadow(2px 2px 0px rgba(0,0,0,0.6));">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L1 21h22M12 6l7.5 13h-15M11 10h2v5h-2M11 16h2v2h-2" stroke="#0A0E27" stroke-width="1.5"/>
                    </svg>
                </div>

                <!-- Bottom accent dashes -->
                <div class="absolute -bottom-4 w-52 flex justify-center gap-1.5 z-0 items-center">
                    <div class="w-20 h-0.5 bg-navy-600 rounded"></div>
                    <div class="w-6 h-0.5 bg-navy-600 rounded"></div>
                    <div class="w-4 h-0.5 bg-navy-600 rounded"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let adblockPreviousActiveElement = null;

    function dismissAdblockModal() {
        document.getElementById('adblock-modal').classList.add('hidden');
        document.getElementById('adblock-modal').classList.remove('flex');
        // Set cookie/localStorage to remember dismissal for 30 days
        var expiry = new Date().getTime() + (30 * 24 * 60 * 60 * 1000);
        localStorage.setItem('adblock_dismissed', expiry);
        if (adblockPreviousActiveElement) {
            adblockPreviousActiveElement.focus();
        }
    }

    function checkAdblock() {
        // If already dismissed and not expired
        const dismissedUntil = localStorage.getItem('adblock_dismissed');
        if (dismissedUntil && parseInt(dismissedUntil) > new Date().getTime()) {
            return;
        }

        // Delay detection slightly to give ad blocker time to hide the element
        setTimeout(function() {
            var bait = document.getElementById('adsbox');
            var isAdBlocked = false;

            // Check if the element was hidden, removed, or dimensions changed
            if (!bait) {
                isAdBlocked = true;
            } else {
                var computedStyle = window.getComputedStyle(bait);
                if (bait.offsetHeight === 0 || bait.style.display === 'none' || computedStyle.display === 'none') {
                    isAdBlocked = true;
                }
            }

            if (isAdBlocked) {
                var modal = document.getElementById('adblock-modal');
                adblockPreviousActiveElement = document.activeElement;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Block scrolling on body
                document.body.style.overflow = 'hidden';
            }
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('atomni-cookie-preference')) {
            checkAdblock();
        } else {
            window.addEventListener('cookieConsentResolved', function() {
                checkAdblock();
            });
        }

        // UI Event Listeners
        document.getElementById('adblock-dismiss-btn').addEventListener('click', function() {
            dismissAdblockModal();
            document.body.style.overflow = '';
        });
        
        document.getElementById('adblock-close').addEventListener('click', function() {
            dismissAdblockModal();
            document.body.style.overflow = '';
        });

        // Accessibility: Focus Trapping
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('adblock-modal');
            if (!modal || modal.classList.contains('hidden')) return;

            if (e.key === 'Escape') {
                dismissAdblockModal();
                document.body.style.overflow = '';
                return;
            }

            if (e.key === 'Tab') {
                const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusableElements.length === 0) return;
                
                // Exclude hidden elements
                const visibleFocusableElements = Array.from(focusableElements).filter(el => {
                    const style = window.getComputedStyle(el);
                    return style.display !== 'none' && style.visibility !== 'hidden' && !el.classList.contains('hidden');
                });

                if (visibleFocusableElements.length === 0) return;

                const firstElement = visibleFocusableElements[0];
                const lastElement = visibleFocusableElements[visibleFocusableElements.length - 1];

                if (e.shiftKey) { // Shift + Tab
                    if (document.activeElement === firstElement) {
                        lastElement.focus();
                        e.preventDefault();
                    }
                } else { // Tab
                    if (document.activeElement === lastElement) {
                        firstElement.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    });
</script>
