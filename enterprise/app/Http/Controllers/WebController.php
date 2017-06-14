<?php

// 用户信息中心

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\CookieModel;
use App\Models\WebgameLogicModel;
use Config;
use Helper\AccountCenter as Account;
// 使用open的Helper
use Helper\IPSearch;
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Input;

class WebController extends Controller {

	public function __construct() {
		$this->middleware("vrauth:0:clientindex", ['only' => ["index"]]);
		$this->middleware("vrauth:json", ['only' => ["sendBindMsgOpenAjax", "bindMobileOpenAjax", "modifyPicAjax"]]);
	}

	// 用户中心-用户资料
	public function index_deleted(Request $request) {

		// 判断是否登录传参true
		$res = $request->userinfo;
		$uid = $res['uid'];
		$token = $res['token'];

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");
		$accountModel = new Account($appid, $appkey);
		$res = $accountModel->info($uid, $token);
		if ($res['code'] == 0) {
			$userinfo = array();
			$userinfo['uid'] = $uid;
			$userinfo['account'] = $res['data']['account'];
			$userinfo['nick'] = $res['data']['nick'];
			$userinfo['bindmobile'] = $res['data']['bindmobile'];
			$userinfo['faceUrl'] = $res['data']['faceUrl'];
			$userinfo['account'] = $res['data']['account'];
			// 得到最后一次登录时间以及ip所在地

			$record = $accountModel->getLoginRecord($uid);
			if ($record['code'] == 0) {
				// 如果code = 0表示有登录记录
				$userinfo['last_month'] = date("m/d", $record['data']['ts']); // 08/29
				$userinfo['last_time'] = date("H:i", $record['data']['ts']); // 16:16
				$userinfo['country'] = IPSearch::find($record['data']['ip']);
				// 判断是否是一个数组
				if ($userinfo['country'] && is_array($userinfo['country']) && count($userinfo['country']) > 0) {
					$countryKey = isset($userinfo['country'][11]) ? $userinfo['country'][11] : 'unknown'; // 国家
					$city = isset($userinfo['country'][9]) ? $userinfo['country'][9] : 0; // 具体城市

					if ($countryKey != 'unknown') {
						$country = Config::get("country_list.{$countryKey}"); // 得到具体的国家

						if ($countryKey == 'CN' && $city != 0) {
							// 如果是中国
							$country = Config::get("china_city_code.{$city}"); // 得到具体的城市
							$country = implode('-', $country);
						}
					}
				}
				$userinfo['country'] = isset($country) ? $country : $record['data']['ip']; //$userinfo['country'][0];
			}
			return view('web.personal_center', compact('userinfo'));
		}
	}

	// 注册账号
	public function registerAjax(Request $request) {
		$name = Input::get('name');
		$pwd = Input::get('pwd');
		$confirPwd = Input::get('confirPwd');

		// 判断用户名或密码不能为空
		if (!$name || !$pwd || !$confirPwd) {
			return response()->json(array(
				'status' => -1,
				'msg' => '用户名或密码不能为空',
			));
		}

		if ($pwd !== $confirPwd) {
			return response()->json(array(
				'status' => -2,
				'msg' => '密码和确认密码不一致',
			));
		}

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");

		$accountModel = new Account($appid, $appkey);

		$result = $accountModel->register($name, md5($pwd), "account");

		if ($result['code'] == 0) {
			// 保存cookie
			CookieModel::setLoginCookie($result['data']['uid'], $result['data']['token'], $result['data']['account'], $result['data']['nick'], $result['data']['face']);

			/*			$data['uid']   = $result['data']['uid'];
				$data['name']  = $name;
				$data['token'] = $result['data']['token'];
				$data['nick']  = $result['data']['nick'];
				$data['face']  = $result['data']['face'];
			*/

			return response()->json(array(
				'status' => 1,
				'msg' => 'ok',
			));
		} else {
			return response()->json(array(
				'status' => 0,
				'msg' => '注册失败',
			));
		}

	}

