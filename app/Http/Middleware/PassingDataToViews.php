<?php

namespace App\Http\Middleware;

use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Feature\NotificationService;
use App\Services\MenuService;
use App\Services\Module\WidgetService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class PassingDataToViews
{
    protected $config, $languange, $menu, $widget;

    public function __construct(
        ConfigurationService $config,
        LanguageService $languange,
        MenuService $menu,
        WidgetService $widget
    )
    {
        $this->config = $config;
        $this->languange = $languange;
        $this->menu = $menu;
        $this->widget = $widget;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $passingData = [];
        $passingData['queryParam'] = $request->query();
        $passingData['totalQueryParam'] = count($passingData['queryParam']);

        //--- Configuration
        foreach ($this->config->getConfigList(['active' => 1]) as $item) {
            if ($item['is_upload'] == 1) {
                $passingData['config'][$item['name']] = $item->file($item['name']);
            } else {
                $passingData['config'][$item['name']] = $item->value($item['name']);
            }
        }
        
        if (request()->segment(1) != 'backend' || request()->segment(1) != 'admin') {

            //--- Language
            if (config('cms.module.feature.language.multiple') == true) {
                $passingData['languages'] = $this->languange->getLanguageActive();
            }
            
            //--- Menu Category
            $filterMenu['parent'] = 0;
            $filterMenu['publish'] = 1;
            $filterMenu['approved'] = 1;
            if (Auth::guard()->check() == false)
                $filterMenu['public'] = 1;

            foreach ($this->menu->getCategoryList(['active' => 1], false) as $key => $value) {
                $filterMenu['category_id'] = $value['id'];
                $passingData['menu'][$value['name']] = $this->menu->getMenuList($filterMenu, false, 10, false, [], [
                    'position' => 'ASC'
                ]);
                foreach ($passingData['menu'][$value['name']] as $keyB => $valueB) {
                    $passingData['menu'][$value['name']][$keyB]['modules'] = $this->menu->getModuleData($valueB);
                }
            }

            //--- Widget Global
            $filterWidget['global'] = 1;
            $filterWidget['publish'] = 1;
            $filterWidget['approved'] = 1;
            $widgets = $this->widget->getWidgetList($filterWidget, false, 10, false, [], [
                'position' => 'ASC'
            ]);
            foreach ($widgets as $key => $value) {
                $passingData['widget_globals'][$value['name']] = $value;
                $passingData['widget_globals'][$value['name']]['module'] = $this->widget->getModuleData($value);
            }
        }


        View::share($passingData);

        return $next($request);
    }
}
