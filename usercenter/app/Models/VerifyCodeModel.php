<?php

namespace App\Models;

use App\Helper\Vmemcached;
use Helper\Library;
use Illuminate\Database\Eloquent\Model;

class VerifyCodeModel extends Model {
	/**
	 * 验证码前缀
	 */
	private $verifyCodePrefix = "code_";

	/**
	 * 当前验证码验证的次数
	 * 针对当前操作的uid+number控制次数
	 */
	private $verifyCountPrefix = "count_";

	/**
	 * 用户收的所有的验证码的次数
	 */
	private $userCountPrefix = "user_count_";

	/**
	 * 手机收的所有的验证码的次数
	 */
	private $numberCountPrefix = "number_count_";

	/**
	 * 手机验证码过期时间
	 * 单位 秒
	 */
	private $mobileCodeExpire = 5 * 60;

	/**
	 * 邮箱验证码过期时间
	 * 单位 秒
	 */
	private $emailCodeExpire = 3 * 60 * 60;

	/**
	 * 每个用户限制发送次数
	 * 单位 次
	 */
	private $userMaxCount = 10;

	/**
	 * 限制每个号码发送的次数
	 * 单位 次
	 */
	private $numberMaxCount = 10;

	/**
	 * 图形验证码根据IP验证的最大次数
	 */
	private $imgCodeMaxCountByIp = 5;

	/**
	 * 图形验证码根据账号验证的最大次数
	 */
	private $imgCodeMaxCountByAccount = 5;

	/**
	 * 获取验证码
	 * @param   int     uid
	 * @param   string  number    手机号码/邮箱账号
	 * @param   string  action    类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;reg:注册;
	 * @return  string  code
	 */
	public function getCode($uid, $number, $type, $action) {
		if (!$uid || !$number || !$action) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		return Vmemcached::get("verfy_code", $ret['codekey']);
	}

	/**
	 * 获取验证码发送次数
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  string  code
	 */
	public function getCount($uid, $number, $type, $action) {
		if (!$uid || !$action || !$type || !$number) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		return Vmemcached::get("verfy_code", $ret['countkey']);
	}

	/**
	 * 获取该用户验证码发送次数
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  string  code
	 */
	public function getUserCount($uid, $number, $type, $action) {
		if (!$uid || !$action || !$type || !$number) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		return Vmemcached::get("verfy_code", $ret['usercountkey']);
	}

	/**
	 * 获取该手机验证码发送次数
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  string  code
	 */
	public function getNumberCount($uid, $number, $type, $action) {
		if (!$uid || !$action || !$type || !$number) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		return Vmemcached::get("verfy_code", $ret['numcountkey']);
	}

	/**
	 * 生成验证码
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  string  code
	 */
	public function setCode($uid, $number, $type, $action) {
		if (!$uid || !$number || !$type || !$action) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		$code = Library::genCode("num", 6);
		if (!$code) {
			return false;
		}

		/**
		 * 设置验证码
		 */
		$ret1 = Vmemcached::set("verfy_code", $ret['codekey'], $code, $ret['expire']);

		/**
		 * 设置验证码的验证次数
		 * 当天过期
		 */
		$ret2 = Vmemcached::set("verfy_code", $ret['countkey'], 5, $ret['expire']);
		return $code;
	}

	/**
	 * 删除验证码
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  bool
	 */
	public function delCode($uid, $number, $type, $action) {
		if (!$uid || !$number || !$type || !$action) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		Vmemcached::delete("verfy_code", $ret['codekey']);
		Vmemcached::delete("verfy_code", $ret['countkey']);
		return true;
	}

	/**
	 * 验证码验证次数-1
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  bool
	 */
	public function decCount($uid, $number, $type, $action) {
		if (!$uid || !$number || !$type || !$action) {
			return false;
		}
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		return Vmemcached::decrement("verfy_code", $ret['countkey'], 1);
	}

	/**
	 * 设置验证码验证次数
	 * 包括用户的和手机的
	 * 如果没有就添加，如果有了就+1
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  array
	 */
	public function addTotalCount($uid, $number, $type, $action) {
		if (!$uid || !$number || !$type || !$action) {
			return false;
		}
		$result = array();
		$ret = $this->getKeyPrefix($uid, $number, $type, $action);
		$r = Vmemcached::add("verfy_code", $ret['usercountkey'], 1, 24 * 60);
		if (!$r) {
			$u = Vmemcached::increment("verfy_code", $ret['usercountkey'], 1);
		} else {
			$u = 1;
		}
		$r = Vmemcached::add("verfy_code", $ret['numcountkey'], 1, 24 * 60);
		if (!$r) {
			$n = Vmemcached::increment("verfy_code", $ret['numcountkey'], 1);
		} else {
			$n = 1;
		}
		return array("user" => $u, "number" => $n);
	}

