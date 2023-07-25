<?php

namespace App\Http\Controllers\Module\Link;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Link\LinkRequest;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\Master\TemplateRepository;
use App\Repositories\Module\LinkRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    private $linkService, $languageService, $templateService;

    public function __construct(
        LinkRepository $linkService,
        LanguageRepository $languageService,
        TemplateRepository $templateService
    )
    {
        $this->linkService = $linkService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;

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

        $data['links'] = $this->linkService->getLinkList($filter, true, 10, false, [],
            config('cms.module.link.ordering'));
        $data['no'] = $data['links']->firstItem();
        $data['links']->withQueryString();

        return view('backend.links.index', compact('data'), [
            'title' => __('module/link.title'),
            'breadcrumbs' => [
                __('module/link.caption') => '',
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

        $data['links'] = $this->linkService->getLinkList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['links']->firstItem();
        $data['links']->withQueryString();

        return view('backend.links.trash', compact('data'), [
            'title' => __('module/link.title').' - '.__('global.trash'),
            'routeBack' => route('link.index'),
            'breadcrumbs' => [
                __('module/link.caption') => route('link.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'link'], false, 0);

        return view('backend.links.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/link.caption')
            ]),
            'routeBack' => route('link.index', $request->query()),
            'breadcrumbs' => [
                __('module/link.caption') => route('link.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(LinkRequest $request)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_media'] = (bool)$request->config_paginate_media;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $link = $this->linkService->storeLink($data);
        $data['query'] = $request->query();

        if ($link['success'] == true) {
            return $this->redirectForm($data)->with('success', $link['message']);
        }

        return redirect()->back()->with('failed', $link['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['link'] = $this->linkService->getLink(['id' => $id]);
        if (empty($data['link']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'link'], false, 0);

        return view('backend.links.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/link.caption')
            ]),
            'routeBack' => route('link.index', $request->query()),
            'breadcrumbs' => [
                __('module/link.caption') => route('link.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(LinkRequest $request, $id)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_media'] = (bool)$request->config_paginate_media;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $link = $this->linkService->updateLink($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($link['success'] == true) {
            return $this->redirectForm($data)->with('success', $link['message']);
        }

        return redirect()->back()->with('failed', $link['message']);
    }

    public function publish($id)
    {
        $link = $this->linkService->statusLink('publish', ['id' => $id]);

        if ($link['success'] == true) {
            return back()->with('success', $link['message']);
        }

        return redirect()->back()->with('failed', $link['message']);
    }

    public function approved($id)
    {
        $link = $this->linkService->statusLink('approved', ['id' => $id]);

        if ($link['success'] == true) {
            return back()->with('success', $link['message']);
        }

        return redirect()->back()->with('failed', $link['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->linkService->sortLink(['id' => $value], $i);
        }
    }

    public function position(Request $request, $id, $position)
    {
        $link = $this->linkService->positionLink(['id' => $id], $position);

        if ($link['success'] == true) {
            return back()->with('success', $link['message']);
        }

        return redirect()->back()->with('failed', $link['message']);
    }

    public function softDelete($id)
    {
        $link = $this->linkService->trashLink(['id' => $id]);

        return $link;
    }

    public function permanentDelete(Request $request, $id)
    {
        $link = $this->linkService->deleteLink($request, ['id' => $id]);

        return $link;
    }

    public function restore($id)
    {
        $link = $this->linkService->restoreLink(['id' => $id]);

        if ($link['success'] == true) {
            return redirect()->back()->with('success', $link['message']);
        }

        return redirect()->back()->with('failed', $link['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('link.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    /**
     * frontend
     */
    public function list(Request $request)
    {
        if (config('cms.module.link.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = config('cmsConfig.file.banner_default');
        $limit = config('cmsConfig.general.content_limit');

        // link
        $data['links'] = $this->linkService->getLinkList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [],
            config('cms.module.link.ordering'));
        $data['no_links'] = $data['links']->firstItem();
        $data['links']->withQueryString();

        // media
        $data['medias'] = $this->linkService->getMediaList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['no_medias'] = $data['medias']->firstItem();
        $data['medias']->withQueryString();

        return view('frontend.links.list', compact('data'), [
            'title' => __('module/link.caption'),
            'breadcrumbs' => [
                __('module/link.caption') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slug');

        $data['read'] = $this->linkService->getLink(['slug' => $slug]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 || $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        if ($data['read']['detail'] == 0) {
            return redirect()->route('home');
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

       // filtring
       $keyword = $request->input('keyword', '');
       if ($keyword != '') {
           $filter['q'] = $keyword;
       }

       $filter['link_id'] = $data['read']['id'];
       $filter['publish'] = 1;
       $filter['approved'] = 1;

       //data
       $data['medias'] = $this->linkService->getMediaList($filter,
           $data['read']['config']['paginate_media'], $data['read']['config']['media_limit'], false,
       [], [$data['read']['config']['media_order_by'] => $data['read']['config']['media_order_type']]);
       if ($data['read']['config']['paginate_media'] == true) {
           $data['no_medias'] = $data['medias']->firstItem();
           $data['medias']->withQueryString();
       }

        $data['fields'] = $data['read']['custom_fields'];
        $data['creator'] = $data['read']['createBy']['name'];
        $data['cover'] = $data['read']['cover_src'];
        $data['banner'] = $data['read']['banner_src'];

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');
        $data['meta_description'] = config('cmsConfig.seo.meta_description');
        if (!empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        //share
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".URL::full().
            "&title=".$data['read']->fieldLang('name')."";
        $data['share_twitter'] = 'https://twitter.com/intent/tweet?text='.
            str_replace('#', '', $data['read']->fieldLang('name')).'&url='.URL::full();
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('name').
            " ".URL::full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            URL::full()."&title=".$data['read']->fieldLang('name')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $data['cover']."&url=".URL::full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

        $blade = 'detail';
        if (!empty($data['read']['template_id'])) {
            $blade = 'custom.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        }

        $this->linkService->recordLinkHits(['id' => $data['read']['id']]);

        return view('frontend.links.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
