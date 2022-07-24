<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Regional\ProvinceRequest;
use App\Services\RegionalService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProvinceController extends Controller
{
    use ApiResponser;

    private $regionalService;

    public function __construct(
        RegionalService $regionalService
    )
    {
        $this->regionalService = $regionalService;
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

        $data['provinces'] = $this->regionalService->getProvinceList($filter, true);
        $data['no'] = $data['provinces']->firstItem();
        $data['provinces']->withQueryString();

        return view('backend.regionals.province.index', compact('data'), [
            'title' => __('module/regional.province.title'),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => '',
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

        $data['provinces'] = $this->regionalService->getProvinceList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['provinces']->firstItem();
        $data['provinces']->withQueryString();

        return view('backend.regionals.province.trash', compact('data'), [
            'title' => __('module/regional.province.title').' - '.__('global.trash'),
            'routeBack' => route('province.index'),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        return view('backend.regionals.province.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/regional.province.caption')
            ]),
            'routeBack' => route('province.index', $request->query()),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(ProvinceRequest $request)
    {
        $data = $request->all();
        $data['locked'] = (bool)$request->locked;
        $province = $this->regionalService->storeProvince($data);
        $data['query'] = $request->query();

        if ($province['success'] == true) {
            return $this->redirectForm($data)->with('success', $province['message']);
        }

        return redirect()->back()->with('failed', $province['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['province'] = $this->regionalService->getProvince(['id' => $id]);
        if (empty($data['province']))
            return abort(404);

        return view('backend.regionals.province.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('module/regional.province.caption')
            ]),
            'routeBack' => route('province.index', $request->query()),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(ProvinceRequest $request, $id)
    {
        $data = $request->all();
        $data['locked'] = (bool)$request->locked;
        $province = $this->regionalService->updateProvince($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($province['success'] == true) {
            return $this->redirectForm($data)->with('success', $province['message']);
        }

        return redirect()->back()->with('failed', $province['message']);
    }

    public function softDelete($id)
    {
        $province = $this->regionalService->trashProvince(['id' => $id]);

        return $province;
    }

    public function permanentDelete(Request $request, $id)
    {
        $province = $this->regionalService->deleteProvince($request, ['id' => $id]);

        return $province;
    }

    public function restore($id)
    {
        $province = $this->regionalService->restoreProvince(['id' => $id]);

        if ($province['success'] == true) {
            return redirect()->back()->with('success', $province['message']);
        }

        return redirect()->back()->with('failed', $province['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('province.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    public function listProvinceApi(Request $request)
    {
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('code', '') != '') {
            $filter['code'] = $request->input('code');
        }

        $province = $this->regionalService->getProvinceList($filter, false, 0);

        return $this->success($province);
    }
}
