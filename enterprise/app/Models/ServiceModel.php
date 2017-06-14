<?php
/*
客服系统model
date:2016/11/1
 */

namespace App\Models;

use DB;
use Helper\UdpLog;
use Illuminate\Database\Eloquent\Model;

class ServiceModel extends Model
{

    //不设置属性，默认连接mysql配置
    protected $connection   = 'db_operate';
    private $serviceDevSize = 15;

    public function searchQuestion($search)
    {
        if (!$search) {
            return false;
        }

        $row = DB::connection('db_operate')->table('service_question')->where("code", $search)->first();

        return $row;
    }

    public function serviceQuestion($stat = 0, $search = '')
    {
        $res = DB::connection('db_operate')->table('service_question');

        if ($search) {
            if (strlen($search) == 16) {
                $res->where("code", $search);
            } else {
                $res->where("title", "LIKE", '%' . $search . '%');
            }
        }

        if ($stat > 0) {
            $res->where("stat", $stat);
        }

        $row = $res->orderBy('ctime', 'desc')->paginate($this->serviceDevSize);
        return $row;
    }

    public function updateQuestion($id, $info)
    {
        if (!$info) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection('db_operate')->table('service_question')->where('id', $id)->update($info);
        } else {
            $ret = DB::connection('db_operate')->table('service_question')->insert($info);
        }

        return $ret;
    }

    /**
     * 根据分类获取QA列表
     *
     * @param  integer $page     [description]
     * @param  integer $pageSize [description]
     * @param  integer $tp       [description]
     * @return [type]            [description]
     */
    public function getServiceQaList($tp = 0, $pageSize = 15)
    {
        $pageSize = (int) $pageSize;
        $tp       = (int) $tp;

        if ($pageSize < 1 || $tp < 0) {
            return false;
        }

        $res = DB::connection('db_operate')->table('service_faq');

        if ($tp == 0) {
            $row = $res->orderBy('ltime', 'desc')->forPage(1, $pageSize)->get();
        } else {
            $row = $res->where("tp", $tp)->orderBy('ltime', 'desc')->paginate($pageSize);
        }

        return $row;
    }

    public function serviceQaId($id)
    {
        if (!$id) {
            return false;
        }
        $row = DB::connection('db_operate')->table('service_faq')->where("id", $id)->first();
        if ($row) {
            DB::connection('db_operate')->table('service_faq')->where("id", $id)->update(['view' => $row['view'] + 1]);
        }
        return $row;
    }

    public function serviceQa($search = '')
    {
        $res = DB::connection('db_operate')->table('service_faq');

        if ($search) {
            if (is_numeric($search)) {
                $res->where("id", $search);
            } else {
                $res->where("question", "LIKE", '%' . $search . '%');
            }
        }

        $row = $res->orderBy('ltime', 'desc')->paginate($this->serviceDevSize);

        return $row;
    }

    public function updateQa($id, $info)
    {
        if (!$info) {
            return false;
        }
        if ($id > 0) {
            $ret = DB::connection('db_operate')->table('service_faq')->where('id', $id)->update($info);
        } else {
            $ret = DB::connection('db_operate')->table('service_faq')->insert($info);
        }

        return $ret;
    }

    public function delServiceQa($id)
    {
        if (!$id) {
            return false;
        }

        $ret = DB::connection('db_operate')->table('service_faq')->where('id', $id)->delete();

        return $ret;
    }

    public function delFeedback($code)
    {
        if (!$code) {
            return false;
        }

        $ret = DB::connection('db_operate')->table('service_question')->where('code', $code)->delete();

        return $ret;
    }

    /**
     * 向advice表插入一条登录信息
     * @param   array data
     * @return  int  primary key id
     */
    public function insertAdvice($data)
    {
        if (!$data || !is_array($data) || count($data) < 4) {
            return false;
        }

        try {
            $id = DB::connection("db_operate")->table('advice')->insertGetId($data);
        } catch (\Exception $e) {
            UdpLog::save2("enterprise/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        return $id;
    }

    /**
     * 获取所有的意见反馈列表
     * @param   null
     * @return  array
     */
    public function getAdvice()
    {

        try {
            $result = DB::connection("db_operate")->table('advice')->orderBy('id', 'desc')->get();
        } catch (\Exception $e) {
            UdpLog::save2("enterprise/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        return $result;
    }

    /**
     * 根据意见反馈id,得到一条信息
     * @param   int id
     * @return  array
     */
    public function getAdviceById($id)
    {
        if (!$id) {
            return false;
        }
        $data['id'] = $id;
        try {
            $result = DB::connection("db_operate")->table('advice')->where($data)->first();
        } catch (\Exception $e) {
            UdpLog::save2("enterprise/storage/error", array("log" => $e->getMessage(), "args" => func_get_args()), __METHOD__ . "[" . __LINE__ . "]");
            return false;
        }

        return $result;
    }

}
