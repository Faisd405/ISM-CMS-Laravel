<?php

namespace App\Http\Controllers\Module\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Document\DocumentFileMultipleRequest;
use App\Http\Requests\Module\Document\DocumentFileRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function index(Request $request, $documentId)
    {
        $filter['document_id'] = $documentId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['document'] = $this->documentService->getDocument(['id' => $documentId]);
        if (empty($data['document']))
            return abort(404);

        $data['files'] = $this->documentService->getFileList($filter, true, 10, false, [], [
            $data['document']['config']['file_order_by'] => $data['document']['config']['file_order_type']
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('backend.documents.file.index', compact('data'), [
            'title' => __('module/document.file.title'),
            'routeBack' => route('document.index'),
            'breadcrumbs' => [
                __('module/document.caption') => route('document.index'),
                __('module/document.file.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $documentId)
    {
        $filter['document_id'] = $documentId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        
        $data['document'] = $this->documentService->getDocument(['id' => $documentId]);
        if (empty($data['document']))
            return abort(404);

        $data['files'] = $this->documentService->getFileList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['files']->firstItem();
        $data['files']->withQueryString();

        return view('backend.documents.file.trash', compact('data'), [
            'title' => __('module/document.file.title').' - '.__('global.trash'),
            'routeBack' => route('document.file.index', ['documentId' => $documentId]),
            'breadcrumbs' => [
                __('module/document.file.caption') => route('document.file.index', ['documentId' => $documentId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $documentId)
    {
        $data['document'] = $this->documentService->getDocument(['id' => $documentId]);
        if (empty($data['document']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.documents.file.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/document.file.caption')
            ]),
            'routeBack' => route('document.file.index', array_merge(['documentId' => $documentId], $request->query())),
            'breadcrumbs' => [
                __('module/document.file.caption') => route('document.file.index', ['documentId' => $documentId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(DocumentFileRequest $request, $documentId)
    {
        $data = $request->all();

        if ($request->hasFile('file_document')) {
            $data['file_document'] = $request->file('file_document');
        }
        
        $data['document_id'] = $documentId;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_title'] = (bool)$request->config_show_title;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $documentFile = $this->documentService->storeFile($data);
        $data['query'] = $request->query();

        if ($documentFile['success'] == true) {
            return $this->redirectForm($data)->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function storeMultiple(DocumentFileMultipleRequest $request, $documentId)
    {
        $data = $request->all();

        $languages = $this->languageService->getLanguageActive($this->lang);
        foreach ($languages as $key => $value) {
            $data['title_'.$value['iso_codes']] = null;
            $data['description_'.$value['iso_codes']] = null;
        }

        $data['file'] = $request->file('file');
        $data['document_id'] = $documentId;
        $data['cover_file'] = null;
        $data['cover_title'] = null;
        $data['cover_alt'] = null;
        $data['publish'] = 1;
        $data['public'] = 1;
        $data['locked'] = 0;
        $data['config_show_title'] = 1;
        $data['config_show_description'] = 1;
        $data['config_show_cover'] = 1;
        $data['config_show_custom_field'] = 0;

        $documentFile = $this->documentService->storeFileMultiple($data);

        return $documentFile;
    }

    public function edit(Request $request, $documentId, $id)
    {
        $data['document'] = $this->documentService->getDocument(['id' => $documentId]);
        if (empty($data['document']))
            return abort(404);

        $data['file'] = $this->documentService->getFile(['id' => $id]);
        if (empty($data['file']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.documents.file.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/document.file.caption')
            ]),
            'routeBack' => route('document.file.index', array_merge(['documentId' => $documentId], $request->query())),
            'breadcrumbs' => [
                __('module/document.file.caption') => route('document.file.index', ['documentId' => $documentId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(DocumentFileRequest $request, $documentId, $id)
    {
        $data = $request->all();

        if ($request->hasFile('file_document')) {
            $data['file_document'] = $request->file('file_document');
        }
        
        $data['document_id'] = $documentId;
        $data['locked'] = (bool)$request->locked;
        $data['config_show_title'] = (bool)$request->config_show_title;
        $data['config_show_description'] = (bool)$request->config_show_description;
        $data['config_show_cover'] = (bool)$request->config_show_cover;
        $data['config_show_custom_field'] = (bool)$request->config_show_custom_field;
        $documentFile = $this->documentService->updateFile($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($documentFile['success'] == true) {
            return $this->redirectForm($data)->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function publish($documentId, $id)
    {
        $documentFile = $this->documentService->statusFile('publish', ['id' => $id]);

        if ($documentFile['success'] == true) {
            return back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function approved($documentId, $id)
    {
        $documentFile = $this->documentService->statusFile('approved', ['id' => $id]);

        if ($documentFile['success'] == true) {
            return back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function sort(Request $request, $documentId)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->documentService->sortFile(['id' => $value, 'document_id' => $documentId], $i);
        }
    }

    public function position(Request $request, $documentId, $id, $position)
    {
        $documentFile = $this->documentService->positionFile(['id' => $id], $position);

        if ($documentFile['success'] == true) {
            return back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    public function softDelete($documentId, $id)
    {
        $documentFile = $this->documentService->trashFile(['id' => $id]);

        return $documentFile;
    }

    public function permanentDelete(Request $request, $documentId, $id)
    {
        $documentFile = $this->documentService->deleteFile($request, ['id' => $id]);

        return $documentFile;
    }

    public function restore($documentId, $id)
    {
        $documentFile = $this->documentService->restoreFile(['id' => $id]);

        if ($documentFile['success'] == true) {
            return redirect()->back()->with('success', $documentFile['message']);
        }

        return redirect()->back()->with('failed', $documentFile['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('document.file.index', array_merge(['documentId' => $data['document_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    public function download($id)
    {
        $documentFile = $this->documentService->getFile(['id' => $id]);
        $document = $documentFile['document'];

        if (!empty($document['roles'])) {
            
            $checkRole = $this->documentService->checkRole(['id' => $document['id']], Auth::user()->hasRole()[0]['id']);

            if (Auth::guard()->check() == false && $checkRole > 0) {
                return redirect()->back()->with('warning', __('global.forbidden'));
            }
        }

        // record download
        $this->documentService->recordDownload(['id' => $id]);

        if ($documentFile['type'] == '0') {
            $file = config('cms.files.document.path').$document['id'].'/'.
                $documentFile['file'];

            return response()->download(storage_path('app/'.$file));
        }

        if ($documentFile['type'] == '1') {

            return response()->download(storage_path('app/public/'.$documentFile['file']));
        }

        if ($documentFile['type'] == '2') {

            return redirect($documentFile['file']);
        }
    }
}
