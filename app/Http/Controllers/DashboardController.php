<?php

namespace App\Http\Controllers;

use App\Services\Feature\ConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $configService;

    public function __construct(
        ConfigurationService $configService
    )
    {
        $this->configService = $configService;
    }

    public function index(Request $request)
    {
        $roleBackend = config('cms.module.auth.login.backend.role');
        if (!Auth::user()->hasRole($roleBackend))
           return redirect()->route('home');

        $data['maintenance'] = $this->configService->getConfigValue('maintenance');

        return view('backend.dashboard.index', compact('data'), [
            'title' => __('module/dashboard.caption')
        ]);
    }
}
