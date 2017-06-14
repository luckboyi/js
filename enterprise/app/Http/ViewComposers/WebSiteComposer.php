<?php

namespace App\Http\ViewComposers;

use Agent;
use App;
use App\Models\CheckUpdateModel;
use Config;
use Cookie;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use \App\Models\UserModel as User;

class WebSiteComposer
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function compose(View $view)
    {
        if (!$view->isset) {
            $uid     = Cookie::get("uid");
            $token   = Cookie::get("token");
            $account = Cookie::get("account");
            $nick    = Cookie::get("nick");
            $face    = Cookie::get("face");

            $checkUpdateModel = new CheckUpdateModel;
            $clientInfo = $checkUpdateModel->oldClient("updateol");
            $client     = $clientInfo['address'];

            $this->setPlatform($platform);

            if ($account == "zy1234") {
                $platform = "pc";
            }

            $isset = 1;

            $view->with(compact("platform", "uid", "nick", "token", "face", "account", "client", "isset"));
        }
    }

    public function setPlatform(&$platform)
    {
        $platform        = "web";
        $ret             = Agent::match('VRonlinePlat');
        $HttpGetPlatform = $this->request->input("platform");
        if ($ret || $HttpGetPlatform == "pc") {
            $platform = "pc";
        }
    }
}
