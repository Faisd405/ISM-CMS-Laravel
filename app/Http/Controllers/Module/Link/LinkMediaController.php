<?php

namespace App\Http\Controllers\Module\Link;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Link\LinkMediaRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkMediaController extends Controller
{
    private $linkService, $languageService;

    public function __construct(
        LinkService $linkService,
        LanguageService $languageService
    )
    {
        $this->linkService = $linkService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $categoryId)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['link_category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['medias'] = $this->linkService->getMediaList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['medias']->firstItem();
        $data['medias']->withPath(url()->current().$param);
        $data['category'] = $this->linkService->getCategory(['id' => $categoryId]);

        return view('backend.links.media.index', compact('data'), [
            'title' => __('module/link.media.title'),
            'routeBack' => route('link.category.index'),
            'breadcrumbs' => [
                __('module/link.caption') => 'javascript:;',
                __('module/link.category.caption') => route('link.category.index'),
                __('module/link.media.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $categoryId)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['link_category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['medias'] = $this->linkService->getMediaList($filter, true, 10, true, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['medias']->firstItem();
        $data['medias']->withPath(url()->current().$param);
        $data['category'] = $this->linkService->getCategory(['id' => $categoryId]);

        return view('backend.links.media.trash', compact('data'), [
            'title' => __('module/link.media.title').' - '.__('global.trash'),
            'routeBack' => route('link.media.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/link.caption') => 'javascript:;',
                __('module/link.media.caption') => route('link.media.index', ['categoryId' => $categoryId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $categoryId)
    {
        $data['category'] = $this->linkService->getCategory(['id' => $categoryId]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.links.media.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/link.media.caption')
            ]),
            'routeBack' => route('link.media.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/link.media.caption') => route('link.media.index', ['categoryId' => $categoryId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(LinkMediaRequest $request, $categoryId)
    {
        $data = $request->all();
        
        $data['link_category_id'] = $categoryId;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $data['is_embed'] = (bool)$request->is_embed;
        $linkMedia = $this->linkService->storeMedia($data);

        if ($linkMedia['success'] == true) {
            return $this->redirectForm($data)->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function edit(Request $request, $categoryId, $id)
    {
        $data['media'] = $this->linkService->getMedia(['id' => $id]);
        $data['category'] = $this->linkService->getCategory(['id' => $categoryId]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.links.media.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/link.media.caption')
            ]),
            'routeBack' => route('link.media.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/link.media.caption') => route('link.media.index', ['categoryId' => $categoryId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(LinkMediaRequest $request, $categoryId, $id)
    {
        $data = $request->all();

        $data['link_category_id'] = $categoryId;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $data['is_embed'] = (bool)$request->is_embed;
        $linkMedia = $this->linkService->updateMedia($data, ['id' => $id]);

        if ($linkMedia['success'] == true) {
            return $this->redirectForm($data)->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function publish($categoryId, $id)
    {
        $linkMedia = $this->linkService->statusMedia('publish', ['id' => $id]);


        if ($linkMedia['success'] == true) {
            return back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function approved($categoryId, $id)
    {
        $linkMedia = $this->linkService->statusMedia('approved', ['id' => $id]);

        if ($linkMedia['success'] == true) {
            return back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function position(Request $request, $categoryId, $id, $position)
    {
        $linkMedia = $this->linkService->positionMedia(['id' => $id], $position);

        if ($linkMedia['success'] == true) {
            return back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function softDelete($categoryId, $id)
    {
        $linkMedia = $this->linkService->trashMedia(['id' => $id]);

        return $linkMedia;
    }

    public function permanentDelete(Request $request, $categoryId, $id)
    {
        $linkMedia = $this->linkService->deleteMedia($request, ['id' => $id]);

        return $linkMedia;
    }

    public function restore($categoryId, $id)
    {
        $linkMedia = $this->linkService->restoreMedia(['id' => $id]);

        if ($linkMedia['success'] == true) {
            return redirect()->back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('link.media.index', ['categoryId' => $data['link_category_id']]);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
