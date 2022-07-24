<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@if (config('cms.module.page.active') == true)
    @if (config('cms.module.page.list_view') == true)
    <sitemap>
        <loc>{{ route('page.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['pages'] as $page)
    <sitemap>
        <loc>{{ route('page.read.'.$page['slug']) }}</loc>
        <lastmod>{{ $page['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.content.section.active') == true)
    @if (config('cms.module.content.section.list_view') == true)
    <sitemap>
        <loc>{{ route('content.section.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['content_sections'] as $section)
    <sitemap>
        <loc>{{ route('content.section.read.'.$section['slug']) }}</loc>
        <lastmod>{{ $section['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.content.category.active') == true)
    @if (config('cms.module.content.category.list_view') == true)
    <sitemap>
        <loc>{{ route('content.category.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['content_categories'] as $category)
    <sitemap>
        <loc>{{ route('content.category.read.'.$category['section']['slug'], ['slugCategory' => $category['slug']]) }}</loc>
        <lastmod>{{ $category['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.content.post.active') == true)
    @if (config('cms.module.content.post.list_view') == true)
    <sitemap>
        <loc>{{ route('content.post.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['content_posts'] as $post)
    <sitemap>
        <loc>{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}</loc>
        <lastmod>{{ $post['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.gallery.active') == true)
    @if (config('cms.module.gallery.list_view') == true)
    <sitemap>
        <loc>{{ route('gallery.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['gallery_categories'] as $category)
    <sitemap>
        <loc>{{ route('gallery.category.read', ['slugCategory' => $category['slug']]) }}</loc>
        <lastmod>{{ $category['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
    @foreach ($data['gallery_albums'] as $album)
    <sitemap>
        <loc>{{ route('gallery.album.read', ['slugAlbum' => $album['slug']]) }}</loc>
        <lastmod>{{ $album['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.document.active') == true)
    @if (config('cms.module.document.list_view') == true)
    <sitemap>
        <loc>{{ route('document.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['documents'] as $document)
    <sitemap>
        <loc>{{ route('document.read', ['slugDocument' => $document['slug']]) }}</loc>
        <lastmod>{{ $document['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.link.active') == true)
    @if (config('cms.module.link.list_view') == true)
    <sitemap>
        <loc>{{ route('link.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['links'] as $link)
    <sitemap>
        <loc>{{ route('link.read.'.$link['slug']) }}</loc>
        <lastmod>{{ $link['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.inquiry.active') == true)
    @if (config('cms.module.inquiry.list_view') == true)
    <sitemap>
        <loc>{{ route('inquiry.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['inquiries'] as $inquiry)
    <sitemap>
        <loc>{{ route('inquiry.read.'.$inquiry['slug']) }}</loc>
        <lastmod>{{ $inquiry['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.event.active') == true)
    @if (config('cms.module.event.list_view') == true)
    <sitemap>
        <loc>{{ route('event.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['events'] as $event)
    <sitemap>
        <loc>{{ route('event.read', ['slugEvent' => $event['slug']]) }}</loc>
        <lastmod>{{ $event['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
</sitemapindex>