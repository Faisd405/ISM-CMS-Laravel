<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    @foreach ($data['languages'] as $lang)
        {{-- Home --}}
        <url>
            <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('/') : url($lang['iso_codes']) }}</loc>
            <lastmod>{{ \Carbon\Carbon::parse($lang['created_at'])->tz('UTC')->toAtomString() }}</lastmod>
            <priority>1.00</priority>
        </url>
        {{-- Landing --}}
        @if (config('cms.setting.url.landing') == true)    
        <url>
            <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('landing') : url($lang['iso_codes'].'/landing') }}</loc>
            <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
            <priority>0.04</priority>
        </url>
        @endif
        {{-- Search --}}
        @if (config('cms.setting.url.search') == true)    
        <url>
            <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('search') : url($lang['iso_codes'].'/search') }}</loc>
            <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
            <priority>0.04</priority>
        </url>
        @endif
        {{-- Sitemap --}}
        @if (config('cms.setting.url.sitemap') == true)    
        <url>
            <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('sitemap') : url($lang['iso_codes'].'/sitemap') }}</loc>
            <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
            <priority>0.04</priority>
        </url>
        @endif
        {{-- Page --}}
        @if (config('cms.module.page.active') == true)
            @if (config('cms.module.page.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('page') : url($lang['iso_codes'].'/page') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['pages'] as $page)
            <url>
                @if ($page['parent'] == 0)
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($page['slug']) : url($lang['iso_codes'].'/'.$page['slug']) }}</loc>
                @else
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($page['path_parent']) : url($lang['iso_codes'].'/'.$page['path_parent']) }}</loc>
                @endif
                <lastmod>{{ \Carbon\Carbon::parse($page['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                @if ($page['parent'] == 0)
                <priority>0.80</priority>
                @else
                <priority>0.64</priority>
                @endif
            </url>
            @endforeach
        @endif
        {{-- Content Section --}}
        @if (config('cms.module.content.section.active') == true)
            @if (config('cms.module.content.section.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('content/section') : url($lang['iso_codes'].'/content/section') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['content_sections'] as $section)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($section['slug']) : url($lang['iso_codes'].'/'.$section['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($section['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.80</priority>
            </url>
            @endforeach
        @endif
        {{-- Content Category --}}
        @if (config('cms.module.content.category.active') == true)
            @if (config('cms.module.content.category.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('content/cat') : url($lang['iso_codes'].'/content/cat') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['content_categories'] as $category)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($category['section']['slug'].'/cat/'.$category['slug']) : url($lang['iso_codes'].'/'.$category['section']['slug'].'/cat/'.$category['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($category['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.51</priority>
            </url>
            @endforeach
        @endif
        {{-- Content Post --}}
        @if (config('cms.module.content.post.active') == true)
            @if (config('cms.module.content.post.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('content/post') : url($lang['iso_codes'].'/content/post') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['content_posts'] as $post)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($post['section']['slug'].'/'.$post['slug']) : url($lang['iso_codes'].'/'.$post['section']['slug'].'/'.$post['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($post['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.64</priority>
            </url>
            @endforeach
        @endif
        {{-- Gallery --}}
        @if (config('cms.module.gallery.active') == true)
            @if (config('cms.module.gallery.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('gallery') : url($lang['iso_codes'].'/gallery') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['gallery_categories'] as $category)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('gallery/cat/'.$category['slug']) : url($lang['iso_codes'].'/gallery/cat/'.$category['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($category['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.51</priority>
            </url>
            @endforeach
            @foreach ($data['gallery_albums'] as $album)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('gallery/'.$album['slug']) : url($lang['iso_codes'].'/gallery/'.$album['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($album['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.64</priority>
            </url>
            @endforeach
        @endif
        {{-- Document --}}
        @if (config('cms.module.document.active') == true)
            @if (config('cms.module.document.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('document') : url($lang['iso_codes'].'/document') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['documents'] as $document)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('document/'.$document['slug']) : url($lang['iso_codes'].'/document/'.$document['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($document['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.64</priority>
            </url>
            @endforeach
        @endif
        {{-- Link --}}
        @if (config('cms.module.link.active') == true)
            @if (config('cms.module.link.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('link') : url($lang['iso_codes'].'/link') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['links'] as $link)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($link['slug']) : url($lang['iso_codes'].'/'.$link['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($link['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.80</priority>
            </url>
            @endforeach
        @endif
        {{-- Inquiry --}}
        @if (config('cms.module.inquiry.active') == true)
            @if (config('cms.module.inquiry.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('inquiry') : url($lang['iso_codes'].'/inquiry') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.04</priority>
            </url>
            @endif
            @foreach ($data['inquiries'] as $inquiry)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url($inquiry['slug']) : url($lang['iso_codes'].'/'.$inquiry['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($inquiry['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.80</priority>
            </url>
            @endforeach
        @endif
        {{-- Event --}}
        @if (config('cms.module.event.active') == true)
            @if (config('cms.module.event.list_view') == true)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('event') : url($lang['iso_codes'].'/event') }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <priority>0.80</priority>
            </url>
            @endif
            @foreach ($data['events'] as $event)
            <url>
                <loc>{{ $lang['iso_codes'] == config('app.fallback_locale') ? url('event/'.$event['slug']) : url($lang['iso_codes'].'/event/'.$event['slug']) }}</loc>
                <lastmod>{{ \Carbon\Carbon::parse($event['updated_at'])->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.64</priority>
            </url>
            @endforeach
        @endif
    @endforeach
</urlset>