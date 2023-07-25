<?php

namespace App\Http\Controllers;

use App\Repositories\Feature\ConfigurationRepository;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\Module\ContentRepository;
use App\Repositories\Module\DocumentRepository;
use App\Repositories\Module\EventRepository;
use App\Repositories\Module\GalleryRepository;
use App\Repositories\Module\InquiryRepository;
use App\Repositories\Module\LinkRepository;
use App\Repositories\Module\PageRepository;
use App\Repositories\Module\WidgetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function landing(Request $request)
    {
        if (config('cms.setting.url.landing') == false)
            return redirect()->route('home');

        $data['data'] = null;

        return view('frontend.landing', compact('data'));
    }

    public function home(Request $request)
    {
        $filter['widget_set'] = 0;
        $filter['global'] = 0;
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        $data['widgets'] = App::make(WidgetRepository::class)->getWidgetList($filter, false, 10, false, [], [
            'position' => 'ASC'
        ]);
        foreach ($data['widgets'] as $key => $value) {
            $data['widgets'][$key]['module'] = App::make(WidgetRepository::class)->getModuleData($value);
        }

        return view('frontend.index', compact('data'));
    }

    public function search(Request $request)
    {
        if (config('cms.setting.url.search') == false)
            return redirect()->route('home');

        $keyword = $request->input('keyword', '');
        if ($keyword == '')
            return redirect()->route('home');

        $filter['publish'] = 1;
        $filter['approved'] = 1;
        $filter['detail'] = 1;
        if ($keyword != '') {
            $filter['q'] = $keyword;
        }

        $data = [];
        if (config('cms.module.page.search') == true)
            $data['pages'] = App::make(PageRepository::class)->getPageList($filter, false, 3, false, [], []);

        if (config('cms.module.content.section.search') == true)
            $data['content_sections'] = App::make(ContentRepository::class)->getSectionList($filter, false, 3, false, [], []);

        if (config('cms.module.content.category.search') == true)
            $data['content_categories'] = App::make(ContentRepository::class)->getCategoryList($filter, false, 3, false, [], []);

        if (config('cms.module.content.post.search') == true)
            $data['content_posts'] = App::make(ContentRepository::class)->getPostList($filter, false, 3, false, [], []);

        if (config('cms.module.gallery.category.search') == true)
            $data['gallery_categories'] = App::make(GalleryRepository::class)->getCategoryList($filter, false, 3, false, [], []);

        if (config('cms.module.gallery.album.search') == true)
            $data['gallery_albums'] = App::make(GalleryRepository::class)->getAlbumList($filter, false, 3, false, [], []);

        if (config('cms.module.document.search') == true)
            $data['documents'] = App::make(DocumentRepository::class)->getDocumentList($filter, false, 3, false, [], []);

        if (config('cms.module.link.search') == true)
            $data['links'] = App::make(LinkRepository::class)->getLinkList($filter, false, 3, false, [], []);

        if (config('cms.module.inquiry.search') == true)
            $data['inquiries'] = App::make(InquiryRepository::class)->getInquiryList($filter, false, 3, false, [], []);

        if (config('cms.module.event.search') == true)
            $data['events'] = App::make(EventRepository::class)->getEventList($filter, false, 3, false, [], []);

        return view('frontend.search', compact('data'), [
            'title' => __('global.search'),
            'breadcrumbs' => [

            ],
        ]);
    }

    public function sitemap(Request $request)
    {
        if (config('cms.setting.url.sitemap') == false)
            return redirect()->route('home');

        $data = [];

        return view('frontend.sitemap', compact('data'), [
            'title' => 'Sitemap'
        ]);
    }

    public function sitemapXml(Request $request)
    {
        $filter['publish'] = 1;
        $filter['approved'] = 1;
        $filter['detail'] = 1;

        $multiple = config('cms.module.feature.language.multiple');
        $data['languages'] = App::make(LanguageRepository::class)->getLanguageActive($multiple);
        $data['pages'] = App::make(PageRepository::class)->getPageList($filter, false, 0, false, [], []);
        $data['content_sections'] = App::make(ContentRepository::class)->getSectionList($filter, false, 0, false, [], []);
        $data['content_categories'] = App::make(ContentRepository::class)->getCategoryList($filter, false, 0, false, [], []);
        $data['content_posts'] = App::make(ContentRepository::class)->getPostList($filter, false, 0, false, [], []);
        $data['gallery_categories'] = App::make(GalleryRepository::class)->getCategoryList($filter, false, 0, false, [], []);
        $data['gallery_albums'] = App::make(GalleryRepository::class)->getAlbumList($filter, false, 0, false, [], []);
        $data['documents'] = App::make(DocumentRepository::class)->getDocumentList($filter, false, 0, false, [], []);
        $data['links'] = App::make(LinkRepository::class)->getLinkList($filter, false, 0, false, [], []);
        $data['inquiries'] = App::make(InquiryRepository::class)->getInquiryList($filter, false, 0, false, [], []);
        $data['events'] = App::make(EventRepository::class)->getEventList($filter, false, 0, false, [], []);

        return response()->view('frontend.sitemap-xml', compact('data'))
            ->header('Content-Type', 'text/xml');
    }

    public function feed(Request $request)
    {
        if (config('cms.setting.url.feed') == false)
            return redirect()->route('home');

        $data['title'] = config('cmsConfig.seo.meta_title');
        $data['description'] = config('cmsConfig.seo.meta_description');
        $data['posts'] = App::make(ContentRepository::class)->getPostList([
            'publish' => 1,
            'approved' => 1,
        ], false, 0, false, [], []);

        return view('frontend.rss.feed', compact('data'));
    }

    public function post(Request $request)
    {
        if (config('cms.setting.url.feed') == false)
            return redirect()->route('home');

        $data['title'] = config('cmsConfig.seo.meta_title');
        $data['description'] = config('cmsConfig.seo.meta_description');
        $data['posts'] = App::make(ContentRepository::class)->getPostList([
            'publish' => 1,
            'approved' => 1
        ], false, 0, false, [], []);

        return view('frontend.rss.post', compact('data'));
    }

    public function maintenance(Request $request)
    {
        if (App::make(ConfigurationRepository::class)->getConfigValue('maintenance') == 0) {
            return redirect()->route('home');
        }

        return view('frontend.maintenance', [
            'title' => __('global.maintenance.title')
        ]);
    }
}
