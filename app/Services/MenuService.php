<?php

namespace App\Services;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuCategory;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MenuService
{
    use ApiResponser;

    private $menuCategoryModel, $menuModel, $language;

    public function __construct(
        MenuCategory $menuCategoryModel,
        Menu $menuModel,
        LanguageService $language
    )
    {
        $this->menuCategoryModel = $menuCategoryModel;
        $this->menuModel = $menuModel;
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
        $menuCategory = $this->menuCategoryModel->query();

        if ($isTrash == true)
            $menuCategory->onlyTrashed();

        if (isset($filter['active']))
            $menuCategory->where('active', $filter['active']);

        if (isset($filter['q']))
            $menuCategory->when($filter['q'], function ($menuCategory, $q) {
                $menuCategory->where('name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $menuCategory->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $menuCategory->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $menuCategory->paginate($limit);
        } else {
            $result = $menuCategory->get();
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
        $menuCategory = $this->menuCategoryModel->query();

        if (!empty($with))
            $menuCategory->with($with);

        $result = $menuCategory->firstWhere($where);

        return $result;
    }

    /**
     * Create Category
     * @param array $data
     */
    public function storeCategory($data)
    {
        try {

            $menuCategory = $this->menuCategoryModel->create([
                'name' => Str::slug($data['name'], '_'),
                'active' => (bool)$data['active'],
                'locked' => (bool)$data['locked'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($menuCategory,  __('global.alert.create_success', [
                'attribute' => __('module/menu.category.caption')
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
        $menuCategory = $this->getCategory($where);

        try {
            
            $menuCategory->update([
                'name' => Str::slug($data['name'], '_'),
                'active' => (bool)$data['active'],
                'locked' => (bool)$data['locked'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $menuCategory['updated_by'],
            ]);

            return $this->success($menuCategory,  __('global.alert.update_success', [
                'attribute' => __('module/menu.category.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Status Category (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusCategory($field, $where)
    {
        $menuCategory = $this->getCategory($where);

        try {
            
            $menuCategory->update([
                $field => !$menuCategory[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $menuCategory['updated_by'],
            ]);

            return $this->success($menuCategory, __('global.alert.update_success', [
                'attribute' => __('module/menu.category.caption')
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
        $menuCategory = $this->getCategory($where);

        try {

            $menus = $menuCategory->menus()->count();
            
            if ($menuCategory['locked'] == 0 && $menus == 0) {
        
                if (Auth::guard()->check()) {
                    $menuCategory->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $menuCategory->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/menu.catgory.caption')
                ]));
    
            } else {
                return $this->error($menuCategory,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/menu.catgory.caption')
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
        $menuCategory = $this->menuCategoryModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $menuCategory->restore();

            return $this->success($menuCategory, __('global.alert.restore_success', [
                'attribute' => __('module/menu.category.caption')
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
            $menuCategory = $this->menuCategoryModel->onlyTrashed()->firstWhere($where);
        } else {
            $menuCategory = $this->getCategory($where);
        }

        try {
                
            $menuCategory->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/menu.category.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //---------------------------
    // MENU
    //---------------------------

    /**
     * Get Menu List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getMenuList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $menu = $this->menuModel->query();

        if ($isTrash == true)
            $menu->onlyTrashed();

        if (isset($filter['category_id']))
            $menu->where('menu_category_id', $filter['category_id']);

        if (isset($filter['parent']))
            $menu->where('parent', $filter['parent']);

        if (isset($filter['publish']))
            $menu->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $menu->where('public', $filter['public']);

        if (isset($filter['created_by']))
            $menu->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $menu->when($filter['q'], function ($menu, $q) {
                $menu->whereRaw('LOWER(JSON_EXTRACT(title, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $menu->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $menu->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $menu->paginate($limit);
        } else {
            $result = $menu->get();
        }

        return $result;
    }

    /**
     * Get Menu One
     * @param array $where
     * @param array $with
     */
    public function getMenu($where, $with = [])
    {
        $menu = $this->menuModel->query();

        if (!empty($with))
            $menu->with($with);

        $result = $menu->firstWhere($where);

        return $result;
    }

    /**
     * Create Menu
     * @param array $data
     */
    public function storeMenu($data)
    {
        try {

            $menu = new Menu;
            $menu->menu_category_id = $data['menu_category_id'];
            $menu->parent = $data['parent'];
            
            $this->setFieldMenu($data, $menu);

            $menu->position = $this->menuModel->where('menu_category_id', $data['menu_category_id'])
                ->max('position') + 1;
            if (Auth::guard()->check()) {

                if (Auth::user()->hasRole('editor') && config('module.menu.approval') == true) {
                    $menu->approved = 2;
                }
                $menu->created_by = Auth::user()['id'];
            }

            $menu->save();

            return $this->success($menu,  __('global.alert.create_success', [
                'attribute' => __('module/menu.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Menu
     * @param array $data
     * @param array $where
     */
    public function updateMenu($data, $where)
    {
        $menu = $this->getMenu($where);

        try {
            
            $this->setFieldMenu($data, $menu);

            if (Auth::guard()->check()) {
                $menu->updated_by = Auth::user()['id'];
            }
            $menu->save();

            return $this->success($menu,  __('global.alert.update_success', [
                'attribute' => __('module/menu.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Menu
     * @param array $data
     * @param model $menu
     */
    private function setFieldMenu($data, $menu)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('cms.module.feature.language.default');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $title[$value['iso_codes']] = ($data['title_'.$value['iso_codes']] == null) ?
                $data['title_'.$langDefault] : $data['title_'.$value['iso_codes']];
        }

        $menu->title = $title;
        $menu->publish = (bool)$data['publish'];
        $menu->public = (bool)$data['public'];
        $menu->locked = (bool)$data['locked'];
        $menu->config = [
            'url' => $data['url'],
            'target_blank' => (bool)$data['target_blank'],
            'not_from_module' => (bool)$data['not_from_module'],
            'icon' => $data['icon'] ?? null,
            'edit_public_menu' => (bool)$data['edit_public_menu'],
        ];
        
        return $menu;
    }

     /**
     * Status Mneu (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusMenu($field, $where)
    {
        $menu = $this->getMenu($where);

        try {
            
            $menu->update([
                $field => !$menu[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $menu['updated_by'],
            ]);

            return $this->success($menu, __('global.alert.update_success', [
                'attribute' => __('module/menu.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Menu
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function positionMenu($where, $position, $parent = null)
    {
        $menu = $this->getMenu($where);
        
        try {

            if ($position >= 1) {
    
                if ($parent != null) {
                    $this->menuModel->where('position', $position)->where('menu_category_id', $menu['menu_category_id'])
                        ->where('parent', $parent)->update([
                        'position' => $menu['position'],
                    ]);
                } else {
                    $this->menuModel->where('position', $position)->where('menu_category_id', $menu['menu_category_id'])
                        ->where('parent', 0)->update([
                        'position' => $menu['position'],
                    ]);
                }
    
                $menu->position = $position;
                if (Auth::guard()->check()) {
                    $menu->updated_by = Auth::user()['id'];
                }
                $menu->save();
    
                return $this->success($menu, __('global.alert.update_success', [
                    'attribute' => __('module/menu.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/menu.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Menu
     * @param array $where
     */
    public function trashMenu($where)
    {
        $menu = $this->getMenu($where);

        try {
            
            $childs = $menu->childs()->count();

            if ($menu['locked'] == 0 && $childs == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $menu['created_by']) {
                        return $this->error($menu,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/menu.caption')
                        ]));
                    }

                    $menu->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $menu->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/menu.caption')
                ]));
    
            } else {
                return $this->error($menu,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/menu.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore menu
     * @param array $where
     */
    public function restoreMenu($where)
    {
        $menu = $this->menuModel->onlyTrashed()->firstWhere($where);

        try {
            
            $checkParent = $this->getMenu(['id' => $menu['parent']]);
            if ($menu['parent'] > 0 && empty($checkParent)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/menu.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $menu->restore();

            return $this->success($menu, __('global.alert.restore_success', [
                'attribute' => __('module/menu.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete menu (Permanent)
     * @param array $where
     */
    public function deleteMenu($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $menu = $this->menuModel->onlyTrashed()->firstWhere($where);
        } else {
            $menu = $this->getmenu($where);
        }

        try {

            $menu->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/menu.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}