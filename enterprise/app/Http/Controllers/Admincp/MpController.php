<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;

class MpController extends Controller {

	public function __construct() {
		// $this->middleware("vrauth:jump:dev", ['only' => ["vrGame", "vrGameCreate", "vrGameDetail"]]);
		// $this->middleware("vrauth:json:dev", ['only' => ["vrGameCreateSubmit", "vrGameSave"]]);
	}

	public function index() {
		return 1;
	}
}