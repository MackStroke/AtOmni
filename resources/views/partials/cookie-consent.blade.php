{{-- Cookie Consent Modal — Dark card with granular toggles --}}
<div id="cookie-consent" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:bottom-6 md:w-[420px] z-[60] hidden flex-col p-0" role="dialog" aria-modal="false" tabindex="-1" aria-labelledby="cookie-title" aria-describedby="cookie-desc">
    {{-- Card --}}
    <div class="relative w-full bg-[#1a1f2e] rounded-2xl shadow-2xl shadow-black/50 overflow-hidden border border-navy-700/30 animate-in slide-in-from-bottom-5 fade-in duration-300">

        {{-- Main View --}}
        <div id="cookie-main" class="p-7">
            <h2 id="cookie-title" class="text-xl font-bold text-white mb-3 tracking-tight">Cookie settings</h2>
            <p id="cookie-desc" class="text-sm text-slate-400 leading-relaxed mb-6">
                We use cookies to deliver and improve our services, analyze site usage, and if you agree, to customize or
                personalize your experience and market our services to you. You can read our
                <a href="{{ route('cookies') }}" class="text-white underline underline-offset-4 hover:text-electric transition-colors">Cookie Policy</a>
                for full details.
            </p>

            <div class="space-y-3">
                {{-- Customize Button --}}
                <button onclick="showCookieCustomize()" class="w-full py-3.5 px-6 rounded-xl bg-white text-[#1a1f2e] text-sm font-bold tracking-wide hover:bg-slate-100 transition-all border border-white/80 shadow-sm">
                    Customize Cookie Settings
                </button>

                {{-- Action Row --}}
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="handleCookieChoice('declined')" class="py-3.5 px-4 rounded-xl bg-transparent text-white text-sm font-bold tracking-wide border border-white/30 hover:bg-white/10 transition-all">
                        Reject All Cookies
                    </button>
                    <button onclick="handleCookieChoice('all')" class="py-3.5 px-4 rounded-xl bg-[#1a1f2e] text-white text-sm font-bold tracking-wide border border-white hover:bg-white hover:text-[#1a1f2e] transition-all">
                        Accept All Cookies
                    </button>
                </div>
            </div>
        </div>

        {{-- Customize View --}}
        <div id="cookie-customize" class="p-7 hidden">
            <button onclick="showCookieMain()" class="flex items-center gap-1.5 text-sm text-slate-400 hover:text-white transition-colors mb-4 group">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </button>

            <h2 class="text-xl font-bold text-white mb-3 tracking-tight">Cookie settings</h2>
            <p class="text-sm text-slate-400 leading-relaxed mb-6">
                Our website uses cookies to distinguish you from other users of our website. This helps us provide you with a
                more personalized experience when you browse our website and also allows us to improve our site. Cookies
                may collect information that is used to tailor ads shown to you on our website and other websites. The information
                might be about you, your preferences or your device. The information does not usually directly identify you, but it
                can give you a more personalized web experience. You can choose not to allow some types of cookies.
            </p>

            {{-- Toggle Items --}}
            <div class="space-y-3 mb-6">
                {{-- Necessary --}}
                <div class="flex items-center justify-between bg-navy-900/60 rounded-xl p-4 border border-navy-700/20">
                    <div>
                        <h3 class="text-sm font-bold text-white">Necessary</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Enables security and basic functionality.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-semibold">Required</span>
                        <div class="relative">
                            <input type="checkbox" checked disabled class="sr-only peer" id="cookie-necessary" name="cookie-necessary">
                            <div class="w-10 h-5 bg-electric rounded-full cursor-not-allowed after:content-[''] after:absolute after:top-0.5 after:left-[22px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                        </div>
                    </div>
                </div>

                {{-- Analytics --}}
                <div class="flex items-center justify-between bg-navy-900/60 rounded-xl p-4 border border-navy-700/20">
                    <div>
                        <h3 class="text-sm font-bold text-white">Analytics</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Enables tracking of site performance.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-semibold cookie-status" data-for="analytics">Off</span>
                        <label class="relative inline-flex items-center cursor-pointer" for="cookie-analytics">
                            <input type="checkbox" class="sr-only peer cookie-toggle" data-type="analytics" id="cookie-analytics" name="cookie-analytics">
                            <div class="w-10 h-5 bg-navy-700 peer-checked:bg-electric rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        </label>
                    </div>
                </div>

                {{-- Marketing --}}
                <div class="flex items-center justify-between bg-navy-900/60 rounded-xl p-4 border border-navy-700/20">
                    <div>
                        <h3 class="text-sm font-bold text-white">Marketing</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Enables ads personalization and tracking.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-semibold cookie-status" data-for="marketing">Off</span>
                        <label class="relative inline-flex items-center cursor-pointer" for="cookie-marketing">
                            <input type="checkbox" class="sr-only peer cookie-toggle" data-type="marketing" id="cookie-marketing" name="cookie-marketing">
                            <div class="w-10 h-5 bg-navy-700 peer-checked:bg-electric rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Save Preferences --}}
            <button onclick="saveCustomPreferences()" class="w-full py-3.5 px-6 rounded-xl bg-white text-[#1a1f2e] text-sm font-bold tracking-wide hover:bg-slate-100 transition-all border border-white/80 shadow-sm">
                Save preferences
            </button>
        </div>
    </div>
