<?php

namespace App\Http\Controllers\Module\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Content\ContentPostRequest;
use App\Services\Feature\LanguageService;
use App\Services\Master\MediaService;
use App\Services\Master\TemplateService;
use App\Services\Module\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ContentPostController extends Controller
{
    private $contentService, $mediaService, $languageService, $templateService;

    public function __construct(
        ContentService $contentService,
        MediaService $mediaService,
        LanguageService $languageService,
        TemplateService $templateService
    )
    {
        $this->contentService = $contentService;
        $this->mediaService = $mediaService;
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
        if ($request->input('category_id', '') != '') {
            $filter['category_id'] = $request->input('category_id');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['posts'] = $this->contentService->getPostList($filter, true, 10, false, [], [
            $data['section']['config']['post_order_by'] => $data['section']['config']['post_order_type']
        ]);
        $data['no'] = $data['posts']->firstItem();
        $data['posts']->withQueryString();
        $data['categories'] = $this->contentService->getCategoryList([
            'section_id' => $sectionId,
            'publish' => 1,
            'approved' => 1
        ], false, 0);

        return view('backend.contents.post.index', compact('data'), [
            'title' => __('module/content.post.title'),
            'routeBack' => route('content.section.index'),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.section.caption') => route('content.section.index'),
                __('module/content.post.caption') => '',
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
        if ($request->input('category_id', '') != '') {
            $filter['category_id'] = $request->input('category_id');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['posts'] = $this->contentService->getPostList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['posts']->firstItem();
        $data['posts']->withQueryString();
        $data['categories'] = $this->contentService->getCategoryList([
            'section_id' => $sectionId,
            'publish' => 1,
            'approved' => 1
        ], false, 0);

        return view('backend.contents.post.trash', compact('data'), [
            'title' => __('module/content.post.title').' - '.__('global.trash'),
            'routeBack' => route('content.post.index', ['sectionId' => $sectionId]),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.post.caption') => route('content.post.index', ['sectionId' => $sectionId]),
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
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'content_post'], false, 0);
        $data['categories'] = $this->contentService->getCategoryList([
            'section_id' => $sectionId,
            'publish' => 1,
            'approved' => 1
        ], false, 0);

        return view('backend.contents.post.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/content.post.caption')
            ]),
            'routeBack' => route('content.post.index', array_merge(['sectionId' => $sectionId], $request->query())),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.post.caption') => route('content.post.index', ['sectionId' => $sectionId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(ContentPostRequest $request, $sectionId)
    {
        $data = $request->all();
        $data['section_id'] = $sectionId;
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_intro'] = (bool)$request->config_show_intro;
        $data['config_show_intro_banner'] = (bool)$request->config_show_intro_banner;
        $data['config_show_content'] = (bool)$request->config_show_content;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_logo_banner'] = (bool)$request->config_show_logo_banner;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_show_media'] = (bool)$request->config_show_media;
        $data['config_action_media'] = (bool)$request->config_action_media;
        $data['config_paginate_media'] = (bool)$request->config_paginate_media;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $post = $this->contentService->storePost($data);
        $data['query'] = $request->query();

        if ($post['success'] == true) {
            return $this->redirectForm($data)->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    public function edit(Request $request, $sectionId, $id)
    {
        $data['section'] = $this->contentService->getSection(['id' => $sectionId]);
        if (empty($data['section']))
            return abort(404);

        $data['post'] = $this->contentService->getPost(['id' => $id]);
        if (empty($data['post']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'content_post'], false, 0);
        $data['categories'] = $this->contentService->getCategoryList([
            'section_id' => $sectionId,
            'publish' => 1,
            'approved' => 1
        ], false, 0);

        if ($data['post']->tags()->count() > 0) {
            foreach ($data['post']->tags as $key => $value) {
                $tags[$key] = $value->tag->name;
            }

            $data['tags'] = implode(',', $tags);
        }

        return view('backend.contents.post.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/content.post.caption')
            ]),
            'routeBack' => route('content.post.index', array_merge(['sectionId' => $sectionId], $request->query())),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.post.caption') => route('content.post.index', ['sectionId' => $sectionId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(ContentPostRequest $request, $sectionId, $id)
    {
        $data = $request->all();
        $data['section_id'] = $sectionId;
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_intro'] = (bool)$request->config_show_intro;
        $data['config_show_intro_banner'] = (bool)$request->config_show_intro_banner;
        $data['config_show_content'] = (bool)$request->config_show_content;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_logo_banner'] = (bool)$request->config_show_logo_banner;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_show_media'] = (bool)$request->config_show_media;
        $data['config_action_media'] = (bool)$request->config_action_media;
        $data['config_paginate_media'] = (bool)$request->config_paginate_media;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $post = $this->contentService->updatePost($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($post['success'] == true) {
            return $this->redirectForm($data)->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    public function publish($sectionId, $id)
    {
        $post = $this->contentService->statusPost('publish', ['id' => $id]);

        if ($post['success'] == true) {
            return back()->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    public function approved($sectionId, $id)
    {
        $post = $this->contentService->statusPost('approved', ['id' => $id]);

        if ($post['success'] == true) {
            return back()->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    public function selected($sectionId, $id)
    {
        $post = $this->contentService->statusPost('selected', ['id' => $id]);

        if ($post['success'] == true) {
            return back()->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    public function sort(Request $request, $sectionId)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->contentService->sortPost(['id' => $value, 'section_id' => $sectionId], $i);
        }
    }

    public function position(Request $request, $sectionId, $id, $position)
    {
        $post = $this->contentService->positionPost(['id' => $id], $position);

        if ($post['success'] == true) {
            return back()->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    public function softDelete($sectionId, $id)
    {
        $post = $this->contentService->trashPost(['id' => $id]);

        return $post;
    }

    public function permanentDelete(Request $request, $sectionId, $id)
    {
        $post = $this->contentService->deletePost($request, ['id' => $id]);

        return $post;
    }

    public function restore($sectionId, $id)
    {
        $post = $this->contentService->restorePost(['id' => $id]);

        if ($post['success'] == true) {
            return redirect()->back()->with('success', $post['message']);
        }

        return redirect()->back()->with('failed', $post['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('content.post.index', array_merge(['sectionId' => $data['section_id']], $data['query']));
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
        if (config('cms.module.content.post.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = config('cmsConfig.file.banner_default');
        $limit = config('cmsConfig.general.content_limit');
        $data['posts'] = $this->contentService->getPostList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'created_at' => 'DESC'
        ]);
        $data['no'] = $data['posts']->firstItem();
        $data['posts']->withQueryString();

        return view('frontend.contents.post.list', compact('data'), [
            'title' => __('module/content.post.title'),
            'breadcrumbs' => [
                __('module/content.post.title') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slug');
        $slugPost = $request->route('slugPost');
        $data['section'] = $this->contentService->getSection(['slug' => $slug]);

        if (empty($data['section']) || $data['section']['publish'] == 0 || $data['section']['approved'] != 1) {
            return redirect()->route('home');
        }

        $data['read'] = $this->contentService->getPost(['slug' => $slugPost]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 && $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        $start = $data['read']['publish_time'];
        $end = $data['read']['publish_end'];
        $now = now()->format('Y-m-d H:i');

        if (!empty($end) && $now > $end->format('Y-m-d H:i'))
            return redirect()->route('home');

        if ($data['read']['detail'] == 0) {
            return redirect()->route('content.section.read.'.$data['section']['slug']);
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        //media
        $data['medias'] = $this->mediaService->getMediaList([
            'module' => 'content_post',
            'mediable_id' => $data['read']['id']
        ], $data['read']['config']['paginate_media'], $data['read']['config']['media_limit'], false, [], [
            'position' => 'ASC'
        ]);
        if ($data['read']['config']['paginate_media'] == true) {
            $data['no_medias'] = $data['medias']->firstItem();
            $data['medias']->withQueryString();
        }

        $data['latest_posts'] = $this->contentService->getLatestPost($data['read']['id'], [
            'section_id' => $data['read']['section_id'],
        ], $data['section']['config']['latest_post_limit']);

        $data['prev'] = $this->contentService->postPrevNext($data['read']['id'], [
            'section_id' => $data['read']['section_id'],
        ], 'prev');

        $data['next'] = $this->contentService->postPrevNext($data['read']['id'], [
            'section_id' => $data['read']['section_id'],
        ], 'next');

        $data['addon_fields'] = $data['read']['addon_fields'];
        $data['fields'] = $data['read']['custom_fields'];
        $data['tags'] = $data['read']->tags();
        $data['creator'] = $data['read']['createBy']['name'];
        if (!empty($data['read']['posted_by_alias'])) {
            $data['creator'] = $data['read']['posted_by_alias'];
        }
        $data['cover'] = $data['read']['cover_src'];
        $data['logo_banner'] = $data['read']['logo_banner_src'];
        $data['banner'] = $data['read']['banner_src'];

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('title');
        if (!empty($data['read']['seo']['title'])) {
            $data['meta_title'] = Str::limit(strip_tags($data['read']['seo']['title']), 69);
        }

        $data['meta_description'] = config('cmsConfig.seo.meta_description');
        if (!empty($data['read']['seo']['description'])) {
            $data['meta_description'] = $data['read']['seo']['description'];
        } elseif (empty($data['read']['seo']['description']) &&
            !empty($data['read']->fieldLang('intro'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('intro')), 155);
        } elseif (empty($data['read']['seo']['description']) &&
            empty($data['read']->fieldLang('intro')) && !empty($data['read']->fieldLang('content'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('content')), 155);
        }

        $data['meta_keywords'] = config('cmsConfig.seo.meta_keywords');
        if (!empty($data['read']['seo']['keywords'])) {
            $data['meta_keywords'] = $data['read']['seo']['keywords'];
        }

        //share
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".URL::full().
            "&title=".$data['read']->fieldLang('title')."";
        $data['share_twitter'] = 'https://twitter.com/intent/tweet?text='.
            str_replace('#', '', $data['read']->fieldLang('title')).'&url='.URL::full();
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('title').
            " ".URL::full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            URL::full()."&title=".$data['read']->fieldLang('title')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $data['cover']."&url=".URL::full()."&is_video=false&description=".$data['read']->fieldLang('title')."";

        $blade = 'post.detail';
        if (!empty($data['read']['template_id'])) {
            $blade = 'post.custom.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        } elseif (empty($data['read']['template_id']) && !empty($data['read']['section']['template_detail_post_id'])) {
            $blade = 'section.detail.'.Str::replace('.blade.php', '', $data['section']['templateDetailPost']['filename']);
        }

        // record hits
        $this->contentService->recordPostHits(['id' => $data['read']['id']]);

        return view('frontend.contents.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('title'),
            'breadcrumbs' => [
                $data['read']->fieldLang('title') => ''
            ],
        ]);
    }
}
