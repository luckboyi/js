<?php

namespace App\Helper;

use App;
use App\Helper\ImageHelper;
use Config;
use Illuminate\Contracts\Routing\Registrar as Router;

class BladeHelper
{

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function showSerachTp($tp)
    {
        $arr = ['video' => '视频', 'news' => '新闻', 'pc' => '评测', 'game' => '游戏'];
        if (isset($arr[$tp])) {
            return $arr[$tp];
        } else {
            return '新闻';
        }
    }
    public function showSubClass($name, $tp, $sub_tp)
    {
        switch ($name) {
            case 'service_question_sub_tp':
                $classConfig = Config::get("category.service_question_tp");
                if (!isset($classConfig[$tp]['sub'])) {
                    return "";
                }
                if (!isset($classConfig[$tp]['sub'][$sub_tp])) {
                    return "";
                }
                return $classConfig[$tp]['sub'][$sub_tp]['name'];
            default:
                # code...
                break;
        }
    }

    public function openAppImage($uid)
    {
        if (!$uid) {
            $img = "";
        }

        $imgs = ImageHelper::getUrl('openuser', ['id' => $uid, 'version' => 1]);
        if (isset($imgs['idcard'])) {
            $img = $imgs['idcard'];
        } else {
            $img = "";
        }

        if ($img) {
            return '<a href="' . $img . '" target="_blank"><img class="ui tiny image" src="' . $img . '"></a>';
        } else {
            return $img;
        }

    }

    public function permHtml()
    {
        $cfg     = Config::get("admincp.perm");
        $permCfg = [];
        foreach ($cfg as $key => $value) {
            if (!isset($permCfg[$value['id']])) {
                $permCfg[$value['id']] = $value;
            }
        }
        $out  = '';
        $html = '';
        foreach ($permCfg as $permId => $value) {
            if (isset($value['tp']) && $value['tp'] == "menu") {
                if ($html) {
                    $out .= $html . '</div>';
                    $html = '';
                }
                $out .= '<h4 class="ui dividing header">' . $value['name'] . '</h4>';
            } elseif (isset($value['tp']) && $value['tp'] == "sub") {
                if ($html) {
                    $out .= $html . '</div>';
                }
                $html = '<div class="inline field">
                <label>' . $value['name'] . '</label>
                <div class="ui checkbox">
                <input type="checkbox" name="group_perms" value="' . $permId . '" >
                <label>查看</label>
                </div>';
            } else {
                $html .= '<div class="ui checkbox">
                <input type="checkbox" name="group_perms" value="' . $permId . '">
                <label>' . $value['name'] . '</label>
                </div>';
            }
        }

        if ($html) {
            $out .= $html . '</div>';
        }
        return $out;
    }

    public function get($class, $type, $separator = " ")
    {
        if (!$class || !in_array($type, ["vrgame", "video", "webgame"])) {
            return false;
        }

        $classConfig = Config::get($type . ".class");

        $classArr = explode(",", $class);
        $out      = [];
        $in       = [];
        foreach ($classArr as $value) {
            $value = isset($classConfig[$value]["name"]) ? $classConfig[$value]["name"] : '';
            if ($value && !isset($in[$value])) {
                $in[$value] = 1;
                $out[]      = $value;
            }
        }

        return join($out, $separator);
    }

    public function showHtmlClass($tp, $class, $style = 'text')
    {
        $html = '';
        switch ($tp) {
            case 'developer_stat':
                $classConfig = Config::get("category.developer_stat");
                $first       = ['id' => -1, 'name' => "全部"];
                if ($style != 'text') {
                    array_unshift($classConfig, $first);
                }
                break;
            case 'game_stat':
                $classConfig = Config::get("category.game_stat");
                break;
            case 'article':
                $classConfig = Config::get("category.article");
                break;
            case 'article_ex':
                $classConfig = Config::get("category.article");
                unset($classConfig[6]);
                unset($classConfig[7]);
                break;

            case 'vronline_video_all':
                $classConfig = Config::get("category.vronline_video");
                $first       = ['id' => 0, 'name' => "全部"];
                array_unshift($classConfig, $first);
                break;
            case 'vronline_news_all':
                $classConfig = Config::get("category.vronline_news");
                $first       = ['id' => 0, 'name' => "全部"];
                array_unshift($classConfig, $first);
                break;
            case 'vronline_pc_all':
                $classConfig = Config::get("category.vronline_pc");
                $first       = ['id' => 0, 'name' => "全部"];
                array_unshift($classConfig, $first);
                break;
            case 'article_all':
                $classConfig = Config::get("category.article");
                $first       = ['id' => 0, 'name' => "全部"];
                array_unshift($classConfig, $first);
                break;
            case "video_all":
                $classConfig = Config::get("category.video");
                $first       = ['id' => 0, 'name' => "所有"];
                array_unshift($classConfig, $first);
                break;
            case "service_qa":
                $classConfig = Config::get("category.service_qa");
                break;
            case "service_question_stat":
                $classConfig = Config::get("category.service_question_stat");
                $first       = ['id' => 0, 'name' => "全部"];
                array_push($classConfig, $first);
                break;
            case "service_question_stat_view":
                $classConfig = Config::get("category.service_question_stat");
                break;
            case "vrgame_stat":
                $classConfig = Config::get("category.service_question_stat");
                break;
            case "tob_defaultgame":
                $classConfig = Config::get("tob.defaultgame");
                $first       = ['id' => 0, 'name' => "全部"];
                array_unshift($classConfig, $first);
                break;
            case "tob_extract_stat":
                $classConfig = Config::get("category.tob_extract_stat");
                $first       = ['id' => -1, 'name' => "全部"];
                if ($style != 'text') {
                    array_unshift($classConfig, $first);
                }
                break;
            case "tob_confirm":
                $classConfig = Config::get("tob.confirm");
                $first       = ['id' => 0, 'name' => "全部"];
                array_unshift($classConfig, $first);
                break;
            case "tob_status":
                $classConfig = Config::get("category." . $tp);
                foreach ($classConfig as $key => $val) {
                    if (!isset($val["asFilter"]) || !$val["asFilter"]) {
                        unset($classConfig[$key]);
                    }
                }
                break;
            case 'support':
                $classConfig = Config::get("category.vr_device");
                $first       = ['id' => 0, 'name' => "设备"];
                array_unshift($classConfig, $first);
                break;
            default:
                $classConfig = Config::get("category." . $tp);
                break;
        }

        $class = trim($class);
        if (strlen($class) < 1) {
            $classArr = [];
        } else {
            $classArr = explode(",", trim($class));
        }

        switch ($style) {
            case 'link1':
                $html = [];
                foreach ($classConfig as $key => $value) {
                    $html[] = '<a  class="fl" href="/news/list/' . $key . '">' . $value['name'] . '</a>';
                }
                $separator = "\n";
                break;
            case 'link2':
                $html = [];
                foreach ($classConfig as $key => $value) {
                    if ($key == $class) {
                        $cur = "cur";
                    } else {
                        $cur = "";
                    }
                    $html[] = '<li class="' . $cur . ' listClass fl"><a href="/news/list/' . $key . '"  class="f14 tac">' . $value['name'] . '</a></li>';
                }
                $separator = "\n";
                break;

            case 'select':
                $html = [];

                foreach ($classConfig as $key => $value) {
                    if (in_array($key, $classArr)) {
                        $cur = "selected";
                    } else {
                        $cur = "";
                    }
                    $html[] = '<option value="' . $key . '" ' . $cur . '>' . $classConfig[$key]['name'] . '</option>';
                }
                $separator = "\n";

                break;

            case 'text':
                $html = [];
                foreach ($classArr as $value) {
                    $html[] = $classConfig[$value]['name'];
                }
                $separator = ",";
                break;
            case 'menu':
                $html = [];
                foreach ($classConfig as $key => $val) {
                    if ($val['id'] == $class) {
                        $active = 'blue';
                    } else {
                        $active = '';
                    }
                    $html[] = '<a href="?choose=' . $val['id'] . '"><div class="ui basic  button ' . $active . '">' . $val["name"] . '</div></a>';
                }
                $separator = "\n";
                break;
        }

        return join($html, $separator);
    }

    public function showHtmlStat($tp, $val, $val1 = '')
    {
        $html = '';
        switch ($tp) {
            case 'article':
                if ($val == 0) {
                    $html = "已发布";
                } else if ($val == 1) {
                    $html = "审核中";
                } else if ($val == 2) {
                    $html = "草稿";
                } else if ($val == 3) {
                    $html = "驳回";
                } else if ($val == 9) {
                    $html = "已下线";
                }
            case 'video':
                if ($val == 0) {
                    $html = "已发布";
                } else if ($val == 1) {
                    $html = "审核中";
                } else if ($val == 3) {
                    $html = "驳回";
                } else if ($val == 9) {
                    $html = "已下线";
                }

                break;
            case 'game':
                if ($val != 9 && $val1 > 0) {
                    $html = "已发布";
                } else {
                    if ($val == 0) {
                        $html = "未审核";
                    } elseif ($val == 1) {
                        $html = "审核中";
                    } else if ($val == 3) {
                        $html = "驳回";
                    } else if ($val == 5) {
                        $html = "审核成功";
                    } else if ($val == 9) {
                        $html = "已下线";
                    }
                }
                break;
            case 'game_color':
                if ($val != 9 && $val1 > 0) {
                    $html = "green";
                } else {
                    if ($val == 0) {
                        $html = "red";
                    } elseif ($val == 1) {
                        $html = "blue";
                    } else if ($val == 3) {
                        $html = "red";
                    } else if ($val == 5) {
                        $html = "green";
                    } else if ($val == 9) {
                        $html = "yellow";
                    }
                }
                break;
            case '3dbb': //3D播播
                if ($val == 0) {
                    $html = "未审核";
                } elseif ($val == 1) {
                    $html = "审核中";
                } else if ($val == 2) {
                    $html = "驳回";
                } else if ($val == 3) {
                    $html = "审核成功";
                }
                break;
            case 'vronline_comments':
                if ($val == 0) {
                    $html = "未审核";
                } elseif ($val == 1) {
                    $html = "审核通过";
                } else if ($val == 2) {
                    $html = "审核拒绝";
                }
                break;
        }

        return $html;
    }

    public function adminCpVideoClass($tp, $curClass = "")
    {
        $html       = '';
        $videoClass = Config::get("video.class");
        if ($tp == 1) {
            if ($curClass == 0) {
                $allActive = 'blue';
            } else {
                $allActive = '';
            }
            $html .= '<a href="/vrhelp/video"><div class="ui basic  button ' . $allActive . '">所有</div></a>';
            foreach ($videoClass as $key => $val) {
                if ($val['id'] == $curClass) {
                    $active = 'blue';
                } else {
                    $active = '';
                }

                $html .= '<a href="?choose=' . $val['id'] . '"><div class="ui basic  button ' . $active . '">' . $val["name"] . '</div></a>';
            }

        } else if ($tp == 2) {
            foreach ($videoClass as $key => $val) {
                $html .= '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
            }
        }
        return $html;
    }

