<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RoleRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
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
        $filter = [];
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['roles'] = $this->userService->getRoleList($filter, true);
        $data['no'] = $data['roles']->firstItem();
        $data['roles']->withQueryString();

        return view('backend.users.acl.role.index', compact('data'), [
            'title' => __('module/user.role.title'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.acl_caption') => 'javascript:;',
                __('module/user.role.caption') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['permissions'] = $this->userService->getPermissionList(['parent' => 0], false, 0);

        return view('backend.users.acl.role.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/user.role.caption')
            ]),
            'routeBack' => route('role.index', $request->query()),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.acl_caption') => 'javascript:;',
                __('module/user.role.caption') => route('role.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(RoleRequest $request)
    {
        $data = $request->all();
        $data['is_register'] = (bool)$request->is_register;
        $data['locked'] = (bool)$request->locked;
        $role = $this->userService->storeRole($data);
        $data['query'] = $request->query();

        if ($role['success'] == true) {
            return $this->redirectForm($data)->with('success', $role['message']);
        }

        return redirect()->back()->with('failed', $role['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['role'] = $this->userService->getRole(['id' => $id]);
        if (empty($data['role']))
            return abort(404);

        $data['permissions'] = $this->userService->getPermissionList(['parent' => 0], false, 0);
        $data['permission_ids'] = $data['role']['permissions']->pluck('id')->toArray();

        return view('backend.users.acl.role.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/user.role.caption')
            ]),
            'routeBack' => route('role.index', $request->query()),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.acl_caption') => 'javascript:;',
                __('module/user.role.caption') => route('role.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(RoleRequest $request, $id)
    {
        $data = $request->all();
        $data['is_register'] = (bool)$request->is_register;
        $data['locked'] = (bool)$request->locked;
        $role = $this->userService->updateRole($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($role['success'] == true) {
            return $this->redirectForm($data)->with('success', $role['message']);
        }

        return redirect()->back()->with('failed', $role['message']);
    }

    public function destroy($id)
    {
        $role = $this->userService->deleteRole($id);

        return $role;
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('role.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
