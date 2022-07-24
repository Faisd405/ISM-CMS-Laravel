<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php echo '<?xml-stylesheet type="text/xsl" media="screen" href="/~files/feed-premium.xsl"?>';?>

<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:feedpress="https://feed.press/xmlns" version="2.0">
  <channel>
    {{-- <feedpress:locale>{{ app()->getlocale() }}</feedpress:locale>
    <atom:link rel="hub" href="http://feedpress.superfeedr.com/"/> --}}
    <title>{!! $data['title'] !!}</title>
    {{-- <atom:link href="{{ route('rss.feed') }}" rel="self" type="application/rss+xml"/>
    <link>{{ route('home') }}</link>
    <description>{!! $data['description'] !!}</description>
    <lastBuildDate>Mon, 06 Apr 2020 13:00:25 +0000</lastBuildDate>
    <language>{{ app()->getlocale().'_'.Str::upper(app()->getlocale()) }}</language>
    <sy:updatePeriod>hourly</sy:updatePeriod>
    <sy:updateFrequency>1</sy:updateFrequency>
    <generator>{{ route('home') }}</generator> --}}
    @foreach($data['posts'] as $post)
    <item>
      <title>{!! $post->fieldLang('title') !!}</title>
      <link>{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}</link>
      <pubDate>{{ $post['created_at'] }}</pubDate>
      <dc:creator><![CDATA[{!! $post['createBy']['name'] !!}]]></dc:creator>
      @if (!empty($post['category_id']))
      <category><![CDATA[@foreach($post['categories'] as $cat) {!! $cat->fieldLang('name') !!}, @endforeach]]></category>
      @endif
      <guid isPermaLink="false">{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}</guid>
      <description><![CDATA[
                <p>
                <img style="float:left;height:150px;width:250px;" src="{{ $post['cover_src'] }}">{!! $post->fieldLang('intro') !!}
                </p>

                <hr>
                ]]></description>
    </item>
    @endforeach

  </channel>
</rss>
