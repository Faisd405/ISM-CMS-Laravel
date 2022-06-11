<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ProfilePhotoRequest;
use App\Http\Requests\User\ProfileRequest;
use App\Http\Requests\User\UserRequest;
use App\Mail\VerificationEmail;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
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
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if (Auth::user()->hasRole('support')) {
            $filter['role_not'] = ['super'];
        }
        if (Auth::user()['roles'][0]['id'] >= 3) {
            $filter['role_not'] = ['super', 'support'];
        }
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        
        if ($request->input('role', '') != '') {
            $filter['role_in'] = [$request->input('role')];
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['users'] = $this->userService->getUserList($filter, true);
        $data['no'] = $data['users']->firstItem();
        $data['users']->withPath(url()->current().$param);
        $data['roles'] = $this->userService->getRoleByUser();

        return view('backend.users.index', compact('data'), [
            'title' => __('module/user.title'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.title') => '',
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
        
        if ($request->input('role', '') != '') {
            $filter['role_in'] = [$request->input('role')];
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['users'] = $this->userService->getUserList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['users']->firstItem();
        $data['users']->withPath(url()->current().$param);
        $data['roles'] = $this->userService->getRoleByUser();

        return view('backend.users.trash', compact('data'), [
            'title' => __('module/user.title').' - '.__('global.trash'),
            'routeBack' => route('user.index'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.title') => route('user.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function log(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if (!Auth::user()->hasRole('super')) {
            $filter['user_id'] = Auth::user()['id'];
        }
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        
        if ($request->input('event', '') != '') {
            $filter['event'] = $request->input('event');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['logs'] = $this->userService->getLogList($filter, true, 10, [], [
            'created_at' => 'DESC'
        ]);
        $data['no'] = $data['logs']->firstItem();
        $data['logs']->withPath(url()->current().$param);

        return view('backend.users.log', compact('data'), [
            'title' => __('module/user.log.title'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.log.caption') => '',
            ]
        ]);
    }

    public function loginFailed(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        
        if ($request->input('user_type', '') != '') {
            $filter['user_type'] = $request->input('user_type');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['login_faileds'] = $this->userService->getLoginFailedList($filter, true, 10, [], [
            'failed_time' => 'DESC'
        ]);
        $data['no'] = $data['login_faileds']->firstItem();
        $data['login_faileds']->withPath(url()->current().$param);

        return view('backend.users.login-failed', compact('data'), [
            'title' => __('module/user.login_failed.title'),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.login_failed.caption') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['roles'] = $this->userService->getRoleByUser(false);
        $data['permissions'] = Auth::user()['roles'][0]['permissions']->where('parent', 0);

        return view('backend.users.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('module/user.caption')
            ]),
            'routeBack' => route('user.index', $request->query()),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.caption') => route('user.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $user = $this->userService->store($data);
        $data['query'] = $request->query();

        if (isset($data['permissions']) && !empty($data['permissions'])) {
            $this->userService->syncPermissionUser($data['permissions'], ['id' => $user['data']['id']]);
        }

        if ($user['success'] == true) {
            return $this->redirectForm($data)->with('success', $user['message']);
        }

        return redirect()->back()->with('failed', $user['message']);
    }

    public function bypass(Request $request, $id)
    {
        $user = $this->userService->getUser(['id' => $id]);
        if (($user['roles'][0]['id'] < Auth::user()['roles'][0]['id'] ) 
            || ($id == Auth::user()['id'])) {
            return abort(403);
        }
        
        $auth = Auth::loginUsingId($id);
        if ($auth) {

            $this->userService->setSession($user['session']);

            return redirect()->route('dashboard')->with('success', __('auth.login_backend.alert.success'));
        }
        
        return redirect()->back()->with('failed', 'Bypass failed');
    }

    public function edit(Request $request, $id)
    {
        $data['user'] = $this->userService->getUser(['id' => $id]);
        if (empty($data['user']))
            return abort(404);

        $data['roles'] = $this->userService->getRoleByUser(false);
        $data['permissions'] = Auth::user()['roles'][0]['permissions']->where('parent', 0);
        $data['permission_ids'] = $data['user']['permissions']->pluck('id')->toArray();

        if (($data['user']['roles'][0]['id'] < Auth::user()['roles'][0]['id'] ) 
            || ($id == Auth::user()['id'])) {
            return abort(403);
        }

        return view('backend.users.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' => __('module/user.caption')
            ]),
            'routeBack' => route('user.index', $request->query()),
            'breadcrumbs' => [
                __('module/user.user_management_caption') => 'javascript:;',
                __('module/user.caption') => route('user.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $user = $this->userService->update($data, ['id' => $id]);
        $data['query'] = $request->query();

        if (isset($data['permissions']) && !empty($data['permissions'])) {
            $this->userService->syncPermissionUser($data['permissions'], ['id' => $id]);
        }

        if ($user['success'] == true) {
            return $this->redirectForm($data)->with('success', $user['message']);
        }

        return redirect()->back()->with('failed', $user['message']);
    }

    public function activate($id)
    {
        $user = $this->userService->activateAccount(['id' => $id]);

        if ($user['success'] == true) {
            return redirect()->back()->with('success', $user['message']);
        }

        return redirect()->back()->with('failed', $user['message']);
    }

    public function softDelete($id)
    {
        $user = $this->userService->trash(['id' => $id]);

        return $user;
    }

    public function permanentDelete(Request $request, $id)
    {
        $user = $this->userService->delete($request, ['id' => $id]);

        return $user;
    }

    public function restore($id)
    {
        $user = $this->userService->restore(['id' => $id]);

        if ($user['success'] == true) {
            return redirect()->back()->with('success', $user['message']);
        }

        return redirect()->back()->with('failed', $user['message']);
    }

    public function logDelete($id)
    {
        $log = $this->userService->deleteLog(['id' => $id]);

        return $log;
    }

    public function logReset()
    {
        $log = $this->userService->resetLog();

        if ($log['success'] == true) {
            return redirect()->back()->with('success', $log['message']);
        }

        return redirect()->back()->with('failed', $log['message']);
    }

    public function loginFailedDelete($ip)
    {
        $loginFailed = $this->userService->deleteLoginFailed(['ip_address' => $ip]);

        return $loginFailed;
    }

    public function loginFailedReset()
    {
        $loginFailed = $this->userService->resetLoginFailed();

        if ($loginFailed['success'] == true) {
            return redirect()->back()->with('success', $loginFailed['message']);
        }

        return redirect()->back()->with('failed', $loginFailed['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('user.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }

    //----------------
    // PROFILE
    //----------------

    public function profile()
    {
        $roleBackend = config('cms.module.auth.login.backend.role');
        if (!Auth::user()->hasRole($roleBackend))
           return redirect()->route('home');

        $data['user'] = $this->userService->getUser(['id' => Auth::user()['id']]);
        if (empty($data['user']))
            return abort(404);

        return view('backend.users.profile', compact('data'), [
            'title' => __('module/user.profile.title'),
            'breadcrumbs' => [
                __('module/user.profile.title') => 'javascript:;',
                $data['user']->name => '',
            ],
        ]);
    }

    public function updateProfile(ProfileRequest $request)
    {
        if (!empty($request->old_password) && !Hash::check($request->old_password, Auth::user()->password)) {
            return back()->with('warning', __('module/user.alert.warning_password_notmatch'));
        }

        $data = $request->all();
        $profile = $this->userService->updateProfile($data, ['id' => Auth::user()['id']]);

        if (!empty($request->old_password) && Hash::check($request->old_password, Auth::user()->password)) { 
            if (Auth::attempt([
                'email' => Auth::user()->email,
                'password' => $request->password
            ])) {

                return back()->with('success', __('global.alert.update_success', [
                    'attribute' => __('module/user.profile.title')
                ]));
            }
        }

        if ($profile['success'] == true) {
            return redirect()->back()->with('success', $profile['message']);
        }

        return redirect()->back()->with('failed', $profile['message']);
    }

    public function sendMailVerification()
    {
        $email = Auth::user()['email'];
        $expired = now()->format('YmdHis');

        if (config('cms.module.feature.notification.email.verification_email') == true) {
            $encrypt = Crypt::encrypt($email);
            $data = [
                'title' => __('mail.verification_email.title'),
                'name' => Auth::user()['name'],
                'email' => $email,
                'expired' => $expired,
                'link' => route('profile.email.verification', ['email' => $encrypt, 'expired' => $expired]),
            ];

            Mail::to($email)->send(new VerificationEmail($data));

            return back()->with('info', __('module/user.alert.verification_info'));
        } else {
            return back()->with('warning', __('module/user.alert.verification_warning'));
        }
    }

    public function verified(Request $request)
    {
        $email = Crypt::decrypt($request->route('email'));
        $expired = $request->route('expired');

        if ($expired >= now()->addHours(3)->format('YmdHis')) {
            return redirect()->route('dashboard')->with('warning', __('module/user.alert.warning_activate_expired'));
        }

        $verified = $this->userService->verificationEmail($email);

        if ($verified['success'] == true) {
            return redirect()->route('dashboard')->with('success', $verified['message']);
        }

        return redirect()->route('dashboard')->with('failed', $verified['message']);
    }

    public function changePhoto(ProfilePhotoRequest $request)
    {
        $photo = $this->userService->changePhoto($request, ['id' => Auth::user()['id']]);

        if ($photo['success'] == true) {
            return redirect()->back()->with('success', $photo['message']);
        }

        return redirect()->back()->with('failed', $photo['message']);
    }

    public function removePhoto()
    {
        $photo = $this->userService->removePhoto(['id' => Auth::user()['id']]);

        return $photo;
    }
}