	// 登录逻辑
	public function loginAjax(Request $request) {

		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Orign: *");

		$name = Input::get('name');
		$pwd = Input::get('pwd');

		// 判断用户名或密码不能为空
		if (!$name || !$pwd) {
			return response()->json(array(
				'status' => -1,
				'msg' => '用户名或密码不能为空',
			));
		}

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");

		$accountModel = new Account($appid, $appkey);

		$result = $accountModel->login($name, md5($pwd));
		if ($result['code'] == 0) {
			// 登录成功返回  ['code'=>0, 'uid'=>xxx, 'token'=>xxxxx]

			// 保存cookie
			CookieModel::setLoginCookie($result['data']['uid'], $result['data']['token'], $result['data']['account'], $result['data']['nick'], $result['data']['face']);
			return response()->json(array(
				'status' => 1,
				'msg' => 'ok',
			));
		} else {
			return response()->json(array(
				'status' => 0,
				'msg' => '用户名或密码错误',
			));
		}

	}

	// 是否登录
	public function isLoginAjax(Request $request) {

		$webLogin = new WebgameLogicModel();

		$res = $webLogin->checkLogin();

		// 如果返回true表示是在登录状态
		if ($res) {
			return response()->json(array(
				'status' => 1,
				'msg' => 'ok',
			));
		} else {
			return response()->json(array( // 唤醒登录注册弹层
				'status' => 0,
				'msg' => '请先登录',
			));
		}
	}

	// 判断用户名是否存在
	public function isExistsAccAjax(Request $request) {

		$name = Input::get('name');

		// 判断用户名是否为空
		if (!$name) {
			return response()->json(array(
				'status' => -1,
				'msg' => '用户名不能为空',
			));
		}

		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");

		$accountModel = new Account($appid, $appkey);

		$result = $accountModel->isExists($name);

		if ($result['code'] == 0) {
			// 不存在的结果是 array('code' => 0)

			return response()->json(array(
				'status' => 1,
				'msg' => 'ok',
			));
		} else {
			return response()->json(array(
				'status' => 0,
				'msg' => '用户名已存在',
			));
		}
	}

	// 修改昵称
	public function modifyNickAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $webLogin->checkLogin(true);

