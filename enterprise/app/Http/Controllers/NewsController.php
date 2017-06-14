<?php
namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\NewsModel;
use Config;
use Cookie;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use SimpleResponse;

    private $_newsPageSize = 10;

    public function index()
    {
        $newsModel = new NewsModel();

        $posCode = [
            'index-top'        => 12,
            'index-slider'     => 6,
            'index-hardware'   => 3,
            'index-ad1'        => 3,
            'index-category-2' => 3,
            'index-category-3' => 5,
            'index-category-5' => 3,
            'index-category-6' => 5,
            'index-category-4' => 3,
            'index-category-7' => 6,
            'index-ad-w'       => 1,
        ];

        $news = [];
        foreach ($posCode as $code => $num) {
            $news[$code] = $newsModel->getNewsByCode($code, 0, $num);
        }

        $posInfo = $newsModel->getPosInfoByCodes(array_keys($posCode));
        $posName = [];
        if (is_array($posInfo)) {
            foreach ($posInfo as $info) {
                $posName[$info["code"]] = $info["name"];
            }
        }
        $news["list"] = $newsModel->getArticleByCategory([1, 2, 3, 4, 5, 6, 7], 0, 11);

        if (isset($news["index-top"]) && is_array($news["index-top"])) {
            $news["index-top"] = array_chunk($news["index-top"], 3);
        }

        return view('news.index', compact("news", "posName"));
    }

    public function newsList(Request $request, $catId)
    {

        $classArticle = Config::get("category.article");
        $className    = '';
        if ($catId > 0) {
            if (!isset($classArticle[$catId])) {
                return redirect("/news", 302, [], true);
            }
            $className = $classArticle[$catId]['name'];
        }

        $crumbs = $this->crumbs('cat', $catId, $className);

        $newsModel = new NewsModel;
        $posCode   = [
            'detail-ad1'       => 1,
            'detail-top-news'  => 7,
            'detail-top-game'  => 7,
            'detail-top-video' => 7,
            'detail-act'       => 2,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $newsModel->getNewsByCode($code, 0, $num);
        }

        $pageSize = $this->_newsPageSize ?: 10;
        $catData  = $newsModel->getArticleByCategory($catId, 0, $pageSize);
        return view('news.list', compact('classArticle', 'recommend', 'catData', 'catId', "pageSize", "crumbs"));
    }

    public function moreList(Request $request, $catId)
    {
        $page  = (int) $request->input('page');
        $num   = (int) $request->input('num');
        $start = (int) $request->input('start');

        $newsModel = new NewsModel;

        $catData = $newsModel->getArticleByCategory($catId, $start, $num);

        if (is_array($catData)) {
            $classArticle = Config::get("category.article");
            foreach ($catData as &$value) {
                $value['cover']   = static_image($value["cover"], 384);
                $value['desc']    = htmlSubStr($value['content'], 200) . "...";
                $value["time"]    = date('Y-m-d', strtotime($value['ctime']));
                $value["tp_name"] = isset($classArticle[$value["tp"]]) ? $classArticle[$value["tp"]]["name"] : "未知";
            }
        }

        return $this->outputJsonWithCode(0, ["data" => $catData]);
    }

    public function newsDetail(Request $request, $id)
    {
        $newsModel = new NewsModel;
        $article   = $newsModel->getArticleById($id);
        if (!$article || $article['stat'] != 0) {
            return redirect("/news", 302, [], true);
        }
        $classArticle = Config::get("category.article");
        if (!$article['tp'] || !in_array($article['tp'], $classArticle)) {
            $article['tp'] = 2;
        }
        $crumbs  = $this->crumbs('detail', $article['tp'], $classArticle[$article['tp']]['name']);
        $posCode = [
            'detail-ad1' => 1,
            'detail-act' => 2,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $newsModel->getNewsByCode($code, 0, $num);
        }

        $realNews          = [];
        $realNews['news']  = $newsModel->getArticleByCategory([1, 2, 3, 4, 5]);
        $realNews['game']  = $newsModel->getArticleByCategory(6);
        $realNews['video'] = $newsModel->getArticleByCategory(7);

        $isSupport = Cookie::get('article_support_' . $id);

        $article['content'] = str_replace('<img', '<img alt="' . $article['title'] . ' - vr虚拟现实第一门户网站 - VRonline.com"', $article['content']);
        return view('news.detail', ['article' => $article, 'recommend' => $recommend, 'realnews' => $realNews, "isSupport" => $isSupport, 'crumbs' => $crumbs]);
    }

    /**
     * API 支持
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function support(Request $request)
    {
        //support:0不支持、1支持
        $support = (int) $request->input('support', 0);
        $id      = (int) $request->input('id');

        if (!$id || !in_array($support, [0, 1])) {
            return $this->outputJsonWithCode(2011);
        }

        $isSupport = Cookie::get('article_support_' . $id);

        if ($isSupport !== null) {
            return $this->outputJsonWithCode(2902);
        }

        $newsModel = new NewsModel;

        $article = $newsModel->getArticleById($id);
        if (!$article) {
            return $this->outputJsonWithCode(2901);
        }

        $ret = $newsModel->updateNewsSupport($id, $support);

        if (!$ret) {
            return $this->outputJsonWithCode(1);
        }

        Cookie::queue("article_support_" . $id, $support, "1");

        return $this->outputJsonWithCode(0);
    }

    private function crumbs($tp, $id, $name)
    {
        if ($tp == 'cat') {
            $html = '<a href="/">首页</a> &gt; <a href="/news/list/0">资讯</a> ';
            if ($id > 0) {
                $html .= " &gt;" . '<a href="/news/list/' . $id . '">' . $name . '</a>';
            }
        } else {
            $html = '<a href="/">首页</a> &gt; <a href="/news/list/' . $id . '">' . $name . '</a> &gt; 正文';

        }
        return $html;
    }

    public function parentIntroDown(Request $request)
    {
        return response()->download(public_path('docs/parentintro/parentintro-three-tables.rar'));
    }
}