    public function adminCpTopPos($tp, $pos, $posid)
    {
        $html = '';
        if ($tp == 1) {
            foreach ($pos as $key => $val) {
                if ($val['posid'] == $posid) {
                    $active = 'blue';
                } else {
                    $active = '';
                }

                $html .= '<a href="?posid=' . $val['posid'] . '"><div class="ui basic  button ' . $active . '">' . $val["name"] . '</div></a>';
            }
        } else if ($tp == 2) {
            foreach ($pos as $key => $val) {
                if ($val['posid'] == $posid) {
                    $active = 'selected';
                } else {
                    $active = '';
                }

                $html .= ' <option value="' . $val['posid'] . '"  ' . $active . '>' . $val['name'] . '</option>';
            }
        }

        return $html;
    }

    public function adminCpClass($tp, $choose = '', $style = '')
    {
        $topClass = Config::get("admincp." . $tp);
        $html     = '';
        if ($style == "a") {
            foreach ($topClass as $key => $val) {
                if ($key == $choose) {
                    $active = "blue";
                } else {
                    $active = "";
                }
                $html .= '<a href="?choose=' . $key . '"><div class="ui basic  button ' . $active . '">' . $val . '</div></a>';
            }
        } else if ($style == "select") {
            foreach ($topClass as $key => $val) {
                if ($key == $choose) {
                    $active = "selected";
                } else {
                    $active = "";
                }
                $html .= ' <option value="' . $key . '"  ' . $active . '>' . $val . '</option>';
            }
        } else {
            if (isset($topClass[$choose])) {
                return $topClass[$choose];
            } else {
                return '';
            }
        }
        return $html;
    }

    public function openAdminMenu($cur)
    {
        $menus = [
            ['name' => 'vrgame', 'title' => 'VR游戏', 'icon' => 'iconfire-vr '],
            ['name' => 'user', 'title' => '开发者信息', 'icon' => 'iconfire-kaifazhe'],
            // ['name' => 'setting', 'title' => '账号设置', 'icon' => 'iconfire-shezhi'],
        ];
        $html = '';
        foreach ($menus as $val) {
            if ($cur == $val['name']) {
                $active = "active";
            } else {
                $active = "";
            }
            $html .= '<a  href="/developer/' . $val['name'] . '" class="item ' . $active . '"><i class="large iconfont-fire ' . $val["icon"] . ' icon"></i>' . $val["title"] . '</a>';
        }
        return $html;
    }

    public function adminCpMenu($tp = 'main', $path1 = 'index', $path2 = 'index', $perms = [])
    {
        $menus   = Config::get("admincp.menu");
        $permCfg = Config::get("admincp.perm");
        if ($tp == 'main') {
            $html = '';

            foreach ($menus as $key => $val) {
                if (isset($permCfg[$val['name']])) {
                    $permId = $permCfg[$val['name']]['id'];
                    if (!in_array($permId, $perms)) {
                        continue;
                    }
                }
                if ($path1 == $val['name']) {
                    $active = "active";
                } else {
                    $active = "";
                }
                $html .= '<a  href="/' . $val['name'] . '" class="item ' . $active . '">' . $val["title"] . '</a>';
            }
        } else if ($tp == "sub") {
            foreach ($menus as $key => $val) {
                if ($path1 == $val['name']) {
                    $title = $val['title'];
                }
            }
            $subMenus = Config::get("admincp.menu_sub");
            $curMenus = $subMenus[$path1];
            $html     = '<div class="head item">' . $title . '</div>';
            foreach ($curMenus as $key => $val) {
                if ($path2 == $key) {
                    $active = "active";
                } else {
                    $active = "";
                }
                $path = $path1 . '/' . $key;
                if (isset($permCfg[$path])) {
                    $permId = $permCfg[$path]['id'];
                    if (!in_array($permId, $perms)) {
                        continue;
                    }
                }
                $html .= '<a href="/' . $path . '" class="item ' . $active . '">' . $val . '</a>';
            }
        }

        return $html;
    }
    /**
     * 获取pic下的资源文件
     *
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public function getResource($file)
    {

        return Config::get("resource.resource_host") . "/" . $file . "?" . Config::get("staticfiles.file_version");

    }

    public function webgameRes($appid, $version, $tp)
    {
        $resInfo = ImageHelper::getUrl("webgameimg", ['id' => $appid, 'version' => $version, 'publish' => true]);
        return isset($resInfo[$tp]) ? $resInfo[$tp] : "";
    }
    /**
     * 显示状态
     *
     * @param  [type] $stat [description]
     * @param  string $tp   [description]
     * @return html
     */
    public function showStat($stat, $tp = "text")
    {
        $stats = Config::get("status.dev.webgame_stat");

        $val = $stats[$stat];
        if ($tp == "text") {
            return $val;
        }

        if ($stat >= 5) {
            $html = '<span class="product-status success"><i></i>' . $val . '</span>';
        } else {
            $html = '<span class="product-status fail"><i></i>' . $val . '</span>';
        }
        return $html;
    }

    /**
     * 生成游戏类型选项
     *
     * @param  string $class [description]
     * @return [type]        [description]
     */
    public function showChooseType($tp, $selected = "")
    {
        $cats        = Config::get("category." . $tp);
        $selectedArr = explode(",", $selected);
        $html        = '';
        foreach ($cats as $key => $value) {
            if (in_array($key, $selectedArr)) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $html .= '<option value="' . $key . '" ' . $selected . '>' . $value['name'] . '</option>';
        }
        return $html;
    }

    /**
     * 展示时间
     *
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    public function showDateTime($time)
    {
        if ($time > 0) {
            return date("Y-m-d H:i:s", $time);
        } else {
            return "暂无";
        }

    }

    /**
     * 显示为最新
     *
     * @param  [type] $stat [description]
     * @return [type]       [description]
     */
    public function showIsNew($stat)
    {
        return $stat == 0 ? "否" : "是";
    }

    /**
     * 显示为推荐
     * @param  string $stat [description]
     * @return [type]       [description]
     */
    public function showRecommend($stat = "")
    {
        return $stat == 0 ? "否" : "是";
    }

    /**
     * 显示服务器状态
     *
     * @param  string $stat [description]
     * @return [type]       [description]
     */
    public function showServerSatus($stat = "")
    {
        $stats = array(0 => '<span class="ok">正常</span>', 3 => '<span class="warn">拥挤</span>', 6 => '<span class="warn">繁忙</span>', 9 => '<span class="error">维护</span>');

        if (!isset($stats[$stat])) {
            return $stats[0];
        }
        return $stats[$stat];
    }

    /**
     * 显示游戏类型
     *
     * @param  string $class [description]
     * @return [type]        [description]
     */
    public function showGameType($class = "")
    {
        $arr = array(1 => "角色扮演", 2 => '射击类', 3 => '棋牌类');
        if ($class > count($arr)) {
            $class = 1;
        }
        return $arr[$class];
    }

    /**
     * 创建act
     *
     * @param  [type] $action     [description]
     * @param  [type] $actionList [description]
     * @return [type]             [description]
     */
    public function createAction($action, $actionList)
    {
        if (isset($actionList[$action["id"]])) {
            foreach ($actionList[$action["id"]] as $act) {
                echo "<div style='padding-left:20px;'>";
                echo <<<EOF
                <label class='checkbox'>
                    <input type="checkbox" value="" id="{$act['id']}">{$act["name"]}
                </label>
EOF;

                if (isset($actionList[$act["id"]])) {
                    foreach ($actionList[$act["id"]] as $a) {
                        $this->createAction($a, $actionList);
                    }
                }
                echo "</div>";
            }
        }
    }

    /**
     * 检查路由情况
     *
     * @param  [type] $route_name [description]
     * @return [type]             [description]
     */
    public function checkRoute($route_name)
    {
        if ($this->router->has($route_name)) {
            return route($route_name);
        } else {
            return "javascript:;";
        }
    }

    public function getVideoTags($tagArr)
    {
        $tags = '';
        if (is_array($tagArr) && count($tagArr) > 0) {
            $count = count($tagArr);
            foreach ($tagArr as $k => $v) {
                if ($k < $count - 1) {
                    $tags .= $v . '+';
                } else {
                    $tags .= $v;
                }
            }
        }
        return $tags;
    }

    /**
     * 时间换算到时、分、秒
     *
     * @param  [type] $seconds [description]
     * @return [type]          [description]
     */
    public function time2second($seconds)
    {
        $seconds = (int) $seconds;

        if ($seconds > 3600) {
            if ($seconds > 24 * 3600) {
                $days     = (int) ($seconds / 86400);
                $days_num = $days . "天";
                $seconds  = $seconds % 86400; //取余
            }
            $hours   = intval($seconds / 3600);
            $minutes = $seconds % 3600; //取余下秒数
            $time    = $days_num . $hours . "小时" . gmstrftime('%M分钟%S秒', $minutes);
        } else {
            $time = gmstrftime('%H小时%M分钟%S秒', $seconds);
        }
        return $time;
    }

    /**
     * 时间换算到时、分、秒=>video视频时间换算
     *
     * @param  [type] $seconds [description]
     * @return [type]          [description]
     */
    public function time2secondForVideo($seconds)
    {
        $seconds = (int) $seconds;

        if ($seconds > 3600) {
            if ($seconds > 24 * 3600) {
                $days     = (int) ($seconds / 86400);
                $days_num = $days . ":";
                $seconds  = $seconds % 86400; //取余
            }
            $hours   = intval($seconds / 3600);
            $minutes = $seconds % 3600; //取余下秒数
            $time    = $days_num . $hours . ":" . gmstrftime('%M:%S', $minutes);
        } else {
            $time = gmstrftime('%H:%M:%S', $seconds);
        }
        return $time;
    }

    public function toogleProtocol($t, $v, $is_deal)
    {
        if ($t == 1) {
            return $v == $is_deal ? 'cur' : '';
        } else {
            return $v == $is_deal ? '' : 'style="display:none"';
        }
    }

    public function checkProtocol($is_deal)
    {
        return $is_deal == 1 ? 'checked=true disabled' : '';
    }

    public function websiteTop($current = "home")
    {

        $list = [
            "home"     => "首页",
            // "charge" => "充值中心",
            // "down"     => "下载中心",
            "vronline" => "VR助手",
        ];

        $topHtmt = "";

        foreach ($list as $route => $name) {
            $class = $current == $route ? "sy" : "";
            if ($route == 'home') {
                $topHtmt .= "<a class='{$class}' href='http://www.vronline.com'>{$name}</a>";
            } else {
                $topHtmt .= "<a class='{$class}' href='http://www.vronline.com/{$route}'>{$name}</a>";
            }
        }

        echo $topHtmt;
    }

    /**
     * 推荐位置的内容分类标签
     *
     * @param  [type] $tp [description]
     * @return [type]     [description]
     */
    public function recommendTag($tp)
    {
        if (!$tp || !in_array($tp, ["vrgame", "video", "webgame"])) {
            return false;
        }
        return Config::get($tp . ".tagName", '');
    }

