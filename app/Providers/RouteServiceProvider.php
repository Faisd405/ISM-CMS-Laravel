<?php

namespace App\Providers;

use App\Models\Feature\Language;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/admin/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        if (config('cms.setting.locales') == true) {
            
            $locales = [];
            foreach (Language::active()->get() as $val) {
                if($val->iso_codes != config('cms.module.feature.language.default'))
                    $locales[$val->iso_codes] = $val->name;
            }
            
            config(['cms.module.feature.language.listLocale'=> $locales]);
            
            // set needLocale yang digunakan di semua routes
            config(['cms.module.feature.language.needLocale' => 
                    request()->segment(1)!=config('cms.module.feature.language.default') && 
                    array_key_exists(request()->segment(1), 
                        config('cms.module.feature.language.listLocale'))
            ]);
        }

        $this->configureRateLimiting();

        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web', 'maintenance', 'passing_data_to_view'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/redirected.php');
            require base_path('routes/web.php');
            require base_path('routes/module/user.php');
            require base_path('routes/module/feature.php');
            require base_path('routes/module/regional.php');
            require base_path('routes/module/master.php');
            require base_path('routes/module/url.php');
            require base_path('routes/module/menu.php');
            require base_path('routes/module/page.php');
            require base_path('routes/module/content.php');
            require base_path('routes/module/banner.php');
            require base_path('routes/module/gallery.php');
            require base_path('routes/module/document.php');
            require base_path('routes/module/link.php');
            require base_path('routes/module/inquiry.php');
            require base_path('routes/module/event.php');
            require base_path('routes/module/widget.php');
        });
    }

    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
            require base_path('routes/api/menu.php');
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
