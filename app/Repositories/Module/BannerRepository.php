<?php

namespace App\Repositories\Module;

use App\Models\Module\Banner\Banner;
use App\Models\Module\Banner\BannerFile;
use App\Repositories\Feature\LanguageRepository;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerRepository
{
    use ApiResponser;

    private $bannerModel, $fileModel, $language;

    public function __construct(
        Banner $bannerModel,
        BannerFile $fileModel,
        LanguageRepository $language
    )
    {
        $this->bannerModel = $bannerModel;
        $this->fileModel = $fileModel;
        $this->language = $language;
    }

    //--------------------------------------------------------------------------
    // BANNER
    //--------------------------------------------------------------------------

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
                $banner->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
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

            if ($limit > 0)
                $banner->limit($limit);

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


            $banner = new Banner;
            $this->setField($data, $banner);
            $banner->position = $this->bannerModel->max('position') + 1;

            if (Auth::guard()->check())
                if (!Auth::user()->hasRole('developer|super') && config('module.banner.approval') == true) {
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

            $this->setField($data, $banner);
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
     * Set Field
     * @param array $data
     * @param model $banner
     */
    private function setField($data, $banner)
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

        $banner->name = $name;
        $banner->description = $description;
        $banner->publish = (bool)$data['publish'];
        $banner->locked = (bool)$data['locked'];
        $banner->config = [
            'show_description' => (bool)$data['config_show_description'],
            'type_text' => (bool)$data['config_type_text'],
            'type_image' => (bool)$data['config_type_image'],
            'type_video' => (bool)$data['config_type_video'],
            'banner_limit' => $data['config_banner_limit'],
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $banner->custom_fields = $customField;
        } else {
            $banner->custom_fields = null;
        }

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

            $value = !$banner[$field];
            if ($field == 'approved') {
                $value = $banner['approved'] == 1 ? 0 : 1;
            }

            $banner->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $banner['updated_by'],
            ]);

            if ($field == 'publish') {
                $banner->widgets()->update([
                    'publish' => $banner['publish']
                ]);
            }

            return $this->success($banner, __('global.alert.update_success', [
                'attribute' => __('module/banner.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Banner
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortBanner($where, $position)
    {
        $banner = $this->getBanner($where);

        $banner->position = $position;
        if (Auth::guard()->check()) {
            $banner->updated_by = Auth::user()['id'];
        }
        $banner->save();

        return $banner;
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

                $this->bannerModel->where('position', $position)->update([
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

            $files = $banner->files()->count();

            if ($banner['locked'] == 0 && $files == 0) {

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

                $banner->widgets()->delete();
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
            $banner->widgets()->restore();
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

            $banner->widgets()->forceDelete();
            $banner->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/banner.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // BANNER FILE
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
        $bannerFile = $this->fileModel->query();

        if ($isTrash == true)
            $bannerFile->onlyTrashed();

        if (isset($filter['banner_id']))
            $bannerFile->where('banner_id', $filter['banner_id']);

        if (isset($filter['type']))
            $bannerFile->where('type', $filter['type']);

        if (isset($filter['publish']))
            $bannerFile->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $bannerFile->where('public', $filter['public']);

        if (isset($filter['approved']))
            $bannerFile->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $bannerFile->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $bannerFile->when($filter['q'], function ($bannerFile, $q) {
                $bannerFile->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $bannerFile->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $bannerFile->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $bannerFile->paginate($limit);
        } else {

            if ($limit > 0)
                $bannerFile->limit($limit);

            $result = $bannerFile->get();
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
        $bannerFile = $this->fileModel->query();

        if (!empty($with))
            $bannerFile->with($with);

        $result = $bannerFile->firstWhere($where);;

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
            $imageType = $data['image_type'];
            $videoType = $data['video_type'];

            $bannerFile = new BannerFile;
            $bannerFile->banner_id = $data['banner_id'];

            $bannerFile->type = $type;
            if ($type == '0') {
                $bannerFile->image_type = $imageType;

                if ($imageType == '0') {
                    $fileUpload = $data['file_image'];
                    $fileName = $fileUpload->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/'.$data['banner_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$fileUpload->getClientOriginalName();
                    }

                    Storage::put(config('cms.files.banner.path').$data['banner_id'].'/'.$fileName,
                        file_get_contents($fileUpload));

                    $bannerFile->file = $fileName;
                }

                if ($imageType == '1') {
                    $bannerFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
                }

                if ($imageType == '2') {
                    $bannerFile->file = $data['file_url'];
                }
            }

            if ($type == '1') {
                $bannerFile->video_type = $videoType;

                if ($videoType == '0') {
                    $fileUpload = $data['file_video'];
                    $fileName = $fileUpload->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/'.$data['banner_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$fileUpload->getClientOriginalName();
                    }

                    Storage::put(config('cms.files.banner.path').$data['banner_id'].'/'.$fileName,
                        file_get_contents($fileUpload));

                    $bannerFile->file = $fileName;
                }

                if ($videoType == '1') {
                    $bannerFile->file = $data['file_youtube'];
                }

                if (isset($data['thumbnail'])) {

                    $fileThumb = $data['thumbnail'];
                    $fileNameThumb = $fileThumb->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/thumbnail/'.$data['banner_id'].'/'.$fileNameThumb))) {
                        $fileNameThumb = Str::random(3).'-'.$fileThumb->getClientOriginalName();
                    }

                    Storage::put(config('cms.files.banner.thumbnail.path').$data['banner_id'].'/'.$fileNameThumb,
                        file_get_contents($fileThumb));

                    $bannerFile->thumbnail = $fileNameThumb;
                }
            }

            $this->setFieldFile($data, $bannerFile);
            $bannerFile->position = $this->fileModel->where('banner_id', (int)$data['banner_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.banner.file.approval') == true) {
                    $bannerFile->approved = 2;
                }
                $bannerFile->created_by = Auth::user()['id'];

            $bannerFile->save();

            return $this->success($bannerFile,  __('global.alert.create_success', [
                'attribute' => __('module/banner.file.caption')
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

            $bannerFile = new BannerFile;
            $bannerFile['banner_id'] = $data['banner_id'];

            $bannerFile->type = '0';
            $bannerFile->image_type = '0';

            $fileUpload = $data['file'];
            $fileName = $fileUpload->getClientOriginalName();
            if (file_exists(storage_path('app/public/banner/'.$data['banner_id'].'/'.$fileName))) {
                $fileName = Str::random(3).'-'.$fileUpload->getClientOriginalName();
            }

            Storage::put(config('cms.files.banner.path').$data['banner_id'].'/'.$fileName,
                file_get_contents($fileUpload));

            $bannerFile->file = $fileName;

            $this->setFieldFile($data, $bannerFile);
            $bannerFile->position = $this->fileModel->where('banner_id', (int)$data['banner_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.banner.file.approval') == true) {
                    $bannerFile->approved = 2;
                }
                $bannerFile->created_by = Auth::user()['id'];

            $bannerFile->save();

            return $this->success($bannerFile,  __('global.alert.create_success', [
                'attribute' => __('module/banner.file.caption')
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
        $bannerFile = $this->getFile($where);

        try {

            if ($bannerFile['type'] == '0') {

                if ($bannerFile['image_type'] == '0' && isset($data['file_image'])) {
                    $fileUpload = $data['file_image'];
                    $fileName = $fileUpload->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/'.$bannerFile['banner_id'].'/'.$fileName))) {
                        $fileName = Str::random(3).'-'.$fileUpload->getClientOriginalName();
                    }

                    Storage::delete(config('cms.files.banner.path').$bannerFile['banner_id'].
                        '/'.$data['old_file']);

                    Storage::put(config('cms.files.banner.path').$bannerFile['banner_id'].'/'.$fileName,
                        file_get_contents($fileUpload));

                    $bannerFile->file = $fileName;
                }

                if ($bannerFile['image_type'] == '1') {
                    $bannerFile->file = Str::replace(url('/storage'), '', $data['filemanager']);
                }

                if ($bannerFile['image_type'] == '2') {
                    $bannerFile->file = $data['file_url'];
                }
            }

            if ($bannerFile['type'] == '1') {

                if ($bannerFile['video_type'] == '0') {

                    if (isset($data['file_video'])) {
                        $fileUpload = $data['file_video'];
                        $fileName = $fileUpload->getClientOriginalName();
                        if (file_exists(storage_path('app/public/banner/'.$bannerFile['banner_id'].'/'.$fileName))) {
                            $fileName = Str::random(3).'-'.$fileUpload->getClientOriginalName();
                        }

                        Storage::delete(config('cms.files.banner.path').$bannerFile['banner_id'].
                            '/'.$data['old_file']);

                        Storage::put(config('cms.files.banner.path').$bannerFile['banner_id'].'/'.$fileName,
                            file_get_contents($fileUpload));

                        $bannerFile->file = $fileName;
                    }
                }

                if ($bannerFile['video_type'] == '1') {
                    $bannerFile->file = $data['file_youtube'];
                }

                if (isset($data['thumbnail'])) {

                    $fileThumb = $data['thumbnail'];
                    $fileNameThumb = $fileThumb->getClientOriginalName();
                    if (file_exists(storage_path('app/public/banner/thumbnail/'.$bannerFile['banner_id'].'/'.$fileNameThumb))) {
                        $fileNameThumb = Str::random(3).'-'.$fileThumb->getClientOriginalName();
                    }

                    Storage::delete(config('cms.files.banner.thumbnail.path').$bannerFile['banner_id'].
                        '/'.$data['old_thumbnail']);

                    Storage::put(config('cms.files.banner.thumbnail.path').$bannerFile['banner_id'].'/'.$fileNameThumb,
                        file_get_contents($fileThumb));

                    $bannerFile->thumbnail = $fileNameThumb;
                }
            }

            $this->setFieldFile($data, $bannerFile);
            if (Auth::guard()->check())
                $bannerFile->updated_by = Auth::user()['id'];

            $bannerFile->save();

            return $this->success($bannerFile,  __('global.alert.update_success', [
                'attribute' => __('module/banner.file.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field File
     * @param array $data
     * @param model $bannerFile
     */
    private function setFieldFile($data, $bannerFile)
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

        $bannerFile->title = $title;
        $bannerFile->description = $description;
        $bannerFile->publish = (bool)$data['publish'];
        $bannerFile->public = (bool)$data['public'];
        $bannerFile->locked = (bool)$data['locked'];
        $bannerFile->url = $data['url'] ?? null;
        $bannerFile->config = [
            'show_title' => (bool)$data['config_show_title'],
            'show_description' => (bool)$data['config_show_description'],
            'show_url' => (bool)$data['config_show_url'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $bannerFile->custom_fields = $customField;
        } else {
            $bannerFile->custom_fields = null;
        }

        return $bannerFile;
    }

    /**
     * Status File (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusFile($field, $where)
    {
        $bannerFile = $this->getFile($where);

        try {

            $value = !$bannerFile[$field];
            if ($field == 'approved') {
                $value = $bannerFile['approved'] == 1 ? 0 : 1;
            }

            $bannerFile->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $bannerFile['updated_by'],
            ]);

            return $this->success($bannerFile, __('global.alert.update_success', [
                'attribute' => __('module/banner.file.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort File
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortFile($where, $position)
    {
        $bannerFile = $this->getFile($where);

        $bannerFile->position = $position;
        if (Auth::guard()->check()) {
            $bannerFile->updated_by = Auth::user()['id'];
        }
        $bannerFile->save();

        return $bannerFile;
    }

    /**
     * Set Position File
     * @param array $where
     * @param int $position
     */
    public function positionFile($where, $position)
    {
        $bannerFile = $this->getFile($where);

        try {

            if ($position >= 1) {

                $this->fileModel->where('banner_id', $bannerFile['banner_id'])
                    ->where('position', $position)->update([
                    'position' => $bannerFile['position'],
                ]);

                $bannerFile->position = $position;
                if (Auth::guard()->check()) {
                    $bannerFile->updated_by = Auth::user()['id'];
                }
                $bannerFile->save();

                return $this->success($bannerFile, __('global.alert.update_success', [
                    'attribute' => __('module/banner.file.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/banner.file.caption')
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
        $bannerFile = $this->getFile($where);

        try {

            if ($bannerFile['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $bannerFile['created_by']) {
                        return $this->error($bannerFile,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/banner.file.caption')
                        ]));
                    }

                    $bannerFile->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $bannerFile->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/banner.file.caption')
                ]));

            } else {

                return $this->error($bannerFile,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/banner.file.caption')
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
        $bannerFile = $this->fileModel->onlyTrashed()->firstWhere($where);

        try {

            //restore data yang bersangkutan
            $bannerFile->restore();

            return $this->success($bannerFile, __('global.alert.restore_success', [
                'attribute' => __('module/banner.file.caption')
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
            $bannerFile = $this->fileModel->onlyTrashed()->firstWhere($where);
        } else {
            $bannerFile = $this->getFile($where);
        }

        try {

            if ($bannerFile['type'] == '0' && $bannerFile['image_type'] == '0') {
                Storage::delete(config('cms.files.banner.path').$bannerFile['banner_id'].
                 '/'.$bannerFile['file']);
            }

            if ($bannerFile['type'] == '1' && $bannerFile['video_type'] == '0') {
                Storage::delete(config('cms.files.banner.path').$bannerFile['banner_id'].
                    '/'.$bannerFile['file']);
            }

            if (!empty($bannerFile['thumbnail'])) {
                Storage::delete(config('cms.files.banner.thumbnail.path').$bannerFile['banner_id'].
                    '/'.$bannerFile['thumbnail']);
            }

            $bannerFile->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/banner.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }
}
