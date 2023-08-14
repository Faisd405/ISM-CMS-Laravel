<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuRequest;
use App\Services\Feature\LanguageService;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $filter['category_id'] = $categoryId;
        $filter['parent'] = 0;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['menus'] = $this->menuService->getMenuList($filter, true, 10, false, [], [
            'position' => 'ASC'
        ]);
        $data['no'] = $data['menus']->firstItem();
        $data['menus']->withQueryString();

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
        $filter['category_id'] = $categoryId;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }
        if ($request->input('publish', '') != '') {
            $filter['publish'] = $request->input('publish');
        }

        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['menus'] = $this->menuService->getMenuList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['menus']->firstItem();
        $data['menus']->withQueryString();

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
        if (empty($data['category']))
            return abort(404);

        $data['languages'] = $this->languageService->getLanguageActive($this->lang);

        $parent = $request->input('parent', '');
        if ($parent != '') {
            $data['parent'] = $this->menuService->getMenu(['id' => $parent]);
        }

        if ($parent == '' && !Auth::user()->hasRole('developer|super')) {
            return abort(403);
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
        $data['edit_public_menu'] = (bool)$request->event;
        $data['create_child'] = (bool)$request->create_child;
        $data['event'] = $request->event;
        $data['locked'] = (bool)$request->locked;
        $menu = $this->menuService->storeMenu($data);
        $data['query'] = $request->query();

        if ($menu['success'] == true) {
            return $this->redirectForm($data)->with('success', $menu['message']);
        }

        return redirect()->back()->with('failed', $menu['message']);
    }

    public function edit(Request $request, $categoryId, $id)
    {
        $data['category'] = $this->menuService->getCategory(['id' => $categoryId]);
        if (empty($data['category']))
            return abort(404);

        $data['menu'] = $this->menuService->getMenu(['id' => $id]);
        if (empty($data['menu']))
            return abort(404);

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
        $data['create_child'] = (bool)$request->create_child;
        $data['event'] = $request->event;
        $data['locked'] = (bool)$request->locked;
        $menu = $this->menuService->updateMenu($data, ['id' => $id]);
        $data['query'] = $request->query();

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
