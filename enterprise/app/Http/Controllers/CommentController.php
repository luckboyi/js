<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/19
 * Time: 9:41
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\CookieModel;
use App\Models\NewCommentModel;
use Helper\Library;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CommentController extends Controller
{
    /**
     * 发表评论
     * @param string    content     评论内容
     * @param float     score       评分
     * @param int       praise      好评1 差评2
     * @param int       target_id   评论的目标ID，比如视频id、页游id、新闻id
     * @param string    type        目标类型 client_webgame/client_vrgame/client_video/news_video/news_game/news_news
     * @return array
     */
    public function addComment(Request $request)
    {
        Library::accessHeader();

        $userInfo = CookieModel::checkLogin();
        $content = trim($request->input('content'));
        $score   = $request->input('score');
        $praise  = intval($request->input('praise'));
        $target_id  = $request->input('target_id');
        $type    = $request->input('type');

        if(!$userInfo || !is_array($userInfo) || !isset($userInfo['uid']) || !isset($userInfo['token'])) {
            return Library::output(1301);
        }

        if(!$target_id || !$type) {
            return Library::output(2001);
        }

        if(!$content) {
            return Library::output(2001, null, "请输入评论内容");
        }

        $uid  = isset($userInfo['uid']) ? $userInfo['uid'] : 0;
        $nick = isset($userInfo['nick']) ? $userInfo['nick'] : "";
        $face = isset($userInfo['face']) ? $userInfo['face'] : "";

        $uinfo = ['nick' => $nick, 'face' => $face];
        $cinfo = ['content' => $content];
        if($score !== null) {
            $cinfo['score'] = $score;
        }
        if($praise !== null) {
            $cinfo['praise'] = $praise;
        }

        $newCommentModel = new NewCommentModel;
        $ret = $newCommentModel->addComment($uid, $target_id, $type, $uinfo, $cinfo);
        if($ret) {
            $data = ['cid' => $ret, 'uid' => $uid, 'nick' => $nick, 'face' => $face];
            return Library::output(0, $data);
        }else {
            return Library::output(1);
        }
    }

    /**
     * 发表回复
     * @param string    content     回复内容
     * @param int       cid         回复的评论id
     * @return array
     */
    public function addReply(Request $request)
    {
        Library::accessHeader();

        $userInfo = CookieModel::checkLogin();
        $content = trim($request->input('content'));
        $cid     = intval($request->input('cid'));

        if(!$userInfo || !is_array($userInfo) || !isset($userInfo['uid']) || !isset($userInfo['token'])) {
            return Library::output(1301);
        }

        if(!$cid) {
            return Library::output(2001);
        }

        if(!$content) {
            return Library::output(2001, null, "请输入评论内容");
        }

        $uid  = isset($userInfo['uid']) ? $userInfo['uid'] : 0;
        $nick = isset($userInfo['nick']) ? $userInfo['nick'] : "";
        $face = isset($userInfo['face']) ? $userInfo['face'] : "";

        $uinfo = ['nick' => $nick, 'face' => $face];
        $cinfo = ['content' => $content];

        $newCommentModel = new NewCommentModel;
        $ret = $newCommentModel->addReply($cid, $uid, $uinfo, $cinfo);
        if($ret) {
            $ret['uid'] = $uid;
            $ret['nick'] = $nick;
            $ret['face'] = $face;
            return Library::output(0, $ret);
        }else {
            return Library::output(1);
        }
    }

    /**
     * 查询评论
     * @param int       target_id   评论的目标ID，比如视频id、页游id、新闻id
     * @param string    type        目标类型 client_webgame/client_vrgame/client_video/news_video/news_game/news_news
     * @param int       page        页数
     * @param int       len         每页数量
     * @return array
     */
    public function getComments(Request $request)
    {
        Library::accessHeader();
        $target_id  = $request->input('target_id');
        $type  = $request->input('type');
        $page = $request->input('page');
        $len  = $request->input('len');

        if(!$target_id || !$type) {
            return Library::output(2001);
        }
        if($page <= 0) {
            $page = 1;
        }
        if($len <= 0) {
            $len = 20;
        }

        $newCommentModel = new NewCommentModel;
        $rows = $newCommentModel->getComments($target_id, $type, $page, $len);
        if(!is_array($rows)) {
            return Library::output(1);
        }else {
            return Library::output(0, $rows);
        }
    }

    /**
     * 审核评论
     * @param Request $request
     * @return array
     */
    public function reviewComment(Request $request)
    {
        $cid    = $request->input('cid');
        $action = $request->input('action');

        if(!$cid || !$action) {
            return Library::output(2001);
        }

        $newCommentModel = new NewCommentModel;
        $ret = $newCommentModel->reviewComment($cid, $action);
        if(!$ret) {
            return Library::output(1);
        }else {
            return Library::output(0);
        }
    }

    /**
     * 查询评论，
     * @param int       page        页数
     * @param int       len         每页数量
     */
    public function getUnReviewComments(Request $request)
    {
        $page = $request->input('page');
        $len  = $request->input('len');

        if($page <= 0) {
            $page = 1;
        }
        if($len <= 0) {
            $len = 20;
        }

        $newCommentModel = new NewCommentModel;
        $rows = $newCommentModel->getUnReviewComments($page, $len);
        if(!is_array($rows)) {
            return Library::output(1);
        }else {
            return Library::output(0, $rows);
        }
    }

    /**
     * 点赞
     * @param int       cid        评论id
     * @param string    action     操作 up 点赞; down 踩;
     */
    public function support(Request $request)
    {
        Library::accessHeader();

        $userInfo = CookieModel::checkLogin();
        $cid     = $request->input('cid');
        $action  = $request->input('action');

        if(!$userInfo || !is_array($userInfo) || !isset($userInfo['uid']) || !isset($userInfo['token'])) {
            return Library::output(1301);
        }

        if(!$cid || !$action) {
            return Library::output(2001);
        }

        $uid  = isset($userInfo['uid']) ? $userInfo['uid'] : 0;

        $newCommentModel = new NewCommentModel;
        $ret = $newCommentModel->support($uid, $cid, $action);
        if(!$ret) {
            return Library::output(1);
        }else if($ret === "already") {
            return Library::output(2902);
        }else {
            return Library::output(0);
        }
    }
}