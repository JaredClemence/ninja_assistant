<?php

namespace App\Http\Middleware;

use Closure;
use App\Clemence\Contact\IntermediateRecord;
use Illuminate\Support\Facades\Auth;

class RedirectForIntermediaries
{
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
        $records = \App\Http\Controllers\ContactCsvFileController::getIntermediariesNeedingApproval($user);
        $count = $records->count();
        if( $count > 0 ){
            return redirect('/contacts/verify');
        }else{
            return $next($request);
        }
    }
}
