<?php

/*
用户Model
date:2016/8/22
 */

namespace App\Models;

use DB;
use Helper\AccountCenter as Account;

// 使用open的Helper
use Illuminate\Database\Eloquent\Model;

// 使用Session 命名空间

use Redirect;
use Session;

class UserModel extends Model {

	//不设置属性，默认连接mysql配置
	protected $connection = 'system';
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

	// 运营系统用户表登录 system 库下面的 users
	// return id int >0 表示登录成功，false 表示用户名或密码错误
	public function login($data) {

		if (is_array($data) && count($data) == 2) {

			$where = [
				'name' => $data['name'],
				'password' => $data['password'],
			];
			$id = DB::connection('system')->table("users")->where($where)->value("id");
			if (!$id) {
				return false;
			}

			// 得到返回的主键id int
			$name = $data['name'];
			unset($data);

			// 这里向coll_user表插入一条记录，如果是第一条则是管理员，其他的都不是
			// 用id 和 0 组合主键去coll_user表中查询，如果没有，则插入一条，并且让他是管理员以及all权限
			$data['type'] = 0;
			$count = DB::connection('system')->table("coll_user")->where($data)->count(); // 查询有没有记录
			$data['id'] = $id;
			if (!$count) {
				// 如果没有记录，则他是管理员，is_admin = 1,permission = all
				$data['is_admin'] = 1;
				$data['permission'] = 'all';
				$data['name'] = $name;
				$data['from'] = 'admin';
				DB::connection('system')->table('coll_user')->insert($data);

				$return['id'] = $id;
				$return['admin'] = 1;
				$return['perm'] = "all";
			} else {
				$res = DB::connection('system')->table("coll_user")->where($data)->select('is_admin', 'permission')->first();
				if (!$res) {
					// 不存在就添加
					$data['name'] = $name;
					$data['from'] = 'admin';
					DB::connection('system')->table('coll_user')->insert($data);

					$return['id'] = $id;
					$return['admin'] = 0;
					$return['perm'] = "";
				} else {
					$return['id'] = $id;
					$return['admin'] = $res['is_admin'];
					$return['perm'] = $res['permission'];
				}
			}
			return $return;
		} else {
			return false;
		}
	}

	// open登录系统过来的
	/**
	 * 登录
	 * @param   int  id     id
	 * @param   string  account 账户名
	 * @return  int id 主键
	 */
	public function openLogin($uid, $account) {
		if (!$uid || !$account) {
			return false;
		}
		$data['id'] = $uid;
		$data['type'] = 1;
		$data['from'] = 'open';
		//$data['name'] = $account;
		$res = DB::connection('system')->table("coll_user")->where($data)->select('permission')->first();
		if (!$res) {
			$data['addip'] = $this->real_ip();
			$id = DB::connection('system')->table('coll_user')->insert($data);
			$res = array();
			$res['perm'] = [];
		} else {
			$res['perm'] = explode(",", $res['permission']);
		}

		return $res;
	}

	public function getAdmincpPerms($uid) {
		$row = DB::connection("system")->table("users")->where('id', $uid)->first();
		if (isset($row['group_id'])) {
			$row = DB::connection("system")->table("user_group")->where('id', $row['group_id'])->first();
			if (isset($row['perms'])) {
				$perms = json_decode($row['perms'], true);
				$addPerms = [];
				foreach ($perms as $key => $perm) {
					$addPermId = intval($perm / 100) * 100;
					if (!isset($addPerms[$addPermId])) {
						$addPerms[$addPermId] = 1;
					}
				}
				foreach ($addPerms as $addPermId => $tmp) {
					$perms[] = $addPermId;
				}
				sort($perms);
				return ['perms' => $perms, 'group_path' => $row['path']];
			}
		}
		return [];
	}

	// 管理员创建运营用户
	// return id int 插入成功后返回主键id
	public function createUser($data) {
		if (is_array($data) && count($data) >= 2) {

			$id = DB::connection("system")->table('users')->insertGetId($data);
			unset($data);
			return $id;
		} else {
			return false;
		}
	}

	// 运营用户修改密码
	// return affect int 所影响的行数，>0 表示修改成功,
	public function changePwd($data, $newPwd) {
		if (is_array($data) && count($data) == 2 && strlen($newPwd) == 32) {

			$affect = DB::connection("system")->table('users')->where($data)->update(array('password' => $newPwd));
			unset($data);
			return $affect;
		} else {
			return false;
		}
	}

	// 根据id,type 更新perm权限
	public function updatePerm($id, $type, $perm) {

		$data['id'] = $id;
		$data['type'] = $type;

		$result = DB::connection("system")->table('coll_user')->where($data)->update(array('permission' => $perm));
		unset($data);

		return $result;
	}

	// 在model里面根据运营后台类型admin或者open查询出所有的权限，
	// 根据数据3层展现，1是一级 2是二级 3是三级
	public function getActionMenu($type = 'admin') {

		$data['type'] = $type;
		$data['status'] = 1;

		$menus = DB::connection("system")->table('action_menu')->where($data)->get();
		unset($data);

		// 开始解析这个数组
		foreach ($menus as $menu) {
			$menuArr[$menu["pid"]][$menu["id"]] = $menu;
		}

		return $menuArr;
	}

	// 根据type类型，查询出所属的当前组的所有用户，比如admin=0
	// return id => name 一个数组
	public function getTypeUser($type = 0) {

		$data['type'] = $type;
		$result = DB::connection("system")->table('coll_user')->where($data)->orderBy('ctime', 'asc')->select('id', 'name')->get(); // 将管理员在前面显示
		unset($data);
		return $result;
	}

