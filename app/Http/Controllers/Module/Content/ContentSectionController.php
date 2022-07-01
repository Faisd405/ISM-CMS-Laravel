<?php

namespace App\Http\Controllers\Module\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Content\ContentSectionRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Master\TemplateService;
use App\Services\Module\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContentSectionController extends Controller
{
    private $contentService, $languageService, $templateService, $configService;

    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        TemplateService $templateService,
        ConfigurationService $configService
    )
    {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;
        $this->configService = $configService;

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

        $data['sections'] = $this->contentService->getSectionList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['sections']->firstItem();
        $data['sections']->withPath(url()->current().$param);

        return view('backend.contents.section.index', compact('data'), [
            'title' => __('module/content.section.title'),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.section.caption') => '',
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

        $data['sections'] = $this->contentService->getSectionList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['sections']->firstItem();
        $data['sections']->withPath(url()->current().$param);

        return view('backend.contents.section.trash', compact('data'), [
            'title' => __('module/content.section.title').' - '.__('global.trash'),
            'routeBack' => route('content.section.index'),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.section.caption') => route('content.section.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['template_lists'] = $this->templateService->getTemplateList(['type' => 1, 'module' => 'content_section'], false, 0);
        $data['template_details'] = $this->templateService->getTemplateList(['type' => 2, 'module' => 'content_section'], false, 0);

        return view('backend.contents.section.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/content.section.caption')
            ]),
            'routeBack' => route('content.section.index', $request->query()),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.section.caption') => route('content.section.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(ContentSectionRequest $request)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $section = $this->contentService->storeSection($data);
        $data['query'] = $request->query();

        if ($section['success'] == true) {
            return $this->redirectForm($data)->with('success', $section['message']);
        }

        return redirect()->back()->with('failed', $section['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['section'] = $this->contentService->getSection(['id' => $id]);
        if (empty($data['section']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['template_lists'] = $this->templateService->getTemplateList(['type' => 1, 'module' => 'content_section'], false, 0);
        $data['template_details'] = $this->templateService->getTemplateList(['type' => 2, 'module' => 'content_section'], false, 0);

        return view('backend.contents.section.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/content.section.caption')
            ]),
            'routeBack' => route('content.section.index', $request->query()),
            'breadcrumbs' => [
                __('module/content.caption') => 'javascript:;',
                __('module/content.section.caption') => route('content.section.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(ContentSectionRequest $request, $id)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $section = $this->contentService->updateSection($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($section['success'] == true) {
            return $this->redirectForm($data)->with('success', $section['message']);
        }

        return redirect()->back()->with('failed', $section['message']);
    }

    public function publish($id)
    {
        $section = $this->contentService->statusSection('publish', ['id' => $id]);

        if ($section['success'] == true) {
            return back()->with('success', $section['message']);
        }

        return redirect()->back()->with('failed', $section['message']);
    }

    public function approved($id)
    {
        $section = $this->contentService->statusSection('approved', ['id' => $id]);

        if ($section['success'] == true) {
            return back()->with('success', $section['message']);
        }

        return redirect()->back()->with('failed', $section['message']);
    }

    public function position(Request $request, $id, $position)
    {
        $section = $this->contentService->positionSection(['id' => $id], $position);

        if ($section['success'] == true) {
            return back()->with('success', $section['message']);
        }

        return redirect()->back()->with('failed', $section['message']);
    }

    public function softDelete($id)
    {
        $section = $this->contentService->trashSection(['id' => $id]);

        return $section;
    }

    public function permanentDelete(Request $request, $id)
    {
        $section = $this->contentService->deleteSection($request, ['id' => $id]);

        return $section;
    }

    public function restore($id)
    {
        $section = $this->contentService->restoreSection(['id' => $id]);

        if ($section['success'] == true) {
            return redirect()->back()->with('success', $section['message']);
        }

        return redirect()->back()->with('failed', $section['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('content.section.index', $data['query']);
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
        if (config('cms.module.content.section.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = $this->configService->getConfigFile('banner_default');
        $limit = $this->configService->getConfigValue('content_limit');
        $data['sections'] = $this->contentService->getSectionList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);

        return view('frontend.contents.section.list', compact('data'), [
            'title' => __('module/content.section.title'),
            'breadcrumbs' => [
                __('module/content.section.title') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slug');

        $data['read'] = $this->contentService->getSection(['slug' => $slug]);

        //check
        if (empty($data['read']) || $data['read']['publish'] == 0 || $data['read']['approved'] != 1) {
            return redirect()->route('home');
        }

        if ($data['read']['config']['is_detail'] == 0) {
            return redirect()->route('home');
        }

        if ($data['read']['public'] == 0 && Auth::guard()->check() == false) {
            return redirect()->route('login.frontend')->with('warning', __('auth.login_request'));
        }

        $this->contentService->recordSectionHits(['id' => $data['read']['id']]);

        //limit
        $catPerpage = $this->configService->getConfigValue('content_limit');
        $postPerpage = $this->configService->getConfigValue('content_limit');
        if ($data['read']['category_perpage'] > 0) {
            $catPerpage = $data['read']['category_perpage'];
        }
        if ($data['read']['post_perpage'] > 0) {
            $postPerpage = $data['read']['post_perpage'];
        }

        //data
        $data['categories'] = $this->contentService->getCategoryList([
            'section_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1
        ], true, $catPerpage, false, [], [
            'position' => 'ASC'
        ]);

        $data['posts'] = $this->contentService->getPostList([
            'section_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1
        ], true, $postPerpage, false, [], [
            $data['read']['ordering']['order_by'] => $data['read']['ordering']['order_seq']
        ]);

        $data['fields'] = $data['read']['custom_fields'];

        $data['creator'] = $data['read']['createBy']['name'];
        $data['banner'] = $data['read']->bannerSrc();

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');
        if (!empty($data['read']['seo']['title'])) {
            $data['meta_title'] = Str::limit(strip_tags($data['read']['seo']['title']), 69);
        }

        $data['meta_description'] = $this->configService->getConfigValue('meta_description');
        if (!empty($data['read']['seo']['description'])) {
            $data['meta_description'] = $data['read']['seo']['description'];
        } elseif (empty($data['read']['seo']['description']) && 
            !empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        $data['meta_keywords'] = $this->configService->getConfigValue('meta_keywords');
        if (!empty($data['read']['seo']['keywords'])) {
            $data['meta_keywords'] = $data['read']['seo']['keywords'];
        }

        //share
        $data['share_facebook'] = "https://www.facebook.com/share.php?u=".url()->full().
            "&title=".$data['read']->fieldLang('name')."";
        $data['share_twitter'] = 'https://twitter.com/intent/tweet?text='.
            str_replace('#', '', $data['read']->fieldLang('name')).'&url='.url()->full();
        $data['share_whatsapp'] = "whatsapp://send?text=".$data['read']->fieldLang('name').
            " ".url()->full()."";
        $data['share_linkedin'] = "https://www.linkedin.com/shareArticle?mini=true&url=".
            url()->full()."&title=".$data['read']->fieldLang('name')."&source=".request()->root()."";
        $data['share_pinterest'] = "https://pinterest.com/pin/create/bookmarklet/?media=".
            $this->configService->getConfigFile('cover_default')."&url=".url()->full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

        $blade = 'detail';
        if (!empty($data['read']['template_list_id'])) {
            $blade = 'list.'.Str::replace('.blade.php', '', $data['read']['templateList']['filename']);
        }

        return view('frontend.contents.section.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
