<?php

namespace App\Http\Middleware;

use App\Services\Feature\ApiService;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;

class ApiCheck
{
    use ApiResponser;

    private $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
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
        $api = $this->api->getApi(['api_key' => $request->header('API-Key')]);

        //missing api key
        if (empty($api)) {
            $this->error(null, 'API key is missing');
        }

        //missing api secret
        if ($request->header('API-Secret') != $api['api_secret']) {
            $this->error(null, 'API Secret is missing');
        }

        //api inactive
        if ($api['active'] == 0) {
            $this->error(null, 'API not found');
        }

        //whitelist IP
        $validIP = in_array($request->ip(), $api['ip_address']);
        if (!empty($api['ip_address']) && !$validIP) {
            $this->error(null, 'IP not registerd');
        }

        return $next($request);
    }
}
