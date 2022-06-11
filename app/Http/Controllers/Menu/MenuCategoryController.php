<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuCategoryRequest;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuCategoryController extends Controller
{
    private $menuService;

    public function __construct(
        MenuService $menuService
    )
    {
        $this->menuService = $menuService;

    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['categories'] = $this->menuService->getCategoryList($filter, true);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.menus.category.index', compact('data'), [
            'title' => __('module/menu.category.title'),
            'breadcrumbs' => [
                __('module/menu.category.caption') => '',
            ]
        ]);
    }

    public function trash(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['categories'] = $this->menuService->getCategoryList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['categories']->firstItem();
        $data['categories']->withPath(url()->current().$param);

        return view('backend.menus.category.trash', compact('data'), [
            'title' => __('module/menu.category.title').' - '.__('global.trash'),
            'routeBack' => route('menu.category.index'),
            'breadcrumbs' => [
                __('module/menu.category.caption') => route('menu.category.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        return view('backend.menus.category.form', [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/menu.category.caption')
            ]),
            'routeBack' => route('menu.category.index', $request->query()),
            'breadcrumbs' => [
                __('module/menu.category.caption') => route('menu.category.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(MenuCategoryRequest $request)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $data['locked'] = $request->locked ?? 0;
        $menuCategory = $this->menuService->storeCategory($data);
        $data['query'] = $request->query();

        if ($menuCategory['success'] == true) {
            return $this->redirectForm($data)->with('success', $menuCategory['message']);
        }

        return redirect()->back()->with('failed', $menuCategory['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['category'] = $this->menuService->getCategory(['id' => $id]);
        if (empty($data['category']))
            return abort(404);

        return view('backend.menus.category.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('module/menu.category.caption')
            ]),
            'routeBack' => route('menu.category.index', $request->query()),
            'breadcrumbs' => [
                __('module/menu.category.caption') => route('menu.category.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(MenuCategoryRequest $request, $id)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $data['locked'] = $request->locked ?? 0;
        $menuCategory = $this->menuService->updateCategory($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($menuCategory['success'] == true) {
            return $this->redirectForm($data)->with('success', $menuCategory['message']);
        }

        return redirect()->back()->with('failed', $menuCategory['message']);
    }

    public function activate($id)
    {
        $menuCategory = $this->menuService->statusCategory('active', ['id' => $id]);

        if ($menuCategory['success'] == true) {
            return back()->with('success', $menuCategory['message']);
        }

        return redirect()->back()->with('failed', $menuCategory['message']);
    }

    public function softDelete($id)
    {
        $menuCategory = $this->menuService->trashCategory(['id' => $id]);

        return $menuCategory;
    }

    public function permanentDelete(Request $request, $id)
    {
        $menuCategory = $this->menuService->deleteCategory($request, ['id' => $id]);

        return $menuCategory;
    }

    public function restore($id)
    {
        $menuCategory = $this->menuService->restoreCategory(['id' => $id]);

        if ($menuCategory['success'] == true) {
            return redirect()->back()->with('success', $menuCategory['message']);
        }

        return redirect()->back()->with('failed', $menuCategory['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('menu.category.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
