<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\AdmincpModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;
use Session;
use \App\Models\CookieModel;

class SysController extends Controller
{

    public function __construct()
    {

        $this->middleware("vrauth:jump:admincp", ['only' => ["index", "user", "group"]]);
        $this->middleware("vrauth:jump:admincp_login", ['only' => ["login"]]);
    }

    public function index(Request $request)
    {
        $userInfo = $request->userinfo;
        if ($userInfo['nextPath']) {
            return redirect($userInfo['nextPath']);
        } else {
            return redirect('index/help', 302, [], true);
        }
    }

    public function user(Request $request)
    {
        $userInfo     = $request->userinfo;
        $admincpModel = new AdmincpModel;
        $data         = $admincpModel->sysUserPage();
        $groups       = $admincpModel->sysUserGroup();
        foreach ($groups as $group) {
            $groupData[$group['id']] = $group;
        }
        return view('admincp.sys.user', ['cur' => 'sys', 'path' => 'user', 'user' => $userInfo, 'data' => $data, 'groups' => $groupData]);
    }

    public function group(Request $request)
    {
        $userInfo     = $request->userinfo;
        $admincpModel = new AdmincpModel;
        $data         = $admincpModel->sysUserGroup();
        return view('admincp.sys.group', ['cur' => 'sys', 'path' => 'group', 'user' => $userInfo, 'data' => $data]);
    }

    public function login()
    {
        return view('admincp.login', []);
    }

    public function forget()
    {
        return view('admincp.forget', ['msg' => "发送邮件，重新设定密码"]);
    }

    public function sendMail(Request $request)
    {
        $email = $request->input('account');

        $admincpModel = new AdmincpModel;
        $userInfo     = $admincpModel->getEmailCode($email);
        if (!$userInfo) {
            return Library::output(1111);
        }
        $data = [
            'uid'   => $userInfo['id'],
            'title' => "VRONLINE 运营ADMINCP",
            'code'  => $userInfo['code'],
        ];
        $ret = $admincpModel->sendMail($email, $userInfo['name'], $data);
        return Library::output(0);
    }

    public function setpwd(Request $request)
    {
        $uid  = $request->input('uid');
        $code = $request->input('code');

        $admincpModel = new AdmincpModel;
        $userInfo     = $admincpModel->checkEmailCode($uid, $code);
        if ($userInfo) {
            $email = $userInfo['account'];
            $name  = $userInfo['name'];
            return view('admincp.setpwd', compact('uid', 'code', 'email', 'name'));
        } else {
            return view('admincp.forget', ['msg' => "链接已经失效，请重新发送邮件"]);
        }
    }

    public function resetPwd(Request $request)
    {
        $uid          = $request->input('uid');
        $code         = $request->input('code');
        $pwd          = $request->input('password');
        $admincpModel = new AdmincpModel;
        $hash         = Config::get("admincp.hash");
        $pwd          = md5(md5($pwd . $hash));
        $ret          = $admincpModel->updateSysPwdByEmail($uid, $code, $pwd);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1, null, '链接已经失效，请重新发送邮件');
        }
    }

    public function loginOut()
    {
        $params = array('admin_uid', 'admin_name', 'admin_account');
        CookieModel::clearCookieArr($params);
        Session::flush();
        return redirect('/login', 302, [], true);
    }

    public function loginSubmit(Request $request)
    {
        $account  = trim($request->input('account'));
        $password = trim($request->input('password'));

        $admincpModel = new AdmincpModel;
        $row          = $admincpModel->sysUser($account);
        if ($row) {
            $hash = Config::get("admincp.hash");
            if (md5(md5($password . $hash)) != $row['password']) {
                return Library::output(1303);
            } else {
                $this->setLoginCookie(['uid' => $row['id'], 'name' => $row['name'], 'account' => $row['account']]);
                return Library::output(0);
            }
        } else {
            return Library::output(1111);
        }
    }

    private function setLoginCookie($arr)
    {
        $params                  = array();
        $params['admin_uid']     = $arr['uid'];
        $params['admin_name']    = $arr['name'];
        $params['admin_account'] = $arr['account'];
        $expire                  = 0;
        CookieModel::setCookieArr($params, $expire);
    }

}
