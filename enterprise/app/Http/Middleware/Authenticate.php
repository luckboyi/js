<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use \App\Models\UserModel as User;

class Authenticate {
	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth) {
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

		if (!empty(User::getSession('admin_uid'))) {   // admin 运营后台和 open后台session只能2选1	
			$id = User::getSession('admin_uid');
		}elseif (!empty(User::getSession('open_uid'))) {
			$id = User::getSession('open_uid');
		}


		$perm = User::getSession('perm');
		$name = User::getSession('name');


		if (isset($id) && isset($name) && isset($perm)) {

			view()->share(["perm" => $perm, "admin_username" => $name]);

			return $next($request);

		} else {

			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			}

			return redirect()->guest('user/login');
		}

		//默认权限认证方法
		// if ($this->auth->guest()) {
		//     if ($request->ajax()) {
		//         return response('Unauthorized.', 401);
		//     } else {
		//         return redirect()->guest('user/login');
		//     }
		// }

	}
}
