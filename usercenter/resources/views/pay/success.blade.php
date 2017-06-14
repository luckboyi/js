<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>充值成功</title>
    <link rel="stylesheet" href="http://pic.vronline.com/common/style/base.css">
    <script src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
</head>
<body style="background:#151920;">
@if(isset($wait))
    <div class="error_charge show tac wind_error_charge" id="wait">
        <h4 style="text-indent:20px">订单处理中</h4>
        <p>充值订单号：<b>{{ $orderid }}</b></p>
        <p style="margin-top:10px"><img src="//pic.vronline.com/website/min/images/loading.gif" style="width:64px;height:64px" /></p>
        <span class="continue_charge cur close">关闭</span>
    </div>
    <div class="suc_charge  tac wind_error_charge hide"  id="success">
    <h4>成功充值</h4>
    <p>充值订单号：<b>{{ $orderid }}</b><!-- ,您已经成功充值<i class="blueCor">10元</i> --></p>
    <p>现在开始体验VRonline平台给您带来的精彩内容吧！</p>
    @if (in_array($from, ["game","vrgame"]))
    <span class="continue_charge cur close">关闭</span>
    @else
    <span class="continue_charge cur">继续充值</span>
    @endif
    </div>
    <div class="error_charge tac window_sucpay hide"  id="fail">
        <h4 >充值失败</h4>
        <p>很抱歉，充值失败，我们会尽快处理，请稍后再试！</p>
        <p>充值订单号：<b>{{ $orderid }}</b></p>
        @if (in_array($from, ["game","vrgame"]))
        <span class="continue_charge cur close">关闭</span>
        @else
        <span class="continue_charge cur">返回</span>
        @endif
    </div>
@else
<div class="suc_charge show tac wind_error_charge">
    <h4>成功充值</h4>
    <p>充值订单号：<b>{{ $orderid }}</b><!-- ,您已经成功充值<i class="blueCor">10元</i> --></p>
    <p>现在开始体验VRonline平台给您带来的精彩内容吧！</p>
    @if (in_array($from, ["game","vrgame"]))
    <span class="continue_charge cur close">关闭</span>
    @else
    <span class="continue_charge cur">继续充值</span>
    @endif
@endif
</div>
</body>
</html>
<script type="text/javascript">
    document.domain = "vronline.com";
    var orderData = {itemid:{{$itemid}}};
    var orderid = "{{ $queryorderid }}"

    @if($from=="game" || $from=="vrgame")
    $(".close").click(function(){
        console.log("close")
        window.parent.postMessage({tp:"close"},'*');
        self.close();
        // var msg={
        //     call:"closePay"
        // };
        // if(parent.messenger){
        //     parent.messenger.targets["gameCon"].send(JSON.stringify(msg));
        // }else{
        //     self.close();
        // }
    });
    @elseif($from == "vrgamein")
    $(".close").click(function(){
        if (typeof window.CppCall == "function") {
            window.CppCall('common', 'close', null);
        }
    });
    @else
    $(".continue_charge").click(function(){
        if (typeof window.CppCall == "function") {
            window.CppCall('common','reloadpay','')
        }else{
            parent.location.href="//www.vronline.com/charge";
        }
    });
    @endif

    window.addEventListener('message',function(e){
     if(typeof(e.data)=="object") {
         if(e.data.tp=="pay") {
           if(e.data.ret==0) {
                payBack(true)
                $("#wait").removeClass('show').addClass('hide');
                $("#success").removeClass('hide').addClass('show');
           } else {
                payBack(false)
                $("#wait").removeClass('show').addClass('hide');
                $("#fail").removeClass('hide').addClass('show');
           }
        }
     } else {
        return;
     }
    },false);

    function queryPay() {
        window.parent.postMessage({tp:"pay",orderid:orderid},'*');
    }
    function payBack(ret) {
        if(ret==true) {
            var obj = {
                code: 0,
                data:orderData
            };
        } else {
            var obj = {
                code: -1
           };
        }
        if(parent.PayFun){
            if(typeof parent.PayFun.fn.payCallBack=="function"){
                parent.PayFun.fn.payCallBack(obj);
            }
        } else if(window.opener){
            if(typeof window.opener.PayFun.fn.payCallBack=="function"){
                window.opener.PayFun.fn.payCallBack(obj);
            }
        }
    }

    @if(!isset($wait))
        payBack(true)
    @else
        queryPay()
    @endif
</script>
