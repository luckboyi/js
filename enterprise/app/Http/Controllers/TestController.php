<?php
namespace App\Http\Controllers;

use App\Helper\ImageHelper;

// use Session;

use App\Http\Controllers\Controller;
use App\Models\OpenModel;
use App\Models\VronlineModel;
use Helper\AccountCenter;
use Illuminate\Http\Request;

// use Helper\HttpRequest;

// 使用Model对象

class TestController extends Controller
{

    public function __construct()
    {
        //$this->middleware("vrauth:0", ['only' => ["testPay"]]);
    }

    public function test(Request $request)
    {
        var_dump($request->input("id"));
    }

    public function index(Request $request)
    {

        // $appid     = "1000048";
        // $openModel = new OpenModel;
        // $appInfo   = $openModel->getDevWebgameInfo($appid, true);

        // $ret = ImageHelper::cosCopyFiles($appInfo);
        // var_dump($ret);
        // $m = new VronlineModel;

        // $arr = $m->getTopByCode('game-detail-subject');
        // var_dump($arr);
        //return view('test', []);
        // $cmd = "/bin/sh /data/work/vr/cosrsync/start_cos_sync.sh vrgame 11 >/tmp/1.log &";
        // $ret = shell_exec($cmd);
        // var_dump($ret);
        //var_dump($request->url());
        // $url = "http://images.83830.com/2016/www/hardware/2016/03/28/20160328153905364.jpg?thumb=1&amp;w=655&amp;h=10000";
        // $path = ImageHelper::localImg($url);
        // var_dump($path);
        //$this->_code(100, 25, 6);

        // $pinyin = new Pinyin();
        // var_dump($pinyin->sentence('美国海军特技飞行 '));
        // $solrModel = new SolrModel;
        // $solrModel->updateTop();
        // $t = Session::getId();
        // var_dump($t);

        //$res = $solrModel->getTop('index-banner');

        //var_dump($res);
        // $solrModel->updateGame();
        // $solrModel->updateVideo();

        //$solrModel->search('video', ["name" => "西湖德美"]);

        // $str = "abasf123123在";
        // if (preg_match("/[\x80-\xff]./", $str)) {
        //     echo '有';
        // } else {
        //     echo '没有';
        // }

        //$solrModel->updateVideo();
        // $appid = "1000036";
        // $openModel = new OpenModel;
        // $appInfo = $openModel->getDevWebgameInfo($appid, true);

        // $ret = ImageHelper::cosCopyFiles($appInfo);
        // var_dump($ret);

        // Facade class, NOT Overtrue\Pinyin\Pinyin
        // $pinyin = new Pinyin();
        // var_dump($pinyin->sentence('asdfasdf你好啊'));
        // $url = 'http://web.image.myqcloud.com/photos/v2/';
        // $appid = 10005081;
        // $bucket = 'vronline1';
        // $path = 'vrgameimg/dev/1000015/rank3';
        // $url .= $appid . "/" . $bucket . "/0/" . urlencode($path);

        // $sign = ImageHelper::imgSignBase($path, $bucket, time() + 60, 0);

        // $sha1 = "a7a78f2f8e82b4363a37586386f0aecb084eed06";
        // $size = 17412;
        // $params = ["Sha" => $sha1, "Op" => "upload_slice", "FileSize" => $size, "Slice_size" => 3145728];

        // var_dump($params);
        // $test = HttpRequest::cosPost($url, $sign, $params);
        // var_dump($test);
        // $model = new RecommendModel();
        // $data = $model->getRecommendContentByCode('landspace-video-banner');
        // var_dump($data);
        // return view('website/vronline', ["current" => "vronline"]);
    }

    public function testPay(Request $request)
    {
        // $id = ImageHelper::videoTranscoding('da2f5bb28d5491385cbdd0dbc158ef3a.mp4', 'avthumb/mp4/vcodec/libx264/crf/30', 'blue');
        // var_dump($id);
        // $id = ImageHelper::videoTranscoding('da2f5bb28d5491385cbdd0dbc158ef3a.mp4', 'avthumb/mp4/vcodec/libx264/crf/32', '1080');
        // var_dump($id);
        // $userInfo = $request->userinfo;
        // $uid      = $userInfo['uid'];
        // $token    = $userInfo['token'];
        // $account  = new AccountCenter(1, "@eWmmNOcnwLYNNjxnBi@XBeK!A0LiuLR", "0QCOoVPFRzgy0KSLnC!jAwLHgFArFGNa");
        // $info     = $account->getPayToken($uid, $token);
        // $payToken = $info['data']['paytoken'];
        // $openid   = 'fzrpfzr0fQS5';
        // return view('testpay', compact("payToken", "uid", "openid"));
    }

    private function _code($_width = 75, $_height = 25, $_rnd_code = 4, $_flag = false)
    {

        $_nmsg = '';
        //创建随机码
        for ($i = 0; $i < $_rnd_code; $i++) {
            $_nmsg .= dechex(mt_rand(0, 15));
        }

        //保存在session
        //$_SESSION['code'] = $_nmsg;

        //创建一张图像
        $_img = imagecreatetruecolor($_width, $_height);

        //白色
        $_white = imagecolorallocate($_img, 255, 255, 255);

        //填充
        imagefill($_img, 0, 0, $_white);

        if ($_flag) {
            //黑色,边框
            $_black = imagecolorallocate($_img, 0, 0, 0);
            imagerectangle($_img, 0, 0, $_width - 1, $_height - 1, $_black);
        }

        //随即画出6个线条
        for ($i = 0; $i < 6; $i++) {
            $_rnd_color = imagecolorallocate($_img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imageline($_img, mt_rand(0, $_width), mt_rand(0, $_height), mt_rand(0, $_width), mt_rand(0, $_height), $_rnd_color);
        }

        //随即雪花
        for ($i = 0; $i < 100; $i++) {
            $_rnd_color = imagecolorallocate($_img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($_img, 1, mt_rand(1, $_width), mt_rand(1, $_height), '*', $_rnd_color);
        }

        //输出验证码
        for ($i = 0; $i < strlen($_nmsg); $i++) {
            $_rnd_color = imagecolorallocate($_img, mt_rand(0, 100), mt_rand(0, 150), mt_rand(0, 200));
            imagestring($_img, 5, $i * $_width / $_rnd_code + mt_rand(1, 10), mt_rand(1, $_height / 2), $_nmsg[$i], $_rnd_color);
        }

        //输出图像
        header('Content-Type: image/png');
        imagepng($_img);

        //销毁
        imagedestroy($_img);
    }
}
