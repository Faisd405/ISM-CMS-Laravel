<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PermissionRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    private $userService;

    public function __construct(
        UserService $userService
    )
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filter['parent'] = 0;
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['permissions'] = $this->userService->getPermissionList($filter, true);
        $data['no'] = $data['permissions']->firstItem();
        $data['permissions']->withQueryString();

        return view('backend.users.acl.permission.index', compact('data'), [
            'title' => __('module/user.permission.title'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.acl_caption') => 'javascript:;',
                __('module/user.permission.caption') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['data'] = null;
        if (!empty($request->parent)) {
            $data['parent'] = $this->userService->getPermission(['id' => $request->parent]);
        }

        return view('backend.users.acl.permission.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/user.permission.caption')
            ]),
            'routeBack' => route('permission.index'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.acl_caption') => 'javascript:;',
                __('module/user.permission.caption') => route('permission.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(PermissionRequest $request)
    {
        $data = $request->all();
        $data['parent'] = $request->parent;
        $permission = $this->userService->storePermission($data);
        $data['query'] = $request->query();

        if ($permission['success'] == true) {
            return $this->redirectForm($data)->with('success', $permission['message']);
        }

        return redirect()->back()->with('failed', $permission['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['permission'] = $this->userService->getPermission(['id' => $id]);
        if (empty($data['permission']))
            return abort(404);

        if (!empty($data['permission']['parent'])) {
            $data['parent'] = $this->userService->getPermission(['id' => $data['permission']['parent']]);
        }

        return view('backend.users.acl.permission.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/user.permission.caption')
            ]),
            'routeBack' => route('permission.index'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.acl_caption') => 'javascript:;',
                __('module/user.permission.caption') => route('permission.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(PermissionRequest $request, $id)
    {
        $data = $request->all();
        $permission = $this->userService->updatePermission($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($permission['success'] == true) {
            return $this->redirectForm($data)->with('success', $permission['message']);
        }

        return redirect()->back()->with('failed', $permission['message']);
    }

    public function destroy($id)
    {
        $permission = $this->userService->deletePermission($id);

        return $permission;
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('permission.index');
        if ($data['action'] == 'back') {
            $redir = redirect()->back();
        }

        return $redir;
    }
}
