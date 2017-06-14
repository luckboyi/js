<?php
namespace App\Http\Controllers\Admincp;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdmincpModel;
use App\Models\NewCommentDBModel;
use App\Models\VronlineModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class VronlineController extends Controller
{
    private $perPage = 20;
    public function __construct()
    {
        $this->middleware("vrauth:jump:admincp", ['only' => ["index", "news", "newsEdit", "pc", "pcEdit", "video", "game", "getGameImg", "addGameImg", "delGameImg", "top", "position"]]);
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

    public function news(Request $request)
    {
        $class_id   = intval($request->input("choose"));
        $search     = $request->input("search");
        $curClass   = $class_id ? $class_id : 0;
        $searchText = $search ? $search : '';
        $newsModel  = new VronlineModel;
        $articles   = $newsModel->getDevNews('news', $curClass, $searchText);
        $userInfo   = $request->userinfo;
        return view('admincp.vronline.news', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'news', 'data' => $articles, 'curClass' => $curClass, 'searchText' => $searchText]);
    }

    public function newsEdit(Request $request, $id = 0)
    {
        $userInfo = $request->userinfo;
        return view('admincp.vronline.news_edit', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'news', 'id' => $id]);
    }

    public function pc(Request $request)
    {
        $class_id   = intval($request->input("choose"));
        $search     = $request->input("search");
        $curClass   = $class_id ? $class_id : 0;
        $searchText = $search ? $search : '';
        $newsModel  = new VronlineModel;
        $articles   = $newsModel->getDevNews('pc', $curClass, $searchText);
        $userInfo   = $request->userinfo;
        return view('admincp.vronline.pc', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'pc', 'data' => $articles, 'curClass' => $curClass, 'searchText' => $searchText]);
    }

    public function pcEdit(Request $request, $id = 0)
    {
        $userInfo = $request->userinfo;
        return view('admincp.vronline.pc_edit', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'pc', 'id' => $id]);
    }

    public function video(Request $request)
    {
        $class_id   = intval($request->input("choose"));
        $search     = $request->input("search");
        $curClass   = $class_id ? $class_id : 0;
        $searchText = $search ? $search : '';
        $newsModel  = new VronlineModel;
        $articles   = $newsModel->getDevNews('video', $curClass, $searchText);
        $userInfo   = $request->userinfo;
        return view('admincp.vronline.video', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'video', 'data' => $articles, 'curClass' => $curClass, 'searchText' => $searchText]);
    }

    public function game(Request $request)
    {
        $userInfo = $request->userinfo;

        $search        = $request->input("search");
        $searchText    = $search ? $search : '';
        $vronlineModel = new VronlineModel;
        $data          = $vronlineModel->getGamesList($searchText);
        return view('admincp.vronline.game', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'game', 'data' => $data, 'searchText' => $searchText]);
    }

    public function getGameImg(Request $request)
    {
        $userInfo      = $request->userinfo;
        $gameId        = $request->input("game_id");
        $vronlineModel = new VronlineModel;
        $imgArr        = $vronlineModel->getGameImg($gameId);

        if (isset($imgArr) && count($imgArr) > 0) {
            return Library::output(0, $imgArr);
        }
        return Library::output(1);
    }
    public function addGameImg(Request $request)
    {
        $userInfo      = $request->userinfo;
        $gameId        = $request->input("game_id");
        $url           = $request->input("game_pic_url");
        $vronlineModel = new VronlineModel;
        $ret           = $vronlineModel->addGamePic($gameId, $url);
        if (is_array($ret)) {
            return Library::output(0, ['id' => $ret[0]['id']]);
        }
        return Library::output(1);
    }
    public function delGameImg(Request $request)
    {
        $userInfo      = $request->userinfo;
        $id            = $request->input("id");
        $vronlineModel = new VronlineModel;
        $ret           = $vronlineModel->deleteGamePic($id);
        if ($ret) {
            return Library::output(0);
        }
        return Library::output(1);
    }

    public function search(Request $request)
    {
        $tp    = $request->input('tp');
        $query = $request->input('q');

        $vronlineModel = new VronlineModel;
        $res           = $vronlineModel->searchContent($tp, $query);
        $arr           = [];
        if ($res) {
            $arr = $res;
        }
        $out = ['results' => $arr];
        return json_encode($out);
    }

    public function top(Request $request)
    {
        $userInfo = $request->userinfo;
        $posCode  = $request->input('choose');

        $groups        = Config::get("admincp.vronline_pos_group");
        $vronlineModel = new VronlineModel;
        $poses         = $vronlineModel->position('', 0);
        $posGroup      = [];
        $posName       = '';
        $defaultGroup  = '';
        foreach ($poses as $key => $value) {
            if (!isset($posGroup[$value['pos_group']])) {
                $posGroup[$value['pos_group']] = [];
            }
            $posGroup[$value['pos_group']][] = $value;
            if (!$posCode) {
                $posCode      = $value['pos_code'];
                $posName      = $value['pos_name'];
                $defaultGroup = $value['pos_group'];
            } else {
                if ($value['pos_code'] == $posCode) {
                    $posName      = $value['pos_name'];
                    $defaultGroup = $value['pos_group'];
                }
            }
        }

        $data = $vronlineModel->topByCode($posCode);

        $articleIds = [];
        foreach ($data as $key => $value) {
            if ($value['tp'] == 'news' || $value['tp'] == 'video' || $value['tp'] == 'pc') {
                $articleIds[] = $value['itemid'];
            } else if ($value['tp'] == 'game') {
                $gameIds[] = $value['itemid'];
            }
        }

        $articles = [];
        if (isset($articleIds)) {
            $articleData = $vronlineModel->getArticlesByIds($articleIds);
            foreach ($articleData as $value) {
                $articles[$value['article_id']] = $value;
            }
        }

        $games = [];
        if (isset($gameIds)) {
            $gameData = $vronlineModel->getGamesByIds($gameIds);
            foreach ($gameData as $game) {
                $games[$game['game_id']] = $game;
            }
        }

        return view('admincp.vronline.top', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'top', 'groups' => $groups, 'posName' => $posName, 'posGroup' => $posGroup, 'defaultGroup' => $defaultGroup, 'posCode' => $posCode, 'articles' => $articles, 'games' => $games, 'data' => $data]);
    }

    public function switchWeight(Request $request)
    {
        Library::accessHeader();
        $dragId = intval($request->input('drag'));
        $dropId = intval($request->input('drop'));

        $vronlineModel = new VronlineModel;
        $ret           = $vronlineModel->topPostionWeight($dragId, $dropId);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }
    public function position(Request $request)
    {
        $userInfo      = $request->userinfo;
        $choose        = trim($request->input('choose'));
        $vronlineModel = new VronlineModel;
        $data          = $vronlineModel->position($choose);
        return view('admincp.vronline.position', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'position', 'choose' => $choose, 'data' => $data]);
    }

}
