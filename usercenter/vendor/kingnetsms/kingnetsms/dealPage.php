<?php
namespace kingnetsms;

class SmsApi {
	private static $username = 'JE0055';
	private static $password = '518930';
	private static $signlens = 0;
	//页面地址  127.0.0.1:7890   192.169.1.143:8086
	private static $pageurl = 'http://120.204.199.44:8026/MWGate/wmgw.asmx';
	//$pageurl = 'http://61.145.229.29:9006/MWGate/wmgw.asmx';
	//soap接口

	//POST,GET请求接口
	private static $pginface = array('MongateSendSubmit', 'MongateGetDeliver', 'MongateGetDeliver', 'MongateQueryBalance', 'MongateGetDeliver', 'MongateMULTIXSend');
	//接口排序

	//POST,GET请求返回字段

	//错误码
	private static $statuscode = array(
		"-1" => "参数为空。信息、电话号码等有空指针，登陆失败",
		"-2" => "电话号码个数超过100",
		"-10" => "申请缓存空间失败",
		"-11" => "电话号码中有非数字字符",
		"-12" => "有异常电话号码",
		"-13" => "电话号码个数与实际个数不相等",
		"-14" => "实际号码个数超过100",
		"-101" => "发送消息等待超时",
		"-102" => "发送或接收消息失败",
		"-103" => "接收消息超时",
		"-200" => "其他错误",
		"-999" => "web服务器内部错误",
		"-10001" => "用户登陆不成功",
		"-10002" => "提交格式不正确",
		"-10003" => "用户余额不足",
		"-10004" => "手机号码不正确",
		"-10005" => "计费用户帐号错误",
		"-10006" => "计费用户密码错",
		"-10007" => "账号已经被停用",
		"-10008" => "账号类型不支持该功能",
		"-10009" => "其它错误",
		"-10010" => "企业代码不正确",
		"-10011" => "信息内容超长",
		"-10012" => "不能发送联通号码",
		"-10013" => "操作员权限不够",
		"-10014" => "费率代码不正确",
		"-10015" => "服务器繁忙",
		"-10016" => "企业权限不够",
		"-10017" => "此时间段不允许发送",
		"-10018" => "经销商用户名或密码错",
		"-10019" => "手机列表或规则错误",
		"-10021" => "没有开停户权限",
		"-10022" => "没有转换用户类型的权限",
		"-10023" => "没有修改用户所属经销商的权限",
		"-10024" => "经销商用户名或密码错",
		"-10025" => "操作员登陆名或密码错误",
		"-10026" => "操作员所充值的用户不存在",
		"-10027" => "操作员没有充值商务版的权限",
		"-10028" => "该用户没有转正不能充值",
		"-10029" => "此用户没有权限从此通道发送信息",
		"-10030" => "不能发送移动号码",
		"-10031" => "手机号码(段)非法",
		"-10032" => "用户使用的费率代码错误",
		"-10033" => "非法关键词",
	);

