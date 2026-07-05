
<div id="topbar" class="fixed inset-x-0 bg-navy-900 border-b border-navy-700/50 light:bg-slate-50 light:border-slate-200" style="top:0;z-index:55;height:30px">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center justify-between h-full text-[11px]">

            
            <div class="flex items-center gap-3 sm:gap-4">
                
                <div class="flex items-center gap-1.5 text-text-secondary">
                    <span id="topbar-flag" class="text-sm">🇮🇳</span>
                    <span class="font-semibold uppercase tracking-wider">Edition</span>
                    <span id="topbar-country-code" class="font-bold text-text-primary">IN</span>
                </div>

                <div class="w-px h-3.5 bg-navy-700/50 light:bg-slate-300"></div>

                
                <div class="relative">
                    <label for="topbar-lang" class="sr-only">Select language</label>
                    <select id="topbar-lang" class="appearance-none bg-transparent text-slate-700 dark:text-slate-300 font-medium pr-4 cursor-pointer focus:outline-none hover:text-slate-900 dark:hover:text-white transition-colors text-[11px] [html:not(.light)_&]:text-slate-300 [html.light_&]:text-slate-700">
                        <option value="en" class="bg-navy-900 text-white light:bg-white light:text-slate-800">English</option>
                        <option value="hi" class="bg-navy-900 text-white light:bg-white light:text-slate-800">हिन्दी</option>
                        <option value="es" class="bg-navy-900 text-white light:bg-white light:text-slate-800">Español</option>
                        <option value="fr" class="bg-navy-900 text-white light:bg-white light:text-slate-800">Français</option>
                        <option value="de" class="bg-navy-900 text-white light:bg-white light:text-slate-800">Deutsch</option>
                        <option value="ar" class="bg-navy-900 text-white light:bg-white light:text-slate-800">العربية</option>
                    </select>
                    <svg class="w-3 h-3 absolute right-0 top-1/2 -translate-y-1/2 text-text-muted pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                
                
                <div id="google_translate_element"></div>
            </div>

            
            <div class="hidden sm:flex items-center gap-1.5 text-text-muted">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span id="topbar-date" class="font-medium"></span>
            </div>

            
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="flex items-center gap-1.5 text-text-secondary">
                    <svg class="w-3 h-3 text-electric" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <span id="topbar-city" class="font-semibold text-text-primary">Detecting...</span>
                </div>

                <div id="topbar-weather" class="hidden items-center gap-1 text-text-muted">
                    <span id="topbar-weather-icon" class="text-sm"></span>
                    <span id="topbar-temp" class="font-bold text-text-primary"></span>
                </div>

                
                <div id="topbar-city-select-wrap" class="hidden">
                    <label for="topbar-city-select" class="sr-only">Select your city</label>
                    <select id="topbar-city-select" onchange="manualCitySelect(this.value)" class="appearance-none bg-navy-800/60 light:bg-slate-100 border border-navy-700/30 light:border-slate-300 rounded px-2 py-0.5 text-[11px] text-text-primary cursor-pointer focus:outline-none focus:ring-1 focus:ring-electric">
                        <option value="">Select City</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Mumbai">Mumbai</option>
                        <option value="Bangalore">Bangalore</option>
                        <option value="Chennai">Chennai</option>
                        <option value="Kolkata">Kolkata</option>
                        <option value="Hyderabad">Hyderabad</option>
                        <option value="Pune">Pune</option>
                        <option value="Ahmedabad">Ahmedabad</option>
                        <option value="New York">New York</option>
                        <option value="London">London</option>
                        <option value="Dubai">Dubai</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Tokyo">Tokyo</option>
                        <option value="Sydney">Sydney</option>
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function() {
    // Set date
    var now = new Date();
    var options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
    var dateEl = document.getElementById('topbar-date');
    if (dateEl) dateEl.textContent = now.toLocaleDateString('en-US', options);

    // ── Check for stored city first (no geolocation prompt on page load) ──
    var storedCity = localStorage.getItem('atomni-city');
    if (storedCity) {
        document.getElementById('topbar-city').textContent = storedCity;
        fetchWeather(storedCity);
        var storedCountry = localStorage.getItem('atomni-country');
        if (storedCountry) {
            document.getElementById('topbar-country-code').textContent = storedCountry;
            document.getElementById('topbar-flag').textContent = countryFlag(storedCountry);
        }
    } else {
        // No stored city — show fallback selector; do NOT auto-prompt geolocation
        showCityFallback();
    }
})();

// Only called when user explicitly clicks "Detect My Location" (if we ever add that button)
// or when the city select fallback is not enough.
function requestGeolocation() {
    if (!navigator.geolocation) { showCityFallback(); return; }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            fetch('https://nominatim.openstreetmap.org/reverse?lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&format=json&accept-language=en')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var city = data.address && (data.address.city || data.address.town || data.address.village || data.address.state) || 'Unknown';
                    var country = (data.address && data.address.country_code && data.address.country_code.toUpperCase()) || 'IN';
                    document.getElementById('topbar-city').textContent = city;
                    document.getElementById('topbar-country-code').textContent = country;
                    document.getElementById('topbar-flag').textContent = countryFlag(country);
                    localStorage.setItem('atomni-city', city);
                    localStorage.setItem('atomni-country', country);
                    fetchWeather(city);
                })
                .catch(function() { showCityFallback(); });
        },
        function() { showCityFallback(); },
        { timeout: 5000 }
    );
}

