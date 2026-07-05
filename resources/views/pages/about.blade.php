@extends('layouts.app')
@section('title', 'About Us — Atomni')
@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden py-20 lg:py-28 border-b border-navy-700/30 light:border-gray-200 light:bg-slate-50">
    <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 light:from-slate-50 light:via-white light:to-slate-100"></div>
    <div class="absolute inset-0 theme-radial-glow-tr"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-electric/10 border border-electric/30 rounded-full px-4 py-1.5 mb-6">
            <span class="w-2 h-2 rounded-full bg-electric animate-pulse"></span>
            <span class="text-xs font-bold text-electric uppercase tracking-widest">About Atomni</span>
        </div>
        
        <h1 class="font-heading font-black text-4xl sm:text-5xl lg:text-6xl text-white light:text-slate-900 leading-tight mb-6 tracking-tight">
            The Pursuit of Truth<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-electric via-cyan-glow to-electric light:from-electric light:via-blue-600 light:to-electric">In the Digital Age</span>
        </h1>
        
        <p class="text-lg sm:text-xl text-text-secondary light:text-slate-600 leading-relaxed max-w-2xl mx-auto mb-8">
            Founded in 2024, Atomni is an independent digital newsroom committed to delivering accurate, unbiased, and in-depth journalism to readers around the world.
        </p>
    </div>
</section>

