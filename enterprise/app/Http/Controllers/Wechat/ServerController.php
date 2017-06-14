<?php
namespace App\Http\Controllers\Wechat;

use App;
use App\Http\Controllers\Controller;
use Config;
use Helper\Library;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    const APP_ID   = "wx222045ca7e1de9f8";
    const APP_KEY  = "cf83ab2776a2d7c77ca4044e1561c4f4";
    const TOKEN    = "gyk402alop953";
    const AES_KEY  = "Xu1XMKSP4m2IN57kLYXdoMEtvyv5XpgjJiRUi3fc1nV";
    const DEV_USER = "gh_a11b421ee158";

    public function __construct()
    {

    }

    public function tickEvent(Request $request)
    {
        error_log(date("Y-m-d H:i:s") . "\t" . json_encode($_GET) . "|" . json_encode($_POST) . "|" . file_get_contents("php://input") . "\n", 3, "/opt/phplog/wechat.log");

        $timeStamp = $request->input('timestamp');
        $nonce     = $request->input('nonce');
        $echostr   = $request->input('echostr');
        $sign      = $request->input('signature');
        $ck        = $this->checkSignature(self::TOKEN, $timeStamp, $nonce, $sign);
        if ($ck) {
            $msg = file_get_contents("php://input");
            if ($msg) {
                $xml       = simplexml_load_string($msg);
                $eventType = (string) $xml->Event;
                if ($eventType == "subscribe") {
                    $name        = (string) $xml->FromUserName;
                    $responseMsg = '<xml>
                <ToUserName><![CDATA[' . $name . ']]></ToUserName>
                <FromUserName><![CDATA[' . self::DEV_USER . ']]></FromUserName>
                <CreateTime>' . time() . '</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[欢迎来到VRonline平台]]></Content>
                </xml>';
                    $scene = (string) $xml->EventKey;
                    if ($scene == "qrscene_dpcard") {
                        $name = (string) $xml->FromUserName;
                        error_log(date("Y-m-d H:i:s") . "\t" . $name . "\t" . $scene . "\n", 3, "/opt/phplog/wechat_dpcard.log");
                    }
                    return response()->make($responseMsg, '200')->header('Content-Type', 'text/xml');
                }
            }
            return $echostr;
        } else {
            return "";
        }
    }

    private function checkSignature($token, $timeStamp, $nonce, $sign)
    {
        if (!$token || !$timeStamp || !$nonce || !$sign) {
            return false;
        }
        $array = array($token, $timeStamp, $nonce);
        sort($array, SORT_STRING);
        $str    = implode($array);
        $mySign = sha1($str);
        if ($sign != $mySign) {
            return false;
        } else {
            return true;
        }
    }
}
