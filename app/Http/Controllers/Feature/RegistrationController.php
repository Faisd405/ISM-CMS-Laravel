<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\RegistrationRequest;
use App\Services\Feature\RegistrationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    private $registrationService, $userService;

    public function __construct(
        RegistrationService $registrationService,
        UserService $userService
    )
    {
        $this->registrationService = $registrationService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());
        $filter = [];

        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['registrations'] = $this->registrationService->getRegistrationList($filter, true);
        $data['no'] = $data['registrations']->firstItem();
        $data['registrations']->withPath(url()->current().$param);

        return view('backend.features.registration.index', compact('data'), [
            'title' => __('feature/registration.title'),
            'breadcrumbs' => [
                __('feature/registration.caption') => '',
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
        if ($request->input('type', '') != '') {
            $filter['type'] = $request->input('type');
        }
        if ($request->input('status', '') != '') {
            $filter['active'] = $request->input('status');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['registrations'] = $this->registrationService->getRegistrationList($filter, true, 10, true, [], [
            'deleted_at' => 'DESC'
        ]);
        $data['no'] = $data['registrations']->firstItem();
        $data['registrations']->withPath(url()->current().$param);

        return view('backend.features.registration.trash', compact('data'), [
            'title' => __('feature/registration.title').' - '.__('global.trash'),
            'routeBack' => route('registration.index'),
            'breadcrumbs' => [
                __('feature/registration.caption') => route('registration.index'),
                __('global.trash') => '',
            ]
        ]);
    }

    public function create(Request $request)
    {
        $data['roles'] = $this->userService->getRoleList(['role_not' => [1, 2, 3, 4]], false);

        return view('backend.features.registration.form', compact('data'), [
            'title' => __('global.add_attr_new', [
                'attribute' => __('feature/registration.caption')
            ]),
            'routeBack' => route('registration.index', $request->query()),
            'breadcrumbs' => [
                __('feature/registration.caption') => route('registration.index'),
                __('global.add') => '',
            ]
        ]);
    }

    public function store(RegistrationRequest $request)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $registration = $this->registrationService->store($data);
        $data['query'] = $request->query();

        if ($registration['success'] == true) {
            return $this->redirectForm($data)->with('success', $registration['message']);
        }

        return redirect()->back()->with('failed', $registration['message']);
    }

    public function edit(Request $request, $id)
    {
        $data['registration'] = $this->registrationService->getRegistration(['id' => $id]);
        if (empty($data['registration']))
            return abort(404);

        $data['roles'] = $this->userService->getRoleList(['role_not' => [1, 2, 3, 4]], false);

        return view('backend.features.registration.form', compact('data'), [
            'title' => __('global.edit_attr', [
                'attribute' =>  __('feature/registration.caption')
            ]),
            'routeBack' => route('registration.index', $request->query()),
            'breadcrumbs' => [
                __('feature/registration.caption') => route('registration.index'),
                __('global.edit') => '',
            ]
        ]);
    }

    public function update(RegistrationRequest $request, $id)
    {
        $data = $request->all();
        $data['active'] = (bool)$request->active;
        $registration = $this->registrationService->update($data, ['id' => $id]);
        $data['query'] = $request->query();

        if ($registration['success'] == true) {
            return $this->redirectForm($data)->with('success', $registration['message']);
        }

        return redirect()->back()->with('failed', $registration['message']);
    }

    public function activate($id)
    {
        $registration = $this->registrationService->activate(['id' => $id]);

        if ($registration['success'] == true) {
            return back()->with('success', $registration['message']);
        }

        return redirect()->back()->with('failed', $registration['message']);
    }

    public function softDelete($id)
    {
        $registration = $this->registrationService->trash(['id' => $id]);

        return $registration;
    }

    public function permanentDelete(Request $request, $id)
    {
        $registration = $this->registrationService->delete($request, ['id' => $id]);

        return $registration;
    }

    public function restore($id)
    {
        $registration = $this->registrationService->restore(['id' => $id]);

        if ($registration['success'] == true) {
            return redirect()->back()->with('success', $registration['message']);
        }

        return redirect()->back()->with('failed', $registration['message']);
    }

    private function redirectForm($data)
    {
        $redir = redirect()->route('registration.index', $data['query']);
        if ($data['action'] == 'back') {
            $redir = back();
        }

        return $redir;
    }
}
