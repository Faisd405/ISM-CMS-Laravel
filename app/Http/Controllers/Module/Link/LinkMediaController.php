<?php

namespace App\Http\Controllers\Module\Link;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Link\LinkMediaRequest;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\Module\LinkRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkMediaController extends Controller
{
    private $linkService, $languageService;

    public function __construct(
        LinkRepository $linkService,
        LanguageRepository $languageService
    )
    {
        $this->linkService = $linkService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $linkId)
    {
        $filter['link_id'] = $linkId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['link'] = $this->linkService->getLink(['id' => $linkId]);
        if (empty($data['link']))
            return abort(404);

        $data['medias'] = $this->linkService->getMediaList($filter, true, 10, false, [], [
            $data['link']['config']['media_order_by'] => $data['link']['config']['media_order_type']
        ]);
        $data['no'] = $data['medias']->firstItem();
        $data['medias']->withQueryString();

        return view('backend.links.media.index', compact('data'), [
            'title' => __('module/link.media.title'),
            'routeBack' => route('link.index'),
            'breadcrumbs' => [
                __('module/link.caption') => route('link.index'),
                __('module/link.media.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $linkId)
    {
        $filter['link_id'] = $linkId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['link'] = $this->linkService->getLink(['id' => $linkId]);
        if (empty($data['link']))
            return abort(404);

        $data['medias'] = $this->linkService->getMediaList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['medias']->firstItem();
        $data['medias']->withQueryString();

        return view('backend.links.media.trash', compact('data'), [
            'title' => __('module/link.media.title').' - '.__('global.trash'),
            'routeBack' => route('link.media.index', ['linkId' => $linkId]),
            'breadcrumbs' => [
                __('module/link.media.caption') => route('link.media.index', ['linkId' => $linkId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $linkId)
    {
        $data['link'] = $this->linkService->getLink(['id' => $linkId]);
        if (empty($data['link']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.links.media.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/link.media.caption')
            ]),
            'routeBack' => route('link.media.index', array_merge(['linkId' => $linkId], $request->query())),
            'breadcrumbs' => [
                __('module/link.media.caption') => route('link.media.index', ['linkId' => $linkId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(LinkMediaRequest $request, $linkId)
    {
        $data = $request->all();

        $data['link_id'] = $linkId;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $data['config_is_embed'] = (bool)$request->config_is_embed;
        $linkMedia = $this->linkService->storeMedia($data);
        $data['query'] = $request->query();

        if ($linkMedia['success'] == true) {
            return $this->redirectForm($data)->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function edit(Request $request, $linkId, $id)
    {
        $data['link'] = $this->linkService->getLink(['id' => $linkId]);
        if (empty($data['link']))
            return abort(404);

        $data['media'] = $this->linkService->getMedia(['id' => $id]);
        if (empty($data['media']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.links.media.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/link.media.caption')
            ]),
            'routeBack' => route('link.media.index', array_merge(['linkId' => $linkId], $request->query())),
            'breadcrumbs' => [
                __('module/link.media.caption') => route('link.media.index', ['linkId' => $linkId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(LinkMediaRequest $request, $linkId, $id)
    {
        $data = $request->all();

        $data['link_id'] = $linkId;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $data['config_is_embed'] = (bool)$request->config_is_embed;
        $linkMedia = $this->linkService->updateMedia($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($linkMedia['success'] == true) {
            return $this->redirectForm($data)->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function publish($linkId, $id)
    {
        $linkMedia = $this->linkService->statusMedia('publish', ['id' => $id]);


        if ($linkMedia['success'] == true) {
            return back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function approved($linkId, $id)
    {
        $linkMedia = $this->linkService->statusMedia('approved', ['id' => $id]);

        if ($linkMedia['success'] == true) {
            return back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function sort(Request $request, $linkId)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->linkService->sortMedia(['id' => $value, 'link_id' => $linkId], $i);
        }
    }

    public function position(Request $request, $linkId, $id, $position)
    {
        $linkMedia = $this->linkService->positionMedia(['id' => $id], $position);

        if ($linkMedia['success'] == true) {
            return back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    public function softDelete($linkId, $id)
    {
        $linkMedia = $this->linkService->trashMedia(['id' => $id]);

        return $linkMedia;
    }

    public function permanentDelete(Request $request, $linkId, $id)
    {
        $linkMedia = $this->linkService->deleteMedia($request, ['id' => $id]);

        return $linkMedia;
    }

    public function restore($linkId, $id)
    {
        $linkMedia = $this->linkService->restoreMedia(['id' => $id]);

        if ($linkMedia['success'] == true) {
            return redirect()->back()->with('success', $linkMedia['message']);
        }

        return redirect()->back()->with('failed', $linkMedia['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('link.media.index', array_merge(['linkId' => $data['link_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
