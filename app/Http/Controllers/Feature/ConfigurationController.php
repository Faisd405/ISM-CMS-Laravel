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
use Spatie\Analytics\Facades\Analytics;
use App\Http\Requests\Feature\ConfigRequest;
use Exception;
use Illuminate\Support\Str;

class ConfigurationController extends Controller
{
    private $configService, $langService;

    public function __construct(
        ConfigurationService $configService,
        LanguageService $langService
    ) {
        $this->configService = $configService;
        $this->langService = $langService;
    }

    /**
     * Website
     */
    public function configWeb(Request $request)
    {
        $data['file'] = $this->configService->getConfigList([
            'group' => 'file', 'is_upload' => 1, 'show_form' => 1
        ]);
        $data['general'] = $this->configService->getConfigList([
            'group' => 'general', 'show_form' => 1
        ]);
        $data['seo'] = $this->configService->getConfigList([
            'group' => 'seo', 'show_form' => 1
        ]);
        $data['social_media'] = $this->configService->getConfigList([
            'group' => 'socmed', 'show_form' => 1
        ]);
        $data['notification'] = $this->configService->getConfigList([
            'group' => 'notif', 'show_form' => 1
        ]);
        $data['dev_only'] = $this->configService->getConfigList([
            'group' => 'dev', 'show_form' => 1
        ]);
        $data['all_config'] = $this->configService->getConfigList([], [
            'group' => 'ASC'
        ]);
        $data['languages'] = $this->langService->getLanguageActive();

        return view('backend.features.configuration.website', compact('data'), [
            'title' => __('feature/configuration.caption') . ' - ' . __('feature/configuration.website.caption'),
            'breadcrumbs' => [
                __('feature/configuration.caption') => 'javascript:;',
                __('feature/configuration.website.caption') => ''
            ],
        ]);
    }

    public function addConfigWeb(ConfigRequest $request)
    {
        $data = $request->all();
        $data['is_upload'] = (bool)$request->is_upload;
        $data['show_form'] = (bool)$request->show_form;
        $data['active'] = (bool)$request->active;
        $data['locked'] = (bool)$request->locked;

        $config = $this->configService->storeConfig($data);

        if ($config['success'] == true) {
            return redirect()->back()->with('success', $config['message']);
        }

        return redirect()->back()->with('failed', $config['message']);
    }

    public function setConfigWeb(Request $request)
    {
        $config = $this->configService->setConfig($request);

        if ($config['success'] == true) {
            return redirect()->back()->with('success', $config['message']);
        }

        return redirect()->back()->with('failed', $config['message']);
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

    public function deleteConfigWeb($name)
    {
        $config = $this->configService->deleteConfig($name);

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
            File::put(base_path('lang/' . $lang . '/text.php'), $data);
            return back()->with('success', __('global.alert.update_success', [
                'attribute' => __('feature/configuration.text.caption')
            ]));
        }

        $data['files'] = Lang::get('text', [], $lang);
        $data['languages'] = $this->langService->getLanguageActive(config('cms.module.feature.language.multiple'));
        $data['lang'] = $this->langService->getLanguage(['iso_codes' => $lang]);

        return view('backend.features.configuration.text', compact('data'), [
            'title' => __('feature/configuration.caption') . ' - ' . __('feature/configuration.text.caption'),
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

        try {

            if (!empty(env('ANALYTICS_PROPERTY_ID'))) {

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
                // $data['aa'] = Analytics::performQuery(Period::years(1),
                // 'ga:sessions', [
                //     'metrics' => 'ga:sessions, ga:pageviews',
                //     'dimensions' => 'ga:yearMonth'
                // ]);

                //session
                $sessionLabel = [];
                $sessionTotal = [];
                foreach ($data['n_visitor'] as $key => $value) {
                    $sessionLabel[$key] = $value['newVsReturning'];
                    $sessionTotal[$key] = $value['activeUsers'];
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
                    $browserTotal[$key] = $value['screenPageViews'];
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
                    $visitorTotal[$key] = $value['activeUsers'];
                }

                $data['total_visitor'] = [
                    'label' => $visitorLabel,
                    'total' => $visitorTotal
                ];
            } else {
                $data['error'] = __('feature/configuration.visitor.warning_caption');
            }
        } catch (Exception $e) {
            //throw $th;
            $data['error'] = $e->getMessage();
        }

        return view('backend.features.configuration.visitor', compact('data'), [
            'title' => __('feature/configuration.visitor.caption'),
            'breadcrumbs' => [
                __('feature/configuration.visitor.caption') => '',
            ],
        ]);
    }
}
