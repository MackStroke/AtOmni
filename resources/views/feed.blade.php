<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL; ?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">

  <channel>
    <title>{{ $feedTitle }}</title>
    <link>{{ $siteUrl }}</link>
    <description>{{ $feedDescription }}</description>
    <language>en-in</language>
    <lastBuildDate>{{ $lastBuildDate }}</lastBuildDate>
    <pubDate>{{ $buildDate }}</pubDate>
    <ttl>30</ttl>
    <generator>Atomni CMS</generator>
    <atom:link href="{{ $feedUrl }}" rel="self" type="application/rss+xml"/>
    <?php $siteLogo = \App\Models\Setting::get("site_logo"); ?>
    @if($siteLogo)
    <image>
      <url>{{ asset("storage/" . ltrim($siteLogo, "/")) }}</url>
      <title>{{ $feedTitle }}</title>
      <link>{{ $siteUrl }}</link>
    </image>
    @endif

    @foreach($posts ?? [] as $post)
    <item>
      <title>{{ $post->title }}</title>
      <link>{{ route("frontend.article", $post->slug) }}</link>
      <guid isPermaLink="true">{{ route("frontend.article", $post->slug) }}</guid>
      <pubDate>{{ ($post?->published_at ?? $post?->created_at ?? now())->toRfc1123String() }}</pubDate>
      @if($post->author)
      <dc:creator>{{ $post->author?->name }}</dc:creator>
      @endif
      @if($post->category)
      <category>{{ $post->category->name }}</category>
      @endif
      @if($post->excerpt)
      <description>{{ $post->excerpt }}</description>
      @else
      <description>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 300) }}</description>
      @endif
      <content:encoded><![CDATA[{!! $post->content !!}]]></content:encoded>
      @php $imgUrl = $post->featuredImageUrl(); @endphp
      @if($imgUrl && !str_ends_with($imgUrl, "atomni-placeholder.svg"))
      <enclosure url="{{ $imgUrl }}" type="image/jpeg" length="0"/>
      <media:content url="{{ $imgUrl }}" medium="image"/>
      @endif
      @if($post->reading_time)
      {{-- Custom reading time as a description tag extension --}}
      @endif
    </item>
    @endforeach

  </channel>
</rss>
