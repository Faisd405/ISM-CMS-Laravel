<?php

namespace App\Http\Controllers\Module\Inquiry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\Inquiry\InquiryFieldRequest;
use App\Services\Feature\LanguageService;
use App\Services\Module\InquiryService;
use Illuminate\Http\Request;

class InquiryFieldController extends Controller
{
    private $inquiryService, $languageService;

    public function __construct(
        InquiryService $inquiryService,
        LanguageService $languageService
    )
    {
        $this->inquiryService = $inquiryService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $inquiryId)
    {
        $filter['inquiry_id'] = $inquiryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['inquiry'] = $this->inquiryService->getInquiry(['id' => $inquiryId]);
        if (empty($data['inquiry']))
            return abort(404);

        $data['fields'] = $this->inquiryService->getFieldList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['fields']->firstItem();
        $data['fields']->withQueryString();

        return view('backend.inquiries.field.index', compact('data'), [
            'title' => __('module/inquiry.field.title'),
            'routeBack' => route('inquiry.index'),
            'breadcrumbs' => [
                __('module/inquiry.caption') => route('inquiry.index'),
                __('module/inquiry.field.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $inquiryId)
    {
        $filter['inquiry_id'] = $inquiryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['inquiry'] = $this->inquiryService->getInquiry(['id' => $inquiryId]);
        if (empty($data['inquiry']))
            return abort(404);

        $data['fields'] = $this->inquiryService->getFieldList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['fields']->firstItem();
        $data['fields']->withQueryString();

        return view('backend.inquiries.field.trash', compact('data'), [
            'title' => __('module/inquiry.field.title').' - '.__('global.trash'),
            'routeBack' => route('inquiry.field.index', ['inquiryId' => $inquiryId]),
            'breadcrumbs' => [
                __('module/inquiry.caption') => 'javascript:;',
                __('module/inquiry.field.caption') => route('inquiry.field.index', ['inquiryId' => $inquiryId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $inquiryId)
    {
        $data['inquiry'] = $this->inquiryService->getInquiry(['id' => $inquiryId]);
        if (empty($data['inquiry']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.inquiries.field.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/inquiry.field.caption')
            ]),
            'routeBack' => route('inquiry.field.index', array_merge(['inquiryId' => $inquiryId], $request->query())),
            'breadcrumbs' => [
                __('module/inquiry.field.caption') => route('inquiry.field.index', ['inquiryId' => $inquiryId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(InquiryFieldRequest $request, $inquiryId)
    {
        $data = $request->all();
        
        $data['inquiry_id'] = $inquiryId;
        $data['is_unique'] = (bool)$request->is_unique;
        $data['locked'] = (bool)$request->locked;
        $field = $this->inquiryService->storeField($data);
        $data['query'] = $request->query();

        if ($field['success'] == true) {
            return $this->redirectForm($data)->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function edit(Request $request, $inquiryId, $id)
    {
        $data['inquiry'] = $this->inquiryService->getInquiry(['id' => $inquiryId]);
        if (empty($data['inquiry']))
        return abort(404);
        
        $data['field'] = $this->inquiryService->getField(['id' => $id]);
        if (empty($data['field']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.inquiries.field.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/inquiry.field.caption')
            ]),
            'routeBack' => route('inquiry.field.index', array_merge(['inquiryId' => $inquiryId], $request->query())),
            'breadcrumbs' => [
                __('module/inquiry.field.caption') => route('inquiry.field.index', ['inquiryId' => $inquiryId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(InquiryFieldRequest $request, $inquiryId, $id)
    {
        $data = $request->all();

        $data['inquiry_id'] = $inquiryId;
        $data['is_unique'] = (bool)$request->is_unique;
        $data['locked'] = (bool)$request->locked;
        $field = $this->inquiryService->updateField($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($field['success'] == true) {
            return $this->redirectForm($data)->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function publish($inquiryId, $id)
    {
        $field = $this->inquiryService->statusField('publish', ['id' => $id]);

        if ($field['success'] == true) {
            return back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function approved($inquiryId, $id)
    {
        $field = $this->inquiryService->statusField('approved', ['id' => $id]);

        if ($field['success'] == true) {
            return back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function sort(Request $request, $inquiryId)
    {
        $i = 0;

        foreach ($request->datas as $value) {
            $i++;
            $this->inquiryService->sortField(['id' => $value, 'inquiry_id' => $inquiryId], $i);
        }
    }

    public function position(Request $request, $inquiryId, $id, $position)
    {
        $field = $this->inquiryService->positionField(['id' => $id], $position);

        if ($field['success'] == true) {
            return back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    public function softDelete($inquiryId, $id)
    {
        $field = $this->inquiryService->trashField(['id' => $id]);

        return $field;
    }

    public function permanentDelete(Request $request, $inquiryId, $id)
    {
        $field = $this->inquiryService->deleteField($request, ['id' => $id]);

        return $field;
    }

    public function restore($inquiryId, $id)
    {
        $field = $this->inquiryService->restoreField(['id' => $id]);

        if ($field['success'] == true) {
            return redirect()->back()->with('success', $field['message']);
        }

        return redirect()->back()->with('failed', $field['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('inquiry.field.index', array_merge(['inquiryId' => $data['inquiry_id']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
