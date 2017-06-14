@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>充值</title>
        <link rel="stylesheet" href="{{static_res('/common/style/base.css')}}">
        <link rel="stylesheet" href="{{static_res('/common/style/pay.css')}}">
        <script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}" type="text/javascript"></script>
        <script src="{{static_res('/common/js/tips.js')}}" type="text/javascript"></script>
        <script src="{{static_res('/common/js/messenger.js')}}"></script>
        <script src="{{static_res('/pay/payFun/payFun.js')}}" type="text/javascript"></script>
        <script src="{{static_res('/website/js/tinyscrollbar.js')}}"></script>
        <style type="text/css">
        .thumb{
            display: block;
        }
        .charge_con{
            height: 358px;
            overflow: hidden;
            padding:20px 12px;
            top:0;
            left: 0;
            transform:none;
            transform:none;
            -ms-transform:none; /* IE 9 */
            -moz-transform:none;    /* Firefox */
            -webkit-transform:none; /* Safari 和 Chrome */
            -o-transform:none;  /* Opera */
        }
        .charge_con .charge_content .remind{
            float: right;
            left: -195px;
            top: -18px;
        }
        .viewport{
            position: relative;
            height: 358px;
            overflow: hidden;
        }
        .overview { position: absolute; left: 0; top: 0; padding: 0; margin: 0; }
        </style>
    </head>
    <body style="background: #000">
        <div class="charge_con pr" >
            <div class="scrollbar pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
            <div class="viewport">
                <div class="overview">
                    <h3>购买</h3>
                    @if($platform=="pc")
                    <i class="close_btn close-pay-con" style="top:1px"></i>
                    @endif
                    <div class="charge_content">
                        <p class="clearfix charge_game">
                            <span class="fl img_box">
                                <img src="{!! $faceUrl !!}" width="100%" height="100%">
                            </span>
                            <span class="fl">
                                <span class="game_num">
                                    <b class="blueColor cud">{{$game["gameb_name"]?$game["gameb_name"]:"元宝"}}</b>
                                    <b class="cud"></b>
                                </span>
                                <b>价格：</b>
                                <b class="blueColor">{{$total}}元</b>
                            </span>
                            <span class="fr balance">
                                <span>{{config("common.platform_coin_name")}}余额：</span>
                                <b class="platformCoin">{{$money}}</b>
                                <b>V</b>
                            </span>
                        </p>
                       {{--  <p class="clearfix charge_num">
                            <span class="fl cud title">购买数量：</span>
                            <span class="fl sub cud">-</span>
                            <input type="text" class="fl" name="buy_num" value="1">
                            <span class="fl add cud">+</span>
                        </p> --}}
                        <p class="charge_method" id="pay-channels">
                            <span class="cud title">付款方式：</span>
                            {{-- <span name="3" class="cud method_list">银行卡</span>
                            <select class="cud method_list">
                                <option name="4" value="">更多</option>
                                <option name="5" value="">更多</option>
                                <option name="6" value="">更多</option>
                            </select> --}}
                        </p>
                        <div class="method_con clearfix">
                            <p class="fl pr charge_pay pay-num">
                                <span class="cud title">交易金额：</span>
                                <span class="cud pay_money pr vr_pay cur">
                                    <b class="blueColor amount"></b>
                                    <em class="blueColor unit" style="vertical-align: bottom;padding: 0 2px;"></em>
                                </span>
                            </p>
                            <p class="fl payment_remind" id="showCoin">
                                <i class="sel pa selected choice_box"></i>
                                <span class="cud title">{{config("common.platform_coin_name")}}支付：</span>
                                <b class="blueColor coin-amount"></b>
                                <b class="blueColor">V</b>币
                            </p>
                            <p class="fl" style="margin: 20px 0;">
                                <span class="errorinfo pr" style="color:red; line-height: 30px; margin: 10px 10px;"></span>
                            </p>
                        </div>
                        <div id="button-container"></div>
                        <span class="remind">下订单之前，请您确认订单信息</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- php渲染的参数 -->
        <div id="hideParam" style="display: none">
            <input type="hidden" name="unit_price" value="{{$price}}">
            <input type="hidden" name="uid" value="{{$uid}}">
            <input type="hidden" name="price" value="{{$price}}">
            <input type="hidden" name="vrkey" value="{{$token}}">
            <input type="hidden" name="paytoken" value="{{$paytoken}}">
            <input type="hidden" name="itemid" value="{{$itemid}}">
            <input type="hidden" name="item" value="{{$item}}">
            <input type="hidden" name="game_type" value="{{$appid}}">
            <input type="hidden" name="openid" value="{{$openid}}">
            <input type="hidden" name="num" value="{{$num}}">
            <input type="hidden" name="money" value="{{$money}}">
            <input type="hidden" name="pay_rate" value="{{$payRate}}">
            <input type="hidden" name="try_uid" value="">
            <input type="hidden" name="callback_url" value="https://pay.vronline.com/callback">
            <input type="hidden" name="jump_url" value="http://payres.vronline.com/result">
            <input type="hidden" name="surl" value=":">
            <input type="hidden" name="from" value="{{$from}}">
            <input type="hidden" name="sid" value="{{$serverid}}">
            <input type="hidden" name="wp_pid" value="">
            <input type="hidden" name="wp_uid" value="">
            <input type="hidden" name="pay_rmb" value="">
            <input type="hidden" name="pay_total" value="">
            <input type="hidden" name="login_openid" value="">
            <input type="hidden" name="login_type" value="">
            <input type="hidden" name="attach" value="">
            <input type="hidden" name="game_owner" value="">
            <input type="hidden" name="is_contract" value="0">
            <input type="hidden" name="sign" value="">
            <input type="hidden" name="user_ip" value="10000">
            <input type="hidden" name="isdev" value="{{$isdev?$isdev:0}}">
            <input type="hidden" name="extra1" value="{{$extra1}}">
            <input type="hidden" name="total" value="{{$total}}">
            <input type="hidden" name="channel" value="">
            <input type="hidden" name="pay_vr" value="1">
            <input type="hidden" name="env" value="{{$env}}">
            <input type="hidden" name="pay_coin" value="">
        </div>
        <div id="payFrameCon" style="display:none;position: absolute; width: 76%; left:12%; height: 350px; background: #fff;top:25px;">
            <div style="position: absolute;display: block;width: 18px;height: 20px;right: 14px;top: 10px;background: url(http://pic.vronline.com/common/images/icon.png) no-repeat -96px -200px; cursor:pointer;" class="close_btn close-pay-frame"></div>
            <iframe name="payFrame" id="payFrame" frameborder="0" style="overflow: hidden;" width="100%" height="100%" scrolling="no"></iframe>
        </div>
    </body>
</html>
<script type="text/javascript">
document.domain = "vronline.com";
PayFun.init({
    funType: "mini",
    channels:{!!json_encode($payChannels)!!},
    banks:{!!json_encode($banks)!!}
});
var retimes = 0;
window.addEventListener('message',function(e){
     if(typeof(e.data)=="object") {
        if(e.data.tp=="close") {
            if (typeof window.CppCall == "function") {
                window.CppCall('common', 'close', null);
            }
        } else if(e.data.tp=="pay") {
            retimes = 0;
            queryPay(e.data.orderid,function(ret){
                window.frames[0].postMessage({tp:"pay","ret":ret},'*');
            })
        }
     } else {
        return;
     }
},false);


function queryPay(orderid,callback) {
     $.get("https://pay.vronline.com/resultapi/" + orderid, function(res) {
        if(res.code=="undefined") {
            callback(0)
        } else {
            if ( res.code == 0) {
                callback(0);
            } else if (res.code==2) {
                retimes++;
                if(retimes>=5) {
                    callback(2)
                } else {
                    setTimeout(function(){
                        queryPay(orderid,callback)
                    },1000)
                }
            } else {
                callback(1)
            }
        }
    }, "json");
}
</script>
