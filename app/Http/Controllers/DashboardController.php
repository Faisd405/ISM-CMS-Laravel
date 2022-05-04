<?php

namespace App\Http\Controllers;

use App\Services\Feature\ConfigurationService;
use App\Services\Module\ContentService;
use App\Services\Module\InquiryService;
use App\Services\Module\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Spatie\Analytics\Period;
use Analytics;
use Carbon\Carbon;

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

        $data['counter'] = [
            'page' => App::make(PageService::class)->getPageList([
                'publish' => 1,
                'approved' => 1
            ], false)->count(),
            'post' => App::make(ContentService::class)->getPostList([
                'publish' => 1,
                'approved' => 1
            ], false)->count(),
        ];

        $data['list'] = [
            'posts' => App::make(ContentService::class)->getPostList([
                'publish' => 1,
                'approved' => 1,
                'is_detail' => 1
            ], true, 5),
            'inquiries' => App::make(InquiryService::class)->getFormList([], true, 5),
        ];

        return view('backend.dashboard.index', compact('data'), [
            'title' => __('module/dashboard.caption')
        ]);
    }
}
