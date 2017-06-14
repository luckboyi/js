<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\NewsModel;
use App\Models\VideoModel;
use Illuminate\Http\Request;

class AuditController extends Controller {

	public function __construct() {
		$this->middleware("vrauth:jump:admincp", ['only' => ["index", "video", "user", "vrgame", "webgame"]]);
	}

	public function index(Request $request) {
		return redirect('/audit/video', 302, [], true);
	}

	public function user(Request $request) {
		$userInfo = $request->userinfo;
		return view('admincp.sys.index', ['cur' => 'audit', 'user' => $userInfo, 'path' => 'user']);
	}

	public function video(Request $request) {
		$userInfo = $request->userinfo;

		$videlModel = new VideoModel();
		$data = $videlModel->getDevVideoPage();

		return view('admincp.audit.video', ['cur' => 'audit', 'user' => $userInfo, 'path' => 'video', 'data' => $data]);
	}

	public function news(Request $request) {
		$userInfo = $request->userinfo;

		$class_id = intval($request->input("class_id"));
		$curClass = $class_id ? $class_id : 0;

		$newsModel = new NewsModel();
		$data = $newsModel->getDevAuditNews($curClass);
		return view('admincp.audit.news', ['cur' => 'audit', 'user' => $userInfo, 'path' => 'news', 'data' => $data, 'curClass' => $curClass]);
	}

	public function vrgame(Request $request) {
		$userInfo = $request->userinfo;
		return view('admincp.sys.index', ['cur' => 'audit', 'user' => $userInfo, 'path' => 'vrgame']);
	}

	public function webgame(Request $request) {
		$userInfo = $request->userinfo;
		return view('admincp.sys.index', ['cur' => 'audit', 'user' => $userInfo, 'path' => 'webgame']);
	}

}
