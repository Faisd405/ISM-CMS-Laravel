<?php

namespace App\Http\Controllers\Module\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Content\ContentCategoryRequest;
use App\Services\Feature\LanguageService;
use App\Services\Master\TemplateService;
use App\Services\Module\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ContentCategoryController extends Controller
{
    private $contentService, $languageService, $templateService;

    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        TemplateService $templateService
    )
    {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $sectionId)
    {
        $filter['section_id'] = $sectionId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['categories'] = $this->contentService->getCategoryList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        return view('backend.contents.category.index', compact('data'), [
            'title' => __('module/content.category.title'),
            'routeBack' => route('content.section.index'),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.section.caption') => route('content.section.index'),
                __('module/content.category.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $sectionId)
    {
        $filter['section_id'] = $sectionId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['categories'] = $this->contentService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        return view('backend.contents.category.trash', compact('data'), [
            'title' => __('module/content.category.title').' - '.__('global.trash'),
            'routeBack' => route('content.category.index', ['sectionId' => $sectionId]),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.category.caption') => route('content.category.index', ['sectionId' => $sectionId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $sectionId)
    {
        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'content_category'], false, 0);

        return view('backend.contents.category.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/content.category.caption')
            ]),
            'routeBack' => route('content.category.index', array_merge(['sectionId' => $sectionId], $request->query())),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.category.caption') => route('content.category.index', ['sectionId' => $sectionId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(ContentCategoryRequest $request, $sectionId)
    {
        $data = $request->all();
        $data['section_id'] = $sectionId;
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_post'] = (bool)$request->config_paginate_post;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $category = $this->contentService->storeCategory($data);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function edit(Request $request, $sectionId, $id)
    {
        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['category'] = $this->contentService->getCategory(['id' => $id]);
        if (empty($data['category']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'content_category'], false, 0);

        return view('backend.contents.category.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/content.category.caption')
            ]),
            'routeBack' => route('content.category.index', array_merge(['sectionId' => $sectionId], $request->query())),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.category.caption') => route('content.category.index', ['sectionId' => $sectionId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(ContentCategoryRequest $request, $sectionId, $id)
    {
        $data = $request->all();
        $data['section_id'] = $sectionId;
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_post'] = (bool)$request->config_paginate_post;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $category = $this->contentService->updateCategory($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function publish($sectionId, $id)
    {
        $category = $this->contentService->statusCategory('publish', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function approved($sectionId, $id)
    {
        $category = $this->contentService->statusCategory('approved', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function sort(Request $request, $sectionId)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->contentService->sortCategory(['id' => $value, 'section_id' => $sectionId], $i);
        }
    }

    public function position(Request $request, $sectionId, $id, $position)
    {
        $category = $this->contentService->positionCategory(['id' => $id], $position);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function softDelete($sectionId, $id)
    {
        $category = $this->contentService->trashCategory(['id' => $id]);

        return $category;
    }

    public function permanentDelete(Request $request, $sectionId, $id)
    {
        $category = $this->contentService->deleteCategory($request, ['id' => $id]);

        return $category;
    }

    public function restore($sectionId, $id)
    {
        $category = $this->contentService->restoreCategory(['id' => $id]);

        if ($category['success'] == true) {
            return redirect()->back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('content.category.index', array_merge(['sectionId' => $data['section_id']], $data['query']));
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
        if (config('cms.module.content.category.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = config('cmsConfig.file.banner_default');
        $limit = config('cmsConfig.general.content_limit');
        $data['categories'] = $this->contentService->getCategoryList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        return view('frontend.contents.category.list', compact('data'), [
            'title' => __('module/content.category.title'),
            'breadcrumbs' => [
                __('module/content.category.title') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slug');
        $slugCat = $request->route('slugCategory');
        $data['section'] = $this->contentService->getSection(['slug' => $slug]);

        if (empty($data['section']) || $data['section']['publish'] == 0 || $data['section']['approved'] != 1) {
            return redirect()->route('home');
        }

        $data['read'] = $this->contentService->getCategory(['slug' => $slugCat]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 || $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        if ($data['read']['detail'] == 0) {
            return redirect()->route('content.section.read.'.$data['section']['slug']);
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        // filtering
        $keyword = $request->input('keyword', '');
        if ($keyword != '') {
            $filter['q'] = $keyword;
        }

        $filter['category_id'] = (string)$data['read']['id'];
        $filter['publish'] = 1;
        $filter['approved'] = 1;

        //post
        $data['posts'] = $this->contentService->getPostList($filter,
            $data['read']['config']['paginate_post'], $data['read']['config']['post_limit'], false,
        [], [$data['section']['config']['post_order_by'] => $data['section']['config']['post_order_type']]);
        if ($data['read']['config']['paginate_post'] == true) {
            $data['no_posts'] = $data['posts']->firstItem();
            $data['posts']->withQueryString();
        }

        $data['fields'] = $data['read']['custom_fields'];
        $data['creator'] = $data['read']['createBy']['name'];
        $data['cover'] = $data['read']['cover_src'];
        $data['banner'] = $data['read']['banner_src'];

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');
        if (!empty($data['read']['seo']['title'])) {
            $data['meta_title'] = Str::limit(strip_tags($data['read']['seo']['title']), 69);
        }

        $data['meta_description'] = config('cmsConfig.seo.meta_description');
        if (!empty($data['read']['seo']['description'])) {
            $data['meta_description'] = $data['read']['seo']['description'];
        } elseif (empty($data['read']['seo']['description']) && 
            !empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        $data['meta_keywords'] = config('cmsConfig.seo.meta_keywords');
        if (!empty($data['read']['seo']['keywords'])) {
            $data['meta_keywords'] = $data['read']['seo']['keywords'];
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
            $blade = 'list.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        }

        // record hits
        $this->contentService->recordCategoryHits(['id' => $data['read']['id']]);

        return view('frontend.contents.category.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