	public function send($V) {
		if (!isset($V['type']) || !isset($V['method'])) {
			echo "参数有误！！";
			return;
		}

		$result = array();
		$smsInfo['userId'] = self::$username;
		$smsInfo['password'] = self::$password;
		//$smsInfo['pszSubPort'] = $V['port'];
		//$smsInfo['flownum'] = $V['flownum'];
		$action = self::$pageurl;
		$defhandle = $V['type']; //设置请求接口
		if ($V['self'] == 4) {
			$smsInfo['multixmt'] = ' ';
			$defhandle = 5; //个性化发送  2014-09-11
		}
		if ($V['method'] > 0) {
			$action .= "/" . self::$pginface[$defhandle];
		}

		$sms = new Client($action, $V['method']);
		$strRet = '';

		switch ($V['type']) {
		//发送信息
		case 0:
			$smsInfo['pszSubPort'] = $V['port'];
			$smsInfo['flownum'] = $V['flownum'];

			$smsInfo['pszMsg'] = $V['msg'];
			if ($V['phones'] == '') {
				$mobiles = array();
			} else {
				$mobiles = explode(',', $V['phones']);
			}

			$smsInfo['pszMsg'] = str_replace("\\\\", "\\", $smsInfo['pszMsg']);

			$PhonsInfo = $V['phones'];
			$Conts = substr_count($PhonsInfo, ',') + 1; //手机号码个数
			if ($Conts > 100) {
				$result = "号码个数超过100"; //
				echo ($result);
				break;
			}

			$lenTest = strLength($V['msg']); // + $signlens;  //短信字符个数
			if ($lenTest > 350) {
				$result = "短信字符个数超过350"; //
				echo ($result);
				break;
			}

			$result = $sms->sendSMS($smsInfo, $mobiles);

			//错误
			if (($strRet = GetCodeMsg($result, $statuscode)) != '') {
				break;
			}

			$len = strLength($V['msg']) + self::$signlens;
			$strsigns = '';

			if ($len <= 70) {
				//单条短信，生成消息ID
				if (0 == $V['method']) {
					$strsigns = singleMsgId($result, $mobiles, ';');
				} else {
					$strsigns = singleMsgId($result[0], $mobiles, ';');
				}

			} else {
				//长短信，生成消息ID
				$nlen = ceil($len / 67);

				if (0 == $V['method']) {
					$strsigns = longMsgId($result, $mobiles, $nlen, ';');
				} else {
					$strsigns = longMsgId($result[0], $mobiles, $nlen, ';');
				}

			}
			$strRet = $strsigns;
			break;

		//获取上行或状态报告
		case 1:
			$result = $sms->GetMoSMS($smsInfo);
			if (!$result) {
				$strRet = '无任何上行信息';
				break;
			}
			//错误
			if (($strRet = GetCodeMsg($result, $statuscode)) != '') {
				break;
			}

			//返回上行信息
			//日期,时间,上行源号码,上行目标通道号,*,信息内容
			//$strRet = implode(';', $result);
			if (is_array($result)) {
				$strRet = implode(';', $result);
			} else {
				$strRet = $result;
			}

			break;

		//获取状态报告
		case 2:
			$result = $sms->GetRpt($smsInfo);
			if (!$result) {
				$strRet = '无任何状态报告';
				break;
			}
			//错误
			if (($strRet = GetCodeMsg($result, $statuscode)) != '') {
				break;
			}

			//返回状态报告
			//日期,时间,信息编号,*,状态值,详细错误原因  状态值（0 接收成功，1 发送暂缓，2 发送失败）
			if (is_array($result)) {
				$strRet = implode(';', $result);
			} else {
				$strRet = $result;
			}

			break;

		//获取余额
		case 3:
			$result = $sms->GetMoney($smsInfo);
			//错误
			if (($strRet = GetCodeMsg($result, $statuscode)) != '') {
				break;
			}

			//返回余额
			if (0 == $V['method']) {
				$strRet = $result;
			} else {
				$strRet = $result[0];
			}

			break;
		case 4:
			$result = $sms->GetMoAndRpt($smsInfo);

			if (!$result) {
				$strRet = '无任何上行信息和状态报告';
				break;
			}
			//错误
			if (($strRet = GetCodeMsg($result, $statuscode)) != '') {
				break;
			}

			//返回上行信息
			//日期,时间,上行源号码,上行目标通道号,*,信息内容
			$strRet = implode(';', $result);
			break;
		case 5:
			$smsInfo['multixmt'] = $V['sefmsg'];
			$smsInfo['multixmt'] = str_replace("\\\\", "\\", $smsInfo['multixmt']);

			$result = $sms->SefsendSMS($smsInfo);

			if (0 == $V['method']) {
				$strRet = str_replace("\n", "", $result);
			} else {
				$strRet = $result[0];
			}
			break;

		default:
			$strRet = "没有匹配的业务类型";
			break;
		}

		return ($strRet);
	}
}
