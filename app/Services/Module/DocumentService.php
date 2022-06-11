<?php

namespace App\Services\Module;

use App\Models\Module\Document\DocumentCategory;
use App\Models\Module\Document\DocumentFile;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    use ApiResponser;

    private $categoryModel, $fileModel, $language;

    public function __construct(
        DocumentCategory $categoryModel,
        DocumentFile $fileModel,
        LanguageService $language
    )
    {
        $this->categoryModel = $categoryModel;
        $this->fileModel = $fileModel;
        $this->language = $language;
    }

    //---------------------------
    // DOCUMENT CATEGORY
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

            $category = new DocumentCategory;
            $this->setFieldCategory($data, $category);
            $category->position = $this->categoryModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.document.category.approval') == true) {
                    $category->approved = 2;
                }
                $category->created_by = Auth::user()['id'];

            $category->save();

            return $this->success($category,  __('global.alert.create_success', [
                'attribute' => __('module/document.category.caption')
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
                'attribute' => __('module/document.category.caption')
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
        if (isset($data['roles'])) {
            $category->roles = $data['roles'];
        }
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
                $category->widgets()->update([
                    'publish' => $category['publish']
                ]);
            }

            return $this->success($category, __('global.alert.update_success', [
                'attribute' => __('module/document.category.caption')
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
                    'attribute' => __('module/document.category.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/document.category.caption')
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
            
            $files = $category->files()->count();

            if ($category['locked'] == 0 && $files == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $category['created_by']) {
                        return $this->error($category,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/document.category.caption')
                        ]));
                    }

                    $category->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $category->menus()->delete();
                $category->widgets()->delete();
                $category->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/document.category.caption')
                ]));
    
            } else {
                return $this->error($category,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/document.category.caption')
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
                    'attribute' => __('module/document.category.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $category->menus()->restore();
            $category->widgets()->restore();
            $category->restore();

            return $this->success($category, __('global.alert.restore_success', [
                'attribute' => __('module/document.category.caption')
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
            $category->widgets()->forceDelete();
            $category->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/document.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Check role file
     * @param array $where
     * @param int $role
     */
    public function checkRole($where, $role)
    {
        $query = $this->categoryModel->query();

        $query->where($where);
        $query->whereJsonContains('roles', $role);

        $result = $query->count();

        return $result;
    }

    //---------------------------
    // DOCUMENT FILE
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

        if (isset($filter['document_category_id']))
            $file->where('document_category_id', $filter['document_category_id']);

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
        try {

            $type = $data['type'];

            $documentFile = new DocumentFile;
            $documentFile->document_category_id = $data['document_category_id'];

            $documentFile->type = $type;
            if ($type == '0') {
                $file = $data['file_document'];
                $fileName = $file->getClientOriginalName();
                if (file_exists(storage_path('app/public/document/'.$data['document_category_id'].'/'.$fileName))) {
                    $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                }
    
                Storage::put(config('cms.files.document.path').$data['document_category_id'].'/'.$fileName, 
                    file_get_contents($file));
                
                $documentFile->file = $fileName;
            }

            if ($type == '1') {
                $documentFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
            }

            if ($type == '2') {
                $documentFile->file = $data['file_url'];
            }

            $this->setFieldFile($data, $documentFile);
            $documentFile->position = $this->fileModel->where('document_category_id', $data['document_category_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.document.file.approval') == true) {
                    $documentFile->approved = 2;
                }
                $documentFile->created_by = Auth::user()['id'];

            $documentFile->save();

            return $this->success($documentFile,  __('global.alert.create_success', [
                'attribute' => __('module/document.file.caption')
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
        try {

            $documentFile = new DocumentFile;
            $documentFile->document_category_id = $data['document_category_id'];

            $documentFile->type = '0';
            
            $file = $data['file'];
            $fileName = $file->getClientOriginalName();
            if (file_exists(storage_path('app/public/document/'.$data['document_category_id'].'/'.$fileName))) {
                $fileName = Str::random(3).'-'.$file->getClientOriginalName();
            }

            Storage::put(config('cms.files.document.path').$data['document_category_id'].'/'.$fileName, 
                file_get_contents($file));
            
            $documentFile->file = $fileName;

            $this->setFieldFile($data, $documentFile);
            $documentFile->position = $this->fileModel->where('document_category_id', (int)$data['document_category_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.document.file.approval') == true) {
                    $documentFile->approved = 2;
                }
                $documentFile->created_by = Auth::user()['id'];

            $documentFile->save();

            return $this->success($documentFile,  __('global.alert.create_success', [
                'attribute' => __('module/document.file.caption')
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
        $documentFile = $this->getFile($where);

        try {
                
            if ($documentFile['type'] == '0' && isset($data['file_document'])) {
                $file = $data['file_document'];
                $fileName = $file->getClientOriginalName();
                if (file_exists(storage_path('app/public/document/'.$documentFile['document_category_id'].'/'.$fileName))) {
                    $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                }

                Storage::delete(config('cms.files.document.path').$documentFile['document_category_id'].
                    '/'.$data['old_file']);
    
                Storage::put(config('cms.files.document.path').$documentFile['document_category_id'].'/'.$fileName, 
                    file_get_contents($file));
                
                $documentFile->file = $fileName;
            }

            if ($documentFile['type'] == '1') {
                $documentFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
            }

            if ($documentFile['type'] == '2') {
                $documentFile->file = $data['file_url'];
            }
            
            $this->setFieldFile($data, $documentFile);
            if (Auth::guard()->check())
                $documentFile->updated_by = Auth::user()['id'];

            $documentFile->save();

            return $this->success($documentFile,  __('global.alert.update_success', [
                'attribute' => __('module/document.file.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field File
     * @param array $data
     * @param model $documentFile
     */
    private function setFieldFile($data, $documentFile)
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

        $documentFile->title = $title;
        $documentFile->description = $description;
        $documentFile->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $documentFile->publish = (bool)$data['publish'];
        $documentFile->public = (bool)$data['public'];
        $documentFile->locked = (bool)$data['locked'];
        $documentFile->config = [
            'hide_title' => (bool)$data['hide_title'],
            'hide_description' => (bool)$data['hide_description'],
            'hide_cover' => (bool)$data['hide_cover']
        ];

        return $documentFile;
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
                'attribute' => __('module/document.file.caption')
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
        $documentFile = $this->getFile($where);
        
        try {

            if ($position >= 1) {
    
                $this->fileModel->where('document_category_id', $documentFile['document_category_id'])
                    ->where('position', $position)->update([
                    'position' => $documentFile['position'],
                ]);
    
                $documentFile->position = $position;
                if (Auth::guard()->check()) {
                    $documentFile->updated_by = Auth::user()['id'];
                }
                $documentFile->save();
    
                return $this->success($documentFile, __('global.alert.update_success', [
                    'attribute' => __('module/document.file.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/document.file.caption')
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
        $documentFile = $this->getFile($where);

        try {

            if ($documentFile['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $documentFile['created_by']) {
                        return $this->error($documentFile,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/document.file.caption')
                        ]));
                    }

                    $documentFile->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $documentFile->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/document.file.caption')
                ]));

            } else {

                return $this->error($documentFile,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/document.file.caption')
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
        $documentFile = $this->fileModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $documentFile->restore();

            return $this->success($documentFile, __('global.alert.restore_success', [
                'attribute' => __('module/document.file.caption')
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
            $documentFile = $this->fileModel->onlyTrashed()->firstWhere($where);
        } else {
            $documentFile = $this->getFile($where);
        }

        try {
            
            if ($documentFile['type'] == '0') {
                Storage::delete(config('cms.files.document.path').$documentFile['document_category_id'].
                 '/'.$documentFile['file']);
            }

            $documentFile->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/document.file.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Record Download Hits
     * @param array $where
     */
    public function recordDownloadHits($where)
    {
        $file = $this->getFile($where);
        $file->update([
            'download' => ($file->download+1)
        ]);

        return $file;
    }
}