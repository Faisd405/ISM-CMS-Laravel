<?php

namespace App\Repositories\Module;

use App\Models\Module\Content\ContentCategory;
use App\Models\Module\Content\ContentPost;
use App\Models\Module\Content\ContentSection;
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

class ContentRepository
{
    use ApiResponser;

    private $sectionModel, $categoryModel, $postModel, $language, $indexUrl;

    public function __construct(
        ContentSection $sectionModel,
        ContentCategory $categoryModel,
        ContentPost $postModel,
        LanguageRepository $language,
        IndexUrlRepository $indexUrl
    )
    {
        $this->sectionModel = $sectionModel;
        $this->categoryModel = $categoryModel;
        $this->postModel = $postModel;
        $this->language = $language;
        $this->indexUrl = $indexUrl;
    }

    //--------------------------------------------------------------------------
    // SECTION
    //--------------------------------------------------------------------------

    /**
     * Get Section List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getSectionList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $section = $this->sectionModel->query();

        if ($isTrash == true)
            $section->onlyTrashed();

        if (isset($filter['publish']))
            $section->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $section->where('public', $filter['public']);

        if (isset($filter['approved']))
            $section->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $section->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $section->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $section->when($filter['q'], function ($section, $q) {
                $section->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(seo, "$.keywords")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $section->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $section->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $section->paginate($limit);
        } else {

            if ($limit > 0)
                $section->limit($limit);

            $result = $section->get();
        }

        return $result;
    }


    /**
     * Get Section One
     * @param array $where
     * @param array $with
     */
    public function getSection($where, $with = [])
    {
        $section = $this->sectionModel->query();

        if (!empty($with))
            $section->with($with);

        $result = $section->firstWhere($where);;

        return $result;
    }

