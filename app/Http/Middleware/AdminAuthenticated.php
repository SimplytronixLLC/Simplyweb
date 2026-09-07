<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AdminAuthenticated{

    public function handle($request, Closure $next){

        if( Auth::check() ){
            
           if ( Auth::user()->isAdmin() ) {
                     return $next($request);
            } else {
                 return redirect(url('/'));
            }

        }

        abort(404);  // for other user throw 404 error
    }
}