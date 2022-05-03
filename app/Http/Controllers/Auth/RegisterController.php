<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ActivateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Feature\NotificationService;
use App\Services\Feature\RegistrationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    private $userService, $registrationService, $notifService;

    public function __construct(
        UserService $userService,
        RegistrationService $registrationService,
        NotificationService $notifService
    )
    {
        $this->userService = $userService;
        $this->registrationService = $registrationService;
        $this->notifService = $notifService;
    }

    public function showRegisterForm(Request $request)
    {
        $data['register'] = $this->registrationService->getRegistration(['type' => 0]);

        if ($data['register']['active'] == 0)
            return abort(404);

        if (empty($data['register']['roles']))
            return abort(404);

        return view('auth.register', compact('data'), [
            'title' => __('auth.register.title')
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $register = $this->registrationService->getRegistration(['type' => 0]);
        $loginAfterRegister = config('cms.module.auth.register.is_login');

        if ($register['active'] == 0)
            return abort(404);
            
        if (empty($register['roles']))
            return abort(404);

        try {
            
            $data = $request->all();
            if (count($register['roles']) == 1) {
                $data['roles'] = $register->getRoles()[0]['name'];
            }

            $data['active'] = 0;
            if ($loginAfterRegister == true) {
                $data['active'] = 1;
            }
            $register = $this->userService->store($data);

            if ($register['success'] == true) {
                
                if ($loginAfterRegister == true) {
                    Auth::login($register['data']);

                    return redirect()->route('dashboard')->with('success', __('auth.register.alert.success'));
                } else {

                    $email = Crypt::encrypt($request->email);
                    $expired = now()->addHours(3)->format('YmdHis');
                    $data = [
                        'title' => __('mail.activate_account.title'),
                        'email' => $request->email,
                        'name' => $request->name,
                        'expired' => $expired,
                        'link' => route('register.activate', ['email' => $email, 'expired' => $expired]),
                    ];
        
                    if (config('cms.module.feature.notification.email.activate_account') == true) {
                        Mail::to($request->email)->send(new \App\Mail\ActivateAccountMail($data));
                    }
                }

                if (config('cms.module.feature.notification.apps.register') == true) {
                    $this->notifService->sendNotif([
                        'user_from' => $register['data']['id'],
                        'user_to' => $this->userService->getUserList(['role_in' => [1, 2, 3]], false)
                            ->pluck('id')->toArray(),
                        'attribute' => [
                            'icon' => 'las la-user',
                            'color' => 'success',
                            'title' => __('feature/notification.register.title'),
                            'content' =>  __('feature/notification.register.text', [
                                'attribute' => $request->name
                            ]),
                        ],
                        'read_by' => [],
                        'link' => 'admin/user?q='.$request->email.'&'
                    ]);
                }
    
                return redirect()->route('login.frontend')->with('success', __('auth.register.alert.success'));
    
            } else {
                return back()->with('failed', $register['message']);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('failed', $th->getMessage());
        }
    }

    public function showActivateForm(Request $request)
    {
        return view('auth.activate', [
            'title' => __('auth.activate.title')
        ]);
    }

    public function sendLinkActivate(ActivateRequest $request)
    {
        if (config('cms.module.auth.register.activate_account') == false)
            return abort(404);

        $user = $this->userService->getUser(['email' => $request->email]);

        $email = Crypt::encrypt($request->email);
        $expired = now()->addHours(3)->format('YmdHis');
        $data = [
            'title' => __('mail.activate_account.title'),
            'email' => $request->email,
            'name' => $user['name'],
            'expired' => $expired,
            'link' => route('register.activate', ['email' => $email, 'expired' => $expired]),
        ];

        if (config('cms.module.feature.notification.email.activate_account') == true) {
            Mail::to($request->email)->send(new \App\Mail\ActivateAccountMail($data));
        }

        return redirect()->back()->with('info', __('auth.activate.alert.info_active'));
    }

    public function activate(Request $request)
    {
        if (config('cms.module.auth.register.activate_account') == false)
            return abort(404);

        $email = Crypt::decrypt($request->route('email'));
        $expired = $request->route('expired');

        if ($expired >= now()->addHours(3)->format('YmdHis')) {
            return redirect()->route('login.frontend')->with('warning', __('auth.register.alert.warning_expired'));
        }

        $activate = $this->userService->activateAccount(['email' => $email]);

        if ($activate['success'] == true) {
            return redirect()->route('login.frontend')->with('info', __('auth.register.alert.info_active'));
        }

        return redirect()->route('login.frontend')->with('failed', $activate['message']);
    }
}
