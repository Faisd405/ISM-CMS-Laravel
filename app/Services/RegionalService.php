<?php

namespace App\Services;

use App\Models\Regional\City;
use App\Models\Regional\District;
use App\Models\Regional\Province;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegionalService
{
    use ApiResponser;

    private $provinceModel, $cityModel, $districtModel;

    public function __construct(
        Province $provinceModel,
        City $cityModel,
        District $districtModel
    )
    {
        $this->provinceModel = $provinceModel;
        $this->cityModel = $cityModel;
        $this->districtModel = $districtModel;
    }

    //--------------------------------------------------------------------------
    // PROVINCE
    //--------------------------------------------------------------------------

    /**
     * Get province List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getProvinceList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $province = $this->provinceModel->query();

        if ($isTrash == true)
            $province->onlyTrashed();

        if (isset($filter['code']))
            $province->where('code', $filter['code']);

        if (isset($filter['created_by']))
            $province->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $province->when($filter['q'], function ($province, $q) {
                $province->where('name', 'like', '%'.$q.'%')
                        ->orWhere('longitude', 'like', '%'.$q.'%')
                        ->orWhere('latitude', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $province->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $province->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $province->paginate($limit);
        } else {

            if ($limit > 0)
                $province->limit($limit);

            $result = $province->get();
        }
        
        return $result;
    }

    /**
     * Get province One
     * @param array $where
     * @param array $with
     */
    public function getProvince($where, $with = [])
    {
        $province = $this->provinceModel->query();
        
        if (!empty($with))
            $province->with($with);
        
        $result = $province->firstWhere($where);;

        return $result;
    }

    /**
     * Create Province
     * @param array $data
     */
    public function storeProvince($data)
    {
        try {

            $province = $this->provinceModel->create([
                'code' => $data['code'],
                'name' => Str::upper($data['name']),
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'locked' => (bool)$data['locked'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($province,  __('global.alert.create_success', [
                'attribute' => __('module/regional.province.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Province
     * @param array $data
     * @param array $where
     */
    public function updateProvince($data, $where)
    {
        $province = $this->getProvince($where);

        try {
            
            $province->update([
                'code' => $data['code'],
                'name' => Str::upper($data['name']),
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'locked' => (bool)$data['locked'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $province['updated_by'],
            ]);

            return $this->success($province,  __('global.alert.update_success', [
                'attribute' => __('module/regional.province.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Trash Province
     * @param array $where
     */
    public function trashProvince($where)
    {
        $province = $this->getProvince($where);

        try {

            $cities = $province->cities()->count();
            
            if ($province['locked'] == 0 && $cities == 0) {
        
                if (Auth::guard()->check()) {
                    $province->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $province->delete();

                return $this->success($province,  __('global.alert.delete_success', [
                    'attribute' => __('module/regional.province.caption')
                ]));
    
            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/regional.province.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Province
     * @param array $where
     */
    public function restoreProvince($where)
    {
        $province = $this->provinceModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $province->restore();

            return $this->success($province, __('global.alert.restore_success', [
                'attribute' => __('module/regional.province.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Province (Permanent)
     * @param array $where
     */
    public function deleteProvince($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $province = $this->provinceModel->onlyTrashed()->firstWhere($where);
        } else {
            $province = $this->getProvince($where);
        }

        try {
            
            $province->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/regional.province.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // CITY
    //--------------------------------------------------------------------------

    /**
     * Get City List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getCityList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $city = $this->cityModel->query();

        if ($isTrash == true)
            $city->onlyTrashed();

        if (isset($filter['province_code']))
            $city->where('province_code', $filter['province_code']);

        if (isset($filter['code']))
            $city->where('code', $filter['code']);

        if (isset($filter['created_by']))
            $city->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $city->when($filter['q'], function ($city, $q) {
                $city->where('name', 'like', '%'.$q.'%')
                    ->orWhere('longitude', 'like', '%'.$q.'%')
                    ->orWhere('latitude', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $city->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $city->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $city->paginate($limit);
        } else {

            if ($limit > 0)
                $city->limit($limit);

            $result = $city->get();
        }
        
        return $result;
    }

    /**
     * Get City One
     * @param array $where
     * @param array $with
     */
    public function getCity($where, $with = [])
    {
        $city = $this->cityModel->query();
        
        if (!empty($with))
            $city->with($with);
        
        $result = $city->firstWhere($where);;

        return $result;
    }

    /**
     * Create City
     * @param array $data
     */
    public function storeCity($data)
    {
        try {

            $city = $this->cityModel->create([
                'province_code' => $data['province_code'],
                'code' => $data['code'],
                'name' => Str::upper($data['name']),
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'locked' => (bool)$data['locked'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($city,  __('global.alert.create_success', [
                'attribute' => __('module/regional.city.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update City
     * @param array $data
     * @param array $where
     */
    public function updateCity($data, $where)
    {
        $city = $this->getCity($where);

        try {
            
            $city->update([
                'province_code' => $data['province_code'],
                'code' => $data['code'],
                'name' => Str::upper($data['name']),
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'locked' => (bool)$data['locked'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $city['updated_by'],
            ]);

            return $this->success($city,  __('global.alert.update_success', [
                'attribute' => __('module/regional.city.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Trash City
     * @param array $where
     */
    public function trashCity($where)
    {
        $city = $this->getCity($where);

        try {

            $districts = $city->districts()->count();

            if ($city['locked'] == 0 && $districts == 0) {
        
                if (Auth::guard()->check()) {
                    $city->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $city->delete();

                return $this->success($city,  __('global.alert.delete_success', [
                    'attribute' => __('module/regional.city.caption')
                ]));
    
            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/regional.city.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore City
     * @param array $where
     */
    public function restoreCity($where)
    {
        $city = $this->cityModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            if ($city['province']->onlyTrashed()->count() > 0) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/regional.city.caption')
                ]));
            }

            $city->restore();

            return $this->success($city, __('global.alert.restore_success', [
                'attribute' => __('module/regional.city.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete City (Permanent)
     * @param array $where
     */
    public function deleteCity($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $city = $this->cityModel->onlyTrashed()->firstWhere($where);
        } else {
            $city = $this->getCity($where);
        }

        try {
            
            $city->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/regional.city.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // DISTRICT
    //--------------------------------------------------------------------------

    /**
     * Get District List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getDistrictList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $district = $this->districtModel->query();

        if ($isTrash == true)
            $district->onlyTrashed();

        if (isset($filter['province_code']))
            $district->where('province_code', $filter['province_code']);

        if (isset($filter['city_code']))
            $district->where('city_code', $filter['city_code']);

        if (isset($filter['code']))
            $district->where('code', $filter['code']);

        if (isset($filter['created_by']))
            $district->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $district->when($filter['q'], function ($district, $q) {
                $district->where('name', 'like', '%'.$q.'%')
                        ->orWhere('longitude', 'like', '%'.$q.'%')
                        ->orWhere('latitude', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $district->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $district->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $district->paginate($limit);
        } else {

            if ($limit > 0)
                $district->limit($limit);

            $result = $district->get();
        }
        
        return $result;
    }

    /**
     * Get District One
     * @param array $where
     * @param array $with
     */
    public function getDistrict($where, $with = [])
    {
        $district = $this->districtModel->query();
        
        if (!empty($with))
            $district->with($with);
        
        $result = $district->firstWhere($where);;

        return $result;
    }

    /**
     * Create District
     * @param array $data
     */
    public function storeDistrict($data)
    {
        try {

            $district = $this->districtModel->create([
                'province_code' => $data['province_code'],
                'city_code' => $data['city_code'],
                'code' => $data['code'],
                'name' => Str::upper($data['name']),
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'locked' => (bool)$data['locked'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($district,  __('global.alert.create_success', [
                'attribute' => __('module/regional.district.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * @param array $data
     * @param array $where
     */
    public function updateDistrict($data, $where)
    {
        $district = $this->getDistrict($where);

        try {
            
            $district->update([
                'province_code' => $data['province_code'],
                'city_code' => $data['city_code'],
                'code' => $data['code'],
                'name' => Str::upper($data['name']),
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'locked' => (bool)$data['locked'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $district['updated_by'],
            ]);

            return $this->success($district,  __('global.alert.update_success', [
                'attribute' => __('module/regional.district.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Trash District
     * @param array $where
     */
    public function trashDistrict($where)
    {
        $district = $this->getDistrict($where);

        try {

            if ($district['locked'] == 0) {
        
                if (Auth::guard()->check()) {
                    $district->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $district->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/regional.district.caption')
                ]));
    
            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/regional.district.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore District
     * @param array $where
     */
    public function restoreDistrict($where)
    {
        $district = $this->districtModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            if ($district['city']->onlyTrashed()->count() > 0) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/regional.district.caption')
                ]));
            }

            $district->restore();

            return $this->success($district, __('global.alert.restore_success', [
                'attribute' => __('module/regional.district.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete District (Permanent)
     * @param array $where
     */
    public function deleteDistrict($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $district = $this->districtModel->onlyTrashed()->firstWhere($where);
        } else {
            $district = $this->getDistrict($where);
        }

        try {
            
            $district->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/regional.district.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}