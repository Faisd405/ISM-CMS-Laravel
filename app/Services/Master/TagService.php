<?php

namespace App\Services\Master;

use App\Models\Master\Tag;
use App\Models\Master\TagType;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TagService
{
    use ApiResponser;

    private $tagModel, $tagTypeModel;

    public function __construct(
        Tag $tagModel,
        TagType $tagTypeModel
    )
    {
        $this->tagModel = $tagModel;
        $this->tagTypeModel = $tagTypeModel;
    }

    /**
     * Get Tag List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getTagList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $tag = $this->tagModel->query();

        if ($isTrash == true)
            $tag->onlyTrashed();

        if (isset($filter['flags']))
            $tag->where('flags', $filter['flags']);

        if (isset($filter['standar']))
            $tag->where('standar', $filter['standar']);

        if (isset($filter['created_by']))
            $tag->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $tag->when($filter['q'], function ($tag, $q) {
                $tag->where('name', 'like', '%'.$q.'%')
                    ->orWhere('description', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $tag->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $tag->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $tag->paginate($limit);
        } else {
            $result = $tag->get();
        }

        return $result;
    }

    /**
     * Get Tag One
     * @param array $where
     * @param array $with
     */
    public function getTag($where, $with = [])
    {
        $tag = $this->tagModel->query();

        if (!empty($with))
            $tag->with($with);

        $result = $tag->firstWhere($where);

        return $result;
    }

    /**
     * Create Tag
     * @param array $data
     */
    public function store($data)
    {
        try {

            $tag = $this->tagModel->create([
                'name' => Str::lower($data['name']),
                'description' => $data['description'] ?? null,
                'flags' => (bool)$data['flags'],
                'standar' => (bool)$data['standar'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($tag,  __('global.alert.create_success', [
                'attribute' => __('master/tags.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update tag
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $tag = $this->getTag($where);

        try {
            
            $tag->update([
                'name' => Str::lower($data['name']),
                'description' => $data['description'] ?? null,
                'flags' => (bool)$data['flags'],
                'standar' => (bool)$data['standar'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $tag['updated_by'],
            ]);

            return $this->success($tag,  __('global.alert.update_success', [
                'attribute' => __('master/tags.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Status Tag (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function status($field, $where)
    {
        $tag = $this->getTag($where);

        try {
            
            $tag->update([
                $field => !$tag[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $tag['updated_by'],
            ]);

            return $this->success($tag, __('global.alert.update_success', [
                'attribute' => __('master/tags.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Wipe Store Tags
     * @param arry|string $tags
     * @param model $model
     */
    public function wipeStore($tags, $model)
    {
        $tagName = explode(',', $tags);
        $tagName = array_map('strtolower', $tagName);

        $tag = new Tag;
        foreach($tagName as $name) {
            $tag->updateOrCreate([
                'name' => $name
            ], [
                'created_by' => Auth::user()->id,
                'name' => $name,
            ]);
        }

        $this->wipeAndUpdate($model, $tagName);

        return true;
    }

    /**
     * Wipe Tags
     * @param model $model
     */
    public function wipe($model)
    {
        $type = $this->tagTypeModel->query();

        $type->where('tagable_id', $model['tagable_id'])
            ->where('tagable_type', $model['tagable_type'])
            ->get();
        
        $type->delete();
    }

    /**
     * Wipe & update Tags
     * @param model $model
     * @param array|string $tags
     */
    public function wipeAndUpdate($model, $tags = null)
    {
        $tagType = new TagType;

        $model = $tagType->tagable()->associate($model);
        $this->wipe($model);

        if ($tags != null) {
            foreach($tags as $name) {
                $tagId = $this->getTag(['name' => $name])['id'];
                $tagType->updateOrCreate([
                    'tag_id' => $tagId,
                    'tagable_id' => $model['tagable_id'],
                    'tagable_type' => $model['tagable_type'],
                ], [
                    'tag_id' => $tagId,
                    'tagable_id' => $model['tagable_id'],
                    'tagable_type' => $model['tagable_type'],
                ]);
            }
        }

        return true;
    }

    /**
     * Trash Tag
     * @param array $where
     */
    public function trash($where)
    {
        $tag = $this->getTag($where);

        try {

            $type = $tag->types()->count();
            
            if ($type == 0) {
        
                if (Auth::guard()->check()) {
                    $tag->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $tag->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('master/tags.caption')
                ]));
    
            } else {
                return $this->error($tag,  __('global.alert.delete_failed_used', [
                    'attribute' => __('master/tags.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Tag
     * @param array $where
     */
    public function restore($where)
    {
        $tag = $this->tagModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $tag->restore();

            return $this->success($tag, __('global.alert.restore_success', [
                'attribute' => __('master/tags.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

     /**
     * Delete Tag (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $tag = $this->tagModel->onlyTrashed()->firstWhere($where);
        } else {
            $tag = $this->getTag($where);
        }

        try {
                
            $tag->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('master/tags.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}