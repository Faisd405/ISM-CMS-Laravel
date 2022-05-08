<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuRequest;
use App\Services\Feature\LanguageService;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    private $menuService, $languageService;

    public function __construct(
        MenuService $menuService,
        LanguageService $languageService
    )
    {
        $this->menuService = $menuService;
        $this->languageService = $languageService;

        $this->lang = config('cms.module.feature.language.multiple');
    }

    public function index(Request $request, $categoryId)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['category_id'] = $categoryId;
        $filter['parent'] = 0;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['menus'] = $this->menuService->getMenuList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['menus']->firstItem();
        $data['menus']->withPath(url()->current().$param);
        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);

        return view('backend.menus.index', compact('data'), [
            'title' => __('module/menu.title'),
            'routeBack' => route('menu.category.index'),
            'breadcrumbs' => [
                __('module/menu.category.caption') => route('menu.category.index'),
                __('module/menu.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request, $categoryId)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['menus'] = $this->menuService->getMenuList($filter, true, 10, true, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['menus']->firstItem();
        $data['menus']->withPath(url()->current().$param);
        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);

        return view('backend.menus.trash', compact('data'), [
            'title' => __('module/menu.title').' - '.__('global.trash'),
            'routeBack' => route('menu.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/menu.caption') => route('menu.index', ['categoryId' => $categoryId]),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request, $categoryId)
    {
        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        if ($request->input('parent', '') != '') {
            $data['parent'] = $this->menuService->getMenu(['id' => $request->input('parent')]);
        }

        return view('backend.menus.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/menu.caption')
            ]),
            'routeBack' => route('menu.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/menu.caption') => route('menu.index', ['categoryId' => $categoryId]),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(MenuRequest $request, $categoryId)
    {
        $data = $request->all();
        $data['menu_category_id'] = $categoryId;
        $data['parent'] = $request->parent ?? 0;
        $data['not_from_module'] = (bool)$request->not_from_module;
        $data['module'] = $request->module;
        $data['menuable_id'] = $request->menuable_id;
        $data['target_blank'] = (bool)$request->target_blank;
        $data['edit_public_menu'] = (bool)$request->edit_public_menu;
        $menu = $this->menuService->storeMenu($data);

        if ($menu['success'] == true) {
            return $this->redirectForm($data)->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    public function edit(Request $request, $categoryId, $id)
    {
        $data['menu'] = $this->menuService->getMenu(['id' => $id]);
        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);
        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        return view('backend.menus.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/menu.caption')
            ]),
            'routeBack' => route('menu.index', ['categoryId' => $categoryId]),
            'breadcrumbs' => [
                __('module/menu.caption') => route('menu.index', ['categoryId' => $categoryId]),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(MenuRequest $request, $categoryId, $id)
    {
        $data = $request->all();
        $data['menu_category_id'] = $categoryId;
        $data['not_from_module'] = (bool)$request->not_from_module;
        $data['module'] = $request->module;
        $data['menuable_id'] = $request->menuable_id;
        $data['target_blank'] = (bool)$request->target_blank;
        $data['edit_public_menu'] = (bool)$request->edit_public_menu;
        $menu = $this->menuService->updateMenu($data, ['id' => $id]);

        if ($menu['success'] == true) {
            return $this->redirectForm($data)->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    public function publish($categoryId, $id)
    {
        $menu = $this->menuService->statusMenu('publish', ['id' => $id]);

        if ($menu['success'] == true) {
            return back()->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    public function approved($categoryId, $id)
    {
        $menu = $this->menuService->statusMenu('approved', ['id' => $id]);

        if ($menu['success'] == true) {
            return back()->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    public function position(Request $request, $categoryId, $id, $position)
    {
        $menu = $this->menuService->positionMenu(['id' => $id], $position, $request->parent);

        if ($menu['success'] == true) {
            return back()->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    public function softDelete($categoryId, $id)
    {
        $menu = $this->menuService->trashMenu(['id' => $id]);

        return $menu;
    }

    public function permanentDelete(Request $request, $categoryId, $id)
    {
        $menu = $this->menuService->deleteMenu($request, ['id' => $id]);

        return $menu;
    }

    public function restore($categoryId, $id)
    {
        $menu = $this->menuService->restoreMenu(['id' => $id]);

        if ($menu['success'] == true) {
            return redirect()->back()->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('menu.index', ['categoryId' => $data['menu_category_id']]);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
