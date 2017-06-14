<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\CommonModel;
use Config;
use Helper\AccountCenter;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * 平台游戏
     *
     * @return view('game.index')
     */
    public function index(Request $request)
    {
        if ($name = $request->input("name")) {

            $games = CommonModel::set("Game")
                ->where("game_type", 1)->where("name", "like", "%{$name}%")
                ->paginate(50);

            $games->appends(["name" => $name]);

        } else {

            $games = CommonModel::set("Game")->where("game_type", 1)->paginate(50);

        }

        $gameTypes   = Config::get("vrgame.class");
        $deviceTypes = Config::get("vrgame.support_device");

        //替换游戏类型、设备类型字段
        foreach ($games as &$game) {

            //替换游戏类型字段
            $types = [];
            $types = explode(",", $game->first_class);

            foreach ($types as &$type) {
                $type = isset($gameTypes[$type]) ? $gameTypes[$type]["name"] : $type;
            }

            $game->type = join(",", $types);

            //替换设备类型字段
            $devices = [];
            $devices = $game->support;

            foreach ($devices as &$device) {
                $device = isset($deviceTypes[$device]) ? $deviceTypes[$device]["name"] : $device;
            }

            $game->device = join(",", $devices);

        }

        return view('game.index', ['games' => $games, 'name' => $name]);
    }

}
