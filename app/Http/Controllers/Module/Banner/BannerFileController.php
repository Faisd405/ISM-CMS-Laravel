<?php

namespace App\Http\Controllers\Module\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Banner\BannerFileMultipleRequest;
use App\Http\Requests\Module\Banner\BannerFileRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\BannerService;
use Illuminate\Http\Request;

class BannerFileController extends Controller
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

    public function index(Request $request, $bannerId)
    {
        $filter['banner_id'] = $bannerId;
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

        $data['banner'] = $this->bannerService->getBanner(['id' => $bannerId]);
        if (empty($data['banner']))
            return abort(404);

        $data['files'] = $this->bannerService->getFileList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('backend.banners.file.index', compact('data'), [
            'title' => __('module/banner.file.title'),
            'routeBack' => route('banner.index'),
            'breadcrumbs' => [
                __('module/banner.caption') => route('banner.index'),
                __('module/banner.file.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $bannerId)
    {
        $filter['banner_id'] = $bannerId;
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

        $data['banner'] = $this->bannerService->getBanner(['id' => $bannerId]);
        if (empty($data['banner']))
            return abort(404);

        $data['files'] = $this->bannerService->getFileList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('backend.banners.file.trash', compact('data'), [
            'title' => __('module/banner.file.title').' - '.__('global.trash'),
            'routeBack' => route('banner.file.index', ['bannerId' => $bannerId]),
            'breadcrumbs' => [
                __('module/banner.file.caption') => route('banner.file.index', ['bannerId' => $bannerId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $bannerId)
    {
        $data['banner'] = $this->bannerService->getBanner(['id' => $bannerId]);
        if (empty($data['banner']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.file.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/banner.file.caption')
            ]),
            'routeBack' => route('banner.file.index', array_merge(['bannerId' => $bannerId], $request->query())),
            'breadcrumbs' => [
                __('module/banner.file.caption') => route('banner.file.index', ['bannerId' => $bannerId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(BannerFileRequest $request, $bannerId)
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
        
        $data['banner_id'] = $bannerId;
        $data['image_type'] = $request->image_type ?? null;
        $data['video_type'] = $request->video_type ?? null;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_title'] = (bool)$request->config_show_title;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_url'] = (bool)$request->config_show_url;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $file = $this->bannerService->storeFile($data);
        $data['query'] = $request->query();

        if ($file['success'] == true) {
            return $this->redirectForm($data)->with('success', $file['message']);
        }

        return redirect()->back()->with('failed', $file['message']);
    }

    public function storeMultiple(BannerFileMultipleRequest $request, $bannerId)
    {
        $data = $request->all();

        $languages = $this->languageService->getLanguageActive($this->lang);
        foreach ($languages as $key => $value) {
            $data['title_'.$value['iso_codes']] = null;
            $data['description_'.$value['iso_codes']] = null;
        }

        $data['file'] = $request->file('file');
        $data['banner_id'] = $bannerId;
        $data['publish'] = 1;
        $data['public'] = 1;
        $data['locked'] = 0;
        $data['config_show_title'] = 1;
        $data['config_show_description'] = 1;
        $data['config_show_url'] = 1;
        $data['config_show_custom_field'] = 0;
        $file = $this->bannerService->storeFileMultiple($data);

        return $file;
    }

    public function edit(Request $request, $bannerId, $id)
    {
        $data['banner'] = $this->bannerService->getBanner(['id' => $bannerId]);
        if (empty($data['banner']))
            return abort(404);

        $data['file'] = $this->bannerService->getFile(['id' => $id]);
        if (empty($data['file']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.banners.file.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/banner.file.caption')
            ]),
            'routeBack' => route('banner.file.index', array_merge(['bannerId' => $bannerId], $request->query())),
            'breadcrumbs' => [
                __('module/banner.file.caption') => route('banner.file.index', ['bannerId' => $bannerId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(BannerFileRequest $request, $bannerId, $id)
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
        
        $data['banner_id'] = $bannerId;
        $data['image_type'] = $request->image_type ?? null;
        $data['video_type'] = $request->video_type ?? null;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_title'] = (bool)$request->config_show_title;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_url'] = (bool)$request->config_show_url;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $file = $this->bannerService->updateFile($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($file['success'] == true) {
            return $this->redirectForm($data)->with('success', $file['message']);
        }

        return redirect()->back()->with('failed', $file['message']);
    }

    public function publish($bannerId, $id)
    {
        $file = $this->bannerService->statusFile('publish', ['id' => $id]);

        if ($file['success'] == true) {
            return back()->with('success', $file['message']);
        }

        return redirect()->back()->with('failed', $file['message']);
    }

    public function approved($bannerId, $id)
    {
        $file = $this->bannerService->statusFile('approved', ['id' => $id]);

        if ($file['success'] == true) {
            return back()->with('success', $file['message']);
        }

        return redirect()->back()->with('failed', $file['message']);
    }

    public function sort(Request $request, $bannerId)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->bannerService->sortFile(['id' => $value, 'banner_id' => $bannerId], $i);
        }
    }

    public function position(Request $request, $bannerId, $id, $position)
    {
        $file = $this->bannerService->positionFile(['id' => $id], $position);

        if ($file['success'] == true) {
            return back()->with('success', $file['message']);
        }

        return redirect()->back()->with('failed', $file['message']);
    }

    public function softDelete($bannerId, $id)
    {
        $file = $this->bannerService->trashFile(['id' => $id]);

        return $file;
    }

    public function permanentDelete(Request $request, $bannerId, $id)
    {
        $file = $this->bannerService->deleteFile($request, ['id' => $id]);

        return $file;
    }

    public function restore($bannerId, $id)
    {
        $file = $this->bannerService->restoreFile(['id' => $id]);

        if ($file['success'] == true) {
            return redirect()->back()->with('success', $file['message']);
        }

        return redirect()->back()->with('failed', $file['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('banner.file.index', array_merge(['bannerId' => $data['banner_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
