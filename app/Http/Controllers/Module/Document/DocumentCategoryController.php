<?php

namespace App\Http\Controllers\Module\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Document\DocumentCategoryRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Master\TemplateService;
use App\Services\Module\DocumentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DocumentCategoryController extends Controller
{
    private $documentService, $languageService, $templateService, $configService,
        $userService;

    public function __construct(
        DocumentService $documentService,
        LanguageService $languageService,
        TemplateService $templateService,
        ConfigurationService $configService,
        UserService $userService
    )
    {
        $this->documentService = $documentService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;
        $this->configService = $configService;
        $this->userService = $userService;

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

        $data['categories'] = $this->documentService->getCategoryList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        return view('backend.documents.category.index', compact('data'), [
            'title' => __('module/document.category.title'),
            'breadcrumbs' => [
                __('module/document.caption') => 'javascript:;',
                __('module/document.category.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request)
    {
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['categories'] = $this->documentService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        return view('backend.documents.category.trash', compact('data'), [
            'title' => __('module/document.category.title').' - '.__('global.trash'),
            'routeBack' => route('document.category.index'),
            'breadcrumbs' => [
                __('module/document.caption') => 'javascript:;',
                __('module/document.category.caption') => route('document.category.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'document_category'], false, 0);
        $data['roles'] = $this->userService->getRoleList(['role_not' => [1, 2, 3, 4]], false, 0);

        return view('backend.documents.category.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/document.category.caption')
            ]),
            'routeBack' => route('document.category.index', $request->query()),
            'breadcrumbs' => [
                __('module/document.caption') => 'javascript:;',
                __('module/document.category.caption') => route('document.category.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(DocumentCategoryRequest $request)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $category = $this->documentService->storeCategory($data);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['category'] = $this->documentService->getCategory(['id' => $id]);
        if (empty($data['category']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'document_category'], false, 0);
        $data['roles'] = $this->userService->getRoleList(['role_not' => [1, 2, 3, 4]], false, 0);

        return view('backend.documents.category.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/document.category.caption')
            ]),
            'routeBack' => route('document.category.index', $request->query()),
            'breadcrumbs' => [
                __('module/document.caption') => 'javascript:;',
                __('module/document.category.caption') => route('document.category.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(DocumentCategoryRequest $request, $id)
    {
        $data = $request->all();
        $data['is_detail'] = (bool)$request->is_detail;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_banner'] = (bool)$request->hide_banner;
        $category = $this->documentService->updateCategory($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($category['success'] == true) {
            return $this->redirectForm($data)->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function publish($id)
    {
        $category = $this->documentService->statusCategory('publish', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function approved($id)
    {
        $category = $this->documentService->statusCategory('approved', ['id' => $id]);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function position(Request $request, $id, $position)
    {
        $category = $this->documentService->positionCategory(['id' => $id], $position);

        if ($category['success'] == true) {
            return back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    public function softDelete($id)
    {
        $category = $this->documentService->trashCategory(['id' => $id]);

        return $category;
    }

    public function permanentDelete(Request $request, $id)
    {
        $category = $this->documentService->deleteCategory($request, ['id' => $id]);

        return $category;
    }

    public function restore($id)
    {
        $category = $this->documentService->restoreCategory(['id' => $id]);

        if ($category['success'] == true) {
            return redirect()->back()->with('success', $category['message']);
        }

        return redirect()->back()->with('failed', $category['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('document.category.index', $data['query']);
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
        $data['categories'] = $this->documentService->getCategoryList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['cat_no'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        $data['files'] = $this->documentService->getFileList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['file_no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('frontend.documents.list', compact('data'), [
            'title' => __('module/document.caption'),
            'breadcrumbs' => [
                __('module/document.caption') => '',
            ],
        ]);
    }

    public function read(Request $request)
    {
        $slug = $request->route('slugCategory');

        $data['read'] = $this->documentService->getCategory(['slug' => $slug]);

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

        $this->documentService->recordCategoryHits(['id' => $data['read']['id']]);

        //limit
        $filePerpage = $this->configService->getConfigValue('content_limit');
        if ($data['read']['file_perpage'] > 0) {
            $filePerpage = $data['read']['file_perpage'];
        }

        //data
        $data['files'] = $this->documentService->getFileList([
            'document_category_id' => $data['read']['id'],
            'publish' => 1,
            'approved' => 1
        ], true, $filePerpage, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        $data['fields'] = $data['read']['custom_fields'];

        $data['creator'] = $data['read']['createBy']['name'];
        $data['banner'] = $data['read']->bannerSrc();

        // meta data
        $data['meta_title'] = $data['read']->fieldLang('name');

        $data['meta_description'] = $this->configService->getConfigValue('meta_description');
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
            $this->configService->getConfigFile('cover_default')."&url=".URL::full()."&is_video=false&description=".$data['read']->fieldLang('name')."";

        $blade = 'detail';
        if (!empty($data['read']['template_id'])) {
            $blade = 'custom.'.Str::replace('.blade.php', '', $data['read']['template']['filename']);
        }

        return view('frontend.documents.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
