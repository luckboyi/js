<?php
/**
 * this is part of xyfree
 *
 * @file TcpLogClient.php
 * @use tcp 日志客户端
 * @author Dongjiwu(dongjw321@163.com)
 * @date 2014-12-08 14:00
 *
 */

namespace Helper;

use Config;

class UdpLog
{
    public static $log  = array();
    public static $dir  = false;
    public static $host = "";
    public static $port = 0;

    public static function setHost($host, $port)
    {
        self::$host = $host;
        self::$port = $port;
    }

    public static function record($file, $info)
    {
        self::$log[] = array('file'=>$file,'info'=>$info,'time'=>time());
    }

    public static function save($file='', $info='', $dir='', $commonlog="")
    {
        if(!self::$host) {
            self::$host = env('UDP_HOST', '10.154.54.37');
        }
        if(!self::$port) {
            self::$port = env('UDP_PORT', 8300);
        }
        $args = func_get_args();
        if (!in_array(func_num_args(), array(1, 3, 4))) {
            throw new Exception('params error');
        }
        if (func_num_args() == 1) {
            self::$dir = $args[0];
        } else {
            if(is_array($info)) {
                $l = $info;
                isset($commonlog) && $l['comm'] = $commonlog;
                isset($_GET) && $l['get'] = $_GET;
                isset($_POST) && $l['post'] = $_POST;
            }else {
                $l = array("log" => $info);
                isset($commonlog) && $l['comm'] = $commonlog;
                isset($_GET) && $l['get'] = $_GET;
                isset($_POST) && $l['post'] = $_POST;
            }
            self::record($file, json_encode($l));
            self::$dir = $dir;
        }

        if (count(self::$log) < 1) {
            return true;
        }

        $data = array();
        foreach (self::$log as $row) {
            $data[] = array('filename' => self::$dir.'/'.$row['file'],'log' =>$row['info'],'time'=>time());
        }
        $msg  = json_encode($data) . "\n";

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $msg, strlen($msg), 0, self::$host, self::$port);
        socket_close($socket);
        //清空已经发送的日志
        self::$log = array();

        return true;
    }


    public static function save2($file, $info='', $commonlog="", $isRequest=true)
    {
        $app_env = getenv("LARAVEL_APP_ENV");
        if($app_env && !in_array($app_env, array("preonline"))) {
            $udpserver = Config::get("cache_connection_dev.udpserver");
        }else {
            $udpserver = Config::get("cache_connection.udpserver");
        }

        self::$host = $udpserver['host'];
        self::$port = $udpserver['port'];

        $l = array();
        if(!defined("_SYSTEM_PROCESS_UNIQUE_ID_")) {
            define("_SYSTEM_PROCESS_UNIQUE_ID_", uniqid("", true));
        }
        $l["pid"] = _SYSTEM_PROCESS_UNIQUE_ID_;
        if(is_array($info)) {
            $l = $l + $info;
        }else {
            $l['log'] = $info;
        }
        isset($commonlog) && $l['common'] = $commonlog;
        $isRequest && isset($_GET) && $l['get'] = $_GET;
        $isRequest && isset($_POST) && $l['post'] = $_POST;

        self::record($file, json_encode($l));

        if (count(self::$log) < 1) {
            return true;
        }

        $data = array();
        foreach (self::$log as $row) {
            $data[] = array('filename' => $row['file'],'log' =>$row['info'],'time'=>time());
        }
        $msg  = json_encode($data);

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $msg, strlen($msg), 0, self::$host, self::$port);
        socket_close($socket);
        //清空已经发送的日志
        self::$log = array();

        return true;
    }
}