	// 在coll_user表中根据id和type判断是否是admin管理员
	// return int true 是管理员 false 非管理员
	public function checkAdmin($id, $type) {

		$data['id'] = $id;
		$data['type'] = $type;

		$result = DB::connection("system")->table('coll_user')->where($data)->value('is_admin'); // 返回 0 或者 1

		return $result;
	}

	// 根据id和type得到具体的权限，all 表示全部
	public function getPerm($id, $type) {

		$data['id'] = $id;
		$data['type'] = $type;

		$result = DB::connection("system")->table('coll_user')->where($data)->value('permission'); // 返回all 或者 1,2,3,4 或者 空

		return $result;
	}

	// http request
	private function httpRequest($url, $post_string, $method = "post", $port = 0, $connectTimeout = 1, $readTimeout = 2, &$errmsg = null) {
		$method = strtolower($method);
		if ($method == "get") {
			$url = $url . "?" . $post_string;
		}
		$result = "";
		if (function_exists('curl_init')) {
			$timeout = $connectTimeout + $readTimeout;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if ($port) {
				curl_setopt($ch, CURLOPT_PORT, $port);
			}
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			//curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			// 从证书中检查SSL加密算法是否存在
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			if ($method == "post") {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'API PHP5 Client (curl) ' . phpversion());
			$result = curl_exec($ch);
			echo "result ==> ";
			var_dump($result);
			echo "\n";
			if (!$result) {
				$errmsg = curl_error($ch);
			}
			curl_close($ch);
		} else {
			$result = false;
			$errmsg = "can not find function curl_init";
		}
		return $result;
	}

	/**
	 * 获得用户的真实IP地址
	 *
	 * @access  public
	 * @return  string
	 */
	public function real_ip() {
		static $realip = null;

		if ($realip !== null) {
			return $realip;
		}

		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
				foreach ($arr as $ip) {
					$ip = trim($ip);

					if ($ip != 'unknown') {
						$realip = $ip;

						break;
					}
				}
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$realip = $_SERVER['REMOTE_ADDR'];
				} else {
					$realip = '0.0.0.0';
				}
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_CLIENT_IP')) {
				$realip = getenv('HTTP_CLIENT_IP');
			} else {
				$realip = getenv('REMOTE_ADDR');
			}
		}

		preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
		$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

		return $realip;
	}

	// 判断是否登录
	// 参数1 type 是 admin 或者 open
	// 如果是open后台用户，还要判断用户状态是否是已审核通过，如果未通过，先注册
	// 参数2 jum 是 0 不跳转，1 跳转
	/**
	 * 判断用户状态
	 * 判断用户是否登录，判断用户是否注册完成并审核完成
	 * @param   string  type    来源，admin/open 两个平台判断
	 * @param   int     jump    如果未登录，是否要跳转到登录页面
	 * @param   int     toregister  如果未审核，是否要跳到注册信息页面
	 */
	public static function checkLogin($type = 'admin', $jump = 0, $toregister = 0) {

		if (!in_array($type, array("admin", "open", "www"))) {
			return false;
		}
		if ($type == 'admin') {
			// 判断是否登录
			if (Session::has('admin_uid')) {
				// 表示登录
				$uid = Session::get('admin_uid');
				$name = Session::get('name');
				$face = Session::get('face');
				$admin = Session::get('admin');
				$perm = Session::get('perm');
				return array('id' => $id, 'name' => $name, 'face' => $face, 'admin' => $admin, 'perm' => $perm);
			} else {
				// 表示未登录

				if ($jump) { // 跳转
					return Redirect::to('user/login'); // 跳到登录页面或其他
				}
				return false;
			}
		} elseif ($type == "open") {
			if (Session::has('open_uid')) {
				// 表示登录
				$uid = Session::get('open_uid');
				$token = Session::get('token');
				$account = Session::get('account');
				$nick = Session::get('nick');
				$face = Session::get('face');
				$open = Session::get('open');
				$perm = Session::get('perm');
				$stat = Session::get('stat');
				$reviewed = Session::get('reviewed');
				if (!$reviewed && $toregister) {
					return Redirect::to('open/register');
				}
				return array('uid' => $uid, 'token' => $token, 'account' => $account, 'nick' => $nick, 'face' => $face, 'open' => $open, 'perm' => $perm, "stat" => $stat);
			} else {
				// 表示未登录

				if ($jump) { // 跳转
					return Redirect::to('open/login'); // 跳到登录页面或其他
				}
				return false;
			}

		} else {
			if (Session::has('open_uid')) {
				// 表示登录
				$uid = Session::get('open_uid');
				$token = Session::get('token');
				$account = Session::get('account');
				$nick = Session::get('nick');
				$face = Session::get('face');

				return array('uid' => $uid, 'token' => $token, 'account' => $account, 'nick' => $nick, 'face' => $face);
			} else {
				return false;
			}
		}
	}

	// 得到session
	public static function getSession($key) {
		$val = Session::get($key);
		if (!isset($val)) {
			$val = "";
		}
		return $val;
	}

	// 设置Open登录Session
	// $val 可以是数组 a,[1,2,3]
	public static function setLoginSession($uid, $token, $account, $nick, $face) {
		Session::put('open_uid', $uid);
		Session::put('token', $token);
		Session::put('account', $account);
		Session::put('nick', $nick);
		Session::put('face', $face);

		return true;
	}

	// 设置单个session
	public static function setSession($k, $v) {
		return Session::put($k, $v);
	}

	// 删除Session
	public static function delSession($k) {
		return Session::forget($k);
	}

	// 得到所有session
	public static function getAllSession() {

		return Session::all();
	}

	// 删除所有session
	public static function delAllSession() {

		return Session::flush();
	}
}