		if (is_array($res) && count($res) > 0) {

			// 得到uid 和 token
			$uid = $res['uid'];
			$token = $res['token'];
			$nick = Input::get('nick'); // 得到用户的昵称

			// 判断昵称存不存在
			if (!$nick) {
				return response()->json(array(
					'status' => -1,
					'msg' => '昵称不存在',
				));
			}

			// 组成数组
			$data['f_nick'] = $nick;

			// 将昵称保存在cookie
			CookieModel::setCookie('nick', $nick);

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);
			$result = $accountModel->updateField($uid, $token, $data);
			unset($data);
			if ($result['code'] == 0) {

				$return['nick'] = $nick;
				//$json = json_encode($return,JSON_UNESCAPED_SLASHES);

				// 表示修改昵称成功
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
					'nick' => $this->jsonEncode($return),
					'nickReal' => $nick,
				));
			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => '修改失败',
				));
			}

		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}
	}

	/**
	 * decodeUnicode-json_encode中文乱码转码函数
	 */
	public function decodeUnicode($str) {
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function('$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'), $str);
	}
	/**
	 * unicodeDecode
	 */
	public function unicodeDecode($string) {
		return json_decode('"' . $string . '"');
	}
	/**
	 * decodeUnicode-json_encode中文乱码转码函数
	 */
	public function jsonEncode($string) {
		return $this->decodeUnicode(json_encode($string));
	}

	// 绑定平台账号
	public function bindAccountAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $webLogin->checkLogin(true);

		if (is_array($res) && count($res) > 0) {

			// 得到uid 和 token
			$uid = $res['uid'];
			$token = $res['token'];

			$name = Input::get('name'); // 账号
			$pwd = Input::get('pwd'); // 密码
			$confirPwd = Input::get('confirPwd'); // 确认密码

			// 判断用户名或密码不能为空
			if (!$name || !$pwd || !$confirPwd) {
				return response()->json(array(
					'status' => -1,
					'msg' => '用户名或密码不能为空',
				));
			}

			if ($pwd !== $confirPwd) {
				return response()->json(array(
					'status' => -2,
					'msg' => '密码和确认密码不一致',
				));
			}

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			$result = $accountModel->bindAccount($uid, $token, $name, md5($pwd));
			if ($result['code'] == 0) {
				// 绑定平台账号成功

				// 更新cookie里面的account内容，前端好展现
				CookieModel::setCookie('account', $name);
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));
			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => '绑定失败',
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}

	}

	//发送验证码open后台使用
	public function sendBindMsgOpenAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $request->userinfo;
		$uid = $res['uid'];
		$token = $res['token'];

		if ($uid) {
			$mobile = trim(Input::get('mobile'));
			$action = Input::get('action', '');

			// 判断用户名是否为空
			if (!$mobile || strlen($mobile) != 11) {
				return response()->json(array(
					'status' => -1,
					'msg' => '手机号格式不正确',
				));
			}

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			if ($action == 'mobileChange') {
				$result = $accountModel->sendBindMsg($uid, $token, $mobile, $action);
			} else {
				$result = $accountModel->sendBindMsg($uid, $token, $mobile);
			}
			if ($result['code'] == 0) {
				// 表示发送验证码成功，array('code' => 0,'data' = array(...))
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));

			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => '发送失败',
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}
	}
	// 绑定手机号码open平台应用
	public function bindMobileOpenAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $request->userinfo;
		$uid = $res['uid'];
		$token = $res['token'];

		if ($uid) {
			$mobile = trim(Input::get('mobile')); // 手机号
			$code = Input::get('code'); // 验证码
			$action = Input::get('action', '');

			// 判断用户名是否为空
			if (!$mobile || strlen($mobile) != 11) {
				return response()->json(array(
					'status' => -1,
					'msg' => '手机号格式不正确',
				));
			}

			// 判断验证码是否合法
			if (!$code || !is_numeric($code)) {
				return response()->json(array(
					'status' => -2,
					'msg' => '验证码格式不正确',
				));
			}
			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			if ($action != '') {
				// 不是绑定手机号码，只是过去验证对不对
				$result = $accountModel->bindMobile($uid, $token, $mobile, $code, $action);
			} else {
				$result = $accountModel->bindMobile($uid, $token, $mobile, $code);
			}

			if ($result['code'] == 0) {
				// 表示绑定手机号码成功
				// 更新cookie里面的bindmobile内容，前端好展现
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));

			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => $result, //'绑定失败',
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}
	}

	// 发送手机验证码
	public function sendBindMsgAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $webLogin->checkLogin(true);

		if (is_array($res) && count($res) > 0) {

			// 得到uid 和 token
			$uid = $res['uid'];
			$token = $res['token'];
			$mobile = trim(Input::get('mobile'));
			$action = Input::get('action', '');

			// 判断用户名是否为空
			if (strlen($mobile) != 11) {
				return response()->json(array(
					'status' => -1,
					'msg' => '手机号格式不正确',
				));
			}

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			if ($action == 'mobileChange') {
				$result = $accountModel->sendBindMsg($uid, $token, $mobile, $action);
			} else {
				$result = $accountModel->sendBindMsg($uid, $token, $mobile);
			}

			if ($result['code'] == 0) {
				// 表示发送验证码成功，array('code' => 0,'data' = array(...))
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));

			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => $result['msg'],
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}
	}

	// 绑定手机号码
	public function bindMobileAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $webLogin->checkLogin(true);

		if (is_array($res) && count($res) > 0) {

			// 得到uid 和 token
			$uid = $res['uid'];
			$token = $res['token'];

			$mobile = trim(Input::get('mobile')); // 手机号
			$code = Input::get('code'); // 验证码
			$action = Input::get('action', '');

			// 判断用户名是否为空
			if (!$mobile || strlen($mobile) != 11) {
				return response()->json(array(
					'status' => -1,
					'msg' => '手机号格式不正确',
				));
			}

			// 判断验证码是否合法
			if (!$code || !is_numeric($code)) {
				return response()->json(array(
					'status' => -2,
					'msg' => '验证码格式不正确',
				));
			}
			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			if ($action != '') {
				// 不是绑定手机号码，只是过去验证对不对
				$result = $accountModel->bindMobile($uid, $token, $mobile, $code, $action);
			} else {
				$result = $accountModel->bindMobile($uid, $token, $mobile, $code);
			}
			if ($result['code'] == 0) {
				// 表示绑定手机号码成功
				// 更新cookie里面的bindmobile内容，前端好展现
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));

			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => $result, //'绑定失败',
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}
	}

	// 解绑手机号
	public function unBindMobileAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $webLogin->checkLogin(true);

		if (is_array($res) && count($res) > 0) {

			// 得到uid 和 token
			$uid = $res['uid'];
			$token = $res['token'];

			$mobile = trim(Input::get('mobile')); // 手机号
			$verNumber = trim(Input::get('verNumber')); // 手机号

			// 判断用户名是否为空
			if (!$mobile || strlen($mobile) != 11) {
				return response()->json(array(
					'status' => -1,
					'msg' => '手机号格式不正确',
				));
			}

			// 判断验证码是否合法
			if (!$verNumber || strlen($verNumber) != 6) {
				return response()->json(array(
					'status' => -1,
					'msg' => '验证码不合法',
				));
			}

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			$result = $accountModel->unBindMobile($uid, $token, $mobile, $verNumber);

			if ($result['code'] == 0) {
				// 表示解绑手机号码成功
				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));

			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => $result, //'解绑失败',
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}
	}

	// 修改密码
	public function modifyPwdAjax(Request $request) {

		$webLogin = new WebgameLogicModel();
		// 判断是否登录传参true
		$res = $webLogin->checkLogin(true);

		if (is_array($res) && count($res) > 0) {

			// 得到uid 和 token
			$uid = $res['uid'];
			$token = $res['token'];
			$oldPwd = Input::get('oldPwd'); // 老密码
			$newPwd = Input::get('newPwd'); // 新密码
			$confirPwd = Input::get('confirPwd'); // 确认密码

			// 判断用户名或密码不能为空
			if (!$oldPwd || !$newPwd || !$confirPwd) {
				return response()->json(array(
					'status' => -1,
					'msg' => '原密码或新密码不能为空',
				));
			}

			if ($newPwd !== $confirPwd) {
				return response()->json(array(
					'status' => -2,
					'msg' => '新密码和确认密码不一致',
				));
			}

			$appid = Config::get("common.uc_appid");
			$appkey = Config::get("common.uc_appkey");

			$accountModel = new Account($appid, $appkey);

			$result = $accountModel->changePwd($uid, $token, md5($oldPwd), md5($newPwd));
			if ($result['code'] == 0) {
				// 表示密码修改成功
				// 重新保存uid 和 token
				CookieModel::setCookie("uid", $result['data']['uid']);
				CookieModel::setCookie("token", $result['data']['token']);

				return response()->json(array(
					'status' => 1,
					'msg' => 'ok',
				));
			} else {
				return response()->json(array(
					'status' => 0,
					'msg' => '原密码不对',
				));
			}
		} else {
			return response()->json(array(
				'status' => -1,
				'msg' => 'uid或token丢失',
			));
		}

	}

	//修改头像
	public function modifyPicAjax(Request $request) {
		$userInfo = $request->userinfo;
		$uid = $userInfo['uid'];
		$token = $userInfo['token'];
		$resInfo = ImageHelper::url("userimg", $uid);
		$data['f_face_ver'] = $ver = time();
		CookieModel::setFace($resInfo['face'] . "?v=" . $ver);
		$appid = Config::get("common.uc_appid");
		$appkey = Config::get("common.uc_appkey");
		$account = new Account($appid, $appkey);
		$result = $account->updateField($uid, $token, $data);
		if ($result['code'] == 0) {
			return Library::output(0);
		} else {
			return Library::output(1);
		}
	}

}