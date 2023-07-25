<?php

namespace App\Http\Controllers\Module\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Banner\BannerRequest;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\Module\BannerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    private $bannerService, $languageService;

    public function __construct(
        BannerRepository $bannerService,
        LanguageRepository $languageService
    )
    {
        $this->bannerService = $bannerService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request)
    {
        $filter = [];
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['banners'] = $this->bannerService->getBannerList($filter, true, 10, false, [],
            config('cms.module.banner.ordering'));
        $data['no'] = $data['banners']->firstItem();
        $data['banners']->withQueryString();

        return view('backend.banners.index', compact('data'), [
            'title' => __('module/banner.title'),
            'breadcrumbs' => [
                __('module/banner.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request)
    {
        $filter = [];
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['banners'] = $this->bannerService->getBannerList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['banners']->firstItem();
        $data['banners']->withQueryString();

        return view('backend.banners.trash', compact('data'), [
            'title' => __('module/banner.title').' - '.__('global.trash'),
            'routeBack' => route('banner.index'),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/banner.caption')
            ]),
            'routeBack' => route('banner.index', $request->query()),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(BannerRequest $request)
    {
        $data = $request->all();
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_type_text'] = (bool)$request->config_type_text;
        $data['config_type_image'] = (bool)$request->config_type_image;
        $data['config_type_video'] = (bool)$request->config_type_video;
        $banner = $this->bannerService->storeBanner($data);
        $data['query'] = $request->query();

        if ($banner['success'] == true) {
            return $this->redirectForm($data)->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['banner'] = $this->bannerService->getBanner(['id' => $id]);
        if (empty($data['banner']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/banner.caption')
            ]),
            'routeBack' => route('banner.index', $request->query()),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(BannerRequest $request, $id)
    {
        $data = $request->all();
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_type_text'] = (bool)$request->config_type_text;
        $data['config_type_image'] = (bool)$request->config_type_image;
        $data['config_type_video'] = (bool)$request->config_type_video;
        $banner = $this->bannerService->updateBanner($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($banner['success'] == true) {
            return $this->redirectForm($data)->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function publish($id)
    {
        $banner = $this->bannerService->statusBanner('publish', ['id' => $id]);

        if ($banner['success'] == true) {
            return back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function approved($id)
    {
        $banner = $this->bannerService->statusBanner('approved', ['id' => $id]);

        if ($banner['success'] == true) {
            return back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->bannerService->sortBanner(['id' => $value], $i);
        }
    }

    public function position(Request $request, $id, $position)
    {
        $banner = $this->bannerService->positionBanner(['id' => $id], $position);

        if ($banner['success'] == true) {
            return back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function softDelete($id)
    {
        $banner = $this->bannerService->trashBanner(['id' => $id]);

        return $banner;
    }

    public function permanentDelete(Request $request, $id)
    {
        $banner = $this->bannerService->deleteBanner($request, ['id' => $id]);

        return $banner;
    }

    public function restore($id)
    {
        $banner = $this->bannerService->restoreBanner(['id' => $id]);

        if ($banner['success'] == true) {
            return redirect()->back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('banner.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
