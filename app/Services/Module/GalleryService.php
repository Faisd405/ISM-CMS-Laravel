<?php

namespace App\Services\Module;

use App\Models\Module\Gallery\GalleryAlbum;
use App\Models\Module\Gallery\GalleryCategory;
use App\Models\Module\Gallery\GalleryFile;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryService
{
    use ApiResponser;

    private $categoryModel, $albumModel, $fileModel, $language;


    public function __construct(
        GalleryCategory $categoryModel,
        GalleryAlbum $albumModel,
        GalleryFile $fileModel,
        LanguageService $language
    )
    {
        $this->categoryModel = $categoryModel;
        $this->albumModel = $albumModel;
        $this->fileModel = $fileModel;
        $this->language = $language;
    }

    //---------------------------
    // GALLERY CATEGORY
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

            $category = new GalleryCategory;
            $this->setFieldCategory($data, $category);
            $category->position = $this->categoryModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.gallery.category.approval') == true) {
                    $category->approved = 2;
                }
                $category->created_by = Auth::user()['id'];

            $category->save();

            return $this->success($category,  __('global.alert.create_success', [
                'attribute' => __('module/gallery.category.caption')
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
                'attribute' => __('module/gallery.category.caption')
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
        $category->image_preview = [
            'filepath' => Str::replace(url('/storage'), '', $data['image_file']) ?? null,
            'title' => $data['image_title'] ?? null,
            'alt' => $data['image_alt'] ?? null,
        ];
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
        $category->template_list_id = $data['template_list_id'] ?? null;
        $category->template_detail_id = $data['template_detail_id'] ?? null;

        if (isset($data['cf_name'])) {
            
            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $category->custom_fields = $customField;
        } else {
            $category->custom_fields = null;
        }

        $category->album_perpage = $data['album_perpage'] ?? 0;
        $category->file_perpage = $data['file_perpage'] ?? 0;

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
                'attribute' => __('module/gallery.category.caption')
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
                    'attribute' => __('module/gallery.category.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/gallery.category.caption')
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
            
            $albums = $category->albums()->count();
            $files = $category->files()->count();

            if ($category['locked'] == 0 && $albums == 0 && $files == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $category['created_by']) {
                        return $this->error($category,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/gallery.category.caption')
                        ]));
                    }

                    $category->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $category->menus()->delete();
                $category->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/gallery.category.caption')
                ]));
    
            } else {
                return $this->error($category,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/gallery.category.caption')
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
                    'attribute' => __('module/gallery.category.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $category->menus()->restore();
            $category->restore();

            return $this->success($category, __('global.alert.restore_success', [
                'attribute' => __('module/gallery.category.caption')
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
                'attribute' => __('module/gallery.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //---------------------------
    // GALLERY ALBUM
    //---------------------------

    /**
     * Get Album List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getAlbumList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $album = $this->albumModel->query();

        if ($isTrash == true)
            $album->onlyTrashed();

        if (isset($filter['gallery_category_id']))
            $album->where('gallery_category_id', $filter['gallery_category_id']);

        if (isset($filter['publish']))
            $album->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $album->where('public', $filter['public']);

        if (isset($filter['approved']))
            $album->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $album->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $album->when($filter['q'], function ($album, $q) {
                $album->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $album->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $album->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $album->paginate($limit);
        } else {
            $result = $album->get();
        }
        
        return $result;
    }

    /**
     * Get Album One
     * @param array $where
     * @param array $with
     */
    public function getAlbum($where, $with = [])
    {
        $album = $this->albumModel->query();
        
        if (!empty($with))
            $album->with($with);
        
        $result = $album->firstWhere($where);;

        return $result;
    }

    /**
     * Create Album
     * @param array $data
     */
    public function storeAlbum($data)
    {
        try {

            $album = new GalleryAlbum;
            $this->setFieldAlbum($data, $album);
            $album->position = $this->albumModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.gallery.album.approval') == true) {
                    $album->approved = 2;
                }
                $album->created_by = Auth::user()['id'];

            $album->save();

            return $this->success($album,  __('global.alert.create_success', [
                'attribute' => __('module/gallery.album.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Album
     * @param array $data
     * @param array $where
     */
    public function updateAlbum($data, $where)
    {
        $album = $this->getAlbum($where);

        try {
            
            $this->setFieldAlbum($data, $album);
            if (Auth::guard()->check())
                $album->updated_by = Auth::user()['id'];

            $album->save();

            return $this->success($album,  __('global.alert.update_success', [
                'attribute' => __('module/gallery.album.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Album
     * @param array $data
     * @param model $album
     */
    private function setFieldAlbum($data, $album)
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

        if (isset($data['gallery_category_id'])) {
            $album->gallery_category_id = $data['gallery_category_id'];
        }

        $album->slug = Str::slug($data['slug'], '-');
        $album->name = $name;
        $album->description = $description;
        $album->image_preview = [
            'filepath' => Str::replace(url('/storage'), '', $data['image_file']) ?? null,
            'title' => $data['image_title'] ?? null,
            'alt' => $data['image_alt'] ?? null,
        ];
        $album->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $album->publish = (bool)$data['publish'];
        $album->public = (bool)$data['public'];
        $album->locked = (bool)$data['locked'];
        $album->config = [
            'is_detail' => (bool)$data['is_detail'],
            'hide_description' => (bool)$data['hide_description'],
            'hide_banner' => (bool)$data['hide_banner'],
        ];
        $album->template_id = $data['template_id'] ?? null;

        if (isset($data['cf_name'])) {
            
            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $album->custom_fields = $customField;
        } else {
            $album->custom_fields = null;
        }

        $album->file_perpage = $data['file_perpage'] ?? 0;

        return $album;
    }

    /**
     * Status Album (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusAlbum($field, $where)
    {
        $album = $this->getAlbum($where);

        try {
            
            $album->update([
                $field => !$album[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $album['updated_by'],
            ]);

            if ($field == 'publish') {
                $album->menus()->update([
                    'publish' => $album['publish']
                ]);
            }

            return $this->success($album, __('global.alert.update_success', [
                'attribute' => __('module/gallery.album.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Album
     * @param array $where
     * @param int $position
     */
    public function positionAlbum($where, $position)
    {
        $album = $this->getAlbum($where);
        
        try {

            if ($position >= 1) {
    
                $this->albumModel->where('position', $position)->update([
                    'position' => $album['position'],
                ]);
    
                $album->position = $position;
                if (Auth::guard()->check()) {
                    $album->updated_by = Auth::user()['id'];
                }
                $album->save();
    
                return $this->success($album, __('global.alert.update_success', [
                    'attribute' => __('module/gallery.album.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/gallery.album.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Record Album Hits
     * @param array $where
     */
    public function recordAlbumHits($where)
    {
        $album = $this->getAlbum($where);
        $album->update([
            'hits' => ($album->hits+1)
        ]);

        return $album;
    }

    /**
     * Trash Album
     * @param array $where
     */
    public function trashAlbum($where)
    {
        $album = $this->getAlbum($where);

        try {
            
            $files = $album->files()->count();

            if ($album['locked'] == 0 && $files == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $album['created_by']) {
                        return $this->error($album,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/gallery.album.caption')
                        ]));
                    }

                    $album->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $album->menus()->delete();
                $album->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/gallery.album.caption')
                ]));
    
            } else {
                return $this->error($album,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/gallery.album.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Album
     * @param array $where
     */
    public function restoreAlbum($where)
    {
        $album = $this->albumModel->onlyTrashed()->firstWhere($where);

        try {
            
            $checkSlug = $this->getAlbum(['slug' => $album['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/gallery.album.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $album->menus()->restore();
            $album->restore();

            return $this->success($album, __('global.alert.restore_success', [
                'attribute' => __('module/gallery.album.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Album (Permanent)
     * @param array $where
     */
    public function deleteAlbum($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $album = $this->albumModel->onlyTrashed()->firstWhere($where);
        } else {
            $album = $this->getAlbum($where);
        }

        try {
                
            $album->menus()->forceDelete();
            $album->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/gallery.album.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //---------------------------
    // GALLERY FILE
    //---------------------------

    /**
     * Get File List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getFileList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $file = $this->fileModel->query();

        if ($isTrash == true)
            $file->onlyTrashed();

        if (isset($filter['gallery_category_id']))
            $file->where('gallery_category_id', $filter['gallery_category_id']);

        if (isset($filter['gallery_album_id']))
            $file->where('gallery_album_id', $filter['gallery_album_id']);

        if (isset($filter['type']))
            $file->where('type', $filter['type']);

        if (isset($filter['publish']))
            $file->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $file->where('public', $filter['public']);

        if (isset($filter['approved']))
            $file->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $file->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $file->when($filter['q'], function ($file, $q) {
                $file->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $file->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $file->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $file->paginate($limit);
        } else {
            $result = $file->get();
        }
        
        return $result;
    }

    /**
     * Get File One
     * @param array $where
     * @param array $with
     */
    public function getFile($where, $with = [])
    {
        $file = $this->fileModel->query();
        
        if (!empty($with))
            $file->with($with);
        
        $result = $file->firstWhere($where);;

        return $result;
    }

    /**
     * Create File
     * @param array $data
     */
    public function storeFile($data)
    {
        $album = $this->getAlbum(['id' => $data['gallery_album_id']]);

        try {

            $type = $data['type'];
            $imageType = $data['image_type'];
            $videoType = $data['video_type'];

            $galleryFile = new GalleryFile;
            $galleryFile->gallery_category_id = $album['gallery_category_id'];
            $galleryFile->gallery_album_id = $data['gallery_album_id'];

            $galleryFile->type = $type;
            if ($type == '0') {
                $galleryFile->image_type = $imageType;

                if ($imageType == '0') {
                    $file = $data['file_image'];
                    $fileName = $file->getClientOriginalName();
                    if (file_exists(storage_path('app/public/gallery/'.$data['gallery_album_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                    }
        
                    Storage::put(config('cms.files.gallery.path').$data['gallery_album_id'].'/'.$fileName, 
                        file_get_contents($file));
                    
                    $galleryFile->file = $fileName;
                }

                if ($imageType == '1') {
                    $galleryFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
                }

                if ($imageType == '2') {
                    $galleryFile->file = $data['file_url'];
                }
            }

            if ($type == '1') {
                $galleryFile->video_type = $videoType;

                if ($videoType == '0') {
                    $file = $data['file_video'];
                    $fileName = $file->getClientOriginalName();
                    if (file_exists(storage_path('app/public/gallery/'.$data['gallery_album_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                    }
        
                    Storage::put(config('cms.files.gallery.path').$data['gallery_album_id'].'/'.$fileName, 
                        file_get_contents($file));
                    
                    $galleryFile->file = $fileName;

                    if (isset($data['thumbnail'])) {
                        
                        $fileThumb = $data['thumbnail'];
                        $fileNameThumb = $fileThumb->getClientOriginalName();
                        if (file_exists(storage_path('app/public/gallery/thumbnail/'.$data['gallery_album_id'].'/'.$fileNameThumb))) {
                            $fileNameThumb = Str::random(3).'-'.$fileThumb->getClientOriginalName();
                        }
            
                        Storage::put(config('cms.files.gallery.thumbnail.path').$data['gallery_album_id'].'/'.$fileNameThumb, 
                            file_get_contents($fileThumb));

                        $galleryFile->thumbnail = $fileNameThumb;
                    }

                }

                if ($videoType == '1') {
                    $galleryFile->file = $data['file_youtube'];
                }
            }

            $this->setFieldFile($data, $galleryFile);
            $galleryFile->position = $this->fileModel->where('gallery_album_id', $data['gallery_album_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.gallery.file.approval') == true) {
                    $galleryFile->approved = 2;
                }
                $galleryFile->created_by = Auth::user()['id'];

            $galleryFile->save();

            return $this->success($galleryFile,  __('global.alert.create_success', [
                'attribute' => __('module/gallery.file.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Create File Multiple
     * @param array $data
     */
    public function storeFileMultiple($data)
    {
        $album = $this->getAlbum(['id' => $data['gallery_album_id']]);

        try {

            $galleryFile = new GalleryFile;
            $galleryFile->gallery_category_id = $album['gallery_category_id'];
            $galleryFile->gallery_album_id = $data['gallery_album_id'];

            $galleryFile->type = '0';
            $galleryFile->image_type = '0';
            
            $file = $data['file'];
            $fileName = $file->getClientOriginalName();
            if (file_exists(storage_path('app/public/gallery/'.$data['gallery_album_id'].'/'.$fileName))) {
                $fileName = Str::random(3).'-'.$file->getClientOriginalName();
            }

            Storage::put(config('cms.files.gallery.path').$data['gallery_album_id'].'/'.$fileName, 
                file_get_contents($file));
            
            $galleryFile->file = $fileName;

            $this->setFieldFile($data, $galleryFile);
            $galleryFile->position = $this->fileModel->where('gallery_album_id', (int)$data['gallery_album_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.gallery.file.approval') == true) {
                    $galleryFile->approved = 2;
                }
                $galleryFile->created_by = Auth::user()['id'];

            $galleryFile->save();

            return $this->success($galleryFile,  __('global.alert.create_success', [
                'attribute' => __('module/gallery.file.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update File
     * @param array $data
     * @param array $where
     */
    public function updateFile($data, $where)
    {
        $galleryFile = $this->getFile($where);

        try {

            if ($galleryFile['type'] == '0') {
                
                if ($galleryFile['image_type'] == '0' && isset($data['file_image'])) {
                    $file = $data['file_image'];
                    $fileName = $file->getClientOriginalName();
                    if (file_exists(storage_path('app/public/gallery/'.$galleryFile['gallery_album_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                    }

                    Storage::delete(config('cms.files.gallery.path').$galleryFile['gallery_album_id'].
                        '/'.$data['old_file']);
        
                    Storage::put(config('cms.files.gallery.path').$galleryFile['gallery_album_id'].'/'.$fileName, 
                        file_get_contents($file));
                    
                    $galleryFile->file = $fileName;
                }

                if ($galleryFile['image_type'] == '1') {
                    $galleryFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
                }

                if ($galleryFile['image_type'] == '2') {
                    $galleryFile->file = $data['file_url'];
                }
            }

            if ($galleryFile['type'] == '1') {
                
                if ($galleryFile['video_type'] == '0') {

                    if (isset($data['file_video'])) {
                        $file = $data['file_video'];
                        $fileName = $file->getClientOriginalName();
                        if (file_exists(storage_path('app/public/gallery/'.$galleryFile['gallery_album_id'].'/'.$fileName))) {
                            $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                        }

                        Storage::delete(config('cms.files.gallery.path').$galleryFile['gallery_album_id'].
                            '/'.$data['old_file']);
                        
                        Storage::put(config('cms.files.gallery.path').$galleryFile['gallery_album_id'].'/'.$fileName, 
                            file_get_contents($file));
                        
                        $galleryFile->file = $fileName;
                    }

                    if (isset($data['thumbnail'])) {
                        
                        $fileThumb = $data['thumbnail'];
                        $fileNameThumb = $fileThumb->getClientOriginalName();
                        if (file_exists(storage_path('app/public/gallery/thumbnail/'.$galleryFile['gallery_album_id'].'/'.$fileNameThumb))) {
                            $fileNameThumb = Str::random(3).'-'.$fileThumb->getClientOriginalName();
                        }

                        Storage::delete(config('cms.files.gallery.thumbnail.path').$galleryFile['gallery_album_id'].
                            '/'.$data['old_thumbnail']);
            
                        Storage::put(config('cms.files.gallery.thumbnail.path').$galleryFile['gallery_album_id'].'/'.$fileNameThumb, 
                            file_get_contents($fileThumb));

                        $galleryFile->thumbnail = $fileNameThumb;
                    }
                }

                if ($galleryFile['video_type'] == '1') {
                    $galleryFile->file = $data['file_youtube'];
                }
            }
            
            $this->setFieldFile($data, $galleryFile);
            if (Auth::guard()->check())
                $galleryFile->updated_by = Auth::user()['id'];

            $galleryFile->save();

            return $this->success($galleryFile,  __('global.alert.update_success', [
                'attribute' => __('module/gallery.file.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field File
     * @param array $data
     * @param model $galleryFile
     */
    private function setFieldFile($data, $galleryFile)
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

        $galleryFile->title = $title;
        $galleryFile->description = $description;
        $galleryFile->publish = (bool)$data['publish'];
        $galleryFile->public = (bool)$data['public'];
        $galleryFile->locked = (bool)$data['locked'];
        $galleryFile->config = [
            'hide_title' => (bool)$data['hide_title'],
            'hide_description' => (bool)$data['hide_description']
        ];

        return $galleryFile;
    }

    /**
     * Status File (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusFile($field, $where)
    {
        $file = $this->getFile($where);

        try {
            
            $file->update([
                $field => !$file[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $file['updated_by'],
            ]);

            return $this->success($file, __('global.alert.update_success', [
                'attribute' => __('module/gallery.file.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position File
     * @param array $where
     * @param int $position
     */
    public function positionFile($where, $position)
    {
        $galleryFile = $this->getFile($where);
        
        try {

            if ($position >= 1) {
    
                $this->fileModel->where('gallery_album_id', $galleryFile['gallery_album_id'])
                    ->where('position', $position)->update([
                    'position' => $galleryFile['position'],
                ]);
    
                $galleryFile->position = $position;
                if (Auth::guard()->check()) {
                    $galleryFile->updated_by = Auth::user()['id'];
                }
                $galleryFile->save();
    
                return $this->success($galleryFile, __('global.alert.update_success', [
                    'attribute' => __('module/gallery.file.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/gallery.file.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash File
     * @param array $where
     */
    public function trashFile($where)
    {
        $galleryFile = $this->getFile($where);

        try {

            if ($galleryFile['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $galleryFile['created_by']) {
                        return $this->error($galleryFile,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/gallery.file.caption')
                        ]));
                    }

                    $galleryFile->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $galleryFile->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/gallery.file.caption')
                ]));

            } else {

                return $this->error($galleryFile,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/gallery.file.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore File
     * @param array $where
     */
    public function restoreFile($where)
    {
        $galleryFile = $this->fileModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $galleryFile->restore();

            return $this->success($galleryFile, __('global.alert.restore_success', [
                'attribute' => __('module/gallery.file.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete File (Permanent)
     * @param array $where
     */
    public function deleteFile($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $galleryFile = $this->fileModel->onlyTrashed()->firstWhere($where);
        } else {
            $galleryFile = $this->getFile($where);
        }

        try {
            
            if ($galleryFile['type'] == '0' && $galleryFile['image_type'] == '0') {
                Storage::delete(config('cms.files.gallery.path').$galleryFile['gallery_album_id'].
                 '/'.$galleryFile['file']);
            }

            if ($galleryFile['type'] == '1' && $galleryFile['video_type'] == '0') {
                Storage::delete(config('cms.files.gallery.path').$galleryFile['gallery_album_id'].
                    '/'.$galleryFile['file']);
                
                if (!empty($galleryFile['thumbnail'])) {
                    Storage::delete(config('cms.files.gallery.thumbnail.path').$galleryFile['gallery_album_id'].
                        '/'.$galleryFile['thumbnail']);
                }
            }

            $galleryFile->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/gallery.file.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}