function showCityFallback() {
    document.getElementById('topbar-city').textContent = 'Select City';
    document.getElementById('topbar-city-select-wrap').classList.remove('hidden');
}

function manualCitySelect(city) {
    if (!city) return;
    document.getElementById('topbar-city').textContent = city;
    document.getElementById('topbar-city-select-wrap').classList.add('hidden');
    localStorage.setItem('atomni-city', city);
    fetchWeather(city);
}

function fetchWeather(city) {
    // Use wttr.in JSON API — the ?format=%c%t endpoint returns HTML wrappers
    // in browser environments, so we use format=j1 (JSON) for clean data.
    fetch('https://wttr.in/' + encodeURIComponent(city) + '?format=j1')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var current = data.current_condition && data.current_condition[0];
            if (!current) return;
            var code    = parseInt(current.weatherCode, 10);
            var tempC   = current.temp_C;
            var icon    = weatherCodeToEmoji(code);
            document.getElementById('topbar-weather-icon').textContent = icon;
            document.getElementById('topbar-temp').textContent = tempC + '°C';
            document.getElementById('topbar-weather').classList.remove('hidden');
            document.getElementById('topbar-weather').classList.add('flex');
        })
        .catch(function() { /* fail silently */ });
}

// Map wttr.in WMO weather codes to emojis
function weatherCodeToEmoji(code) {
    if (code === 113)                          return '☀️';
    if (code === 116)                          return '⛅';
    if (code === 119 || code === 122)          return '☁️';
    if (code >= 143 && code <= 260)            return '🌫️';
    if (code >= 263 && code <= 281)            return '🌦️';
    if (code >= 293 && code <= 306)            return '🌧️';
    if (code >= 308 && code <= 314)            return '🌨️';
    if (code >= 317 && code <= 335)            return '❄️';
    if (code >= 338 && code <= 350)            return '🌨️';
    if (code >= 353 && code <= 374)            return '🌩️';
    if (code >= 377 && code <= 395)            return '⛈️';
    return '🌡️';
}

function countryFlag(cc) {
    if (!cc || cc.length !== 2) return '🏳️';
    return String.fromCodePoint(...[...cc.toUpperCase()].map(function(c) { return 0x1F1E6 - 65 + c.charCodeAt(0); }));
}

// ── Google Translate — load only on first interaction ───────────────────
var _gtLoaded = false;
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,hi,es,fr,de,ar',
        layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL,
        autoDisplay: false
    }, 'google_translate_element');
}

function loadGoogleTranslate() {
    if (_gtLoaded) return;
    _gtLoaded = true;
    var s = document.createElement('script');
    s.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    s.async = true;
    document.head.appendChild(s);
}

function changeLanguage(langCode) {
    document.cookie = 'googtrans=/en/' + langCode + '; path=/; domain=' + window.location.hostname;
    document.cookie = 'googtrans=/en/' + langCode + '; path=/; domain=.' + window.location.hostname;
    var googleSelect = document.querySelector('.goog-te-combo');
    if (googleSelect) {
        googleSelect.value = langCode;
        var event = document.createEvent('HTMLEvents');
        event.initEvent('change', false, true);
        googleSelect.dispatchEvent(event);
    } else {
        window.location.reload();
    }
}

// Restore language state + bind events on load
window.addEventListener('load', function() {
    var googCookie = document.cookie.split('; ').find(function(r) { return r.startsWith('googtrans='); });
    if (googCookie) {
        var val = googCookie.split('=')[1];
        var lang = val.split('/')[2];
        if (lang && lang !== 'en') {
            // User had a non-English selection — load translate immediately
            loadGoogleTranslate();
            var select = document.getElementById('topbar-lang');
            if (select) select.value = lang;
        }
    }

    // Load Google Translate only when user interacts with the dropdown
    var customSelect = document.getElementById('topbar-lang');
    if (customSelect) {
        customSelect.addEventListener('focus', loadGoogleTranslate, { once: true });
        customSelect.addEventListener('change', function(e) {
            loadGoogleTranslate();
            // Give translate SDK a moment to initialize before calling changeLanguage
            setTimeout(function() { changeLanguage(customSelect.value); }, 500);
        });
    }
});
</script>

<style>
    /* Hide Google Translate UI elements strongly */
    body { top: 0 !important; }
    .skiptranslate iframe, 
    .VIpgJd-ZVi9od-ORHb-OEVmcd,
    .VIpgJd-ZVi9od-aZ2wEe-wOHMyf,
    #goog-gt-tt,
    .goog-te-balloon-frame { display: none !important; }
    
    /* Hide the actual google translate widget container but keep it in DOM */
    #google_translate_element { 
        position: absolute;
        opacity: 0;
        z-index: -10;
        pointer-events: none;
    }

    /* Language selector — dark mode: legible light text */
    html:not(.light) #topbar-lang {
        color: #CBD5E1; /* slate-300 */
    }
    html:not(.light) #topbar-lang:hover,
    html:not(.light) #topbar-lang:focus {
        color: #F1F5F9; /* slate-100 */
    }
    html:not(.light) #topbar-lang option {
        background-color: #0F1535;
        color: #F1F5F9;
    }

    /* Language selector — light mode: legible dark text */
    html.light #topbar-lang {
        color: #334155; /* slate-700 */
    }
    html.light #topbar-lang:hover,
    html.light #topbar-lang:focus {
        color: #0F172A; /* slate-900 */
    }
    html.light #topbar-lang option {
        background-color: #FFFFFF;
        color: #334155;
    }
</style>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/partials/topbar.blade.php ENDPATH**/ ?>