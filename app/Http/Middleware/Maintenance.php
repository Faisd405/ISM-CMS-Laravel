<?php

namespace App\Http\Middleware;

use App\Repositories\Feature\ConfigurationRepository;
use Closure;
use Illuminate\Http\Request;

class Maintenance
{
    private $config;

    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
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
        $segment = request()->segment(1);
        $urlUnBlock = config('cms.module.feature.maintenance.url_unblock');

        if ($this->config->getConfigValue('maintenance') == 1) {
            if (!in_array($segment, $urlUnBlock)) {
                return redirect()->route('maintenance');
            }
        }

        return $next($request);
    }
}
