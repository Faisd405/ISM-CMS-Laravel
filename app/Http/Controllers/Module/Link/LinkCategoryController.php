<?php

namespace App\Http\Controllers\Module\Link;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Link\LinkCategoryRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Master\TemplateService;
use App\Services\Module\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LinkCategoryController extends Controller
{
    private $linkService, $languageService, $templateService, $configService;

    public function __construct(
        LinkService $linkService,
        LanguageService $languageService,
        TemplateService $templateService,
        ConfigurationService $configService
    )
    {
        $this->linkService = $linkService;
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

        $data['categories'] = $this->linkService->getCategoryList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.links.category.index', compact('data'), [
            'title' => __('module/link.category.title'),
            'breadcrumbs' => [
                __('module/link.caption') => 'javascript:;',
                __('module/link.category.caption') => '',
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

        $data['categories'] = $this->linkService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.links.category.trash', compact('data'), [
            'title' => __('module/link.category.title').' - '.__('global.trash'),
            'routeBack' => route('link.category.index'),
            'breadcrumbs' => [
                __('module/link.caption') => 'javascript:;',
                __('module/link.category.caption') => route('link.category.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'link_category'], false);

        return view('backend.links.category.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/link.category.caption')
            ]),
            'routeBack' => route('link.category.index'),
            'breadcrumbs' => [
                __('module/link.caption') => 'javascript:;',
                __('module/link.category.caption') => route('link.category.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(LinkCategoryRequest $request)
    {
        $data = $request->all();
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $category = $this->linkService->storeCategory($data);

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['category'] = $this->linkService->getCategory(['id' => $id]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'link_category'], false);

        return view('backend.links.category.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/link.category.caption')
            ]),
            'routeBack' => route('link.category.index'),
            'breadcrumbs' => [
                __('module/link.caption') => 'javascript:;',
                __('module/link.category.caption') => route('link.category.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(LinkCategoryRequest $request, $id)
    {
        $data = $request->all();
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $category = $this->linkService->updateCategory($data, ['id' => $id]);

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function publish($id)
    {
        $category = $this->linkService->statusCategory('publish', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function approved($id)
    {
        $category = $this->linkService->statusCategory('approved', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function position(Request $request, $id, $position)
    {
        $category = $this->linkService->positionCategory(['id' => $id], $position);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function softDelete($id)
    {
        $category = $this->linkService->trashCategory(['id' => $id]);

        return $category;
    }

    public function permanentDelete(Request $request, $id)
    {
        $category = $this->linkService->deleteCategory($request, ['id' => $id]);

        return $category;
    }

    public function restore($id)
    {
        $category = $this->linkService->restoreCategory(['id' => $id]);

        if ($category['success'] == true) {
            return redirect()->back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('link.category.index');
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
        //data
        $data['banner'] = $this->configService->getConfigFile('banner_default');
        $limit = $this->configService->getConfigValue('content_limit');
        $data['categories'] = $this->linkService->getCategoryList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['medias'] = $this->linkService->getMediaList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);

        return view('frontend.links.list', compact('data'), [
            'title' => __('module/link.caption'),
            'breadcrumbs' => [
                __('module/link.caption') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slugCategory');

        $data['read'] = $this->linkService->getCategory(['slug' => $slug]);

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

        $this->linkService->recordCategoryHits(['id' => $data['read']['id']]);

        //limit
        $mediaPerpage = $this->configService->getConfigValue('content_limit');
        if ($data['read']['media_perpage'] > 0) {
            $mediaPerpage = $data['read']['media_perpage'];
        }

        //data
        $data['medias'] = $this->linkService->getMediaList([
            'link_category_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1
        ], true, $mediaPerpage, false, [], [
            'position' => 'ASC'
        ]);

        $data['fields'] = $data['read']['custom_fields'];

        $data['creator'] = $data['read']['createBy']['name'];
        $data['banner'] = $data['read']->bannerSrc();

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');

        $data['meta_description'] = $this->configService->getConfigValue('meta_description');
        if (!empty($data['read']->fieldLang('description'))) {
            $data['meta_description'] = Str::limit(strip_tags($data['read']->fieldLang('description')), 155);
        }

        $blade = 'detail';
        if (!empty($data['read']['template_id'])) {
            $blade = 'custom.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        }

        return view('frontend.links.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}