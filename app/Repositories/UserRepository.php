<?php

namespace App\Repositories;

use App\Models\Feature\Registration;
use App\Models\User;
use App\Models\UserLog;
use App\Models\UserLoginFailed;
use App\Models\UserSession;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserRepository
{
    use ApiResponser;

    private $userModel, $userSessionModel, $userLogModel, $userLoginFailedModel,
        $roleModel, $permissionModel;

    public function __construct(
        User $userModel,
        UserSession $userSessionModel,
        UserLog $userLogModel,
        UserLoginFailed $userLoginFailedModel,
        Role $roleModel,
        Permission $permissionModel
    )
    {
        $this->userModel = $userModel;
        $this->userSessionModel = $userSessionModel;
        $this->userLogModel = $userLogModel;
        $this->roleModel =$roleModel;
        $this->permissionModel = $permissionModel;
        $this->userLoginFailedModel = $userLoginFailedModel;
    }

    //--------------------------------------------------------------------------
    // AUTHENTICATIONS
    // manage auth proccess
    //--------------------------------------------------------------------------

    /**
     * Login
     * @param array $data
     * @param string $loginType
     */
    public function loginProccess($data, $loginType = 'backend')
    {
        $user = $this->getUser(['username' => $data['username']]);
        if (empty($user))
            $user = $this->getUser(['email' => $data['email']]);

        $checkRole = $user->hasRole(config('cms.module.auth.login.backend.role'));
        if ($loginType == 'frontend')
            $checkRole = $user->hasRole(config('cms.module.auth.login.frontend.role'));

        try {

            if ($checkRole) {

                $isMaintenance = config('cmsConfig.dev.maintenance');
                if ($isMaintenance == true && $user->roles[0]['level'] > 3) {
                    return $this->error(null, __('auth.login_'.$loginType.'.alert.failed'));
                }

                $remember = $data['remember'] ? true : false;
                $auth = Auth::attempt($data['forms'], $remember);
                if ($auth) {

                    $this->setSession($user['session']);
                    // $auth['token'] = $user->createToken('User Token '.$user['id'])->plainTextToken;

                    return $this->success($auth, __('auth.login_'.$loginType.'.alert.success'));
                }

                return $this->error(null, __('auth.login_'.$loginType.'.alert.failed'));

            } else {
                return $this->error(null, __('auth.login_'.$loginType.'.alert.failed'));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Logout
     */
    public function logoutProccess()
    {
        try {

            $session = Auth::user()['session'];
            if (!empty($session)) {
                $session->update([
                    'last_activity' => now(),
                ]);
            }

            // Auth::user()->tokens()->delete();
            Auth::logout();
            Session::flush();

            return $this->success(null, __('auth.logout.alert.success'));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Session
     * @param model $session
     */
    public function setSession($session)
    {
        $ipAddress = request()->ip();
        $dateNow = now();
        $userAgent = request()->server('HTTP_USER_AGENT');

        if (empty($session)) {

            $this->userSessionModel->create([
                'user_id' => Auth::user()->id,
                'ip_address' => $ipAddress,
                'first_access' => $dateNow,
                'last_login' => $dateNow,
                'user_agent' => $userAgent
            ]);

        } else {

            $session->update([
                'ip_address' => $ipAddress,
                'last_login' => $dateNow,
                'user_agent' => $userAgent
            ]);
        }

        return true;
    }

    //--------------------------------------------------------------------------
    // USERS
    // manage data & proccess user
    //--------------------------------------------------------------------------

    /**
     * Get User List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getUserList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $user = $this->userModel->query();

        if ($isTrash == true)
            $user->onlyTrashed();

        if (isset($filter['role_not']) || isset($filter['role_in']))
            $user->whereHas('roles', function ($user) use ($filter) {
                if (isset($filter['role_not']))
                    $user->whereNotIn('name', $filter['role_not']);

                if (isset($filter['role_in']))
                    $user->whereIn('id', $filter['role_in']);
            });

        if (isset($filter['status']))
            $user->where('active', $filter['status']);

        if (isset($filter['verified']))
            $user->where('email_verified', $filter['verified']);

        if (isset($filter['created_by']))
            $user->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $user->when($filter['q'], function ($user, $q) {
                $user->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('username', 'like', '%'.$q.'%')
                    ->orWhere('phone', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $user->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $user->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $user->paginate($limit);
        } else {

            if ($limit > 0)
                $user->limit($limit);

            $result = $user->get();
        }

        return $result;
    }

    /**
     * Get User One
     * @param array $where
     * @param array $with
     */
    public function getUser($where, $with = [])
    {
        $user = $this->userModel->query();

        if (!empty($with))
            $user->with($with);

        $result = $user->firstWhere($where);

        return $result;
    }

    /**
     * Create User
     * @param array $data
     */
    public function store($data)
    {
        try {

            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->username = $data['username'];
            $user->password = Hash::make($data['password']);
            $user->active = $data['active'] ?? 0;
            $user->active_at = $data['active'] == 1 ? now() : null;
            $user->phone = $data['phone'] ?? null;

            if (isset($data['locked']))
                $user->locked = (bool)$data['locked'];

            $user->assignRole($data['roles']);

            if (Auth::guard()->check())
                $user->created_by = Auth::user()['id'];

            $user->save();

            return $this->success($user, __('global.alert.create_success', [
                'attribute' => __('module/user.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sync Permission for user
     * @param array $permission
     * @param array $where
     */
    public function syncPermissionUser($permission, $where)
    {
        $user = $this->getUser($where);
        $user->syncPermissions($permission);

        return $user;
    }

    /**
     * Update User
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $user = $this->getUser($where);

        try {

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->username = $data['username'];

            if ($data['email'] != $data['old_email'])
                $user->email_verified = 0;
                $user->email_verified_at = null;

            if ($data['password'] != '')
                $user->password = Hash::make($data['password']);

            if ($data['roles'] != 'editor')
                $user->permissions()->delete();

            $user->active = (bool)$data['active'];
            $user->active_at = ((bool)$data['active'] == 1) ? now() : null;
            $user->phone = $data['phone'] ?? null;

            if (isset($data['locked']))
                $user->locked = (bool)$data['locked'];

            $user->syncRoles($data['roles']);

            if (Auth::guard()->check())
                $user->updated_by = Auth::user()['id'];

            $user->save();

            return $this->success($user, __('global.alert.update_success', [
                'attribute' => __('module/user.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Activate Account
     * @param array $where
     */
    public function activateAccount($where)
    {
        $user = $this->getUser($where);

        try {

            $user->active = !$user['active'];
            $user->active_at = $user['active'] == 1 ? now() : null;

            if (Auth::guard()->check())
                $user->updated_by = Auth::user()['id'];

            $user->save();

            return $this->success($user, __('module/user.alert.activate_success'));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Verification Email
     * @param string $email
     */
    public function verificationEmail($email)
    {
        $user = $this->getUser(['email' => $email]);

        try {

            $user->email_verified = 1;
            $user->email_verified_at = now();

            if (Auth::guard()->check())
                $user->updated_by = Auth::user()['id'];

            $user->save();

            return $this->success($user, __('module/user.alert.verification_success'));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Update Profile
     * @param array $data
     * @param array $where
     */
    public function updateProfile($data, $where)
    {
        $user = $this->getUser($where);

        try {

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->username = $data['username'];

            if ($data['email'] != $data['old_email'])
                $user->email_verified = 0;
                $user->email_verified_at = null;

            if ($data['password'] != '')
                $user->password = Hash::make($data['password']);

            $user->phone = $data['phone'] ?? null;

            if (Auth::guard()->check())
                $user->updated_by = Auth::user()['id'];

            $user->save();

            return $this->success($user, __('global.alert.update_success', [
                'attribute' => __('module/user.profile.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Change Photo
     * @param request $request
     * @param array $where
     */
    public function changePhoto($request, $where)
    {
        $user = $this->getUser($where);

        try {

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $name = Str::slug($user['username'], '-');
                $fileName = $name.'.'.$file->getClientOriginalExtension();

                Storage::delete(config('cms.files.avatar.path').
                    $request['old_avatar']);
                Storage::put(config('cms.files.avatar.path').
                    $fileName, file_get_contents($file));

                $user->photo = [
                    'filename' => $fileName,
                    'filetype' => $file->getClientOriginalExtension(),
                    'filesize' => $file->getSize()
                ];

                if (Auth::guard()->check())
                    $user->updated_by = Auth::user()['id'];

                $user->save();

                return $this->success($user, __('global.alert.update_success', [
                    'attribute' => __('module/user.label.photo')
                ]));

            } else {
                return $this->success($user, __('global.alert.update_failed', [
                    'attribute' => __('module/user.label.photo')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Remove Photo
     * @param array $where
     */
    public function removePhoto($where)
    {
        $user = $this->getUser($where);

        try {

            Storage::delete(config('cms.files.avatar.path').
                $user['photo']['filename']);
            $user['photo'] = null;

            if (Auth::guard()->check())
                $user->updated_by = Auth::user()['id'];

            $user->save();

            return $this->success($user, __('global.alert.delete_success', [
                'attribute' => __('module/user.label.photo')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }


    /**
     * Trash User
     * @param array $where
     */
    public function trash($where)
    {
        $user = $this->getUser($where);

        try {

            $logs = $user['logs']->count();

            if ($user['locked'] == 0 && $logs == 0) {

                if (Auth::guard()->check())
                    $user->deleted_by = Auth::user()['id'];

                $user->save();

                //hapus data yang bersangkutan
                $user->delete();

                return $this->success($user, __('global.alert.delete_success', [
                    'attribute' => __('module/user.caption')
                ]));

            } else {
                return $this->error(null, __('global.alert.delete_failed_used', [
                    'attribute' => __('module/user.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Restore Usr
     * @param array $where
     */
    public function restore($where)
    {
        $user = $this->userModel->onlyTrashed()->firstWhere($where);

        try {

            //restore data yang bersangkutan
            $user->restore();

            return $this->success($user, __('global.alert.restore_success', [
                'attribute' => __('module/user.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete User (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $user = $this->userModel->onlyTrashed()->where($where)->first();
        } else {
            $user = $this->getUser($where);
        }

        try {

            $logs = $user['logs']->count();

            if ($user['locked'] == 0 && $logs == 0) {

                if (!empty($user['photo']))
                    Storage::delete(config('cms.files.avatar.path').
                        $user['photo']['filename']);

                //hapus data tabel tambahan jika ada user dengan role berbeda & memiliki tabel tersendiri
                if (!empty($user['session']))
                    $user->session()->delete();

                $user->logs()->delete();
                $user->permissions()->delete();
                $user->forceDelete();

                return $this->success(null, __('global.alert.delete_success', [
                    'attribute' => __('module/user.caption')
                ]));

            } else {
                return $this->error(null, __('global.alert.delete_failed_used', [
                    'attribute' => __('module/user.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // LOGS
    // manage data & proccess log
    //--------------------------------------------------------------------------

    /**
     * Get Log List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param array $with
     * @param array $orderBy
     */
    public function getLogList($filter = [], $withPaginate = true, $limit = 10,
         $with = [], $orderBy = [])
    {
        $log = $this->userLogModel->query();

        if (isset($filter['user_id']))
            $log->where('user_id', $filter['user_id']);

        if (isset($filter['event']))
            $log->where('event', $filter['event']);

        if (isset($filter['q']))
            $log->when($filter['q'], function ($log, $q) {
                $log->whereHas('user', function (Builder $queryB) use ($q) {
                    $queryB->where('name', 'like', '%'.$q.'%');
                })->orWhere('ip_address', 'like', '%'.$q.'%')
                    ->orWhere('logable_name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $log->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $log->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $log->paginate($limit);
        } else {

            if ($limit > 0)
                $log->limit($limit);

            $result = $log->get();
        }

        return $result;
    }

    /**
     * Record Log
     * @param array $data
     * @param model $model
     */
    public function recordLog($data, $model)
    {
        try {

            $log = new UserLog();
            $logData = $log->logable()->associate($model);

            if (Auth::check() == true) {
                UserLog::create([
                    'user_id' => isset($data['user_id']) ? $data['user_id'] : Auth::user()['id'],
                    'event' => $data['event'],
                    'content' => isset($data['content']) ? $data['content'] : null,
                    'logable_id' => $logData['logable_id'],
                    'logable_type' => $logData['logable_type'],
                    'logable_name' => isset($data['logable_name']) ? $data['logable_name'] : $model->getTable(),
                    'ip_address' => request()->ip(),
                ]);
            }

        } catch (Exception $e) {
            //throw $e;
        }
    }

    /**
     * Reset Log Failed (delete all row)
     */
    public function resetLog()
    {
        $log = $this->userLogModel;

        try {

            if ($log->count() == 0)
                return $this->error(null,  __('global.data_attr_empty', [
                    'attribute' => __('module/user.log.caption')
                ]));

            $log->truncate();

            return $this->success(null,  __('global.alert.reset_success', [
                'attribute' => __('module/user.log.caption')
            ]));

        } catch (Exception $th) {

            return $this->error(null,  $th->getMessage());
        }
    }

    /**
     * Delete Log (Permanent)
     * @param array $where
     */
    public function deleteLog($where)
    {
        $log = $this->userLogModel->firstWhere($where);

        try {

            $log->delete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/user.log.caption')
            ]));

        } catch (Exception $th) {

            return $this->error(null,  $th->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // LOGIN FAILED
    // manage data & proccess login failed
    //--------------------------------------------------------------------------

    /**
     * Get Login Failed List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param array $with
     * @param array $orderBy
     */
    public function getLoginFailedList($filter = [], $withPaginate = true, $limit = 10,
         $with = [], $orderBy = [])
    {
        $loginFailed = $this->userLoginFailedModel->query();

        if (isset($filter['ip_address']))
            $loginFailed->where('ip_address', $filter['ip_address']);

        if (isset($filter['user_type']))
            $loginFailed->where('user_type', $filter['user_type']);

        if (isset($filter['failed_time']))
            $loginFailed->where('failed_time', $filter['failed_time']);

        if (isset($filter['q']))
            $loginFailed->when($filter['q'], function ($loginFailed, $q) {
                $loginFailed->where('username', 'like', '%'.$q.'%')
                        ->orWhere('password', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $loginFailed->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $loginFailed->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $loginFailed->paginate($limit);
        } else {

            if ($limit > 0)
                $loginFailed->limit($limit);

            $result = $loginFailed->get();
        }

        return $result;
    }

    /**
     * Record Failed Login
     * @param array $data
     */
    public function recordLoginfailed($data)
    {
        $failed = $this->userLoginFailedModel->create([
            'ip_address' => $data['ip'],
            'username' => $data['username'],
            'password' => $data['password'],
            'failed_time' => now(),
            'user_type' => $data['type']
        ]);

        return $failed;
    }

    /**
     * Reset Login Failed (delete all row)
     */
    public function resetLoginFailed()
    {
        $loginFailed = $this->userLoginFailedModel;

        try {

            if ($loginFailed->count() == 0)
                return $this->error(null,  __('global.data_attr_empty', [
                    'attribute' => __('module/user.login_failed.caption')
                ]));

            $loginFailed->truncate();

            return $this->success(null,  __('global.alert.reset_success', [
                'attribute' => __('module/user.login_failed.caption')
            ]));

        } catch (Exception $th) {

            return $this->error(null,  $th->getMessage());
        }
    }

    /**
     * Delete Login Failed (Permanent)
     * @param array $where
     */
    public function deleteLoginFailed($where)
    {
        $loginFailed = $this->userLoginFailedModel->firstWhere($where);

        try {

            $loginFailed->delete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/user.login_failed.caption')
            ]));

        } catch (Exception $th) {

            return $this->error(null,  $th->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // ROLES
    // manage data & proccess role
    //--------------------------------------------------------------------------

    /**
     * Get Role List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param array $with
     * @param array $orderBy
     */
    public function getRoleList($filter = [], $withPaginate = true, $limit = 10,
        $with = [], $orderBy = [])
    {
        $role = $this->roleModel->query();

        if (isset($filter['role_in']))
            $role->whereIn('id', $filter['role_in']);

        if (isset($filter['role_not']))
            $role->whereNotIn('id', $filter['role_not']);

        if (isset($filter['level']))
            $role->where('level', $filter['level']);

        if (isset($filter['is_register']))
            $role->where('is_register', $filter['is_register']);

        if (isset($filter['q']))
            $role->when($filter['q'], function ($role, $q) {
                $role->where('name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $role->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $role->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $role->paginate($limit);
        } else {

            if ($limit > 0)
                $role->limit($limit);

            $result = $role->get();
        }

        return $result;
    }

    /**
     * Get Role By User
     * @param booleean $allRole
     */

    public function getRoleByUser($allRole = true)
    {
        $role = $this->roleModel->query();

        if ($allRole == false) {
            $role->where('level', '>=', Auth::user()->roles[0]->level);
            // $role->whereNotIn('name', ['member']);
        }

        if ($allRole == true && !Auth::user()->hasRole('developer|super|support')) {
            $role->where('level', '>=', 4);
        }

        $result = $role->get();

        return $result;
    }

    /**
     * Get Role One
     * @param array $where
     * @param array $with
     */
    public function getRole($where, $with = [])
    {
        $role = $this->roleModel->query();

        if (!empty($with))
            $role->with($with);

        $result = $role->firstWhere($where);;

        return $result;
    }

    /**
     * Create Role
     * @param array $data
     */
    public function storeRole($data)
    {
        try {

            $role = $this->roleModel->create([
                'name' => Str::slug($data['name'], '_'),
                'level' => $data['level'],
                'is_register' => (bool)$data['is_register'],
                'guard_name' => 'web',
                'locked' => (bool)$data['locked'],
            ]);

            if (isset($data['permission']))
                $this->syncPermissionRole($data['permission'], $role['id']);

            $this->recordLog([
                'event' => 1,
                'content' => $role,
                'logable_name' => 'role'
            ], $role);

            return $this->success($role, __('global.alert.create_success', [
                'attribute' => __('module/user.role.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Update Role
     * @param array $data
     * @param array $where
     */
    public function updateRole($data, $where)
    {
        $role = $this->getRole($where);
        $oldData = $this->getRole($where);

        if (!empty($data['permission'])) {
            $permission = $data['permission'];
        } else {
            $permission = [];
        }

        try {

            $role->update([
                'name' => Str::slug($data['name'], '_'),
                'level' => $data['level'],
                'is_register' => (bool)$data['is_register'],
                'guard_name' => 'web',
                'locked' => (bool)$data['locked'],
            ]);

            $this->recordLog([
                'event' => 2,
                'content' => [
                    'old' => $oldData,
                    'new' => $role
                ],
                'logable_name' => 'role'
            ], $role);

            $this->syncPermissionRole($permission, $role['id']);

            return $this->success($role, __('global.alert.update_success', [
                'attribute' => __('module/user.role.caption')
            ]));


        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sync Permission Role
     * @param string $permission
     * @param int $id
     */
    public function syncPermissionRole($permission, $id)
    {
        $role = $this->getRole(['id' => $id]);
        $role->syncPermissions($permission);

        return $role;
    }

    /**
     * Delete Role (Permanent)
     * @param int $id
     */
    public function deleteRole($id)
    {
        $role = $this->getRole(['id' => $id]);

        try {

            $hasRole = DB::table('model_has_roles')
                ->where('role_id', $id)
                ->count();
            $hasPermission = DB::table('role_has_permissions')
                ->where('role_id', $id)
                ->count();
            $registration = Registration::whereJsonContains('roles', $id)->count();

            if ($role['locked'] == 0 && $hasRole == 0 && $hasPermission == 0
                && $registration == 0) {

                $this->recordLog([
                    'event' => 0,
                    'content' => $role,
                    'logable_name' => 'role'
                ], $role);

                $role->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => 'Role'
                ]));

            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/user.role.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // PERMISSIONS
    // manage data & proccess permission
    //--------------------------------------------------------------------------

        /**
     * Get Permission List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param array $with
     * @param array $orderBy
     */
    public function getPermissionList($filter = [], $withPaginate = true, $limit = 10,
        $with = [], $orderBy = [])
    {
        $permission = $this->permissionModel->query();

        if (isset($filter['parent']))
            $permission->where('parent', $filter['parent']);

        if (isset($filter['q']))
            $permission->when($filter['q'], function ($permission, $q) {
                $permission->where('name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $permission->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $permission->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $permission->paginate($limit);
        } else {

            if ($limit > 0)
                $permission->limit($limit);

            $result = $permission->get();
        }

        return $result;
    }

    /**
     * Get Permission One
     * @param array $where
     * @param array $with
     */
    public function getPermission($where, $with = [])
    {
        $permission = $this->permissionModel->query();

        if (!empty($with))
            $permission->with($with);

        $result = $permission->firstWhere($where);;

        return $result;
    }

    /**
     * Create Permission
     * @param array $data
     */
    public function storePermission($data)
    {
        try {

            $permission = $this->permissionModel->create([
                'parent' => $data['parent'] ?? 0,
                'name' => Str::slug($data['name'], '_'),
                'guard_name' => 'web',
                'locked' => (bool)$data['locked'],
            ]);

            $this->recordLog([
                'event' => 1,
                'content' => $permission,
                'logable_name' => 'permission'
            ], $permission);

            return $this->success($permission,  __('global.alert.create_success', [
                'attribute' => __('module/user.permission.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Permission
     * @param array $data
     */
    public function updatePermission($data, $where)
    {
        $permission = $this->getPermission($where);
        $oldData = $this->getPermission($where);

        try {

            $permission->update([
                'name' => Str::slug($data['name'], '_'),
                'guard_name' => 'web',
                'locked' => (bool)$data['locked'],
            ]);

            $this->recordLog([
                'event' => 2,
                'content' => [
                    'old' => $oldData,
                    'new' => $permission
                ],
                'logable_name' => 'permission'
            ], $permission);

            return $this->success($permission,  __('global.alert.update_success', [
                'attribute' => __('module/user.permission.caption')
            ]));


        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Delete Permission (Permanent)
     * @param int $id
     */
    public function deletePermission($id)
    {
        try {

            $permission = $this->getPermission(['id' => $id]);
            $roleHasPermission = DB::table('role_has_permissions')
                ->where('permission_id', $id)
                ->count();
            $modelHasPermission = DB::table('model_has_permissions')
                ->where('permission_id', $id)
                ->count();

            if ($permission['locked'] == 0 && $roleHasPermission == 0
                && $modelHasPermission == 0) {

                $this->recordLog([
                    'event' => 0,
                    'content' => $permission,
                    'logable_name' => 'permission'
                ], $permission);

                Permission::where('parent', $id)->delete();
                $permission->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/user.permission.caption')
                ]));

            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/user.permission.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }
}
