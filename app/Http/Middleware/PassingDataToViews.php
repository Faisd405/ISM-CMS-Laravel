<?php

namespace App\Http\Middleware;

use App\Services\Feature\ConfigurationService;
use App\Services\Feature\LanguageService;
use App\Services\Feature\NotificationService;
use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class PassingDataToViews
{
    protected $config, $languange, $menu;

    public function __construct(
        ConfigurationService $config,
        LanguageService $languange,
        MenuService $menu
    )
    {
        $this->config = $config;
        $this->languange = $languange;
        $this->menu = $menu;
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
        $passingData['totalQueryParam'] = count($request->query());

        //--- Configuration
        foreach ($this->config->getConfigList(['active' => 1]) as $item) {
            if ($item['is_upload'] == 1) {
                $passingData['config'][$item['name']] = $item->file($item['name']);
            } else {
                $passingData['config'][$item['name']] = $item->value($item['name']);
            }
        }

        //--- Language
        if (config('cms.module.feature.language.multiple') == true && request()->segment(1) != 'admin') {
            $passingData['languages'] = $this->languange->getLanguageActive();
        }

        //--- Menu Category
        if (request()->segment(1) != 'admin') {
            $filter['parent'] = 0;
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            if (Auth::guard()->check() == false)
                $filter['public'] = 1;

            foreach ($this->menu->getCategoryList(['active' => 1], false) as $key => $value) {
                $filter['category_id'] = $value['id'];
                $passingData['menu'][$value['name']] = $this->menu->getMenuList($filter, false);
            }
        }

        View::share($passingData);

        return $next($request);
    }
}
