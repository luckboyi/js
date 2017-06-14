<?php

namespace App\Helper;

use Config;

class Vmemcached
{
    private static $connections = array();

    /**
     * 获得连接
     * @param   string  group   key组
     * @return  array()
     */
    public static function connection($group)
    {
        $configs = self::getConfig($group);
        if(!$configs) {
            throw new \Exception("config file {$group} not exist");
            return false;
        }

        $cache_config = $configs['cache_config'];
        $cache_connection = $configs['cache_connection'];

        $connection = $cache_config['connection'];
        if(isset(self::$connections[$connection]) && self::$connections[$connection]) {
            return array("conn" => self::$connections[$connection], "conf" => $cache_config);
        }

        $servers = $cache_connection['servers'];
        $memcache = new \Memcached;
        for($i = 0; $i < count($servers); $i++) {
            $host   = $servers[$i]['host'];
            $port   = $servers[$i]['port'];
            $weight = $servers[$i]['weight'];
            $memcache->addServer($host, $port, $weight);
        }
        self::$connections[$connection] = $memcache;
        return array("conn" => self::$connections[$connection], "conf" => $cache_config);
    }

    /**
     * 缓存配置
     * @param   string  group   key组
     * @return  array()
     */
    private static function getConfig($group)
    {
        $cache_config = Config::get("cache_config.memcached.{$group}");
        if(!$cache_config) {
            throw new \Exception("config file {$group} not exist");
            return false;
        }
        $connection = $cache_config['connection'];

        $app_env = getenv("LARAVEL_APP_ENV");
        if($app_env && !in_array($app_env, array("preonline"))) {
            $cache_connection = Config::get("cache_connection_dev.memcached.{$connection}");
        }else {
            $cache_connection = Config::get("cache_connection.memcached.{$connection}");
        }

        return array("cache_config" => $cache_config, "cache_connection" => $cache_connection);
    }

    /**
     * memcached::get
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  string
     */
    public static function get($group, $suffix)
    {
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        $key  = $conf['key_prefix'] . $suffix;
        if(strlen($key) == 0) {
            return false;
        }
        return $mc->get($key);
    }

    /**
     * memcached::get
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  array
     */
    public static function getMulti($group, $suffix)
    {
        if(!$suffix || !is_array($suffix)) {
            return false;
        }
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];

        $key = array();
        foreach($suffix as $key => $val) {
            $k = $conf['key_prefix'] . $val;
            if(strlen($k) == 0) {
                continue;
            }
            $key[] = $k;
        }
        return $mc->getMulti($key);
    }

    /**
     * memcached::set
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @param   int     expire  过期时间
     * @return  bool
     */
    public static function set($group, $suffix, $value, $expire = null)
    {
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        $key  = $conf['key_prefix'] . $suffix;
        if($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        return $mc->set($key, $value, $expire);
    }

    /**
     * memcached::setMulti
     * @param   string  group   key组
     * @param   array   items   key后缀与value的数组, array('suffix1' => 'val1', 'suffix2' => 'val2', 'suffix3' => 'val3')
     * @param   int     expire  过期时间
     * @return  bool
     */
    public static function setMulti($group, $items, $expire = null)
    {
        if(!$items || !is_array($items)) {
            return false;
        }
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        if($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }

        $newitems = array();
        foreach($items as $suff => $val) {
            $newkey = $conf['key_prefix'] . $suff;
            $newitems[$newkey] = $val;
        }
        return $mc->setMulti($newitems, $expire);
    }

    /**
     * memcached::add
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @param   int     expire  过期时间
     * @return  bool
     */
    public static function add($group, $suffix, $value, $expire = null)
    {
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        $key  = $conf['key_prefix'] . $suffix;
        if($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        return $mc->add($key, $value, $expire);
    }

    /**
     * memcached::decrement
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     value   设置的值
     * @return  int
     */
    public static function decrement($group, $suffix, $value = 1)
    {
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        $key  = $conf['key_prefix'] . $suffix;
        return $mc->decrement($key, $value);
    }

    /**
     * memcached::increment
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     value   设置的值
     * @return  int
     */
    public static function increment($group, $suffix, $value = 1)
    {
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        $key  = $conf['key_prefix'] . $suffix;
        return $mc->increment($key, $value);
    }

    /**
     * memcached::delete
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     value   设置的值
     * @return  int
     */
    public static function delete($group, $suffix)
    {
        $info = self::connection($group);
        $mc   = $info['conn'];
        $conf = $info['conf'];
        $key  = $conf['key_prefix'] . $suffix;
        return $mc->delete($key);
    }

    /**
     * 关闭链接
     */
    public static function close($group = null)
    {
        if($group) {
            $configs = self::getConfig($group);
            $cache_config = $configs['cache_config'];
            $cache_connection = $configs['cache_connection'];
            $connection = $cache_config['connection'];
            if(isset(self::$connections[$connection])) {
                $ret = self::$connections[$connection]->quit();
                unset(self::$connections[$connection]);
                return $ret;
            }
            return false;
        }
        if(self::$connections && is_array(self::$connections)) {
            foreach(self::$connections as $key => $val) {
                $val->quit();
                unset(self::$connections[$key]);
            }
        }
        return true;
    }

}