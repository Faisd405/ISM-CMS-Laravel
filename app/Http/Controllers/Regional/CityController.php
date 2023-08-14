<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Regional\CityRequest;
use App\Services\RegionalService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    use ApiResponser;

    private $regionalService;

    public function __construct(
        RegionalService $regionalService
    )
    {
        $this->regionalService = $regionalService;
    }

    public function index(Request $request, $provinceCode)
    {
        $filter['province_code'] = $provinceCode;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['province'] = $this->regionalService->getProvince(['code' => $provinceCode]);
        if (empty($data['province']))
            return abort(404);

        $data['cities'] = $this->regionalService->getCityList($filter, true);
        $data['no'] = $data['cities']->firstItem();
        $data['cities']->withQueryString();

        return view('backend.regionals.city.index', compact('data'), [
            'title' => __('module/regional.city.title'),
            'routeBack' => route('province.index'),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('module/regional.city.caption') => ''
            ]
        ]);
    }

    public function trash(Request $request, $provinceCode)
    {
        $filter['province_code'] = $provinceCode;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['province'] = $this->regionalService->getProvince(['code' => $provinceCode]);
        if (empty($data['province']))
            return abort(404);

        $data['cities'] = $this->regionalService->getCityList($filter, true, 10, true, [], [
            'deleted_by' => 'DESC'
        ]);
        $data['no'] = $data['cities']->firstItem();
        $data['cities']->withQueryString();

        return view('backend.regionals.city.trash', compact('data'), [
            'title' => __('module/regional.city.title').' - '.__('global.trash'),
            'routeBack' => route('city.index', ['provinceCode' => $provinceCode]),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('module/regional.city.caption') => route('city.index', ['provinceCode' => $provinceCode]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $provinceCode)
    {
        $data['province'] = $this->regionalService->getProvince(['code' => $provinceCode]);
        if (empty($data['province']))
            return abort(404);

        return view('backend.regionals.city.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/regional.city.caption')
            ]),
            'routeBack' => route('city.index', array_merge(['provinceCode' => $provinceCode], $request->query())),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.city.caption') => route('city.index', ['provinceCode' => $provinceCode]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(CityRequest $request, $provinceCode)
    {
        $data = $request->all();
        $data['province_code'] = $provinceCode;
        $data['locked'] = (bool)$request->locked;
        $city = $this->regionalService->storeCity($data);
        $data['query'] = $request->query();

        if ($city['success'] == true) {
            return $this->redirectForm($data)->with('success', $city['message']);
        }

        return redirect()->back()->with('failed', $city['message']);
    }

    public function edit(Request $request, $provinceCode, $id)
    {
        $data['province'] = $this->regionalService->getProvince(['code' => $provinceCode]);
        if (empty($data['province']))
            return abort(404);

        $data['city'] = $this->regionalService->getCity(['id' => $id]);
        if (empty($data['city']))
            return abort(404);

        return view('backend.regionals.city.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/regional.city.caption')
            ]),
            'routeBack' => route('city.index', array_merge(['provinceCode' => $provinceCode], $request->query())),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.city.caption') => route('city.index', ['provinceCode' => $provinceCode]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(CityRequest $request, $provinceCode, $id)
    {
        $data = $request->all();
        $data['province_code'] = $provinceCode;
        $data['locked'] = (bool)$request->locked;
        $city = $this->regionalService->updateCity($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($city['success'] == true) {
            return $this->redirectForm($data)->with('success', $city['message']);
        }

        return redirect()->back()->with('failed', $city['message']);
    }

    public function softDelete($provinceCode, $id)
    {
        $city = $this->regionalService->trashCity(['id' => $id]);

        return $city;
    }

    public function permanentDelete(Request $request, $provinceCode, $id)
    {
        $city = $this->regionalService->deleteCity($request, ['id' => $id]);

        return $city;
    }

    public function restore($provinceCode, $id)
    {
        $city = $this->regionalService->restoreCity(['id' => $id]);

        if ($city['success'] == true) {
            return redirect()->back()->with('success', $city['message']);
        }

        return redirect()->back()->with('failed', $city['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('city.index', array_merge(['provinceCode' => $data['province_code']], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    public function listCityApi(Request $request)
    {
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('province_code', '') != '') {
            $filter['province_code'] = $request->input('province_code');
        }
        if ($request->input('code', '') != '') {
            $filter['code'] = $request->input('code');
        }

        $city = $this->regionalService->getCityList($filter, false, 0);

        return $this->success($city);
    }
}
