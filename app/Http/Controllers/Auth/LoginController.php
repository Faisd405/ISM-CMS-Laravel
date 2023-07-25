<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginBackendRequest;
use App\Http\Requests\Auth\LoginFrontendRequest;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    use ValidatesRequests;

    private $userService;

    public function __construct(
        UserRepository $userService
    )
    {
        $this->userService = $userService;

        $this->loginBackend = config('cms.module.auth.login.backend.active');
        $this->loginFrontend = config('cms.module.auth.login.frontend.active');
    }

    public function showLoginBackendForm(Request $request)
    {
        if ($this->loginBackend == false)
            return abort(404);

        $data['failed_logins'] = $this->userService->getLoginFailedList([
            'ip_address' => $request->ip(),
            'user_type' => 0
        ], false);

        return view('auth.login-backend', compact('data'), [
            'title' => __('auth.login_backend.title')
        ]);
    }

    public function loginBackend(LoginBackendRequest $request)
    {
        if ($this->loginBackend == false)
            return abort(404);

        try {

            // Cek fitur lock login
            $lock = config('cms.module.auth.login.lock_failed');
            $totalFailed = $this->userService->getLoginFailedList([
                'ip_address' => $request->ip(),
                'user_type' => 0
            ], false)->count();
            $totalLock = config('cms.module.auth.login.backend.lock_total');

            $data['email'] = $request->username;
            $data['username'] = $request->username;
            $data['remember'] = $request->has('remember');
            $data['forms'] = $request->forms();

            $login = $this->userService->loginProccess($data, 'backend');
            if ($login['success'] == true) {

                if ($totalFailed > 0)
                    foreach ($this->userService->getLoginFailedList([
                        'ip_address' => $request->ip(),
                        'user_type' => 0
                    ], false) as $key => $value) {
                        $value->delete();
                    }

                return redirect()->route('dashboard')->with('success', $login['message']);

            } else {

                if ($lock == true) {

                    if ($totalFailed < $totalLock)
                        $dataLogin['ip'] = $request->ip();
                        $dataLogin['username'] = $request->username;
                        $dataLogin['password'] = $request->password;
                        $dataLogin['type'] = 0;
                        $this->userService->recordLoginfailed($dataLogin);

                    if ($totalFailed == ($totalLock-1)) {

                        //--- Email Developer
                        $dev = config('cmsConfig.dev.system_email');
                        $data = [
                            'title' => __('mail.login_failed.title'),
                            'ip_address' => $request->ip(),
                            'username' => $request->username,
                            'password' => $request->password
                        ];

                        if (config('cms.module.feature.notification.email.login_failed') == true)
                            Mail::to($dev)->send(new \App\Mail\LoginFailedMail($data));

                        return back()->with('warning', __('auth.lock_form_caption'));
                    }
                }

                return back()->with('failed', $login['message']);
            }

        } catch (Exception $e) {
            return back()->with('failed', $e->getMessage());
        }
    }

    public function logoutBackend()
    {
        $logout = $this->userService->logoutProccess();

        if ($logout['success'] == true) {
            return redirect()->route('login')->with('success', $logout['message']);
        }

        return redirect()->back()->with('failed', $logout['message']);
    }

    /**
     * login for frontend
     */
    public function showLoginFrontendForm(Request $request)
    {
        if ($this->loginFrontend == false)
            return abort(404);

        $data['failed_logins'] = $this->userService->getLoginFailedList([
            'ip_address' => $request->ip(),
            'user_type' => 1
        ], false);

        return view('auth.login-frontend', compact('data'), [
            'title' => __('auth.login_frontend.title')
        ]);
    }

    public function loginFrontend(LoginFrontendRequest $request)
    {
        if ($this->loginFrontend == false)
            return abort(404);

        try {

            $lock = config('cms.module.auth.login.lock_failed');
            $totalFailed = $this->userService->getLoginFailedList([
                'ip_address' => $request->ip(),
                'user_type' => 1
            ], false)->count();
            $totalLock = config('cms.module.auth.login.frontend.lock_total');

            $data['email'] = $request->username;
            $data['username'] = $request->username;
            $data['remember'] = $request->has('remember');
            $data['forms'] = $request->forms();
            $login = $this->userService->loginProccess($data, 'frontend');
            if ($login['success'] == true) {

                if ($totalFailed > 0)
                    foreach ($this->userService->getLoginFailedList([
                        'ip_address' => $request->ip(),
                        'user_type' => 1
                    ], false) as $key => $value) {
                        $value->delete();
                    }

                return redirect()->route('home')->with('success', $login['message']);
            } else {

                if ($lock == true) {

                    if ($totalFailed < $totalLock)
                        $dataLogin['ip'] = $request->ip();
                        $dataLogin['username'] = $request->username;
                        $dataLogin['password'] = $request->password;
                        $dataLogin['type'] = 1;
                        $this->userService->recordLoginfailed($dataLogin);

                    if ($totalFailed == ($totalLock-1)) {

                        //--- Email Developer
                        $dev = config('cmsConfig.dev.system_email');
                        $data = [
                            'title' => __('mail.login_failed.title'),
                            'ip_address' => $request->ip(),
                            'username' => $request->username,
                            'password' => $request->password
                        ];

                        if (config('cms.module.feature.notification.email.login_failed') == true)
                            Mail::to($dev)->send(new \App\Mail\LoginFailedMail($data));

                        return back()->with('warning', __('auth.lock_form_caption'));
                    }
                }

                return back()->with('failed', $login['message']);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('failed', $th->getMessage());
        }
    }

    public function logoutFrontend()
    {
        $logout = $this->userService->logoutProccess();

        if ($logout['success'] == true) {
            return redirect()->route('login.frontend')->with('success', $logout['message']);
        }

        return redirect()->back()->with('failed', $logout['message']);
    }
}
