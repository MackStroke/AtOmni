@extends('layouts.app')
@section('title', ($page?->title ?? 'Contact Us') . ' — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="text-electric text-sm font-semibold uppercase tracking-wider">Get in Touch</span>
        <h1 class="font-heading font-bold text-4xl sm:text-5xl text-text-primary mt-3 mb-5">{{ $page?->title ?? 'Contact Us' }}</h1>
        @if($page && $page->content)
            <div class="text-text-secondary text-lg max-w-2xl mx-auto prose prose-invert prose-p:text-text-secondary prose-p:text-lg mx-auto text-center leading-relaxed">
                {!! $page->content !!}
            </div>
        @else
            <p class="text-text-secondary text-lg max-w-2xl mx-auto">Have a tip, feedback, or inquiry? We'd love to hear from you. Our team typically responds within 24 hours.</p>
        @endif
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

        {{-- Contact Form (3/5) --}}
        <div class="lg:col-span-3">
            <div class="glass-card rounded-xl p-8">
                <h2 class="font-heading font-bold text-2xl text-text-primary mb-6">Send a Message</h2>
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5" id="contact-form">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="contact-name" class="text-text-secondary text-sm font-medium mb-2 block">Full Name</label>
                            <input type="text" id="contact-name" name="name" autocomplete="name" value="{{ old('name') }}" required placeholder="John Doe" class="w-full px-4 py-3 rounded-lg bg-navy-800 border {{ $errors->has('name') ? 'border-red-500' : 'border-navy-700' }} text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors" aria-describedby="name-error">
                            @error('name')<span id="name-error" class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="contact-email" class="text-text-secondary text-sm font-medium mb-2 block">Email Address</label>
                            <input type="email" id="contact-email" name="email" autocomplete="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-lg bg-navy-800 border {{ $errors->has('email') ? 'border-red-500' : 'border-navy-700' }} text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors" aria-describedby="email-error">
                            @error('email')<span id="email-error" class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="contact-subject" class="text-text-secondary text-sm font-medium mb-2 block">Subject</label>
                        <select id="contact-subject" name="subject" class="w-full px-4 py-3 rounded-lg bg-navy-800 border {{ $errors->has('subject') ? 'border-red-500' : 'border-navy-700' }} text-text-primary text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                            <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                            <option value="News Tip" {{ old('subject') == 'News Tip' ? 'selected' : '' }}>News Tip</option>
                            <option value="Advertising" {{ old('subject') == 'Advertising' ? 'selected' : '' }}>Advertising</option>
                            <option value="Partnership" {{ old('subject') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                            <option value="Technical Issue" {{ old('subject') == 'Technical Issue' ? 'selected' : '' }}>Technical Issue</option>
                            <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="contact-message" class="text-text-secondary text-sm font-medium mb-2 block">Message</label>
                        <textarea id="contact-message" name="message" required placeholder="Tell us what's on your mind..." class="w-full px-4 py-3 rounded-lg bg-navy-800 border {{ $errors->has('message') ? 'border-red-500' : 'border-navy-700' }} text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors resize-none h-36" aria-describedby="message-error">{{ old('message') }}</textarea>
                        @error('message')<span id="message-error" class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" id="submit-btn" class="group relative px-8 py-3 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20 overflow-hidden disabled:opacity-75 disabled:cursor-not-allowed">
                        <span class="inline-flex items-center gap-2 transition-opacity duration-300" id="btn-text">
                            Send Message →
                        </span>
                        <span class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300" id="btn-loader">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                    <script>
                        document.getElementById('contact-form')?.addEventListener('submit', function() {
                            const btn = document.getElementById('submit-btn');
                            const text = document.getElementById('btn-text');
                            const loader = document.getElementById('btn-loader');
                            if(btn && text && loader) {
                                btn.disabled = true;
                                text.classList.remove('opacity-100');
                                text.classList.add('opacity-0');
                                loader.classList.remove('opacity-0');
                                loader.classList.add('opacity-100');
                                this.submit();
                            }
                        });
                    </script>
                </form>
            </div>
        </div>

        {{-- Contact Info (2/5) --}}
        <div class="lg:col-span-2 space-y-6">
            @php
                $email = \App\Models\Setting::get('contact_email', 'hello@atomni.com');
                $phone = \App\Models\Setting::get('contact_phone', '+1 (555) 123-4567');
                $address = \App\Models\Setting::get('contact_address', "123 Innovation Drive\nSan Francisco, CA 94105");
                
                $contacts = [
                    ['icon' => '📧', 'title' => 'Email', 'lines' => [$email]],
                    ['icon' => '📍', 'title' => 'Office', 'lines' => explode("\n", $address)],
                    ['icon' => '📞', 'title' => 'Phone', 'lines' => [$phone]],
                ];
            @endphp
            @foreach($contacts as $contact)
                <div class="glass-card rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <span class="text-2xl">{{ $contact['icon'] }}</span>
                        <div>
                            <h3 class="font-heading font-semibold text-text-primary mb-2">{{ $contact['title'] }}</h3>
                            @foreach($contact['lines'] as $line)
                                @if(trim($line))
                                    <p class="text-text-secondary text-sm">{{ $line }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Map/Location --}}
            @php $mapEmbed = \App\Models\Setting::get('contact_map_embed', ''); @endphp
            @if(trim($mapEmbed))
                <div class="rounded-xl border border-navy-700/50 overflow-hidden h-64 md:h-80 w-full relative group">
                    <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0 filter grayscale hover:grayscale-0 transition-all duration-500">
                        {!! $mapEmbed !!}
                    </div>
                </div>
            @else
                {{-- Map Placeholder --}}
                <div class="rounded-xl border-2 border-dashed border-navy-700 h-48 flex items-center justify-center">
                    <span class="text-text-muted text-sm">🗺️ Map placeholder — Google Maps embed</span>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
