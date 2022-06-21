<?php

namespace App\Services;

use App\Models\IndexingUrl;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Str;

class IndexUrlService
{
    use ApiResponser;

    private $indexUrlModel;

    public function __construct(
        IndexingUrl $indexUrlModel
    )
    {
        $this->indexUrlModel = $indexUrlModel;
    }

    /**
     * Get Index Url List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param bool $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getIndexUrlList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $indexUrl = $this->indexUrlModel->query();

        if ($isTrash == true)
            $indexUrl->onlyTrashed();

        if (isset($filter['code']))
            $indexUrl->where('code', $filter['code']);

        if (isset($filter['q']))
            $indexUrl->when($filter['q'], function ($indexUrl, $q) {
                $indexUrl->where('slug', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $indexUrl->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $indexUrl->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $indexUrl->paginate($limit);
        } else {

            if ($limit > 0)
                $indexUrl->limit($limit);

            $result = $indexUrl->get();
        }
        
        return $result;
    }

    /**
     * Get Index Url One
     * @param array $where
     * @param array $with
     */
    public function getIndexUrl($where, $with = [])
    {
        $indexUrl = $this->indexUrlModel->query();
        
        if (!empty($with))
            $indexUrl->with($with);
        
        $result = $indexUrl->firstWhere($where);;

        return $result;
    }

    /**
     * Create Index Url
     * @param array $data
     */
    public function store($data)
    {
        try {

            $indexUrl = $this->indexUrlModel->create([
                'slug' => Str::slug($data['slug'], '-'),
                'module' => $data['module'] ?? null,
                'urlable_id' => $data['urlable_id'] ?? null,
                'urlable_type' => $data['urlable_type'] ?? null
            ]);

            return $this->success($indexUrl,  __('global.alert.create_success', [
                'attribute' => __('module/url.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Create Index Url Associate
     * @param array $data
     * @param model $model
     */
    public function storeAssociate($data, $model)
    {
        $indexUrl = new IndexingUrl;
        $indexUrl->slug = $data['slug'];
        $indexUrl->module = $data['module'];
        $indexUrl->locked = 1;
        $indexUrl->urlable()->associate($model);
        $indexUrl->save();

        return $indexUrl;
    }

    /**
     * Update Index Url
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $indexUrl = $this->getIndexUrl($where);

        try {
            
            $indexUrl->update([
                'slug' => Str::slug($data['slug'], '-'),
                'module' => $data['module'] ?? null,
                'urlable_id' => $data['urlable_id'] ?? null,
                'urlable_type' => $data['urlable_type'] ?? null
            ]);

            return $this->success($indexUrl,  __('global.alert.update_success', [
                'attribute' => __('module/url.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Index Url Associate
     * @param string $slug
     * @param array $where
     */
    public function updateAssociate($slug, $where)
    {
        $indexUrl = $this->getIndexUrl($where);

        try {
            
            $indexUrl->update([
                'slug' => $slug
            ]);

            return $this->success($indexUrl,  __('global.alert.update_success', [
                'attribute' => __('module/url.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Trash Index Url
     * @param array $where
     */
    public function trash($where)
    {
        $indexUrl = $this->getIndexUrl($where);

        try {

            if ($indexUrl['locked'] == 0) {

                $indexUrl->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/url.caption')
                ]));
    
            } else {
                return $this->error($indexUrl,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/url.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Index Url
     * @param array $where
     */
    public function restore($where)
    {
        $indexUrl = $this->indexUrlModel->onlyTrashed()->firstWhere($where);

        try {
            
            $checkSlug = $this->indexUrlModel->firstWhere('slug', $indexUrl['slug']);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/url.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $indexUrl->restore();

            return $this->success($indexUrl, __('global.alert.restore_success', [
                'attribute' => __('module/url.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

     /**
     * Delete Index Url (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $indexUrl = $this->indexUrlModel->onlyTrashed()->firstWhere($where);
        } else {
            $indexUrl = $this->getIndexUrl($where);
        }

        try {
                
            $indexUrl->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/url.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}