<?php
namespace App\Http\Controllers\Developer;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use Config;
use Helper\AccountCenter;
use Helper\Library;
use Illuminate\Http\Request;
use \App\Models\DeveloperModel;
use \App\Models\VersionModel;

class DeveloperController extends Controller
{
    public function __construct()
    {
        $this->model = new DeveloperModel;
        $this->middleware("vrauth:0:openlogin:https", ['only' => ["index"]]);
        $this->middleware("vrauth:jump:dev", ['only' => ["vrGame", "vrGameDetail", "user", "setting", "vrGameAgreement"]]);
        $this->middleware("vrauth:jump", ['only' => ["sign", "signFill", "signEmail", "activeEmail", "signWait", "signReject", "signSuccess"]]);
    }

    public function index(Request $request)
    {
        $userInfo = $request->userinfo;

        $referer = $request->input('referer');
        if (isset($userInfo['uid'])) {
            $loginStyle  = 'style="display:none"';
            $loginStyle2 = 'style="display:block"';
        } else {
            $loginStyle  = '';
            $loginStyle2 = '';
            $userInfo    = array('nick' => '', 'face' => '');
        }

        return view('open.login', ['referer' => $referer, 'style1' => $loginStyle, 'style2' => $loginStyle2, 'nick' => $userInfo['nick'], 'face' => $userInfo['face']]);
    }

    public function vrGame(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        $choose = $request->input('choose');
        $choose = $choose ? $choose : "";

        $search = $request->input('search');
        $search = $search ? $search : "";

        $data = $this->model->getGameByUid($uid, 1, ['choose' => $choose, 'search' => $search]);
        return view("open.admin.game", ['user' => $userInfo, 'cur' => 'vrgame', 'data' => $data, 'choose' => $choose, 'search' => $search]);
    }

    public function vrGameDetail(Request $request, $id)
    {
        $userInfo = $request->userinfo;
        $data     = $this->model->getGameById($id);
        if (!$data) {
            return redirect('/', 302, [], true);
        }
        $accountModel = new AccountCenter();

        $keyInfo = $accountModel->getAppInfo($id);
        if (!$keyInfo) {
            return redirect('/', 302, [], true);
        }
        $data['appkey'] = $keyInfo['appkey'];
        $data['paykey'] = $keyInfo['paykey'];

        return view("open.admin.detail", ['user' => $userInfo, 'cur' => 'vrgame', 'id' => $id, 'data' => $data]);
    }

    public function vrGameAgreement(Request $request, $id)
    {
        $userInfo = $request->userinfo;
        $data     = $this->model->getGameById($id);
        if ($data['is_deal'] == 1) {
            $data['agreement_type'] = "";
        } else {
            $data['agreement_type'] = "input";
        }
        return view("open.admin.agreement", ['user' => $userInfo, 'cur' => 'vrgame', 'id' => $id, 'data' => $data]);
    }

    public function vrGameCopyright(Request $request, $id)
    {
        $userInfo = $request->userinfo;
        $data     = $this->model->getGameById($id);

        return view("open.admin.copyright", ['user' => $userInfo, 'cur' => 'vrgame', 'id' => $id, 'data' => $data]);
    }

    public function vrGameVersion(Request $request, $id)
    {
        $userInfo = $request->userinfo;

        $versionModel = new VersionModel;
        $data         = $versionModel->getVersions($id, [], 10);

        $gameInfo = $this->model->getGameById($id);
        $gameName = $gameInfo['name'];

        return view("open.admin.vrgame_version", ['user' => $userInfo, 'cur' => 'vrgame', 'id' => $id, 'data' => $data, 'gameName' => $gameName]);
    }

    public function user(Request $request)
    {
        $userInfo = $request->userinfo;

        $uid = $userInfo['uid'];

        $devInfo = $this->model->getUser($uid);
        if (empty($devInfo)) {
            return redirect('/', 302, [], true);
        }
        $devInfo['url'] = ImageHelper::getUrl('openuser', ['id' => $uid, 'version' => $devInfo['pic_version']]);
        return view("open.admin.user", ['user' => $userInfo, 'cur' => 'user', 'data' => $devInfo]);
    }

    public function setting(Request $request)
    {
        $userInfo = $request->userinfo;
        return view("open.admin.setting", ['user' => $userInfo, 'cur' => 'setting']);
    }

    public function stat(Request $request)
    {
        $userInfo = $request->userinfo;
        return view("open.admin.user", ['user' => $userInfo, 'cur' => 'vrgame']);
    }

