<?php

namespace App\Repositories\Module;

use App\Models\Module\Link\Link;
use App\Models\Module\Link\LinkMedia;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\IndexUrlRepository;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LinkRepository
{
    use ApiResponser;

    private $linkModel, $mediaModel, $language, $indexUrl;

    public function __construct(
        Link $linkModel,
        LinkMedia $mediaModel,
        LanguageRepository $language,
        IndexUrlRepository $indexUrl
    )
    {
        $this->linkModel = $linkModel;
        $this->mediaModel = $mediaModel;
        $this->language = $language;
        $this->indexUrl = $indexUrl;
    }

    //--------------------------------------------------------------------------
    // LINK
    //--------------------------------------------------------------------------

    /**
     * Get Link List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getLinkList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $link = $this->linkModel->query();

        if ($isTrash == true)
            $link->onlyTrashed();

        if (isset($filter['publish']))
            $link->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $link->where('public', $filter['public']);

        if (isset($filter['approved']))
            $link->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $link->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $link->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $link->when($filter['q'], function ($link, $q) {
                $link->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $link->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $link->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $link->paginate($limit);
        } else {

            if ($limit > 0)
                $link->limit($limit);

            $result = $link->get();
        }

        return $result;
    }

    /**
     * Get Link One
     * @param array $where
     * @param array $with
     */
    public function getLink($where, $with = [])
    {
        $link = $this->linkModel->query();

        if (!empty($with))
            $link->with($with);

        $result = $link->firstWhere($where);;

        return $result;
    }

    /**
     * Create Link
     * @param array $data
     */
    public function storeLink($data)
    {
        try {

            DB::beginTransaction();

            $link = new Link;
            $this->setFieldLink($data, $link);
            $link->position = $this->linkModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.link.approval') == true) {
                    $link->approved = 2;
                }
                $link->created_by = Auth::user()['id'];

            $link->save();

            try {

                DB::commit();
                $slug = Str::slug(strip_tags($data['slug']), '-');
                $data['slug'] = $slug;
                $data['module'] = 'link';
                $this->indexUrl->storeAssociate($data, $link);

                return $this->success($link,  __('global.alert.create_success', [
                    'attribute' => __('module/link.caption')
                ]));

            } catch (Exception $e) {

                return $this->error(null,  $e->getMessage());
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Link
     * @param array $data
     * @param array $where
     */
    public function updateLink($data, $where)
    {
        $link = $this->getLink($where);

        try {

            $this->setFieldLink($data, $link);
            if (Auth::guard()->check())
                $link->updated_by = Auth::user()['id'];

            $link->save();

            $slug = Str::slug(strip_tags($data['slug']), '-');
            $this->indexUrl->updateAssociate($slug, ['id' => $link['indexing']['id']]);

            return $this->success($link,  __('global.alert.update_success', [
                'attribute' => __('module/link.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Link
     * @param array $data
     * @param model $link
     */
    private function setFieldLink($data, $link)
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

        $link->slug = Str::slug(strip_tags($data['slug']), '-');
        $link->name = $name;
        $link->description = $description;
        $link->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $link->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $link->publish = (bool)$data['publish'];
        $link->public = (bool)$data['public'];
        $link->locked = (bool)$data['locked'];
        $link->detail = (bool)$data['detail'];
        $link->config = [
            'show_description' => (bool)$data['config_show_description'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'paginate_media' => (bool)$data['config_paginate_media'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
            'media_limit' => $data['config_media_limit'],
            'media_order_by' => $data['config_media_order_by'],
            'media_order_type' => $data['config_media_order_type'],
        ];
        $link->template_id = $data['template_id'] ?? null;

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $link->custom_fields = $customField;
        } else {
            $link->custom_fields = null;
        }

        return $link;
    }

    /**
     * Status Link (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusLink($field, $where)
    {
        $link = $this->getLink($where);

        try {

            $value = !$link[$field];
            if ($field == 'approved') {
                $value = $link['approved'] == 1 ? 0 : 1;
            }

            $link->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $link['updated_by'],
            ]);

            if ($field == 'publish') {
                $link->menus()->update([
                    'publish' => $link['publish']
                ]);
                $link->widgets()->update([
                    'publish' => $link['publish']
                ]);
            }

            return $this->success($link, __('global.alert.update_success', [
                'attribute' => __('module/link.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Link
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortLink($where, $position)
    {
        $link = $this->getLink($where);

        $link->position = $position;
        if (Auth::guard()->check()) {
            $link->updated_by = Auth::user()['id'];
        }
        $link->save();

        return $link;
    }

    /**
     * Set Position Link
     * @param array $where
     * @param int $position
     */
    public function positionLink($where, $position)
    {
        $link = $this->getLink($where);

        try {

            if ($position >= 1) {

                $this->linkModel->where('position', $position)->update([
                    'position' => $link['position'],
                ]);

                $link->position = $position;
                if (Auth::guard()->check()) {
                    $link->updated_by = Auth::user()['id'];
                }
                $link->save();

                return $this->success($link, __('global.alert.update_success', [
                    'attribute' => __('module/link.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/link.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

     /**
     * Record Link Hits
     * @param array $where
     */
    public function recordLinkHits($where)
    {
        $link = $this->getLink($where);

        if (empty(Session::get('linktHits-'.$link['id']))) {
            Session::put('linktHits-'.$link['id'], $link['id']);
            $link->hits = ($link->hits+1);
            $link->timestamps = false;
            $link->save();
        }

        return $link;
    }

        /**
     * Trash Link
     * @param array $where
     */
    public function trashLink($where)
    {
        $link = $this->getLink($where);

        try {

            $meedia = $link->medias()->count();

            if ($link['locked'] == 0 && $meedia == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $link['created_by']) {
                        return $this->error($link,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/link.caption')
                        ]));
                    }

                    $link->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $link->menus()->delete();
                $link->widgets()->delete();
                // $link->indexing->delete();
                $link->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/link.caption')
                ]));

            } else {
                return $this->error($link,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/link.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Link
     * @param array $where
     */
    public function restoreLink($where)
    {
        $link = $this->linkModel->onlyTrashed()->firstWhere($where);

        try {

            $checkSlug = $this->getLink(['slug' => $link['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/link.caption')
                ]));
            }

            //restore data yang bersangkutan
            $link->menus()->restore();
            $link->widgets()->restore();
            // $link->indexing()->restore();
            $link->restore();

            return $this->success($link, __('global.alert.restore_success', [
                'attribute' => __('module/link.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Link (Permanent)
     * @param array $where
     */
    public function deleteLink($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $link = $this->linkModel->onlyTrashed()->firstWhere($where);
        } else {
            $link = $this->getLink($where);
        }

        try {

            $link->menus()->forceDelete();
            $link->widgets()->forceDelete();
            $link->indexing()->forceDelete();
            $link->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/link.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // LINK MEDIA
    //--------------------------------------------------------------------------

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

        if (isset($filter['link_id']))
            $media->where('link_id', $filter['link_id']);

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
            $media->link_id = $data['link_id'];
            $this->setFieldMedia($data, $media);
            $media->position = $this->mediaModel->where('link_id', $data['link_id'])->max('position') + 1;

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
            'show_description' => (bool)$data['config_show_description'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
            'is_embed' => (bool)$data['config_is_embed']
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $media->custom_fields = $customField;
        } else {
            $media->custom_fields = null;
        }

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

            $value = !$media[$field];
            if ($field == 'approved') {
                $value = $media['approved'] == 1 ? 0 : 1;
            }

            $media->update([
                $field => $value,
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
     * Sort Media
     * @param array $where
     * @param int $position
     */
    public function sortMedia($where, $position)
    {
        $media = $this->getMedia($where);

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
     */
    public function positionMedia($where, $position)
    {
        $media = $this->getMedia($where);

        try {

            if ($position >= 1) {

                $this->mediaModel->where('link_id', $media['link_id'])
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