	/**
	 * 获取验证码
	 * 校验验证码的次数
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  string  code
	 */
	public function getVerifyCode($uid, $number, $type, $action) {
		if (!$uid || !$number || !$action) {
			return false;
		}
		$code = $this->getCode($uid, $number, $type, $action);
		if (!$code) {
			return $code;
		}
		$count = $this->getCount($uid, $number, $type, $action);
		if ($count <= 0) {
			return "error:notimes";
		}
		$this->decCount($uid, $number, $type, $action);
		return $code;
	}

	/**
	 * 保存验证码
	 * @param   int     uid
	 * @param   string  number      手机号码/邮箱账号
	 * @param   string  type        验证码类型，mobile/email
	 * @param   string  action      类型， bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 * @return  string  code
	 */
	public function setVerifyCode($uid, $number, $type, $action) {
		if (!$uid || !$number || !$type || !$action) {
			return false;
		}

		$code = Library::genCode("num", 6);

		$counts = $this->addTotalCount($uid, $number, $type, $action);
		if (!$counts) {
			$counts = array();
		}
		$userCount = isset($counts['user']) ? $counts['user'] : 0;
		$numCount = isset($counts['number']) ? $counts['number'] : 0;

		if ($userCount > $this->userMaxCount) {
			return "error:expireusermax"; // 用户发送超过最大限制
		}

		if ($numCount > $this->numberMaxCount) {
			return "error:expirenummax"; // 手机发送超过最大限制
		}

		return $this->setCode($uid, $number, $type, $action);

	}

	/**
	 * 返回key的前缀
	 * @param   int     uid
	 * @param   string  number  发送的号码，手机号码、邮箱等等
	 * @param   string  type    类型，mobile/email
	 * @param   string  action，操作，bind_mobile: 绑定手机; bind_email: 绑定邮箱; find_pwd_mobile: 手机找回密码; find_pwd_email: 邮箱找回密码;
	 */
	private function getKeyPrefix($uid, $number, $type, $action) {
		if (!$number || !$type) {
			return false;
		}

		$today = date("Ymd");

		/**
		 * 验证码的key
		 */
		$verifyCodeKey = $this->verifyCodePrefix . $action . "_" . $uid . "_" . $number;

		/**
		 * 验证码验证次数的key
		 * 不分今天，总的验证次数
		 */
		$verifyCountKey = $this->verifyCountPrefix . $action . "_" . $uid;

		/**
		 * 验证次数的key
		 */
		$userVerifyCountKey = $this->userCountPrefix . $today . "_" . $action . "_" . $uid;

		/**
		 * 验证次数的key
		 */
		$numVerifyCountKey = $this->numberCountPrefix . $today . "_" . $action . "_" . $number;

		switch ($type) {
		case "mobile":
			$expire = $this->mobileCodeExpire;
			break;
		case "email":
			$expire = $this->emailCodeExpire;
			break;
		default:
			$expire = -1;
			break;
		}
		return array("codekey" => $verifyCodeKey, "countkey" => $verifyCountKey, "expire" => $expire, "usercountkey" => $userVerifyCountKey, "numcountkey" => $numVerifyCountKey);
	}

	/*
		      +-----------------------------------------------------------------------------+
		      |                                                                             |
		      |             图 形 验 证 码                                                  |
		      |                                                                             |
		      +-----------------------------------------------------------------------------+
	*/
	public function createImgCode($device, $action) {
		if (!$device || !$action) {
			return false;
		}
		$info = ['device' => $device, 'action' => $action];

		$set = $this->setImgCode($device, $info);
		if ($set) {
			return $info;
		} else {
			return false;
		}
	}

	/**
	 * 获取图形验证码
	 * @param   string  device  设备信息，设备号，或sessionid
	 * @return  string  code
	 */
	public function getImgCode($device) {
		if (!$device) {
			return false;
		}
		$suff = "img_{$device}";
		$json = Vmemcached::get("verfy_code", $suff);
		if ($json) {
			return json_decode($json, true);
		}
		return false;
	}

	/**
	 * 设置图形验证码
	 * @param   string  device  设备信息，设备号，或sessionid
	 * @param   string  code    验证码
	 * @return  bool
	 */
	public function setImgCode($device, $info) {
		if (!$device || !is_array($info) || empty($info)) {
			return false;
		}
		$suff = "img_{$device}";
		$expire = 600;
		return Vmemcached::set("verfy_code", $suff, json_encode($info), $expire);
	}

