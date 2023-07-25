<?php

namespace App\Http\Controllers\Module\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Document\DocumentRequest;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\Master\TemplateRepository;
use App\Repositories\Module\DocumentRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private $documentService, $languageService, $templateService,
        $userService;

    public function __construct(
        DocumentRepository $documentService,
        LanguageRepository $languageService,
        TemplateRepository $templateService,
        UserRepository $userService
    )
    {
        $this->documentService = $documentService;
        $this->languageService = $languageService;
        $this->templateService = $templateService;
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

        $data['documents'] = $this->documentService->getDocumentList($filter, true, 10, false, [],
            config('cms.module.document.ordering'));
        $data['no'] = $data['documents']->firstItem();
        $data['documents']->withQueryString();

        return view('backend.documents.index', compact('data'), [
            'title' => __('module/document.title'),
            'breadcrumbs' => [
                __('module/document.caption') => '',
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

        $data['documents'] = $this->documentService->getDocumentList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['documents']->firstItem();
        $data['documents']->withQueryString();

        return view('backend.documents.trash', compact('data'), [
            'title' => __('module/document.title').' - '.__('global.trash'),
            'routeBack' => route('document.index'),
            'breadcrumbs' => [
                __('module/document.caption') => route('document.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'document'], false, 0);
        $data['roles'] = $this->userService->getRoleList(['role_not' => [1, 2, 3, 4, 5]], false, 0);

        return view('backend.documents.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/document.caption')
            ]),
            'routeBack' => route('document.index', $request->query()),
            'breadcrumbs' => [
                __('module/document.caption') => route('document.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(DocumentRequest $request)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_file'] = (bool)$request->config_paginate_file;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $document = $this->documentService->storeDocument($data);
        $data['query'] = $request->query();

        if ($document['success'] == true) {
            return $this->redirectForm($data)->with('success', $document['message']);
        }

        return redirect()->back()->with('failed', $document['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['document'] = $this->documentService->getDocument(['id' => $id]);
        if (empty($data['document']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);
        $data['templates'] = $this->templateService->getTemplateList(['type' => 0, 'module' => 'document'], false, 0);
        $data['roles'] = $this->userService->getRoleList(['role_not' => [1, 2, 3, 4, 5]], false, 0);

        return view('backend.documents.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/document.caption')
            ]),
            'routeBack' => route('document.index', $request->query()),
            'breadcrumbs' => [
                __('module/document.caption') => route('document.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(DocumentRequest $request, $id)
    {
        $data = $request->all();
        $data['detail'] = (bool)$request->detail;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_banner'] = (bool)$request->config_show_banner;
        $data['config_paginate_file'] = (bool)$request->config_paginate_file;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $document = $this->documentService->updateDocument($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($document['success'] == true) {
            return $this->redirectForm($data)->with('success', $document['message']);
        }

        return redirect()->back()->with('failed', $document['message']);
    }

    public function publish($id)
    {
        $document = $this->documentService->statusDocument('publish', ['id' => $id]);

        if ($document['success'] == true) {
            return back()->with('success', $document['message']);
        }

        return redirect()->back()->with('failed', $document['message']);
    }

    public function approved($id)
    {
        $document = $this->documentService->statusDocument('approved', ['id' => $id]);

        if ($document['success'] == true) {
            return back()->with('success', $document['message']);
        }

        return redirect()->back()->with('failed', $document['message']);
    }

    public function sort(Request $request)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->documentService->sortDocument(['id' => $value], $i);
        }
    }

    public function position(Request $request, $id, $position)
    {
        $document = $this->documentService->positionDocument(['id' => $id], $position);

        if ($document['success'] == true) {
            return back()->with('success', $document['message']);
        }

        return redirect()->back()->with('failed', $document['message']);
    }

    public function softDelete($id)
    {
        $document = $this->documentService->trashDocument(['id' => $id]);

        return $document;
    }

    public function permanentDelete(Request $request, $id)
    {
        $document = $this->documentService->deleteDocument($request, ['id' => $id]);

        return $document;
    }

    public function restore($id)
    {
        $document = $this->documentService->restoreDocument(['id' => $id]);

        if ($document['success'] == true) {
            return redirect()->back()->with('success', $document['message']);
        }

        return redirect()->back()->with('failed', $document['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('document.index', $data['query']);
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
        if (config('cms.module.document.list_view') == false)
            return redirect()->route('home');

        //data
        $data['banner'] = config('cmsConfig.file.banner_default');
        $limit = config('cmsConfig.general.content_limit');

        // category
        $data['categories'] = $this->documentService->getDocumentList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [],
            config('cms.module.document.ordering'));
        $data['no_categories'] = $data['categories']->firstItem();
        $data['categories']->withQueryString();

        // file
        $data['files'] = $this->documentService->getFileList([
            'publish' => 1,
            'approved' => 1
        ], true, $limit, false, [], [
            'position' => 'ASC'
        ]);
        $data['no_files'] = $data['files']->firstItem();
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
        $slug = $request->route('slugDocument');

        $data['read'] = $this->documentService->getDocument(['slug' => $slug]);

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

        $filter['document_id'] = $data['read']['id'];
        $filter['publish'] = 1;
        $filter['approved'] = 1;

        //data
        $data['files'] = $this->documentService->getFileList($filter,
            $data['read']['config']['paginate_file'], $data['read']['config']['file_limit'], false,
        [], [$data['read']['config']['file_order_by'] => $data['read']['config']['file_order_type']]);
        if ($data['read']['config']['paginate_file'] == true) {
            $data['no_files'] = $data['files']->firstItem();
            $data['files']->withQueryString();
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

        // record hits
        $this->documentService->recordDocumentHits(['id' => $data['read']['id']]);

        return view('frontend.documents.'.$blade, compact('data'), [
            'title' => $data['read']->fieldLang('name'),
            'breadcrumbs' => [
                $data['read']->fieldLang('name') => ''
            ],
        ]);
    }
}
