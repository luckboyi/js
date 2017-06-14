<?php
namespace App\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Models\ServiceModel;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware("vrauth:jump:admincp", ['only' => ["index", "feedback", "qa", "feedbackTps", "feedbackInfo"]]);
    }

    public function index(Request $request)
    {
        $userInfo = $request->userinfo;
        if ($userInfo['nextPath']) {
            return redirect($userInfo['nextPath']);
        } else {
            return redirect('index/help', 302, [], true);
        }
    }

    public function feedback(Request $request)
    {
        $userInfo = $request->userinfo;

        $search     = $request->input("search");
        $searchText = $search ? $search : '';

        $class_id = intval($request->input("choose"));
        $curClass = $class_id ? $class_id : 0;

        $service = new ServiceModel();
        $data    = $service->serviceQuestion($curClass, $searchText);

        return view('admincp.service.feedback', ['cur' => 'service', 'user' => $userInfo, 'path' => 'feedback', 'data' => $data, 'searchText' => $searchText, 'curClass' => $curClass]);
    }

    public function feedbackInfo(Request $request, $code)
    {
        $userInfo = $request->userinfo;
        $service  = new ServiceModel();
        $val      = $service->searchQuestion($code);
        $content  = json_decode($val['content'], true);
        return view('admincp.service.feedbackInfo', ['cur' => 'service', 'user' => $userInfo, 'path' => 'feedback', 'val' => $val, 'content' => $content]);
    }

    public function feedbackTps(Request $request)
    {
        $userInfo = $request->userinfo;

        $tp = intval($request->input("tp"));

        $cfg = Config::get('category.service_question_tp');
        if (!isset($cfg[$tp])) {
            return Library::output(1);
        }

        $options = $cfg[$tp]['sub'];
        $html    = '';
        foreach ($options as $option) {
            $html .= '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
        }
        return Library::output(0, ['html' => $html]);
    }

    public function feedbackDel(Request $request)
    {
        $userInfo = $request->userinfo;

        $code = $request->input("code");

        if (!$code) {
            return false;
        }

        $service = new ServiceModel();

        $delRet = $service->delFeedback($code);

        return Library::output(0);
    }

    public function qa(Request $request)
    {
        $userInfo   = $request->userinfo;
        $search     = $request->input("search");
        $searchText = $search ? $search : '';

        $service = new ServiceModel();
        $data    = $service->serviceQa($searchText);

        $qaTps = Config::get("category.service_qa");

        return view('admincp.service.qa', ['cur' => 'service', 'user' => $userInfo, 'path' => 'qa', 'data' => $data, 'searchText' => $searchText, 'qaTps' => $qaTps]);
    }
}