    /**
     * [sign 注册开发者]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function sign(Request $request)
    {
        $userInfo = $request->userinfo;
        $devInfo  = $this->model->getUser($userInfo['uid']);
        $redirect = $this->signRedirect($devInfo, 'sign');
        if ($redirect) {
            return $redirect;
        }
        return view("open.sign.index", ['user' => $userInfo]);
    }

    public function signFill(Request $request, $tp = '')
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        $devInfo = $this->model->getUser($uid);
        if (!$devInfo) {
            if (!in_array($tp, ['company', 'user'])) {
                return redirect('/', 302, [], true);
            }
        } else {
            $tp       = $devInfo['type'] == 1 ? "company" : "user";
            $redirect = $this->signRedirect($devInfo, 'fill');
            if ($redirect) {
                return $redirect;
            }
        }

        $accountModel = new AccountCenter();
        $userInfoArr  = $accountModel->info($userInfo['uid'], $userInfo['token']);
        if (empty($userInfoArr['data'])) {
            return redirect('/', 302, [], true);
        }
        $mobile = $userInfoArr['data']['bindmobile'] == '' ? '' : $userInfoArr['data']['bindmobile'];
        return view("open.sign.fill", ['user' => $userInfo, 'tp' => $tp, 'mobile' => $mobile]);
    }

    public function signEmail(Request $request)
    {
        $userInfo = $request->userinfo;
        $devInfo  = $this->model->getUser($userInfo['uid']);
        $redirect = $this->signRedirect($devInfo, 'email');
        if ($redirect) {
            return $redirect;
        }
        return view("open.sign.wait", ['user' => $userInfo, 'dev' => $devInfo, 'status' => 'email']);
    }

    public function activeEmail(Request $request)
    {
        $uid      = $request->input('uid');
        $code     = $request->input('code');
        $userInfo = $request->userinfo;
        if ($userInfo['uid'] != $uid || !$code) {
            return redirect('/', 302, [], true);
        }
        $devInfo  = $this->model->getUser($uid);
        $redirect = $this->signRedirect($devInfo, 'active_email');
        if ($redirect) {
            return $redirect;
        }

        $myCode = $this->model->getActiveEmailCode($uid);
        if ($code === $myCode) {
            $info = array('stat' => 1);
            $ret  = $this->model->updateUser($uid, $info);
            if ($ret) {
                $this->model->delActiveEmailCode($uid);
                return redirect('/developer/sign/wait', 302, [], true);
            }
        }
        return view('open.sign.wait', ['user' => $userInfo, 'dev' => $devInfo, "status" => 'email_error']);
    }

    /**
     * [signWait 等待审核]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signWait(Request $request)
    {
        $userInfo = $request->userinfo;
        $devInfo  = $this->model->getUser($userInfo['uid']);
        $redirect = $this->signRedirect($devInfo, 'wait');
        if ($redirect) {
            return $redirect;
        }
        return view("open.sign.wait", ['user' => $userInfo, 'dev' => $devInfo, 'status' => 'wait']);
    }

    /**
     * [signReject 审核失败]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signReject(Request $request)
    {
        $userInfo = $request->userinfo;
        $devInfo  = $this->model->getUser($userInfo['uid']);
        $redirect = $this->signRedirect($devInfo, 'reject');
        if ($redirect) {
            return $redirect;
        }
        return view("open.sign.wait", ['user' => $userInfo, 'dev' => $devInfo, 'status' => 'reject', 'msg' => $devInfo['msg']]);
    }

    /**
     * [signSuccess 注册开发者成功]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signSuccess(Request $request)
    {
        $userInfo = $request->userinfo;
        $devInfo  = $this->model->getUser($userInfo['uid']);

        $redirect = $this->signRedirect($devInfo, 'success');
        if ($redirect) {
            return $redirect;
        }
        return view("open.sign.wait", ['user' => $userInfo, 'dev' => $devInfo, 'status' => 'success']);
    }

    private function signRedirect($devInfo, $no)
    {
        if (!isset($devInfo['stat'])) {
            if (!in_array($no, ["fill", "sign"])) {
                return redirect('/developer/sign', 302, [], true);
            }
        } else if ($devInfo['stat'] == 0) {
            if (!in_array($no, ["email", "active_email", "fill"])) {
                return redirect('/developer/sign/email', 302, [], true);
            }
        } else if ($devInfo['stat'] == 1) {
            if (!in_array($no, ["wait"])) {
                return redirect('/developer/sign/wait', 302, [], true);
            }
        } else if ($devInfo['stat'] == 3) {
            if (!in_array($no, ["reject", "fill"])) {
                return redirect('/developer/sign/reject', 302, [], true);
            }
        } else if ($devInfo['stat'] == 5) {
            if (!in_array($no, ["success"])) {
                return redirect('/developer/sign/success', 302, [], true);
            }
        }
        return false;
    }
}
