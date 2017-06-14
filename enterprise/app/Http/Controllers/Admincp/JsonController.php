<?php
namespace App\Http\Controllers\Admincp;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\ActModel;
use App\Models\AdmincpModel;
use App\Models\CacheModel;
use App\Models\CdkModel;
use App\Models\DeveloperModel;
use App\Models\DevModel;
use App\Models\GameModel;
use App\Models\NewCommentModel;
use App\Models\NewsModel;
use App\Models\ServiceModel;
use App\Models\ThreeDBBDBModel;
use App\Models\ToBDBModel;
use App\Models\VersionModel;
use App\Models\VideoModel;
use App\Models\VronlineModel;
use App\Models\WebgameModel;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;
use Overtrue\Pinyin\Pinyin;

class JsonController extends Controller
{
    public function __construct()
    {
        $this->middleware("vrauth:json:admincp", ['only' => ["edit", "save", "del", "pass"]]);
    }

    public function edit(Request $request)
    {
        $user         = $request->userinfo;
        $name         = $request->input('name');
        $id           = intval($request->input('id'));
        $admincpModel = new AdmincpModel;
        switch ($name) {
            case 'vrgame_version':
                $appid       = intval($request->input('appid'));
                $versionName = $request->input('version_name');
                if ($versionName) {
                    $versionModel = new VersionModel;
                    $rows         = $versionModel->getVersions($appid, ['version_name' => $versionName]);
                    $data         = $rows[0];
                } else {
                    $data = ['version_name' => '', 'version_desc' => '', "version_start_exe" => ''];
                }
                $out = [
                    'version_name'      => ['tp' => 'input', 'val' => $data["version_name"], 'ck' => 'length'],
                    'version_desc'      => ['tp' => 'textarea', 'val' => $data["version_desc"], 'ck' => 'length'],
                    'version_start_exe' => ['tp' => 'input', 'val' => $data["version_start_exe"], 'ck' => 'length'],

                ];
                break;
            case 'vrhelp_video':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['video_id' => 0, 'video_name' => '', 'video_class' => '', 'video_intro' => '', 'video_copyright' => 1, 'video_link_tp' => 1, 'video_cover' => '', 'video_link' => '', 'video_uid' => $user['wwwUid'], 'video_times' => '', 'video_keywords' => '', 'video_vr' => 1];
                }
                $out = [
                    'video_id'       => ['tp' => 'input', 'val' => $data["video_id"], 'ck' => 'num'],
                    'video_name'     => ['tp' => 'input', 'val' => $data["video_name"], 'ck' => 'length'],
                    'video_class'    => ['tp' => 'muti_select', 'val' => $data["video_class"], 'ck' => 'length'],
                    'video_intro'    => ['tp' => 'textarea', 'val' => $data["video_intro"], 'ck' => 'length'],
                    'video_vr'       => ['tp' => 'radio', 'val' => $data["video_vr"], 'ck' => 'val'],
                    'video_link_tp'  => ['tp' => 'radio', 'val' => $data["video_link_tp"], 'ck' => 'val'],
                    'video_cover'    => ['tp' => 'img_input', 'val' => $data["video_cover"], 'ck' => 'length'],
                    'video_times'    => ['tp' => 'input', 'val' => $data["video_times"], 'ck' => 'length'],
                    'video_keywords' => ['tp' => 'input', 'val' => $data["video_keywords"], 'ck' => 'length'],
                    'wwwUid'         => $data["video_uid"],
                ];
                if ($data["video_link_tp"] == 1) {
                    $out['video_link']        = ['tp' => 'input', 'val' => $data["video_link"], 'ck' => 'no'];
                    $out['video_source_code'] = ['tp' => 'input', 'val' => '', 'ck' => 'no'];
                } else {
                    $out['video_source_code'] = ['tp' => 'input', 'val' => $data["video_link"], 'ck' => 'no'];
                    $out['video_link']        = ['tp' => 'input', 'val' => '', 'ck' => 'no'];
                }
                break;
            case 'vrhelp_vrgame':
                $tp = $request->input('tp');
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['appid' => 0, 'uid' => '', 'name' => '', 'tags' => '', 'first_class' => '', 'support' => '', 'content' => '', 'mini_device' => '', 'recomm_device' => '', 'sell' => 0, 'original_sell' => 0, 'screenshots' => '', 'img_slider' => '', 'img_version' => 0, 'ocruntimeversion' => '', 'mountings' => '', 'language' => '', 'product_com' => '', 'issuing_com' => '', 'client_size' => 100];
                }
                if ($tp == "pic") {
                    $imgs = ImageHelper::getUrl('vrgameimg', ['id' => $id, 'version' => $data['img_version'], 'img_slider' => $data['img_slider'], 'img_screenshots' => $data['screenshots']]);
                    $out  = [
                        'game_id'     => ['tp' => 'input', 'val' => $data["appid"], 'ck' => 'num'],
                        'game_logo'   => ['tp' => 'img_input', 'val' => $imgs['logo'], 'ck' => 'length'],
                        'game_icon'   => ['tp' => 'img_input', 'val' => $imgs['icon'], 'ck' => 'length'],
                        'game_rank'   => ['tp' => 'img_input', 'val' => $imgs['rank'], 'ck' => 'length'],
                        'game_bg'     => ['tp' => 'img_input', 'val' => $imgs['bg'], 'ck' => 'length'],
                        'game_slider' => ['tp' => 'imgs_input', 'val' => implode(",", $imgs['slider']), 'ck' => 'imgs_4'],
                    ];
                } else {
                    $recommend_device = json_decode($data['recomm_device'], true);
                    if ($recommend_device) {
                        $system   = isset($recommend_device['system']) ? $recommend_device['system'] : 'Windows 7';
                        $cpu      = isset($recommend_device['cpu']) ? $recommend_device['cpu'] : 'Intel i7';
                        $memory   = isset($recommend_device['memory']) ? $recommend_device['memory'] : '8G';
                        $directx  = isset($recommend_device['directx']) ? $recommend_device['directx'] : 'directx12';
                        $graphics = isset($recommend_device['graphics']) ? $recommend_device['graphics'] : 'GTX 970';
                    } else {
                        $system   = 'Windows 7';
                        $cpu      = 'Intel i7';
                        $memory   = '8G';
                        $directx  = 'directx12';
                        $graphics = 'GTX 970';
                    }

                    $out = [
                        'game_id'                 => ['tp' => 'input', 'val' => $data["appid"], 'ck' => 'num'],
                        'game_name'               => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'length'],
                        'game_tag'                => ['tp' => 'input', 'val' => $data["tags"], 'ck' => 'length'],
                        'game_uid'                => ['tp' => 'input', 'val' => $data["uid"], 'ck' => 'length'],
                        'game_class'              => ['tp' => 'muti_select', 'val' => $data["first_class"], 'ck' => 'length'],
                        'game_device'             => ['tp' => 'muti_select', 'val' => $data["support"], 'ck' => 'val'],
                        'game_mountings'          => ['tp' => 'muti_select', 'val' => $data["mountings"], 'ck' => 'no'],
                        'game_intro'              => ['tp' => 'textarea', 'val' => $data["content"], 'ck' => 'val'],
                        'game_original_sell'      => ['tp' => 'input', 'val' => $data["original_sell"], 'ck' => 'num'],
                        'game_sell'               => ['tp' => 'input', 'val' => $data["sell"], 'ck' => 'num'],
                        'game_oculus'             => ['tp' => 'input', 'val' => $data["ocruntimeversion"], 'ck' => 'no'],
                        'game_recommend_system'   => ['tp' => 'select_text', 'val' => $system, 'ck' => 'length'],
                        'game_recommend_cpu'      => ['tp' => 'select_text', 'val' => $cpu, 'ck' => 'length'],
                        'game_recommend_memory'   => ['tp' => 'select_text', 'val' => $memory, 'ck' => 'length'],
                        'game_recommend_directx'  => ['tp' => 'select_text', 'val' => $directx, 'ck' => 'length'],
                        'game_recommend_graphics' => ['tp' => 'select_text', 'val' => $graphics, 'ck' => 'length'],
                        'game_language'           => ['tp' => 'input', 'val' => $data["language"], 'ck' => 'no'],
                        'game_product_com'        => ['tp' => 'input', 'val' => $data["product_com"], 'ck' => 'no'],
                        'game_issuing_com'        => ['tp' => 'input', 'val' => $data["issuing_com"], 'ck' => 'no'],
                        'game_size'               => ['tp' => 'input', 'val' => $data["client_size"], 'ck' => 'no'],
                    ];
                }
                break;
            case 'top_banner':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'banner_url' => '', 'tp' => 'vrgame', 'target_url' => '', 'top_icon' => '', 'top_title' => '', 'top_sub_title' => '', 'top_desc' => '', 'link_tp' => 0, 'itemid' => ''];
                }
                $out = [
                    'top_id'        => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'banner_url'    => ['tp' => 'img_input', 'val' => $data["banner_url"], 'ck' => 'no'],
                    'target_url'    => ['tp' => 'input', 'val' => $data["target_url"], 'ck' => 'no'],
                    'top_icon'      => ['tp' => 'img_input', 'val' => $data["top_icon"], 'ck' => 'no'],
                    'top_title'     => ['tp' => 'input', 'val' => $data["top_title"], 'ck' => 'no'],
                    'top_sub_title' => ['tp' => 'input', 'val' => $data["top_sub_title"], 'ck' => 'no'],
                    'top_desc'      => ['tp' => 'textarea', 'val' => $data["top_desc"], 'ck' => 'no'],
                    'top_link_tp'   => ['tp' => 'select', 'val' => $data["link_tp"], 'ck' => 'no'],
                    'top_item_id'   => ['tp' => 'input', 'val' => $data["itemid"], 'ck' => 'no'],
                    'top_tp'        => ['tp' => 'radio', 'val' => $data["tp"], 'ck' => 'length'],
                ];
                break;
            case 'dbb_info':
                if ($id > 0) {
                    $info = $admincpModel->getOneData($name, $id);
                    $data = [
                        'id'           => $info["id"],
                        'info_img_url' => $info["detail"]["img"],
                        "info_sort"    => $info["sort"],
                        'info_title'   => $info["detail"]["title"],
                        'video_url'    => $info["detail"]["video"],
                    ];
                } else {
                    $data = ['id' => 0, 'info_img_url' => 0, "info_sort" => 0, 'info_title' => '', 'video_url' => ''];
                }
                $out = [
                    'info_id'      => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'info_img_url' => ['tp' => 'img_input', 'val' => $data["info_img_url"], 'ck' => 'img'],
                    'info_sort'    => ['tp' => 'input', 'val' => $data["info_sort"], 'ck' => 'num'],
                    'info_title'   => ['tp' => 'input', 'val' => $data["info_title"], 'ck' => 'length'],
                    'video_url'    => ['tp' => 'input', 'val' => $data["video_url"], 'ck' => 'length'],
                ];
                break;
            case 'vrhelp_position':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['posid' => 0, 'content_tp' => 'vrgame', 'name' => '', 'code' => '', 'tp' => '', 'desc' => ''];
                }
                $out = [
                    'top_id'         => ['tp' => 'input', 'val' => $data["posid"], 'ck' => 'num'],
                    'top_content_tp' => ['tp' => 'radio', 'val' => $data["content_tp"], 'ck' => 'length'],
                    'top_name'       => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'length'],
                    'top_code'       => ['tp' => 'input', 'val' => $data["code"], 'ck' => 'length'],
                    'top_tp'         => ['tp' => 'select', 'val' => $data["tp"], 'ck' => 'length'],
                    'top_desc'       => ['tp' => 'input', 'val' => $data["desc"], 'ck' => 'length'],
                ];
                break;
            case 'vronline_position':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['pos_id' => 0, 'pos_code' => '', 'pos_group' => '', 'pos_name' => '', 'pos_desc' => ''];
                }
                $out = [
                    'pos_id'    => ['tp' => 'input', 'val' => $data["pos_id"], 'ck' => 'num'],
                    'pos_code'  => ['tp' => 'input', 'val' => $data["pos_code"], 'ck' => 'length'],
                    'pos_group' => ['tp' => 'select', 'val' => $data["pos_group"], 'ck' => 'length'],
                    'pos_name'  => ['tp' => 'input', 'val' => $data["pos_name"], 'ck' => 'length'],
                    'pos_desc'  => ['tp' => 'input', 'val' => $data["pos_desc"], 'ck' => 'no'],
                ];
                break;
            case 'vronline_game':
                if ($id > 0) {
                    $ret = $admincpModel->getOneData($name, $id);

                    $data = ['game_id' => $ret['game_id'], 'game_name' => $ret['game_name'], 'game_alias' => $ret['game_alias'], 'game_category' => $ret['game_category'], 'game_tag' => $ret['game_tag'], 'game_sell_date' => date('Y-m-d H:i:s', $ret['game_sell_date']), 'game_price' => $ret['game_price'], 'game_device' => $ret['game_device'], 'game_platform' => $ret['game_platform'], 'game_lang' => $ret['game_lang'], 'game_theme' => $ret['game_theme'], 'game_developer' => $ret['game_company'], 'game_operator' => $ret['game_operator'], 'game_website' => $ret['game_offical_url'], 'game_address' => $ret['game_buy_url'], 'game_download' => $ret['game_down_url'], 'game_desc' => $ret['game_desc'], 'game_search_name' => $ret['game_search_name'], 'game_image' => $ret['game_image']];
                } else {
                    $data = ['game_id' => 0, 'game_name' => '', 'game_alias' => '', 'game_category' => '', 'game_tag' => '', 'game_sell_date' => '2017-04-05 12:00:00', 'game_price' => '', 'game_device' => '', 'game_platform' => '', 'game_lang' => '', 'game_theme' => '', 'game_developer' => '', 'game_operator' => '', 'game_website' => '', 'game_address' => '', 'game_download' => '', 'game_desc' => '', 'game_search_name' => '', 'game_image' => ''];
                }
                $out = [
                    'game_id'          => ['tp' => 'input', 'val' => $data["game_id"], 'ck' => 'num'],
                    'game_name'        => ['tp' => 'input', 'val' => $data["game_name"], 'ck' => 'length'],
                    'game_alias'       => ['tp' => 'input', 'val' => $data["game_alias"], 'ck' => 'length'],
                    'game_category'    => ['tp' => 'muti_select', 'val' => $data["game_category"], 'ck' => 'length'],
                    'game_tag'         => ['tp' => 'muti_select', 'val' => $data["game_tag"], 'ck' => 'length'],
                    'game_sell_date'   => ['tp' => 'input', 'val' => $data["game_sell_date"], 'ck' => 'length'],
                    'game_price'       => ['tp' => 'input', 'val' => $data["game_price"], 'ck' => 'length'],
                    'game_device'      => ['tp' => 'muti_select', 'val' => $data["game_device"], 'ck' => 'length'],
                    'game_platform'    => ['tp' => 'muti_select', 'val' => $data["game_platform"], 'ck' => 'length'],
                    'game_lang'        => ['tp' => 'muti_select', 'val' => $data["game_lang"], 'ck' => 'length'],
                    'game_theme'       => ['tp' => 'input', 'val' => $data["game_theme"], 'ck' => 'length'],
                    'game_developer'   => ['tp' => 'input', 'val' => $data["game_developer"], 'ck' => 'length'],
                    'game_operator'    => ['tp' => 'input', 'val' => $data["game_operator"], 'ck' => 'length'],
                    'game_website'     => ['tp' => 'input', 'val' => $data["game_website"], 'ck' => 'length'],
                    'game_address'     => ['tp' => 'input', 'val' => $data["game_address"], 'ck' => 'no'],
                    'game_download'    => ['tp' => 'input', 'val' => $data["game_download"], 'ck' => 'length'],
                    'game_desc'        => ['tp' => 'textarea', 'val' => $data["game_desc"], 'ck' => 'length'],
                    'game_search_name' => ['tp' => 'input', 'val' => $data["game_search_name"], 'ck' => 'length'],
                    'top_cover'        => ['tp' => 'img_input', 'val' => $data["game_image"], 'ck' => 'no'],
                ];
                break;
            case 'vronline_video':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['article_id' => 0, 'article_title' => '', 'article_category' => '', 'article_content' => '', 'article_video_tp' => 3, 'article_video_source_tp' => 1, 'article_cover' => '', 'article_video_source_url' => '', 'article_video_time' => '', 'article_tag' => ''];
                }
                $out = [
                    'video_id'        => ['tp' => 'input', 'val' => $data["article_id"], 'ck' => 'num'],
                    'video_title'     => ['tp' => 'input', 'val' => $data["article_title"], 'ck' => 'length'],
                    'video_category'  => ['tp' => 'muti_select', 'val' => $data["article_category"], 'ck' => 'length'],
                    'video_content'   => ['tp' => 'textarea', 'val' => $data["article_content"], 'ck' => 'length'],
                    'video_tp'        => ['tp' => 'radio', 'val' => $data["article_video_tp"], 'ck' => 'length'],
                    'video_time'      => ['tp' => 'input', 'val' => $data["article_video_time"], 'ck' => 'length'],
                    'video_tag'       => ['tp' => 'input', 'val' => $data["article_tag"], 'ck' => 'length'],
                    'video_source_tp' => ['tp' => 'radio', 'val' => $data["article_video_source_tp"], 'ck' => 'length'],
                    'video_cover'     => ['tp' => 'img_input', 'val' => $data["article_cover"], 'ck' => 'length'],
                ];
                if ($data["article_video_source_tp"] == 1) {
                    $out['video_source_url']  = ['tp' => 'input', 'val' => $data["article_video_source_url"], 'ck' => 'no'];
                    $out['video_source_code'] = ['tp' => 'input', 'val' => '', 'ck' => 'no'];
                } else {
                    $out['video_source_code'] = ['tp' => 'input', 'val' => $data["article_video_source_url"], 'ck' => 'no'];
                    $out['video_source_url']  = ['tp' => 'input', 'val' => '', 'ck' => 'no'];
                }
                break;
            case 'vronline_top':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'tp' => 'banner', 'cover' => '', 'title' => '', 'itemid' => '', 'intro' => '', 'target_url' => ''];
                }
                $out = [
                    'top_id'         => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'top_tp'         => ['tp' => 'radio', 'val' => $data["tp"], 'ck' => 'length'],
                    'top_itemid'     => ['tp' => 'input', 'val' => $data["itemid"], 'ck' => 'no'],
                    'top_cover'      => ['tp' => 'img_input', 'val' => $data["cover"], 'ck' => 'no'],
                    'top_title'      => ['tp' => 'input', 'val' => $data["title"], 'ck' => 'no'],
                    'top_intro'      => ['tp' => 'input', 'val' => $data["intro"], 'ck' => 'no'],
                    'top_target_url' => ['tp' => 'input', 'val' => $data["target_url"], 'ck' => 'no'],
                ];
                break;
            case 'sys_user':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'name' => '', 'account' => '', 'password' => '', 'group_id' => 0];
                }
                $out = [
                    'user_id'  => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'name'     => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'length'],
                    'account'  => ['tp' => 'input', 'val' => $data["account"], 'ck' => 'length'],
                    'group'    => ['tp' => 'select', 'val' => $data["group_id"], 'ck' => 'length'],
                    'password' => ['tp' => 'input', 'val' => '', 'ck' => 'password'],
                ];
                break;
            case 'product_client':
                if ($id > 0) {
                    $data               = $admincpModel->getOneData($name, $id);
                    $data['client_uid'] = $user['wwwUid'];
                } else {
                    $data = ['id' => 0, 'version' => '1.0.1', 'pushtime' => date('Y-m-d H:i:s', time()), 'newfeature' => '', 'updtype' => '', 'whole_size' => '', 'online_size' => '', 'status' => 1, 'pushnum' => 0, 'client_uid' => $user['wwwUid']];
                }
                $out = [
                    'id'          => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'version'     => ['tp' => 'input', 'val' => $data["version"], 'ck' => 'length'],
                    'pushtime'    => ['tp' => 'input', 'val' => $data["pushtime"], 'ck' => 'length'],
                    'newfeature'  => ['tp' => 'textarea', 'val' => $data["newfeature"], 'ck' => 'length'],
                    'updtype'     => ['tp' => 'select', 'val' => $data["updtype"], 'ck' => 'length'],
                    'whole_size'  => ['tp' => 'input', 'val' => $data["whole_size"], 'ck' => 'length'],
                    'online_size' => ['tp' => 'input', 'val' => $data["online_size"], 'ck' => 'length'],
                    'pushnum'     => ['tp' => 'input', 'val' => $data["pushnum"], 'ck' => 'num'],
                    'status'      => ['tp' => 'input', 'val' => $data["status"], 'ck' => 'num'],
                    'wwwUid'      => $data["client_uid"],
                ];
                break;
            case 'online_client': //客户端在线更新添加的接口
                if ($id > 0) {
                    $data               = $admincpModel->getOneData($name, $id);
                    $data['client_uid'] = $user['wwwUid'];
                } else {
                    $data = ['id' => 0, 'version' => '1.0.1', 'online_size' => '', 'status' => 0, 'client_uid' => $user['wwwUid']];
                }
                $out = [
                    'id'          => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'version'     => ['tp' => 'input', 'val' => $data["version"], 'ck' => 'length'],
                    'online_size' => ['tp' => 'input', 'val' => $data["online_size"], 'ck' => 'length'],
                    'status'      => ['tp' => 'input', 'val' => $data["status"], 'ck' => 'num'],
                    'wwwUid'      => $data["client_uid"],
                ];
                break;
            case 'news_article':
                $data = $admincpModel->getOneData($name, $id);
                $out  = [
                    'article_id'          => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'article_title'       => ['tp' => 'input', 'val' => $data["title"], 'ck' => 'length'],
                    'article_tp'          => ['tp' => 'select', 'val' => $data["tp"], 'ck' => 'no'],
                    'article_source'      => ['tp' => 'input', 'val' => $data["source"], 'ck' => 'no'],
                    'article_source_link' => ['tp' => 'input', 'val' => $data["source_link"], 'ck' => 'no'],
                    'article_content'     => ['tp' => 'article', 'val' => $data["content"], 'ck' => 'length'],
                    'article_cover'       => ['tp' => 'img_input', 'val' => $data["cover"], 'ck' => 'no'],
                ];
                break;
            case 'vronline_news':
            case 'vronline_pc':
                $data = $admincpModel->getOneData($name, $id);
                $out  = [
                    'article_id'       => ['tp' => 'input', 'val' => $data["article_id"], 'ck' => 'num'],
                    'article_title'    => ['tp' => 'input', 'val' => $data["article_title"], 'ck' => 'length'],
                    'article_alias'    => ['tp' => 'input', 'val' => $data["article_alias"], 'ck' => 'no'],
                    'article_category' => ['tp' => 'muti_select', 'val' => $data["article_category"], 'ck' => 'no'],
                    'article_source'   => ['tp' => 'input', 'val' => $data["article_source"], 'ck' => 'no'],
                    'article_tag'      => ['tp' => 'input', 'val' => $data["article_tag"], 'ck' => 'no'],
                    'article_keywords' => ['tp' => 'input', 'val' => $data["article_keywords"], 'ck' => 'no'],
                    'article_content'  => ['tp' => 'article', 'val' => $data["article_content"], 'ck' => 'length'],
                    'article_cover'    => ['tp' => 'img_input', 'val' => $data["article_cover"], 'ck' => 'no'],
                ];
                if ($name == "vronline_pc") {
                    $out['article_pc_match']  = ['tp' => 'input', 'val' => $data["article_pc_match"], 'ck' => 'length'];
                    $out['article_category']  = ['tp' => 'select', 'val' => $data["article_category"], 'ck' => 'length'];
                    $out['article_target_id'] = ['tp' => 'input', 'val' => $data["article_target_id"], 'ck' => 'length'];
                }
                break;
            case 'news_position':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['posid' => 0, 'content_tp' => 'article', 'name' => '', 'code' => '', 'desc' => ''];
                }
                $out = [
                    'top_id'         => ['tp' => 'input', 'val' => $data["posid"], 'ck' => 'num'],
                    'top_content_tp' => ['tp' => 'radio', 'val' => $data["content_tp"], 'ck' => 'length'],
                    'top_name'       => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'length'],
                    'top_code'       => ['tp' => 'input', 'val' => $data["code"], 'ck' => 'length'],
                    'top_desc'       => ['tp' => 'input', 'val' => $data["desc"], 'ck' => 'length'],
                ];
                break;
            case 'news_recommend':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'cover' => '', 'title' => '', 'itemid' => ''];
                }
                $out = [
                    'rec_id'      => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'rec_item_id' => ['tp' => 'input', 'val' => $data["itemid"], 'ck' => 'val'],
                    'rec_cover'   => ['tp' => 'img_input', 'val' => $data["cover"], 'ck' => 'no'],
                    'rec_title'   => ['tp' => 'input', 'val' => $data["title"], 'ck' => 'no'],
                ];
                break;
            case 'news_banner':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'cover' => '', 'target_url' => '', 'title' => '', 'intro' => ''];
                }
                $out = [
                    'rec_id'         => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'rec_cover'      => ['tp' => 'img_input', 'val' => $data["cover"], 'ck' => 'img'],
                    'rec_target_url' => ['tp' => 'input', 'val' => $data["target_url"], 'ck' => 'length'],
                    'rec_title'      => ['tp' => 'input', 'val' => $data["title"], 'ck' => 'no'],
                    'rec_intro'      => ['tp' => 'input', 'val' => $data["intro"], 'ck' => 'no'],
                ];
                break;
            case "sys_group":
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'name' => '', 'perms' => [], 'path' => ''];
                }
                $out = [
                    'group_id'    => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'group_name'  => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'length'],
                    'group_perms' => ['tp' => 'checkboxs', 'val' => $data["perms"], 'ck' => 'length'],
                    'group_path'  => ['tp' => 'input', 'val' => $data["path"], 'ck' => 'length'],
                ];
                break;
            case "service_qa":
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'tp' => 0, 'question' => '', 'answer' => ''];
                }
                $out = [
                    'qa_id'       => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'qa_tp'       => ['tp' => 'select', 'val' => $data["tp"], 'ck' => 'length'],
                    'qa_question' => ['tp' => 'textarea', 'val' => $data["question"], 'ck' => 'length'],
                    'qa_answer'   => ['tp' => 'article', 'val' => $data["answer"], 'ck' => 'length'],
                ];
                break;
            case "service_feedback":
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'uid' => '', 'account' => '', 'tp' => 0, 'sub_tp' => 0, 'title' => '', 'name' => '', 'mobile' => '', 'qq' => '', 'email' => ''];
                }
                $out = [
                    'question_id'      => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'question_uid'     => ['tp' => 'input', 'val' => $data["uid"], 'ck' => 'no'],
                    'question_account' => ['tp' => 'input', 'val' => $data["account"], 'ck' => 'no'],
                    'question_title'   => ['tp' => 'textarea', 'val' => $data["title"], 'ck' => 'length'],
                    'question_tp'      => ['tp' => 'select', 'val' => $data["tp"], 'ck' => 'length'],
                    'question_sub_tp'  => ['tp' => 'select', 'val' => $data["sub_tp"], 'ck' => 'length'],
                    'question_name'    => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'no'],
                    'question_mobile'  => ['tp' => 'input', 'val' => $data["mobile"], 'ck' => 'no'],
                    'question_qq'      => ['tp' => 'input', 'val' => $data["qq"], 'ck' => 'no'],
                    'question_email'   => ['tp' => 'input', 'val' => $data["email"], 'ck' => 'no'],
                ];
                break;
            case "webgame_news":
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'title' => '', 'tp' => 0, 'link' => ''];
                }
                $out = [
                    'news_id'    => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'news_title' => ['tp' => 'input', 'val' => $data["title"], 'ck' => 'length'],
                    'news_tp'    => ['tp' => 'select', 'val' => $data["tp"], 'ck' => 'length'],
                    'news_link'  => ['tp' => 'input', 'val' => $data["link"], 'ck' => 'length'],
                ];
                break;
            case "tob_merchats":
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'merchantid' => '', 'account' => '', 'merchant' => '', 'contact' => '', 'tel' => '', 'address' => '', 'bank_account' => '', 'bank_type' => '', 'pay_pwd' => ''];
                }
                $out = [
                    'id'           => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'merchantid'   => ['tp' => 'input', 'val' => $data["merchantid"], 'ck' => 'length', 'readonly' => true],
                    'account'      => ['tp' => 'input', 'val' => $data["account"], 'ck' => 'length', 'readonly' => true],
                    'merchant'     => ['tp' => 'input', 'val' => $data["merchant"], 'ck' => 'length'],
                    'contact'      => ['tp' => 'input', 'val' => $data["contact"], 'ck' => 'length'],
                    'tel'          => ['tp' => 'input', 'val' => $data["tel"], 'ck' => 'length'],
                    'address'      => ['tp' => 'input', 'val' => $data["address"], 'ck' => 'length'],
                    'bank_account' => ['tp' => 'input', 'val' => $data["bank_account"], 'ck' => 'length'],
                    'bank_type'    => ['tp' => 'select', 'val' => $data["bank_type"], 'ck' => 'length'],
                    'pay_pwd'      => ['tp' => 'input', 'val' => $data["pay_pwd"], 'ck' => 'length'],
                ];
                break;
            case 'tob_banner':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['id' => 0, 'cover' => '', 'target_url' => '', 'title' => '', 'intro' => ''];
                }
                $out = [
                    'rec_id'         => ['tp' => 'input', 'val' => $data["id"], 'ck' => 'num'],
                    'rec_cover'      => ['tp' => 'img_input', 'val' => $data["cover"], 'ck' => 'img'],
                    'rec_target_url' => ['tp' => 'input', 'val' => $data["target_url"], 'ck' => 'length'],
                    'rec_title'      => ['tp' => 'input', 'val' => $data["title"], 'ck' => 'no'],
                    'rec_intro'      => ['tp' => 'input', 'val' => $data["intro"], 'ck' => 'no'],
                ];
                break;
            case 'vrhelp_price':
                if ($id > 0) {
                    $data = $admincpModel->getOneData($name, $id);
                } else {
                    $data = ['appid' => 0, 'original_sell' => 0, 'sell' => 0];
                }
                $out = [
                    'appid'         => ['tp' => 'input', 'val' => $data["appid"], 'ck' => 'num'],
                    'original_sell' => ['tp' => 'input', 'val' => $data["original_sell"], 'ck' => 'num'],
                    'sell'          => ['tp' => 'input', 'val' => $data["sell"], 'ck' => 'num'],
                ];
                break;
            case 'vrhelp_cdk':

                $data = ['itemid' => 0, 'num' => 0, 'type' => 'game'];

                $out = [
                    'itemid' => ['tp' => 'input', 'val' => $data["itemid"], 'ck' => 'val'],
                    'num'    => ['tp' => 'input', 'val' => $data["num"], 'ck' => 'val'],
                    'type'   => ['tp' => 'input', 'val' => $data["type"], 'ck' => 'length'],
                ];
                break;
            default:
                # code...
                break;
        }
        return json_encode($out);
    }

    public function save(Request $request, $name)
    {
        $userInfo = $request->userinfo;
        switch ($name) {
            case 'vrgame_version':
                $appid           = intval($request->input('appid'));
                $versionName     = $request->input('version_name');
                $versionDesc     = $request->input('version_desc');
                $versionStartExe = $request->input('version_start_exe');
                $versionId       = $request->input('version_id');
                $versionModel    = new VersionModel;
                if ($versionId) {
                    $ret = $versionModel->chooseSubVersion($appid, $versionName, $versionId);
                } else {
                    $versions = $versionModel->getVersions($appid, ['version_name' => $versionName]);
                    if ($versions) {
                        // if ($versions[0]['stat'] != 0) {
                        //     return Library::output(3302);
                        // } else {
                        $ret = $versionModel->updateVersion($appid, $versionName, ['version_desc' => $versionDesc, 'version_start_exe' => $versionStartExe]);
                        //}
                    } else {
                        $versions = $versionModel->getVersions($appid, ['stat' => 0]);
                        if ($versions) {
                            return Library::output(3302);
                        }
                        $ret = $versionModel->addVersion($appid, ['version_name' => $versionName, 'version_desc' => $versionDesc, 'version_start_exe' => $versionStartExe]);
                    }
                }

                break;
            case 'vrhelp_video':
                $data = $request->all();
                $info = $this->parseData($data);

                if ($info['video_link_tp'] == 1) {
                    $info['video_link'] = $request->input('video_link');
                } else {
                    $info['video_link'] = $request->input('video_source_code');
                }
                unset($info['video_source_code']);
                $id = $info['video_id'];
                unset($info['video_id']);
                $videoModel         = new VideoModel;
                $info['video_stat'] = 1;
                $ret                = $videoModel->saveDevVideoInfo($id, $info);
                break;
            case 'vrhelp_vrgame':
                $appid     = intval($request->input('game_id'));
                $game_logo = $request->input('game_logo');
                $devModel  = new DevModel;
                if ($game_logo) {
                    $gameInfo = $devModel->getWebgameInfo($appid);
                    if (!$gameInfo) {
                        return Library::output(1);
                    }
                    $game_slider = $request->input('game_slider');
                    $sliderArr   = explode(",", $game_slider);
                    foreach ($sliderArr as $key => $value) {
                        $tmp             = explode("/", $value);
                        $sliderArr[$key] = $tmp[count($tmp) - 1];
                    }
                    $arr                = [];
                    $arr['img_slider']  = json_encode($sliderArr);
                    $arr['img_version'] = $gameInfo['img_version'] + 1;
                    $ret                = $devModel->updWebgameInfo($appid, $arr);
                } else {
                    $arr                     = [];
                    $arr['uid']              = $request->input('game_uid');
                    $arr['name']             = $request->input('game_name');
                    $arr['tags']             = $request->input('game_tag');
                    $arr['first_class']      = $request->input('game_class');
                    $arr['support']          = $request->input('game_device');
                    $arr['mountings']        = $request->input('game_mountings');
                    $arr['content']          = $request->input('game_intro');
                    $arr['original_sell']    = $request->input('game_original_sell');
                    $arr['sell']             = $request->input('game_sell');
                    $arr['ocruntimeversion'] = $request->input('game_oculus');
                    $arr['game_type']        = 1;
                    $system                  = $request->input('game_recommend_system');
                    $cpu                     = $request->input('game_recommend_cpu');
                    $memory                  = $request->input('game_recommend_memory');
                    $directx                 = $request->input('game_recommend_directx');
                    $graphics                = $request->input('game_recommend_graphics');
                    $arr['language']         = $request->input('game_language');
                    $arr['product_com']      = $request->input('game_product_com');
                    $arr['issuing_com']      = $request->input('game_issuing_com');
                    $arr['client_size']      = (int) $request->input('game_size');
                    if (!$arr['uid'] || !$arr['name'] || !$arr['tags'] || !$arr['first_class'] || !$arr['support'] || !$arr['content']) {
                        return Library::output(1);
                    }
                    $arr['recomm_device'] = json_encode(['system' => $system, 'cpu' => $cpu, 'memory' => $memory, 'directx' => $directx, 'graphics' => $graphics]);
                    if (!$appid) {
                        $ret = $devModel->addWebgameInfo($arr);
                        if ($ret) {
                            $accountModel = new AccountCenter();
                            $accountModel->setAppInfo($ret, array('appid' => $ret));
                        }

                    } else {
                        $ret = $devModel->updWebgameInfo($appid, $arr);
                    }
                }
                break;
            case 'top_banner':
                $data = $request->all();
                $info = $this->parseData($data);
                $id   = intval($info['top_id']);
                $arr  = ['posid' => $info['posid'], 'itemid' => 1, 'tp' => $info['top_tp'], 'banner_url' => $info['banner_url'], 'target_url' => $info['target_url'], 'link_tp' => $info['top_link_tp'], 'top_icon' => $info['top_icon'], 'top_title' => $info['top_title'], 'top_sub_title' => $info['top_sub_title'], 'top_desc' => $info['top_desc'], 'itemid' => $info['top_item_id']];

                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->updateTopRecommend($id, $arr);
                break;
            case 'dbb_info':
                $data   = $request->all();
                $info   = $this->parseData($data);
                $id     = intval($info['info_id']);
                $detail = [
                    "title" => $info["info_title"],
                    "img"   => $info["info_img_url"],
                    "video" => $info["video_url"],
                ];
                $arr = [
                    "position" => $info["info_position"],
                    "detail"   => json_encode($detail),
                    "sort"     => $info["info_sort"],
                ];
                $actModel = new ActModel;
                $ret      = $actModel->updateInfo($id, $arr);
                break;
            case 'sys_user':
                $user_id         = intval($request->input('user_id'));
                $arr             = [];
                $arr['name']     = $request->input('name');
                $arr['account']  = $request->input('account');
                $arr['group_id'] = intval($request->input('group'));
                $password        = $request->input('password');
                if (strlen($password) >= 6) {
                    $hash            = Config::get("admincp.hash");
                    $arr['password'] = md5(md5($password . $hash));
                }

                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->updateSysUser($user_id, $arr);
                break;
            case 'vrhelp_position':
                $top_id         = intval($request->input('top_id'));
                $top_code       = trim($request->input('top_code'));
                $top_content_tp = trim($request->input('top_content_tp'));
                $top_desc       = trim($request->input('top_desc'));
                $top_name       = trim($request->input('top_name'));
                $top_tp         = trim($request->input('top_tp'));

                $info         = ['code' => $top_code, 'name' => $top_name, 'content_tp' => $top_content_tp, 'desc' => $top_desc, 'tp' => $top_tp];
                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->updateTopPostion($top_id, $info);
                break;
            case 'vronline_position':
                $pos_id    = trim($request->input('pos_id'));
                $pos_code  = trim($request->input('pos_code'));
                $pos_group = trim($request->input('pos_group'));
                $pos_desc  = trim($request->input('pos_desc'));
                $pos_name  = trim($request->input('pos_name'));

                $info          = ['pos_code' => $pos_code, 'pos_name' => $pos_name, 'pos_group' => $pos_group, 'pos_desc' => $pos_desc];
                $vronlineModel = new VronlineModel;
                $ret           = $vronlineModel->updatePostion($pos_id, $info);
                break;
            case 'product_client':
                $id                 = intval($request->input('id'));
                $arr                = [];
                $arr['version']     = $request->input('version');
                $arr['pushtime']    = $request->input('pushtime');
                $arr['updtype']     = $request->input('updtype');
                $arr['status']      = $request->input('status');
                $arr['newfeature']  = $request->input('newfeature');
                $arr['whole_size']  = $request->input('whole_size');
                $arr['online_size'] = $request->input('online_size');
                $arr['pushnum']     = $request->input('pushnum');

                $admincpModel = new AdmincpModel;
                if ($arr['status'] == 1) {
                    $result = $admincpModel->updateVersionStatus(1);
                }
                $ret = $admincpModel->updateClientVersion($id, $arr);
                break;
            case 'online_client':
                $id                 = intval($request->input('id'));
                $arr                = [];
                $arr['version']     = $request->input('version');
                $arr['status']      = $request->input('status');
                $arr['online_size'] = $request->input('online_size');

                $admincpModel = new AdmincpModel;
                // if ($arr['status'] == 1) {
                //     $result = $admincpModel->updateVersionStatus(1);
                // }
                $ret = $admincpModel->updateOnlineVersion($id, $arr);
                break;
            case 'news_article':
                $id                 = intval($request->input('article_id'));
                $arr                = [];
                $arr['title']       = $request->input('article_title');
                $arr['tp']          = $request->input('article_tp');
                $arr['source']      = $request->input('article_source');
                $arr['source_link'] = $request->input('article_source_link');
                $arr['content']     = $request->input('article_content');
                $arr['cover']       = $request->input('article_cover');
                $arr['stat']        = 2;
                if ($id < 1) {
                    $arr['author'] = $userInfo['name'];
                }
                $newsModel = new NewsModel;
                $result    = $newsModel->updateDevNews($id, $arr);
                if ($result === false) {
                    return Library::output(2505);
                }
                if (is_numeric($result) && $id == 0) {
                    $out = ['id' => $result];
                    return Library::output(0, $out);
                }
                break;
            case "news_article_sub":
                $id          = intval($request->input('id'));
                $arr         = [];
                $arr['stat'] = 1;

                $newsModel = new NewsModel;
                $result    = $newsModel->updateDevNews($id, $arr);
                break;
            case "vronline_news_sub":
            case "vronline_pc_sub":
            case "vronline_video_sub":
                $id                  = intval($request->input('id'));
                $arr                 = [];
                $arr['article_stat'] = 1;

                $vronlineModel = new VronlineModel;
                $result        = $vronlineModel->updateDevNews($id, $arr);
                break;
            case "vronline_game":
                $data         = $request->all();
                $info         = $this->parseData($data);
                $id           = intval($info['game_id']);
                $gameCategory = '';
                $arr          = ['game_name' => $info['game_name'], 'game_vrhelp_id' => 1, 'game_alias' => $info['game_alias'], 'game_category' => $info['game_category'], 'game_tag' => $info['game_tag'], 'game_sell_date' => strtotime($info['game_sell_date']), 'game_price' => $info['game_price'], 'game_device' => $info['game_device'], 'game_platform' => $info['game_platform'], 'game_lang' => $info['game_lang'], 'game_theme' => $info['game_theme'], 'game_company' => $info['game_developer'], 'game_operator' => $info['game_operator'], 'game_offical_url' => $info['game_website'], 'game_buy_url' => $info['game_address'], 'game_down_url' => $info['game_download'], 'game_desc' => $info['game_desc'], 'game_search_name' => $info['game_search_name'], 'game_image' => $info['top_cover']];

                $vronlineModel = new VronlineModel;
                $ret           = $vronlineModel->saveGameInfo($id, $arr);
                if ($ret) {
                    return Library::output(0);
                }
                return Library::output(1, $arr);
                break;
            case 'news_position':
                $top_id         = intval($request->input('top_id'));
                $top_code       = trim($request->input('top_code'));
                $top_content_tp = trim($request->input('top_content_tp'));
                $top_desc       = trim($request->input('top_desc'));
                $top_name       = trim($request->input('top_name'));

                $info         = ['code' => $top_code, 'name' => $top_name, 'content_tp' => $top_content_tp, 'desc' => $top_desc];
                $admincpModel = new NewsModel;
                $ret          = $admincpModel->updateNewsPosition($top_id, $info);
                break;
            case "news_recommend":
                $rec_id      = trim($request->input('rec_id'));
                $rec_posid   = intval($request->input('posid'));
                $rec_item_id = trim($request->input('rec_item_id'));
                $rec_cover   = trim($request->input('rec_cover'));
                $rec_title   = trim($request->input('rec_title'));

                $rec_intro      = trim($request->input('rec_intro'));
                $rec_target_url = trim($request->input('rec_target_url'));

                $tp           = trim($request->input('tp'));
                $info         = ['posid' => $rec_posid, 'itemid' => $rec_item_id, 'cover' => $rec_cover, 'title' => $rec_title, 'tp' => $tp, 'intro' => $rec_intro, 'target_url' => $rec_target_url];
                $admincpModel = new NewsModel;
                $ret          = $admincpModel->updateNewsRecommend($rec_id, $info);
                break;
            case "vronline_top":
                $top_id         = intval($request->input('top_id'));
                $posCode        = trim($request->input('pos_code'));
                $top_itemid     = intval($request->input('top_itemid'));
                $top_cover      = trim($request->input('top_cover'));
                $top_title      = trim($request->input('top_title'));
                $top_intro      = trim($request->input('top_intro'));
                $top_target_url = trim($request->input('top_target_url'));
                $tp             = trim($request->input('top_tp'));
                if ($tp == 'sort') {
                    $tp = 'banner';
                }
                $info          = ['pos_code' => $posCode, 'itemid' => $top_itemid, 'cover' => $top_cover, 'title' => $top_title, 'tp' => $tp, 'intro' => $top_intro, 'target_url' => $top_target_url];
                $vronlineModel = new VronlineModel;
                $ret           = $vronlineModel->updateTop($top_id, $info);
                break;
            case 'vronline_video':
                $id                             = intval($request->input('video_id'));
                $arr                            = [];
                $arr['article_title']           = $request->input('video_title');
                $arr['article_category']        = $request->input('video_category');
                $arr['article_video_tp']        = $request->input('video_tp');
                $arr['article_video_time']      = $request->input('video_time');
                $arr['article_video_source_tp'] = $request->input('video_source_tp');
                $arr['article_tag']             = $request->input('video_tag');
                $arr['article_content']         = $request->input('video_content');
                $arr['article_cover']           = $request->input('video_cover');
                $arr['article_stat']            = 2;
                $arr['article_tp']              = 'video';

                if ($arr['article_video_source_tp'] == 1) {
                    $arr['article_video_source_url'] = $request->input('video_source_url');
                } else {
                    $arr['article_video_source_url'] = $request->input('video_source_code');
                }

                $vronlineModel = new VronlineModel;
                $result        = $vronlineModel->updateDevNews($id, $arr);
                if ($result === false) {
                    return Library::output(2505);
                }
                if (is_numeric($result) && $id == 0) {
                    $out = ['id' => $result];
                    return Library::output(0, $out);
                }
                break;
            case 'vronline_news':
            case 'vronline_pc':
                $id                      = intval($request->input('article_id'));
                $arr                     = [];
                $arr['article_title']    = $request->input('article_title');
                $arr['article_alias']    = $request->input('article_alias');
                $arr['article_category'] = $request->input('article_category');
                $arr['article_source']   = $request->input('article_source');
                $arr['article_keywords'] = $request->input('article_keywords');
                $arr['article_tag']      = $request->input('article_tag');
                $arr['article_content']  = $request->input('article_content');
                $arr['article_cover']    = $request->input('article_cover');
                $arr['article_stat']     = 2;
                if ($name == "vronline_pc") {
                    $arr['article_target_id'] = $request->input('article_target_id');
                    $arr['article_pc_match']  = $request->input('article_pc_match');
                    $arr['article_tp']        = 'pc';
                }
                if ($id < 1) {
                    $arr['article_author_id'] = 10017;
                }
                $vronlineModel = new VronlineModel;
                $result        = $vronlineModel->updateDevNews($id, $arr);
                if ($result === false) {
                    return Library::output(2505);
                }
                if (is_numeric($result) && $id == 0) {
                    $out = ['id' => $result];
                    return Library::output(0, $out);
                }
                break;
            case "sys_group":
                $group_id    = intval($request->input('group_id'));
                $group_name  = trim($request->input('group_name'));
                $group_perms = $request->input('group_perms');
                $group_path  = $request->input('group_path');
                $perms       = [];
                foreach ($group_perms as $value) {
                    $perms[] = intval($value);
                }
                $info         = ['id' => $group_id, 'name' => $group_name, 'perms' => json_encode($perms), 'path' => $group_path];
                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->updateSysGroup($group_id, $info);
                break;
            case 'service_qa':
                $id              = intval($request->input('qa_id'));
                $arr             = [];
                $arr['tp']       = intval($request->input('qa_tp'));
                $arr['question'] = $request->input('qa_question');
                $arr['answer']   = $request->input('qa_answer');
                $serviceModel    = new ServiceModel;
                $ret             = $serviceModel->updateQa($id, $arr);
                break;
            case 'service_feedback':
                $id  = intval($request->input('question_id'));
                $arr = [];

                if ($id < 1) {
                    $arr['code'] = questionCode();
                }
                $arr['uid']     = intval($request->input('question_uid'));
                $arr['account'] = $request->input('question_account');
                $arr['title']   = $request->input('question_title');
                $arr['tp']      = $request->input('question_tp');
                $arr['sub_tp']  = $request->input('question_sub_tp');
                $arr['name']    = $request->input('question_name');
                $arr['mobile']  = $request->input('question_mobile');
                $arr['qq']      = $request->input('question_qq');
                $arr['email']   = $request->input('question_email');
                $serviceModel   = new ServiceModel;
                $ret            = $serviceModel->updateQuestion($id, $arr);
                break;
            case 'webgame_news':
                $id  = intval($request->input('news_id'));
                $arr = [];

                $arr['gameid'] = intval($request->input('news_gameid'));
                $arr['title']  = $request->input('news_title');
                $arr['tp']     = $request->input('news_tp');
                $arr['link']   = $request->input('news_link');
                $webGameModel  = new WebgameModel;
                $ret           = $webGameModel->updateWebGameNews($id, $arr);
                break;
            case 'service_feedback_reply':
                $code = $request->input('code');
                $cn   = $request->input('cn');

                $servicesModel = new ServiceModel();
                $row           = $servicesModel->searchQuestion($code);
                if (!$row) {
                    return Library::output(1);
                }

                $content = json_decode($row['content'], true);
                if (!$content) {
                    $content = [];
                }
                $arr = [];
                array_push($content, ['tp' => 1, 'cn' => $cn, 'time' => time(), 'name' => '']);
                $arr['content'] = json_encode($content);
                $servicesModel  = new ServiceModel();
                $ret            = $servicesModel->updateQuestion($row['id'], $arr);
                break;
            case "tob_merchats":
                $id                  = intval($request->input('id'));
                $arr                 = [];
                $arr['merchantid']   = $request->input('merchantid');
                $arr['account']      = $request->input('account');
                $arr['merchant']     = $request->input('merchant');
                $arr['contact']      = $request->input('contact');
                $arr['tel']          = $request->input('tel');
                $arr['address']      = $request->input('address');
                $arr['bank_account'] = $request->input('bank_account');
                $arr['bank_type']    = $request->input('bank_type');
                $arr['pay_pwd']      = $request->input('pay_pwd');
                $toBDBModel          = new ToBDBModel;
                if ($id < 1) {
                    $ret = $toBDBModel->add2bMerchant($arr['merchantid'], $arr);
                } else {
                    $merchantid = $arr['merchantid'];
                    unset($arr['merchantid']);
                    unset($arr['account']);
                    $ret = $toBDBModel->upd2bMerchant($merchantid, $arr);
                }
                break;
            case "tob_addgame":
                $tp        = $request->input('tp');
                $id        = intval($request->input('id'));
                $gameModel = new GameModel;
                if ($tp == "add") {
                    $ret = $gameModel->updateTobGame($id, 1);
                } else if ($tp == "del") {
                    $ret = $gameModel->updateTobGame($id, 0);
                } else if ($tp == "default-add") {
                    $ret = $gameModel->updateTobGame($id, 2);
                } else if ($tp == "default-del") {
                    $ret = $gameModel->updateTobGame($id, 1);
                }
                break;
            case "tob_defaultproduct":
                $price      = round($request->input('price'), 2);
                $time       = round($request->input('time'), 2);
                $desc       = $request->input('desc');
                $lowrate    = round($request->input('lowrate'), 2);
                $ToBDBModel = new ToBDBModel();
                $ret        = $ToBDBModel->setDefaultProduct([$price, $time, $desc, $lowrate]);

                break;
            case "tob_banner":
                $rec_id         = trim($request->input('rec_id'));
                $rec_cover      = trim($request->input('rec_cover'));
                $rec_title      = trim($request->input('rec_title'));
                $rec_intro      = trim($request->input('rec_intro'));
                $rec_target_url = trim($request->input('rec_target_url'));

                $info         = ['cover' => $rec_cover, 'title' => $rec_title, 'tp' => 1, 'intro' => $rec_intro, 'target_url' => $rec_target_url];
                $admincpModel = new ToBDBModel;
                $ret          = $admincpModel->updateWwwBanner($rec_id, $info);
                break;
            case 'vrhelp_price':
                $appid         = trim($request->input('appid'));
                $sell          = trim($request->input('sell'));
                $original_sell = trim($request->input('original_sell'));

                $info      = ['sell' => $sell, 'original_sell' => $original_sell];
                $gameModel = new GameModel;
                $ret       = $gameModel->updatePubGameInfo($appid, $info);
                break;
            case 'vrhelp_cdk':
                $num    = trim($request->input('num'));
                $type   = trim($request->input('type'));
                $itemid = trim($request->input('itemid'));

                $cdkModel = new CdkModel;
                $ret      = $cdkModel->importCdk($itemid, $type, $num);
                break;
            case 'vrhelp_3dbb':
                $stat   = trim($request->input('tp'));
                $itemid = trim($request->input('id'));
                $msg    = $request->input('msg');

                $threeDBBModel = new ThreeDBBDBModel();

                $ret = $threeDBBModel->updateStat($itemid, $stat, $msg);
                break;
            case 'vronline_comments':
                $stat   = trim($request->input('tp'));
                $itemid = trim($request->input('id'));
                $msg    = $request->input('msg');
                if ($stat == 1) {
                    $action = "pass";
                } else if ($stat == 2) {
                    $action = "deny";
                } else {
                    return Library::output(1);
                }
                $newCommentModel = new NewCommentModel;
                $ret             = $newCommentModel->reviewComment($itemid, $action);
                break;
        }
        return Library::output(0);
    }

    public function pass(Request $request, $name)
    {
        switch ($name) {
            case 'vrhelp_developer':
                $id  = $request->input('edit_id');
                $tp  = intval($request->input('tp'));
                $msg = $request->input('msg');
                if (!$id) {
                    return Library::output(2001);
                }
                if ($tp == 1) {
                    $stat = 5;
                } else {
                    $stat = 3;
                }
                $developerModel = new DeveloperModel;
                $ret            = $developerModel->updateUser($id, ['stat' => $stat, 'msg' => $msg]);
                break;
            case 'vrgame_version':
                $appid       = $request->input('appid');
                $versionName = $request->input('version_name');

                $versionModel = new VersionModel;
                $ret          = $versionModel->publishversion($appid, $versionName);

                break;
            case 'vrhelp_video':
                $id  = $request->input('edit_id');
                $tp  = intval($request->input('tp'));
                $msg = $request->input('msg');

                $videoModel = new VideoModel;
                $video      = $videoModel->getDevVideoById($id);
                if (isset($video['video_name']) && $video['video_name']) {
                    $pinyin = new Pinyin();
                    $spell  = strtolower($pinyin->sentence($video['video_name']));
                    $spell  = substr($spell, 0, 1);
                    if (is_numeric($spell)) {
                        $spell = Library::num2Pinyin($spell);
                        $spell = substr($spell, 0, 1);
                    }
                    $setinfo                = array();
                    $setinfo['video_spell'] = $spell;
                    $videoModel->saveDevVideoInfo($id, $setinfo);
                }
                if ($tp == 1) {
                    $ret = $videoModel->passDevVideoInfo($id);
                } else {
                    $info                 = array();
                    $info['video_stat']   = 3;
                    $info['video_review'] = $msg;
                    $ret                  = $videoModel->saveDevVideoInfo($id, $info);
                }
                break;
            case 'news_article':
                $id  = $request->input('edit_id');
                $tp  = intval($request->input('tp'));
                $msg = $request->input('msg');

                $newsModel = new NewsModel;
                if ($tp == 1) {
                    $ret = $newsModel->passDevNews($id);
                } else {
                    $info           = array();
                    $info['stat']   = 3;
                    $info['review'] = $msg;
                    $ret            = $newsModel->updateDevNews($id, $info);
                }
                break;
            case 'vronline_news':
            case 'vronline_pc':
            case 'vronline_video':
                $id  = $request->input('edit_id');
                $tp  = intval($request->input('tp'));
                $msg = $request->input('msg');

                $vronlineModel = new VronlineModel;
                if ($tp == 1) {
                    $ret = $vronlineModel->passDevNews($id);
                } else {
                    $info                   = array();
                    $info['article_stat']   = 3;
                    $info['article_review'] = $msg;
                    $ret                    = $vronlineModel->updateDevNews($id, $info);
                }
                break;
            case 'vrhelp_vrgame':
            case 'vrhelp_webgame':
                $id  = $request->input('edit_id');
                $tp  = intval($request->input('tp'));
                $msg = trim($request->input('msg'));
                if (!$id || !$tp) {
                    return Library::output(2001);
                }
                $gameModel = new GameModel;
                $gameInfo  = $gameModel->getDevGameInfo($id);
                if (!$gameInfo) {
                    return Library::output(1);
                }

                if (strlen($gameInfo['img_slider']) < 5) {
                    return Library::output(1);
                }

                if ($tp == 1) {
                    $ret = $gameModel->passDevGame($gameInfo);
                } else {
                    $info           = array();
                    $info['stat']   = 3;
                    $info['review'] = $msg;
                    $ret            = $gameModel->updateDevGameInfo($id, $info);
                }
                break;
            case 'merchant':
                $id  = $request->input('edit_id');
                $tp  = intval($request->input('tp'));
                $msg = trim($request->input('msg'));
                if (!$id || $tp < 0) {
                    return Library::output(2001);
                }
                $ToBDBModel   = new ToBDBModel;
                $merchantInfo = $ToBDBModel->get2bMerchant($id);
                if (!$merchantInfo) {
                    return Library::output(1, "未查到相关店铺");
                }
                $info = [];
                if ($tp == 1) {
                    $ret = $ToBDBModel->addMerchantDefaultGame($id);
                    if (!$ret) {
                        return Library::output(1, "添加默认游戏失败");
                    }
                    $info["status"] = 9;
                    $ret            = $ToBDBModel->upd2bMerchant($id, $info);
                } else {
                    $info           = array();
                    $info['status'] = 5;
                    $info['reason'] = $msg;
                    $ret            = $ToBDBModel->upd2bMerchant($id, $info);
                }
                if (!$ret) {
                    return Library::output(1, "更新店铺信息失败");
                }
                break;
        }
        return Library::output(0);
    }

    public function del(Request $request, $name)
    {
        $id = intval($request->input('del_id'));
        switch ($name) {
            case 'vrgame_version':
                $appid        = intval($request->input('appid'));
                $versionName  = $request->input('version_name');
                $versionModel = new VersionModel;
                $ret          = $versionModel->delVersion($appid, $versionName);

                break;
            case 'vrhelp_video':
                $videoModel = new VideoModel;
                $ret        = $videoModel->offlineDevVideoInfo($id);

                break;
            case 'sys_user':
                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->delSysUser($id);
                break;
            case 'product_client':
                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->delClientVersion($id);
                break;
            case 'online_client':
                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->delUpClientVersion($id);
                break;
            case 'vrhelp_position':
                $admincpModel = new AdmincpModel;
                $ret          = $admincpModel->delTopPostion($id);
                break;
            case 'vronline_position':
                $admincpModel = new VronlineModel;
                $ret          = $admincpModel->delPostion($id);
                break;
            case 'vronline_top':
                $admincpModel = new VronlineModel;
                $ret          = $admincpModel->delTop($id);
                break;
            case 'news_article':
                $newsModel = new NewsModel;
                $ret       = $newsModel->delDevNews($id);
                break;
            case 'vronline_news':
            case 'vronline_pc':
            case 'vronline_video':
                $vronlineModel = new VronlineModel;
                $ret           = $vronlineModel->delDevNews($id);
                break;
            case 'news_position':
                $newsModel = new NewsModel;
                $ret       = $newsModel->delNewsPosition($id);
                break;
            case "news_recommend":
                $newsModel = new NewsModel;
                $ret       = $newsModel->delNewsRecommend($id);
                break;
            case "sys_group":
                $newsModel = new AdmincpModel;
                $ret       = $newsModel->delSysGroup($id);
                break;
            case "service_qa":
                $serviceModel = new ServiceModel;
                $ret          = $serviceModel->delServiceQa($id);
                break;
            case "webgame_news":
                $webGameModel = new WebgameModel;
                $ret          = $webGameModel->delWebGameNews($id);
                break;
            case 'vrhelp_vrgame':
            case 'vrhelp_webgame':
                $tp = intval($request->input('del_tp'));
                if ($tp == 1) {
                    $gameModel = new GameModel;
                    $gameInfo  = $gameModel->onlineGame($id);
                } else {
                    $gameModel = new GameModel;
                    $gameInfo  = $gameModel->offlineGame($id);
                }
                break;
            case 'tob_merchats':
                $toBDBModel = new ToBDBModel;
                $ret        = $toBDBModel->del2bMerchant($id);
                break;
            case 'vronline_game':
                $vronlineModel = new VronlineModel;
                $ret           = $vronlineModel->delGame($id);
                break;
            case "tob_banner":
                $toBDBModel = new ToBDBModel;
                $ret        = $toBDBModel->delWwwBanner($id);
                break;
            case "dbb_info":
                $actModel = new ActModel;
                $ret      = $actModel->delInfoById($id);
                break;
        }
        return Library::output(0);
    }

    private function parseData($data)
    {
        foreach ($data as $key => $value) {
            if (strstr($key, "json")) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    public function updateVersionStatus(Request $request)
    {
        $status = $request->input('status');
        $id     = $request->input('id');

        $admincpModel = new AdmincpModel;
        $arr          = [
            'status' => $status,
        ];
        if ($status == 1) {
            $result = $admincpModel->updateVersionStatus(1);
        } elseif ($status == 2) {
            $result = $admincpModel->updateVersionStatus(2);
        }
        $ret = $admincpModel->updateClientVersion($id, $arr);
        if ($ret) {
            return Library::output(0);
        }
    }

    public function databasePublic()
    {
        $admincpModel = new AdmincpModel;
        $status       = [1, 2];
        $versionInfo  = '';
        foreach ($status as $v) {
            $versionInfo[] = $admincpModel->getClientVersion($v);
        }
        return Library::output(0, $versionInfo);
    }

    public function versionPublic()
    {
        $CacheModel   = new CacheModel();
        $admincpModel = new AdmincpModel;
        $statusArr    = [
            1 => 'latest',
            2 => 'stable',
        ];
        $versionInfo = [];
        foreach ($statusArr as $k => $v) {
            $tmpinfo = $admincpModel->getClientVersion($k);
            if (!$tmpinfo || !is_array($tmpinfo) || count($tmpinfo) != 1) {
                if ($k == 1) {
                    $tmptitle = "最新版本";
                    $CacheModel->delClientVersionInfo($v);
                    continue;
                } elseif ($k == 2) {
                    $tmptitle = "稳定版本";
                } else {
                    $tmptitle = "X版本";
                }
                return Library::output(9, "", "{$tmptitle}不是1个");
            }
            $versionInfo = $tmpinfo[0];
            if ($versionInfo) {
                $info = [
                    "version"     => $versionInfo['version'],
                    "pushtime"    => $versionInfo['pushtime'],
                    "newfeature"  => $versionInfo['newfeature'],
                    "whole_size"  => $versionInfo['whole_size'],
                    "online_size" => $versionInfo['online_size'],
                    "pushnum"     => $versionInfo['pushnum'],
                    "updtype"     => $versionInfo['updtype'],
                ];
                $ret = $CacheModel->setClientVersionInfo($v, $info);
            }

            /**
             * 记录最后一次操作时，这个版本的状态，便于找到某个时间点的版本发布情况
             */
            $admincpModel->updateClientVersion($versionInfo['id'], array("laststatus" => $versionInfo['status'], "lasttime" => date("Y-m-d H:i:s")));
        }
        if ($ret) {
            return Library::output(0, $versionInfo);
        }
    }

    public function alreadyPublic(Request $request)
    {
        $CacheModel = new CacheModel();
        $statusArr  = [
            1 => 'latest',
            2 => 'stable',
        ];
        $versionInfo = '';
        foreach ($statusArr as $k => $v) {
            $versionInfo[] = $CacheModel->getClientVersionInfo($v);
        }
        return Library::output(0, $versionInfo);
    }
    /**
     * 更新在线更新版本的状态
     * [updateVersionStatus description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function updateOnlineStatus(Request $request)
    {
        $status = $request->input('status');
        $id     = $request->input('id');

        $admincpModel = new AdmincpModel;
        $arr          = [
            'status' => $status,
        ];
        if ($status == 1) {
            $result = $admincpModel->updateOnlineStatus(1);
        }
        $ret = $admincpModel->updateOnlineVersion($id, $arr);
        if ($ret) {
            return Library::output(0);
        }
        return Library::output(1);
    }
    /**
     * 获取数据库中的在线更新版本的接口
     * [databasePublic description]
     * @return [type] [description]
     */
    public function databaseUpPublic()
    {
        $admincpModel = new AdmincpModel;
        $status       = [1];
        $versionInfo  = '';
        foreach ($status as $v) {
            $versionInfo[] = $admincpModel->getUpOnlineVersion($v);
        }
        return Library::output(0, $versionInfo);
    }

    public function setUpOnlineCache()
    {
        $CacheModel   = new CacheModel();
        $admincpModel = new AdmincpModel;
        $versionInfo  = $admincpModel->getUpOnlineVersion(1);
        if (!$versionInfo) {
            return Library::output(2);
        }
        $versionInfoArr = [
            'version' => $versionInfo[0]['version'],
            'size'    => $versionInfo[0]['online_size'],
        ];
        // $json = json_encode($versionInfoArr);
        $ret = $CacheModel->setOnlinePreVersion($versionInfoArr);
        if ($ret) {
            return Library::output(0);
        }
        return Library::output(1, $versionInfoArr);
    }
}
