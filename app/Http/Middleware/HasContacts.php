<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HasContacts
{

    const endpoint_on_failure = "upload_csv";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $contactListCount = $user->contacts->count();
        if( $contactListCount == 0 ){
            $routeName = self::endpoint_on_failure;
            $redirectUrl = redirect()->route($routeName);
            return $redirectUrl;
        }
        return $next($request);
    }
}
