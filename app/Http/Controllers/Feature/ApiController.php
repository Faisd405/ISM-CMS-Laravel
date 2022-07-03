<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\ApiRequest;
use App\Services\Feature\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    private $apiService;

    public function __construct(
        ApiService $apiService
    )
    {
        $this->apiService = $apiService;
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

        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }

        $data['apis'] = $this->apiService->getApiList($filter, true);
        $data['no'] = $data['apis']->firstItem();
        $data['apis']->withQueryString();

        return view('backend.features.api.index', compact('data'), [
            'title' => __('feature/api.title'),
            'breadcrumbs' => [
                __('feature/api.caption') => '',
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

        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }

        $data['apis'] = $this->apiService->getApiList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['apis']->firstItem();
        $data['apis']->withQueryString();

        return view('backend.features.api.trash', compact('data'), [
            'title' => __('feature/api.title').' - '.__('global.trash'),
            'routeBack' => route('api.index'),
            'breadcrumbs' => [
                __('feature/api.caption') => route('api.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        return view('backend.features.api.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('feature/api.caption')
            ]),
            'routeBack' => route('api.index', $request->query()),
            'breadcrumbs' => [
                __('feature/api.caption') => route('api.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(ApiRequest $request)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $api = $this->apiService->store($data);
        $data['query'] = $request->query();

        if ($api['success'] == true) {
            return $this->redirectForm($data)->with('success', $api['message']);
        }

        return redirect()->back()->with('failed', $api['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['api'] = $this->apiService->getApi(['id' => $id]);
        if (empty($data['api']))
            return abort(404);

        return view('backend.features.api.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('feature/api.caption')
            ]),
            'routeBack' => route('api.index', $request->query()),
            'breadcrumbs' => [
                __('feature/api.caption') => route('api.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(ApiRequest $request, $id)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $api = $this->apiService->update($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($api['success'] == true) {
            return $this->redirectForm($data)->with('success', $api['message']);
        }

        return redirect()->back()->with('failed', $api['message']);
    }

    public function activate($id)
    {
        $api = $this->apiService->activate(['id' => $id]);

        if ($api['success'] == true) {
            return back()->with('success', $api['message']);
        }

        return redirect()->back()->with('failed', $api['message']);
    }

    public function regenerate($id)
    {
        $api = $this->apiService->regenerateApi(['id' => $id]);

        if ($api['success'] == true) {
            return back()->with('success', $api['message']);
        }

        return redirect()->back()->with('failed', $api['message']);
    }

    public function softDelete($id)
    {
        $api = $this->apiService->trash(['id' => $id]);

        return $api;
    }

    public function permanentDelete(Request $request, $id)
    {
        $api = $this->apiService->delete($request, ['id' => $id]);

        return $api;
    }

    public function restore($id)
    {
        $api = $this->apiService->restore(['id' => $id]);

        if ($api['success'] == true) {
            return redirect()->back()->with('success', $api['message']);
        }

        return redirect()->back()->with('failed', $api['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('api.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