	/**
	 * 删除图形验证码
	 * @param   string  device  设备信息，设备号，或sessionid
	 * @param   string  code    验证码
	 * @return  bool
	 */
	public function delImgCode($device) {
		if (!$device) {
			return false;
		}
		$suff = "img_{$device}";
		return Vmemcached::delete("verfy_code", $suff);
	}

	/**
	 * 获取重试次数，达到一定数量后，要有验证码
	 * @param   string  action  register 注册/login 登录
	 * @param   string  flag    IP或者账号
	 * @return  string  code
	 */
	public function getRetryCount($action, $flag) {
		if (!$action || !$flag) {
			return false;
		}

		$suff = "img_retry_count_{$action}_{$flag}";

		$res = Vmemcached::get("verfy_code", $suff);

		return $res;
	}

	public function setRetryCount($tp, $flag) {
		if (!$flag) {
			return false;
		}
		$expire = 86400;
		$suff = "img_retry_count_login_{$flag}";
		if ($tp == "ip") {
			$set = Vmemcached::set("verfy_code", $suff, $this->imgCodeMaxCountByIp, $expire);
		} else {
			$set = Vmemcached::set("verfy_code", $suff, $this->imgCodeMaxCountByAccount, $expire);
		}

		return $set;
	}
	/**
	 * 增加次数
	 * @param   string  action  register 注册/login 登录
	 * @param   string  flag    IP或者账号
	 * @return  string  code
	 */
	public function addRetryCount($action, $flag) {
		if (!$action || !$flag) {
			return false;
		}
		$suff = "img_retry_count_{$action}_{$flag}";
		$expire = 86400;
		$inc = Vmemcached::increment("verfy_code", $suff);
		if (!$inc) {
			$inc = Vmemcached::add("verfy_code", $suff, 1, $expire);
		}

		return $inc;
	}

	/**
	 * 获取重试次数，达到一定数量后，要有验证码
	 * @param   string  action  register 注册/login 登录
	 * @param   string  flag    IP或者账号
	 * @return  string  code
	 */
	public function delRetryCount($action, $flag) {
		if (!$action || !$flag) {
			return false;
		}
		$suff = "img_retry_count_{$action}_{$flag}";
		return Vmemcached::delete("verfy_code", $suff);
	}

	/**
	 * 判断是否需要验证码
	 * @param   string  action  register 注册/login 登录
	 * @param   string  flag    IP或者账号
	 * @param   string  type    类型 IP或者account
	 * @param   int     counter    当前数量
	 * @return  string  code
	 */
	public function isNeedImgCode($action, $flag, $type, $counter = 0) {
		if (!$action || !$flag) {
			return false;
		}
		if ($type == "ip") {
			$max = $this->imgCodeMaxCountByIp;
		} else {
			$max = $this->imgCodeMaxCountByAccount;
		}
		if (!$counter) {
			$counter = $this->getRetryCount($action, $flag);
		}
		if ($counter >= $max) {
			return true;
		} else {
			return false;
		}
	}

	public function showImg($_width = 75, $_height = 25, $_nmsg, $_flag = false) {

		if (!$_nmsg || strlen($_nmsg) < 4) {
			return false;
		}

		$_rnd_code = strlen($_nmsg);

		//创建一张图像
		$_img = imagecreatetruecolor($_width, $_height);

		//白色
		$_white = imagecolorallocate($_img, 255, 255, 255);

		//填充
		imagefill($_img, 0, 0, $_white);

		if ($_flag) {
			//黑色,边框
			$_black = imagecolorallocate($_img, 0, 0, 0);
			imagerectangle($_img, 0, 0, $_width - 1, $_height - 1, $_black);
		}

		//随即画出6个线条
		for ($i = 0; $i < 6; $i++) {
			$_rnd_color = imagecolorallocate($_img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imageline($_img, mt_rand(0, $_width), mt_rand(0, $_height), mt_rand(0, $_width), mt_rand(0, $_height), $_rnd_color);
		}

		//随即雪花
		for ($i = 0; $i < 100; $i++) {
			$_rnd_color = imagecolorallocate($_img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagestring($_img, 1, mt_rand(1, $_width), mt_rand(1, $_height), '*', $_rnd_color);
		}

		//输出验证码
		for ($i = 0; $i < strlen($_nmsg); $i++) {
			$_rnd_color = imagecolorallocate($_img, mt_rand(0, 100), mt_rand(0, 150), mt_rand(0, 200));
			imagestring($_img, 5, $i * $_width / $_rnd_code + mt_rand(1, 10), mt_rand(1, $_height / 2), $_nmsg[$i], $_rnd_color);
		}

		//输出图像
		header('Content-Type: image/png');
		imagepng($_img);

		//销毁
		imagedestroy($_img);
	}
}