    /**
     * 用户协议
     *
     * @return [type] [description]
     */
    public static function showNewProtocol($type = "input", $info = "")
    {
        $agreement = '<div class="ui main text">
    <form action="" class="ui form" id="subform" method="post">
        <center>
            <h2>VRonline&lt;开发者服务协议&gt;电子合同</h2></center>
        <div class="ui segment" style="border: none"></div>
        <div class="inline field">
            <label>甲方</label>
            [%cpName%#longInput#]
        </div>
        <div class="inline field">
            <label>乙方&nbsp;&nbsp;&nbsp;上海恺英网络科技有限公司</label>
        </div>
        <br>
        <div class="inline fields">
            <div class="inline field">
                <label>本协议于</label>
                [%year%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day%#shortInput#]
            </div>
            <div class="inline field">
                <label>日在上海签订</label>
            </div>
        </div>
        <div class="inline fields">
            <div class="inline field">
                <label>甲方：</label>
                [%cpName%#longInput#]
            </div>
            <div class="inline field">
                <label>地址：</label>
                [%cpAddress%#longInput#]
            </div>
            <div class="inline field">
                <label>联系人：</label>
                [%cpContact%#longInput#]
            </div>
            <div class="inline field">
                <label>电子邮箱：</label>
                [%cpEmail%#longInput#]
            </div>
            <div class="inline field">
                <label>电话：</label>
                [%cpTel%#longInput#]
            </div>
            <div class="inline field">
                <label>邮编：</label>
                [%cpPostcode%#longInput#]
            </div>
        </div>
        <div class="inline fields">
            <div class="inline field">
                <label>乙方：&nbsp;&nbsp;&nbsp;上海恺英网络科技有限公司</label>
            </div>
            <div class="inline field">
                <label>联系人：孟想</label>
            </div>
            <div class="inline field">
                <label>电子邮箱：mengx@kingnet.com</label>
            </div>
            <div class="inline field">
                <label>电话：021-54310366-8065</label>
            </div>
            <div class="inline field">
                <label>邮编：201114</label>
            </div>
        </div>
        <h4>鉴于：</h4>
        <p>
            1、甲方是依据中国法律设立并有效续存的公司，是互联网内容和应用服务开发商，拥有先进的网络游戏产品技术和维护技术和实践经验。
        </p>
        <p>
            2、乙方是依据中国法律设立并有效续存的公司，对框架协议内VRonline拥有著作权，拥有完善的网络信息服务及发行渠道，并在网络游戏研发和运营方面有丰富的实践经验。
        </p>
        <p>
            3、甲乙双方同意利用其各自的技术和信息优势，在本协议约定期间及区域内，就合作在乙方VRONLINE上开展运营合作。
        </p>
        <p>
            4、经友好协商，在符合法律规定的前提下，双方就游戏标的物通过乙方VRonline向网络用户进行商业化运营并依据本协议合理分配运营收益等事宜，经充分协商，一致达成以下条款。。
        </p>
        <h4>第一条 定义条款</h4>
        <p>
            除非本协议另有约定，否则任何一方均应按照本条对相关用语作出唯一、确定的解释。
        </p>
        <p>
            <table class="ui table">
                <tr>
                    <td style="width: 10%">游戏标的物</td>
                    <td>VRonline客户端和游戏软件及其后续的更新、升级版本，具体游戏由双方以补充协议方式确定。</td>
                </tr>
                <tr>
                    <td class="tdTitle">运营服务</td>
                    <td>指乙方通过其VRonline的市场推广服务，吸引网络用户进入乙方VRonline，并在乙方运营管理的平台上接受游戏服务并充值，乙方自行通过运营平台对游戏标的物进行商业化运营，并向网络用户提供游戏标的物的服务与支持的行为，包括但不限于：安装和运行使用游戏标的物的服务器端软件包，并授权网络用户安装、运行和使用游戏标的物，向网络用户提供客户服务和技术支持，发行并销售游戏相关产品与服务等。</td>
                </tr>
                <tr>
                    <td class="tdTitle">运营平台</td>
                    <td>指由乙方设立的【VRonline】的游戏标的物运营平台。</td>
                </tr>
                <tr>
                    <td class="tdTitle">运营区域</td>
                    <td>中华人民共和国境内，港澳台地区除外。</td>
                </tr>
                <tr>
                    <td class="tdTitle">虚拟货币</td>
                    <td>指游戏标的物内的通用虚拟货币，人民币（元）与通用虚拟货币（个）的兑换比例由本框架协议之补充协议约定。若游戏中出现充值赠送行为，以双方约定的具体比例为准。</td>
                </tr>
                <tr>
                    <td class="tdTitle">硬件资源</td>
                    <td>指为运营游戏标的物而投入的包括但不限于服务器组与IDC资源组成的计算机硬件组。服务器组指由甲方提供游戏服务所必需的所有硬件设备和带宽资源，包括安装架设游戏服务器、数据库服务器、下载服务器、网络服务器和其他运营所需服务器，并将服务器置于适合提供游戏标的物运营服务的在线网络环境之中。</td>
                </tr>
                <tr>
                    <td class="tdTitle">IDC资源</td>
                    <td>指确保服务器组提供正常服务的软硬件资源，包括但不限于接入互联网所需的IP地址、带宽、服务器托管机柜等。</td>
                </tr>
                <tr>
                    <td class="tdTitle">网络用户</td>
                    <td>指任何通过运营平台注册、登录游戏标的物的互联网用户，网络用户在游戏标的物中充值并进行有效消费后可被定义为消费用户。充值方式包括但不限于：网银、支付宝、易宝、神州行、联通卡、电信卡、应用内计费、声讯充值等方式。</td>
                </tr>
                <tr>
                    <td class="tdTitle">运营收入</td>
                    <td>指通过乙方运营平台及推广/渠道引入的网络用户，在运营平台对游戏标的物进行充值所获得的来源于网络用户的人民币收入。</td>
                </tr>
                <tr>
                    <td class="tdTitle">知识产权</td>
                    <td>指按照中国《著作权法》、《商标法》和《反不正当竞争法》及其他相关法律、法规或规章规定的，与著作权、商标权、域名及商业秘密等有关的一切体现智力成果的权益。</td>
                </tr>
                <tr>
                    <td class="tdTitle">用户数据</td>
                    <td>指网络用户因注册、接受游戏标的物的运营服务而产生、持续、更新的数据, 包括但不限于游戏标的物内人物角色的外貌(脸形/身体等) 和特征(级别/经验值等), 物品箱以及其它任何与终端用户相关的数据；以及所有消费用户的付费信息, 包括真实姓名、身份证号、信用卡、地址、固定电话、手机号码、电子邮件或者其它必要身份信息。</td>
                </tr>
            </table>
        </p>
        <h4> 第二条 合作条款</h4>
        <p>
            2.1 就游戏标的物，甲方授权乙方在授权区域与授权期限内拥有有偿的、可撤销、非独占、不可转授权或分授权的以合作为目的的运营和市场推广权利。未经甲方书面授权，乙方不得擅自向第三方进行游戏标的物的复制、发行以及信息网络传播行为等一切提供游戏标的物原件或者复制件的行为。
        </p>
        <p>
            2.2 甲方在对游戏标的物及其衍生产品进行重大修改及发布时，应提前告知乙方，方便乙方展开运营相关的宣传与推广活动。
        </p>
        <p>
            2.3 乙方负责为游戏标的物提供宣传、推广等支持与服务，双方共同在VRonline上建设专属于游戏标的物的游戏专区。
        </p>
        <p>
            2.4 甲方负责提供和维护游戏标的物运营所需的硬件资源，硬件资源的所有权与使用权均归属于甲方。甲方拥有游戏标的物的知识产权并对此承担责任。
        </p>
        <p>
            2.5 甲方负责投入硬件资源，并保证游戏标的物的正常运营。
        </p>
        <p>
            2.6 乙方负责建立人民币和游戏标的物虚拟货币的兑换计费接口，以方便网络用户完成游戏账号的人民币充值，实现人民币对虚拟货币的兑换。人民币与虚拟货币的兑换比例，均依据本框架协议之补充协议予以确定；上述兑换比例若需修改，须经双方协商一致后以书面形式予以变更。兑换计费接口完成后，乙方根据VRonline用户真实充值记录向甲方发出虚拟货币兑换指令；甲方根据乙方指令与虚拟货币兑换记录向网络用户发放虚拟货币。双方均能够通过后台计费系统查询网络用户的充值与兑换信息。用户充值后不能成功兑换相应的虚拟货币的，由甲方负责处理。
        </p>
        <p>
            2.7 甲方应在服务器安装完毕同时，向乙方提供必要接口以供乙方通过该接口查询游戏标的物的在线人数、角色属性、兑换信息、消费信息等，但乙方不得擅自增删、变动上述信息；若上述信息已经甲方加密处理，甲方应当向乙方提供查询通道，乙方对甲方提供的上述信息承担保密义务。
        </p>
        <p>
            2.8 甲方授权乙方在本协议约定范围内为推广、宣传游戏标的物而合理使用甲方公司商号、商标及游戏标的物的相关权利。乙方不得将甲方公司商号、商标擅自授权第三方，否则视为侵权。
        </p>
        <p>
            2.9 未经甲方事先书面同意，乙方不得擅自登记与甲方商号、企业标识、商标或者游戏标的物元素（包括但不限于游戏标的物的名称、地图、角色或物品、原画等）相同或者类似的任何知识产权。
        </p>
        <p>
            2.10 甲方保证合法拥有游戏标的物的全部知识产权。涉及游戏标的物的任何侵权行为及知识产权纠纷，均与乙方无关。由此给乙方造成的一切损失由甲方负责赔偿。
        </p>
        <p>
            2.11 甲方承诺办理并且取得运营标的物游戏所涉及的所有政府审批、许可、备案和注册登记包括但不限于游戏出版备案、游戏运营备案。
        </p>
        <p>
            2.12 双方应当在本协议履行期内及终止后壹年内妥善保存本协议全部合作记录与信息，无论上述记录与信息以何种形式存在。
        </p>
        <h4>  第三条 权利义务条款</h4>
        <h5>3.1 甲方的权利和义务：</h5>
        <p>
            3.1.1 甲方承诺拥有游戏标的物及其衍生产品在合作期限与授权区域内的合法权利（包括但不限于知识产权）及在中国境内运营网络游戏的合法资质，本协议签订时，甲方应同时向乙方提供其拥有游戏标的物的权利登记证明文件或获得授权的证明文件（提供加盖甲方公章的复印件）。如因甲方对游戏标的物的权利存在瑕疵，造成乙方、游戏用户或其他第三方损失的，甲方承担全部的赔偿责任。
        </p>
        <p>
            3.1.2本协议履行过程中，如遇第三方提出权利异议的情形，甲方应在收到乙方通知后24小时内作出处理并及时告知乙方采取相应的措施。
        </p>
        <p>
            3.1.3甲方拥有硬件资源最高且唯一的管理操作权限以及基于上述权限的硬件资源管理操作权限的分配权。
        </p>
        <p>
            3.1.4甲方拥有对游戏标的物的最终决定权与最终修改权，若对游戏标的物的重大修改，甲方应提前十五个工作日以书面形式通知乙方，以保证游戏标的物在运营平台的正常运营。&nbsp;
        </p>
        <p>
            3.1.5甲方就游戏标的物提供技术支持和技术服务（包括游戏标的物的版本更新、升级），应及时在运营平台就变更内容通过公开渠道对网络用户进行公开、明确的公告与说明。
        </p>
        <p>
            3.1.6在运营期间，甲方应提供给乙方游戏标的物相关的技术接口、计费接口、查询工具接口与相应查询权限，以保证乙方进行相关的运营活动。
        </p>
        <p>
            3.1.7 甲方须配合乙方处理游戏标的物的常见问题、游戏基本规则及虚拟道具兑换问题引起的用户咨询和投诉，解决客户在游戏中遇到的问题，提供问题解答。
        </p>
        <h5>3.2 乙方的权利和义务&nbsp;</h5>
        <p>
            3.2.1 乙方承诺对运营平台拥有唯一的管理与运营资格，乙方具备优秀的市场宣传推广能力及人员储备以履行本协议。
        </p>
        <p>
            3.2.2 乙方应当依据本协议对游戏标的物进行市场宣传与推广。乙方有权出于实现本合同约定目的将游戏标的物的文本、图片用于与游戏标的物运营具有实质性关联的广告资料、促销资料上，但甲方指定的保密信息或尚未进入公共领域的信息除外。
        </p>
        <p>
            3.2.3乙方应在授权范围内使用甲方公司商号、商标及游戏标的物名称及内容等信息。在市场宣传与推广中，乙方不得对甲方的商号、商标、游戏标的物名称及内容等信息进行任何形式的改动。
        </p>
        <p>
            3.2.4 乙方应根据自身的渠道和其他市场资源积极对游戏标的物的运营进行积极、正面、良好的市场宣传与推广活动。
        </p>
        <p>
            3.2.5 乙方应根据排期将游戏标的物的宣传方式、宣传渠道及宣传资料制作等相关信息提供给甲方，甲方应提供必要配合与支持。
        </p>
        <p>
            3.2.6 乙方拥有VRonline的全部用户数据的使用及维护权。
        </p>
        <p>
            3.2.7 如遇第三方向乙方就游戏标的物的知识产权提出异议，要求乙方在VE助手上删除游戏标的物或者断开用户下载链接或提出其他权利主张的，乙方应当及时通知甲方；但乙方有权先行对第三方提供的材料进行初步审查并根据自己的判断决定采取相应的删除、下架或断开连接等措施，除非甲方在接到乙方通知后24小时内提出反证或者权利保证声明且认为第三方的主张明显不合理。
        </p>
        <p>
            3.2.8 本协议履行过程中，乙方接到行政部门、司法部门等发出的有效法律文书或其他文件等对游戏标的物及其权利提出疑问或要求，乙方将予以配合并执行行政部门或司法部门的决定。
        </p>
        <h4> 第四条 运营收入分配条款</h4>
        <p>
            4.1 双方确认，对游戏标的物在乙方VRonline上产生的运营收入按照本条款进行结算与分配，运营收入依据乙方的计费系统最终确认。
        </p>
        <p>
            4.2 游戏标的物在VRonline的正式商业化运营期间，双方运营收入分配比例由本框架协议之补充协议约定。
        </p>
        <p>
            4.3若双方就运营收入的统计数据存在异议，则按照以下方式解决：若双方统计数据误差不超过0.5%，则以乙方的统计数据进行结算；若双方统计数据误差超过0.5%，则双方应重新核对当月统计数据，并在十个工作日内予以解决。统计数据误差导致当月结算停滞的，不影响之前或之后无误差月的结算与分配。
        </p>
        <p>
            4.4 结算方式：双方按自然月（自每月首日0时起至该月末日24时）结算。
        </p>
        <p>
            乙方应当于每月第五个工作日前向甲方提供上月运营收入对账单，甲方在收到对账单后五个工作日内予以核对。甲方在收到对账单后五个工作日内未提出异议的，即视为甲方确认对账单无误。甲方确认对账单后应向乙方开具正规发票，乙方自收到甲方发票后的十个工作日内，将甲方应分收益支付到本协议第4.6条规定的甲方指定账户。
        </p>
        <p>
            4.5当某自然月甲方收益不足人民币1000元时，乙方有权将甲方该月收益自动延期到下个自然月累计结算，合同履行期间内的最后一个自然月收益除外。
        </p>
        <div class="inline fields">
            <div class="inline field">
                <label>4.6 甲方指定账户</label>
                [%cpAccount%#longInput#]
            </div>
            <div class="inline field">
                <label>户名：</label>
                [%cpAccountName%#longInput#]
            </div>
            <div class="inline field">
                <label>开户行：</label>
                [%cpAccountBank%#longInput#]
            </div>
            <div class="inline field">
                <label>账号：</label>
                [%cpAccountNum%#longInput#]
            </div>
        </div>
        <h4>第五条 客户服务</h4>
        <p>
            5.1 甲方应就游戏标的物提供客户服务。甲方应向网络用户公开提供具体、明确的客户服务方案，负责承担游戏标的物的常见问题解答，受理游戏基本规则及虚拟道具兑换问题引起的用户咨询和投诉，解决客户在游戏中遇到的问题。甲方在其宣传资料和运营平台专区明确标注客户服务热线和客户服务办法。
        </p>
        <p>
            5.2 甲方负责提供游戏标的物相关资料和游戏标的物运营常见问题的处理方式，在游戏标的物通过运营平台的运营期间，应对乙方客服人员进行必要的客服培训。在乙方客服人员掌握相关技能后可直接向用户提供客户服务。
        </p>
        <p>
            5.3 乙方负责承担运营平台产生的问题，并在其宣传资料和网站的突出位置标明其客户服务热线和客户服务办法等内容。
        </p>
        <h4>第六条 保密条款</h4>
        <p>
            6.1 任何一方通过订立、履行、终止本协议所获得的另一方信息，应视为专属于另一方的保密信息与商业秘密。未经另一方书面明确同意，信息持有方不得向任何第三方进行披露、使用，但本协议第6.2条规定的除外。
        </p>
        <p>
            6.2 在下列情形下，任何一方可以依法对保密信息与商业秘密做出必要、合理的披露：
        </p>
        <p>
            6.2.1 法律明文规定或者行政机关、司法机关明确要求；
        </p>
        <p>
            6.2.2保密信息与商业秘密已经进入公共知识领域；
        </p>
        <p>
            6.2.3另一方书面明确同意。
        </p>
        <p>
            6.3 双方的保密义务不受本协议期限的限制。
        </p>
        <h4>第七条 终止运营条款</h4>
        <p>
            7.1 无论游戏标的物因何种原因导致停止在运营平台上进行商业化运营，双方均应积极完成终止运营前后的各项义务。
        </p>
        <p>
            7.2 甲方应提前六十天在运营平台专区首页显著位置向网络用户公告终止运营的通知，通知内容应至少包括终止运营时间、终止运营前用户持有的虚拟货币处理方式等。
        </p>
        <p>
            7.3甲方向网络用户正式发出终止运营通知后，在与乙方商定的时间内关闭支付渠道，网络用户将无法通过运营平台进行充值。甲乙双方共同解决因终止运营产生的用户投诉等事宜，共同就向网络用户已经充值的货币提供处理方式和渠道，以确保网络用户权益。但因甲方的游戏标的物停止运营产生的一切责任由甲方最终承担。
        </p>
        <p>
            7.4 自乙方运营服务正式终止之日起，任何一方均应停止使用包含另一方LOGO、商标、商号、游戏名称、游戏关键字等含有知识产权的资料、素材与信息，否则视为侵权。
        </p>
        <p>
            7.5 运营终止前用户已充值且已完成虚拟道具兑换的，因此实际产生的运营收入应按本协议相关条款予以分配；运营终止前用户已充值但未完成虚拟道具兑换的，不得作为运营收入分配，由乙方负责妥善解决向用户退款问题。
        </p>
        <h4>第八条 争端解决条款</h4>
        <p>
            8.1 本协议的签订、履行和解释适用中华人民共和国法律。
        </p>
        <p>
            8.2 凡因本协议之订立、履行、终止所引起的或与本协议相关的任何争端，各方应首先友好协商解决；协商不成，任何一方有权向原告方所在地人民法院提起诉讼。。
        </p>
        <h4>第九条 文件送达</h4>
        <p>
            9.1 在本协议履行过程中所有向对方发送的的通知、意见、文件或其他意思表示（统称文件）均需按本协议首页所载联系地址和方式送达，除非收件方已以书面方式提前通知发送方变更联系地址和方式。
        </p>
        <p>
            9.2 任何符合9.1条发出的文件，按下列方式应视为送达：
        </p>
        <p>
            9.2.1专人送达的，以收件方签收日为送达日；收件方拒不签收的，则收件方拒不签收日视为送达日。
        </p>
        <p>
            9.2.2 经电子传送确认的传真、电子邮件发送的，以成功发出传真、电子邮件日为送达日。
        </p>
        <p>
            9.2.3 通过已付费的国内普遍认可的快递服务发送的，以收件方签收日但最迟以发送方交邮之日起第3个工作日为送达日。
        </p>
        <p>
            9.2.4因收件方联系方式无效、不畅通而被退回或送达失败，则文件被退回或送达失败之日视为送达日。
        </p>
        <p>
            9.3 收件方任何人员（包括但不限于行政人员、业务人员、前台、门卫、保安等）对送达文件的签收视为收件方的有效签收。
        </p>
        <h4>第十条 反商业贿赂保证</h4>
        <p>
            10.1 商业贿赂是指为获取与对方的合作及合作的利益，一方工作人员给予另一方工作人员（包括工作人员之家属、朋友或有直接或间接关系人士）的一切精神及物质上直接或间接的馈赠，包括但不限于回扣、娱乐、旅游、吃请等。甲乙双方除严格遵守《中华人民共和国反不正当竞争法》、《刑法》等有关禁止商业贿赂行为规定外，还应坚决拒绝商业贿赂、行贿及其他不正当之商业行为的馈赠。
        </p>
        <p>
            10.2双方应对对方员工持尊重、公平和诚恳的态度，任何情况下，都不得利用职务之便借故刁难一方任何部门、员工，不得以任何名义向另一方索取或收受金钱、物品及任何形式的馈赠；如有该行为，被索贿方及员工应向另一方举报并提供证据；如一方明知被索取合同金额以外的财务或利益而不予举报或告知的，视为被索贿方严重违约。
        </p>
        <p>
            10.3 若一方违反本规定，贿赂另一方任何员工，以图获取任何不正当商业利益或更特殊的商业待遇或不配合另一方查处其员工的受贿行为的，守约方有权单方面解除合同、停止双方间一切合作，并要求违约方支付10万元人民币或者所涉订单（合同）金额的30%作为违约金（两者以高者为准）。
        </p>
        <h4> 第十一条 协议其他条款</h4> 1.1 本协议自首页载明的双方签署之日生效，
        <div class="inline fields">
            <div class="inline field">
                <label>自</label>
                [%year1%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month1%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day1%#shortInput#]
            </div>
            <div class="inline field">
                <label>日 至</label>
                [%year2%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month2%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day2%#shortInput#]
            </div>
            <div class="inline field">
                <label>日。</label>
            </div>
        </div>
        如本协议期限届满，双方有意继续合作，应提前30日向对方提出续签意向，双方协商重新签订书面协议；否则本协议到期自动终止。新框架协议签订后，双方应按新协议约定的权利义务确定各款合作游戏并签订补充协议。
        <p>
            11.2若在本框架协议有效期内签订的补充协议约定的某款游戏运营期限超过了本框架协议的有效期限，本框架协议的全部条款仍适用于尚未到期的补充协议，直至补充协议期满。
        </p>
        <p>
            11.3协议由主协议组成，一式两份，双方各执一份，具有同等法律效力。
        </p>
        <p>
            11.4 本协议构成双方之间就游戏标的物通过运营平台进行运营所达成的全部真实意思表示，并且取代任何一方或双方之间在本协议生效前所有口头或书面形式的声明、备忘录、合同、协议、承诺、保证等文件。除非以书面形式签署并经盖章确认，否则对本协议的任何变更或废止均对双方不具有法律约束力。本协议项下所有权利和义务不可全部或者部分转让给第三方。
        </p>
        <p> 兹确认，本协议由以下双方具有合法授权人士在本协议载明之日盖章生效。
        </p>
        <p class="strong">
            甲方： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 乙方： 上海恺英网络科技有限公司
        </p>
        <p>
            （盖章） &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; （盖章）
        </p>
        <p>
            &nbsp;授权代表（签字）： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 授权代表（签字）：
        </p>
        <p>
            <br/>
        </p>
        <p>
            &nbsp;日期： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;日期：
        </p>
        <br>
        <br>
        <div class="inline fields">
            <div class="inline field">
                <label>VRonline开发者协议之补充协议</label>
            </div>
            <div class="inline field">
                <label>-- 游戏《</label>
                [%gamename%#longInput#] &nbsp;&nbsp;》
            </div>
        </div>
        <div class="inline fields">
            <div class="inline field">
                <label>甲方：</label>
                [%cpName%#longInput#]
            </div>
            <div class="inline field">
                <label>地址：</label>
                [%cpAddress%#longInput#]
            </div>
        </div>
        <p>
            乙方：上海恺英网络科技有限公司&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;地址：上海陈行路2388号9号楼10楼&nbsp;
        </p>
        <div class="inline fields">
            <div class="inline field">
                <label> 鉴于甲乙双方于</label>
                [%year3%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month3%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day3%#shortInput#]
            </div>
            <div class="inline field">
                <label>日</label>
            </div>
        </div>
        <div class="inline field">
            签署了《VRonline开发者协议》，约定甲方授权乙方非独家运营甲方独家代理的手机游戏。现甲、乙双方经友好协商，特就特就《 [%gamename%#longInput#]》 游戏运营服务事宜于
        </div>
        <div class="inline fields">
            <div class="inline field">
                [%year4%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month4%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day4%#shortInput#]
            </div>
            <div class="inline field">
                <label>日订立以下补充协议：</label>
            </div>
        </div>
        <p>
            一、合作游戏信息如下：
        </p>
        <div class="inline field">
            <label> 游戏名称：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            [%gamename%#longInput#]
        </div>
        <div class="inline field">
            <label> 著作权证书登记号：</label>
            [%rightnum%#longInput#]
        </div>
        <p>
            二、游戏合作平台为【VRonline】
        </p>
        <div class="inline fields">
            <div class="inline field">
                <label>三、游戏虚拟货币即 </label>
                [%gameb%#shortInput#]
            </div>
            <div class="inline field">
                <label>，人民币与 </label>
                [%gameb%#shortInput#]
            </div>
            <div class="inline field">
                <label>的兑换比例为1:</label>
                [%rate%#shortInput#]
            </div>
            <div class="inline field">
                <label>，即1元人民币兑换</label>
                [%rate%#shortInput#]
            </div>
            <div class="inline field">
                <label>个游戏</label>
                [%gameb%#shortInput#]
            </div>
        </div>
        <p>
            四、游戏分成比例：
        </p>
        <p>
            <table class="ui table" style="width:50%">
                <tr>
                    <td rowspan="2" style="text-align: center;">授权游戏当月运营收入
                        <br>(扣除渠道手续费)</td>
                    <td>分成比例（甲方）</td>
                    <td>分成比例（乙方）</td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <div class="inline field">
                            [%ratea%#shortInput#]%</div>
                    </td>
                    <td style="text-align: center;">
                        <div class="inline field">
                            [%rateb%#shortInput#]%</div>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">渠道手续费</td>
                    <td colspan="2" style="text-align: center;">
                        <div class="inline field">
                            [%fee%#shortInput#]%</div>
                    </td>
                </tr>
            </table>
        </p>
        <p>
            五、游戏的运营服务事项按框架协议执行，运营收入分成按照框架协议的约定进行结算支付。
        </p>
        <div class="inline fields">
            <div class="inline field">
                <label>六、游戏合作期限：自</label>
                [%year5%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month5%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day5%#shortInput#]
            </div>
            <div class="inline field">
                <label>日 至</label>
                [%year6%#shortInput#]
            </div>
            <div class="inline field">
                <label>年</label>
                [%month6%#shortInput#]
            </div>
            <div class="inline field">
                <label>月</label>
                [%day6%#shortInput#]
            </div>
            <div class="inline field">
                <label>日订立以下补充协议</label>
            </div>
        </div>
        <p>
            七、本补充协议的所有用词与定义均与框架协议一致，未尽事宜按框架协议的约定执行。框架协议与本补充协议不一致的，以本补充协议约定为准。
        </p>
        <p>
            八、本补充协议一式二份，甲、乙双方各执一份，自双方签字盖章后生效。
        </p>
        <p class="strong">
            <strong>甲方：</strong> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>乙方： 上海恺英网络科技有限公司</strong>
        </p>
        <p>
            （盖章） &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; （盖章）
        </p>
        <p>
            &nbsp;授权代表（签字）： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;授权代表（签字）：
        </p>
        <p>
            &nbsp;日期： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;日期：
        </p>
        <div class="ui segment" style="border: none"></div>
        [%footer%]
    </form>
</div>
';

        if ($type == "input") {
            $pattern = "/\[%([^\]]+)%#([^\]]+)#\]/";
            $replace = "<input type='text' value='' name='$1' class='$2'>";
            $ret     = preg_replace($pattern, $replace, $agreement);
            $ret     = str_replace('[%footer%]', ' <center><div class="ui checkbox"><input type="checkbox" name="cp_deal" value="1" ><label>同意以上所有协议</label></div><br /><br /><div class="ui button action-back">返回</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="ui primary button action-save">确定</div></center>', $ret);
        } else {
            $replace  = $search  = array();
            $protocal = json_decode($info, true);
            if ($protocal && is_array($protocal)) {
                foreach ($protocal as $key => $val) {
                    $search[]  = "[%{$key}%#shortInput#]";
                    $search[]  = "[%{$key}%#longInput#]";
                    $replace[] = "<b><u>{$val}</u></b>";
                    $replace[] = "<b><u>{$val}</u></b>";
                }
            }
            $ret     = str_replace($search, $replace, $agreement);
            $pattern = "/\[%([^\]]+)%#([^\]]+)#\]/";
            $replace = "";
            $ret     = preg_replace($pattern, $replace, $ret);
            $ret     = str_replace('[%footer%]', ' <center><div class="ui checkbox"><input type="checkbox" name="cp_deal" value="1" checked=true disabled=true><label>同意以上所有协议</label></div><br /><br />', $ret);
        }

        return $ret;
    }

    /**
     * 用户协议
     *
     * @return [type] [description]
     */
    public static function showProtocol($type = "input", $info = "")
    {
        $agreement = '<div style="margin: 10px 10%;">
                    <form action="" class="whole" id="subform" method="post">
                          <div class="header">
                            <h3>VRonline</h3>
                            <h3><开发者服务协议>电子合同</h3>
                          </div>
                          <br>
                          <br>
                          <div class="title">
                          <h3>
                            <table class="table">
                              <tr>
                                <td>甲方 :</td>
                                <td><span class="inputMsg">[%cpName%#longInput#]</span></td>
                              </tr>
                              <tr>
                                <td>乙方 :</td>
                                <td>上海恺英网络科技有限公司</td>
                              </tr>
                            </table>
                          </h3>
                          <br>
                          <p>本协议于[%year%#shortInput#]年[%month%#shortInput#]月[%day%#shortInput#]日在上海签订</p>
                                <table class="table">
                                  <tr>
                                    <td>甲方：</td>
                                    <td>[%cpName%#longInput#]</td>
                                  </tr>
                                  <tr>
                                    <td>地址：</td>
                                    <td>[%cpAddress%#longInput#]</td>
                                  </tr>
                                  <tr>
                                    <td>联系人：</td>
                                    <td>[%cpContact%#longInput#]</td>
                                  </tr>
                                  <tr>
                                    <td>电子邮箱：</td>
                                    <td>[%cpEmail%#longInput#]</td>
                                  </tr>
                                  <tr>
                                    <td>电话：</td>
                                    <td>[%cpTel%#longInput#]</td>
                                  </tr>
                                  <tr>
                                    <td>邮编：</td>
                                    <td>[%cpPostcode%#longInput#]</td>
                                  </tr>
                                  <tr>
                                    <td>乙方：</td>
                                    <td>上海恺英网络科技有限公司</td>
                                  </tr>
                                  <tr>
                                    <td>地址：</td>
                                    <td>上海市闵行区陈行公路2388号9号楼10楼</td>
                                  </tr>
                                  <tr>
                                    <td>联系人：</td>
                                    <td>孟想</td>
                                  </tr>
                                  <tr>
                                    <td>电子邮箱：</td>
                                    <td>mengx@kingnet.com</td>
                                  </tr>
                                  <tr>
                                    <td>电话：</td>
                                    <td>021-54310366-8065</td>
                                  </tr>
                                  <tr>
                                    <td>邮编：</td>
                                    <td>201114</td>
                                  </tr>
                                </table>
                              </div>
                            <p class="strong">鉴于：</p>
                          <p>
                              1、甲方是依据中国法律设立并有效续存的公司，是互联网内容和应用服务开发商，拥有先进的网络游戏产品技术和维护技术和实践经验。
                          </p>
                          <p>
                              2、乙方是依据中国法律设立并有效续存的公司，对框架协议内VRonline拥有著作权，拥有完善的网络信息服务及发行渠道，并在网络游戏研发和运营方面有丰富的实践经验。
                          </p>
                          <p>
                              3、甲乙双方同意利用其各自的技术和信息优势，在本协议约定期间及区域内，就合作在乙方VRONLINE上开展运营合作。
                          </p>
                          <p>
                              4、经友好协商，在符合法律规定的前提下，双方就游戏标的物通过乙方VRonline向网络用户进行商业化运营并依据本协议合理分配运营收益等事宜，经充分协商，一致达成以下条款。。
                          </p>
                          <p class="strong">
                              第一条 定义条款
                          </p>
                          <p>
                              除非本协议另有约定，否则任何一方均应按照本条对相关用语作出唯一、确定的解释。
                          </p>
                          <p>
                            <table border="1" cellpadding="0" cellspacing="0" class="table">
                              <tr>
                                <td class="tdTitle">游戏标的物</td>
                                <td>VRonline客户端和游戏软件及其后续的更新、升级版本，具体游戏由双方以补充协议方式确定。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">运营服务</td>
                                <td>指乙方通过其VRonline的市场推广服务，吸引网络用户进入乙方VRonline，并在乙方运营管理的平台上接受游戏服务并充值，乙方自行通过运营平台对游戏标的物进行商业化运营，并向网络用户提供游戏标的物的服务与支持的行为，包括但不限于：安装和运行使用游戏标的物的服务器端软件包，并授权网络用户安装、运行和使用游戏标的物，向网络用户提供客户服务和技术支持，发行并销售游戏相关产品与服务等。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">运营平台</td>
                                <td>指由乙方设立的【VRonline】的游戏标的物运营平台。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">运营区域</td>
                                <td>中华人民共和国境内，港澳台地区除外。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">虚拟货币</td>
                                <td>指游戏标的物内的通用虚拟货币，人民币（元）与通用虚拟货币（个）的兑换比例由本框架协议之补充协议约定。若游戏中出现充值赠送行为，以双方约定的具体比例为准。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">硬件资源</td>
                                <td>指为运营游戏标的物而投入的包括但不限于服务器组与IDC资源组成的计算机硬件组。服务器组指由甲方提供游戏服务所必需的所有硬件设备和带宽资源，包括安装架设游戏服务器、数据库服务器、下载服务器、网络服务器和其他运营所需服务器，并将服务器置于适合提供游戏标的物运营服务的在线网络环境之中。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">IDC资源</td>
                                <td>指确保服务器组提供正常服务的软硬件资源，包括但不限于接入互联网所需的IP地址、带宽、服务器托管机柜等。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">网络用户</td>
                                <td>指任何通过运营平台注册、登录游戏标的物的互联网用户，网络用户在游戏标的物中充值并进行有效消费后可被定义为消费用户。充值方式包括但不限于：网银、支付宝、易宝、神州行、联通卡、电信卡、应用内计费、声讯充值等方式。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">运营收入</td>
                                <td>指通过乙方运营平台及推广/渠道引入的网络用户，在运营平台对游戏标的物进行充值所获得的来源于网络用户的人民币收入。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">知识产权</td>
                                <td>指按照中国《著作权法》、《商标法》和《反不正当竞争法》及其他相关法律、法规或规章规定的，与著作权、商标权、域名及商业秘密等有关的一切体现智力成果的权益。</td>
                              </tr>
                              <tr>
                                <td class="tdTitle">用户数据</td>
                                <td>指网络用户因注册、接受游戏标的物的运营服务而产生、持续、更新的数据, 包括但不限于游戏标的物内人物角色的外貌(脸形/身体等) 和特征(级别/经验值等), 物品箱以及其它任何与终端用户相关的数据；以及所有消费用户的付费信息, 包括真实姓名、身份证号、信用卡、地址、固定电话、手机号码、电子邮件或者其它必要身份信息。</td>
                              </tr>
                            </table>
                          </p>
                          <p class="strong">
                              第二条 合作条款
                          </p>
                          <p>
                              2.1 就游戏标的物，甲方授权乙方在授权区域与授权期限内拥有有偿的、可撤销、非独占、不可转授权或分授权的以合作为目的的运营和市场推广权利。未经甲方书面授权，乙方不得擅自向第三方进行游戏标的物的复制、发行以及信息网络传播行为等一切提供游戏标的物原件或者复制件的行为。
                          </p>
                          <p>
                              2.2 甲方在对游戏标的物及其衍生产品进行重大修改及发布时，应提前告知乙方，方便乙方展开运营相关的宣传与推广活动。
                          </p>
                          <p>
                              2.3 乙方负责为游戏标的物提供宣传、推广等支持与服务，双方共同在VRonline上建设专属于游戏标的物的游戏专区。
                          </p>
                          <p>
                              2.4 甲方负责提供和维护游戏标的物运营所需的硬件资源，硬件资源的所有权与使用权均归属于甲方。甲方拥有游戏标的物的知识产权并对此承担责任。
                          </p>
                          <p>
                              2.5 甲方负责投入硬件资源，并保证游戏标的物的正常运营。
                          </p>
                          <p>
                              2.6 乙方负责建立人民币和游戏标的物虚拟货币的兑换计费接口，以方便网络用户完成游戏账号的人民币充值，实现人民币对虚拟货币的兑换。人民币与虚拟货币的兑换比例，均依据本框架协议之补充协议予以确定；上述兑换比例若需修改，须经双方协商一致后以书面形式予以变更。兑换计费接口完成后，乙方根据VRonline用户真实充值记录向甲方发出虚拟货币兑换指令；甲方根据乙方指令与虚拟货币兑换记录向网络用户发放虚拟货币。双方均能够通过后台计费系统查询网络用户的充值与兑换信息。用户充值后不能成功兑换相应的虚拟货币的，由甲方负责处理。
                          </p>
                          <p>
                              2.7 甲方应在服务器安装完毕同时，向乙方提供必要接口以供乙方通过该接口查询游戏标的物的在线人数、角色属性、兑换信息、消费信息等，但乙方不得擅自增删、变动上述信息；若上述信息已经甲方加密处理，甲方应当向乙方提供查询通道，乙方对甲方提供的上述信息承担保密义务。
                          </p>
                          <p>
                              2.8 甲方授权乙方在本协议约定范围内为推广、宣传游戏标的物而合理使用甲方公司商号、商标及游戏标的物的相关权利。乙方不得将甲方公司商号、商标擅自授权第三方，否则视为侵权。
                          </p>
                          <p>
                              2.9 未经甲方事先书面同意，乙方不得擅自登记与甲方商号、企业标识、商标或者游戏标的物元素（包括但不限于游戏标的物的名称、地图、角色或物品、原画等）相同或者类似的任何知识产权。
                          </p>
                          <p>
                              2.10 甲方保证合法拥有游戏标的物的全部知识产权。涉及游戏标的物的任何侵权行为及知识产权纠纷，均与乙方无关。由此给乙方造成的一切损失由甲方负责赔偿。
                          </p>
                          <p>
                              2.11 甲方承诺办理并且取得运营标的物游戏所涉及的所有政府审批、许可、备案和注册登记包括但不限于游戏出版备案、游戏运营备案。
                          </p>
                          <p>
                              2.12 双方应当在本协议履行期内及终止后壹年内妥善保存本协议全部合作记录与信息，无论上述记录与信息以何种形式存在。
                          </p>
                          <p class="strong">
                              第三条 权利义务条款
                          </p>
                          <p >
                              <span class="strong">3.1</span> 甲方的权利和义务：
                          </p>
                          <p>
                              3.1.1 甲方承诺拥有游戏标的物及其衍生产品在合作期限与授权区域内的合法权利（包括但不限于知识产权）及在中国境内运营网络游戏的合法资质，本协议签订时，甲方应同时向乙方提供其拥有游戏标的物的权利登记证明文件或获得授权的证明文件（提供加盖甲方公章的复印件）。如因甲方对游戏标的物的权利存在瑕疵，造成乙方、游戏用户或其他第三方损失的，甲方承担全部的赔偿责任。
                          </p>
                          <p>
                              3.1.2本协议履行过程中，如遇第三方提出权利异议的情形，甲方应在收到乙方通知后24小时内作出处理并及时告知乙方采取相应的措施。
                          </p>
                          <p>
                              3.1.3甲方拥有硬件资源最高且唯一的管理操作权限以及基于上述权限的硬件资源管理操作权限的分配权。
                          </p>
                          <p>
                              3.1.4甲方拥有对游戏标的物的最终决定权与最终修改权，若对游戏标的物的重大修改，甲方应提前十五个工作日以书面形式通知乙方，以保证游戏标的物在运营平台的正常运营。&nbsp;
                          </p>
                          <p>
                              3.1.5甲方就游戏标的物提供技术支持和技术服务（包括游戏标的物的版本更新、升级），应及时在运营平台就变更内容通过公开渠道对网络用户进行公开、明确的公告与说明。
                          </p>
                          <p>
                              3.1.6在运营期间，甲方应提供给乙方游戏标的物相关的技术接口、计费接口、查询工具接口与相应查询权限，以保证乙方进行相关的运营活动。
                          </p>
                          <p>
                              3.1.7 甲方须配合乙方处理游戏标的物的常见问题、游戏基本规则及虚拟道具兑换问题引起的用户咨询和投诉，解决客户在游戏中遇到的问题，提供问题解答。
                          </p>
                          <p>
                              <span class="strong">3.2</span> 乙方的权利和义务&nbsp;
                          </p>
                          <p>
                              3.2.1 乙方承诺对运营平台拥有唯一的管理与运营资格，乙方具备优秀的市场宣传推广能力及人员储备以履行本协议。
                          </p>
                          <p>
                              3.2.2 乙方应当依据本协议对游戏标的物进行市场宣传与推广。乙方有权出于实现本合同约定目的将游戏标的物的文本、图片用于与游戏标的物运营具有实质性关联的广告资料、促销资料上，但甲方指定的保密信息或尚未进入公共领域的信息除外。
                          </p>
                          <p>
                              3.2.3乙方应在授权范围内使用甲方公司商号、商标及游戏标的物名称及内容等信息。在市场宣传与推广中，乙方不得对甲方的商号、商标、游戏标的物名称及内容等信息进行任何形式的改动。
                          </p>
                          <p>
                              3.2.4 乙方应根据自身的渠道和其他市场资源积极对游戏标的物的运营进行积极、正面、良好的市场宣传与推广活动。
                          </p>
                          <p>
                              3.2.5 乙方应根据排期将游戏标的物的宣传方式、宣传渠道及宣传资料制作等相关信息提供给甲方，甲方应提供必要配合与支持。
                          </p>
                          <p>
                              3.2.6 乙方拥有VRonline的全部用户数据的使用及维护权。
                          </p>
                          <p>
                              3.2.7 如遇第三方向乙方就游戏标的物的知识产权提出异议，要求乙方在VE助手上删除游戏标的物或者断开用户下载链接或提出其他权利主张的，乙方应当及时通知甲方；但乙方有权先行对第三方提供的材料进行初步审查并根据自己的判断决定采取相应的删除、下架或断开连接等措施，除非甲方在接到乙方通知后24小时内提出反证或者权利保证声明且认为第三方的主张明显不合理。
                          </p>
                          <p>
                              3.2.8 本协议履行过程中，乙方接到行政部门、司法部门等发出的有效法律文书或其他文件等对游戏标的物及其权利提出疑问或要求，乙方将予以配合并执行行政部门或司法部门的决定。
                          </p>
                          <p class="strong">
                              第四条 运营收入分配条款
                          </p>
                          <p>
                              4.1 双方确认，对游戏标的物在乙方VRonline上产生的运营收入按照本条款进行结算与分配，运营收入依据乙方的计费系统最终确认。
                          </p>
                          <p>
                              4.2 游戏标的物在VRonline的正式商业化运营期间，双方运营收入分配比例由本框架协议之补充协议约定。
                          </p>
                          <p>
                              4.3若双方就运营收入的统计数据存在异议，则按照以下方式解决：若双方统计数据误差不超过0.5%，则以乙方的统计数据进行结算；若双方统计数据误差超过0.5%，则双方应重新核对当月统计数据，并在十个工作日内予以解决。统计数据误差导致当月结算停滞的，不影响之前或之后无误差月的结算与分配。
                          </p>
                          <p>
                              4.4 结算方式：双方按自然月（自每月首日0时起至该月末日24时）结算。
                          </p>
                          <p>
                              乙方应当于每月第五个工作日前向甲方提供上月运营收入对账单，甲方在收到对账单后五个工作日内予以核对。甲方在收到对账单后五个工作日内未提出异议的，即视为甲方确认对账单无误。甲方确认对账单后应向乙方开具正规发票，乙方自收到甲方发票后的十个工作日内，将甲方应分收益支付到本协议第4.6条规定的甲方指定账户。
                          </p>
                          <p>
                              4.5当某自然月甲方收益不足人民币1000元时，乙方有权将甲方该月收益自动延期到下个自然月累计结算，合同履行期间内的最后一个自然月收益除外。
                          </p>
                          <p>
                              4.6 甲方指定账户：[%cpAccount%#longInput#]
                          </p>
                          <p>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;户 &nbsp;&nbsp;&nbsp;名：[%cpAccountName%#longInput#]
                          </p>
                          <p>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开户行：[%cpAccountBank%#longInput#]
                          </p>
                          <p>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;账 &nbsp;&nbsp;号：[%cpAccountNum%#longInput#]
                          </p>
                          <p class="strong">
                              第五条 客户服务
                          </p>
                          <p>
                              5.1 甲方应就游戏标的物提供客户服务。甲方应向网络用户公开提供具体、明确的客户服务方案，负责承担游戏标的物的常见问题解答，受理游戏基本规则及虚拟道具兑换问题引起的用户咨询和投诉，解决客户在游戏中遇到的问题。甲方在其宣传资料和运营平台专区明确标注客户服务热线和客户服务办法。
                          </p>
                          <p>
                              5.2 甲方负责提供游戏标的物相关资料和游戏标的物运营常见问题的处理方式，在游戏标的物通过运营平台的运营期间，应对乙方客服人员进行必要的客服培训。在乙方客服人员掌握相关技能后可直接向用户提供客户服务。
                          </p>
                          <p>
                              5.3 乙方负责承担运营平台产生的问题，并在其宣传资料和网站的突出位置标明其客户服务热线和客户服务办法等内容。
                          </p>
                          <p class="strong">
                              第六条 保密条款
                          </p>
                          <p>
                              6.1 任何一方通过订立、履行、终止本协议所获得的另一方信息，应视为专属于另一方的保密信息与商业秘密。未经另一方书面明确同意，信息持有方不得向任何第三方进行披露、使用，但本协议第6.2条规定的除外。
                          </p>
                          <p>
                              6.2 在下列情形下，任何一方可以依法对保密信息与商业秘密做出必要、合理的披露：
                          </p>
                          <p>
                              6.2.1 法律明文规定或者行政机关、司法机关明确要求；
                          </p>
                          <p>
                              6.2.2保密信息与商业秘密已经进入公共知识领域；
                          </p>
                          <p>
                              6.2.3另一方书面明确同意。
                          </p>
                          <p>
                              6.3 双方的保密义务不受本协议期限的限制。
                          </p>
                          <p class="strong">
                             第七条 终止运营条款
                          </p>
                          <p>
                              7.1 无论游戏标的物因何种原因导致停止在运营平台上进行商业化运营，双方均应积极完成终止运营前后的各项义务。
                          </p>
                          <p>
                              7.2 甲方应提前六十天在运营平台专区首页显著位置向网络用户公告终止运营的通知，通知内容应至少包括终止运营时间、终止运营前用户持有的虚拟货币处理方式等。
                          </p>
                          <p>
                              7.3甲方向网络用户正式发出终止运营通知后，在与乙方商定的时间内关闭支付渠道，网络用户将无法通过运营平台进行充值。甲乙双方共同解决因终止运营产生的用户投诉等事宜，共同就向网络用户已经充值的货币提供处理方式和渠道，以确保网络用户权益。但因甲方的游戏标的物停止运营产生的一切责任由甲方最终承担。
                          </p>
                          <p>
                              7.4 自乙方运营服务正式终止之日起，任何一方均应停止使用包含另一方LOGO、商标、商号、游戏名称、游戏关键字等含有知识产权的资料、素材与信息，否则视为侵权。
                          </p>
                          <p>
                              7.5 运营终止前用户已充值且已完成虚拟道具兑换的，因此实际产生的运营收入应按本协议相关条款予以分配；运营终止前用户已充值但未完成虚拟道具兑换的，不得作为运营收入分配，由乙方负责妥善解决向用户退款问题。
                          </p>
                          <p class="strong">
                              第八条 争端解决条款
                          </p>
                          <p>
                              8.1 本协议的签订、履行和解释适用中华人民共和国法律。
                          </p>
                          <p>
                              8.2 凡因本协议之订立、履行、终止所引起的或与本协议相关的任何争端，各方应首先友好协商解决；协商不成，任何一方有权向原告方所在地人民法院提起诉讼。。
                          </p>
                          <p class="strong">
                              第九条 文件送达
                          </p>
                          <p>
                              9.1 在本协议履行过程中所有向对方发送的的通知、意见、文件或其他意思表示（统称文件）均需按本协议首页所载联系地址和方式送达，除非收件方已以书面方式提前通知发送方变更联系地址和方式。
                          </p>
                          <p>
                              9.2 任何符合9.1条发出的文件，按下列方式应视为送达：
                          </p>
                          <p>
                              9.2.1专人送达的，以收件方签收日为送达日；收件方拒不签收的，则收件方拒不签收日视为送达日。
                          </p>
                          <p>
                              9.2.2 经电子传送确认的传真、电子邮件发送的，以成功发出传真、电子邮件日为送达日。
                          </p>
                          <p>
                              9.2.3 通过已付费的国内普遍认可的快递服务发送的，以收件方签收日但最迟以发送方交邮之日起第3个工作日为送达日。
                          </p>
                          <p>
                              9.2.4因收件方联系方式无效、不畅通而被退回或送达失败，则文件被退回或送达失败之日视为送达日。
                          </p>
                          <p>
                              9.3 收件方任何人员（包括但不限于行政人员、业务人员、前台、门卫、保安等）对送达文件的签收视为收件方的有效签收。
                          </p>
                          <p class="strong">
                              第十条 反商业贿赂保证
                          </p>
                          <p>
                              10.1 商业贿赂是指为获取与对方的合作及合作的利益，一方工作人员给予另一方工作人员（包括工作人员之家属、朋友或有直接或间接关系人士）的一切精神及物质上直接或间接的馈赠，包括但不限于回扣、娱乐、旅游、吃请等。甲乙双方除严格遵守《中华人民共和国反不正当竞争法》、《刑法》等有关禁止商业贿赂行为规定外，还应坚决拒绝商业贿赂、行贿及其他不正当之商业行为的馈赠。
                          </p>
                          <p>
                              10.2双方应对对方员工持尊重、公平和诚恳的态度，任何情况下，都不得利用职务之便借故刁难一方任何部门、员工，不得以任何名义向另一方索取或收受金钱、物品及任何形式的馈赠；如有该行为，被索贿方及员工应向另一方举报并提供证据；如一方明知被索取合同金额以外的财务或利益而不予举报或告知的，视为被索贿方严重违约。
                          </p>
                          <p>
                              10.3 若一方违反本规定，贿赂另一方任何员工，以图获取任何不正当商业利益或更特殊的商业待遇或不配合另一方查处其员工的受贿行为的，守约方有权单方面解除合同、停止双方间一切合作，并要求违约方支付10万元人民币或者所涉订单（合同）金额的30%作为违约金（两者以高者为准）。
                          </p>
                          <p class="strong">
                              第十一条 协议其他条款
                          </p>
                          <p>
                              11.1本协议自首页载明的双方签署之日生效，自【[%year1%#shortInput#]】年【[%month1%#shortInput#]】月【[%day1%#shortInput#]】日至【[%year2%#shortInput#]】年【[%month2%#shortInput#]】月【[%day2%#shortInput#]】日。如本协议期限届满，双方有意继续合作，应提前30日向对方提出续签意向，双方协商重新签订书面协议；否则本协议到期自动终止。新框架协议签订后，双方应按新协议约定的权利义务确定各款合作游戏并签订补充协议。
                          </p>
                          <p>
                              11.2若在本框架协议有效期内签订的补充协议约定的某款游戏运营期限超过了本框架协议的有效期限，本框架协议的全部条款仍适用于尚未到期的补充协议，直至补充协议期满。
                          </p>
                          <p>
                              11.3协议由主协议组成，一式两份，双方各执一份，具有同等法律效力。
                          </p>
                          <p>
                              11.4 本协议构成双方之间就游戏标的物通过运营平台进行运营所达成的全部真实意思表示，并且取代任何一方或双方之间在本协议生效前所有口头或书面形式的声明、备忘录、合同、协议、承诺、保证等文件。除非以书面形式签署并经盖章确认，否则对本协议的任何变更或废止均对双方不具有法律约束力。本协议项下所有权利和义务不可全部或者部分转让给第三方。
                          </p>
                          <p> 兹确认，本协议由以下双方具有合法授权人士在本协议载明之日盖章生效。
                          </p>
                          <p class="strong">
                              甲方： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 乙方： 上海恺英网络科技有限公司
                          </p>
                          <p>
                              （盖章） &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; （盖章）
                          </p>
                          <p>
                              &nbsp;授权代表（签字）： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 授权代表（签字）：
                          </p>
                          <p>
                              <br/>
                          </p>
                          <p>
                              &nbsp;日期： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;日期：
                          </p>
                          <br>
                          <br>
                          <p class="supply strong">
                              <strong>VRonline开发者协议之补充协议</strong>
                              <br>
                               <strong>&nbsp; &nbsp; &nbsp; &nbsp;——游戏《[%gamename%#longInput#]》</strong>
                          </p>
                          <p>
                              甲方：[%cpName%#longInput#]
                          </p>
                          <p>
                              地址：[%cpAddress%#longInput#]
                          </p>
                          <p>
                              乙方：上海恺英网络科技有限公司&nbsp;
                          </p>
                          <p>
                              地址：上海陈行路2388号9号楼10楼&nbsp;
                          </p>
                          <p class="strong">
                              <strong>鉴于：</strong>
                          </p>
                          <p>
                              甲乙双方于[%year3%#shortInput#]年[%month3%#shortInput#]月[%day3%#shortInput#]日签署了《VRonline开发者协议》，约定甲方授权乙方非独家运营甲方独家代理的手机游戏。现甲、乙双方经友好协商，特就《[%gamename%#longInput#]》游戏运营服务事宜于[%year4%#shortInput#]年[%month4%#shortInput#]月[%day4%#shortInput#]日订立以下补充协议：
                          </p>
                          <p>
                              一、合作游戏信息如下：
                          </p>
                          <p>
                              游戏名称：《[%gamename%#longInput#]》
                          </p>
                          <p>
                              游戏著作权证书登记号：[%rightnum%#longInput#]
                          </p>
                          <p>
                              二、游戏合作平台为【VRonline】
                          </p>
                          <p>
                              三、游戏虚拟货币即[%gameb%#shortInput#]，人民币与[%gameb%#shortInput#]的兑换比例为1:[%rate%#shortInput#]，即1元人民币兑换[%rate%#shortInput#]个游戏[%gameb%#shortInput#]。
                          </p>
                          <p>
                              四、游戏分成比例：
                          </p>
                          <p>
                          <table border="1" cellspacing="0" cellspacing="0" class="table">
                            <tr>
                              <td rowspan="2" style="width: 200px;text-align: center;">授权游戏当月运营收入<br>
                        (扣除渠道手续费)</td>
                              <td>分成比例（甲方）</td>
                              <td>分成比例（乙方）</td>
                            </tr>
                            <tr>
                              <td style="text-align: center;">[%ratea%#shortInput#]%</td>
                              <td style="text-align: center;">[%rateb%#shortInput#]%</td>
                            </tr>
                            <tr>
                              <td style="text-align: center;">渠道手续费</td>
                              <td colspan="2" style="text-align: center;">[%fee%#shortInput#]%</td>
                            </tr>
                          </table>
                          </p>
                          <p>
                              五、游戏的运营服务事项按框架协议执行，运营收入分成按照框架协议的约定进行结算支付。
                          </p>
                          <p>
                              六、游戏合作期限：自[%year5%#shortInput#]年[%month5%#shortInput#]月[%day5%#shortInput#]日至[%year6%#shortInput#]年[%month6%#shortInput#]月[%day6%#shortInput#]日。
                          </p>
                          <p>
                              七、本补充协议的所有用词与定义均与框架协议一致，未尽事宜按框架协议的约定执行。框架协议与本补充协议不一致的，以本补充协议约定为准。
                          </p>
                          <p>
                              八、本补充协议一式二份，甲、乙双方各执一份，自双方签字盖章后生效。
                          </p>
                          <p class="strong">
                              <strong>甲方：</strong> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>乙方： 上海恺英网络科技有限公司</strong>
                          </p>
                          <p>
                              （盖章） &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; （盖章）
                          </p>
                          <p>
                              &nbsp;授权代表（签字）： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;授权代表（签字）：
                          </p>
                          <p>
                              &nbsp;日期： &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;日期：
                          </p>
                          <input type="hidden" value="" name="soft" id="softfield">
                          <input type="hidden" value="" name="record" id="recordfield">
                          <input type="hidden" value="" name="publish" id="publishfield">
                          <input type="hidden" value="0" name="cp_deal" id="cp_dealfield">
                          </form>
                    </div>';
        if ($type == "input") {
            $pattern = "/\[%([^\]]+)%#([^\]]+)#\]/";
            $replace = "<input type='text' value='' name='$1' class='$2'>";
            $ret     = preg_replace($pattern, $replace, $agreement);
            return $ret;
        }
        $replace  = $search  = array();
        $protocal = json_decode($info, true);
        if ($protocal && is_array($protocal)) {
            foreach ($protocal as $key => $val) {
                $search[]  = "[%{$key}%#shortInput#]";
                $search[]  = "[%{$key}%#longInput#]";
                $replace[] = "<b><u>{$val}</u></b>";
                $replace[] = "<b><u>{$val}</u></b>";
            }
        }
        $ret     = str_replace($search, $replace, $agreement);
        $pattern = "/\[%([^\]]+)%#([^\]]+)#\]/";
        $replace = "";
        $ret     = preg_replace($pattern, $replace, $ret);
        return $ret;
    }

    /**
     * 根据内容分类判断显示评分、游玩人数、观看人数
     * vr游戏显示评分，网页游戏显示游玩人数、视频显示观看人数
     *
     * @param  arr $info 推荐信息
     * @return str       返回显示的信息
     */
    public function getScoreOrNum($info)
    {
        if (!is_array($info) || !isset($info["tp"])) {
            return false;
        }

        switch ($info["tp"]) {
            case 'webgame':
                $play = isset($info["play"]) ? $info["play"] : "0";
                return $this->formatNum($play) . "人玩过";
                break;

            case 'vrgame':
                return '评分：' . number_format($info["score"], 1);
                break;

            case 'video':
                $play = isset($info["play"]) ? $info["play"] : "0";
                return $this->formatNum($play) . "次播放";
                break;

            default:
                return "";
                break;
        }
    }

    /**
     * 格式化播放次数和游玩人数
     * >10000 以"万"为单位
     *
     * @param  [type] $num [description]
     * @return [type]      [description]
     */
    public function formatNum($num)
    {
        if ($num >= 10000) {
            $num = number_format($num / 10000, 1) . "万";
        }
        return $num;
    }

    /**
     * 转换分类显示
     *
     * @param  str $class [description]
     * @param  str $type  [description]
     * @return str        [description]
     */
    public static function transConetentClass($class, $type, $separator = " ")
    {
        if (!$class || !in_array($type, ["vrgame", "video", "webgame"])) {
            return false;
        }

        $classConfig = Config::get($type . ".class");

        $out = [];
        $in  = [];
        foreach ($class as $value) {
            $value = isset($classConfig[$value]["name"]) ? $classConfig[$value]["name"] : '';
            if ($value && !isset($in[$value])) {
                $in[$value] = 1;
                $out[]      = $value;
            }
        }

        return join($out, $separator);
    }

    /**
     * 根据内容分类判断添加类和属性
     *
     * @param  arr $info 推荐信息
     * @param  arr $attr 默认属性
     * @return str       返回显示的信息
     */
    public function handleRecommendAttr($info, $defaultAttr = [])
    {
        if (!is_array($defaultAttr) || !is_array($info)) {
            return false;
        }

        if (!isset($info["tp"])) {
            return false;
        }

        $attr = [];
        switch ($info["tp"]) {
            case 'webgame':
                $attr["class"]     = "start-web-game";
                $attr["game-id"]   = $info["id"];
                $attr["server-id"] = -1;
                $attr["game-name"] = $info["name"];
                break;

            case 'vrgame':
                $attr["class"] = "show-vrgame-detail";
                $attr["appid"] = $info["id"];
                break;

            case 'video':
                $attr["class"]    = "show-video-detail";
                $attr["video-id"] = $info["id"];
                break;

            default:
                break;
        }

        $attr = $attr + $defaultAttr;

        $attrStr = "";
        foreach ($attr as $attrName => $attrValue) {
            if (isset($defaultAttr[$attrName])) {
                $attrValue = $attrValue . " " . $defaultAttr[$attrName];
            }
            $attrStr .= $attrName . "=\"" . $attrValue . "\" ";
        }

        return $attrStr;
    }

    /**
     * 根据支持设备生成推荐位icon
     *
     * @param  [type] $devices [description]
     * @return [type]          [description]
     */
    public static function handleDeviceIcon($devices)
    {
        if (!$devices) {
            return "";
        }
        sort($devices);
        $deviceConfig = Config::get("vrgame.support_device");

        $html = "";
        foreach ($devices as $device) {
            if (isset($deviceConfig[$device])) {
                $html .= "<i title='{$deviceConfig[$device]["name"]}' class='icon-device icon-device-{$deviceConfig[$device]["icon-class"]}'></i>";
            }
        }

        return $html;
    }

    /**
     * 根据支持设备生成推荐位icon
     *
     * @param  [type] $devices [description]
     * @return [type]          [description]
     */
    public static function handleDeviceIconSuper($devices, $postion = "www_icon_class")
    {
        if (!$devices) {
            return "";
        }
        sort($devices);
        $deviceConfig = Config::get("vrgame.support_device");

        $arr = [];
        foreach ($devices as $device) {
            if (isset($deviceConfig[$device][$postion]) && $deviceConfig[$device][$postion]) {
                $arr[] = $deviceConfig[$device][$postion];
            }
        }
        $arr = array_unique($arr);
        return $arr;
    }

    /**
     * 根据链接类型生成不同的跳转方式
     *
     * @param  [type] $devices [description]
     * @return [type]          [description]
     */
    public function handleBannerAttr($banner, $defaultAttr = [])
    {
        if (!$banner) {
            return "";
        }

        $attr = [];

        $banner["link"] = isset($banner["link"]) ? $banner["link"] : "";

        if (isset($banner["link_tp"])) {
            switch ($banner["link_tp"]) {
                case 0:
                    $attr["href"] = $banner["link"] ?: "javascript:;";
                    break;
                case 1:
                    $attr["class"]   = "open-link";
                    $attr["link-to"] = $banner["link"];
                    $attr["href"]    = "javascript:;";
                    break;
                case 2:
                    $attr["class"]   = "open-link-platform";
                    $attr["link-to"] = $banner["link"];
                    $attr["href"]    = "javascript:;";
                    break;
                default:
                    $attr["href"] = "javascript:;";
                    break;
            }
        }

        $attr = $attr + $defaultAttr;

        $attrStr = "";
        foreach ($attr as $attrName => $attrValue) {
            if (isset($defaultAttr[$attrName])) {
                $attrValue = $attrValue . " " . $defaultAttr[$attrName];
            }
            $attrStr .= $attrName . "=\"" . $attrValue . "\" ";
        }

        return $attrStr;
    }

    public function getUpdtype($updtype)
    {
        switch ($updtype) {
            case 'force':
                return '强制更新';
                break;

            case 'silence':
                return '静默更新';
                break;

            case 'normal':
                return '普通更新';
                break;

            default:
                return "";
                break;
        }
    }

    public function getLocalUrl()
    {
        if (isset($_SERVER['HTTP_PROTOCOL'])) {
            $protocol = $_SERVER['HTTP_PROTOCOL'];
        } else {
            $protocol = "http";
        }
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

    public function handle3dbbInfo($arr)
    {
        if (!$arr || !is_array($arr)) {
            return false;
        }
        $html = "";
        if (isset($arr["title"])) {
            $html .= "<p>标题：{$arr["title"]}</p>";
        }

        if (isset($arr["img"])) {
            $html .= "<p>图片：<img style='vertical-align: top' width='100' src='" . static_image($arr["img"]) . "' /></p>";
        }

        if (isset($arr["video"])) {
            $html .= "<p>视频：<a href='{$arr['video']}' target='_blank'>{$arr['video']}</a></p>";
        }

        return $html;
    }
}
