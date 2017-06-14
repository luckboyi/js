<?php
namespace App\Http\Controllers\Developer;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;
use \App\Models\DeveloperModel;
use \App\Models\VersionModel;
use \App\Models\WebgameModel;

class JsonController extends Controller
{
    public function __construct()
    {
        $this->model = new DeveloperModel;
        $this->middleware("vrauth:json", ['only' => ["edit", "save", "del", "submit"]]);

    }

    public function edit(Request $request)
    {
        $user = $request->userinfo;
        $name = $request->input('name');
        $id   = intval($request->input('id'));
        switch ($name) {
            case 'sign':
                $tp      = $request->input('tp');
                $devInfo = $this->model->getUser($user['uid']);
                if (!$devInfo) {
                    $data = ['name' => '', 'idcard' => '', 'email' => '', 'address' => '', 'contacts' => '', 'img' => ''];
                } else {
                    $data        = $devInfo;
                    $imgInfo     = ImageHelper::getUrl('openuser', ['id' => $user['uid'], 'version' => $data['pic_version']]);
                    $data['img'] = $imgInfo['idcard'];
                }

                $out = [
                    'name'       => ['tp' => 'input', 'val' => $data["name"], 'ck' => 'no'],
                    'idcard'     => ['tp' => 'input', 'val' => $data['idcard'], 'ck' => 'no'],
                    'email'      => ['tp' => 'input', 'val' => $data['email'], 'ck' => 'no'],
                    'address'    => ['tp' => 'input', 'val' => $data['address'], 'ck' => 'no'],
                    'idcard_pic' => ['tp' => 'img_input', 'val' => $data['img'], 'ck' => 'no'],
                ];
                if ($tp == "company") {
                    $out['contacts'] = ['tp' => 'input', 'val' => $data["contacts"], 'ck' => 'num'];
                }
                break;
            case 'vrgame':
                $tp = $request->input('tp');
                if ($id) {
                    $data = $this->model->getGameById($id);
                } else {
                    $data = ['appid' => 0, 'uid' => '', 'name' => '', 'tags' => '', 'first_class' => '', 'support' => '', 'content' => '', 'mini_device' => '', 'recomm_device' => '', 'sell' => 0, 'original_sell' => 0, 'screenshots' => '', 'img_slider' => '', 'img_version' => 0, 'ocruntimeversion' => '', 'mountings' => '', 'language' => '', 'product_com' => '', 'issuing_com' => ''];
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
                } else if ($tp == "pay") {
                    $payUrls = $this->model->getPayUrl($id);
                    if (!$payUrls) {
                        $payUrls = ['payurl' => '', 'payurltest' => ''];
                    }
                    $out = [
                        'game_id'           => ['tp' => 'input', 'val' => $data["appid"], 'ck' => 'num'],
                        'game_pay_url'      => ['tp' => 'input', 'val' => $payUrls['payurl'], 'ck' => 'length'],
                        'game_pay_url_test' => ['tp' => 'input', 'val' => $payUrls['payurltest'], 'ck' => 'length'],
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
                        'game_class'              => ['tp' => 'muti_select', 'val' => $data["first_class"], 'ck' => 'length'],
                        'game_device'             => ['tp' => 'muti_select', 'val' => $data["support"], 'ck' => 'val'],
                        'game_intro'              => ['tp' => 'textarea', 'val' => $data["content"], 'ck' => 'val'],
                        'game_original_sell'      => ['tp' => 'input', 'val' => $data["original_sell"], 'ck' => 'num'],
                        'game_sell'               => ['tp' => 'input', 'val' => $data["sell"], 'ck' => 'num'],
                        'game_recommend_system'   => ['tp' => 'select_text', 'val' => $system, 'ck' => 'length'],
                        'game_recommend_cpu'      => ['tp' => 'select_text', 'val' => $cpu, 'ck' => 'length'],
                        'game_recommend_memory'   => ['tp' => 'select_text', 'val' => $memory, 'ck' => 'length'],
                        'game_recommend_directx'  => ['tp' => 'select_text', 'val' => $directx, 'ck' => 'length'],
                        'game_recommend_graphics' => ['tp' => 'select_text', 'val' => $graphics, 'ck' => 'length'],
                        'game_oculus'             => ['tp' => 'input', 'val' => $data["ocruntimeversion"], 'ck' => 'no'],
                        'game_mountings'          => ['tp' => 'muti_select', 'val' => $data["mountings"], 'ck' => 'no'],
                        'game_language'           => ['tp' => 'input', 'val' => $data["language"], 'ck' => 'no'],
                        'game_product_com'        => ['tp' => 'input', 'val' => $data["product_com"], 'ck' => 'no'],
                        'game_issuing_com'        => ['tp' => 'input', 'val' => $data["issuing_com"], 'ck' => 'no'],
                    ];
                }
                break;
            case "vrgame_copyright":
                if ($id) {
                    $data = $this->model->getGameById($id);
                }
                $cp_soft    = $data['cp_soft'];
                $cp_record  = $data['cp_record'];
                $cp_publish = $data['cp_publish'];
                $cp_soft    = $cp_soft ? $cp_soft : "";
                $cp_record  = $cp_record ? $cp_record : "";
                $cp_publish = $cp_publish ? $cp_publish : "";
                $out        = [
                    'soft'    => ['tp' => 'imgs_input', 'val' => $cp_soft, 'ck' => 'no'],
                    'record'  => ['tp' => 'imgs_input', 'val' => $cp_record, 'ck' => 'no'],
                    'publish' => ['tp' => 'imgs_input', 'val' => $cp_publish, 'ck' => 'no'],
                ];
                break;
            case 'vrgame_version':
                $versionName = $request->input('version_name');
                if ($versionName) {
                    $versionModel = new VersionModel;
                    $rows         = $versionModel->getVersions($id, ['version_name' => $versionName]);
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
        }
        return json_encode($out);
    }

    public function save(Request $request, $name)
    {
        $user = $request->userinfo;
        $id   = intval($request->input('id'));

        switch ($name) {
            case 'sign':
                $tp       = $request->input('tp');
                $name     = $request->input('name');
                $idcard   = $request->input('idcard');
                $email    = $request->input('email');
                $address  = $request->input('address');
                $contacts = $request->input('contacts');

                if (!$name || strlen($idcard) <= 8 || strlen($email) < 4 || strlen($address) < 5) {
                    return Library::output(1);
                }

                $type = $tp == "company" ? 1 : 2;
                $info = array(
                    'name'        => $name,
                    'idcard'      => $idcard,
                    'email'       => $email,
                    'address'     => $address,
                    'pic_version' => time(),
                    'type'        => $type,
                );

                if ($type == 1) {
                    if (!$contacts) {
                        return Library::output(1);
                    }
                    $info['contacts'] = $contacts;
                } else {
                    $info['contacts'] = $name;
                }

                $accountModel = new AccountCenter();
                $userInfoArr  = $accountModel->info($user['uid'], $user['token']);
                if (empty($userInfoArr['data'])) {
                    return Library::output(1);
                }
                $mobile = $userInfoArr['data']['bindmobile'] == '' ? '' : $userInfoArr['data']['bindmobile'];
                if (!$mobile) {
                    return Library::output(1);
                }
                $devInfo = $this->model->getUser($user['uid']);
                if (!$devInfo) {
                    $ret = $this->model->addUser($user['uid'], $info);
                    $this->sendMail($user['uid'], $info['name'], $info['email']);
                } else {
                    if ($devInfo['stat'] == 1) {
                        return Library::output(1);
                    }
                    unset($info['type']);
                    if ($devInfo['stat'] == 3) {
                        $info['msg']  = '';
                        $info['stat'] = 1;
                    }
                    if ($devInfo['email'] != $info['email']) {
                        $info['stat'] = 0;
                        $this->sendMail($user['uid'], $info['name'], $info['email']);
                    }
                    $ret = $this->model->updateUser($user['uid'], $info);
                }

                break;
            case 'vrgame':
                $appid        = intval($request->input('game_id'));
                $game_logo    = $request->input('game_logo');
                $game_pay_url = $request->input('game_pay_url');
                if ($game_logo) {
                    $gameInfo = $this->model->getGameById($appid);
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
                    $ret                = $this->model->updateGameInfo($appid, $arr);
                } else if ($game_pay_url) {
                    $url_test = $request->input('game_pay_url_test');
                    $ret      = $this->model->updatePayUrl($appid, $game_pay_url, $url_test);
                    if ($ret) {
                        $accountModel = new AccountCenter();
                        $accountModel->setAppInfo($appid, array('payurl' => $game_pay_url, 'payurltest' => $url_test, 'use_default' => 1));
                    }
                } else {
                    $arr                     = [];
                    $arr['uid']              = $user['uid'];
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
                    if (!$arr['uid'] || !$arr['name'] || !$arr['tags'] || !$arr['first_class'] || !$arr['support'] || !$arr['content']) {
                        return Library::output(1);
                    }
                    $arr['recomm_device'] = json_encode(['system' => $system, 'cpu' => $cpu, 'memory' => $memory, 'directx' => $directx, 'graphics' => $graphics]);
                    if (!$appid) {
                        $ck = $this->model->checkGameName($arr['name']);
                        if ($ck) {
                            return Library::output(2506);
                        }
                        $ret = $this->model->addGameInfo($arr);
                        if ($ret) {
                            $accountModel = new AccountCenter();
                            $accountModel->setAppInfo($ret, array('appid' => $ret));
                        }
                    } else {
                        $ck = $this->model->checkGameName($arr['name'], $appid);
                        if ($ck) {
                            return Library::output(2506);
                        }
                        $ret = $this->model->updateGameInfo($appid, $arr);
                    }
                }
                break;
            case 'vrgame_agreement':
                $agreement = $_POST;
                unset($agreement['id']);
                if ($agreement && is_array($agreement) && count($agreement) > 30) {
                    $info['agreement'] = json_encode($agreement);
                    $ret               = $this->model->updateGameInfo($id, $info);
                }

                break;
            case 'vrgame_copyright':
                $param['soft']    = $request->input('soft');
                $param['record']  = $request->input('record');
                $param['publish'] = $request->input('publish');
                $soft             = $param['soft'] ? $param['soft'] : "";
                $record           = $param['record'] ? $param['record'] : "";
                $publish          = $param['publish'] ? $param['publish'] : "";

                $info['cp_soft']    = $soft;
                $info['cp_record']  = $record;
                $info['cp_publish'] = $publish;
                $ret                = $this->model->updateGameInfo($id, $info);
                break;
            case 'vrgame_version':
                $versionName     = $request->input('version_name');
                $versionDesc     = $request->input('version_desc');
                $versionStartExe = $request->input('version_start_exe');
                $versionId       = $request->input('version_id');
                $versionModel    = new VersionModel;
                if ($versionId) {
                    $ret = $versionModel->chooseSubVersion($id, $versionName, $versionId);
                } else {
                    $versions = $versionModel->getVersions($id, ['version_name' => $versionName]);
                    if ($versions) {
                        // if ($versions[0]['stat'] != 0) {
                        //     return Library::output(3302);
                        // } else {
                        $ret = $versionModel->updateVersion($id, $versionName, ['version_desc' => $versionDesc, 'version_start_exe' => $versionStartExe]);
                        //}
                    } else {
                        $versions = $versionModel->getVersions($id, ['stat' => 0]);
                        if ($versions) {
                            return Library::output(3302);
                        }
                        $ret = $versionModel->addVersion($id, ['version_name' => $versionName, 'version_desc' => $versionDesc, 'version_start_exe' => $versionStartExe]);
                    }
                }

                break;
        }
        return Library::output(0);
    }

    public function del(Request $request, $name)
    {
        $user = $request->userinfo;
        $id   = intval($request->input('id'));
        switch ($name) {
            case 'vrgame_version':
                $versionName  = $request->input('version_name');
                $versionModel = new VersionModel;
                $ret          = $versionModel->delVersion($id, $versionName);
                break;
        }
        return Library::output(0);
    }

    public function submit(Request $request, $name)
    {
        $user = $request->userinfo;
        $id   = intval($request->input('id'));
        switch ($name) {
            case 'vrgame_version':
                $versionName  = $request->input('version_name');
                $versionModel = new VersionModel;
                $ret          = $versionModel->publishversion($id, $versionName);
                break;
            case 'dev_mail':
                $devInfo = $this->model->getUser($user['uid']);
                if (!$devInfo || $devInfo['stat'] != 0) {
                    return Library::output(1);
                }
                $ret = $this->sendMail($user['uid'], $devInfo['name'], $devInfo['email']);
                if ($ret) {
                    return Library::output(0);
                } else {
                    return Library::output(1);
                }
                break;
            case 'vrgame_review':
                $appInfo = $this->model->getGameById($id);
                if (!$appInfo || $appInfo['uid'] != $user['uid']) {
                    return Library::output(1);
                }
                if ($appInfo['is_deal'] != 1 || $appInfo['img_version'] < 1 || strlen($appInfo['img_slider']) < 1) {
                    return Library::output(1, '', "资料未填写完整");
                }

                $ret = $this->model->updateGameInfo($id, array('stat' => 1));
                break;
            case 'vrgame_publish':
                $appInfo = $this->model->getGameById($id);
                if (!$appInfo || $appInfo['uid'] != $user['uid']) {
                    return Library::output(1);
                }
                if ($appInfo['stat'] != 5) {
                    return Library::output(2501);
                }
                if ($appInfo['send_time'] > 0) {
                    return Library::output(2502);
                }

                $info = array('send_time' => time());
                $ret  = $this->model->updateGameInfo($id, $info);

                $webModel = new WebgameModel;
                $ret      = $webModel->setGameInfo($id, $info);
                break;
        }
        return Library::output(0);
    }

    private function sendMail($uid, $name, $email)
    {
        if (!$uid || !$email || !$name) {
            return false;
        }
        $this->model->setActiveEmailCode($uid);
        $code       = $this->model->getActiveEmailCode($uid);
        $msgDataArr = array(
            'uid'        => $uid,
            'title'      => '请点击邮件里激活链接，激活您的账号！',
            'activeCode' => $code,
        );
        $ret = $this->model->sendVerifyMail($email, $name, $msgDataArr);
        return $ret;
    }
}
