<?php

namespace App\Services\Module;

use App\Models\Module\Banner\Banner;
use App\Models\Module\Banner\BannerCategory;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerService
{
    use ApiResponser;

    private $bannerCategoryModel, $bannerModel, $language;

    public function __construct(
        BannerCategory $bannerCategoryModel,
        Banner $bannerModel,
        LanguageService $language
    )
    {
        $this->bannerCategoryModel = $bannerCategoryModel;
        $this->bannerModel = $bannerModel;
        $this->language = $language;
    }

    //---------------------------
    // CATEGORY
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
        $category = $this->bannerCategoryModel->query();

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
        $category = $this->bannerCategoryModel->query();
        
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


            $category = new BannerCategory;
            $this->setFieldCategory($data, $category);

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.banner.category.approval') == true) {
                    $category->approved = 2;
                }
                $category->created_by = Auth::user()['id'];

            $category->save();

            return $this->success($category,  __('global.alert.create_success', [
                'attribute' => __('module/banner.category.caption')
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
                'attribute' => __('module/banner.category.caption')
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

        $category->name = $name;
        $category->description = $description;
        $category->banner_perpage = $data['banner_perpage'] ?? 0;
        $category->publish = (bool)$data['publish'];
        $category->locked = (bool)$data['locked'];
        $category->config = [
            'hide_description' => (bool)$data['hide_description'],
        ];

        if (isset($data['cf_name'])) {
            
            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $category->custom_fields = $customField;
        } else {
            $category->custom_fields = null;
        }

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

            return $this->success($category, __('global.alert.update_success', [
                'attribute' => __('module/banner.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Category
     * @param array $where
     */
    public function trashCategory($where)
    {
        $category = $this->getCategory($where);

        try {
            
            $banners = $category->banners()->count();

            if ($category['locked'] == 0 && $banners == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $category['created_by']) {
                        return $this->error($category,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/banner.category.caption')
                        ]));
                    }

                    $category->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $category->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/banner.category.caption')
                ]));
    
            } else {
                return $this->error($category,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/banner.category.caption')
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
        $category = $this->bannerCategoryModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $category->restore();

            return $this->success($category, __('global.alert.restore_success', [
                'attribute' => __('module/banner.category.caption')
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
            $category = $this->bannerCategoryModel->onlyTrashed()->firstWhere($where);
        } else {
            $category = $this->getCategory($where);
        }

        try {
                
            $category->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/banner.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //---------------------------
    // BANNER
    //---------------------------

    /**
     * Get Banner List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getBannerList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $banner = $this->bannerModel->query();

        if ($isTrash == true)
            $banner->onlyTrashed();

        if (isset($filter['category_id']))
            $banner->where('banner_category_id', $filter['category_id']);

        if (isset($filter['type']))
            $banner->where('type', $filter['type']);

        if (isset($filter['publish']))
            $banner->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $banner->where('public', $filter['public']);

        if (isset($filter['approved']))
            $banner->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $banner->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $banner->when($filter['q'], function ($banner, $q) {
                $banner->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $banner->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $banner->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $banner->paginate($limit);
        } else {
            $result = $banner->get();
        }
        
        return $result;
    }

    /**
     * Get Banner One
     * @param array $where
     * @param array $with
     */
    public function getBanner($where, $with = [])
    {
        $banner = $this->bannerModel->query();
        
        if (!empty($with))
            $banner->with($with);
        
        $result = $banner->firstWhere($where);;

        return $result;
    }

    /**
     * Create Banner
     * @param array $data
     */
    public function storeBanner($data)
    {
        try {

            $type = $data['type'];
            $imageType = $data['image_type'];
            $videoType = $data['video_type'];

            $banner = new Banner;
            $banner->banner_category_id = $data['category_id'];

            $banner->type = $type;
            if ($type == '0') {
                $banner->image_type = $imageType;

                if ($imageType == '0') {
                    $file = $data['file_image'];
                    $fileName = $file->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/'.$data['category_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                    }
        
                    Storage::put(config('cms.files.banner.path').$data['category_id'].'/'.$fileName, 
                        file_get_contents($file));
                    
                    $banner->file = $fileName;
                }

                if ($imageType == '1') {
                    $banner->file = Str::replace(url('/storage'), '', $data['filemanager']);
                }

                if ($imageType == '2') {
                    $banner->file = $data['file_url'];
                }
            }

            if ($type == '1') {
                $banner->video_type = $videoType;

                if ($videoType == '0') {
                    $file = $data['file_video'];
                    $fileName = $file->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/'.$data['category_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                    }
        
                    Storage::put(config('cms.files.banner.path').$data['category_id'].'/'.$fileName, 
                        file_get_contents($file));
                    
                    $banner->file = $fileName;

                    if (isset($data['thumbnail'])) {
                        
                        $fileThumb = $data['thumbnail'];
                        $fileNameThumb = $fileThumb->getClientOriginalName();
                        if (file_exists(storage_path('app/public/banner/thumbnail/'.$data['category_id'].'/'.$fileNameThumb))) {
                            $fileNameThumb = Str::random(3).'-'.$fileThumb->getClientOriginalName();
                        }
            
                        Storage::put(config('cms.files.banner.thumbnail.path').$data['category_id'].'/'.$fileNameThumb, 
                            file_get_contents($fileThumb));

                        $banner->thumbnail = $fileNameThumb;
                    }

                }

                if ($videoType == '1') {
                    $banner->file = $data['file_youtube'];
                }
            }

            $this->setFieldBanner($data, $banner);
            $banner->position = $this->bannerModel->where('banner_category_id', (int)$data['category_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.banner.approval') == true) {
                    $banner->approved = 2;
                }
                $banner->created_by = Auth::user()['id'];

            $banner->save();

            return $this->success($banner,  __('global.alert.create_success', [
                'attribute' => __('module/banner.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Create Banner Multiple
     * @param array $data
     */
    public function storeBannerMultiple($data)
    {
        try {

            $banner = new Banner;
            $banner['banner_category_id'] = $data['category_id'];

            $banner->type = '0';
            $banner->image_type = '0';
            
            $file = $data['file'];
            $fileName = $file->getClientOriginalName();
            if (file_exists(storage_path('app/public/banner/'.$data['category_id'].'/'.$fileName))) {
                $fileName = Str::random(3).'-'.$file->getClientOriginalName();
            }

            Storage::put(config('cms.files.banner.path').$data['category_id'].'/'.$fileName, 
                file_get_contents($file));
            
            $banner->file = $fileName;

            $this->setFieldBanner($data, $banner);
            $banner->position = $this->bannerModel->where('banner_category_id', (int)$data['category_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.banner.approval') == true) {
                    $banner->approved = 2;
                }
                $banner->created_by = Auth::user()['id'];

            $banner->save();

            return $this->success($banner,  __('global.alert.create_success', [
                'attribute' => __('module/banner.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Banner
     * @param array $data
     * @param array $where
     */
    public function updateBanner($data, $where)
    {
        $banner = $this->getBanner($where);

        try {

            if ($banner['type'] == '0') {
                
                if ($banner['image_type'] == '0' && isset($data['file_image'])) {
                    $file = $data['file_image'];
                    $fileName = $file->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/'.$banner['banner_category_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                    }

                    Storage::delete(config('cms.files.banner.path').$banner['banner_category_id'].
                        '/'.$data['old_file']);
        
                    Storage::put(config('cms.files.banner.path').$banner['banner_category_id'].'/'.$fileName, 
                        file_get_contents($file));
                    
                    $banner->file = $fileName;
                }

                if ($banner['image_type'] == '1') {
                    $banner->file = Str::replace(url('/storage'), '', $data['filemanager']);
                }

                if ($banner['image_type'] == '2') {
                    $banner->file = $data['file_url'];
                }
            }

            if ($banner['type'] == '1') {
                
                if ($banner['video_type'] == '0') {

                    if (isset($data['file_video'])) {
                        $file = $data['file_video'];
                        $fileName = $file->getClientOriginalName();
                        if (file_exists(storage_path('app/public/banner/'.$banner['banner_category_id'].'/'.$fileName))) {
                            $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                        }

                        Storage::delete(config('cms.files.banner.path').$banner['banner_category_id'].
                            '/'.$data['old_file']);
                        
                        Storage::put(config('cms.files.banner.path').$banner['banner_category_id'].'/'.$fileName, 
                            file_get_contents($file));
                        
                        $banner->file = $fileName;
                    }

                    if (isset($data['thumbnail'])) {
                        
                        $fileThumb = $data['thumbnail'];
                        $fileNameThumb = $fileThumb->getClientOriginalName();
                        if (file_exists(storage_path('app/public/banner/thumbnail/'.$banner['banner_category_id'].'/'.$fileNameThumb))) {
                            $fileNameThumb = Str::random(3).'-'.$fileThumb->getClientOriginalName();
                        }

                        Storage::delete(config('cms.files.banner.thumbnail.path').$banner['banner_category_id'].
                            '/'.$data['old_thumbnail']);
            
                        Storage::put(config('cms.files.banner.thumbnail.path').$banner['banner_category_id'].'/'.$fileNameThumb, 
                            file_get_contents($fileThumb));

                        $banner->thumbnail = $fileNameThumb;
                    }
                }

                if ($banner['video_type'] == '1') {
                    $banner->file = $data['file_youtube'];
                }
            }
            
            $this->setFieldBanner($data, $banner);
            if (Auth::guard()->check())
                $banner->updated_by = Auth::user()['id'];

            $banner->save();

            return $this->success($banner,  __('global.alert.update_success', [
                'attribute' => __('module/banner.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Banner
     * @param array $data
     * @param model $banner
     */
    private function setFieldBanner($data, $banner)
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

        $banner->title = $title;
        $banner->description = $description;
        $banner->publish = (bool)$data['publish'];
        $banner->public = (bool)$data['public'];
        $banner->locked = (bool)$data['locked'];
        $banner->config = [
            'hide_title' => (bool)$data['hide_title'],
            'hide_description' => (bool)$data['hide_description'],
        ];
        $banner->url = $data['url'] ?? null;

        return $banner;
    }

    /**
     * Status Banner (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusBanner($field, $where)
    {
        $banner = $this->getBanner($where);

        try {
            
            $banner->update([
                $field => !$banner[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $banner['updated_by'],
            ]);

            return $this->success($banner, __('global.alert.update_success', [
                'attribute' => __('module/banner.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Banner
     * @param array $where
     * @param int $position
     */
    public function positionBanner($where, $position)
    {
        $banner = $this->getBanner($where);
        
        try {

            if ($position >= 1) {
    
                $this->bannerModel->where('banner_category_id', $banner['banner_category_id'])
                    ->where('position', $position)->update([
                    'position' => $banner['position'],
                ]);
    
                $banner->position = $position;
                if (Auth::guard()->check()) {
                    $banner->updated_by = Auth::user()['id'];
                }
                $banner->save();
    
                return $this->success($banner, __('global.alert.update_success', [
                    'attribute' => __('module/banner.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/banner.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Banner
     * @param array $where
     */
    public function trashBanner($where)
    {
        $banner = $this->getBanner($where);

        try {

            if ($banner['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $banner['created_by']) {
                        return $this->error($banner,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/banner.caption')
                        ]));
                    }

                    $banner->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $banner->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/banner.caption')
                ]));
            
            } else {

                return $this->error($banner,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/banner.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Banner
     * @param array $where
     */
    public function restoreBanner($where)
    {
        $banner = $this->bannerModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $banner->restore();

            return $this->success($banner, __('global.alert.restore_success', [
                'attribute' => __('module/banner.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Banner (Permanent)
     * @param array $where
     */
    public function deleteBanner($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $banner = $this->bannerModel->onlyTrashed()->firstWhere($where);
        } else {
            $banner = $this->getBanner($where);
        }

        try {
            
            if ($banner['type'] == '0' && $banner['image_type'] == '0') {
                Storage::delete(config('cms.files.banner.path').$banner['banner_category_id'].
                 '/'.$banner['file']);
            }

            if ($banner['type'] == '1' && $banner['video_type'] == '0') {
                Storage::delete(config('cms.files.banner.path').$banner['banner_category_id'].
                    '/'.$banner['file']);
                
                if (!empty($banner['thumbnail'])) {
                    Storage::delete(config('cms.files.banner.thumbnail.path').$banner['banner_category_id'].
                        '/'.$banner['thumbnail']);
                }
            }

            $banner->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/banner.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}