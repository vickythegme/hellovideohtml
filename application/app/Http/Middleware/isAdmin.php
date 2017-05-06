<?php namespace HelloVideo\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use \Auth as Auth;

class isAdmin {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(Auth::user()->role != 'admin'){
			die('Sorry ' . Auth::user()->role . ' users do not have access to this area');
		}
		
		return $next($request);
	}

}
