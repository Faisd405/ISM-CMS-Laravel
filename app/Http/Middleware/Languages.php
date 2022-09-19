<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class Languages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = config('app.fallback_locale');

        if (array_key_exists($request->segment(1), 
            config('cms.module.feature.language.listLocale') )) {

            $lang = $request->segment(1);
            if($request->segment(2))
                $request->route()->setParameter('slug', $request->segment(2));

        } elseif ($request->segment(1) == config('app.fallback_locale')) {

            $segments = $request->segments();
            unset($segments[0]);
            return redirect()->to(implode('/', $segments));

        } else {
            $request->route()->setParameter('slug', $request->segment(1));
        }

        App::setLocale($lang);
        if($lang != config('app.fallback_locale'))
            URL::defaults(['locale' => App::getLocale()]);

        return $next($request);
    }
}