</div>

<script>
let cookiePreviousActiveElement = null;

(function() {
    const pref = localStorage.getItem('atomni-cookie-preference');
    if (!pref) {
        const el = document.getElementById('cookie-consent');
        if (el) {
            cookiePreviousActiveElement = document.activeElement;
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
    } else {
        window.dispatchEvent(new CustomEvent('cookieConsentResolved'));
    }
})();

window.showCookieCustomize = function() {
    document.getElementById('cookie-main').classList.add('hidden');
    document.getElementById('cookie-customize').classList.remove('hidden');
};

window.showCookieMain = function() {
    document.getElementById('cookie-customize').classList.add('hidden');
    document.getElementById('cookie-main').classList.remove('hidden');
};

// Toggle status labels
document.querySelectorAll('.cookie-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const type = this.dataset.type;
        const label = document.querySelector('.cookie-status[data-for="' + type + '"]');
        if (label) label.textContent = this.checked ? 'On' : 'Off';
    });
});

function handleCookieChoice(choice) {
    const prefs = { necessary: true, analytics: choice === 'all', marketing: choice === 'all' };
    localStorage.setItem('atomni-cookie-preference', JSON.stringify(prefs));
    // Consent Mode v2: notify Google tags of the updated consent state
    updateGtagConsent(prefs.analytics, prefs.marketing);
    window.dispatchEvent(new CustomEvent('cookiePreferenceUpdated', { detail: prefs }));
    window.dispatchEvent(new CustomEvent('cookieConsentResolved'));
    hideCookieModal();
}

function saveCustomPreferences() {
    const analytics = document.querySelector('.cookie-toggle[data-type="analytics"]');
    const marketing = document.querySelector('.cookie-toggle[data-type="marketing"]');
    const prefs = {
        necessary: true,
        analytics: analytics ? analytics.checked : false,
        marketing: marketing ? marketing.checked : false
    };
    localStorage.setItem('atomni-cookie-preference', JSON.stringify(prefs));
    // Consent Mode v2: notify Google tags of the updated consent state
    updateGtagConsent(prefs.analytics, prefs.marketing);
    window.dispatchEvent(new CustomEvent('cookiePreferenceUpdated', { detail: prefs }));
    window.dispatchEvent(new CustomEvent('cookieConsentResolved'));
    hideCookieModal();
}

function hideCookieModal() {
    const el = document.getElementById('cookie-consent');
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
    if (cookiePreviousActiveElement) {
        cookiePreviousActiveElement.focus();
    }
}

/**
 * Consent Mode v2 — send gtag consent update with all 4 required parameters.
 * analytics_storage  → Analytics toggle
 * ad_storage         → Marketing toggle (controls ad cookies)
 * ad_user_data       → Marketing toggle (controls sending user data for ads) [NEW in v2]
 * ad_personalization → Marketing toggle (controls ad personalization)        [NEW in v2]
 */
function updateGtagConsent(analyticsGranted, marketingGranted) {
    if (typeof gtag !== 'function') return;
    gtag('consent', 'update', {
        'analytics_storage':   analyticsGranted  ? 'granted' : 'denied',
        'ad_storage':          marketingGranted  ? 'granted' : 'denied',
        'ad_user_data':        marketingGranted  ? 'granted' : 'denied',
        'ad_personalization':  marketingGranted  ? 'granted' : 'denied'
    });
}

// Make globally accessible
window.handleCookieChoice = handleCookieChoice;

// Accessibility: Focus Trapping
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('cookie-consent');
    if (!modal || modal.classList.contains('hidden')) return;

    // Optional: close on Escape (though usually cookie consent forces a choice, we can allow escape to reject or ignore)
    // If you don't want them to bypass, remove this if block. Let's allow bypass = decline.
    if (e.key === 'Escape') {
        handleCookieChoice('declined');
        return;
    }
});

// Set initial focus when opened
const originalRemove = DOMTokenList.prototype.remove;
DOMTokenList.prototype.remove = function() {
    originalRemove.apply(this, arguments);
    if (this.contains('hidden') === false && document.getElementById('cookie-consent') && document.getElementById('cookie-consent').classList === this) {
        setTimeout(() => {
            const firstBtn = document.getElementById('cookie-consent').querySelector('button');
            if (firstBtn) firstBtn.focus();
        }, 50);
    }
};
</script>
