<?php

namespace App\Providers;

use App\Models\Feature\Language;
use App\Repositories\Feature\ConfigurationRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Index Lengths & MySQL / MariaDB
        Schema::defaultStringLength(191);

        // Bootstrap pagination default
        Paginator::useBootstrap();

        if (config('cms.setting.index_url') == true && !App::runningInConsole()) {
            // set config cache
            App::make(ConfigurationRepository::class)->setConfigCache();

            // set lang cache
            $language = Language::where('iso_codes', config('cmsConfig.dev.default_lang'))->active()->first();
            if (!empty($language)) {
                $config = app('config');
                $config->set('app.timezone', $language['time_zone']);
                $config->set('app.locale', $language['iso_codes']);
                $config->set('app.fallback_locale', $language['fallback_locale']);
                $config->set('app.faker_locale', $language['faker_locale']);
            }
        }

        // Format mata uang
        //-- rupiah
        Blade::directive('rupiah', function ($expression) {
            return "Rp. <?php echo number_format($expression, 0, ',', '.'); ?>";
        });

        //-- dollar
        Blade::directive('dollar', function ($expression) {
            return "$<?php echo number_format($expression, 2); ?>";
        });
    }
}