    /**
     * Create Section
     * @param array $data
     */
    public function storeSection($data)
    {
        try {

            DB::beginTransaction();

            $section = new ContentSection();
            $this->setFieldSection($data, $section);
            $section->position = $this->sectionModel->max('position') + 1;

            if (Auth::guard()->check())
                if (!Auth::user()->hasRole('developer|super') && config('module.content.section.approval') == true) {
                    $section->approved = 2;
                }
                $section->created_by = Auth::user()['id'];

            $section->save();

            try {

                DB::commit();
                $slug = Str::slug(strip_tags($data['slug']), '-');
                $data['slug'] = $slug;
                $data['module'] = 'content_section';
                $this->indexUrl->storeAssociate($data, $section);

                return $this->success($section,  __('global.alert.create_success', [
                    'attribute' => __('module/content.section.caption')
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
     * Update Section
     * @param array $data
     * @param array $where
     */
    public function updateSection($data, $where)
    {
        $section = $this->getSection($where);

        try {

            $this->setFieldSection($data, $section);
            if (Auth::guard()->check())
                $section->updated_by = Auth::user()['id'];

            $section->save();

            $slug = Str::slug(strip_tags($data['slug']), '-');
            $this->indexUrl->updateAssociate($slug, ['id' => $section['indexing']['id']]);

            return $this->success($section,  __('global.alert.update_success', [
                'attribute' => __('module/content.section.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Section
     * @param array $data
     * @param model $section
     */
    private function setFieldSection($data, $section)
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

        $section->slug = Str::slug(strip_tags($data['slug']), '-');
        $section->name = $name;
        $section->description = $description;
        $section->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $section->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $section->publish = (bool)$data['publish'];
        $section->public = (bool)$data['public'];
        $section->detail = (bool)$data['detail'];
        $section->locked = (bool)$data['locked'];
        $section->config = [
            'show_description' => (bool)$data['config_show_description'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'show_category' => (bool)$data['config_show_category'],
            'multiple_category' => (bool)$data['config_multiple_category'],
            'show_post' => (bool)$data['config_show_post'],
            'post_selected' => (bool)$data['config_post_selected'],
            'show_tags' => (bool)$data['config_show_tags'],
            'latest_post' => (bool)$data['config_latest_post'],
            'latest_post_limit' => $data['config_latest_post_limit'],
            'detail_category' => (bool)$data['config_detail_category'],
            'detail_post' => (bool)$data['config_detail_post'],
            'paginate_category' => (bool)$data['config_paginate_category'],
            'paginate_post' => (bool)$data['config_paginate_post'],
            'show_media' => (bool)$data['config_show_media'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
            'category_limit' => $data['config_category_limit'],
            'post_limit' => $data['config_post_limit'],
            'post_order_by' => $data['config_post_order_by'],
            'post_order_type' => $data['config_post_order_type'],
        ];
        $section->template_list_id = $data['template_list_id'] ?? null;
        $section->template_detail_category_id = $data['template_detail_category_id'] ?? null;
        $section->template_detail_post_id = $data['template_detail_post_id'] ?? null;
        $section->seo = [
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $section->custom_fields = $customField;
        } else {
            $section->custom_fields = null;
        }

        if (Auth::user()->hasRole('developer|super')) {
            if (isset($data['af_name'])) {

                $addonField = [];
                foreach ($data['af_name'] as $key => $value) {
                    $addonField[$key] = [
                        'name' => $value,
                        'type' => $data['af_type'][$key],
                        'value' => $data['af_value'][$key],
                    ];
                }

                $section->addon_fields = $addonField;
            } else {
                $section->addon_fields = null;
            }
        }

        return $section;
    }

    /**
     * Status Section (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusSection($field, $where)
    {
        $section = $this->getSection($where);

        try {

            $value = !$section[$field];
            if ($field == 'approved') {
                $value = $section['approved'] == 1 ? 0 : 1;
            }

            $section->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $section['updated_by'],
            ]);

            if ($field == 'publish') {
                $section->menus()->update([
                    'publish' => $section['publish']
                ]);
                $section->widgets()->update([
                    'publish' => $section['publish']
                ]);
            }

            return $this->success($section, __('global.alert.update_success', [
                'attribute' => __('module/content.section.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Section
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortSection($where, $position)
    {
        $section = $this->getSection($where);

        $section->position = $position;
        if (Auth::guard()->check()) {
            $section->updated_by = Auth::user()['id'];
        }
        $section->save();

        return $section;
    }

    /**
     * Set Position Section
     * @param array $where
     * @param int $position
     */
    public function positionSection($where, $position)
    {
        $section = $this->getSection($where);

        try {

            if ($position >= 1) {

                $this->sectionModel->where('position', $position)->update([
                    'position' => $section['position'],
                ]);

                $section->position = $position;
                if (Auth::guard()->check()) {
                    $section->updated_by = Auth::user()['id'];
                }
                $section->save();

                return $this->success($section, __('global.alert.update_success', [
                    'attribute' => __('module/content.section.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/content.section.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Record Section Hits
     * @param array $where
     */
    public function recordSectionHits($where)
    {
        $section = $this->getSection($where);

        if (empty(Session::get('contentSectionHits-'.$section['id']))) {
            Session::put('contentSectionHits-'.$section['id'], $section['id']);
            $section->hits = ($section->hits+1);
            $section->timestamps = false;
            $section->save();
        }

        return $section;
    }

    /**
     * Trash Section
     * @param array $where
     */
    public function trashSection($where)
    {
        $section = $this->getSection($where);

        try {

            $categories = $section->categories()->count();
            $posts = $section->posts()->count();

            if ($section['locked'] == 0 && $categories == 0 && $posts == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('support|admin|editor') && Auth::user()['id'] != $section['created_by']) {
                        return $this->error($section,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/content.section.caption')
                        ]));
                    }

                    $section->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $section->menus()->delete();
                $section->widgets()->delete();
                // $section->indexing->delete();
                $section->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/content.section.caption')
                ]));

            } else {
                return $this->error($section,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/content.section.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Section
     * @param array $where
     */
    public function restoreSection($where)
    {
        $section = $this->sectionModel->onlyTrashed()->firstWhere($where);

        try {

            $checkSlug = $this->getSection(['slug' => $section['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/content.section.caption')
                ]));
            }

            //restore data yang bersangkutan
            $section->menus()->restore();
            $section->widgets()->restore();
            // $section->indexing()->restore();
            $section->restore();

            return $this->success($section, __('global.alert.restore_success', [
                'attribute' => __('module/content.section.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Section (Permanent)
     * @param array $where
     */
    public function deleteSection($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $section = $this->sectionModel->onlyTrashed()->firstWhere($where);
        } else {
            $section = $this->getSection($where);
        }

        try {

            $section->menus()->forceDelete();
            $section->widgets()->forceDelete();
            $section->indexing()->forceDelete();
            $section->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/content.section.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // CATEGORY
    //--------------------------------------------------------------------------

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

        if (isset($filter['section_id']))
            $category->where('section_id', $filter['section_id']);

        if (isset($filter['publish']))
            $category->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $category->where('public', $filter['public']);

        if (isset($filter['approved']))
            $category->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $category->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $category->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $category->when($filter['q'], function ($category, $q) {
                $category->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(seo, "$.keywords")) like ?', ['"%' . strtolower($q) . '%"']);
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

            if ($limit > 0)
                $category->limit($limit);

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

            $category = new ContentCategory;
            $category->section_id = $data['section_id'];
            $this->setFieldCategory($data, $category);
            $category->position = $this->categoryModel->where('section_id', $data['section_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.content.category.approval') == true) {
                    $category->approved = 2;
                }
                $category->created_by = Auth::user()['id'];

            $category->save();

            return $this->success($category,  __('global.alert.create_success', [
                'attribute' => __('module/content.category.caption')
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
                'attribute' => __('module/content.category.caption')
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
        $langDefault = config('app.fallback_locale');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $name[$value['iso_codes']] = ($data['name_'.$value['iso_codes']] == null) ?
                $data['name_'.$langDefault] : $data['name_'.$value['iso_codes']];

            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];
        }

        $category->slug = Str::slug(strip_tags($data['slug']), '-');
        $category->name = $name;
        $category->description = $description;
        $category->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $category->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $category->publish = (bool)$data['publish'];
        $category->public = (bool)$data['public'];
        $category->detail = (bool)$data['detail'];
        $category->locked = (bool)$data['locked'];

        $category->config = [
            'show_description' => (bool)$data['config_show_description'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'paginate_post' => (bool)$data['config_paginate_post'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
            'post_limit' => $data['config_post_limit'],
        ];

        $category->template_id = $data['template_id'] ?? null;
        $category->seo = [
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
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

            $value = !$category[$field];
            if ($field == 'approved') {
                $value = $category['approved'] == 1 ? 0 : 1;
            }

            $category->update([
                $field => $value,
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
                'attribute' => __('module/content.category.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Category
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortCategory($where, $position)
    {
        $category = $this->getCategory($where);

        $category->position = $position;
        if (Auth::guard()->check()) {
            $category->updated_by = Auth::user()['id'];
        }
        $category->save();

        return $category;
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

                $this->categoryModel->where('section_id', $category['section_id'])
                    ->where('position', $position)->update([
                    'position' => $category['position'],
                ]);

                $category->position = $position;
                if (Auth::guard()->check()) {
                    $category->updated_by = Auth::user()['id'];
                }
                $category->save();

                return $this->success($category, __('global.alert.update_success', [
                    'attribute' => __('module/content.category.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/content.category.caption')
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

        if (empty(Session::get('contentCategoryHits-'.$category['id']))) {
            Session::put('contentCategoryHits-'.$category['id'], $category['id']);
            $category->hits = ($category->hits+1);
            $category->timestamps = false;
            $category->save();
        }

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

            $posts = $category->posts()->count();

            if ($category['locked'] == 0 && $posts == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $category['created_by']) {
                        return $this->error($category,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/content.category.caption')
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
                    'attribute' => __('module/content.category.caption')
                ]));

            } else {
                return $this->error($category,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/content.category.caption')
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
            $section = $this->getSection(['id' => $category['section_id']]);
            if (!empty($checkSlug) || empty($section)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/content.category.caption')
                ]));
            }

            //restore data yang bersangkutan
            $category->menus()->restore();
            $category->widgets()->restore();
            $category->restore();

            return $this->success($category, __('global.alert.restore_success', [
                'attribute' => __('module/content.category.caption')
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
                'attribute' => __('module/content.category.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // POST
    //--------------------------------------------------------------------------

    /**
     * Get Post List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getPostList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $post = $this->postModel->query();

        if ($isTrash == true)
            $post->onlyTrashed();

        if (isset($filter['section_id']))
            $post->where('section_id', $filter['section_id']);

        if (isset($filter['category_id']))
            $post->whereJsonContains('category_id', $filter['category_id']);

        if (isset($filter['publish']))
            $post->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $post->where('public', $filter['public']);

        if (isset($filter['approved']))
            $post->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $post->where('detail', $filter['detail']);

        if (isset($filter['selected']))
            $post->where('selected', $filter['selected']);

        if (isset($filter['created_by']))
            $post->where('created_by', $filter['created_by']);

        if (isset($filter['publish_start']))
            $post->where('publish_start', '>=', $filter['publish_start']);

        if (isset($filter['publish_end']))
            $post->where('publish_end', '<=', $filter['publish_end']);

        if (isset($filter['q']))
            $post->when($filter['q'], function ($post, $q) {
                $post->where(function ($post) use ($q) {
                    $post->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(content, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(seo, "$.keywords")) like ?', ['"%' . strtolower($q) . '%"']);
                });
            });

        if (isset($filter['tags']))
            $post->whereHas('tags', function ($post) use ($filter) {
                $post->whereHas('tag', function ($post) use ($filter) {
                    $post->where('name', $filter['tags']);
                });
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $post->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $post->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $post->paginate($limit);
        } else {

            if ($limit > 0)
                $post->limit($limit);

            $result = $post->get();
        }

        return $result;
    }

     /**
     * Get Latest Post
     * @param int $id
     * @param array $filter
     * @param int $limit
     */
    public function getLatestPost($id, $filter, $limit = 4)
    {
        $post = $this->postModel->query();

        if (isset($filter['section_id']))
            $post->where('section_id', $filter['section_id']);

        if (isset($filter['category_id']))
            $post->whereJsonContains('category_id', $filter['category_id']);

        $post->publish()->approved()->detail();
        if (!Auth::guard()->check())
            $post->public();

        $post->where('id', '!=', $id);

        if ($limit > 0) {
            $post->limit($limit);
        }

        $result = $post->inRandomOrder()->get();

        return $result;
    }

    /**
     * Get Post Prev Next
     * @param int $id
     * @param array $filter
     * @param string $type
     */
    public function postPrevNext($id, $filter = [], $type, $limit = 1)
    {
        $post = $this->postModel->query();

        if (isset($filter['section_id']))
            $post->where('section_id', $filter['section_id']);

        if (isset($filter['category_id']))
            $post->whereJsonContains('category_id', $filter['category_id']);

        $post->publish()->approved()->detail();
        if (!Auth::guard()->check())
            $post->public();

        if ($type == 'prev') {
            $post->where('id', '<', $id);
        }

        if ($type == 'next') {
            $post->where('id', '>', $id);
        }

        $post->where('id', '!=', $id);

        $result = $post->inRandomOrder()->limit($limit)->get();

        return $result;
    }

     /**
     * Get Post One
     * @param array $where
     * @param array $with
     */
    public function getPost($where, $with = [])
    {
        $post = $this->postModel->query();

        if (!empty($with))
            $post->with($with);

        $result = $post->firstWhere($where);;

        return $result;
    }

    /**
     * Create Post
     * @param array $data
     */
    public function storePost($data)
    {
        try {

            $sectionId = $data['section_id'];

            $post = new ContentPost;
            $post->section_id = $sectionId;
            $this->setFieldPost($data, $post, $sectionId);
            $post->position = $this->postModel->where('section_id', $sectionId)->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('editor') && config('module.content.post.approval') == true) {
                    $post->approved = 2;
                }
                $post->created_by = Auth::user()['id'];

            $post->save();

            if (isset($data['tags']))
                App::make(TagRepository::class)->wipeStore($data['tags'], $post);

            return $this->success($post,  __('global.alert.create_success', [
                'attribute' => __('module/content.post.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Post
     * @param array $data
     * @param array $where
     */
    public function updatePost($data, $where)
    {
        $post = $this->getPost($where);

        try {

            $this->setFieldPost($data, $post, $post['section_id']);
            if (Auth::guard()->check())
                $post->updated_by = Auth::user()['id'];

            $post->save();

            if (isset($data['tags']))
                App::make(TagRepository::class)->wipeStore($data['tags'], $post);

            return $this->success($post,  __('global.alert.update_success', [
                'attribute' => __('module/content.post.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Post
     * @param array $data
     * @param model $post
     */
    private function setFieldPost($data, $post, $sectionId = null)
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

        if (isset($data['category_id']) && count($data['category_id']) > 0) {
            $post->category_id = $data['category_id'];
        } else {
            $post->category_id = null;
        }
        $post->slug = Str::slug(strip_tags($data['slug']), '-');
        $post->title = $title;
        $post->intro = $intro;
        $post->content = $content;
        $post->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $post->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        $post->publish = (bool)$data['publish'];
        $post->public = (bool)$data['public'];
        $post->detail = (bool)$data['detail'];
        $post->locked = (bool)$data['locked'];
        $post->config = [
            'show_intro' => (bool)$data['config_show_intro'],
            'show_content' => (bool)$data['config_show_content'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'show_media' => (bool)$data['config_show_media'],
            'action_media' => (bool)$data['config_action_media'],
            'paginate_media' => (bool)$data['config_paginate_media'],
            'media_limit' => $data['config_media_limit'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
        ];
        $post->template_id = $data['template_id'] ?? null;
        $post->seo = [
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ];

        $post->posted_by_alias = $data['posted_by_alias'] ?? null;
        $post->publish_time = $data['publish_time'] ?? null;
        $post->publish_end = $data['publish_end'] ?? null;

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $post->custom_fields = $customField;
        } else {
            $post->custom_fields = null;
        }

        if ($sectionId != null) {
            $section = $this->getSection(['id' => $sectionId]);
            if (!empty($section['addon_fields'])) {
                $addonField = null;
                foreach ($section['addon_fields'] as $key => $value) {
                    if (isset($data['af_'.$value['name']])) {
                        if ($value['type'] == 'checkbox') {
                            $addonField[$value['name']] = $data['af_'.$value['name']];
                        } else {
                            $addonField[$value['name']] = Str::replace('"', '', $data['af_'.$value['name']]);
                        }
                    }
                }

                $post->addon_fields = $addonField;
            }
        }

        return $post;
    }

    /**
     * Status Post (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusPost($field, $where)
    {
        $post = $this->getPost($where);

        try {

            $value = !$post[$field];
            if ($field == 'approved') {
                $value = $post['approved'] == 1 ? 0 : 1;
            }

            $post->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $post['updated_by'],
            ]);

            if ($field == 'publish') {
                $post->menus()->update([
                    'publish' => $post['publish']
                ]);
            }

            return $this->success($post, __('global.alert.update_success', [
                'attribute' => __('module/content.post.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Post
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortPost($where, $position)
    {
        $post = $this->getPost($where);

        $post->position = $position;
        if (Auth::guard()->check()) {
            $post->updated_by = Auth::user()['id'];
        }
        $post->save();

        return $post;
    }

    /**
     * Set Position Post
     * @param array $where
     * @param int $position
     */
    public function positionPost($where, $position)
    {
        $post = $this->getPost($where);

        try {

            if ($position >= 1) {

                $this->postModel->where('section_id', $post['section_id'])
                    ->where('position', $position)->update([
                    'position' => $post['position'],
                ]);

                $post->position = $position;
                if (Auth::guard()->check()) {
                    $post->updated_by = Auth::user()['id'];
                }
                $post->save();

                return $this->success($post, __('global.alert.update_success', [
                    'attribute' => __('module/content.post.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/content.post.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Record Post Hits
     * @param array $where
     */
    public function recordPostHits($where)
    {
        $post = $this->getPost($where);

        if (empty(Session::get('contentPostHits-'.$post['id']))) {
            Session::put('contentPostHits-'.$post['id'], $post['id']);
            $post->hits = ($post->hits+1);
            $post->timestamps = false;
            $post->save();
        }

        return $post;
    }

    /**
     * Trash Post
     * @param array $where
     */
    public function trashPost($where)
    {
        $post = $this->getPost($where);

        try {

            if ($post['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $post['created_by']) {
                        return $this->error($post,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/content.post.caption')
                        ]));
                    }

                    $post->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $post->medias()->delete();
                $post->menus()->delete();
                $post->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/content.post.caption')
                ]));

            } else {
                return $this->error($post,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/content.post.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Post
     * @param array $where
     */
    public function restorePost($where)
    {
        $post = $this->postModel->onlyTrashed()->firstWhere($where);

        try {

            $checkSlug = $this->getPost(['slug' => $post['slug']]);
            $section = $this->getSection(['id' => $post['section_id']]);
            if (!empty($checkSlug) || empty($section)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/content.post.caption')
                ]));
            }

            //restore data yang bersangkutan
            $post->medias()->restore();
            $post->menus()->restore();
            $post->restore();

            return $this->success($post, __('global.alert.restore_success', [
                'attribute' => __('module/content.post.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Post (Permanent)
     * @param array $where
     */
    public function deletePost($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $post = $this->postModel->onlyTrashed()->firstWhere($where);
        } else {
            $post = $this->getPost($where);
        }

        try {

            $post->medias()->forceDelete();
            $post->tags()->delete();
            $post->menus()->forceDelete();
            $post->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/content.post.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }
}
