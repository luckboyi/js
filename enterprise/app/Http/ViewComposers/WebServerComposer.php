<?php

namespace App\Http\ViewComposers;

use App;
use App\Helper\ImageHelper;
use App\Models\OpenModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WebServerComposer {

	public function __construct(Request $request) {
		$this->request = $request;
	}

	public function compose(View $view) {

		$webGameModel = App::make('webGame');

		$appids = [];
		$serverids = [];

		$preServers = $webGameModel->latestGameServer(5); //准备中的服务器
		$newServers = $webGameModel->newGameServer(25); //已经开启的服务器

		foreach ($preServers as $tmp) {
			$appids[] = $tmp['appid'];
		}

		foreach ($newServers as $tmp) {
			$appids[] = $tmp['appid'];
		}

		$webgamesInfo = $webGameModel->getMultiGameInfo($appids);

		if (!$webgamesInfo || !is_array($webgamesInfo)) {
			$webgamesInfo = array();
		}

		$openModel = new OpenModel;

		foreach ($webgamesInfo as $webgame) {

			$resInfo = ImageHelper::path('webgameimg', $webgame['appid'], $webgame['img_version'], $webgame['img_slider'], false);

			$webgame['img_url'] = isset($resInfo['ico']) ? $resInfo['ico'] : '';
			$webgames[$webgame["appid"]] = $webgame;

		}

		$serversInfo = $webGameModel->getMultiServer(array_values($serverids));
		if (!$serversInfo || !is_array($serversInfo)) {
			$serversInfo = array();
		}
		foreach ($serversInfo as $server) {
			$key = $server["appid"] . "_" . $server["serverid"];
			$servers[$key] = $server;
		}

		$view->with(compact("preServers", "newServers", "webgames", "servers"));
	}

}
