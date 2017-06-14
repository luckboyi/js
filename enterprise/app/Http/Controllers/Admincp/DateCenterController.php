<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use Helper\HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DateCenterController extends Controller {

	private $passport_protocal = "http://dcinter.vronline.com/dataCenter.php";
	// private $passport_protocal = "http://pic.vronline.com/dataCenter/dataCenter.php";
	private $perPage = 20;

	public function __construct() {
		$this->middleware("vrauth:jump:admincp", ['only' => ["index", "allData", "vrGameData", "vrVideoData"]]);
	}

	public function index(Request $request) {
		return redirect('/stat/index', 302, [], true);
	}

	public function allData(Request $request) {
		$userInfo = $request->userinfo;
		$action = $request->input('action');
		$start = $request->input('start');
		$end = $request->input('end');
		$page = $request->input('page');
		$action = $action ? $action : 'getall';

		$start = $start ? $start : date('Y-m-d');
		$end = $end ? $end : date('Y-m-d');

		$tb = 'allData';
		$param = array("action" => $action, "tb" => $tb);
		if ($action == 'getselect') {
			$start = $start != '' ? $start : '2016-11-10';
			$end = $end != '' ? $end : date('Y-m-d', time());
			$param = array("action" => $action, "tb" => $tb, "start" => $start, "end" => $end);
		}

		$request = $this->passport_protocal;

		//$ret = $this->post($param, $request);
		$ret = json_decode(HttpRequest::post($request, $param), 1);
		$retDate = isset($ret['data']) ? $ret['data'] : [];

		$perPage = $this->perPage;
		if ($page != '') {
			$currentPage = $page;
			$currentPage = $currentPage <= 0 ? 1 : $currentPage;
		} else {
			$currentPage = 1;
		}
		$item = array_slice($retDate, ($currentPage - 1) * $perPage, $perPage); //注释1
		$total = count($retDate);
		$paginator = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
			'path' => Paginator::resolveCurrentPath(), //注释2
			'pageName' => 'page',
		]);
		$retDate = $paginator->toArray()['data'];

		//获取数据中心的最后一条数据
		$lastRet = $this->getLastDate();
		return view('admincp.stat.index', ['cur' => 'stat', 'user' => $userInfo, 'path' => 'index', 'retDate' => $retDate, 'paginator' => $paginator, 'lastRet' => $lastRet, 'action' => $action, 'start' => $start, 'end' => $end]);
	}
	/**
	 * VR游戏的数据中心统计数据展示
	 * [vrGameData description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function vrGameData(Request $request) {
		$userInfo = $request->userinfo;
		$action = $request->input('action');
		$start = $request->input('start');
		$end = $request->input('end');
		$page = $request->input('page');
		if ($action == '') {
			$action = 'getdate';
		}
		$tb = 'vrgame';
		$param = array("action" => $action, "tb" => $tb);
		if ($action == 'getselect') {
			$start = $start != '' ? $start : '2016-11-10';
			$end = $end != '' ? $end : date('Y-m-d', time());
			$param = array("action" => $action, "tb" => $tb, "start" => $start, "end" => $end);
		}

		$request = $this->passport_protocal;

		//$ret = $this->post($param, $request);
		$ret = json_decode(HttpRequest::post($request, $param), 1);
		$retDate = isset($ret['data']) ? $ret['data'] : [];

		$perPage = $this->perPage;
		if ($page != '') {
			$currentPage = $page;
			$currentPage = $currentPage <= 0 ? 1 : $currentPage;
		} else {
			$currentPage = 1;
		}
		$item = array_slice($retDate, ($currentPage - 1) * $perPage, $perPage); //注释1
		$total = count($retDate);
		$paginator = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
			'path' => Paginator::resolveCurrentPath(), //注释2
			'pageName' => 'page',
		]);
		$retDate = $paginator->toArray()['data'];

		//获取数据中心的最后一条数据
		$lastRet = $this->getLastDate($tb);
		return view('admincp.stat.vrgame', ['cur' => 'stat', 'user' => $userInfo, 'path' => 'vrgame', 'retDate' => $retDate, 'paginator' => $paginator, 'lastRet' => $lastRet, 'action' => $action, 'start' => $start, 'end' => $end]);
	}

	/**
	 * 视频的数据中心统计数据展示
	 * [vrGameData description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function vrVideoData(Request $request) {
		$userInfo = $request->userinfo;
		$action = $request->input('action');
		$start = $request->input('start');
		$end = $request->input('end');
		$page = $request->input('page');
		if ($action == '') {
			$action = 'getdate';
		}
		$tb = 'vrvideo';
		$param = array("action" => $action, "tb" => $tb);
		if ($action == 'getselect') {
			$start = $start != '' ? $start : '2016-11-10';
			$end = $end != '' ? $end : date('Y-m-d', time());
			$param = array("action" => $action, "tb" => $tb, "start" => $start, "end" => $end);
		}

		$request = $this->passport_protocal;

		//$ret = $this->post($param, $request);
		$ret = json_decode(HttpRequest::post($request, $param), 1);
		$retDate = isset($ret['data']) ? $ret['data'] : [];

		$perPage = $this->perPage;
		if ($page != '') {
			$currentPage = $page;
			$currentPage = $currentPage <= 0 ? 1 : $currentPage;
		} else {
			$currentPage = 1;
		}
		$item = array_slice($retDate, ($currentPage - 1) * $perPage, $perPage); //注释1
		$total = count($retDate);
		$paginator = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
			'path' => Paginator::resolveCurrentPath(), //注释2
			'pageName' => 'page',
		]);
		$retDate = $paginator->toArray()['data'];

		//获取数据中心的最后一条数据
		$lastRet = $this->getLastDate($tb);
		return view('admincp.stat.vrvideo', ['cur' => 'stat', 'user' => $userInfo, 'path' => 'vrvideo', 'retDate' => $retDate, 'paginator' => $paginator, 'lastRet' => $lastRet, 'action' => $action, 'start' => $start, 'end' => $end]);
	}

	/**
	 * 获取数据的最后一条
	 * [getLastDate description]
	 * @param  [type] $tb [description]
	 * @return [type]     [description]
	 */
	public function getLastDate($tb = '') {
		$request = $this->passport_protocal;
		$param = array("action" => "getlast", "tb" => $tb);
		$ret = json_decode(HttpRequest::post($request, $param), 1);
		$lastRet = isset($ret['data'][0]) ? $ret['data'][0] : [];
		return $lastRet;
	}
}
