<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="http://pic.vronline.com/common/style/base.css">
    <script src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
</head>
<body style="background:#151920;">
    <div class="error_charge show tac window_sucpay">
        <h4 >充值失败</h4>
        <p>很抱歉，充值失败，我们会尽快处理，请稍后再试！</p>
        <p>充值订单号：<b>{{ $orderid }}</b></p>
        @if (in_array($from, ["game","vrgame"]))
        <span class="continue_charge cur close">关闭</span>
        @else
        <span class="continue_charge cur">返回</span>
        @endif
    </div>
</html>
</body>
<script type="text/javascript">
    document.domain = "vronline.com";
    if(parent.PayFun){
        if(typeof parent.PayFun.fn.payCallBack=="function"){
            var obj = {
                code: -1
            };
            parent.PayFun.fn.payCallBack(obj);
        }
    }else if(window.opener){
        if(window.opener.PayFun && typeof window.opener.PayFun.fn.payCallBack=="function"){
            var obj = {
                code: -1
            };
            window.opener.PayFun.fn.payCallBack(obj);
        }
    }

    @if($from=="game" || $from = "vrgame")
    $(".close").click(function(){
        var msg={
            call:"closePay"
        };
        if(parent.messenger){
            parent.messenger.targets["gameCon"].send(JSON.stringify(msg));
        }else{
            self.close();
        }
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
            window.CppCall('common','reloadpay','');
        }else{
            parent.location.href ="//www.vronline.com/charge";
        }
    });
    @endif
</script>
