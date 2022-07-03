<?php

namespace App\Http\Controllers\Module\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Banner\BannerMultipleRequest;
use App\Http\Requests\Module\Banner\BannerRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\BannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    private $bannerService, $languageService;

    public function __construct(
        BannerService $bannerService,
        LanguageService $languageService
    )
    {
        $this->bannerService = $bannerService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $categoryId)
    {
        $filter['category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['category'] = $this->bannerService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['banners'] = $this->bannerService->getBannerList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['banners']->firstItem();
        $data['banners']->withQueryString();

        return view('backend.banners.index', compact('data'), [
            'title' => __('module/banner.title'),
            'routeBack' => route('banner.category.index'),
            'breadcrumbs' => [
                __('module/banner.category.caption') => route('banner.category.index'),
                __('module/banner.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $categoryId)
    {
        $filter['category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['category'] = $this->bannerService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['banners'] = $this->bannerService->getBannerList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['banners']->firstItem();
        $data['banners']->withQueryString();

        return view('backend.banners.trash', compact('data'), [
            'title' => __('module/banner.title').' - '.__('global.trash'),
            'routeBack' => route('banner.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index', ['categoryId' => $categoryId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $categoryId)
    {
        $data['category'] = $this->bannerService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/banner.caption')
            ]),
            'routeBack' => route('banner.index', array_merge(['categoryId' => $categoryId], $request->query())),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index', ['categoryId' => $categoryId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(BannerRequest $request, $categoryId)
    {
        $data = $request->all();

        if ($request->hasFile('file_image')) {
            $data['file_image'] = $request->file('file_image');
        }

        if ($request->hasFile('file_video')) {
            $data['file_video'] = $request->file('file_video');
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        if ($request->hasFile('file_youtube')) {
            $data['file_youtube'] = $request->file('file_youtube');
        }
        
        $data['category_id'] = $categoryId;
        $data['image_type'] = $request->image_type ?? null;
        $data['video_type'] = $request->video_type ?? null;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $banner = $this->bannerService->storeBanner($data);
        $data['query'] = $request->query();

        if ($banner['success'] == true) {
            return $this->redirectForm($data)->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function storeMultiple(BannerMultipleRequest $request, $categoryId)
    {
        $data = $request->all();

        $languages = $this->languageService->getLanguageActive($this->lang);
        foreach ($languages as $key => $value) {
            $data['title_'.$value['iso_codes']] = null;
            $data['description_'.$value['iso_codes']] = null;
        }

        $data['file'] = $request->file('file');
        $data['category_id'] = $categoryId;
        $data['publish'] = 1;
        $data['public'] = 1;
        $data['locked'] = 1;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;

        $banner = $this->bannerService->storeBannerMultiple($data);

        return $banner;
    }

    public function edit(Request $request, $categoryId, $id)
    {
        $data['category'] = $this->bannerService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['banner'] = $this->bannerService->getBanner(['id' => $id]);
        if (empty($data['banner']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/banner.caption')
            ]),
            'routeBack' => route('banner.index', array_merge(['categoryId' => $categoryId], $request->query())),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index', ['categoryId' => $categoryId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(BannerRequest $request, $categoryId, $id)
    {
        $data = $request->all();

        if ($request->hasFile('file_image')) {
            $data['file_image'] = $request->file('file_image');
        }

        if ($request->hasFile('file_video')) {
            $data['file_video'] = $request->file('file_video');
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }

        if ($request->hasFile('file_youtube')) {
            $data['file_youtube'] = $request->file('file_youtube');
        }
        
        $data['category_id'] = $categoryId;
        $data['image_type'] = $request->image_type ?? null;
        $data['video_type'] = $request->video_type ?? null;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $banner = $this->bannerService->updateBanner($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($banner['success'] == true) {
            return $this->redirectForm($data)->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function publish($categoryId, $id)
    {
        $banner = $this->bannerService->statusBanner('publish', ['id' => $id]);

        if ($banner['success'] == true) {
            return back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function approved($categoryId, $id)
    {
        $banner = $this->bannerService->statusBanner('approved', ['id' => $id]);

        if ($banner['success'] == true) {
            return back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function position(Request $request, $categoryId, $id, $position)
    {
        $banner = $this->bannerService->positionBanner(['id' => $id], $position);

        if ($banner['success'] == true) {
            return back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    public function softDelete($categoryId, $id)
    {
        $banner = $this->bannerService->trashBanner(['id' => $id]);

        return $banner;
    }

    public function permanentDelete(Request $request, $categoryId, $id)
    {
        $banner = $this->bannerService->deleteBanner($request, ['id' => $id]);

        return $banner;
    }

    public function restore($categoryId, $id)
    {
        $banner = $this->bannerService->restoreBanner(['id' => $id]);

        if ($banner['success'] == true) {
            return redirect()->back()->with('success', $banner['message']);
        }

        return redirect()->back()->with('failed', $banner['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('banner.index', array_merge(['categoryId' => $data['category_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
