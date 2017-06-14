<?php
namespace App\Http\Controllers\Admincp;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\NewCommentDBModel;
use App\Models\NewsModel;
use Helper\Library;
use Illuminate\Http\Request;

class NewsController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:jump:admincp", ['only' => ["index", "article", "articleEdit", "articlePreview", "top", "topAdmin", "position", "comments"]]);
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

    public function article(Request $request)
    {

        $class_id   = intval($request->input("choose"));
        $search     = $request->input("search");
        $curClass   = $class_id ? $class_id : 0;
        $searchText = $search ? $search : '';
        $newsModel  = new NewsModel;
        $articles   = $newsModel->getDevNews($curClass, $searchText);
        $userInfo   = $request->userinfo;
        return view('admincp.news.article', ['cur' => 'news', 'user' => $userInfo, 'path' => 'article', 'data' => $articles, 'curClass' => $curClass, 'searchText' => $searchText]);
    }

    public function articlePreview(Request $request, $id = 0)
    {
        if (!is_numeric($id) || $id < 1) {
            return redirect('/news/', 302, [], true);
        }
        $newsModel = new NewsModel;
        $article   = $newsModel->getDevNewsById($id);

        $userInfo = $request->userinfo;
        return view('admincp.news.article_preview', ['cur' => 'news', 'user' => $userInfo, 'path' => 'article', 'data' => $article]);
    }

    public function articleEdit(Request $request, $id = 0)
    {
        $userInfo = $request->userinfo;
        return view('admincp.news.article_edit', ['cur' => 'news', 'user' => $userInfo, 'path' => 'article', 'id' => $id]);
    }

    public function top(Request $request)
    {
        $userInfo = $request->userinfo;

        $posid     = $request->input('posid');
        $newsModel = new NewsModel;

        $defaultTp = '';
        $newsPos   = $newsModel->getNewsAllTopPos();
        foreach ($newsPos as $key => $value) {
            if ($key == 0 && !$posid) {
                $posid       = $value['posid'];
                $defaultName = $value['name'];
                $defaultTp   = $value['content_tp'];
                break;
            } else if ($posid == $value['posid']) {
                $defaultName = $value['name'];
                $defaultTp   = $value['content_tp'];
                break;
            }
        }

        $data = $newsModel->postionData($posid);

        $articleIds = [];
        foreach ($data as $key => $value) {
            if ($value['tp'] == 'article') {
                $articleIds[] = $value['itemid'];
            }
        }

        $articles    = [];
        $articleData = $newsModel->getArticlesByIds($articleIds);
        foreach ($articleData as $value) {
            $articles[$value['id']] = $value;
        }

        return view('admincp.news.recommend', ['cur' => 'news', 'user' => $userInfo, 'path' => 'top', 'postp' => $defaultTp, 'posid' => $posid, 'pos' => $newsPos, 'articles' => $articles, 'data' => $data, 'posName' => $defaultName]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $newsModel = new NewsModel;
        $res       = $newsModel->searchTitle($query);
        $arr       = [];
        if ($res) {
            $arr = $res;
        }
        $out = ['results' => $arr];
        return json_encode($out);
    }

    public function switchWeight(Request $request)
    {
        $dragId = intval($request->input('drag'));
        $dropId = intval($request->input('drop'));

        $newsModel = new NewsModel;
        $ret       = $newsModel->topPostionWeight($dragId, $dropId);
        if ($ret) {
            return Library::output(0);
        } else {
            return Library::output(1);
        }
    }

    public function position(Request $request)
    {

        $userInfo  = $request->userinfo;
        $newsModel = new NewsModel;
        $data      = $newsModel->getNewsTopPos();

        return view('admincp.news.position', ['cur' => 'news', 'user' => $userInfo, 'path' => 'position', 'data' => $data]);
    }

    public function comments(Request $request)
    {
        $userInfo = $request->userinfo;
        $status   = $request->input("choose");

        $search         = $request->input("search");
        $searchText     = $search ? $search : '';
        $CommentDBModel = new NewCommentDBModel;
        $clause         = [];

        if ($status == '' || $status === -1) {
            $choose = -1;
        } else {
            $choose           = intval($status);
            $clause['status'] = $choose;
        }

        if ($searchText) {
            $clause['search'] = $searchText;
        }
        $data = $CommentDBModel->getCommentsForCp($clause);

        return view('admincp.vronline.comments', ['cur' => 'vronline', 'user' => $userInfo, 'path' => 'comments', 'data' => $data, 'choose' => $choose, 'searchText' => $searchText]);
    }

    public function localImg(Request $request)
    {
        $html     = $request->input('html');
        $firstImg = "";

        $html = preg_replace('/<a(.*?)+href=(\'|")(.*?)(\'|")/', '<a href="/"', $html);
        $html = preg_replace('/<script(.*?)>(.*?)<\/script(.*?)>/', '', $html);
        $html = preg_replace('/<img(.*?)alt=(\'|")(.*?)(\'|")(.*?)>/', '<img$1$5>', $html);

        preg_match_all('/<img(.*?)+src=(\'|")(.*?)(\'|")/', $html, $matches);
        if (isset($matches[3])) {
            foreach ($matches[3] as $value) {
                if (strstr($value, 'image.vronline.com')) {
                    if (!$firstImg) {
                        $firstImg = str_replace("http://image.vronline.com/", "", $value);
                    }
                    continue;
                }
                $path = ImageHelper::localImg($value);
                if ($path) {
                    if (!$firstImg) {
                        $firstImg = $path;
                    }
                    $html = str_replace($value, static_image($path), $html);
                } else {
                    return Library::output(1);
                }
            }
            return Library::output(0, ['html' => $html, 'cover' => $firstImg]);
        } else {
            return Library::output(0, ['html' => $html]);
        }
    }
}
