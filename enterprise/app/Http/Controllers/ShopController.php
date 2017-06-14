<?php
namespace App\Http\Controllers;

use App;
use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\DeveloperModel;
use App\Models\VersionModel;
use App\Models\WebgameModel;
use Helper\Library;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:json:api", ['only' => ["gameStat", "myGame"]]);
    }

    /**
     * [gameStat 获取游戏状态]
     * @param  Request $request [description]
     * @param  [int]  $appid   [游戏id]
     * @param  [int]  $uid   [uid]
     * @param  [string]  $token   [token]
     * @return [json]           [description]
     */
    public function gameStat(Request $request, $appid)
    {
        if (!is_numeric($appid)) {
            return Library::output(1);
        }
        $uid    = $request->userinfo['uid'];
        $isTest = (bool) $request->input('is_test');

        $gameModel = new WebgameModel;
        $gameInfo  = $gameModel->getOneGameInfo($appid);
        if (!$gameInfo) {
            $developerModel = new DeveloperModel;
            $gameInfo       = $developerModel->getGameById($appid);
        }

        if (!$gameInfo) {
            return Library::output(2304);
        }

        if ($gameInfo['sell'] == 0) {
            $isBuy  = 1;
            $isFree = 1;
        } else {
            $isFree = 0;
            $game   = $gameModel->getOneGameLog($uid, $appid);
            if (!$game) {
                $isBuy = 0;
            } else {
                $isBuy = 1;
            }
        }
        $out = ['appid' => $appid, 'buy' => $isBuy, 'free' => $isFree];
        if ($isBuy == 1) {
            if ($gameInfo['version_code'] <= 1478862445) {
                $out['old']    = 1;
                $out['prefix'] = 'http://down.vrgame.vronline.com/dev/' . $appid . '/' . $gameInfo['version_code'];
            } else {
                $VersionModel = new VersionModel;
                $res          = $VersionModel->downGame($appid, $isTest);
                if ($res) {
                    $out['prefix'] = 'http://down.vrgame.vronline.com/' . $appid . "/";
                    $out['old']    = 0;
                    if ($isTest) {
                        $out['version'] = $res['dev'];
                    } else {
                        $out['version'] = $res['publish'];
                    }
                }
            }
        }
        return Library::output(0, $out);
    }

    public function gameXml(Request $request, $appid)
    {
        if (!is_numeric($appid)) {
            return Library::output(1);
        }
        $uid          = $request->userinfo['uid'];
        $isTest       = (bool) $request->input('is_test');
        $VersionModel = new VersionModel;
        $res          = $VersionModel->downGame($appid, $isTest);
        $out          = [];
        if ($res) {
            $prefix = 'http://down.vrgame.vronline.com/' . $appid . "/";
            if ($isTest) {
                $version = $res['dev'];
            } else {
                $version = $res['publish'];
            }
            $out['files'] = $res['files'];
        }
        $startExe = $res['exe'];
        $str      = '<?xml version="1.0"?>' . "\n";
        $str .= '<Content ver="' . $version . '"  prefix="' . $prefix . '" startexe="' . $startExe . '">' . "\n";
        foreach ($out['files'] as $key => $file) {
            $str .= '<file filename="' . $key . '" size="' . $file['size'] . '" md5="' . $file['md5'] . '" zipSize="' . $file['zipSize'] . '" zipMd5="' . $file['zipMd5'] . '">' . $file['version_id'] . '/' . $key . '</file>' . "\n";
        }
        $str .= '</Content>';
        return response()->make($str, '200')->header('Content-Type', 'text/xml');
    }

    /**
     * [myGame 我的游戏]
     * @param  Request $request [description]
     * @param  [int]  $uid   [uid]
     * @param  [string]  $token   [token]
     * @return [json]           [description]
     */
    public function myGame(Request $request)
    {
        $uid = $request->userinfo['uid'];

        $gameModel = new WebgameModel;
        $games     = $gameModel->getGameLog($uid, 'all', 1);
        if (!$games) {
            return Library::output(0);
        } else {
            foreach ($games as $value) {
                $out[] = ['appid' => $value['appid'], 'appname' => $value['appname']];
            }
            return Library::output(0, $out);
        }
    }

    /**
     * [buyGame 购买游戏]
     * @param  Request $request [description]
     * @param  [type]  $appid   [游戏id]
     * @return [json]           [description]
     */
    public function buyGame(Request $request, $appid)
    {

    }

}
