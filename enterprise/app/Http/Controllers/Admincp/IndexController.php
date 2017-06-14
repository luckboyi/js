<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use URL;

class IndexController extends Controller {

	public function __construct() {
		$this->middleware("vrauth:jump:admincp", ['only' => ["index", "help"]]);
	}

	public function index(Request $request) {
		$userInfo = $request->userinfo;
		if (isset($userInfo['index'])) {
			return redirect($userInfo['index']);
		} else {
			return redirect('/index/help', 302, [], true);
		}

	}

	public function help(Request $request) {
		$userInfo = $request->userinfo;

		$link = URL::previous();
		if (!$link) {
			$link = '/';
		}
		return view('admincp.sys.help', ['cur' => 'index', 'user' => $userInfo, 'path' => 'help', 'link' => $link]);
	}
}