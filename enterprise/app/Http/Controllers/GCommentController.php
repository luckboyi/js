<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Traits\SimpleResponse;
use App\Models\CommonModel;
use App\Models\Game;
use Illuminate\Http\Request;

class GCommentController extends Controller
{

    use SimpleResponse;

    /**
     * 评论列表
     *
     * @param  int  $gid 游戏ID
     * @return view
     */
    public function commentList($appid)
    {
        $game = CommonModel::set("Game")->where("appid", $appid)->first();

        $comments = CommonModel::set("Comment")->where("target_id", $appid)->paginate(15);

        return view('game.comment_list', ['game' => $game, 'comments' => $comments]);
    }

    /**
     * 评论编辑展示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($appid, $id)
    {

        $appid = (int) $appid;
        $id    = (int) $id;

        $game = CommonModel::set("Game")->where("appid", $appid)->first();

        $comment = $game ? $game->comment->where("id", $id)->first() : null;

        return view('game.comment_edit', ['game' => $game, 'comment' => $comment]);
    }

    /**
     * 评论编辑提交
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $appid, $id)
    {
        //
        $appid = (int) $appid;
        $id    = (int) $id;

        $redirect_url = url("gcomment/{$appid}/{$id}");

        if ($appid <= 0 || $id <= 0) {
            return $this->errorRedirect($redirect_url, "保存失败：参数错误");
        }

        $content = $request->input("content");

        if (!$content) {
            return $this->errorRedirect($redirect_url, "保存失败：参数错误，没有提交内容");
        }

        $game = CommonModel::set("Game")->where("appid", $appid)->first();

        $comment = $game ? $game->comment->where("id", $id)->first() : null;

        if (!$comment) {
            return $this->errorRedirect($redirect_url, "保存失败：评论不存在");
        }

        $comment->content = $content;

        $ret = $comment->save();

        if (!$ret) {
            return $this->errorRedirect($redirect_url, "保存失败：保存错误");
        }

        return redirect($redirect_url)
            ->with('status', '更新成功')
            ->withInput();
    }

    /**
     * 删除评论
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $id = (int) $request->input("id");
        if ($id <= 0) {
            return $this->outputJson(["code" => -1, "msg" => "参数错误"]);
        }

        $ret = $comment = CommonModel::set("Comment")::where("id", $id)->delete();
        if (!$ret) {
            return $this->outputJson(["code" => -1, "msg" => "评论删除失败"]);
        }

        return $this->outputJson(["code" => 1, "msg" => "删除成功"]);

    }

}
