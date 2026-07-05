@extends('layouts.app')

@section('title', 'Support Our Journalism — Contribute to At Omni')
@section('meta-description', 'Support At Omni\'s mission of fearless, independent journalism. Contribute via UPI and help us keep truthful reporting alive.')

@section('content')

@php
    $qrLink = \App\Models\Setting::get('donate_qr_link', '');
    $donateNote = \App\Models\Setting::get('donate_note', '');
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden py-20 lg:py-28 light:bg-slate-50">
    <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 light:from-slate-50 light:via-white light:to-slate-100"></div>
    <div class="absolute inset-0 theme-radial-glow-tr"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-rose-500/10 border border-rose-500/20 rounded-full px-4 py-1.5 mb-6">
            <svg class="w-4 h-4 text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
            <span class="text-xs font-bold text-rose-400 uppercase tracking-widest">Support Independent Journalism</span>
        </div>

        <h1 class="font-heading font-black text-4xl sm:text-5xl lg:text-6xl text-white light:text-slate-900 leading-tight mb-6 tracking-tight">
            Help Us Keep<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-electric via-cyan-glow to-electric light:from-electric light:via-blue-600 light:to-electric">Truth Alive.</span>
        </h1>

        <p class="text-lg sm:text-xl text-text-secondary light:text-slate-600 leading-relaxed max-w-2xl mx-auto mb-8">
            At Omni runs on reader contributions — not ads, not corporate money. If our journalism matters to you, consider supporting us with a contribution of any amount.
        </p>

        <div class="grid grid-cols-3 gap-6 max-w-md mx-auto">
            <div class="text-center">
                <div class="text-2xl font-black text-white light:text-slate-900">100%</div>
                <div class="text-[10px] text-text-muted light:text-slate-500 uppercase tracking-wider font-semibold">Independent</div>
            </div>
            <div class="text-center border-x border-navy-700/30 light:border-slate-300">
                <div class="text-2xl font-black text-white light:text-slate-900">0</div>
                <div class="text-[10px] text-text-muted light:text-slate-500 uppercase tracking-wider font-semibold">Corporate Owners</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-black text-white light:text-slate-900">Free</div>
                <div class="text-[10px] text-text-muted light:text-slate-500 uppercase tracking-wider font-semibold">No Paywalls</div>
            </div>
        </div>
    </div>
</section>


