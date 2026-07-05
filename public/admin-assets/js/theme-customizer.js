// Dynamically load font preview
function updateFontPreview(fontName) {
    const encodedName = fontName.replace(/ /g, '+');
    const linkId = 'preview-font-link';
    let link = document.getElementById(linkId);
    if (!link) {
        link = document.createElement('link');
        link.id = linkId;
        link.rel = 'stylesheet';
        document.head.appendChild(link);
    }
    link.href = `https://fonts.googleapis.com/css2?family=${encodedName}:wght@400;500;700&display=swap`;
    
    document.getElementById('font_preview_heading').style.fontFamily = `"${fontName}", sans-serif`;
    document.getElementById('font_preview_body').style.fontFamily = `"${fontName}", sans-serif`;
}

// Initialize preview and scrollspy on page load
document.addEventListener('DOMContentLoaded', function() {
    const fontSelect = document.getElementById('font_family');
    if(fontSelect) {
        updateFontPreview(fontSelect.value);
    }

    // Live Theme Switcher for presets
    document.querySelectorAll('input[name="theme_color"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const typeVal = document.querySelector('input[name="theme_type"]:checked')?.value;
            if (this.checked && typeVal === 'preset') {
                applyThemeClass(this.value);
            }
        });
    });

    // Watch theme type changes (preset vs manual)
    document.querySelectorAll('input[name="theme_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'preset') {
                const selectedPreset = document.querySelector('input[name="theme_color"]:checked');
                if (selectedPreset) {
                    applyThemeClass(selectedPreset.value);
                }
                removeManualStyles();
            } else if (this.value === 'manual') {
                removeThemeClasses();
                updateManualColors();
            }
        });
    });

    function applyThemeClass(themeName) {
        removeThemeClasses();
        document.documentElement.classList.add('theme-' + themeName);
    }

    function removeThemeClasses() {
        document.documentElement.className.split(' ').forEach(className => {
            if (className.startsWith('theme-')) {
                document.documentElement.classList.remove(className);
            }
        });
    }

    function removeManualStyles() {
        document.documentElement.style.removeProperty('--color-electric');
        document.documentElement.style.removeProperty('--color-accent-blue');
        document.documentElement.style.removeProperty('--color-accent-blue-hover');
        document.documentElement.style.removeProperty('--color-cyan-glow');
        document.documentElement.style.removeProperty('--color-brand-primary');
        document.documentElement.style.removeProperty('--color-brand-secondary');
        document.documentElement.style.removeProperty('--color-electric-light');
        document.documentElement.style.removeProperty('--color-electric-dark');
    }

    // ─── Manual Custom Colors Enhanced Switcher & Studio ───
    const primaryInput = document.getElementById('theme_manual_primary');
    const primaryInputText = document.getElementById('theme_manual_primary_text');
    const secondaryInput = document.getElementById('theme_manual_secondary');
    const secondaryInputText = document.getElementById('theme_manual_secondary_text');

    // Color converters
    function hexToRgb(hex) {
        const clean = hex.replace('#', '');
        const r = parseInt(clean.slice(0, 2), 16);
        const g = parseInt(clean.slice(2, 4), 16);
        const b = parseInt(clean.slice(4, 6), 16);
        return { r, g, b };
    }

    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return { h, s, l };
    }

    function hslToHex(h, s, l) {
        let r, g, b;
        if (s === 0) {
            r = g = b = l;
        } else {
            const hue2rgb = (p, q, t) => {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1/6) return p + (q - p) * 6 * t;
                if (t < 1/2) return q;
                if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            };
            const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            const p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }
        const toHex = x => {
            const hex = Math.round(x * 255).toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        };
        return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
    }

    // Relative luminance & contrast
    function getLuminance(r, g, b) {
        const a = [r, g, b].map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function getContrast(hex1, hex2) {
        const rgb1 = hexToRgb(hex1);
        const rgb2 = hexToRgb(hex2);
        const l1 = getLuminance(rgb1.r, rgb1.g, rgb1.b);
        const l2 = getLuminance(rgb2.r, rgb2.g, rgb2.b);
        return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
    }

    // Helper to update validation badges
    function updateContrastBadge(elementId, ratio) {
        const badge = document.getElementById(elementId);
        if (!badge) return;

        let text = 'FAIL';
        let classes = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';

        if (ratio >= 7.0) {
            text = 'PASS AAA';
            classes = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
        } else if (ratio >= 4.5) {
            text = 'PASS AA';
            classes = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
        } else if (ratio >= 3.0) {
            text = 'PASS (LARGE)';
            classes = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
        }

        badge.innerText = text;
        badge.className = `px-2 py-0.5 rounded text-[9px] font-bold uppercase shrink-0 ${classes}`;
    }

    // Apply pre-curated manual preset
    window.applyManualPreset = function(prim, sec) {
        if (primaryInput && primaryInputText) {
            primaryInput.value = prim;
            primaryInputText.value = prim.toUpperCase();
        }
        if (secondaryInput && secondaryInputText) {
            secondaryInput.value = sec;
            secondaryInputText.value = sec.toUpperCase();
        }
        updateManualColors();
    };

    // Suggest a matching secondary shade
    window.suggestSecondary = function(mode) {
        if (!primaryInput) return;
        const primHex = primaryInput.value;
        const rgb = hexToRgb(primHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        let suggested = '#1A5FD1';

        if (mode === 'dark') {
            hsl.l = Math.max(0.08, hsl.l - 0.20);
            suggested = hslToHex(hsl.h, hsl.s, hsl.l);
        } else if (mode === 'glow') {
            hsl.l = Math.min(0.80, hsl.l + 0.15);
            hsl.s = Math.min(1.0, hsl.s + 0.20);
            suggested = hslToHex(hsl.h, hsl.s, hsl.l);
        } else if (mode === 'complementary') {
            hsl.h = (hsl.h + 0.5) % 1.0;
            suggested = hslToHex(hsl.h, hsl.s, hsl.l);
        }

        if (secondaryInput && secondaryInputText) {
            secondaryInput.value = suggested;
            secondaryInputText.value = suggested.toUpperCase();
            updateManualColors();
        }
    };

    // Generate random combo
    window.randomizePair = function() {
        const h = Math.random();
        const s = 0.85 + Math.random() * 0.15;
        const l = 0.42 + Math.random() * 0.15;
        const prim = hslToHex(h, s, l);

        let h2 = (h + 0.1 + Math.random() * 0.3) % 1.0;
        let s2 = Math.min(1.0, s + 0.1);
        let l2 = Math.max(0.1, l - 0.22);
        const sec = hslToHex(h2, s2, l2);

        applyManualPreset(prim, sec);
    };

    function updateManualColors() {
        const checkedType = document.querySelector('input[name="theme_type"]:checked');
        if (checkedType && checkedType.value === 'manual' && primaryInput && secondaryInput) {
            const primVal = primaryInput.value;
            const secVal = secondaryInput.value;
            
            // 1. Update site variables
            document.documentElement.style.setProperty('--color-electric', primVal, 'important');
            document.documentElement.style.setProperty('--color-accent-blue', primVal, 'important');
            document.documentElement.style.setProperty('--color-accent-blue-hover', secVal, 'important');
            document.documentElement.style.setProperty('--color-cyan-glow', secVal, 'important');
            document.documentElement.style.setProperty('--color-brand-primary', primVal, 'important');
            document.documentElement.style.setProperty('--color-brand-secondary', secVal, 'important');
            document.documentElement.style.setProperty('--color-electric-dark', secVal, 'important');
            document.documentElement.style.setProperty('--color-electric-light', `color-mix(in srgb, ${primVal} 70%, white)`, 'important');

            // 2. Update Mockup Header Gradient
            const mockHeaderGradient = document.getElementById('mock-header-gradient');
            if (mockHeaderGradient) {
                mockHeaderGradient.style.background = `linear-gradient(135deg, ${secVal} 0%, #0A0E27 100%)`;
            }

            // 3. Update Mockup Badge
            const mockBadge = document.getElementById('mock-badge');
            if (mockBadge) {
                mockBadge.style.backgroundColor = `${primVal}20`; // 12% opacity
                mockBadge.style.borderColor = `${primVal}50`; // 30% opacity
                mockBadge.style.borderWidth = '1px';
                mockBadge.style.color = primVal;
            }

            // 4. Update Mockup Button
            const mockBtn = document.getElementById('mock-btn');
            if (mockBtn) {
                mockBtn.style.backgroundColor = primVal;
                mockBtn.style.boxShadow = `0 6px 20px color-mix(in srgb, ${primVal} 35%, transparent)`;
                
                // Hover behavior on mockup button
                mockBtn.onmouseenter = () => {
                    mockBtn.style.backgroundColor = secVal;
                    mockBtn.style.boxShadow = `0 6px 20px color-mix(in srgb, ${secVal} 45%, transparent)`;
                };
                mockBtn.onmouseleave = () => {
                    mockBtn.style.backgroundColor = primVal;
                    mockBtn.style.boxShadow = `0 6px 20px color-mix(in srgb, ${primVal} 35%, transparent)`;
                };
            }

            // 5. Update Mockup Input
            const mockInput = document.getElementById('mock-input');
            if (mockInput) {
                mockInput.style.borderColor = `${primVal}80`; // 50% opacity
                mockInput.style.boxShadow = `0 0 0 1px ${primVal}40`;
            }

            // 6. Recalculate and update WCAG contrast ratios
            const contrastBtn = getContrast(primVal, '#ffffff');
            const contrastDark = getContrast(primVal, '#0A0E27');

            const btnRatioText = document.getElementById('contrast-btn-ratio');
            if (btnRatioText) btnRatioText.innerText = `${contrastBtn.toFixed(1)}:1`;
            updateContrastBadge('contrast-btn-status', contrastBtn);

            const darkRatioText = document.getElementById('contrast-dark-ratio');
            if (darkRatioText) darkRatioText.innerText = `${contrastDark.toFixed(1)}:1`;
            updateContrastBadge('contrast-dark-status', contrastDark);
        }
    }

    // Live binding for inputs
    if (primaryInput && primaryInputText) {
        primaryInput.addEventListener('input', function() {
            primaryInputText.value = this.value.toUpperCase();
            updateManualColors();
        });
        primaryInputText.addEventListener('input', function() {
            if (/^#[a-fA-F0-9]{6}$/i.test(this.value)) {
                primaryInput.value = this.value;
                updateManualColors();
            }
        });
    }

    if (secondaryInput && secondaryInputText) {
        secondaryInput.addEventListener('input', function() {
            secondaryInputText.value = this.value.toUpperCase();
            updateManualColors();
        });
        secondaryInputText.addEventListener('input', function() {
            if (/^#[a-fA-F0-9]{6}$/i.test(this.value)) {
                secondaryInput.value = this.value;
                updateManualColors();
            }
        });
    }

    // Initial setup for live manual color if active
    const initialType = document.querySelector('input[name="theme_type"]:checked');
    if (initialType && initialType.value === 'manual') {
        updateManualColors();
    }

    // Scrollspy for sidebar
    const sections = document.querySelectorAll('form[id^="section-"]');
    const navLinks = document.querySelectorAll('[id^="nav-"]');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Remove active from all
                navLinks.forEach(link => {
                    link.classList.remove('bg-accent-blue/10', 'text-accent-blue', 'font-bold');
                    link.classList.add('text-text-muted', 'hover:bg-navy-800', 'hover:text-text-primary', 'font-medium');
                    const iconDiv = link.querySelector('.w-8');
                    if(iconDiv) {
                        iconDiv.classList.remove('bg-accent-blue/20', 'text-accent-blue');
                        iconDiv.classList.add('bg-navy-800', 'text-text-muted');
                    }
                });

                // Add active to current
                const activeId = entry.target.id.replace('section-', 'nav-');
                const activeLink = document.getElementById(activeId);
                if (activeLink) {
                    activeLink.classList.add('bg-accent-blue/10', 'text-accent-blue', 'font-bold');
                    activeLink.classList.remove('text-text-muted', 'hover:bg-navy-800', 'hover:text-text-primary', 'font-medium');
                    const iconDiv = activeLink.querySelector('.w-8');
                    if(iconDiv) {
                        iconDiv.classList.add('bg-accent-blue/20', 'text-accent-blue');
                        iconDiv.classList.remove('bg-navy-800', 'text-text-muted');
                    }
                }
            }
        });
    }, { rootMargin: '-100px 0px -60% 0px' });

    sections.forEach(section => observer.observe(section));
});
