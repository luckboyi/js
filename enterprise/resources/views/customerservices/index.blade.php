@extends('news.layout')

@section('meta')
<title>VRonline客服中心</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{static_res("/news/style/customer.css")}}" />
@endsection

@section('content')
<div class="container">
    <div class="headline">
        <span>
            <img src="{{static_res("/news/images/headline.jpg")}}" />
        </span>
    </div>
    <div class="feedback">
        <h3 class="title">我要反馈</h3>
        <div>
            <ul class="clearfix">
                <li class="fl"><a href="/customer/service/question/4"><i class="device_connection"></i><span>设备连接</span></a></li>
                <li class="fl"><a href="/customer/service/question/1"><i class="account_password"></i><span>账号密码</span></a></li>
                <li class="fl"><a href="/customer/service/question/2"><i class="recharge_payment"></i><span>充值支付</span></a></li>
                <li class="fl"><a href="/customer/service/question/3"><i class="platform_game"></i><span>平台游戏</span></a></li>
                <li class="fl"><a href="/customer/service/question/5"><i class="audit_release"></i><span>审核发布</span></a></li>
                <li class="fl"><a href="/customer/service/question/6"><i class="other_issues"></i><span>其他问题</span></a></li>
            </ul>
        </div>
    </div>
    <div class="clearfix">
        <div class="fl question">
            <h3 class="title">热门问题</h3>
            <div>
                <ul class="clearfix">
                    @if($servicesQa)
                    @foreach ($servicesQa as $qa)
                        <li class="fl ells"><a class="clearfix" href="/customer/service/faqinfo/{{ $qa['id'] }}"><i class="fl"></i><span class="fl ells">{{$qa["question"]}}？</span></a></li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <div class="fr complaint">
            <i class="line"></i>
            <a href="/customer/service/question/7">
                <i class="opinion"></i>
                <p>投诉建议</p>
            </a>
            <div class="query_results clearfix"><a href="/customer/service/myquestion"><i class="fl"></i><span class="fl">查询结果</span></a></div>
        </div>
    </div>
    <div class="clearfix">
        <div class="fl inquiry">
            <h3 class="title">自助查询</h3>
            <div>
                <ul class="clearfix">
                    <li class="fl"><a href="/customer/service/faq/1"><i class=""></i><span>账号问题</span></a></li>
                    <li class="fl"><a href="/customer/service/faq/2"><i class="recharge_problem"></i><span>充值问题</span></a></li>
                    <li class="fl"><a href="/customer/service/faq/3"><i class="game_problem"></i><span>游戏问题</span></a></li>
                    <li class="fl"><a href="/customer/service/faq/4"><i class="equipment_problem"></i><span>设备连接</span></a></li>
                    <li class="fl"><a href="/customer/service/faq/5"><i class="developer"></i><span>开发者专区</span></a></li>
                </ul>
            </div>
        </div>
        <div class="fr customer_service">
            <h3 class="title">我的客服</h3>
            <div>
                <p class="clearfix"><i class="fl"></i><span class="fl">QQ交流群: 324688287</span></p>
                <p>客服在线时段:</p>
                <p>工作日（10:00-20:30）</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
@endsection
