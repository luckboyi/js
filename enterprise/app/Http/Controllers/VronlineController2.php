<?php
namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Models\CookieModel;
use App\Models\NewCommentDBModel;
use App\Models\SolrModel;
use App\Models\VronlineModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VronlineController extends Controller
{

    const PAGE_SIZE = 10;

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'index-slider'         => 5,
            'index-top'            => 9,
            'index-vr-hot'         => 10,
            'index-video-hot'      => 6,
            'index-vr-special'     => 10,
            'index-pic'            => 8,
            'index-vrgame-pic'     => 5,
            'index-vrgame-new'     => 3,
            'index-vrgame-news'    => 5,
            'index-vrgame-subject' => 1,
            'index-video-pic'      => 5,
            'index-video-new'      => 3,
            'index-video-news'     => 5,
            'index-game-hot-rank'  => 5,
            'index-game-sell-rank' => 5,
            'index-video-rank'     => 5,
            'index-hardware-rank'  => 5,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }
        $topNews    = [];
        $solrModel  = new SolrModel;
        $topNews[0] = $solrModel->searchVronline(['tag' => '今日要闻', 'tp' => 'news', 'limit' => [0, 5]]);
        $topNews[1] = $solrModel->searchVronline(['tag' => '国内热点', 'tp' => 'news', 'limit' => [0, 5]]);
        $topNews[2] = $solrModel->searchVronline(['tag' => '海外热点', 'tp' => 'news', 'limit' => [0, 5]]);

        return view('vronline.index', ['index' => true, 'tops' => $tops, 'topNews' => $topNews]);
    }

    public function article(Request $request)
    {
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'news-index-slider'       => 4,
            'news-index-slider-right' => 3,
            'news-index-viewpoint'    => 1,
            'news-index-hot'          => 5,
            'news-index-subject'      => 2,
            'news-index-act'          => 5,
            'news-index-week'         => 10,
            'news-index-list'         => 10,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        return view('vronline.article', ['tops' => $tops]);
    }

    public function top(Request $request, $code)
    {
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'game-rank-vrgame-mostpv' => 10,
            'pc-new'                  => 3,
            'search-ad1'              => 1,
            $code                     => 10,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        return view('vronline.search', ['tops' => $tops, 'topArticles' => $tops[$code]]);
    }

    public function tag(Request $request, $tag, $page = 1)
    {
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'game-rank-vrgame-mostpv' => 10,
            'pc-new'                  => 3,
            'search-ad1'              => 1,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        $page = intval($page);
        if ($page < 0) {
            $page = 1;
        }
        $limit     = [self::PAGE_SIZE * ($page - 1), self::PAGE_SIZE];
        $solrModel = new SolrModel;
        $articles  = $solrModel->searchVronline(['tag' => $tag, 'tp' => 'news', 'limit' => $limit], true, $articleNum);
        $pageObj   = new LengthAwarePaginator([], $articleNum, self::PAGE_SIZE, $page);
        $pageObj->appends(['base' => '/vronline/tag/' . $tag]);
        return view('vronline.search', ['tops' => $tops, 'articles' => $articles, 'pageObj' => $pageObj]);
    }

    public function articleList(Request $request, $id, $page = 1)
    {
        $category      = Library::getCurrentCategory('news', $id);
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'game-rank-vrgame-mostpv' => 10,
            'pc-new'                  => 3,
            'search-ad1'              => 1,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        $page = intval($page);
        if ($page < 0) {
            $page = 1;
        }
        $limit     = [self::PAGE_SIZE * ($page - 1), self::PAGE_SIZE];
        $solrModel = new SolrModel;
        $articles  = $solrModel->searchVronline(['category' => $category['id'], 'tp' => 'news', 'limit' => $limit], true, $articleNum);

        $pageObj = new LengthAwarePaginator([], $articleNum, self::PAGE_SIZE, $page);
        $pageObj->appends(['base' => '/vronline/article/list/' . $id]);
        return view('vronline.search', ['tops' => $tops, 'category' => $category, 'articles' => $articles, 'pageObj' => $pageObj]);
    }

    public function pcList(Request $request, $id, $page = 1)
    {
        $category      = Library::getCurrentCategory('pc', $id);
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'game-rank-vrgame-mostpv' => 10,
            'pc-new'                  => 3,
            'search-ad1'              => 1,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        $page = intval($page);
        if ($page < 0) {
            $page = 1;
        }
        $limit     = [self::PAGE_SIZE * ($page - 1), self::PAGE_SIZE];
        $solrModel = new SolrModel;
        $articles  = $solrModel->searchVronline(['category' => $category['id'], 'tp' => 'pc', 'limit' => $limit], true, $articleNum);

        $pageObj = new LengthAwarePaginator([], $articleNum, self::PAGE_SIZE, $page);
        $pageObj->appends(['base' => '/vronline/pc/list/' . $id]);
        return view('vronline.search', ['tops' => $tops, 'category' => $category, 'articles' => $articles, 'pageObj' => $pageObj]);
    }

    public function search(Request $request, $words, $page = 1)
    {
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'game-rank-vrgame-mostpv' => 10,
            'pc-new'                  => 3,
            'search-ad1'              => 1,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        $page = intval($page);
        if ($page < 0) {
            $page = 1;
        }
        $limit     = [self::PAGE_SIZE * ($page - 1), self::PAGE_SIZE];
        $solrModel = new SolrModel;
        $articles  = $solrModel->searchVronline(['title' => $words, 'limit' => $limit], true, $articleNum);
        $pageObj   = new LengthAwarePaginator([], $articleNum, self::PAGE_SIZE, $page);
        $pageObj->appends(['base' => '/vronline/search/' . $words]);
        return view('vronline.search', ['tops' => $tops, 'articles' => $articles, 'pageObj' => $pageObj, 'words' => $words]);
    }

    public function author(Request $request, $id, $page = 1)
    {

        $vronlineModel = new VronlineModel;
        $posCode       = [
            'game-rank-vrgame-mostpv' => 10,
            'pc-new'                  => 3,
            'search-ad1'              => 1,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        $page = intval($page);
        if ($page < 0) {
            $page = 1;
        }
        $limit     = [self::PAGE_SIZE * ($page - 1), self::PAGE_SIZE];
        $solrModel = new SolrModel;
        $articles  = $solrModel->searchVronline(['author' => $id, 'tp' => 'news', 'limit' => $limit], true, $articleNum);
        $pageObj   = new LengthAwarePaginator([], $articleNum, self::PAGE_SIZE, $page);
        $pageObj->appends(['base' => '/vronline/author/' . $id]);
        $author = $vronlineModel->getWriter($id);
        return view('vronline.search', ['tops' => $tops, 'articles' => $articles, 'pageObj' => $pageObj, 'author' => $author]);
    }

    public function articleDetail(Request $request, $id)
    {
        $userInfo = CookieModel::checkLogin();
        $uid      = isset($userInfo['uid']) && $userInfo['uid'] ? $userInfo['uid'] : 0;

        $vronlineModel = new VronlineModel;
        $article       = $vronlineModel->getArticleById($id);
        $author        = $vronlineModel->getWriter($article['article_author_id']);
        $updateTime    = date("Y-m-d", strtotime($article['vtime']));

        $solrModel      = new SolrModel;
        $authorArticles = $solrModel->searchVronline(['itemid' => $id, 'author' => $article['article_author_id'], 'limit' => [0, 3]], true, $authorArticleNums);
        $category       = Library::getCurrentCategory('news', $article['article_category']);

        $relatedArticles = $solrModel->searchVronline(['itemid' => $id, 'category' => $category['id'], 'limit' => [0, 3]]);

        $posCode = [
            'news-rank' => 10,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        return view('vronline.article_detail', ['article_id' => $id, 'article' => $article, 'updateTime' => $updateTime, 'author' => $author, 'authorArticleNums' => $authorArticleNums, 'category' => $category, 'tops' => $tops, 'authorArticles' => $authorArticles, 'relatedArticles' => $relatedArticles, 'uid' => $uid]);
    }

    public function game(Request $request)
    {
        return view('vronline.game');
    }

    public function gameList(Request $request)
    {
        return view('vronline.game_list');
    }

    public function gameDetail(Request $request)
    {
        return view('vronline.game_detail');
    }

    public function video(Request $request)
    {
        $vronlineModel = new VronlineModel;
        $posCode       = [
            'video-index-slider1'   => 4,
            'index-video'           => 2,
            'video-index-original'  => 7,
            'video-index-3dbb'      => 7,
            'video-index-games'     => 7,
            'video-index-headwear'  => 5,
            'video-index-reported'  => 5,
            'video-index-vreyeshot' => 5,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }
        return view('vronline.video', ['tops' => $tops]);
    }

    public function videoList(Request $request)
    {
        $vronlineModel = new VronlineModel;
        $videoClass    = $request->input('class');
        if (!$videoClass) {
            $videoClass = 1;
        }
        //获取视频的分类
        $category  = Config::get("category.vronline_video");
        $videoList = $vronlineModel->getDevNewsForWeb('video', $videoClass, 1);
        $posCode   = [
            'video-list-recommend' => 5,
            'video-list-adhref'    => 2,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }

        $total    = $videoList->total();
        $current  = $videoList->currentPage();
        $totalArr = [];
        for ($i = 1; $i <= $total; $i++) {
            $totalArr[] = $i;
        }

        if (count($totalArr) <= 10) {
            $showPages = $totalArr;
        }

        if (count($totalArr) > 10) {
            $showPages = $this->pagebar($total - 2, $current, 7);
        }
        return view('vronline.video_list', ['category' => $category, 'videoClass' => $videoClass, 'videoList' => $videoList, 'tops' => $tops, 'showPages' => $showPages]);
    }

    public function videoDetail(Request $request, $id)
    {
        $userInfo = CookieModel::checkLogin();
        $uid      = isset($userInfo['uid']) && $userInfo['uid'] ? $userInfo['uid'] : 0;

        $vronlineModel  = new VronlineModel;
        $videoInfo      = $vronlineModel->getArticlesById($id);
        $videoInfoAfter = $vronlineModel->getArticlesByIdAfter($id);
        $posCode        = [
            'video-detail-ad'        => 1,
            'video-detail-recommend' => 5,
        ];

        $tops = [];
        foreach ($posCode as $code => $num) {
            $tops[$code] = $vronlineModel->getTopByCode($code, 0, $num);
        }
        return view('vronline.video_detail', ['videoInfo' => $videoInfo, 'videoInfoAfter' => $videoInfoAfter, 'tops' => $tops, 'uid' => $uid]);
    }

    public static function pagebar($count, $page, $num)
    {
        $num     = min($count, $num);
        $pageArr = [];
        if ($page > $count || $page < 1) {
            return;
        }

        $end   = $page + floor($num / 2) <= $count ? $page + floor($num / 2) : $count;
        $start = $end - $num + 1;
        if ($start < 1) {
            $end -= $start - 1;
            $start = 1;
        }
        for ($i = $start; $i <= $end; $i++) {
            $pageArr[] = $i;
        }
        return $pageArr;
    }
}
