<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\ConfigUploadRequest;
use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Spatie\Analytics\Period;
use Analytics;
use Exception;
use Illuminate\Support\Str;

class ConfigurationController extends Controller
{
    private $configService, $langService;

    public function __construct(
        ConfigurationService $configService,
        LanguageService $langService
    )
    {
        $this->configService = $configService;
        $this->langService = $langService;
    }

    /**
     * Website
     */
    public function configWeb(Request $request)
    {
        $data['upload'] = $this->configService->getConfigList(['group' => 1, 'is_upload' => 1, 'show_form' => 1]);
        $data['general'] = $this->configService->getConfigList(['group' => 2, 'show_form' => 1]);
        $data['meta_data'] = $this->configService->getConfigList(['group' => 3, 'show_form' => 1]);
        $data['social_media'] = $this->configService->getConfigList(['group' => 4, 'show_form' => 1]);
        $data['dev_only'] = $this->configService->getConfigList(['group' => 100, 'show_form' => 1]);
        $data['languages'] = $this->langService->getLanguageActive();

        return view('backend.features.configuration.website', compact('data'), [
            'title' => __('feature/configuration.caption').' - '.__('feature/configuration.website.caption'),
            'breadcrumbs' => [
                __('feature/configuration.caption') => 'javascript:;',
                __('feature/configuration.website.caption') => ''
            ],
        ]);
    }

    public function updateConfigWeb(Request $request)
    {
        $config = $this->configService->updateConfig($request->name);

        if ($config['success'] == true) {
            return redirect()->back()->with('success', $config['message']);
        }

        return redirect()->back()->with('failed', $config['message']);
    }

    public function uploadConfigWeb(ConfigUploadRequest $request, $name)
    {
        $config = $this->configService->uploadFileConfig($request, $name);

        if ($config['success'] == true) {
            return redirect()->back()->with('success', $config['message']);
        }

        return redirect()->back()->with('failed', $config['message']);
    }

    public function deleteUploadConfigWeb($name)
    {
        $config = $this->configService->deleteFileConfig($name);

        return $config;
    }

    /**
     * Text
     */
    public function configText(Request $request, $lang)
    {
        if ($request->has('lang')) {
            $data = "<?php \n\nreturn [\n";
            foreach ($request->lang as $key => $value) {
                $val = Str::replace("'", "\'", $value);
                $data .= "\t'$key' => '$val',\n";
            }
            $data .= "];";
            File::put(base_path('resources/lang/'.$lang.'/text.php'), $data);
            return back()->with('success', __('global.alert.update_success', [
                'attribute' => __('feature/configuration.text.caption')
            ]));
        }

        $data['files'] = Lang::get('text', [], $lang);
        $data['languages'] = $this->langService->getLanguageActive(config('cms.module.feature.language.multiple'));
        $data['lang'] = $this->langService->getLanguage(['iso_codes' => $lang]);

        return view('backend.features.configuration.text', compact('data'), [
            'title' => __('feature/configuration.caption').' - '.__('feature/configuration.text.caption').' : <b class="text-primary">'.Str::upper($data['lang']['name']).'</b>',
            'breadcrumbs' => [
                __('feature/configuration.caption') => 'javascript:;',
                __('feature/configuration.text.caption') => ''
            ],
        ]);
    }

    /**
     * Filemanager
     */
    public function filemanager()
    {
        return view('backend.features.configuration.filemanager', [
            'title' => __('feature/configuration.filemanager.caption'),
            'breadcrumbs' => [
                __('feature/configuration.filemanager.caption') => '',
            ],
        ]);
    }

    /**
     * Visitor
     */
    public function visitor(Request $request)
    {
        $data = [];
        if (!empty(env('ANALYTICS_VIEW_ID'))) {

            $filter = $request->input('filter', '');

            if ($filter == 'today') {
                $start = now()->today();
                $end = now()->today();
            }

            if ($filter == 'current_week') {
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
            }

            if ($filter == 'latest_week') {
                $start = now()->subWeek()->startOfWeek();
                $end = now()->subWeek()->endOfWeek();
            }

            if ($filter == 'current_month') {
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
            }

            if ($filter == 'latest_month') {
                $start = now()->parse('-1 months')->startOfMonth();
                $end = now()->parse('-1 months')->endOfMonth();
            }

            if ($filter == 'current_year') {
                $start = now()->startOfYear();
                $end = now()->endOfYear();
            }

            if ($filter == 'current_year') {
                $start = now()->startOfYear();
                $end = now()->endOfYear();
            }

            if ($filter == 'latest_year') {
                $start = now()->parse('-1 years')->startOfYear();
                $end = now()->parse('-1 years')->endOfYear();
            }

            if ($filter != '') {
                $periode = Period::create($start, $end);
            } else {
                $periode = Period::days(7);
            }

            $data['total'] = Analytics::fetchTotalVisitorsAndPageViews($periode);
            $data['n_visitor'] = Analytics::fetchUserTypes($periode);
            $data['browser'] = Analytics::fetchTopBrowsers($periode);
            $data['refe'] = Analytics::fetchTopReferrers($periode);
            $data['top'] = Analytics::fetchMostVisitedPages($periode);
            $data['vp'] = Analytics::fetchVisitorsAndPageViews($periode);
            $data['aa'] = Analytics::performQuery(Period::years(1),
            'ga:sessions', [
                'metrics' => 'ga:sessions, ga:pageviews',
                'dimensions' => 'ga:yearMonth'
            ]);
            
            //session
            $sessionLabel = [];
            $sessionTotal = [];
            foreach ($data['n_visitor'] as $key => $value) {
                $sessionLabel[$key] = $value['type'];
                $sessionTotal[$key] = $value['sessions'];
            }

            $data['session_visitor'] = [
                'label' => $sessionLabel,
                'total' => $sessionTotal
            ];

            //browser
            $browserLabel = [];
            $browserTotal = [];
            foreach ($data['browser'] as $key => $value) {
                $browserLabel[$key] = $value['browser'];
                $browserTotal[$key] = $value['sessions'];
            }

            $data['browser_visitor'] = [
                'label' => $browserLabel,
                'total' => $browserTotal
            ];

            //visitor total
            $visitorLabel = [];
            $visitorTotal = [];
            foreach ($data['total'] as $key => $value) {
                $visitorLabel[$key] = Carbon::parse($value['date'])->format('d F Y');
                $visitorTotal[$key] = $value['visitors'];
            }

            $data['total_visitor'] = [
                'label' => $visitorLabel,
                'total' => $visitorTotal
            ];

        } else {
            $data['error'] = 'Analytics error';
        }

        return view('backend.features.configuration.visitor', compact('data'), [
            'title' => __('feature/configuration.visitor.caption'),
            'breadcrumbs' => [
                __('feature/configuration.visitor.caption') => '',
            ],
        ]);
    }
}
