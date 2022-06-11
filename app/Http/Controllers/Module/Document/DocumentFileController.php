<?php

namespace App\Http\Controllers\Module\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Document\DocumentFileMultipleRequest;
use App\Http\Requests\Module\Document\DocumentFileRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentFileController extends Controller
{
    private $documentService, $languageService;

    public function __construct(
        DocumentService $documentService,
        LanguageService $languageService
    )
    {
        $this->documentService = $documentService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $categoryId)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['document_category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['category'] = $this->documentService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['files'] = $this->documentService->getFileList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withPath(url()->current().$param);

        return view('backend.documents.file.index', compact('data'), [
            'title' => __('module/document.file.title'),
            'routeBack' => route('document.category.index'),
            'breadcrumbs' => [
                __('module/document.caption') => 'javascript:;',
                __('module/document.category.caption') => route('document.category.index'),
                __('module/document.file.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $categoryId)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['document_category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['category'] = $this->documentService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['files'] = $this->documentService->getFileList($filter, true, 10, true, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withPath(url()->current().$param);

        return view('backend.documents.file.trash', compact('data'), [
            'title' => __('module/document.file.title').' - '.__('global.trash'),
            'routeBack' => route('document.file.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/document.caption') => 'javascript:;',
                __('module/document.file.caption') => route('document.file.index', ['categoryId' => $categoryId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $categoryId)
    {
        $data['category'] = $this->documentService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.documents.file.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/document.file.caption')
            ]),
            'routeBack' => route('document.file.index', array_merge(['categoryId' => $categoryId], $request->query())),
            'breadcrumbs' => [
                __('module/document.file.caption') => route('document.file.index', ['categoryId' => $categoryId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(DocumentFileRequest $request, $categoryId)
    {
        $data = $request->all();

        if ($request->hasFile('file_document')) {
            $data['file_document'] = $request->file('file_document');
        }
        
        $data['document_category_id'] = $categoryId;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $documentFile = $this->documentService->storeFile($data);
        $data['query'] = $request->query();

        if ($documentFile['success'] == true) {
            return $this->redirectForm($data)->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function storeMultiple(DocumentFileMultipleRequest $request, $categoryId)
    {
        $data = $request->all();

        $languages = $this->languageService->getLanguageActive($this->lang);
        foreach ($languages as $key => $value) {
            $data['title_'.$value['iso_codes']] = null;
            $data['description_'.$value['iso_codes']] = null;
        }

        $data['file'] = $request->file('file');
        $data['document_category_id'] = $categoryId;
        $data['publish'] = 1;
        $data['public'] = 1;
        $data['locked'] = 1;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $data['cover_file'] = null;
        $data['cover_title'] = null;
        $data['cover_alt'] = null;

        $documentFile = $this->documentService->storeFileMultiple($data);

        return $documentFile;
    }

    public function edit(Request $request, $categoryId, $id)
    {
        $data['category'] = $this->documentService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['file'] = $this->documentService->getFile(['id' => $id]);
        if (empty($data['file']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.documents.file.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/document.file.caption')
            ]),
            'routeBack' => route('document.file.index', array_merge(['categoryId' => $categoryId], $request->query())),
            'breadcrumbs' => [
                __('module/document.file.caption') => route('document.file.index', ['categoryId' => $categoryId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(DocumentFileRequest $request, $categoryId, $id)
    {
        $data = $request->all();

        if ($request->hasFile('file_document')) {
            $data['file_document'] = $request->file('file_document');
        }
        
        $data['document_category_id'] = $categoryId;
        $data['hide_title'] = (bool)$request->hide_title;
        $data['hide_description'] = (bool)$request->hide_description;
        $data['hide_cover'] = (bool)$request->hide_cover;
        $documentFile = $this->documentService->updateFile($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($documentFile['success'] == true) {
            return $this->redirectForm($data)->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function publish($categoryId, $id)
    {
        $documentFile = $this->documentService->statusFile('publish', ['id' => $id]);

        if ($documentFile['success'] == true) {
            return back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function approved($categoryId, $id)
    {
        $documentFile = $this->documentService->statusFile('approved', ['id' => $id]);

        if ($documentFile['success'] == true) {
            return back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function position(Request $request, $categoryId, $id, $position)
    {
        $documentFile = $this->documentService->positionFile(['id' => $id], $position);

        if ($documentFile['success'] == true) {
            return back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function softDelete($categoryId, $id)
    {
        $documentFile = $this->documentService->trashFile(['id' => $id]);

        return $documentFile;
    }

    public function permanentDelete(Request $request, $categoryId, $id)
    {
        $documentFile = $this->documentService->deleteFile($request, ['id' => $id]);

        return $documentFile;
    }

    public function restore($categoryId, $id)
    {
        $documentFile = $this->documentService->restoreFile(['id' => $id]);

        if ($documentFile['success'] == true) {
            return redirect()->back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('document.file.index', array_merge(['categoryId' => $data['document_category_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    public function download($id)
    {
        $document = $this->documentService->getFile(['id' => $id]);
        $category = $document['category'];

        if (!empty($category['roles'])) {
            
            $checkRole = $this->documentService->checkRole(['id' => $category['id']], Auth::user()->hasRole()[0]['id']);

            if (Auth::guard()->check() == false && $checkRole > 0) {
                return redirect()->back()->with('warning', __('global.forbidden'));
            }
        }

        $this->documentService->recordDownloadHits(['id' => $id]);

        if ($document['type'] == '0') {
            $file = config('custom.files.document.path').$category['id'].'/'.
                $document['file'];

            return response()->download(storage_path('app/'.$file));
        }

        if ($document['type'] == '1') {

            return response()->download(storage_path('app/public/filemanager/'.
                $document['file']));
        }

        if ($document['type'] == '3') {

            return $document['file'];
        }
    }
}
