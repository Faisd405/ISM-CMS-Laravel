<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\TemplateRequest;
use App\Services\Master\TemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    private $templateService;

    public function __construct(
        TemplateService $templateService
    )
    {
        $this->templateService = $templateService;
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('module', '') != '') {
            $filter['module'] = $request->input('module');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['templates'] = $this->templateService->getTemplateList($filter, true);
        $data['no'] = $data['templates']->firstItem();
        $data['templates']->withPath(url()->current().$param);

        return view('backend.masters.template.index', compact('data'), [
            'title' => __('master/template.title'),
            'breadcrumbs' => [
                __('master/template.caption') => '',
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
        if ($request->input('module', '') != '') {
            $filter['module'] = $request->input('module');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['templates'] = $this->templateService->getTemplateList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['templates']->firstItem();
        $data['templates']->withPath(url()->current().$param);

        return view('backend.masters.template.trash', compact('data'), [
            'title' => __('master/template.title').' - '.__('global.trash'),
            'routeBack' => route('template.index'),
            'breadcrumbs' => [
                __('master/template.caption') => route('template.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create()
    {
        return view('backend.masters.template.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('master/template.caption')
            ]),
            'routeBack' => route('template.index'),
            'breadcrumbs' => [
                __('master/template.caption') => route('template.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(TemplateRequest $request)
    {
        $data = $request->all();
        $template = $this->templateService->store($data);

        if ($template['success'] == true) {
            return $this->redirectForm($data)->with('success', $template['message']);
        }

        return redirect()->back()->with('failed', $template['message']);
    }

    public function edit($id)
    {
        $data['template'] = $this->templateService->getTemplate(['id' => $id]);

        return view('backend.masters.template.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('master/template.caption')
            ]),
            'routeBack' => route('template.index'),
            'breadcrumbs' => [
                __('master/template.caption') => route('template.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(TemplateRequest $request, $id)
    {
        $data = $request->all();
        $template = $this->templateService->update($data, ['id' => $id]);

        if ($template['success'] == true) {
            return $this->redirectForm($data)->with('success', $template['message']);
        }

        return redirect()->back()->with('failed', $template['message']);
    }

    public function softDelete($id)
    {
        $template = $this->templateService->trash(['id' => $id]);

        return $template;
    }

    public function permanentDelete(Request $request, $id)
    {
        $template = $this->templateService->delete($request, ['id' => $id]);

        return $template;
    }

    public function restore($id)
    {
        $template = $this->templateService->restore(['id' => $id]);

        if ($template['success'] == true) {
            return redirect()->back()->with('success', $template['message']);
        }

        return redirect()->back()->with('failed', $template['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('template.index');
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
