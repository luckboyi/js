<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommonModel extends Model
{
    //use SoftDeletes;

    protected static $alias;
    protected static $config;

    protected $alias_table;
    protected $allowMethod;
    protected $transAttribute;
    protected $transJson;

    protected $needTrans = array();

    /**
     * 构造函数，实例化时根据tablemap配置文件进行处理
     *
     * @param array $attributes [description]
     */
    public function __construct(array $attributes = [])
    {

        $config = self::$config;

        foreach ($config as $property => $value) {
            $this->{$property} = $value;
        }

        if (isset($this->transAttribute)) {
            $this->needTrans = array_merge($this->needTrans, $this->transAttribute);
        }

        if (isset($this->transJson)) {
            $this->needTrans = array_merge($this->needTrans, $this->transJson);
        }

        $this->alias_table = self::$alias;

        parent::__construct($attributes);

    }

    /**
     * 设置对应的数据表，根据配置名实例化对应的模型
     *
     * @param  array $table 传了数据表的配置名，配置在config/tablemap.php里
     * @return Model 返回模型
     */
    public static function set($table)
    {
        self::$alias = $table;

        $defaultCfg = Config::get("tablemap.default");
        $tableCfg   = Config::get("tablemap.{$table}");

        self::$config = array_merge($defaultCfg, $tableCfg);

        $model = new static;

        return $model;
    }

    /**
     * 调用非public方法的预处理函数
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->transAttribute) && in_array($method, $this->transAttribute)) {
            return call_user_func_array([$this, "transAttributeToArr"], $parameters);
        }

        if (isset($this->transJson) && in_array($method, $this->transJson)) {
            return call_user_func_array([$this, "transJsonToArr"], $parameters);
        }

        if (!$this->checkAllowMethod($method)) {
            return parent::__call($method, $parameters);
        }

        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * 预处理相关字段
     *
     * @param  [type]  $key [description]
     * @return boolean      [description]
     */
    public function hasGetMutator($key)
    {
        if ($this->needTrans) {
            return in_array('get' . Str::studly($key) . 'Attribute', $this->needTrans);
        }
        return method_exists($this, 'get' . Str::studly($key) . 'Attribute');
    }

    /**
     * 检查数据表是否可以调用相应方法
     *
     * @param  string $method 方法名
     * @return boolean
     */
    public function checkAllowMethod($method)
    {

        //方法不在配置中，默认可以调用
        if (!isset($this->allowMethod)) {
            return false;
        }

        //对应表在配置中，可以正常调用
        if (!in_array($method, $this->allowMethod)) {
            return false;
        }

        return true;
    }

    /**
     * 获取游戏评论
     *
     * @return Model 游戏评论模型
     */
    private function comment()
    {
        return $this->hasMany(CommonModel::set("Comment"), "target_id", "appid");
    }

    /**
     * 获取所有通过审核的内容：设备类型或者游戏类型，根据设置的表读取
     *
     * @return 查询结果
     */
    private function getAllPassedType()
    {
        return $this->where("ispassed", "Y")->get();
    }

    /**
     * 预处理游戏类型Gtid，自动分割为数组
     *
     * @param  string $value 以“,”分割的gtid字段
     * @return array  gtid数组
     */
    public function transAttributeToArr($value)
    {
        $arr = explode(",", $value);
        return array_filter($arr);
    }

    /**
     * 获取菜单列表
     *
     * @return [type] [description]
     */
    private function getAdminMenuList()
    {
        return $this->where("type", "admin")
            ->where("status", 1)
            ->get();
    }

    /**
     * 获取游戏
     */
    private function game()
    {
        return $this->hasOne(CommonModel::set("Game"), "appid", "content_id");
    }

    /**
     * 预处理数据表里储存结构为json的字段
     *
     * @param  string $value json
     * @return array
     */
    public function transJsonToArr($value)
    {
        return json_decode($value, true);
    }
}
