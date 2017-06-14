@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')
@section('title')VRonline充值中心@endsection

@section('css')
<link rel="stylesheet" href="{{static_res('/common/style/pay.css')}}">
<style type="text/css">
    .pageGame_sel{
        display: none;
    }
    .choiced{
        background-position: 0 -16px;
    }
</style>
@endsection

@section('content')
<div class="charge_container clearfix">
    <div class="fl charge_title">
        <ul>
            <?php $i = 0;?>
            @foreach ($payChannels as $key => $channel)
                <?php $i++;?>
                <li class="{{$i==1?"cur ":""}}pr datacenter-onclick-stat" channel="{{$key}}"><i class="{{$channel["icon"]}}"></i><span>{!! $channel["title"]??"" !!}</span></li>
            @endforeach
        </ul>
    </div>
    <div class="fl in_charge_container paymentCenter">
        <div class="in_charge_con">
            <h4 class="wdHoverColor">充值账号</h4>
            <p class="con">
                <span>平台账号：</span>
                <span class="name" user-id="{{$uid}}" token="{{$token}}">{{$nick}}</span>
                <span class="blueColor2 platform_balance">V币余额：</span>
                <span class="blueColor2 has_v_num balance_num">{{$money}}</span>
            </p>
            <div class="con pay_sel_btn select-item charge_sel">
                <span class="cur vr_charge" item="platformCoin">V币充值</span>
                <!-- <span class="pageGame_charge" item="gameCoin">页游充值</span> -->
            </div>{{--
            <p class="con charge_sel">
                <span class="cur">充值V币</span>
                <span>网页游戏</span>
            </p> --}}
        </div>
        <div class="in_charge_con">
            <h4 class="wdHoverColor">选择充值金额 <span class="pay-rate">(兑换比例￥1:{{$payRate}}V币)</span></h4>
            <p class="con pageGame_sel">
                <span>游戏名称：</span>
                <select  name="list" id="gameSelect">
                    @foreach ($allgame as $game)
                    <option class="blueCor" value="{{$game["appid"]}}"@if($appid==$game["appid"]) selected="selected" @endif>{{$game["name"]}}</option>
                    @endforeach
                </select>
                <span class="sel_server">选择服务器：</span>
                <select name="list" id="serverSelect" init="{{$serverid}}">
                </select>
                <span class="hasSevers">
                    <span class="mal30 total_scale">支付比例：</span>
                    <span>￥</span>
                    <span>1:</span>
                    <span class="scale_num">100</span>
                    <span class="unit"></span>
                </span>
            </p>
            <p class="con charge_num payment_num">
                <span class="cur" num="100">￥100</span>
                <span num="500">￥500</span>
                <span num="2000">￥2000</span>
                <span num="3000">￥3000</span>
            </p>
            <p class="con other_num clearfix pr">
                <span class="other_title fl">其他金额：</span>
                <span class="other_input fl pr">
                    <input type="text" id="pay-num" placeholder="填写1~3000之间的整数" value="100">
                    <i class="pa"><span class="errorColor pa " >金额填写1~3000之间的整数，不含小数点</span></i>
                </span>
                <span class="hasSevers">
                    <span class="mal30 pageGame_sel">获得游戏币：</span>
                    <span class="blueColor pageGame_sel" id="getGameCon"></span>
                    <span class="unit pageGame_sel"></span>
                </span>
                <span class="errorColor pa errorMsg erro_tips">金额填写1~3000之间的整数，不含小数点</span>
            </p>
            <div class="con pay_v pageGame_sel">
                <p>
                    <span>V币余额：</span>
                    <span class="blueColor balance_num">{{$money}}</span>
                    <span class="blueColor">点</span>
                    <span class="pr mal30"><i class="sel pa choice_box" id="use_vr"></i>使用V币支付本次交易</span>
                </p>
                <p class="use_v choice_vr_pay">
                    <span>支付总额：</span>
                    <span class="whiteColor">￥</span>
                    <span class="whiteColor total_num"></span>
                    <span>/</span>
                    <span class="blueColor use_v_num user_num"></span>
                    <span class="blueColor">点被使用</span>
                </p>
                <p>
                    <span>你需支付：</span>
                    <span>￥</span>
                    <span class="need_num" id="need-num"></span>
                </p>
            </div>
            <p class="con sel_pay" id="button-container">
            </p>
            <div class="con clearfix pay_tips">
                <span class="fl"></span>
                <div class="fl">
                    <p>您的V币数余额不足，无法支付。您可以通过以下方法进行充值：</p>
                    <p>方法1：对平台账号充值VR点数。<a href="javascript:;" class="blueColor">如何获得V币数>></a></p>
                    <p>方法2：使用支付宝进行支付。</p>
                </div>
            </div>
        </div>
        <div class="in_charge_con">
            <h4  class="wdHoverColor">温馨提示<span class="fr"><a href="javascript:;" class="blueColor f12  pr"><i class="pa"></i>联系客服</a></span></h4>
            <div class="tips_con">
                <p>1、您充入的V币不可返还，并且无法提现，某些游戏和（或）服务可能会收费额外费用。</p>
                <p>2、支付宝账户额支付：只要您的支付宝账户中存有余额，就可以为游戏进行充值。</p>
                <p>3、使用支付宝支付，对消费者来说，目前不需要任何的手续费。</p>
                <p>想要了解更多信息，请阅读 <a href="http://www.vronline.com/license/service" target="_blank" class="wdHoverColor">《VRonline许可及服务协议》</a>和 <a href="http://www.vronline.com/license/service" target="_blank" class="wdHoverColor">《销售条款》</a></p>
            </div>
        </div>
    </div>
