@php
    $posts = $section->getPosts();
@endphp

@if($posts->count() > 0)
    @if($section->layout_type == 'tech_complex_grid')
        {{-- Technology Complex Grid Layout --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-7 bg-electric rounded-full"></div>
                <h2 class="font-heading font-bold text-2xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                    <a href="{{ $section->category ? route('category', $section->category->slug) : '#' }}" class="hover:text-electric transition-colors">{{ $section->title }}</a>
                    @if($section->category)
                        <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    @endif
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Featured Post (Spans 2 columns) --}}
                @if($featured = $posts->first())
                <div class="lg:col-span-2 row-span-2 group">
                    <a href="{{ route('frontend.article', $featured->slug) }}" class="block relative h-full min-h-[400px] rounded-2xl overflow-hidden shadow-xl border border-navy-700/50">
                        <img loading="lazy" src="{{ $featured->featuredImageUrl() }}" alt="{{ $featured->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-900/60 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
                            @if($featured->category)
                            <span class="inline-block px-3 py-1 bg-electric/90 text-white text-xs font-bold uppercase tracking-wider rounded-lg mb-3">{{ $featured->category->name }}</span>
                            @endif
                            <h3 class="font-heading font-bold text-2xl sm:text-3xl text-white mb-2 leading-tight group-hover:text-electric-light transition-colors">{{ $featured->title }}</h3>
                            <p class="text-text-muted text-sm line-clamp-2 mb-3">{{ $featured->excerpt }}</p>
                            <div class="flex items-center gap-3 text-xs text-text-muted">
                                <span>{{ $featured->author?->name ?? 'Staff' }}</span>
                                <span>A</span>
                                <span>{{ $featured->published_at?->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                
                {{-- Other Posts --}}
                @foreach($posts->skip(1)->take(4) as $post)
                <div class="group">
                    <a href="{{ route('frontend.article', $post->slug) }}" class="block h-full glass-card rounded-xl overflow-hidden hover:bg-navy-800/60 transition-colors">
                        <div class="relative aspect-[4/3] overflow-hidden bg-navy-800 border-b border-navy-700/50">
                            <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @if($post->category)
                            <div class="absolute top-3 right-3 px-2.5 py-1 bg-navy-900/80 backdrop-blur border border-navy-700 text-[10px] font-bold text-electric uppercase tracking-wider rounded-md">
                                {{ $post->category->name }}
                            </div>
                            @endif
                        </div>
                        <div class="p-4 sm:p-5">
                            <h3 class="font-heading font-semibold text-text-primary text-base leading-snug group-hover:text-electric-light transition-colors line-clamp-3 mb-3">{{ $post->title }}</h3>
                            <div class="mt-auto flex items-center justify-between text-xs text-text-muted">
                                <span>{{ $post->published_at?->diffForHumans() }}</span>
                                <span class="flex items-center gap-1 group-hover:text-electric transition-colors">Read <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </section>

    @elseif($section->layout_type == '3d_carousel')
        {{-- 3D Movie Carousel Layout --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-7 bg-pink-500 rounded-full shadow-[0_0_10px_rgba(236,72,153,0.5)]"></div>
                <h2 class="font-heading font-bold text-2xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                    <a href="{{ $section->category ? route('category', $section->category->slug) : '#' }}" class="hover:text-pink-400 transition-colors">{{ $section->title }}</a>
                    @if($section->category)
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    @endif
                </h2>
            </div>
            
            <div class="relative rounded-3xl bg-navy-900/50 border border-navy-800 p-8 sm:p-12 overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-pink-500/10 blur-[100px] rounded-full pointer-events-none"></div>
                
                <div class="swiper three-d-carousel pb-12">
                    <div class="swiper-wrapper">
                        @foreach($posts as $post)
                            <div class="swiper-slide w-64 sm:w-80">
                                <a href="{{ route('frontend.article', $post->slug) }}" class="block group relative rounded-2xl overflow-hidden bg-navy-800 shadow-2xl border border-navy-700/50 aspect-[2/3]">
                                    <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-900/40 to-transparent"></div>
                                    
                                    <!-- Play/Review icon -->
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="w-16 h-16 rounded-full bg-pink-500/90 flex items-center justify-center backdrop-blur shadow-[0_0_30px_rgba(236,72,153,0.5)] transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                            <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    
                                    <div class="absolute bottom-0 left-0 right-0 p-6 text-center transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                        <h3 class="font-heading font-bold text-xl text-white leading-tight mb-2 text-shadow">{{ $post->title }}</h3>
                                        <div class="flex items-center justify-center gap-2 text-pink-400 text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">
                                            Read Review <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination !bottom-0"></div>
                </div>
            </div>
        </section>

    @elseif($section->layout_type == 'horizontal_scroll')
        {{-- Horizontal Scroll Layout --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-7 bg-cyan-glow rounded-full shadow-[0_0_10px_rgba(0,255,255,0.5)]"></div>
                    <h2 class="font-heading font-bold text-2xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                        <a href="{{ $section->category ? route('category', $section->category->slug) : '#' }}" class="hover:text-cyan-glow transition-colors">{{ $section->title }}</a>
                        @if($section->category)
                            <svg class="w-5 h-5 text-cyan-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        @endif
                    </h2>
                </div>
                
                <div class="hidden sm:flex items-center gap-2">
                    <button class="nav-btn prev-{{ $section->id }} w-10 h-10 rounded-full bg-navy-800 hover:bg-cyan-glow text-text-secondary hover:text-navy-950 flex items-center justify-center transition-all border border-navy-700 hover:border-cyan-glow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button class="nav-btn next-{{ $section->id }} w-10 h-10 rounded-full bg-navy-800 hover:bg-cyan-glow text-text-secondary hover:text-navy-950 flex items-center justify-center transition-all border border-navy-700 hover:border-cyan-glow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
            
            <div class="swiper horizontal-carousel-{{ $section->id }} -mx-4 px-4 sm:mx-0 sm:px-0">
                <div class="swiper-wrapper">
                    @foreach($posts as $post)
                        <div class="swiper-slide w-48 sm:w-64">
                            <a href="{{ route('frontend.article', $post->slug) }}" class="block group">
                                <div class="relative aspect-[9/16] rounded-2xl overflow-hidden bg-navy-800 shadow-lg border border-navy-700/50 mb-3">
                                    <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-900/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                                    
                                    <!-- Play Icon overlay for video feeling -->
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="w-12 h-12 rounded-full bg-black/50 backdrop-blur flex items-center justify-center border border-white/20">
                                            <svg class="w-5 h-5 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>

                                    <div class="absolute bottom-0 left-0 right-0 p-4">
                                        <h3 class="font-heading font-semibold text-white text-sm leading-tight line-clamp-3 mb-1">{{ $post->title }}</h3>
                                        <p class="text-text-muted text-[10px]">{{ $post->published_at?->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    @else
        {{-- Standard Grid Layout --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-7 bg-electric rounded-full"></div>
                <h2 class="font-heading font-bold text-2xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                    <a href="{{ $section->category ? route('category', $section->category->slug) : '#' }}" class="hover:text-electric transition-colors">{{ $section->title }}</a>
                    @if($section->category)
                        <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    @endif
                </h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <a href="{{ route('frontend.article', $post->slug) }}" class="flex flex-col h-full group glass-card rounded-2xl overflow-hidden hover:bg-navy-800/60 transition-colors">
                        <div class="relative aspect-video overflow-hidden bg-navy-800 border-b border-navy-700/50">
                            <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @if($post->category)
                            <div class="absolute top-3 right-3 px-2.5 py-1 bg-navy-900/80 backdrop-blur border border-navy-700 text-[10px] font-bold text-electric uppercase tracking-wider rounded-md">
                                {{ $post->category->name }}
                            </div>
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-heading font-semibold text-text-primary text-lg leading-snug group-hover:text-electric-light transition-colors line-clamp-2 mb-2">{{ $post->title }}</h3>
                            <p class="text-text-secondary text-sm line-clamp-2 mb-4">{{ $post->excerpt }}</p>
                            <div class="mt-auto flex items-center justify-between text-xs text-text-muted">
                                <span>{{ $post->author?->name ?? 'Staff Writer' }}</span>
                                <span>{{ $post->published_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    
    @if(in_array($section->layout_type, ['3d_carousel', 'horizontal_scroll']))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if($section->layout_type == '3d_carousel')
                        if(typeof Swiper !== 'undefined') {
                            new Swiper('.three-d-carousel', {
                                effect: 'coverflow',
                                grabCursor: true,
                                centeredSlides: true,
                                slidesPerView: 'auto',
                                initialSlide: 1,
                                coverflowEffect: {
                                    rotate: 30,
                                    stretch: 0,
                                    depth: 150,
                                    modifier: 1,
                                    slideShadows: true,
                                },
                                pagination: {
                                    el: '.swiper-pagination',
                                    dynamicBullets: true,
                                },
                            });
                        }
                    @elseif($section->layout_type == 'horizontal_scroll')
                        if(typeof Swiper !== 'undefined') {
                            new Swiper('.horizontal-carousel-{{ $section->id }}', {
                                slidesPerView: 'auto',
                                spaceBetween: 16,
                                freeMode: true,
                                navigation: {
                                    nextEl: '.next-{{ $section->id }}',
                                    prevEl: '.prev-{{ $section->id }}',
                                },
                            });
                        }
                    @endif
                });
            </script>
        @endpush
    @endif
@endif
