<?php

namespace App\Services\Feature;

use App\Models\Feature\Api;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiService
{
    use ApiResponser;

    private $apiModel;

    public function __construct(
        Api $apiModel
    )
    {
        $this->apiModel = $apiModel;
    }

    /**
     * Get Api List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getApiList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $api = $this->apiModel->query();

        if ($isTrash == true)
            $api->onlyTrashed();

        if (isset($filter['status']))
            $api->where('active', $filter['status']);

        if (isset($filter['created_by']))
            $api->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $api->when($filter['q'], function ($api, $q) {
                $api->where('name', 'like', '%'.$q.'%')
                    ->orWhere('description', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $api->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $api->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $api->paginate($limit);
        } else {

            if ($limit > 0)
                $api->limit($limit);

            $result = $api->get();
        }

        return $result;
    }

    /**
     * Get Api One
     * @param array $where
     * @param array $with
     */
    public function getApi($where, $with = [])
    {
        $api = $this->apiModel->query();

        if (!empty($with))
            $api->with($with);

        $result = $api->firstWhere($where);

        return $result;
    }

    /**
     * Create Api
     * @param array $data
     */
    public function store($data)
    {
        try {

            $api = $this->apiModel->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'api_key' => $this->generateApi()['apiKey'],
                'api_secret' => $this->generateApi()['apiSecret'],
                'modules' => $data['modules'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'active' => (bool)$data['active'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($api,  __('global.alert.create_success', [
                'attribute' => __('feature/api.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Api
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $api = $this->getApi($where);

        try {
            
            $api->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'modules' => $data['modules'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'active' => (bool)$data['active'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $api['updated_by'],
            ]);

            return $this->success($api,  __('global.alert.update_success', [
                'attribute' => __('feature/api.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Activate Api
     * @param array $where
     */
    public function activate($where)
    {
        $api = $this->getApi($where);

        try {
            
            $api->update([
                'active' => !$api['active'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $api['updated_by'],
            ]);

            return $this->success($api, __('global.alert.update_success', [
                'attribute' => __('feature/api.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Regenerate Api
     * @param array $where
     */
    public function regenerateApi($where)
    {
        $api = $this->getApi($where);

        try {
            
            $api->update([
                'api_key' => $this->generateApi()['apiKey'],
                'api_secret' => $this->generateApi()['apiSecret'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $api['updated_by'],
            ]);

            return $this->success($api, __('global.alert.update_success', [
                'attribute' => __('feature/api.label.regenerate')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Api
     * @param array $where
     */
    public function trash($where)
    {
        $api = $this->getApi($where);
        
        try {
            
            if ($api['is_default'] == 0) {

                if (Auth::guard()->check()) {
                    $api->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }
    
                $api->delete();
    
                return $this->success($api,  __('global.alert.delete_success', [
                    'attribute' => __('feature/api.caption')
                ]));

            } else {

                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('feature/api.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Api
     * @param array $where
     */
    public function restore($where)
    {
        $api = $this->apiModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $api->restore();

            return $this->success($api, __('global.alert.restore_success', [
                'attribute' => __('feature/api.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Api (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $api = $this->apiModel->onlyTrashed()->firstWhere($where);
        } else {
            $api = $this->getApi($where);
        }

        try {
            
            $api->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('feature/api.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Generate API
     * @param int $lenghtKey
     * @param int $lenghtSecret
     */
    private function generateApi($lenghtKey = 16, $lenghtSecret = 19)
    {
        do {
            $apiKey = Str::random($lenghtKey);
        } while ($this->apiModel->where('api_key', $apiKey)->count());

        do {
            $apiSecret = Str::random($lenghtSecret);
        } while ($this->apiModel->where('api_secret', $apiSecret)->count());

        return [
            'apiKey' => $apiKey,
            'apiSecret' => $apiSecret
        ];
    }
}