</div>
<div id="hideParam" style="display: none">
    <input type="hidden" name = "pay_rate" value="{{$payRate}}">
    <input type="hidden" name="money" value="{{$money}}">
    <input type="hidden" name = "price" value="">
    <input type="hidden" name = "num" value="1">
    <input type="hidden" name = "itemid" value="0">
    <input type="hidden" name = "uid" value="{{$uid}}">
    <input type="hidden" name = "try_uid" value="">
    <input type="hidden" name = "vrkey" value="{{$token}}">
    <input type="hidden" name = "callback_url" value="https://pay.vronline.com/callback">
    <input type="hidden" name = "jump_url" value="http://payres.vronline.com/result">
    <input type="hidden" name = "surl" value=":">
    <input type="hidden" name = "from" value="plat">
    <input type="hidden" name = "paytoken" value="{{$paytoken}}">
    <input type="hidden" name = "game_type" value="">
    <input type="hidden" name = "sid" value="">
    <input type="hidden" name = "wp_pid" value="">
    <input type="hidden" name = "wp_uid" value="">
    <input type="hidden" name = "pay_total" value="">
    <input type="hidden" name = "pay_rmb" value="">
    <input type="hidden" name = "login_openid" value="">
    <input type="hidden" name = "login_type" value="">
    <input type="hidden" name = "attach" value="">
    <input type="hidden" name = "game_owner" value="">
    <input type="hidden" name = "is_contract" value="0">
    <input type="hidden" name = "sign" value="">
    <input type="hidden" name = "user_ip" value="1000">
    <input type="hidden" name = "channel" value="">
    <input type="hidden" name = "pay_vr" value="1">
    <input type="hidden" name = "isdev" value="{{$isdev?$isdev:0}}">
    <input type="hidden" name = "env" value="{{$env}}">
    <input type="hidden" name = "appid" value="{{$appid}}">
    <input type="hidden" name = "hasRole" value="0">
</div>
<div class='mask_layer ali_window' style='display: none;' id="payFrameCon">
    <div class='aliPay_window pr' style='border:0;background:#fff; position: absolute; top:50%;left: 50%;z-index: 999; transform: translate(-50%,-50%); width:450px; height:530px;'><i class='close pa'></i>
        <iframe scrolling='no' id="payFrame" width='100%' height='100%'  frameborder='no' border='0' src=''></iframe>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{static_res('/common/js/tips.js')}}" type="text/javascript"></script>
<script src="{{static_res('/pay/payFun/payFun.js')}}" type="text/javascript"></script>
<script>
document.domain = "vronline.com";
$(function(){
    window.

    PayFun.init({
        funType: "web",
        channel:"alipay",
        channels:{!!json_encode($payChannels)!!},
        banks:{!!json_encode($banks)!!}
    });

    $('.charge_title li').on('click',function(){
        var n = $(this).index();
        $(this).addClass('cur').siblings().removeClass('cur');
        var channel=$(this).attr("channel");
        PayFun.fn.changeChannel(channel);
    });
});
</script>
@endsection
