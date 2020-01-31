<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Symfony\Component\HttpFoundation\Response;
class AdminAuth
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
      if($request->user()->user_role=='a'){
        return $next($request);
      }
      else{
        return response(array(
          'success'=>0,
          'msg'=>'You have no permissions'
        ));
      }

    }
}
