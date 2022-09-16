<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php echo '<?xml-stylesheet type="text/xsl" media="screen" href="/~files/feed-premium.xsl"?>';?>

<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:feedpress="https://feed.press/xmlns" version="2.0">
  <channel>
        <title>{!! $data['title'] !!}</title>
        <link><![CDATA[{{ route('rss.post') }}]]></link>
        <description><![CDATA[{!! $data['description'] !!}]]></description>
        <language><![CDATA[{{ App::getLocale() }}]]></language>
        <pubDate><![CDATA[{{ now()->toDayDateTimeString('Asia/Jakarta') }}]]></pubDate>

        @foreach($data['posts'] as $post)
        <item>
            <title><![CDATA[{!! $post->fieldLang('title') !!}]]></title>
            <link><![CDATA[{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}]]></link>
            <description><![CDATA[{!! !empty($post->fieldLang('intro')) ? strip_tags($post->fieldLang('intro')) : strip_tags($post->fieldLang('content')) !!}]]></description>
            @if (!empty($post['category_id']))
            <category><![CDATA[@foreach($post->categories() as $cat) {!! $cat->fieldLang('name') !!}, @endforeach]]></category>
            @endif
            <author><![CDATA[{{ $post->posted_by_alias ?? $post->createBy['name']  }}]]></author>
            <guid isPermaLink="false"><![CDATA[{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}]]></guid>
            <pubDate><![CDATA[{{ $post->created_at->toRssString() }}]]></pubDate>
        </item>
        @endforeach
  </channel>
</rss>
