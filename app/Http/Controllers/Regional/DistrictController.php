<?php

namespace App\Http\Controllers\Regional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Regional\DistrictRequest;
use App\Repositories\RegionalRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DistrictController extends Controller
{
    use ApiResponser;

    private $regionalService;

    public function __construct(
        RegionalRepository $regionalService
    )
    {
        $this->regionalService = $regionalService;
    }

    public function index(Request $request, $provinceCode, $cityCode)
    {
        $filter = [];
        $filter['province_code'] = $provinceCode;
        $filter['city_code'] = $cityCode;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['city'] = $this->regionalService->getCity(['code' => $cityCode]);
        if (empty($data['city']))
            return abort(404);

        $data['districts'] = $this->regionalService->getDistrictList($filter, true);
        $data['no'] = $data['districts']->firstItem();
        $data['districts']->withQueryString();

        return view('backend.regionals.district.index', compact('data'), [
            'title' => __('module/regional.district.title'),
            'routeBack' => route('city.index', ['provinceCode' => $provinceCode]),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('module/regional.city.caption') => route('city.index', ['provinceCode' => $provinceCode]),
                __('module/regional.district.caption') => ''
            ]
        ]);
    }

    public function trash(Request $request, $provinceCode, $cityCode)
    {
        $filter = [];
        $filter['province_code'] = $provinceCode;
        $filter['city_code'] = $cityCode;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['city'] = $this->regionalService->getCity(['code' => $cityCode]);
        if (empty($data['city']))
            return abort(404);

        $data['districts'] = $this->regionalService->getDistrictList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['districts']->firstItem();
        $data['districts']->withQueryString();

        return view('backend.regionals.district.trash', compact('data'), [
            'title' => __('module/regional.district.title').' - '.__('global.trash'),
            'routeBack' => route('district.index', ['provinceCode' => $provinceCode, 'cityCode' => $cityCode]),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.province.caption') => route('province.index'),
                __('module/regional.city.caption') => route('city.index', ['provinceCode' => $provinceCode]),
                __('module/regional.district.caption') => route('district.index', ['provinceCode' => $provinceCode, 'cityCode' => $cityCode]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $provinceCode, $cityCode)
    {
        $data['city'] = $this->regionalService->getCity(['code' => $cityCode]);
        if (empty($data['city']))
            return abort(404);

        return view('backend.regionals.district.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/regional.district.caption')
            ]),
            'routeBack' => route('district.index', array_merge([
                'provinceCode' => $provinceCode,
                'cityCode' => $cityCode
            ], $request->query())),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.district.caption') => route('district.index', ['provinceCode' => $provinceCode, 'cityCode' => $cityCode]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(DistrictRequest $request, $provinceCode, $cityCode)
    {
        $data = $request->all();
        $data['province_code'] = $provinceCode;
        $data['city_code'] = $cityCode;
        $data['locked'] = (bool)$request->locked;
        $district = $this->regionalService->storeDistrict($data);
        $data['query'] = $request->query();

        if ($district['success'] == true) {
            return $this->redirectForm($data)->with('success', $district['message']);
        }

        return redirect()->back()->with('failed', $district['message']);
    }

    public function edit(Request $request, $provinceCode, $cityCode, $id)
    {
        $data['city'] = $this->regionalService->getCity(['code' => $cityCode]);
        if (empty($data['city']))
            return abort(404);

        $data['district'] = $this->regionalService->getDistrict(['id' => $id]);
        if (empty($data['district']))
            return abort(404);

        return view('backend.regionals.district.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/regional.district.caption')
            ]),
            'routeBack' => route('district.index', array_merge([
                'provinceCode' => $provinceCode,
                'cityCode' => $cityCode
            ], $request->query())),
            'breadcrumbs' => [
                __('module/regional.caption') => 'javascript:;',
                __('module/regional.district.caption') => route('district.index', ['provinceCode' => $provinceCode, 'cityCode' => $cityCode]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(DistrictRequest $request, $provinceCode, $cityCode, $id)
    {
        $data = $request->all();
        $data['province_code'] = $provinceCode;
        $data['city_code'] = $cityCode;
        $data['locked'] = (bool)$request->locked;
        $district = $this->regionalService->updateDistrict($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($district['success'] == true) {
            return $this->redirectForm($data)->with('success', $district['message']);
        }

        return redirect()->back()->with('failed', $district['message']);
    }

    public function softDelete($provinceCode, $cityCode, $id)
    {
        $district = $this->regionalService->trashDistrict(['id' => $id]);

        return $district;
    }

    public function permanentDelete(Request $request, $provinceCode, $cityCode, $id)
    {
        $district = $this->regionalService->deleteDistrict($request, ['id' => $id]);

        return $district;
    }

    public function restore($provinceCode, $cityCode, $id)
    {
        $district = $this->regionalService->restoreDistrict(['id' => $id]);

        if ($district['success'] == true) {
            return redirect()->back()->with('success', $district['message']);
        }

        return redirect()->back()->with('failed', $district['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('district.index', array_merge([
            'provinceCode' => $data['province_code'],
            'cityCode' => $data['city_code'],
        ], $data['query']));
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    public function listDistrictApi(Request $request)
    {
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('province_code', '') != '') {
            $filter['province_code'] = $request->input('province_code');
        }
        if ($request->input('city_code', '') != '') {
            $filter['city_code'] = $request->input('city_code');
        }
        if ($request->input('code', '') != '') {
            $filter['code'] = $request->input('code');
        }

        $district = $this->regionalService->getDistrictList($filter, false, 0);

        return $this->success($district);
    }
}
