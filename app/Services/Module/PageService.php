<?php

namespace App\Services\Module;

use App\Models\Module\Page;
use App\Services\Feature\LanguageService;
use App\Services\IndexUrlService;
use App\Services\Master\TagService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    use ApiResponser;

    private $pageModel, $language, $indexUrl;

    public function __construct(
        Page $pageModel,
        LanguageService $language,
        IndexUrlService $indexUrl
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

        if (isset($filter['created_by']))
            $page->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $page->when($filter['q'], function ($page, $q) {
                $page->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(intro, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
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
                $slug = Str::slug($data['slug'], '-');
                $data['slug'] = $slug;
                $data['module'] = 'page';
                $this->indexUrl->storeAssociate($data, $page);

                if (isset($data['tags']))
                    App::make(TagService::class)->wipeStore($data['tags'], $page);

                return $this->success($page,  __('global.alert.create_success', [
                    'attribute' => __('module/page.caption')
                ]));

            } catch (Exception $e) {
            
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

            $slug = Str::slug($data['slug'], '-');
            $this->indexUrl->updateAssociate($slug, ['id' => $page['indexing']['id']]);

            if (isset($data['tags']))
                App::make(TagService::class)->wipeStore($data['tags'], $page);

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
        $langDefault = config('cms.module.feature.language.default');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $title[$value['iso_codes']] = ($data['title_'.$value['iso_codes']] == null) ?
                $data['title_'.$langDefault] : $data['title_'.$value['iso_codes']];

            $intro[$value['iso_codes']] = ($data['intro_'.$value['iso_codes']] == null) ?
                $data['intro_'.$langDefault] : $data['intro_'.$value['iso_codes']];

            $content[$value['iso_codes']] = ($data['content_'.$value['iso_codes']] == null) ?
                $data['content_'.$langDefault] : $data['content_'.$value['iso_codes']];
        }

        $page->slug = Str::slug($data['slug'], '-');
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
        $page->locked = (bool)$data['locked'];
        $page->config = [
            'is_detail' => (bool)$data['is_detail'],
            'hide_intro' => (bool)$data['hide_intro'],
            'hide_tags' => (bool)$data['hide_tags'],
            'hide_cover' => (bool)$data['hide_cover'],
            'hide_banner' => (bool)$data['hide_banner'],
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
            
            $page->update([
                $field => !$page[$field],
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
        $page->update([
            'hits' => ($page->hits+1)
        ]);

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

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $page['created_by']) {
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
            $page->indexing()->forceDelete();
            $page->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/page.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}