<?php

namespace App\Http\Controllers;

use App\Helper\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\CommonModel;
use Illuminate\Http\Request;

class GRecommendController extends Controller {

	use SimpleResponse;

	protected $position = [
		"name" => "game",
		"type" => 0,
		"num" => 6,
	];

	protected $content_type = 2;

	/**
	 * 平台游戏
	 *
	 * @return view('game.index')
	 */
	public function index() {
		$recommends = CommonModel::set("Recommend")->where("position_id", "like", "game\_0\_%")->get();
		$newRecommends = [];
		foreach ($recommends as $recommend) {
			if ($recommend->game) {
				$recommend->game->imginfo = ImageHelper::path("vrgameimg", $recommend->game->appid, $recommend->game->img_version, $recommend->game->img_slider);
			}
			$newRecommends[$recommend->position_id] = $recommend;
		}

		return view('game.recommend', ["recommends" => $newRecommends]);
	}

	/**
	 * 更新推荐信息接口
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request) {

		//获取请求参数
		$position_id = $request->input("position_id");
		$gid = (int) $request->input("gid");
		$op = $request->input("op");
		$ed = $request->input("ed");

		//判断参数是否齐备
		if (!$position_id || !$gid > 0 || !strtotime($op) || !strtotime($ed)) {
			return $this->outputJson(["code" => -1, "msg" => "参数错误"]);
		}

		//判断时间是否正确
		if (strtotime($op) > strtotime($ed)) {
			return $this->outputJson(["code" => -1, "msg" => "开始时间不可大于结束时间"]);
		}

		//判断位置是否正确
		$position = explode("_", $position_id);
		if (!isset($position[0]) || $position[0] != $this->position["name"]) {
			return $this->outputJson(["code" => -1, "msg" => "位置id错误"]);
		}
		if (!isset($position[1]) || $position[1] != $this->position["type"]) {
			return $this->outputJson(["code" => -1, "msg" => "位置id错误"]);
		}
		if (!isset($position[2]) || $position[2] < 0 || $position[2] > $this->position["num"]) {
			return $this->outputJson(["code" => -1, "msg" => "位置id错误"]);
		}

		//判断游戏是否存在
		$game = CommonModel::set("Game")->where("gid", $gid)->first();
		if (!$game) {
			return $this->outputJson(["code" => -1, "msg" => "该游戏不存在"]);
		}
		unset($game);

		//该推荐位中是否内容
		$recommend = CommonModel::set("Recommend")->where("position_id", $position_id)->first();
		if (!$recommend) {
			$recommend = CommonModel::set("Recommend");
		}

		$recommend->position_id = $position_id;
		$recommend->content_id = $gid;
		$recommend->content_type = $this->content_type;
		$recommend->opening_time = $op;
		$recommend->end_time = $ed;

		$ret = $recommend->save();

		if (!$ret) {
			return $this->outputJson(["code" => -1, "msg" => "保存失败"]);
		}

		return $this->outputJson(["code" => 1, "msg" => "保存成功"]);
	}
}
