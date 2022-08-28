<?php

namespace App\Http\Controllers;

use App\Services\Module\ContentService;
use App\Services\Module\InquiryService;
use App\Services\Module\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Spatie\Analytics\Period;
use Analytics;
use App\Services\Feature\ConfigurationService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;

class DashboardController extends Controller
{
    use ApiResponser; 

    public function index(Request $request)
    {
        $roleBackend = config('cms.module.auth.login.backend.role');
        if (!Auth::user()->hasRole($roleBackend))
           return redirect()->route('home');

        $data['counter'] = [
            'page' => App::make(PageService::class)->getPageList([
                'publish' => 1,
                'approved' => 1,
            ], false, 0)->count(),
            'post' => App::make(ContentService::class)->getPostList([
                'publish' => 1,
                'approved' => 1
            ], false, 0)->count(),
        ];

        $data['list'] = [
            'posts' => App::make(ContentService::class)->getPostList([
                'publish' => 1,
                'approved' => 1,
                'detail' => 1
            ], true, 5, false, [], [
                'created_at' => 'DESC'
            ]),
            'inquiries' => App::make(InquiryService::class)->getFormList([], true, 5, false, [], [
                'submit_time' => 'DESC'
            ]),
        ];
        
        $data['maintenance'] = App::make(ConfigurationService::class)->getConfigValue('maintenance');

        return view('backend.dashboard.index', compact('data'), [
            'title' => __('module/dashboard.caption')
        ]);
    }

    public function analytics(Request $request)
    {
        try {
            
            $periode = Period::days(7);

            $visitors = [];
            foreach (Analytics::fetchTotalVisitorsAndPageViews($periode) as $key => $value) {
                $visitors[$key] = [
                    'date' => Carbon::parse($value['date'])->format('d F'),
                    'visitor' => $value['visitors']
                ];
            }

            return $this->success($visitors, 'load analytics successfully');
            
        } catch (Exception $e) {
            
            return $this->error(null, 'load analytics failed');
        }
    }
}
