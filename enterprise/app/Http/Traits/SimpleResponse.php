<?php

namespace App\Http\Traits;

use Config;

trait SimpleResponse {
	/**
	 * 带有错误信息跳转
	 *
	 * @param  string $url   跳转的链接
	 * @param  array  $error 需要共享的错误信息
	 * @return redirect
	 */
	public function errorRedirect($url, $error) {
		return redirect($url, 302, [], true)
			->with("error", $error)
			->withInput();
	}

	/**
	 * 输出Json
	 *
	 * @param  array    $arr 需要输出的json数组
	 * @return response json 输出json
	 */
	public function outputJson($arr) {
		return response()->json($arr);
	}

	/**
	 * 根据code返回信息
	 *
	 * @param  [type] $code [description]
	 * @param  [type] $data [description]
	 * @param  [type] $msg  [description]
	 * @return [type]       [description]
	 */
	public function outputJsonWithCode($code, $data = null, $msg = null) {
		$return = array();
		if (!$msg) {
			$msg = Config::get("errorcode.{$code}");
		}
		if (!$msg) {
			$msg = "未知错误";
		}
		$return['code'] = $code;
		if ($data) {
			$return['data'] = $data;
		}

		$return['msg'] = $msg;
		return $this->outputJson($return);
	}

	/**
	 * 输出404页面
	 *
	 * @param  [type] $code [description]
	 * @param  [type] $msg  [description]
	 * @return [type]       [description]
	 */
	public function error404($code, $msg = null) {

		if (!$msg) {
			$msg = Config::get("errorcode.{$code}");
		}
		if (!$msg) {
			$msg = "未知错误";
		}

		//return $code . ":" . $msg;
		return view("errors.404", compact("msg", "code"));

	}
}
