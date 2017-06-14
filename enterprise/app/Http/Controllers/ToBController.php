<?php

// ToB  平台账号申请体验店账号

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\ToBDBModel;
use App\Models\ToBStoreModel;
use Config;
use Cookie;
use Helper\AccountCenter as Account;
use Helper\Library;
use Illuminate\Http\Request;

class ToBController extends Controller
{
    use SimpleResponse;

    public function __construct()
    {
        $this->middleware("vrauth:0:toblogin", ['only' => ["login"]]);
        $this->middleware("vrauth:jump", ['only' => ["enter", "validateEmail", "wait"]]);
        $this->middleware("vrauth:json", ['only' => ["sendEmail", "apply", "submit"]]);
    }

    public function index(Request $request)
    {
        $uid     = Cookie::get("uid");
        $account = Cookie::get("account");

        $merchantInfo = "";
        $ToBDB        = new ToBDBModel();

        $banners = $ToBDB->getWwwBanners();

        if ($uid) {
            $merchantInfo = $ToBDB->get2bMerchant($uid);
        }
        return view('tob.home', compact("uid", "account", "merchantInfo", "banners"));
    }

    /**
     * 填写体验店资料
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function enter(Request $request)
    {
        $userinfo = $request->userinfo;
        $uid      = $userinfo["uid"];
        $token    = $userinfo["token"];

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $userInfoArr  = $accountModel->info($uid, $token);

        //获取用户是否绑定手机号的逻辑
        if (empty($userInfoArr['data'])) {
            return $this->error404(1, "无法取得用户信息");
        }

        $bindMobile = $userInfoArr['data']['bindmobile'] ?? "";

        $ToBDB        = new ToBDBModel();
        $merchantInfo = $ToBDB->get2bMerchant($uid);

        $edit = $request->input("edit");
        if ($merchantInfo && !$edit) {
            return redirect("validateEmail", 302, [], true);
        }

        if (in_array($merchantInfo["status"], [7, 9])) {
            return redirect("validateEmail", 302, [], true);
        }

        $idCardPath  = ImageHelper::path('merchant_idcard', $uid)["base"];
        $licensePath = ImageHelper::path('merchant_license', $uid)["base"];

        $allAddress     = $merchantInfo['address'] ?? "";
        $tmpAddr        = explode("|", $allAddress);
        $province       = $tmpAddr[0] ?? "";
        $city           = $tmpAddr[1] ?? "";
        $address        = $tmpAddr[2] ?? "";
        $merchant       = $merchantInfo['merchant'] ?? "";
        $license        = $merchantInfo['license'] ?? "";
        $idcard         = $merchantInfo['idcard'] ?? "";
        $email          = $merchantInfo['email'] ?? "";
        $tel            = $merchantInfo['tel'] ?? "";
        $contact        = $merchantInfo['contact'] ?? "";
        $idCardPreview  = '<div class="preview preview-idcard" style="display:none"></div>';
        $licensePreview = '<div class="preview preview-license" style="display:none"></div>';
        if ($merchantInfo) {
            $idCardPreview  = '<div class="preview preview-idcard"><a href="' . $idCardPath . '" target="_blank"><img src="' . $idCardPath . '" width="100%" height="100%"/></a></div>';
            $licensePreview = '<div class="preview preview-license"><a href="' . $licensePath . '" target="_blank"><img src="' . $licensePath . '" width="100%" height="100%"/></a></div>';
        }

        $data = [
            "bindMobile"     => $bindMobile,
            "user"           => $userinfo,
            "province"       => $province,
            "city"           => $city,
            "address"        => $address,
            "merchant"       => $merchant,
            "license"        => $license,
            "idcard"         => $idcard,
            "email"          => $email,
            "tel"            => $tel,
            "contact"        => $contact,
            "idCardPreview"  => $idCardPreview,
            "licensePreview" => $licensePreview,
            "edit"           => $edit,
        ];

        return view('tob.information', $data);

    }

    /**
     * 上传用户资料
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function apply(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
        $token    = $userInfo["token"];

        $appid        = Config::get("common.uc_appid");
        $appkey       = Config::get("common.uc_appkey");
        $accountModel = new Account($appid, $appkey);
        $userInfoArr  = $accountModel->info($uid, $token);

        //获取用户是否绑定手机号的逻辑
        if (empty($userInfoArr['data'])) {
            return Library::output(1, "无法取得用户信息");
        }

        $account  = $userInfo["account"];
        $merchant = $request->input('merchantName');
        $license  = $request->input('license');
        $contact  = $request->input('connector');
        $idCard   = $request->input('idCard');
        $email    = $request->input('email');
        $province = $request->input('province');
        $city     = $request->input('city');
        $address  = $request->input('address');
        $tel      = $request->input('mobile');

        $addressAll = $province . '|' . $city . '|' . $address;
        if (!$uid || !$account || !$merchant || !$license || !$contact || strlen($idCard) < 10 || strlen($email) < 4 || strlen($addressAll) < 5 || !$tel) {
            return Library::output(1);
        }

        $merchantInfoArr = array(
            'account'  => $account,
            'merchant' => $merchant,
            'contact'  => $contact,
            "license"  => $license,
            'idcard'   => $idCard,
            'tel'      => $tel,
            'email'    => $email,
            'address'  => $addressAll,
        );

        $sendMail     = false;
        $ToBDB        = new ToBDBModel();
        $merchantInfo = $ToBDB->get2bMerchant($uid);

        if (empty($merchantInfo)) {
            $sendMail = true;
            $result   = $ToBDB->add2bMerchant($uid, $merchantInfoArr);
        } else {
            if (in_array($merchantInfo["status"], [7, 9])) {
                return Library::output(3001);
            }
            if ($merchantInfo['email'] != $email) {
                $merchantInfoArr['email_verfy'] = 0;
                $sendMail                       = true;
            }
            $merchantInfoArr['status'] = 1;
            $result                    = $ToBDB->upd2bMerchant($uid, $merchantInfoArr);
        }
        $sendMail = true;
        if ($result) {
            if ($sendMail) {
                $ToBStore = new ToBStoreModel;
                $ToBStore->setActiveEmailCode($uid);
                $activeCode = $ToBStore->getActiveEmailCode($uid);
                $msgDataArr = array(
                    'uid'        => $uid,
                    'title'      => '请点击邮件里激活链接，激活您的账号！',
                    'activeCode' => $activeCode,
                );
                $ToBStore->sendVerifyMail($email, $contact, $msgDataArr);
            }
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    /**
     * 邮件验证
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function validateEmail(Request $request)
    {
        $user = $request->userinfo;
        $uid  = $user["uid"];

        $ToBDB    = new ToBDBModel();
        $merchant = $ToBDB->get2bMerchant($uid);

        if (!$merchant) {
            return redirect("/enter", 302, [], true);
        }

        if (in_array($merchant["status"], [5, 7, 9])) {
            return redirect("/wait", 302, [], true);
        }

        return view('tob.authEmail', compact("user", "merchant"));
    }

    /**
     * 发送验证邮件接口
     *
     * @param  Request $request [description]
     * @return json             [description]
     */
    public function sendEmail(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        $ToBDB        = new ToBDBModel();
        $merchantInfo = $ToBDB->get2bMerchant($uid);

        if (!$merchantInfo) {
            return Library::output(3003);
        }

        if ($merchantInfo["email_verfy"] == 1) {
            return Library::output(3002);
        }

        $email   = $merchantInfo["email"];
        $contact = $merchantInfo["contact"];
        if (!$email || !$contact) {
            return Library::output(3004);
        }
        $ToBStore = new ToBStoreModel;
        $ToBStore->setActiveEmailCode($uid);
        $activeCode = $ToBStore->getActiveEmailCode($uid);
        $msgDataArr = array(
            'uid'        => $uid,
            'title'      => '请点击邮件里激活链接，激活您的账号！',
            'activeCode' => $activeCode,
        );
        $ToBStore->sendVerifyMail($email, $contact, $msgDataArr);

        return Library::output(0);
    }

