<?php

namespace App\Helper;

use Config;

class Vredis
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

        if (!$configs) {
            throw new \Exception("config file {$group} not exist");
            return false;
        }

        $cache_config     = $configs['cache_config'];
        $cache_connection = $configs['cache_connection'];

        $conn = $cache_config['connection'];

        if (isset(self::$connections[$conn]) && self::$connections[$conn]) {
            return array("conn" => self::$connections[$conn], "conf" => $cache_config);
        }

        $redis   = new \Redis;
        $connect = $redis->connect($cache_connection['host'], $cache_connection['port'], 1);
        if (!$connect) {
            return false;
        }
        if (isset($cache_connection['password']) && $cache_connection['password']) {
            $ret = $redis->auth($cache_connection['password']);
            if (!$ret) {
                $redis->close();
                return false;
            }
        }
        self::$connections[$conn] = $redis;
        return array("conn" => self::$connections[$conn], "conf" => $cache_config);
    }

    /**
     * 缓存配置
     * @param   string  group   key组
     * @return  array()
     */
    private static function getConfig($group)
    {
        $cache_config = Config::get("cache_config.redis.{$group}");
        if (!$cache_config) {
            throw new \Exception("config file {$group} not exist");
            return false;
        }
        $conn = $cache_config['connection'];

        $app_env = getenv("LARAVEL_APP_ENV");
        if ($app_env && !in_array($app_env, array("preonline"))) {
            $cache_connection = Config::get("cache_connection_dev.redis.{$conn}");
        } else {
            $cache_connection = Config::get("cache_connection.redis.{$conn}");
        }

        return array("cache_config" => $cache_config, "cache_connection" => $cache_connection);
    }

    /**
     * 删除一个缓存
     * @param   string  group   key组
     * @return  int     1 成功 0 失败
     */
    public static function del($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->del($key);
    }

    /**
     * 查询一个key剩余的有效时间
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  int     剩余的过期时间，如果没有设置过期时间返回-1，如果已经过期或key不存在，返回-2
     */
    public static function ttl($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->ttl($key);
    }

    /**
     * 查询一个key是否存在
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  bool
     */
    public static function exists($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->exists($key);
    }

    /**
     * 设置过期时间，参数是秒
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  bool
     */
    public static function expire($group, $suffix, $expire = 0)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->expire($key, $expire);
    }

    /**
     * 设置过期时间，参数是时间戳
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     timestamp  过期的时间戳
     * @return  int
     */
    public static function expireat($group, $suffix, $timestamp = 0)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->expireat($key, $timestamp);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             字 符 串                                                        |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 获取一个key
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  string
     */
    public static function get($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->get($key);
    }

    /**
     * 获取多个key
     * @param   string  group   key组
     * @param   array   suffixs  key后缀数组
     * @return  array
     */
    public static function mget($group, $suffixs)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];

        $keys = array();
        foreach ($suffixs as $key => $val) {
            $k = $conf['key_prefix'] . $val;
            if (strlen($k) == 0) {
                continue;
            }
            $keys[] = $k;
        }
        $result = $redis->mget($keys);
        $return = array();
        if (is_array($result)) {
            for ($i = 0; $i < count($keys); $i++) {
                $return[$keys[$i]] = $result[$i];
            }
            return $return;
        }
        return $result;
    }

    /**
     * 设置一个key
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @param   int     expire  过期时间, 0不过期
     * @return  bool
     */
    public static function set($group, $suffix, $value, $expire = null)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if ($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        if ($expire > 0) {
            return $redis->set($key, $value, $expire);
        } else {
            return $redis->set($key, $value);
        }
    }

    /**
     * 设置一key，只有该key不存在的时候才设置成功
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @param   int     expire  过期时间, 0不过期
     * @return  bool
     */
    public static function setnx($group, $suffix, $value, $expire = null)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if ($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        $ret = $redis->setnx($key, $value);
        if ($ret) {
            if ($expire > 0) {
                $redis->expire($key, $expire);
            }
        }
        return $ret;
    }

    /**
     * 设置一key，只有该key存在的时候才设置成功
     * 主要用来将值和过期时间一起设置的
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @param   int     expire  过期时间, 必传，大于0
     * @return  bool
     */
    public static function setex($group, $suffix, $value, $expire)
    {
        if (!$expire) {
            return false;
        }
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if ($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        return $redis->setex($key, $expire, $value);
    }

    /**
     * 批量设置多个key，参数是后缀对应的值的数组
     * @param   string  group   key组
     * @param   array   items   key后缀数组
     * @return  bool
     */
    public static function mset($group, $items)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis    = $info['conn'];
        $conf     = $info['conf'];
        $newitems = array();
        foreach ($items as $suffix => $val) {
            $newkey            = $conf['key_prefix'] . $suffix;
            $newitems[$newkey] = $val;
        }
        return $redis->mset($newitems);
    }

    /**
     * 在一个key对应的value后面追加内容
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @return  bool
     */
    public static function append($group, $suffix, $value)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->append($key, $value);
    }

    /**
     * 设置一个新值，并获得老值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @param   int     expire  过期时间
     * @return  string
     */
    public static function getset($group, $suffix, $value, $expire = null)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if ($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        $ret = $redis->getset($key, $value);
        if ($ret) {
            if ($expire > 0) {
                $redis->expire($key, $expire);
            }
        }
        return $ret;
    }

    /**
     * 值加1，不存在创建
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  bool
     */
    public static function incr($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->incr($key);
    }

    /**
     * 值加指定整数，不存在创建
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @return  bool
     */
    public static function incrby($group, $suffix, $value)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->incrby($key, $value);
    }

    /**
     * 值减1，不存在创建
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  bool
     */
    public static function decr($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->decr($key);
    }

    /**
     * 值减指定整数，不存在创建
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  value   设置的值
     * @return  bool
     */
    public static function decrby($group, $suffix, $value)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->decrby($key, $value);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             哈 希                                                           |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 设置一个字段的值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  field   字段名称
     * @param   string  value   字段值
     * @return  int false 失败;1:新增field成功;0:修改field成功;
     */
    public static function hset($group, $suffix, $field, $value)
    {
        if (!$field) {
            return false;
        }
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hset($key, $field, $value);
    }

    /**
     * 设置一个字段的值，这个字段不存在的时候才能设置成功，否则设置失败
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  field   字段名称
     * @param   string  value   字段值
     * @return  bool
     */
    public static function hsetnx($group, $suffix, $field, $value)
    {
        if (!$field) {
            return false;
        }
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hsetnx($key, $field, $value);
    }

    /**
     * 设置多个字段的值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   array   items   设置的值，key=value数组
     * @param   int     expire  过期时间
     * @return  bool
     */
    public static function hmset($group, $suffix, $items, $expire = null)
    {
        if (!$items || !is_array($items)) {
            return false;
        }
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if ($expire === null) {
            $expire = isset($conf['expire']) && $conf['expire'] ? $conf['expire'] : 0;
        }
        $ret = $redis->hmset($key, $items);
        if ($ret) {
            if ($expire > 0) {
                $redis->expire($key, $expire);
            }
        }
        return $ret;
    }

    /**
     * 获得一个字段的值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  field   字段名称
     * @return  string
     */
    public static function hget($group, $suffix, $field)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hget($key, $field);
    }

    /**
     * 获得指定的多个字段的值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   array   fields  字段数组
     * @return  array
     */
    public static function hmget($group, $suffix, $fields)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hmget($key, $fields);
    }

    /**
     * 获得一个key的所有字段的值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  array
     */
    public static function hgetall($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hgetall($key);
    }

    /**
     * 删除一个字段
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string/array  fields  要删除的字段
     * @return  bool
     */
    public static function hdel($group, $suffix, $fields)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hdel($key, $fields);
    }

    /**
     * 获得字段的数量
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  int
     */
    public static function hlen($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hlen($key);
    }

    /**
     * 判断字段是否存在
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  field   字段名称
     * @return  bool
     */
    public static function hexists($group, $suffix, $field)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hexists($key, $field);
    }

    /**
     * 字段的值自增
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  field   字段名称
     * @param   int     num     数量
     * @return  int
     */
    public static function hincrby($group, $suffix, $field, $num)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hincrby($key, $field, $num);
    }

    /**
     * 获得所有字段列表
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  array
     */
    public static function hkeys($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hkeys($key);
    }

    /**
     * 获得所有值的列表
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  array
     */
    public static function hvals($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->hvals($key);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             list                                                            |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 从左侧添加到队列一个值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string/array  values   值
     * @return  int 队列长度
     */
    public static function lpush($group, $suffix, $values)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;

        if (!is_array($values)) {
            return $redis->lpush($key, $values);
        } else {
            array_unshift($values, $key);
            return call_user_func_array(array($redis, "lpush"), $values);
        }
    }

    /**
     * 从右侧添加到队列一个值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string/array  values   值
     * @return  int 队列长度
     */
    public static function rpush($group, $suffix, $values)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if (!is_array($values)) {
            return $redis->rpush($key, $values);
        } else {
            array_unshift($values, $key);
            return call_user_func_array(array($redis, "rpush"), $values);
        }
    }

    /**
     * 从左侧拿出队列一个值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  string
     */
    public static function lpop($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->lpop($key);
    }

    /**
     * 从右侧拿出队列一个值
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  string
     */
    public static function rpop($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->rpop($key);
    }

    /**
     * 从左侧拿出队列一个值，如果没有了，就等 timeout 秒，timeout设置最好小于60
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     timeout   超时时间
     * @return  string
     */
    public static function blpop($group, $suffix, $timeout)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->blpop($key, $timeout);
    }

    /**
     * 从右侧拿出队列一个值，如果没有了，就等 timeout 秒，timeout设置最好小于60
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     timeout   超时时间
     * @return  string
     */
    public static function brpop($group, $suffix, $timeout)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->brpop($key, $timeout);
    }

    /**
     * 获得队列长度
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  int
     */
    public static function llen($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->llen($key);
    }

    /**
     * 从队列1 拿出一个值，再放入队列2
     * 必须是在同一个连接里！！！！！！！！！！！！
     * @param   string  group1  key组1
     * @param   string  suffix1 key后缀1
     * @param   string  group2  key组2
     * @param   string  suffix2 key后缀2
     * @return  string
     */
    public static function rpoplpush($group1, $suffix1, $group2, $suffix2)
    {
        $info1 = self::connection($group1);
        $info2 = self::connection($group2);
        if (!$info1 || !$info2) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key1  = $conf['key_prefix'] . $suffix1;
        $key2  = $conf['key_prefix'] . $suffix2;
        return $redis->rpoplpush($key1, $key2);
    }

    /**
     * 从队列1 拿出一个值，再放入队列2，如果队列1没有，等待timeout秒
     * 必须是在同一个连接里！！！！！！！！！！！！
     * @param   string  group1  key组1
     * @param   string  suffix1 key后缀1
     * @param   string  group2  key组2
     * @param   string  suffix2 key后缀2
     * @param   int     timeout  超时时间
     * @return  string
     */
    public static function brpoplpush($group1, $suffix1, $group2, $suffix2, $timeout)
    {
        $info1 = self::connection($group1);
        $info2 = self::connection($group2);
        if (!$info1 || !$info2) {
            return false;
        }
        $redis = $info1['conn'];
        $key1  = $info1['conf']['key_prefix'] . $suffix1;
        $key2  = $info2['conf']['key_prefix'] . $suffix2;
        return $redis->brpoplpush($key1, $key2, $timeout);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             集 合                                                           |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 集合中添加一个元素
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string/array  members   添加的元素列表
     * @return  int 添加的元素数量
     */
    public static function sadd($group, $suffix, $members)
    {
        if (!$members) {
            return false;
        }
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        if (!is_array($members)) {
            return $redis->sadd($key, $members);
        } else {
            array_unshift($members, $key);
            return call_user_func_array(array($redis, "sadd"), $members);
        }
    }

    /**
     * 集合中删除一个元素
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  member   添加的元素列表
     * @return  int 删除的元素数量
     */
    public static function srem($group, $suffix, $member)
    {
        if (!$member || is_array($member)) {
            return false;
        }
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;

        return $redis->srem($key, $member);
    }

    /**
     * 获得集合的所有成员
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  int
     */
    public static function smembers($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->smembers($key);
    }

    /**
     * 判断某个值是否是集合中的成员
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  member  一个元素
     * @return  bool
     */
    public static function sismember($group, $suffix, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->sismember($key, $member);
    }

    /**
     * 获得集合成员数量
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  int
     */
    public static function scard($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->scard($key);
    }

    /*
    +-----------------------------------------------------------------------------+
    |                                                                             |
    |             有 序 集 合                                                     |
    |                                                                             |
    +-----------------------------------------------------------------------------+
     */

    /**
     * 添加一个成员到有序集合
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     score   排序字段
     * @param   string  member   添加的元素
     * @return  int     添加成功的数量
     */
    public static function zadd($group, $suffix, $score, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zadd($key, $score, $member);
    }

    /**
     * 删除一个成员
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  member   添加的元素
     * @return  int
     */
    public static function zrem($group, $suffix, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrem($key, $member);
    }

    /**
     * 有序集合成员数量
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @return  int
     */
    public static function zcard($group, $suffix)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zcard($key);
    }

    /**
     * 获得某个成员在集合中的score
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  member  集合成员
     * @return  int
     */
    public static function zscore($group, $suffix, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zscore($key, $member);
    }

    /**
     * 某个成员的score加上一个整数
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     num     加的数量
     * @param   string  member  集合成员
     * @return  int
     */
    public static function zincrby($group, $suffix, $num, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zincrby($key, $num, $member);
    }

    /**
     * 获得成员在集合中排名，score从大到小
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  member  集合成员
     * @return  int
     */
    public static function zrank($group, $suffix, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrank($key, $member);
    }

    /**
     * 获得成员在集合中排名，score从小到大
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   string  member  集合成员
     * @return  int
     */
    public static function zrevrank($group, $suffix, $member)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrevrank($key, $member);
    }

    /**
     * 获得集合中排名在指定区间的成员，score由大到小排
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     start   开始位置
     * @param   int     stop    结束位置
     * @return  int
     */
    public static function zrange($group, $suffix, $start, $stop, $withscore = false)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrange($key, $start, $stop, $withscore);
    }

    /**
     * 获得集合中排名在指定区间的成员，score由小到大排
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     start   开始位置
     * @param   int     stop    结束位置
     * @return  int
     */
    public static function zrevrange($group, $suffix, $start, $stop, $withscore = false)
    {

        $info = self::connection($group);
        if (!$info) {
            return false;
        }

        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrevrange($key, $start, $stop, $withscore);
    }

    /**
     * 获得集合中积分在指定区间的成员，score由大到小排
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     min     最小积分
     * @param   int     stop    最大积分
     * @return  int
     */
    public static function zrangebyscore($group, $suffix, $min, $max)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrangebyscore($key, $min, $max);
    }

    /**
     * 获得集合中积分在指定区间的成员，score由小到大排
     * @param   string  group   key组
     * @param   string  suffix  key后缀
     * @param   int     min     最小积分
     * @param   int     stop    最大积分
     * @return  int
     */
    public static function zrevrangebyscore($group, $suffix, $min, $max)
    {
        $info = self::connection($group);
        if (!$info) {
            return false;
        }
        $redis = $info['conn'];
        $conf  = $info['conf'];
        $key   = $conf['key_prefix'] . $suffix;
        return $redis->zrevrangebyscore($key, $min, $max);
    }

    /**
     * 关闭链接
     */
    public static function close($group = null)
    {
        if ($group) {
            $configs          = self::getConfig($group);
            $cache_config     = $configs['cache_config'];
            $cache_connection = $configs['cache_connection'];
            $conn             = $cache_config['connection'];
            if (isset(self::$connections[$conn])) {
                $ret = self::$connections[$conn]->close();
                unset(self::$connections[$conn]);
                return $ret;
            }
            return false;
        }
        if (self::$connections && is_array(self::$connections)) {
            foreach (self::$connections as $key => $val) {
                $val->close();
                unset(self::$connections[$key]);
            }
        }
        return true;
    }

}
