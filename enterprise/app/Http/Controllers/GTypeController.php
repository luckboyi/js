<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Traits\SimpleResponse;
use App\Models\CommonModel;
use Illuminate\Http\Request;

class GTypeController extends Controller
{

    use SimpleResponse;

    /**
     * 平台游戏类型
     *
     * @return view('game.index')
     */
    public function index()
    {
        $gameTypes = CommonModel::set("GameType")->getAllPassedType();

        return view('game.type_list', ['gameTpyes' => $gameTypes]);
    }

    /**
     * 保存游戏类型
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {

        $name = $request->input("name");

        if (!$name) {
            return $this->outputJson(["code" => -1, "msg" => "参数错误"]);
        }

        $gameType = CommonModel::set("GameType")->where("typename", $name)->first();
        if ($gameType) {
            return $this->outputJson(["code" => -1, "msg" => "该游戏类型已存在"]);
        }
        unset($gameType);

        $newGameType = CommonModel::set("GameType");

        $newGameType->typename = $name;
        $newGameType->ispassed = "Y";

        $ret = $newGameType->save();

        if (!$ret) {
            return $this->outputJson(["code" => -1, "msg" => "保存失败"]);
        }

        return $this->outputJson(["code" => 1, "msg" => "保存成功"]);
    }
}