    /**
     * 邮件激活页面
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function activeEmail(Request $request)
    {
        $requestUid = $request->input('uid');

        $uid = $requestUid;

        $ToBDB    = new ToBDBModel();
        $ToBStore = new ToBStoreModel;

        $merchantInfo = $ToBDB->get2bMerchant($uid);

        if (empty($merchantInfo)) {
            return view('tob.active', ["msg" => "没有查找到相关的体验店信息"]);
        }

        $name  = $merchantInfo['account'];
        $email = $merchantInfo['email'];

        view()->share(["nologin" => 1]);

        $activeCode = $request->input('activeCode');
        if (!$activeCode) {
            return view('tob.active', ["msg" => "验证失败，请通过邮件中的地址访问"]);
        }

        if ($merchantInfo['email_verfy'] === 1) {
            return view('tob.active', ["msg" => "邮箱 {$email}，验证成功", "success" => 1]);
        }

        $code = $ToBStore->getActiveEmailCode($uid);

        if ($code === $activeCode) {
            $ToBStore->delActiveEmailCode($uid);
            $infoArr = array(
                'email_verfy' => 1,
            );
            $ret = $ToBDB->upd2bMerchant($uid, $infoArr);
            if (!$ret) {
                return view('tob.active', ["msg" => "验证链接已经失效，请重新发送邮件"]);
            }
            return view('tob.active', ["msg" => "邮箱 {$email}，验证成功", "success" => 1]);
        } else {
            return view('tob.active', ["msg" => "验证链接已经失效，请重新发送邮件"]);
        }
    }

    /**
     * 提交审核
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function submit(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        $ToBDB        = new ToBDBModel();
        $merchantInfo = $ToBDB->get2bMerchant($uid);

        if (!$merchantInfo) {
            return Library::output(3003);
        }

        if ($merchantInfo["email_verfy"] != 1) {
            return Library::output(3005);
        }

        if (in_array($merchantInfo["status"], [7, 9])) {
            return Library::output(3006);
        }

        $merchantInfoArr['status'] = 7;

        $result = $ToBDB->upd2bMerchant($uid, $merchantInfoArr);
        if (!$result) {
            return Library::output(1);
        }
        return Library::output(0);
    }

    /**
     * 等待审核页面
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function wait(Request $request)
    {
        $user = $request->userinfo;
        $uid  = $user['uid'];

        $ToBDB        = new ToBDBModel();
        $merchantInfo = $ToBDB->get2bMerchant($uid);
        if (!$merchantInfo) {
            return redirect("/enter", 302, [], true);
        }

        if (!$merchantInfo["email_verfy"]
            || in_array($merchantInfo["status"], [1, 2])) {
            return redirect("validateEmail", 302, [], true);
        }

        return view('tob.wait', compact("user", "merchantInfo"));
    }

    /**
     * 登入页面
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function login(Request $request)
    {
        return view('tob.login');
    }

}
