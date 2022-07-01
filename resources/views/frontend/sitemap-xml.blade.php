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
    <sitemap>
        <loc>{{ route('gallery.list') }}</loc>
    </sitemap>
    @foreach ($data['gallery_categories'] as $cat)
    <sitemap>
        <loc>{{ route('gallery.category.read', ['slugCategory' => $cat['slug']]) }}</loc>
        <lastmod>{{ $cat['updated_at'] }}</lastmod>
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
    @foreach ($data['document_categories'] as $cat)
    <sitemap>
        <loc>{{ route('document.category.read', ['slugCategory' => $cat['slug']]) }}</loc>
        <lastmod>{{ $cat['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.link.active') == true)
    @if (config('cms.module.link.list_view') == true)
    <sitemap>
        <loc>{{ route('link.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['link_categories'] as $cat)
    <sitemap>
        <loc>{{ route('link.category.read', ['slugCategory' => $cat['slug']]) }}</loc>
        <lastmod>{{ $cat['updated_at'] }}</lastmod>
    </sitemap>
    @endforeach
@endif
@if (config('cms.module.inquiry.active') == true)
    @if (config('cms.module.inquiry.list_view') == true)
    <sitemap>
        <loc>{{ route('inquiry.list') }}</loc>
    </sitemap>
    @endif
    @foreach ($data['inquiries'] as $inq)
    <sitemap>
        <loc>{{ route('inquiry.read.'.$inq['slug']) }}</loc>
        <lastmod>{{ $inq['updated_at'] }}</lastmod>
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