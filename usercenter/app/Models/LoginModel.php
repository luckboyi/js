<?php

/*
登录Model
date:2016/8/22
 */

namespace App\Models;

use DB;
use Helper\UdpLog;
use App\Helper\Vmemcached;
use App\Helper\Vredis;
use Illuminate\Database\Eloquent\Model;

class LoginModel extends Model
{

    private $tokenPrefix = "token_";

    //不设置属性，默认连接mysql配置
    protected $connection = 'db_login';
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
     * 获取用户库的库、表名后缀
     */
    protected function getDB($account)
    {
        if (!$account) {
            return false;
        }
        $db_suff  = 0;
        $tbl_suff = hexdec(substr(md5($account), 0, 2)) % 32;
        return array('db' => "db_login", 'table_login' => "t_login_" . $tbl_suff);
    }

    /**
     * 根据token获取uid
     * @param   int     uid
     * @param   type    类型，login_token/pay_token
     * @return  string
     */
    public function getToken($uid, $type)
    {
        if (!$uid) {
            return false;
        }
        try {
            $token = Vmemcached::get($type, $uid);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $token;
    }

    /**
     * 生成token
     * @param   int     uid
     * @param   type    类型，login_token/pay_token
     * @param   string  token   token
     * @param   int     expire  过期时间
     * @return  bool
     */
    public function genToken($uid, $type, $token, $expire=null)
    {
        if (!$uid || !$type || !$token) {
            return false;
        }
        /**
         * 通过id、随机数、时间戳哈希成token
         */
        try {
            $ret = Vmemcached::set($type, $uid, $token, $expire);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 生成校验用的code
     * @param   int     id
     * @param   string  code   code
     * @param   int     expire  过期时间
     * @return  bool
     */
    public function genTmpCode($id, $code, $expire=null)
    {
        if (!$id || !$code) {
            return false;
        }
        try {
            $ret = Vmemcached::set("tmp_code", $id, $code, $expire);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $ret;
    }

    /**
     * 根据token获取uid
     * @param   int     id
     * @return  string
     */
    public function getTmpCode($id)
    {
        if (!$id) {
            return false;
        }
        try {
            $code = Vmemcached::get("tmp_code", $id);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $code;
    }

    /**
     * 向t_login_x表中插入一条数据
     * @param   array  data
     * @return  int >0 表示所影响的行数，=0 表示新增失败
     */
    public function tLoginInsert($data = array())
    {
        $loginRes = $this->getDB($data['f_login']);

        try {
            $affect = DB::connection($loginRes['db'])->table($loginRes['table_login'])->insert($data);
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $affect;
    }
    /**
     * 向t_login_x表中插入一条数据
     * @param   array  data
     * @return  int >0 表示所影响的行数，=0 表示新增失败
     */
    public function tLoginUpdate($mobile, $data = array())
    {

        $loginRes = $this->getDB($mobile);
        $where    = array(
            'f_login' => $mobile,
        );
        $affect = DB::connection($loginRes['db'])->table($loginRes['table_login'])->where($where)->delete();

        if ($affect) {
            $ret = $this->tLoginInsert($data);
            return $ret;
        }
        return $affect;
    }

    /*
     * 解除手机号的绑定接口
     * 删除t_login_x的记录
     * @param   array  data
     * @return  int >0 表示所影响的行数，=0 表示新增失败
     */
    public function unbind($data = array())
    {
        //先删除记录表
        $loginRes = $this->getDB($data['f_login']);
        $where    = array(
            'f_login' => $data['f_login'],
        );
        $affect = DB::connection($loginRes['db'])->table($loginRes['table_login'])->where($where)->delete();
        return $affect;
    }

    /**
     * 获取uid
     * @param   string  account
     * @return  int  f_uid
     */
    public function getUid($account)
    {
        $dbRes = $this->getDB($account);
        try {
            $uid = DB::connection($dbRes['db'])->table($dbRes['table_login'])->where('f_login', $account)->value('f_uid');
        } catch (\Exception $e) {
            UdpLog::save2("usercenter/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }
        return $uid;

    }

    /**
     * 判断用户存不存在
     * @param   string  username
     * @return  int  f_uid
     */
    public function existUser($account)
    {
        return $this->getUid($account);
    }

    /**
     * 读上次登录状态
     * @param   int     uid
     * @return  array   info
     */
    public function getLastLogin($uid)
    {
        if(!$uid) {
            return false;
        }
        $ret = Vredis::hgetall("user_last_login", $uid);
        $ret['this'] = isset($ret['this']) && $ret['this'] ? json_decode($ret['this'], true) : "";
        $ret['last'] = isset($ret['last']) && $ret['last'] ? json_decode($ret['last'], true) : "";
        Vredis::close();
        return $ret;
    }

    /**
     * 修改登录状态，当前的，和上次的
     * 先修改再读
     * @param   int     uid
     * @param   array   info    本次登录信息，array(ts=>xxx, ip=>xxx, city=>xxx, type=>登录方式, appid=>xxx)
     * @return  array   上次登录信息
     */
    public function setLastLogin($uid, $info)
    {
        if(!$uid || !$info || !is_array($info)) {
            return false;
        }
        $ret = Vredis::hgetall("user_last_login", $uid);
        $ret['last'] = isset($ret['this']) && $ret['this'] ? $ret['this'] : "";
        $ret['this'] = json_encode($info);
        Vredis::hmset("user_last_login", $uid, $ret);
        Vredis::close();
        return json_decode($ret['last'], true);
    }
}
