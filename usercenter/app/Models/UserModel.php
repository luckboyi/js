<?php

/*
用户Model
date:2016/8/22
 */

namespace App\Models;

use DB;
use Helper\Library;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model {

	//不设置属性，默认连接mysql配置
	protected $connection = 'db_user_0';
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	//protected $table = 'article';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//protected $fillable = ['title', 'intro', 'content'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = ['password', 'remember_token'];

	/**
	 * 插入db_user.t_uid
	 * @param   string  username
	 * @return  int  primary key id
	 */
	public function tUidInsert($username) {
		try {
			$id = DB::connection("db_user_0")->table('t_uid')->insertGetId(
				array('f_account' => $username)
			);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $id;
	}

	/**
	 * 插入 db_user.t_user_info_x
	 * @param   array  data
	 * @return  int >0 表示所影响的行数，=0 表示新增失败
	 */
	public function tUserinfo($data) {
		return $this->insBaseinfo($data['f_uid'], $data);
	}

	/**
	 * 读用户基本信息
	 * @param   int     uid
	 * @return  array
	 */
	public function baseInfo($uid) {
		if (!$uid) {
			return false;
		}
		$userRes = $this->getDB($uid);
		try {
			$info = DB::connection($userRes['db'])->table($userRes['table_info'])->where("f_uid", $uid)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}

		return $info;
	}

	/**
	 * 根据uid，修改app信息
	 * @param   array  data
	 * @return  bool true
	 */
	public function updateBaseinfo($uid, $data) {
		if (!$uid || !$data || !is_array($data)) {
			return false;
		}
		$userRes = $this->getDB($uid);
		try {
			if (isset($data['f_pwd'])) {
				$userInfo = DB::connection($userRes['db'])->table($userRes['table_info'])->where("f_uid", $uid)->get();
				if ($userInfo[0]['f_pwd'] !== $data['f_pwd']) {
					$ret = DB::connection($userRes['db'])->table($userRes['table_info'])->where("f_uid", $uid)->update($data);
				} else {
					//两次密码相同
					return 2;
				}
			} else {
				$ret = DB::connection($userRes['db'])->table($userRes['table_info'])->where("f_uid", $uid)->update($data);
			}

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}

		return $ret;
	}

	/**
	 * 插入用户基本信息
	 * @param   array  data
	 * @return  bool true
	 */
	public function insBaseinfo($uid, $data) {
		if (!$uid || !$data || !is_array($data)) {
			return false;
		}
		$userRes = $this->getDB($uid);
		try {
			$ret = DB::connection($userRes['db'])->table($userRes['table_info'])->insert($data);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}

		return $ret;
	}

	/**
	 * 读用户扩展信息
	 * @param   int     uid
	 * @return  array
	 */
	public function extInfo($uid) {
		if (!$uid) {
			return false;
		}
		$userRes = $this->getDB($uid);

		try {
			$info = DB::connection($userRes['db'])->table($userRes['table_ext'])->where("f_uid", $uid)->first();
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}

		return $info;
	}

	/**
	 * 插入用户扩展信息
	 * @param   array  data
	 * @return  bool true
	 */
	public function insExtinfo($uid, $data) {
		if (!$uid || !$data || !is_array($data)) {
			return false;
		}
		$userRes = $this->getDB($uid);
		try {
			$ret = DB::connection($userRes['db'])->table($userRes['table_ext'])->insert($data);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}

		return $ret;
	}

	/**
	 * 扣平台币
	 * @param   int     uid
	 * @return  array
	 */
	public function subPlatb($uid, $num) {
		$num2 = intval($num);
		if (!$uid || $num2 <= 0 || $num2 != $num) {
			return false;
		}
		$userRes = $this->getDB($uid);

		try {
			$ret = DB::connection($userRes['db'])->table($userRes['table_ext'])->where("f_uid", $uid)->decrement("f_money", $num2);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if (!$ret) {
			return false;
		}
		return $ret;
	}

	/**
	 * 修改扩展信息，增加平台币及总充值数据
	 * @param   int     uid
	 * @param   int     addnum     增加平台币数量，包含获得赠送
	 * @param   int     paynum     增加平台币数量，不包含获得赠送，纯统计部分，可用来计算vip
	 * @return  array
	 */
	public function addPlatb($uid, $addnum, $paynum) {
		$addnum2 = intval($addnum);
		$paynum2 = intval($paynum);
		if (!$uid || $addnum2 <= 0 || $addnum2 != $addnum) {
			return false;
		}
		$userRes = $this->getDB($uid);

		try {
			$info = DB::connection($userRes['db'])->table($userRes['table_ext'])->where("f_uid", $uid)->first();
			if (!$info) {
				$data = array("f_uid" => $uid, "f_money" => $addnum, "f_consume" => $paynum, "f_lastip" => Library::realIp());
				$ret = DB::connection($userRes['db'])->table($userRes['table_ext'])->insert($data);
			} else {
				$ret = DB::connection($userRes['db'])->table($userRes['table_ext'])->where("f_uid", $uid)->update(array(
					'f_money' => DB::raw("f_money + {$addnum2}"),
					'f_consume' => DB::raw("f_consume + {$paynum2}"),
				));
			}

		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if (!$ret) {
			return false;
		}

		return $ret;
	}

	/**
	 * 用户登录，根据uid在db_uesr_0库中查询出在哪个t_user_info_0表
	 * @param   array  data
	 * @return  int primary key uid ，如果返回false 表示登录失败
	 */
	public function login($data) {
		if (!$data || !is_array($data) || !isset($data['f_uid']) || !$data['f_uid']) {
			return false;
		}

		// 根据f_uid，求出db_user_0库下面的t_user_info_x表

		$userRes = $this->getDB($data['f_uid']);

		try {
			$uid = DB::connection($userRes['db'])->table($userRes['table_info'])->where($data)->value("f_uid");
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}

		// $uid 大于0，表示查询成功，记录登录次数
		if ($uid) {
			// 判断当前日期是否等于数据库更新日期，如果等于则登录次数+1，如果不等于，则登录次数重置为1

			return $uid;
		} else {
			// 用户名或者密码不对
			return false;
		}
	}

	/**
	 * 修改密码
	 */
	public function changePwd($data, $newPwd) {
		// 根据f_uid，求出db_user_0库下面的t_user_info_x表
		//$userRes = $this->getDB($data['f_uid']);

		try {
			$affected = DB::connection('db_user_0')->table('t_user_info_0')->where($data)->update(array('f_pwd' => $newPwd));
			// 如果 affected > 0 表示更改密码成功 否则 表示密码错误
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $affected;
	}

	/**
	 * 生成游客自动注册的账号后缀
	 */
	public function getGuestId() {
		try {
			$id = DB::connection("db_user_0")->table('t_guest')->insertGetId(array('f_time' => time()));
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		return $id;
	}

	public function setFace($data) {
		$dbArr = self::getDB($data['uid']);
		$where = array(
			'f_uid' => $data['uid'],
		);
		$update = array(
			'f_face_ver' => $data['setFact'],
		);
		try {
			$ret = DB::connection($dbArr['db'])->table($dbArr['table_info'])->where($where)->update($update);
		} catch (\Exception $e) {
			UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
			return false;
		}
		if ($ret) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取用户库的库、表名后缀
	 */
	protected function getDB($uid) {
		$uid = intval($uid);
		if ($uid <= 0) {
			return false;
		}
		$db_suff = 0;
		$tbl_suff = floor($uid / 10000000);
		return array('db' => "db_user_" . $db_suff, 'table_info' => "t_user_info_" . $tbl_suff, 'table_ext' => "t_user_ext_" . $tbl_suff);
	}

}
