<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\DevModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class DataController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:jump:admincp", ['only' => ["index"]]);
    }

    public function devUsers(Request $request)
    {
        $devModel = new DevModel;
        $res      = $devModel->getUsersName();
        return Library::output(0, $res);
    }

}
