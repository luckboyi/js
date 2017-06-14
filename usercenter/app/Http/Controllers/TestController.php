<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

// 引用Model
use Illuminate\Http\Request;
use \App\Models\PassportModel;

class TestController extends Controller {

	/**
	 *
	 */
	public function genToken(Request $request) {
		$uid = $request->input('uid');
		$pm = new PassportModel;
		$token = $pm->genToken($uid);
		return json_encode(array("code" => 0, "data" => array("token" => $token)));
	}

	public function test() {
		// $phones = $_GET['phone'];
		// $msgCode = $_GET['msgcode'];
		// $phones = "18916531365";
		// $msgCode = "1234";
		// $msg = '您的验证码是：' . $msgCode . '，欢迎注册体验。';

		// $type = 0; //接口里的发送短信的action标识
		// $port = '*'; //扩展子号标识
		// $self = 0;
		// $flownum = 0; //流水号
		// $method = 1; //请求方式，0:soap 1:post 2:get

		// $params = array(
		// 	'type' => $type,
		// 	'method' => $method,
		// 	'port' => $port,
		// 	'flownum' => $flownum,
		// 	'msg' => $msg,
		// 	'self' => $self,
		// 	'phones' => $phones,
		// );

		// $ret = \sms\SmsApi::send($params);
		// var_dump($ret);
		// $passport = new PassportModel;
		// $ret = $passport->sendMsgApi("18916531365", "1234");
		// var_dump($ret);
	}
}
