<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\TemplateRequest;
use App\Repositories\Master\TemplateRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    private $templateService;

    public function __construct(
        TemplateRepository $templateService
    )
    {
        $this->templateService = $templateService;
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
        if ($request->input('module', '') != '') {
            $filter['module'] = $request->input('module');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }

        $data['templates'] = $this->templateService->getTemplateList($filter, true);
        $data['no'] = $data['templates']->firstItem();
        $data['templates']->withQueryString();

        return view('backend.masters.template.index', compact('data'), [
            'title' => __('master/template.title'),
            'breadcrumbs' => [
                __('master/template.caption') => '',
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
        if ($request->input('module', '') != '') {
            $filter['module'] = $request->input('module');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }

        $data['templates'] = $this->templateService->getTemplateList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['templates']->firstItem();
        $data['templates']->withQueryString();

        return view('backend.masters.template.trash', compact('data'), [
            'title' => __('master/template.title').' - '.__('global.trash'),
            'routeBack' => route('template.index'),
            'breadcrumbs' => [
                __('master/template.caption') => route('template.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        return view('backend.masters.template.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('master/template.caption')
            ]),
            'routeBack' => route('template.index', $request->query()),
            'breadcrumbs' => [
                __('master/template.caption') => route('template.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(TemplateRequest $request)
    {
        $data = $request->all();
        $data['locked'] = (bool)$request->locked;
        $template = $this->templateService->store($data);
        $data['query'] = $request->query();

        if ($template['success'] == true) {
            return $this->redirectForm($data)->with('success', $template['message']);
        }

        return redirect()->back()->with('failed', $template['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['template'] = $this->templateService->getTemplate(['id' => $id]);

        if (empty($data['template']))
            return abort(404);

        return view('backend.masters.template.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('master/template.caption')
            ]),
            'routeBack' => route('template.index', $request->query()),
            'breadcrumbs' => [
                __('master/template.caption') => route('template.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(TemplateRequest $request, $id)
    {
        $data = $request->all();
        $data['locked'] = (bool)$request->locked;
        $template = $this->templateService->update($data, ['id' => $id]);
        $data['query'] = $request->query();

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
        $redir = redirect()->route('template.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
