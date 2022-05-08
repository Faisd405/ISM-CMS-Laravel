<?php

namespace App\Services\Module;

use App\Models\Module\Link\LinkCategory;
use App\Models\Module\Link\LinkMedia;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LinkService
{
    use ApiResponser;

    private $categoryModel, $mediaModel, $language;

    public function __construct(
        LinkCategory $categoryModel,
        LinkMedia $mediaModel,
        LanguageService $language
    )
    {
        $this->categoryModel = $categoryModel;
        $this->mediaModel = $mediaModel;
        $this->language = $language;
    }

    //---------------------------
    // LINK CATEGORY
    //---------------------------

    /**
     * Get Category List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getCategoryList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $category = $this->categoryModel->query();

        if ($isTrash == true)
            $category->onlyTrashed();

        if (isset($filter['publish']))
            $category->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $category->where('public', $filter['public']);

        if (isset($filter['approved']))
            $category->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $category->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $category->when($filter['q'], function ($category, $q) {
                $category->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $category->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $category->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $category->paginate($limit);
        } else {
            $result = $category->get();
        }
        
        return $result;
    }

    /**
     * Get Category One
     * @param array $where
     * @param array $with
     */
    public function getCategory($where, $with = [])
    {
        $category = $this->categoryModel->query();
        
        if (!empty($with))
            $category->with($with);
        
        $result = $category->firstWhere($where);;

        return $result;
    }

    /**
     * Create Category
     * @param array $data
     */
    public function storeCategory($data)
    {
        try {

            $category = new LinkCategory;
            $this->setFieldCategory($data, $category);
            $category->position = $this->categoryModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.link.category.approval') == true) {
                    $category->approved = 2;
                }
                $category->created_by = Auth::user()['id'];

            $category->save();

            return $this->success($category,  __('global.alert.create_success', [
                'attribute' => __('module/link.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Category
     * @param array $data
     * @param array $where
     */
    public function updateCategory($data, $where)
    {
        $category = $this->getCategory($where);

        try {
            
            $this->setFieldCategory($data, $category);
            if (Auth::guard()->check())
                $category->updated_by = Auth::user()['id'];

            $category->save();

            return $this->success($category,  __('global.alert.update_success', [
                'attribute' => __('module/link.category.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Category
     * @param array $data
     * @param model $category
     */
    private function setFieldCategory($data, $category)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('cms.module.feature.language.default');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $name[$value['iso_codes']] = ($data['name_'.$value['iso_codes']] == null) ?
                $data['name_'.$langDefault] : $data['name_'.$value['iso_codes']];

            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];
        }

        $category->slug = Str::slug($data['slug'], '-');
        $category->name = $name;
        $category->description = $description;
        $category->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $category->publish = (bool)$data['publish'];
        $category->public = (bool)$data['public'];
        $category->locked = (bool)$data['locked'];
        $category->config = [
            'is_detail' => (bool)$data['is_detail'],
            'hide_description' => (bool)$data['hide_description'],
            'hide_banner' => (bool)$data['hide_banner'],
        ];
        $category->template_id = $data['template_id'] ?? null;

        if (isset($data['cf_name'])) {
            
            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $category->custom_fields = $customField;
        } else {
            $category->custom_fields = null;
        }

        $category->media_perpage = $data['media_perpage'] ?? 0;

        return $category;
    }

    /**
     * Status Category (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusCategory($field, $where)
    {
        $category = $this->getCategory($where);

        try {
            
            $category->update([
                $field => !$category[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $category['updated_by'],
            ]);

            if ($field == 'publish') {
                $category->menus()->update([
                    'publish' => $category['publish']
                ]);
            }

            return $this->success($category, __('global.alert.update_success', [
                'attribute' => __('module/link.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Category
     * @param array $where
     * @param int $position
     */
    public function positionCategory($where, $position)
    {
        $category = $this->getCategory($where);
        
        try {

            if ($position >= 1) {
    
                $this->categoryModel->where('position', $position)->update([
                    'position' => $category['position'],
                ]);
    
                $category->position = $position;
                if (Auth::guard()->check()) {
                    $category->updated_by = Auth::user()['id'];
                }
                $category->save();
    
                return $this->success($category, __('global.alert.update_success', [
                    'attribute' => __('module/link.category.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/link.category.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

     /**
     * Record Category Hits
     * @param array $where
     */
    public function recordCategoryHits($where)
    {
        $category = $this->getCategory($where);
        $category->update([
            'hits' => ($category->hits+1)
        ]);

        return $category;
    }

        /**
     * Trash Category
     * @param array $where
     */
    public function trashCategory($where)
    {
        $category = $this->getCategory($where);

        try {
            
            $meedia = $category->medias()->count();

            if ($category['locked'] == 0 && $meedia == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $category['created_by']) {
                        return $this->error($category,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/link.category.caption')
                        ]));
                    }

                    $category->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $category->menus()->delete();
                $category->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/link.category.caption')
                ]));
    
            } else {
                return $this->error($category,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/link.category.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Category
     * @param array $where
     */
    public function restoreCategory($where)
    {
        $category = $this->categoryModel->onlyTrashed()->firstWhere($where);

        try {
            
            $checkSlug = $this->getCategory(['slug' => $category['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/link.category.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $category->menus()->restore();
            $category->restore();

            return $this->success($category, __('global.alert.restore_success', [
                'attribute' => __('module/link.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Category (Permanent)
     * @param array $where
     */
    public function deleteCategory($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $category = $this->categoryModel->onlyTrashed()->firstWhere($where);
        } else {
            $category = $this->getCategory($where);
        }

        try {
            
            $category->menus()->forceDelete();
            $category->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/link.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //---------------------------
    // LINK MEDIA
    //---------------------------

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

        if (isset($filter['link_category_id']))
            $media->where('link_category_id', $filter['link_category_id']);

        if (isset($filter['publish']))
            $media->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $media->where('public', $filter['public']);

        if (isset($filter['approved']))
            $media->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $media->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $media->when($filter['q'], function ($media, $q) {
                $media->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
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
        
        $result = $media->firstWhere($where);;

        return $result;
    }

    /**
     * Create Media
     * @param array $data
     */
    public function storeMedia($data)
    {
        try {

            $media = new LinkMedia;
            $media->link_category_id = $data['link_category_id'];
            $this->setFieldMedia($data, $media);
            $media->position = $this->mediaModel->where('link_category_id', $data['link_category_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.link.media.approval') == true) {
                    $media->approved = 2;
                }
                $media->created_by = Auth::user()['id'];

            $media->save();

            return $this->success($media,  __('global.alert.create_success', [
                'attribute' => __('module/link.media.caption')
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
    public function updateMedia($data, $where)
    {
        $media = $this->getMedia($where);

        try {
            
            $this->setFieldMedia($data, $media);
            if (Auth::guard()->check())
                $media->updated_by = Auth::user()['id'];

            $media->save();

            return $this->success($media,  __('global.alert.update_success', [
                'attribute' => __('module/link.media.caption')
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
    private function setFieldMedia($data, $media)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('cms.module.feature.language.default');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $title[$value['iso_codes']] = ($data['title_'.$value['iso_codes']] == null) ?
                $data['title_'.$langDefault] : $data['title_'.$value['iso_codes']];

            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];
        }

        $media->title = $title;
        $media->description = $description;
        $media->url = $data['url'];
        $media->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $media->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $media->publish = (bool)$data['publish'];
        $media->public = (bool)$data['public'];
        $media->locked = (bool)$data['locked'];
        $media->config = [
            'hide_description' => (bool)$data['hide_description'],
            'hide_cover' => (bool)$data['hide_cover'],
            'hide_banner' => (bool)$data['hide_banner'],
            'is_embed' => (bool)$data['is_embed']
        ];

        return $media;
    }

     /**
     * Status Media (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusMedia($field, $where)
    {
        $media = $this->getMedia($where);

        try {
            
            $media->update([
                $field => !$media[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $media['updated_by'],
            ]);

            return $this->success($media, __('global.alert.update_success', [
                'attribute' => __('module/link.media.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Media
     * @param array $where
     * @param int $position
     */
    public function positionMedia($where, $position)
    {
        $media = $this->getMedia($where);
        
        try {

            if ($position >= 1) {
    
                $this->mediaModel->where('link_category_id', $media['link_category_id'])
                    ->where('position', $position)->update([
                    'position' => $media['position'],
                ]);
    
                $media->position = $position;
                if (Auth::guard()->check()) {
                    $media->updated_by = Auth::user()['id'];
                }
                $media->save();
    
                return $this->success($media, __('global.alert.update_success', [
                    'attribute' => __('module/link.media.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/link.media.caption')
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
    public function trashMedia($where)
    {
        $media = $this->getMedia($where);

        try {

            if ($media['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $media['created_by']) {
                        return $this->error($media,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/link.media.caption')
                        ]));
                    }

                    $media->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $media->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/link.media.caption')
                ]));

            } else {

                return $this->error($media,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/link.media.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Media
     * @param array $where
     */
    public function restoreMedia($where)
    {
        $media = $this->mediaModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $media->restore();

            return $this->success($media, __('global.alert.restore_success', [
                'attribute' => __('module/link.media.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Media (Permanent)
     * @param array $where
     */
    public function deleteMedia($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $media = $this->mediaModel->onlyTrashed()->firstWhere($where);
        } else {
            $media = $this->getMedia($where);
        }

        try {

            $media->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/link.media.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}