{{-- How to Contribute --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="font-heading font-bold text-3xl text-white light:text-slate-900 mb-3">How to Contribute</h2>
        <p class="text-text-secondary light:text-slate-600 text-base max-w-xl mx-auto">It's simple — scan, pay, and let us know. Every contribution, big or small, directly powers our newsroom.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

        {{-- QR Code Section --}}
        <div class="glass-card light:bg-white rounded-2xl p-8 border border-navy-700/20 light:border-slate-200 text-center light:shadow-sm">
            <div class="mb-6">
                <div class="inline-flex items-center gap-2 bg-electric/10 light:bg-electric/5 border border-electric/20 light:border-electric/20 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-xs font-bold text-electric uppercase tracking-widest">Step 1 — Scan &amp; Pay</span>
                </div>
                <h3 class="font-heading font-bold text-xl text-white light:text-slate-900 mb-2">Scan the QR Code</h3>
                <p class="text-sm text-text-muted light:text-slate-500">Open any UPI app (Google Pay, PhonePe, Paytm) and scan below</p>
            </div>

            @if($qrLink)
                <div class="bg-white rounded-2xl p-6 inline-block mx-auto mb-6 shadow-xl shadow-black/20" id="donate-qrcode-container"></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        new QRCode(document.getElementById("donate-qrcode-container"), {
                            text: @json($qrLink),
                            width: 208,
                            height: 208,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    });
                </script>
                <p class="text-sm text-text-muted light:text-slate-500 mb-2">Or pay directly via UPI:</p>
                <div class="inline-flex items-center gap-2 bg-navy-800 light:bg-slate-50 rounded-xl px-4 py-2.5 border border-navy-700/30 light:border-slate-200">
                    <span class="text-sm text-electric font-mono font-semibold">{{ $qrLink }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $qrLink }}'); this.innerHTML='✓ Copied'; setTimeout(() => this.innerHTML='Copy', 2000)"
                            class="text-xs text-text-muted light:text-slate-500 hover:text-white light:hover:text-slate-900 bg-navy-700 light:bg-slate-200 rounded-lg px-3 py-1 transition-colors font-semibold">
                        Copy
                    </button>
                </div>
            @else
                <div class="bg-navy-800/50 light:bg-slate-50 rounded-2xl p-12 mb-6 border-2 border-dashed border-navy-700/40 light:border-slate-300">
                    <svg class="w-16 h-16 mx-auto text-text-muted light:text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <p class="text-text-muted light:text-slate-500 text-sm">QR Code coming soon. Please check back or contact us directly.</p>
                </div>
            @endif
        </div>

        {{-- Step 2 & Shoutout Info --}}
        <div class="space-y-6">
            <div class="glass-card light:bg-white rounded-2xl p-8 border border-navy-700/20 light:border-slate-200 light:shadow-sm">
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-4"
                     style="background: color-mix(in srgb, var(--color-electric) 10%, transparent); border: 1px solid color-mix(in srgb, var(--color-electric) 20%, transparent);">
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: var(--color-electric);">Step 2 — Tell Us</span>
                </div>
                <h3 class="font-heading font-bold text-xl text-white light:text-slate-900 mb-2">Share Your Contribution</h3>
                <p class="text-sm text-text-muted light:text-slate-500 mb-6">After paying, share a screenshot of your payment with us! You can do this in two ways:</p>

                <div class="space-y-3">
                    <a href="https://www.instagram.com/at_omni/" target="_blank" rel="noopener"
                       class="flex items-center gap-4 p-4 rounded-xl bg-navy-800/60 light:bg-slate-50 border border-navy-700/20 light:border-slate-200 hover:border-pink-500/30 light:hover:border-pink-400/40 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="text-sm font-semibold text-white light:text-slate-800 group-hover:text-pink-400 transition-colors">Post on Instagram / Facebook</div>
                            <div class="text-xs text-text-muted light:text-slate-500">Tag <span class="text-electric">@at_omni</span> with your payment screenshot</div>
                        </div>
                        <svg class="w-4 h-4 text-text-muted light:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>

                    <a href="mailto:info@atomni.in"
                       class="flex items-center gap-4 p-4 rounded-xl bg-navy-800/60 light:bg-slate-50 border border-navy-700/20 light:border-slate-200 hover:border-electric/30 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-electric/20 light:bg-electric/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="text-sm font-semibold text-white light:text-slate-800 group-hover:text-electric transition-colors">Email Us</div>
                            <div class="text-xs text-text-muted light:text-slate-500">Send screenshot to <span class="text-electric">info@atomni.in</span></div>
                        </div>
                        <svg class="w-4 h-4 text-text-muted light:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>

            <div class="glass-card light:bg-white rounded-2xl p-8 border border-navy-700/20 light:border-slate-200 light:shadow-sm">
                <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/20 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">🎁 Get Featured</span>
                </div>
                <h3 class="font-heading font-bold text-xl text-white light:text-slate-900 mb-2">Contribute ₹1,000+ and Get a Shoutout!</h3>
                <p class="text-sm text-text-muted light:text-slate-500 mb-4">If you contribute <span class="text-amber-400 font-semibold">₹1,000 or more</span>, we'll:</p>
                <ul class="space-y-2.5">
                    @foreach([
                        'Give you a <strong>shoutout on our Instagram</strong> (<span class="text-electric">@at_omni</span>)',
                        'Feature your name on our website as a proud supporter of independent journalism',
                        'Send you a <strong>personalized thank you</strong> from the editorial team'
                    ] as $perk)
                    <li class="flex items-start gap-3 text-sm text-text-secondary light:text-slate-600">
                        <svg class="w-4 h-4 text-amber-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{!! $perk !!}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>


{{-- Donor Shoutout Wall --}}
@if($donors->isNotEmpty())
<section class="py-24 lg:py-32 bg-navy-900 light:bg-[#f8fafc] border-b border-navy-700/30 light:border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-5" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.22);">
                <span style="font-size:11px; font-weight:700; color:#fbbf24; text-transform:uppercase; letter-spacing:0.1em;">🌟 Donor Shoutout</span>
            </div>
            <h2 class="font-heading font-black text-4xl lg:text-5xl text-white light:text-slate-900 mb-4 tracking-tight">Our Supporters</h2>
            <p style="color:#94a3b8; font-size:1.1rem; max-width:480px; margin:0 auto;" class="light:text-slate-500">
                These incredible people keep independent journalism alive. Thank you. ❤️
            </p>
        </div>

        {{-- Cards — all side by side, centered --}}
        <div class="dw-grid">
            @foreach($donors as $donor)
                <div class="dw-card">
                    <div class="dw-card__photo">
                        @if($donor->image_path)
                            <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($donor->image_path) }}" alt="{{ $donor->name }}" class="dw-card__img">
                        @else
                            <div class="dw-card__noimg">
                                <svg viewBox="0 0 64 64" fill="none" class="w-10 h-10" style="opacity:0.35; color:#94a3b8;">
                                    <circle cx="32" cy="22" r="13" stroke="currentColor" stroke-width="2.5"/>
                                    <path d="M6 58c0-14.359 11.640-26 26-26s26 11.641 26 26" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        @endif
                        @if($donor->amount)
                            <div class="dw-badge">₹{{ rtrim(rtrim(number_format($donor->amount, 2), '0'), '.') }}</div>
                        @endif
                    </div>
                    <div class="dw-card__body">
                        @if($donor->message)
                            <p class="dw-card__msg">{{ $donor->message }}</p>
                        @endif
                        <div>
                            @if($donor->social_link)
                                <a href="{{ $donor->social_link }}" target="_blank" rel="noopener" class="dw-card__name dw-card__name--link">{{ $donor->name }}</a>
                            @else
                                <span class="dw-card__name">{{ $donor->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <style>
        .dw-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }
        .dw-card {
            width: 160px;
            flex-shrink: 0;
            border-radius: 16px;
            overflow: hidden;
            background: #252930;
            border: 1px solid rgba(255,255,255,0.07);
            box-shadow: 0 6px 24px rgba(0,0,0,0.4);
            transition: transform 0.22s cubic-bezier(.22,.68,0,1.2), box-shadow 0.22s ease;
        }
        html.light .dw-card, body.light .dw-card, [data-theme="light"] .dw-card, .light .dw-card {
            background: #ffffff; border-color: rgba(0,0,0,0.09); box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .dw-card:hover { transform: translateY(-7px) scale(1.025); box-shadow: 0 20px 45px rgba(0,0,0,0.55); position: relative; z-index: 10; }
        .dw-card__photo { position: relative; width: 100%; aspect-ratio: 1/1; overflow: hidden; background: #1b1f27; display: flex; align-items: center; justify-content: center; }
        html.light .dw-card__photo, body.light .dw-card__photo, [data-theme="light"] .dw-card__photo, .light .dw-card__photo { background: #f1f5f9; }
        .dw-card__img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease; }
        .dw-card:hover .dw-card__img { transform: scale(1.04); }
        .dw-card__noimg { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
        .dw-badge { position: absolute; top: 7px; right: 7px; background: rgba(10,14,22,0.85); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 2px 7px; font-size: 10px; font-weight: 700; color: #f1f5f9; letter-spacing: 0.03em; z-index: 5; line-height: 1.6; }
        html.light .dw-badge, body.light .dw-badge, [data-theme="light"] .dw-badge, .light .dw-badge { background: rgba(255,255,255,0.90); border-color: rgba(0,0,0,0.1); color: #1e293b; }
        .dw-card__body { padding: 10px 11px 11px; }
        .dw-card__msg { font-size: 11.5px; line-height: 1.55; color: #94a3b8; margin-bottom: 7px; word-break: break-word; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
        html.light .dw-card__msg, body.light .dw-card__msg, [data-theme="light"] .dw-card__msg, .light .dw-card__msg { color: #4b5563; }
        .dw-card__name { font-size: 11.5px; font-weight: 600; color: #64748b; display: inline; }
        .dw-card__name--link { color: #60a5fa; text-decoration: none; transition: color 0.15s; }
        .dw-card__name--link:hover { color: #93c5fd; text-decoration: underline; }
        html.light .dw-card__name--link { color: #2563eb; }
        html.light .dw-card__name--link:hover { color: #1d4ed8; }
        @media (max-width: 640px) { .dw-card { width: 140px; } .dw-grid { gap: 10px; } }
    </style>
</section>
@endif


{{-- Where Your Money Goes --}}
<section class="bg-navy-900/50 light:bg-slate-50 border-y border-navy-700/20 light:border-slate-200 py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-heading font-bold text-3xl text-white light:text-slate-900 mb-3">Where Your Money Goes</h2>
            <p class="text-text-secondary light:text-slate-600 text-base max-w-2xl mx-auto">Every rupee is invested back into journalism that matters.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="glass-card light:bg-white rounded-2xl p-6 text-center border border-navy-700/20 light:border-slate-200 light:shadow-sm">
                <div class="w-14 h-14 rounded-2xl bg-electric/10 light:bg-electric/5 text-electric flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h3 class="font-bold text-white light:text-slate-900 text-lg mb-2">Investigative Reporting</h3>
                <p class="text-sm text-text-muted light:text-slate-500 leading-relaxed">Funding deep-dive investigations that hold power accountable.</p>
            </div>

            <div class="glass-card light:bg-white rounded-2xl p-6 text-center border border-navy-700/20 light:border-slate-200 light:shadow-sm">
                <div class="w-14 h-14 rounded-2xl bg-amber/10 light:bg-amber-100 text-amber light:text-amber-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="font-bold text-white light:text-slate-900 text-lg mb-2">Our Reporters</h3>
                <p class="text-sm text-text-muted light:text-slate-500 leading-relaxed">Fair wages for journalists delivering verified, unbiased news.</p>
            </div>

            <div class="glass-card light:bg-white rounded-2xl p-6 text-center border border-navy-700/20 light:border-slate-200 light:shadow-sm">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 light:bg-emerald-100 text-emerald-400 light:text-emerald-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-bold text-white light:text-slate-900 text-lg mb-2">Technology &amp; Reach</h3>
                <p class="text-sm text-text-muted light:text-slate-500 leading-relaxed">Keeping our platform fast, ad-light, and accessible to all.</p>
            </div>
        </div>
    </div>
</section>


{{-- Follow Us CTA --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <h2 class="font-heading font-bold text-2xl text-white light:text-slate-900 mb-4">Stay Connected</h2>
    <p class="text-text-secondary light:text-slate-600 text-sm mb-8 max-w-lg mx-auto">Follow us on Instagram for the latest updates, behind-the-scenes, shoutouts to our contributors, and more.</p>

    <a href="https://www.instagram.com/at_omni/" target="_blank" rel="noopener"
       class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-purple-600 via-pink-500 to-orange-400 hover:from-purple-500 hover:via-pink-400 hover:to-orange-300 text-white font-bold text-base transition-all shadow-2xl shadow-pink-500/30 hover:shadow-pink-500/50 hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        Follow @at_omni on Instagram
    </a>
</section>

@endsection