{{-- Mission & Values --}}
<section class="relative py-20 lg:py-28 bg-navy-800/30 light:bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-heading font-bold text-3xl md:text-4xl text-text-primary light:text-gray-900">Our Core Principles</h2>
            <div class="w-20 h-1 bg-electric mx-auto mt-6 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $values = [
                    ['icon' => '🎯', 'title' => 'Accuracy First', 'desc' => 'Every story goes through rigorous fact-checking and editorial review before publication. We correct errors promptly and transparently.'],
                    ['icon' => '🌍', 'title' => 'Global Perspective', 'desc' => 'Our network of correspondents spans six continents, ensuring that our coverage reflects the full complexity of world events.'],
                    ['icon' => '🤝', 'title' => 'Reader Trust', 'desc' => 'We are funded by readers, not special interests. Our editorial independence is non-negotiable and protected by our founding charter.'],
                ];
            @endphp
            @foreach($values as $value)
                <div class="glass-card hover:bg-navy-800/80 light:hover:bg-white/80 transition-all duration-300 rounded-2xl p-8 sm:p-10 text-center border border-navy-700/50 light:border-gray-200 group hover:-translate-y-2 hover:shadow-2xl hover:shadow-electric/10">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-electric/20 to-navy-800 light:to-gray-100 flex items-center justify-center text-4xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                        {{ $value['icon'] }}
                    </div>
                    <h3 class="font-heading font-bold text-2xl text-text-primary light:text-gray-900 mb-4">{{ $value['title'] }}</h3>
                    <p class="text-text-secondary light:text-gray-600 text-base leading-relaxed">{{ $value['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Visual Break --}}
<section class="py-10 lg:py-16 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl overflow-hidden relative h-[400px] shadow-2xl">
            <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Team at work" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-900 via-navy-900/40 to-transparent"></div>
            <div class="absolute bottom-10 left-10 md:left-16 right-10">
                <blockquote class="font-heading text-2xl md:text-4xl font-bold text-white max-w-3xl leading-snug">
                    "Journalism is printing what someone else does not want printed: everything else is public relations."
                </blockquote>
            </div>
        </div>
    </div>
</section>

{{-- Team --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
    <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
        <div>
            <h2 class="font-heading font-bold text-4xl text-text-primary light:text-gray-900">Meet Our Team</h2>
            <p class="text-text-secondary light:text-gray-600 mt-3 text-lg">Award-winning journalists, data scientists, and storytellers.</p>
        </div>
        <div class="w-24 h-1 bg-electric rounded-full hidden md:block"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @if(isset($teamMembers) && $teamMembers->count() > 0)
            @foreach($teamMembers as $member)
                <div class="glass-card rounded-2xl p-6 text-center group border border-navy-700/50 light:border-gray-200 hover:border-electric/50 transition-colors duration-300">
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <div class="absolute inset-0 rounded-full bg-electric/20 animate-pulse group-hover:bg-electric/40 transition-colors duration-300 -m-2"></div>
                        <div class="relative w-full h-full rounded-full bg-navy-800 light:bg-gray-100 overflow-hidden border-2 border-navy-600 light:border-gray-300 z-10">
                            @if($member->photo_path)
                                <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($member->photo_path) }}" alt="{{ $member->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-electric to-cyan-glow text-white font-bold text-4xl group-hover:scale-110 transition-transform duration-500">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        @if($member->is_founding_member)
                            <div class="absolute -bottom-2 -right-2 bg-gradient-to-r from-rose-500 to-orange-500 text-white text-[10px] font-bold uppercase tracking-widest py-1 px-3 rounded-full border-2 border-navy-800 light:border-white z-20 shadow-lg" title="Founding Member">
                                2025 Legend
                            </div>
                        @endif
                    </div>
                    <h3 class="font-heading font-bold text-xl text-text-primary light:text-gray-900">{{ $member->name }}</h3>
                    <p class="text-electric font-medium text-sm mt-1 mb-4">{{ $member->role }}</p>
                    @if($member->bio)
                        <p class="text-sm text-text-muted light:text-gray-500 leading-relaxed">{{ \Illuminate\Support\Str::limit($member->bio, 100) }}</p>
                    @endif
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-16 glass-card rounded-2xl border border-dashed border-navy-600 light:border-gray-300">
                <p class="text-text-muted light:text-gray-500 text-lg">Our dedicated team details are coming soon.</p>
            </div>
        @endif
    </div>
</section>

{{-- Timeline --}}
<section class="py-12 lg:py-16 bg-electric text-white border-y border-navy-700/30 light:border-transparent overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 lg:mb-14">
            <h2 class="font-heading font-black text-4xl md:text-5xl text-white mb-4">Our Journey</h2>
            <p class="text-white/80 text-lg sm:text-lg max-w-2xl mx-auto">From a single conversation to a multi-platform media organization.</p>
        </div>
        
        <div class="relative w-full max-w-full overflow-hidden" id="journey-slider" style="-webkit-tap-highlight-color: transparent;">
            <!-- Navigation -->
            <div class="flex justify-center md:justify-end gap-3 mb-6">
                <button id="btn-prev" class="w-12 h-12 rounded-full flex items-center justify-center bg-white/10 border border-white/20 text-white hover:bg-white hover:text-electric hover:border-white transition-all focus:outline-none shadow-lg" aria-label="Previous Slide">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button id="btn-next" class="w-12 h-12 rounded-full flex items-center justify-center bg-white/10 border border-white/20 text-white hover:bg-white hover:text-electric hover:border-white transition-all focus:outline-none shadow-lg" aria-label="Next Slide">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Slider Track -->
            <div class="flex transition-transform duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] will-change-transform cursor-grab active:cursor-grabbing pb-8 pt-4" id="journey-track">
                
                {{-- Slide 1 --}}
                <div class="w-full flex-shrink-0 px-2 sm:px-4 md:px-6 transition-all duration-700 transform scale-95 opacity-50 journey-slide">
                    <div class="glass-card bg-white/10 backdrop-blur-2xl border border-white/20 shadow-2xl rounded-3xl p-6 lg:p-10 h-full flex flex-col justify-center">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                            <div class="order-2 lg:order-1">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs sm:text-sm font-bold tracking-wider mb-4 sm:mb-6">
                                    August 20, 2025
                                </div>
                                <h3 class="font-heading font-bold text-2xl md:text-3xl lg:text-4xl text-white mb-4 sm:mb-6">Three Minds, One Mission.</h3>
                                <p class="text-white/90 text-base lg:text-lg leading-relaxed">
                                    In a quiet corner of our city, our journey began. Armed with nothing but a single notebook, an ambitious dream, and a coffee-fueled fire, our founding trio launched Atomni. We didn't have a grand office or a massive budget. We just had a belief that news could be better: faster, fairer, and focused on you.
                                </p>
                            </div>
                            <div class="order-1 lg:order-2 relative group">
                                <div class="absolute inset-0 bg-white/20 blur-2xl -m-6 rounded-3xl z-0 transition-opacity opacity-70"></div>
                                <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border border-white/20">
                                    <img loading="lazy" src="{{ asset('assets/images/journey_spark.png') }}" alt="The Spark" class="w-full h-auto object-cover aspect-video lg:aspect-[4/3] group-hover:scale-105 transition-transform duration-700 pointer-events-none select-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2 --}}
                <div class="w-full flex-shrink-0 px-2 sm:px-4 md:px-6 transition-all duration-700 transform scale-95 opacity-50 journey-slide">
                    <div class="glass-card bg-white/10 backdrop-blur-2xl border border-white/20 shadow-2xl rounded-3xl p-6 lg:p-10 h-full flex flex-col justify-center">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                            <div class="relative group">
                                <div class="absolute inset-0 bg-white/20 blur-2xl -m-6 rounded-3xl z-0 transition-opacity opacity-70"></div>
                                <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border border-white/20">
                                    <img loading="lazy" src="{{ asset('assets/images/journey_surge.png') }}" alt="The Social Surge" class="w-full h-auto object-cover aspect-video lg:aspect-[4/3] group-hover:scale-105 transition-transform duration-700 pointer-events-none select-none">
                                </div>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs sm:text-sm font-bold tracking-wider mb-4 sm:mb-6">
                                    Late 2025
                                </div>
                                <h3 class="font-heading font-bold text-2xl md:text-3xl lg:text-4xl text-white mb-4 sm:mb-6">We Found Our Voice (And Your Ears).</h3>
                                <p class="text-white/90 text-base lg:text-lg leading-relaxed">
                                    We didn't wait for permission to be heard. We started where the conversation was happening: Instagram. In those early months, we focused on delivering sharp, visual news bites directly to your feed. The response was electric. Your comments, shares, and DMs told us we were on the right track. This wasn't just a "page"; it was the beginning of a community that demanded more substance from their media.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 3 --}}
                <div class="w-full flex-shrink-0 px-2 sm:px-4 md:px-6 transition-all duration-700 transform scale-95 opacity-50 journey-slide">
                    <div class="glass-card bg-white/10 backdrop-blur-2xl border border-white/20 shadow-2xl rounded-3xl p-6 lg:p-10 h-full flex flex-col justify-center">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                            <div class="order-2 lg:order-1">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs sm:text-sm font-bold tracking-wider mb-4 sm:mb-6">
                                    2026 & Beyond
                                </div>
                                <h3 class="font-heading font-bold text-2xl md:text-3xl lg:text-4xl text-white mb-4 sm:mb-6">From A Post to A Platform.</h3>
                                <p class="text-white/90 text-base lg:text-lg leading-relaxed">
                                    As your trust in us grew, so did we. We realized that our mission—to simplify the noise of the news—needed a bigger stage. Over the next year, we rapidly expanded our footprint, adapting our voice for X (Twitter), LinkedIn, TikTok, and finally, launching our comprehensive news website admin panel—the engine that powers everything you see today. We are no longer just "the three founders on Insta." We are Atomni: a unified, multi-platform media organization built by the community, for the community.
                                </p>
                            </div>
                            <div class="order-1 lg:order-2 relative group">
                                <div class="absolute inset-0 bg-white/20 blur-2xl -m-6 rounded-3xl z-0 transition-opacity opacity-70"></div>
                                <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border border-white/20">
                                    <img loading="lazy" src="{{ asset('assets/images/journey_expansion.png') }}" alt="The Omnichannel Expansion" class="w-full h-auto object-cover aspect-video lg:aspect-[4/3] group-hover:scale-105 transition-transform duration-700 pointer-events-none select-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dots -->
            <div class="flex justify-center items-center gap-3 mt-12" id="journey-dots">
                <button class="h-3 rounded-full bg-white w-8 transition-all duration-300 focus:outline-none" aria-label="Go to slide 1"></button>
                <button class="h-3 rounded-full bg-white/30 w-3 hover:bg-white/60 transition-all duration-300 focus:outline-none" aria-label="Go to slide 2"></button>
                <button class="h-3 rounded-full bg-white/30 w-3 hover:bg-white/60 transition-all duration-300 focus:outline-none" aria-label="Go to slide 3"></button>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('journey-track');
            if(!track) return;
            const slides = track.children;
            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');
            const dots = document.querySelectorAll('#journey-dots button');
            
            let currentIndex = 0;
            const totalSlides = slides.length;
            let autoPlayInterval;

            function updateSlider() {
                track.style.transform = `translateX(-${currentIndex * 100}%)`;
                
                // Update slide classes for cool glassmorphism animation
                Array.from(slides).forEach((slide, index) => {
                    if(index === currentIndex) {
                        slide.classList.remove('scale-95', 'opacity-50');
                        slide.classList.add('scale-100', 'opacity-100');
                    } else {
                        slide.classList.remove('scale-100', 'opacity-100');
                        slide.classList.add('scale-95', 'opacity-50');
                    }
                });

                // Update dots
                dots.forEach((dot, index) => {
                    if (index === currentIndex) {
                        dot.classList.remove('bg-white/30', 'w-3');
                        dot.classList.add('bg-white', 'w-8');
                    } else {
                        dot.classList.remove('bg-white', 'w-8');
                        dot.classList.add('bg-white/30', 'w-3');
                    }
                });
            }

            function goToSlide(index) {
                currentIndex = index;
                if (currentIndex < 0) currentIndex = totalSlides - 1;
                if (currentIndex >= totalSlides) currentIndex = 0;
                updateSlider();
            }

            function nextSlide() { goToSlide(currentIndex + 1); }
            function prevSlide() { goToSlide(currentIndex - 1); }

            function startAutoPlay() {
                autoPlayInterval = setInterval(nextSlide, 5000); // 5 seconds
            }

            function resetAutoPlay() {
                clearInterval(autoPlayInterval);
                startAutoPlay();
            }

            // Event Listeners
            btnPrev.addEventListener('click', () => {
                prevSlide();
                resetAutoPlay();
            });
            btnNext.addEventListener('click', () => {
                nextSlide();
                resetAutoPlay();
            });
            
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    goToSlide(index);
                    resetAutoPlay();
                });
            });

            // Touch Support (Swipe)
            let touchStartX = 0;
            let touchEndX = 0;

            track.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
                clearInterval(autoPlayInterval);
            }, {passive: true});

            track.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
                startAutoPlay();
            }, {passive: true});

            function handleSwipe() {
                const threshold = 50;
                if (touchEndX < touchStartX - threshold) nextSlide(); // Swipe left
                if (touchEndX > touchStartX + threshold) prevSlide(); // Swipe right
            }

            // Initialization
            goToSlide(0);
            startAutoPlay();
            
            // Initial call to ensure classes are applied to the first slide instantly 
            updateSlider();
        });
        </script>
    </div>
</section>

{{-- Call to Action --}}
<section class="py-20 lg:py-28 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-950 light:from-slate-50 light:via-white light:to-slate-100 z-0"></div>
    <div class="absolute inset-0 theme-radial-glow-bl z-0"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="font-heading font-black text-4xl md:text-5xl text-white light:text-slate-900 mb-6 tracking-tight">Built by the community, <span class="text-transparent bg-clip-text bg-gradient-to-r from-electric to-cyan-glow">for the community.</span></h2>
        <p class="text-lg sm:text-xl text-text-secondary light:text-slate-600 leading-relaxed max-w-2xl mx-auto mb-10">We bypassed the legacy corporate media model to speak directly to you. Now, we rely on your true support to keep this independent engine running.</p>
        <a href="{{ route('donate') }}" class="inline-flex items-center justify-center gap-2 bg-electric hover:bg-electric-light text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-electric/25 hover:shadow-electric/40 transition-all hover:-translate-y-1">
            Back Our Mission
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    </div>
</section>

@endsection
