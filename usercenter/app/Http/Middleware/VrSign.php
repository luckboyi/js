<?php

namespace App\Http\Middleware;
use Config;
use Closure;
use Helper\Library;
use \App\Models\AppinfoModel;

class VrSign {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  params 使用:分割参数 第一位:处理方式，jump/json 第二位:权限类型
	 * @return mixed
	 */
	public function handle($request, Closure $next, $params = null) {
		$appid = $request->input('appid');
		$json = $request->input('json');
		$time = $request->input('time');
		$sign = $request->input('sign');

		$diff = abs(time() - $time);
		// if ($diff > 20) {
		// 	return Library::output(1);
		// }

		if (!$appid || !$json || !$time || !$sign) {
			return Library::output(1);
		}

        $clientkey = Config::get("common.vr_client_key");

		$reqSign = md5($appid . $json . $time . $clientkey);
		if ($reqSign != $sign) {
			return Library::output(1);
		}
		return $next($request);
	}

}
