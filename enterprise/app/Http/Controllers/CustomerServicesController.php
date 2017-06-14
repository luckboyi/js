<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Http\Traits\SimpleResponse;
use App\Models\ServiceModel;
use Config;
use Helper\AccountCenter as Account;
use Helper\Library;
use Illuminate\Http\Request;

class CustomerServicesController extends Controller
{

    use SimpleResponse;

    public function index()
{
        $servicesModel = new ServiceModel();
        $servicesQa    = $servicesModel->getServiceQaList(0, 12);
        return view('customerservices.index', compact("servicesQa"));
    }

    public function faq($tp = 1)
    {
        $faqTps = Config::get("category.service_qa");
        if (!isset($faqTps[$tp])) {
            $tp = 1;
        }
        $servicesModel = new ServiceModel();
        $data          = $servicesModel->getServiceQaList($tp);
        return view('customerservices.faqlist', compact('tp', 'faqTps', 'data'));
    }

    public function faqinfo($id)
    {
        $servicesModel = new ServiceModel();
        $faqinfo       = $servicesModel->serviceQaId($id);
        if (!$faqinfo) {
            return redirect('/customer/service', 302, [], true);
        }
        $tp = $faqinfo['tp'];

        $faqTps = Config::get("category.service_qa");
        if (!isset($faqTps[$tp])) {
            $tp = 1;
        }

        return view('customerservices.faqlist', compact('faqTps', 'tp', 'faqinfo'));
    }

    public function faqpost($id)
    {
        $servicesModel = new ServiceModel();
        $data          = $servicesModel->serviceQaId($id);
        if ($data) {
            $html = '<div class="show_msg_content">' . $data['answer'] . '</div>';
            return Library::output(0, ['html' => $html]);
        } else {
            return Library::output(1);
        }
    }

    public function question($tp)
    {
        $questionTps = Config::get("category.service_question_tp");
        if (!isset($questionTps[$tp])) {
            $tp = 1;
        }

        return view('customerservices.question', compact('questionTps', 'tp'));
    }

    public function myQuestion(Request $request)
    {
        $questionTps = Config::get("category.service_question_tp");
        $out         = ['questionTps' => $questionTps];
        $search      = trim($request->input("search"));
        if ($search) {
            $servicesModel = new ServiceModel();
            $data          = $servicesModel->searchQuestion($search);
            $out['data']   = $data;
        }

        return view('customerservices.questionlist', $out);
    }

    public function questionInfo($id)
    {
        $questionTps = Config::get("category.service_question_tp");
        $out         = ['questionTps' => $questionTps];

        if (!$id) {
            return redirect('/customer/service/myquestion', 302, [], true);
        }
        $servicesModel = new ServiceModel();
        $data          = $servicesModel->searchQuestion($id);
        if (!$data) {
            return redirect('/customer/service/myquestion', 302, [], true);
        }
        $content        = json_decode($data['content'], true);
        $out['data']    = $data;
        $out['content'] = $content;
        $out['code']    = $id;
        return view('customerservices.questioninfo', $out);
    }

    public function submitQuestion(Request $request)
    {
        $arr            = [];
        $arr['tp']      = intval($request->input('tp'));
        $arr['sub_tp']  = intval($request->input('sub_tp'));
        $arr['account'] = $request->input('account');
        $arr['title']   = $request->input('title');
        $arr['name']    = $request->input('name');
        $arr['mobile']  = $request->input('mobile');
        $arr['email']   = $request->input('email');
        $arr['qq']      = $request->input('qq');
        $arr['gender']  = intval($request->input('gender'));

        $idcard        = $request->input('idcard');
        $arr['idcard'] = $idcard ? $idcard : '';

        $checkAccount = $this->isExistsAccAjax($arr['account']);
        if ($checkAccount) {
            return $checkAccount;
        }
        $arr['code']   = questionCode();
        $servicesModel = new ServiceModel();
        $ret           = $servicesModel->updateQuestion(0, $arr);
        if ($ret) {
            return Library::output(0, ['code' => $arr['code']]);
        } else {
            return Library::output(1);
        }
    }

    // 判断用户名是否存在
    public function isExistsAccAjax($name)
    {
        // $name = Input::get('name');

        // 判断用户名是否为空
        if (!$name) {
            return response()->json(array(
                'code' => -1,
                'msg'  => '用户名不能为空',
            ));
        }

        $appid  = Config::get("common.uc_appid");
        $appkey = Config::get("common.uc_appkey");

        $accountModel = new Account($appid, $appkey);

        $result = $accountModel->isExists($name);

        if ($result['code'] == 0) {
            return [
                'code' => 1107,
                'msg'  => '用户名不存在',
            ];
        }
    }

    public function replyQuestion(Request $request)
    {
        $id = $request->input('code');
        $cn = $request->input('cn');

        $servicesModel = new ServiceModel();
        $row           = $servicesModel->searchQuestion($id);
        if (!$row) {
            return Library::output(1);
        }

        $content = json_decode($row['content'], true);
        if (!$content) {
            $content = [];
        }
        $arr = [];
        array_push($content, ['tp' => 2, 'cn' => $cn, 'time' => time(), 'name' => '']);
        $arr['content'] = json_encode($content);
        $servicesModel  = new ServiceModel();
        $ret            = $servicesModel->updateQuestion($row['id'], $arr);
        return Library::output(0);
    }

    public function completeQuestion(Request $request)
    {
        $id    = $request->input('code');
        $score = intval($request->input('score'));

        $servicesModel = new ServiceModel();
        $row           = $servicesModel->searchQuestion($id);
        if (!$row) {
            return Library::output(1);
        }

        $arr['score']  = $score;
        $arr['stat']   = 3;
        $servicesModel = new ServiceModel();
        $ret           = $servicesModel->updateQuestion($row['id'], $arr);
        return Library::output(0);
    }

}
