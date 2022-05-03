<?php

namespace App\Http\Controllers;

use App\Services\Feature\ConfigurationService;
use App\Services\Module\ContentService;
use App\Services\Module\DocumentService;
use App\Services\Module\GalleryService;
use App\Services\Module\LinkService;
use App\Services\Module\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    private $configService;

    public function __construct(
        ConfigurationService $configService
    )
    {
        $this->configService = $configService;
    }

    public function landing(Request $request)
    {
        if (config('cms.setting.url.landing') == false)
            return redirect()->route('home');
        
        $data['data'] = null;

        return view('frontend.landing', compact('data'));
    }

    public function home(Request $request)
    {
        $data['data'] = null;

        return view('frontend.index', compact('data'));
    }

    public function search(Request $request)
    {
        if (config('cms.setting.url.search') == false)
            return redirect()->route('home');

        if ($request->input('keyword', '') == '')
            return redirect()->route('home');

        $data = null;

        return view('frontend.search', compact('data'), [
            'title' => __('global.search'),
            'breadcrumbs' => [

            ],
        ]);
    }

    public function sitemap(Request $request)
    {
        $data['pages'] = App::make(PageService::class)->getPageList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['content_sections'] = App::make(ContentService::class)->getSectionList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['content_categories'] = App::make(ContentService::class)->getCategoryList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['content_posts'] = App::make(ContentService::class)->getPostList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['gallery_categories'] = App::make(GalleryService::class)->getCategoryList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['gallery_albums'] = App::make(GalleryService::class)->getAlbumList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['document_categories'] = App::make(DocumentService::class)->getCategoryList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        $data['link_categories'] = App::make(LinkService::class)->getCategoryList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        return response()->view('frontend.sitemap', compact('data'))
            ->header('Content-Type', 'text/xml');
    }

    public function feed(Request $request)
    {
        $data['title'] = $this->configService->getConfigValue('meta_title');
        $data['description'] = $this->configService->getConfigValue('meta_description');
        $data['posts'] = App::make(ContentService::class)->getPostList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        return view('frontend.rss.feed', compact('data'));
    }

    public function post(Request $request)
    {
        $data['title'] = $this->configService->getConfigValue('meta_title');
        $data['description'] = $this->configService->getConfigValue('meta_description');
        $data['posts'] = App::make(ContentService::class)->getPostList([
            'publish' => 1,
            'approved' => 1,
            'is_detail' => 1
        ], false, 0, false, [], []);

        return view('frontend.rss.post', compact('data'));
    }

    public function maintenance(Request $request)
    {
        if ($this->configService->getConfigValue('maintenance') == false) {
            return redirect()->route('home');
        }

        return view('frontend.maintenance', [
            'title' => __('global.maintenance.title')
        ]);
    }
}
