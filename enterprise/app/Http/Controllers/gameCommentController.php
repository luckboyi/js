<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Traits\SimpleResponse;
use App\Models\CommentModel;
use App\Models\SupportModel;
use App\Models\CookieModel;
use App\Models\UserModel as User;
use config;
use Helper\AccountCenter as Account;
use Illuminate\Http\Request;
use PhpParser\Comment;

class gameCommentController extends Controller
{

    use SimpleResponse;

    public function __construct()
    {
        $this->middleware("vrauth:json", ['only' => ["delComment", "addComment", "support"]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];
//        $sessionArr = user::getAllSession();
        //
        //        // 得到uid 和 token
        //        $uid          = $sessionArr['open_uid'];
        //        $token        = $sessionArr['token'];
        //        $name         = $sessionArr['nick'];
        //        $appid        = Config::get("common.uc_appid");
        //        $appkey       = Config::get("common.uc_appkey");
        //        $accountModel = new Account($appid, $appkey);
        //
        //        $userInfoArr = $accountModel->info($uid, $token);
        //        echo '<pre>';
        //print_r($userInfo);

    }

    /*
     * 添加评论
     * 参数：targetId=1000027&targetType=1&content=12121212&commentType=1&serverId=1&resource=1
     * targetType=》评论对象类型 1:webgame 2:vrgame
     * commentType=》1:好评 2:差评'
     * resource =》1：web 2:pc端
     */
    public function addComment(Request $request)
    {
        $comment = new CommentModel();
        $cookie  = new CookieModel();
        //获取提交的一些信息

        $targetId    = $request->input('targetId');
        $targetType  = $request->input('targetType');
        $content     = $request->input('content');
        $commentType = $request->input('commentType');
        $resource    = $request->input('resource');
        $score       = $request->input('score');

        //判断用户的
        $userInfo = $request->userinfo;
        $uid      = $userInfo['uid'];

        if (!isset($uid) || $uid == '') {
            return $this->outputJsonWithCode(2601);
        }

        //测试添加uid
        //$uid = $request->input('uid');

        if (!isset($uid) || !isset($targetId) || !isset($content)) {
            return $this->outputJsonWithCode(1);
        }

        if ($commentType == 1 || $commentType == 2) {
        } else {
            return $this->outputJsonWithCode(2608);
        }

        $ifCommentArr = array(
            'uid'        => $uid,
            'targetId'   => $targetId,
            'targetType' => $targetType,
        );
        $ifComment = $comment->ifComment($ifCommentArr);
        if (empty($ifComment)) {
            return $this->outputJsonWithCode(2602);
        }

        $commentArr = array(
            'uid'          => $uid,
            'target_id'    => $targetId,
            'target_type'  => $targetType,
            'content'      => $content,
            'comment_type' => $commentType,
            'create_at'    => time(),
        );
        $alreadyComment = $comment->alreadyComment($commentArr);
        if (!empty($alreadyComment)) {
            return $this->outputJsonWithCode(2603);
        }

        //添加评论评分的接口
        $addScoreArr = array(
            'uid'       => $uid,
            'target_id' => $targetId,
            'score'     => $score,
        );
        $addScoreRet = $this->addScore($addScoreArr);
//        var_dump($addScoreRet);die;
        if (!$addScoreRet) {
            return $this->outputJsonWithCode(2606);
        }

        //添加评论表
        $ret = $comment->addComment($commentArr);
        if (!$ret) {
            return $this->outputJsonWithCode(1);
        }
        return $this->outputJsonWithCode(0);
    }

    /*
     * 删除评论接口
     */
    public function delComment(Request $request)
    {
        $comment = new CommentModel();
        $cookie  = new CookieModel();
        //获取提交的一些信息
        $commentId = $request->input('commentId');
        $support   = $request->input('support');
        $resource  = $request->input('resource');
        //判断用户的
        $loginUid = '';
        $userInfo = $request->userinfo;
        $loginUid = $userInfo['uid'];

        //测试添加uid

        if ($commentId == '') {
            return $this->outputJsonWithCode(1);
        }

        $commentInfoArr                = $comment->getCommentInfoOne($commentId);
        $commentInfoArr[0]['loginUid'] = $loginUid;

        $commentArr = $commentInfoArr[0];

        if ($loginUid !== $commentArr['uid']) {
            return $this->outputJsonWithCode(2607);
        }
        $ret = $comment->delComment(array('commentId' => $commentId));
        if (!ret) {
            return $this->outputJsonWithCode(1);
        }

        return $this->outputJsonWithCode(0);
    }

    /*
     * 评论支持与不支持的数据添加
     * uid=10049&targetId=1000027&targetType=1&commentType=1&resource=1&support=1
     * targetType=》评论对象类型 1:webgame 2:vrgame
     * commentType=》1:好评 2:差评'
     * resource =》1：web 2:pc端
     * support =》0：不支持 1:支持
     */
    public function support(Request $request)
    {
        $comment = new CommentModel();
        $cookie  = new CookieModel();
        //获取提交的一些信息
        $commentId = $request->input('commentId');
        $support   = $request->input('support');
        $resource  = $request->input('resource');
        //判断用户的
        $loginUid = '';
        $userInfo = $request->userinfo;
        $loginUid = $userInfo['uid'];

        //测试添加uid

        if ($commentId == '') {
            return $this->outputJsonWithCode(1);
        }

        $commentInfoArr                = $comment->getCommentInfoOne($commentId);
        $commentInfoArr[0]['loginUid'] = $loginUid;
        $commentInfoArr[0]['support']  = $support;

        $commentArr = $commentInfoArr[0];
        $supportModel = new SupportModel;
        $supp = $supportModel->isSupported($loginUid, $commentId, "comment");
        if ($supp) {
            return $this->outputJsonWithCode(2604);
        }

        $ret = $comment->updateSupport($commentArr);

        if (!$ret) {
            return $this->outputJsonWithCode(1);
        }
        return $this->outputJsonWithCode(0);
    }

    /*
     * 添加游戏评分
     */
    public function addScore($info)
    {
        $comment = new CommentModel();
        $ret     = $comment->addCommentScore($info);
        return $ret;
    }

    /*
     * 获取游戏评论数
     */
    public function getCommentCount(Request $request)
    {
        $comment = new CommentModel();
        $cookie  = new CookieModel();
        //获取提交的一些信息

        $targetId   = $request->input('targetId');
        $targetType = $request->input('targetType');
        $type       = $request->input('type');
        $clause     = array(
            'target_id'   => $targetId,
            'target_type' => $targetType,
            'status'      => 1,
        );

        $row['count'] = 0;
        switch ($type) {
            case 'positive':
                $clause['comment_type'] = 1;
                $row['count']           = $comment->getCommentCount($clause);
                return $this->outputJsonWithCode(0, $row);
            case 'negative':
                $clause['comment_type'] = 2;
                $row['count']           = $comment->getCommentCount($clause);
                return $this->outputJsonWithCode(0, $row);
            default:
                $row['count'] = $comment->getCommentCount($clause);
                return $this->outputJsonWithCode(0, $row);
        }

    }
    /*
     * 拉取某游戏的所有评论信息，包括分页，
     * targetId=1000027&targetType=1&resource=1&page=2&pageNum=2&type=hot
     * targetType=》评论对象类型 1:webgame 2:vrgame
     * resource =》1：web 2:pc端
     * type => hot:热评，new:最新，my:我的
     */
    public function getCommentByGid(Request $request)
    {
        $comment = new CommentModel();

        //获取提交的一些信息
        $targetId   = $request->input('targetId');
        $targetType = $request->input('targetType');
        $page       = $request->input('page');
        $startNum   = $request->input('startNum') !== null ? $request->input('startNum') : '';
        $pageNum    = $request->input('pageNum');
        $resource   = $request->input('resource');
        $type       = $request->input('type');

        //判断用户的
        $loginUid = $request->cookie('uid');

        //测试添加uid
        //$uid = $request->input('uid');
        $commentArr = array(
            'loginUid'    => $loginUid,
            'target_id'   => $targetId,
            'target_type' => $targetType,
            'type'        => $type,
        );

        $ret = $comment->getComment($commentArr, $page, $startNum, $pageNum);
        if (empty($ret)) {
            return $this->outputJsonWithCode(2605);
        }
        $ret["type"] = $type;
        return $this->outputJsonWithCode(0, $ret);
    }
}
