<?php

namespace App\Repositories\Module;

use App\Models\Module\Page;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\IndexUrlRepository;
use App\Repositories\Master\TagRepository;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PageRepository
{
    use ApiResponser;

    private $pageModel, $language, $indexUrl;

    public function __construct(
        Page $pageModel,
        LanguageRepository $language,
        IndexUrlRepository $indexUrl
    )
    {
        $this->pageModel = $pageModel;
        $this->language = $language;
        $this->indexUrl = $indexUrl;
    }

    /**
     * Get Page List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getPageList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $page = $this->pageModel->query();

        if ($isTrash == true)
            $page->onlyTrashed();

        if (isset($filter['parent']))
            $page->where('parent', $filter['parent']);

        if (isset($filter['publish']))
            $page->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $page->where('public', $filter['public']);

        if (isset($filter['approved']))
            $page->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $page->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $page->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $page->when($filter['q'], function ($page, $q) {
                $page->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(content, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(seo, "$.keywords")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['tags']))
            $page->whereHas('tags', function ($page) use ($filter) {
                $page->whereHas('tag', function ($page) use ($filter) {
                    $page->where('name', $filter['tags']);
                });
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $page->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $page->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $page->paginate($limit);
        } else {

            if ($limit > 0)
                $page->limit($limit);

            $result = $page->get();
        }

        return $result;
    }

    /**
     * Get Page One
     * @param array $where
     * @param array $with
     */
    public function getPage($where, $with = [])
    {
        $page = $this->pageModel->query();

        if (!empty($with))
            $page->with($with);

        $result = $page->firstWhere($where);;

        return $result;
    }

    /**
     * Create Page
     * @param array $data
     */
    public function store($data)
    {
        try {

            DB::beginTransaction();

            $page = new Page;
            $page->parent = $data['parent'] ?? 0;

            $parent = $this->getPage(['id' => $data['parent']]);
            if (!empty($parent)) {
                $path = [];
                if(!empty($parent['path']))
                    $path = $parent['path'];

                if(!in_array($parent['id'], $path))
                    array_push($path, $parent['id']);

                $page->path = $path;
            }

            $this->setField($data, $page);
            $page->position = $this->pageModel->where('parent', (int)$data['parent'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.page.approval') == true) {
                    $page->approved = 2;
                }
                $page->created_by = Auth::user()['id'];

            $page->save();

            try {

                DB::commit();

                if ($page['parent'] == 0) {
                    $slug = Str::slug(strip_tags($data['slug']), '-');
                    $data['slug'] = $slug;
                    $data['module'] = 'page';
                    $this->indexUrl->storeAssociate($data, $page);
                }

                if (isset($data['tags']))
                    App::make(TagRepository::class)->wipeStore($data['tags'], $page);

                return $this->success($page,  __('global.alert.create_success', [
                    'attribute' => __('module/page.caption')
                ]));

            } catch (Exception $e) {

                DB::rollBack();

                return $this->error(null,  $e->getMessage());
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Page
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $page = $this->getPage($where);

        try {

            $this->setField($data, $page);
            if (Auth::guard()->check())
                $page->updated_by = Auth::user()['id'];

            $page->save();

            if ($page['parent'] == 0) {
                $slug = Str::slug(strip_tags($data['slug']), '-');
                $this->indexUrl->updateAssociate($slug, ['id' => $page['indexing']['id']]);
            }

            if (isset($data['tags']))
                App::make(TagRepository::class)->wipeStore($data['tags'], $page);

            return $this->success($page,  __('global.alert.update_success', [
                'attribute' => __('module/page.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Page
     * @param array $data
     * @param model $page
     */
    private function setField($data, $page)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('app.fallback_locale');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $title[$value['iso_codes']] = ($data['title_'.$value['iso_codes']] == null) ?
                $data['title_'.$langDefault] : $data['title_'.$value['iso_codes']];

            $intro[$value['iso_codes']] = ($data['intro_'.$value['iso_codes']] == null) ?
                $data['intro_'.$langDefault] : $data['intro_'.$value['iso_codes']];

            $content[$value['iso_codes']] = ($data['content_'.$value['iso_codes']] == null) ?
                $data['content_'.$langDefault] : $data['content_'.$value['iso_codes']];
        }

        $page->slug = Str::slug(strip_tags($data['slug']), '-');
        $page->title = $title;
        $page->intro = $intro;
        $page->content = $content;
        $page->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $page->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $page->publish = (bool)$data['publish'];
        $page->public = (bool)$data['public'];
        $page->detail = (bool)$data['detail'];
        $page->locked = (bool)$data['locked'];
        $page->config = [
            'show_intro' => (bool)$data['config_show_intro'],
            'show_content' => (bool)$data['config_show_content'],
            'show_tags' => (bool)$data['config_show_tags'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'show_media' => (bool)$data['config_show_media'],
            'detail_child' => (bool)$data['config_detail_child'],
            'create_child' => (bool)$data['config_create_child'],
            'paginate_child' => (bool)$data['config_paginate_child'],
            'action_media' => (bool)$data['config_action_media'],
            'paginate_media' => (bool)$data['config_paginate_media'],
            'child_limit' => $data['config_child_limit'],
            'media_limit' => $data['config_media_limit'],
            'child_order_by' => $data['config_child_order_by'],
            'child_order_type' => $data['config_child_order_type'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
        ];
        $page->template_id = $data['template_id'] ?? null;
        $page->seo = [
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $page->custom_fields = $customField;
        } else {
            $page->custom_fields = null;
        }

        return $page;
    }

    /**
     * Status Page (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function status($field, $where)
    {
        $page = $this->getPage($where);

        try {

            $value = !$page[$field];
            if ($field == 'approved') {
                $value = $page['approved'] == 1 ? 0 : 1;
            }

            $page->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $page['updated_by'],
            ]);

            if ($field == 'publish') {
                $page->menus()->update([
                    'publish' => $page['publish']
                ]);
                $page->widgets()->update([
                    'publish' => $page['publish']
                ]);
            }

            return $this->success($page, __('global.alert.update_success', [
                'attribute' => __('module/page.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Page
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function position($where, $position, $parent = null)
    {
        $page = $this->getPage($where);

        try {

            if ($position >= 1) {

                if ($parent != null) {
                    $this->pageModel->where('position', $position)->where('parent', $parent)->update([
                        'position' => $page['position'],
                    ]);
                } else {
                    $this->pageModel->where('position', $position)->where('parent', 0)->update([
                        'position' => $page['position'],
                    ]);
                }

                $page->position = $position;
                if (Auth::guard()->check()) {
                    $page->updated_by = Auth::user()['id'];
                }
                $page->save();

                return $this->success($page, __('global.alert.update_success', [
                    'attribute' => __('module/page.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/page.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Record Hits
     * @param array $where
     */
    public function recordHits($where)
    {
        $page = $this->getPage($where);

        if (empty(Session::get('pageHits-'.$page['id']))) {
            Session::put('pageHits-'.$page['id'], $page['id']);
            $page->hits = ($page->hits+1);
            $page->timestamps = false;
            $page->save();
        }

        return $page;
    }

    /**
     * Trash Page
     * @param array $where
     */
    public function trash($where)
    {
        $page = $this->getPage($where);

        try {

            $childs = $page->childs()->count();

            if ($page['locked'] == 0 && $childs == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('support|admin|editor') && Auth::user()['id'] != $page['created_by']) {
                        return $this->error($page,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/page.caption')
                        ]));
                    }

                    $page->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $page->medias()->delete();
                $page->menus()->delete();
                $page->widgets()->delete();
                // $page->indexing->delete();
                $page->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/page.caption')
                ]));

            } else {
                return $this->error($page,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/page.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Page
     * @param array $where
     */
    public function restore($where)
    {
        $page = $this->pageModel->onlyTrashed()->firstWhere($where);

        try {

            $checkSlug = $this->getPage(['slug' => $page['slug']]);
            $checkParent = $this->getPage(['id' => $page['parent']]);
            if (!empty($checkSlug) || $page['parent'] > 0 && empty($checkParent)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/page.caption')
                ]));
            }

            //restore data yang bersangkutan
            $page->medias()->restore();
            $page->menus()->restore();
            $page->widgets()->restore();
            // $page->indexing()->restore();
            $page->restore();

            return $this->success($page, __('global.alert.restore_success', [
                'attribute' => __('module/page.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Page (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $page = $this->pageModel->onlyTrashed()->firstWhere($where);
        } else {
            $page = $this->getPage($where);
        }

        try {

            $page->medias()->forceDelete();
            $page->tags()->delete();
            $page->menus()->forceDelete();
            $page->widgets()->forceDelete();
            if ($page['parent'] == 0) {
                $page->indexing()->forceDelete();
            }
            $page->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/page.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }
}
