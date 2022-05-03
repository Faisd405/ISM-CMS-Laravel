<?php

namespace App\Http\Controllers\Module\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Banner\BannerCategoryRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\BannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerCategoryController extends Controller
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

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['categories'] = $this->bannerService->getCategoryList($filter, true, 10, false);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.banners.category.index', compact('data'), [
            'title' => __('module/banner.category.title'),
            'breadcrumbs' => [
                __('module/banner.category.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['categories'] = $this->bannerService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.banners.category.trash', compact('data'), [
            'title' => __('module/banner.category.title').' - '.__('global.trash'),
            'routeBack' => route('banner.category.index'),
            'breadcrumbs' => [
                __('module/banner.category.caption') => route('banner.category.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.category.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/banner.category.caption')
            ]),
            'routeBack' => route('banner.category.index'),
            'breadcrumbs' => [
                __('module/banner.category.caption') => route('banner.category.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(BannerCategoryRequest $request)
    {
        $data = $request->all();
        $data['hide_description'] = (bool)$request->hide_description;
        $bannerCategory = $this->bannerService->storeCategory($data);

        if ($bannerCategory['success'] == true) {
            return $this->redirectForm($data)->with('success', $bannerCategory['message']);
        }

        return redirect()->back()->with('failed', $bannerCategory['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['category'] = $this->bannerService->getCategory(['id' => $id]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.category.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/banner.category.caption')
            ]),
            'routeBack' => route('banner.category.index'),
            'breadcrumbs' => [
                __('module/banner.category.caption') => route('banner.category.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(BannerCategoryRequest $request, $id)
    {
        $data = $request->all();
        $data['hide_description'] = (bool)$request->hide_description;
        $bannerCategory = $this->bannerService->updateCategory($data, ['id' => $id]);

        if ($bannerCategory['success'] == true) {
            return $this->redirectForm($data)->with('success', $bannerCategory['message']);
        }

        return redirect()->back()->with('failed', $bannerCategory['message']);
    }

    public function publish($id)
    {
        $bannerCategory = $this->bannerService->statusCategory('publish', ['id' => $id]);

        if ($bannerCategory['success'] == true) {
            return back()->with('success', $bannerCategory['message']);
        }

        return redirect()->back()->with('failed', $bannerCategory['message']);
    }

    public function approved($id)
    {
        $bannerCategory = $this->bannerService->statusCategory('approved', ['id' => $id]);

        if ($bannerCategory['success'] == true) {
            return back()->with('success', $bannerCategory['message']);
        }

        return redirect()->back()->with('failed', $bannerCategory['message']);
    }

    public function softDelete($id)
    {
        $bannerCategory = $this->bannerService->trashCategory(['id' => $id]);

        return $bannerCategory;
    }

    public function permanentDelete(Request $request, $id)
    {
        $bannerCategory = $this->bannerService->deleteCategory($request, ['id' => $id]);

        return $bannerCategory;
    }

    public function restore($id)
    {
        $bannerCategory = $this->bannerService->restoreCategory(['id' => $id]);

        if ($bannerCategory['success'] == true) {
            return redirect()->back()->with('success', $bannerCategory['message']);
        }

        return redirect()->back()->with('failed', $bannerCategory['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('banner.category.index');
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
