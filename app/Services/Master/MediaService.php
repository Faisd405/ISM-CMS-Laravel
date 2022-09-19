<?php

namespace App\Services\Master;

use App\Models\Master\Media;
use App\Models\Module\Content\ContentPost;
use App\Models\Module\Page;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MediaService
{
    use ApiResponser;

    private $mediaModel, $language;

    public function __construct(
        Media $mediaModel,
        LanguageService $language
    )
    {
        $this->mediaModel = $mediaModel;
        $this->language = $language;
    }

     /**
     * Get Media List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getMediaList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $media = $this->mediaModel->query();

        if ($isTrash == true)
            $media->onlyTrashed();

        if (isset($filter['module']))
            $media->where('module', $filter['module']);

        if (isset($filter['mediable_id']))
            $media->where('mediable_id', $filter['mediable_id']);

        if (isset($filter['mediable_type']))
            $media->where('mediable_type', $filter['mediable_type']);

        if (isset($filter['created_by']))
            $media->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $media->when($filter['q'], function ($media, $q) {
                $media->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $media->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $media->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $media->paginate($limit);
        } else {

            if ($limit > 0)
                $media->limit($limit);

            $result = $media->get();
        }

        return $result;
    }

    /**
     * Get Media One
     * @param array $where
     * @param array $with
     */
    public function getMedia($where, $with = [])
    {
        $media = $this->mediaModel->query();

        if (!empty($with))
            $media->with($with);

        $result = $media->firstWhere($where);

        return $result;
    }

    /**
     * Create Media
     * @param array $data
     */
    public function store($data)
    {
        try {

            if ($data['module_type'] == 'page') {
                $classes = Page::class;
                $module = Page::firstWhere('id', $data['module_id']);
            }

            if ($data['module_type'] == 'content_post') {
                $classes = ContentPost::class;
                $module = ContentPost::firstWhere('id', $data['module_id']);
            }

            $isYoutube = (bool)$data['is_youtube'];

            $media = new Media;
            $media->module = $data['module_type'];
            $media->is_youtube = $isYoutube;
            
            if ($isYoutube == 0) {
                $media->filepath = [
                    'filename' => Str::replace(url('/storage'), '', $data['filename']),
                    'thumbnail' => Str::replace(url('/storage'), '', $data['thumbnail']),
                ];
                $media->youtube_id = null;
            } else {
                $media->filepath = [
                    'filename' => null,
                    'thumbnail' => null,
                ];
                $media->youtube_id = $data['youtube_id'];
            }

            $this->setField($data, $media);

            $media->position = $this->mediaModel->where('mediable_id', $data['module_id'])
                ->where('mediable_type', $classes)->max('position') + 1;
            if (Auth::guard()->check()) {
                $media->created_by = Auth::user()['id'];
            }
            $media->mediable()->associate($module);
            $media->save();

            return $this->success($media,  __('global.alert.create_success', [
                'attribute' => __('master/media.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Media
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $media = $this->getMedia($where);

        try {
            
            $isYoutube = (bool)$data['is_youtube'];

            $media->is_youtube = $isYoutube;
            if ($isYoutube == 0) {
                $media->filepath = [
                    'filename' => Str::replace(url('/storage'), '', $data['filename']),
                    'thumbnail' => Str::replace(url('/storage'), '', $data['thumbnail']),
                ];
                $media->youtube_id = null;
            } else {
                $media->filepath = [
                    'filename' => null,
                    'thumbnail' => null,
                ];
                $media->youtube_id = $data['youtube_id'];
            }

            $this->setField($data, $media);

            if (Auth::guard()->check()) {
                $media->updated_by = Auth::user()['id'];
            }
            $media->save();

            return $this->success($media,  __('global.alert.update_success', [
                'attribute' => __('master/media.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Media
     * @param array $data
     * @param model $media
     */
    private function setField($data, $media)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('app.fallback_locale');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $title[$value['iso_codes']] = ($data['title_'.$value['iso_codes']] == null) ?
                $data['title_'.$langDefault] : $data['title_'.$value['iso_codes']];

            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];
        }

        $media->title = $title;
        $media->description = $description;

        $media->locked = (bool)$data['locked'];
        $media->config = [];
        
        return $media;
    }

    /**
     * Sort Media
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sort($where, $position)
    {
        $getMedia = $this->getMedia($where);

        $media = $this->mediaModel->firstWhere([
            'id' => $getMedia['id'],
            'mediable_id' => $getMedia['mediable_id'],
            'mediable_type' => $getMedia['mediable_type']
        ]);
        $media->position = $position;
        if (Auth::guard()->check()) {
            $media->updated_by = Auth::user()['id'];
        }
        $media->save();

        return $media;
    }

    /**
     * Set Position Media
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function position($where, $position)
    {
        $media = $this->getMedia($where);

        try {

            if ($position >= 1) {
    
                $this->mediaModel->where('position', $position)
                    ->where('module', $media['module'])
                    ->where('mediable_id', $media['mediable_id'])
                    ->where('mediable_type', $media['mediable_type'])
                    ->update([
                    'position' => $media['position'],
                ]);
    
                $media->position = $position;
                if (Auth::guard()->check()) {
                    $media->updated_by = Auth::user()['id'];
                }
                $media->save();
    
                return $this->success($media, __('global.alert.update_success', [
                    'attribute' => __('master/media.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('master/media.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Media
     * @param array $where
     */
    public function trash($where)
    {
        $media = $this->getMedia($where);

        try {

            $media->delete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/media.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore media
     * @param array $where
     */
    public function restore($where)
    {
        $media = $this->mediaModel->onlyTrashed()->firstWhere($where);

        try {
            
            $media->restore();

            return $this->success($media, __('global.alert.restore_success', [
                'attribute' => __('master/media.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete media (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $media = $this->mediaModel->onlyTrashed()->firstWhere($where);
        } else {
            $media = $this->getMedia($where);
        }

        try {
            
            $media->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('master/media.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}