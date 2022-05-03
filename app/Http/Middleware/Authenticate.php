<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $route = route('login');
        if (config('cms.module.auth.login.frontend.active') == true)
            $route = route('login.frontend');

        if (! $request->expectsJson()) {
            return $route;
        }
    }
}
