<?php

namespace App\Services\Module;

use App\Models\Module\Document\Document;
use App\Models\Module\Document\DocumentFile;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    use ApiResponser;

    private $documentModel, $fileModel, $language;

    public function __construct(
        Document $documentModel,
        DocumentFile $fileModel,
        LanguageService $language
    )
    {
        $this->documentModel = $documentModel;
        $this->fileModel = $fileModel;
        $this->language = $language;
    }

    //--------------------------------------------------------------------------
    // DOCUMENT
    //--------------------------------------------------------------------------

    /**
     * Get Document List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getDocumentList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $document = $this->documentModel->query();

        if ($isTrash == true)
            $document->onlyTrashed();

        if (isset($filter['publish']))
            $document->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $document->where('public', $filter['public']);

        if (isset($filter['approved']))
            $document->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $document->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $document->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $document->when($filter['q'], function ($document, $q) {
                $document->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $document->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $document->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $document->paginate($limit);
        } else {

            if ($limit > 0)
                $document->limit($limit);

            $result = $document->get();
        }

        return $result;
    }

    /**
     * Get Document One
     * @param array $where
     * @param array $with
     */
    public function getDocument($where, $with = [])
    {
        $document = $this->documentModel->query();

        if (!empty($with))
            $document->with($with);

        $result = $document->firstWhere($where);;

        return $result;
    }

    /**
     * Create Document
     * @param array $data
     */
    public function storeDocument($data)
    {
        try {

            $document = new Document;
            $this->setFieldDocument($data, $document);
            $document->position = $this->documentModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.document.approval') == true) {
                    $document->approved = 2;
                }
                $document->created_by = Auth::user()['id'];

            $document->save();

            return $this->success($document,  __('global.alert.create_success', [
                'attribute' => __('module/document.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Document
     * @param array $data
     * @param array $where
     */
    public function updateDocument($data, $where)
    {
        $document = $this->getDocument($where);

        try {

            $this->setFieldDocument($data, $document);
            if (Auth::guard()->check())
                $document->updated_by = Auth::user()['id'];

            $document->save();

            return $this->success($document,  __('global.alert.update_success', [
                'attribute' => __('module/document.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Document
     * @param array $data
     * @param model $document
     */
    private function setFieldDocument($data, $document)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('app.fallback_locale');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $name[$value['iso_codes']] = ($data['name_'.$value['iso_codes']] == null) ?
                $data['name_'.$langDefault] : $data['name_'.$value['iso_codes']];

            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];
        }

        $document->slug = Str::slug(strip_tags($data['slug']), '-');
        $document->name = $name;
        $document->description = $description;
        if (isset($data['roles']) && count($data['roles']) > 0) {
            $document->roles = $data['roles'];
        } else {
            $document->roles = null;
        }
        $document->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $document->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $document->publish = (bool)$data['publish'];
        $document->public = (bool)$data['public'];
        $document->locked = (bool)$data['locked'];
        $document->detail = (bool)$data['detail'];
        $document->config = [
            'show_description' => (bool)$data['config_show_description'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'paginate_file' => (bool)$data['config_paginate_file'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
            'file_limit' => $data['config_file_limit'],
            'file_order_by' => $data['config_file_order_by'],
            'file_order_type' => $data['config_file_order_type'],
        ];
        $document->template_id = $data['template_id'] ?? null;

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $document->custom_fields = $customField;
        } else {
            $document->custom_fields = null;
        }

        return $document;
    }

    /**
     * Status Document (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusDocument($field, $where)
    {
        $document = $this->getDocument($where);

        try {

            $value = !$document[$field];
            if ($field == 'approved') {
                $value = $document['approved'] == 1 ? 0 : 1;
            }

            $document->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $document['updated_by'],
            ]);

            if ($field == 'publish') {
                $document->menus()->update([
                    'publish' => $document['publish']
                ]);
                $document->widgets()->update([
                    'publish' => $document['publish']
                ]);
            }

            return $this->success($document, __('global.alert.update_success', [
                'attribute' => __('module/document.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Document
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortDocument($where, $position)
    {
        $document = $this->getDocument($where);

        $document->position = $position;
        if (Auth::guard()->check()) {
            $document->updated_by = Auth::user()['id'];
        }
        $document->save();

        return $document;
    }

    /**
     * Set Position Document
     * @param array $where
     * @param int $position
     */
    public function positionDocument($where, $position)
    {
        $document = $this->getDocument($where);

        try {

            if ($position >= 1) {

                $this->documentModel->where('position', $position)->update([
                    'position' => $document['position'],
                ]);

                $document->position = $position;
                if (Auth::guard()->check()) {
                    $document->updated_by = Auth::user()['id'];
                }
                $document->save();

                return $this->success($document, __('global.alert.update_success', [
                    'attribute' => __('module/document.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/document.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

     /**
     * Record Document Hits
     * @param array $where
     */
    public function recordDocumentHits($where)
    {
        $document = $this->getDocument($where);

        if (empty(Session::get('documentHits-'.$document['id']))) {
            Session::put('documentHits-'.$document['id'], $document['id']);
            $document->hits = ($document->hits+1);
            $document->timestamps = false;
            $document->save();
        }

        return $document;
    }

        /**
     * Trash Document
     * @param array $where
     */
    public function trashDocument($where)
    {
        $document = $this->getDocument($where);

        try {

            $files = $document->files()->count();

            if ($document['locked'] == 0 && $files == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $document['created_by']) {
                        return $this->error($document,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/document.caption')
                        ]));
                    }

                    $document->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $document->menus()->delete();
                $document->widgets()->delete();
                $document->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/document.caption')
                ]));

            } else {
                return $this->error($document,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/document.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Document
     * @param array $where
     */
    public function restoreDocument($where)
    {
        $document = $this->documentModel->onlyTrashed()->firstWhere($where);

        try {

            $checkSlug = $this->getDocument(['slug' => $document['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/document.caption')
                ]));
            }

            //restore data yang bersangkutan
            $document->menus()->restore();
            $document->widgets()->restore();
            $document->restore();

            return $this->success($document, __('global.alert.restore_success', [
                'attribute' => __('module/document.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Document (Permanent)
     * @param array $where
     */
    public function deleteDocument($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $document = $this->documentModel->onlyTrashed()->firstWhere($where);
        } else {
            $document = $this->getDocument($where);
        }

        try {

            $document->menus()->forceDelete();
            $document->widgets()->forceDelete();
            $document->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/document.caption')
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
        $query = $this->documentModel->query();

        $query->where($where);
        $query->whereJsonContains('roles', $role);

        $result = $query->count();

        return $result;
    }

    //--------------------------------------------------------------------------
    // DOCUMENT FILE
    //--------------------------------------------------------------------------

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
        $documentFile = $this->fileModel->query();

        if ($isTrash == true)
            $documentFile->onlyTrashed();

        if (isset($filter['document_id']))
            $documentFile->where('document_id', $filter['document_id']);

        if (isset($filter['type']))
            $documentFile->where('type', $filter['type']);

        if (isset($filter['publish']))
            $documentFile->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $documentFile->where('public', $filter['public']);

        if (isset($filter['approved']))
            $documentFile->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $documentFile->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $documentFile->when($filter['q'], function ($documentFile, $q) {
                $documentFile->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $documentFile->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $documentFile->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $documentFile->paginate($limit);
        } else {

            if ($limit > 0)
                $documentFile->limit($limit);

            $result = $documentFile->get();
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
        $documentFile = $this->fileModel->query();

        if (!empty($with))
            $documentFile->with($with);

        $result = $documentFile->firstWhere($where);;

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
            $documentFile->document_id = $data['document_id'];

            $documentFile->type = $type;

            if ($type == '0') {
                $file = $data['file_document'];
                $fileName = $file->getClientOriginalName();
                if (file_exists(storage_path('app/public/document/'.$data['document_id'].'/'.$fileName))) {
                    $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                }

                Storage::put(config('cms.files.document.path').$data['document_id'].'/'.$fileName,
                    file_get_contents($file));

                $documentFile->file = $fileName;
            }

            if ($type == '1') {
                $documentFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
            }

            if ($type == '2') {
                $documentFile->file = $data['file_url'];
            }

            if ($data['slug']) {
                $documentFile->slug = Str::slug(strip_tags($data['slug']), '-');

                $checkSlug = $this->getFile(['slug' => $documentFile['slug']]);
                if (!empty($checkSlug)) {
                    return $this->error(null, __('global.alert.create_failed', [
                        'attribute' => __('module/document.file.caption')
                    ]));
                }
            }

            $this->setFieldFile($data, $documentFile);
            $documentFile->position = $this->fileModel->where('document_id', $data['document_id'])->max('position') + 1;

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
            $documentFile->document_id = $data['document_id'];

            $documentFile->type = '0';

            $file = $data['file'];
            $fileName = $file->getClientOriginalName();
            if (file_exists(storage_path('app/public/document/'.$data['document_id'].'/'.$fileName))) {
                $fileName = Str::random(3).'-'.$file->getClientOriginalName();
            }

            Storage::put(config('cms.files.document.path').$data['document_id'].'/'.$fileName,
                file_get_contents($file));

            $documentFile->file = $fileName;

            $this->setFieldFile($data, $documentFile);
            $documentFile->position = $this->fileModel->where('document_id', (int)$data['document_id'])->max('position') + 1;

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

        if ($data['slug']) {
            $documentFile->slug = Str::slug(strip_tags($data['slug']), '-');

            $checkSlug = $this->fileModel
                ->where('id', '!=', $documentFile['id'])
                ->where('slug', $documentFile['slug'])
                ->first();

            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/document.file.caption')
                ]));
            }
        }

        try {

            if ($documentFile['type'] == '0' && isset($data['file_document'])) {
                $file = $data['file_document'];
                $fileName = $file->getClientOriginalName();
                if (file_exists(storage_path('app/public/document/'.$documentFile['document_id'].'/'.$fileName))) {
                    $fileName = Str::random(3).'-'.$file->getClientOriginalName();
                }

                Storage::delete(config('cms.files.document.path').$documentFile['document_id'].
                    '/'.$data['old_file']);

                Storage::put(config('cms.files.document.path').$documentFile['document_id'].'/'.$fileName,
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
        $langDefault = config('app.fallback_locale');
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
            'show_title' => (bool)$data['config_show_title'],
            'show_description' => (bool)$data['config_show_description'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $documentFile->custom_fields = $customField;
        } else {
            $documentFile->custom_fields = null;
        }

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

            $value = !$file[$field];
            if ($field == 'approved') {
                $value = $file['approved'] == 1 ? 0 : 1;
            }

            $file->update([
                $field => $value,
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
     * Sort File
     * @param array $where
     * @param int $position
     */
    public function sortFile($where, $position)
    {
        $file = $this->getFile($where);

        $file->position = $position;
        if (Auth::guard()->check()) {
            $file->updated_by = Auth::user()['id'];
        }
        $file->save();

        return $file;
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

                $this->fileModel->where('document_id', $documentFile['document_id'])
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
                Storage::delete(config('cms.files.document.path').$documentFile['document_id'].
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
     * Record Download
     * @param array $where
     */
    public function recordDownload($where)
    {
        $file = $this->getFile($where);

        if (empty(Session::get('documentDownload-'.$file['id']))) {
            Session::put('documentDownload-'.$file['id'], $file['id']);
            $file->download = ($file->download+1);
            $file->timestamps = false;
            $file->save();
        }

        return $file;
    }
}
