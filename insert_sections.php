<?php
$file = 'resources/views/home.blade.php';
$content = file_get_contents($file);

$insert = <<<EOD

@if(isset(\$dynamicSections) && \$dynamicSections->count() > 0)
{{-- "?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?
     ",  DYNAMIC HOMEPAGE SECTIONS                                  ",
     "?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"?"? --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 space-y-12">
    @foreach(\$dynamicSections as \$section)
        @php \$sectionPosts = \$section->getPosts(); @endphp
        @if(\$sectionPosts->count() > 0)
        <div class="border-b border-navy-700/50 pb-12 last:border-0 last:pb-0">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-heading font-bold text-xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                    @if(\$section->category)
                        <a href="{{ route('category', \$section->category->slug) }}" class="hover:text-electric transition-colors">{{ \$section->title }}</a>
                        <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    @else
                        <span>{{ \$section->title }}</span>
                    @endif
                </h2>
            </div>
            
            @if(\$section->layout_type == 'grid')
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @else
                <div class="flex overflow-x-auto gap-6 pb-4 snap-x">
            @endif
            
                @foreach(\$sectionPosts as \$post)
                <a href="{{ route('frontend.article', \$post->slug) }}" class="block group flex flex-col {{ \$section->layout_type != 'grid' ? 'shrink-0 w-[280px] snap-start' : '' }}">
                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-3">
                        @if(\$post->featured_image)
                            <img src="{{ \$post->featuredImageUrl() }}" alt="{{ \$post->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <img src="{{ asset('images/atomni-placeholder.svg') }}" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 to-transparent opacity-50 group-hover:opacity-80 transition-opacity"></div>
                    </div>
                    <h3 class="font-heading font-semibold text-sm text-text-primary group-hover:text-electric transition-colors line-clamp-3">
                        {{ \$post->title }}
                    </h3>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
</section>
@endif

EOD;

$pattern = '/\{\{-- .*?SPECIFIC CATEGORY SECTIONS.*?--\}\}/is';
$newContent = preg_replace($pattern, $insert . '$0', $content, 1);
file_put_contents($file, $newContent);
echo "Inserted successfully.\n";

