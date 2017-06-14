@extends('layouts.website')

@inject('blade', 'App\Helper\BladeHelper')

@section('title')VRonline个人中心@endsection

@section('content')
<div class="personal_center clearfix official_personal_center">
    <div class="left_per fl">
        <ul>
            <li class="pr userMsg datacenter-onclick-stat" stat-actid="click_user_info_menu"><a href="/profile">用户资料</a></li>
            <li class="pr my_video datacenter-onclick-stat" stat-actid="click_my_video_menu"><a href="/profile/video">我的视频</a></li>
            <li class="pr problem @if($cur=='problem') cur @endif datacenter-onclick-stat" stat-actid="click_nomal_question_menu"><a href="/profile/problem">常见问题</a></li>
            <li class="pr about_vr @if($cur=='about') cur @endif datacenter-onclick-stat" stat-actid="click_about_vr_menu"><a href="/profile/about">关于VR助手</a></li>
        </ul>
    </div>
    <div class="right_per">
        <ul class="in_right_per">
            <!-- 常见问题 -->
            @if($cur=='problem')
            <li class="list_con problem_center">
                <h2 class="f20 tac fw">VRonline常见问题FAQ</h2>
                <div class="pro_content ">
                    <h4 class="f14 fw">问：如何注册VR助手账号</h4>
                    <div>
                        <p>答：1）下载并安装VR助手客户端，成功启动VR助手。</p>
                        <p class="tin">2）点击VR助手右上角的头像进入登录界面，填写账号和密码完成注册。</p>
                    </div>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我可以用第三方账号登录吗？</h4>
                    <div>
                        <p>答：VR助手目前支持QQ账号、3D播播账号、微信账号、新浪微博账号的登录。</p>
                    </div>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：账号密码忘了，如何找回？</h4>
                    <div>
                        <p>答：目前只开放手机找密码，注册账号成功后登录个人中心，建议进行手机绑定，提高账号安全性，操作方法具体如下：</p>
                        <p class="tin">步骤1：成功绑定手机。</p>
                        <p class="tin">步骤2：获取短信验证码。</p>
                        <p class="tin">步骤3：输入新密码+确认新密码。</p>
                        <p class="tin">步骤4：密码找回成功，使用新密码登录。</p>
                    </div>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我是用第三方账号登录，如何修改账号？</h4>
                    <p>答：通过第三方账号登录，即作为游客身从登录VR助手。您可以进入“个人中心→账号安全”，填写新的VR助手账号+新密码进行修改，每个游戏账号只提供1次修改机会。</p>
                </div>
                <div class="pro_content">
                    <h4>问：如何绑定手机/解绑手机/修改绑定手机操作？</h4>
                    <p>答：进入“个人中心”通过绑定手机+验证码，完成绑定/解绑/修改等操作。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：现在在VR助手上面玩VR游戏，我要付费吗？</h4>
                    <p>答：VR助手目前提供的游戏都是免费的，不需要支付任何费用，以后请留意官网最新动态。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我要如何玩VR助手内提供的VR游戏？</h4>
                    <p>答：步骤1：登录VR助手，选择“VR游戏”，选择任意一款VR游戏进入游戏详情页。</p>
                    <p class="tin">步聚2：点击“安装”，安装成功。</p>
                    <p class="tin">步骤3：安装设备驱动和连接线正确与电脑连接。</p>
                    <p class="tin">步聚4：点击“开始”按钮，启动游戏。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：如何安装本地VR游戏至VR助手内？</h4>
                    <p>答：点击VR游戏左侧底部“添加游戏”，可选择本地桌面的VR游戏安程包进行安装。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：VR游戏目前支持哪几款VR设备？</h4>
                    <p>答：Deepoon E2、HTC VIVE、DPVR E3、Oculus Rift、OSVR HDK1等市面上主流的VR硬件设备。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：如何安装本地游戏至VR助手内？</h4>
                    <p>答：点击VR游戏左侧底部“添加游戏”，可选择本地桌面的VR游戏安程包进行安装。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我想玩魔兽世界，用VR怎么游戏？</h4>
                    <p>答：您可以先将魔兽世界添加至本地游戏，然后用VR设备连接VR助手，成功连接后启动游戏并戴上您的设备体验魔兽世界。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我使用Oculus设备，无法正常启动VR？</h4>
                    <p>答：Oculus 设备可能需要在菜单中将“General→Unknown Sources→设置 为开启”</p>
                    <p><img src="http://image.vronline.com/newsimg/d14afa12e43df95fdbb36be06bbfd29f1494591822600.png" style="width:800px;"></p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：充值V币，如何使用？</h4>
                    <p>答：VRonline充获得可以获得对应数据的V币。作为VR助手统一货币，目前可用于支付或购买VR游戏，更多功能敬请留意后续动态。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：目前平台支持哪些方式的充值？</h4>
                    <p>答：支付宝充值、网银-支付宝、银行卡充值、微信充值。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我充值时，但平台虚拟币还有余额，可以合并支付吗？</h4>
                    <p>答：可以。用户可以选择使用V币余额支付抵扣一部分的充值金额或全额支付（余额足够充值页游的金额），或不需要使用平台余额而采用全额现金充值。</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我想提些意见，可以去哪里反馈？</h4>
                    <p>答：VR助手内可以至右上角下拉菜单“意见”中反馈。官网可以至右侧侧边栏中通过在线客户或意见进行反馈</p>
                </div>
                <div class="pro_content">
                    <h4 class="f14 fw">问：我想在线咨询客服，请哪去哪里可以联系？</h4>
                    <p>答：您可以申请加入VR助手官方运营群，Q群号： 526823220，管理员会第一时间为您解答。</p>
                </div>
            </li>
            @elseif($cur=='about')
             <!-- 关于VR -->
            <li class="aboutVR">
                <h3>关于VR</h3>
                <ul>
                    <li class="clearfix">
                        <div class="fl left_li">
                            <span></span>
                            <p>VR到底是什么？</p>
                        </div>
                        <div class="fl right_li">
                            <p>虚拟现实（Virtual Reality,简称VR）。利用计算机图像处理能力和外部设备模拟产生可交互的三维虚拟空间，提供使用者关于视觉，听觉，触觉等感官的模拟，让使用者如同身临其境。</p>
                        </div>
                    </li>
                    <li class="clearfix user_platform">
                        <div class="fl left_li">
                            <span></span>
                            <p>我们是什么样的平台？</p>
                        </div>
                        <div class="fl right_li">
                            <p>虚拟现实（Virtual Reality,简称VR）。利用计算机图像处理能力和外部设备模拟产生可交互的三维虚拟空间，提供使用者关于视觉，听觉，触觉等感官的模拟，让使用者如同身临其境。</p>
                        </div>
                    </li>
                    <li class="clearfix experience_vr">
                        <div class="fl left_li">
                            <span></span>
                            <p>我们要如何体验VR?</p>
                        </div>
                        <div class="fl right_li">
                            <div>
                                <img src="{!! static_res('/website/images/experience01.png') !!}" />
                                <p>1 首先您需要一套VR设备 (大朋头盔、HTC VIVE、 OUCLUS)</p>
                            </div>
                            <span></span>
                            <div>
                                <img src="{!! static_res('/website/images/experience02.png') !!}" />
                                <p>2 打开VRonline客户端，选择一款VR游戏</p>
                            </div>
                            <span></span>
                            <div>
                                <img src="{!! static_res('/website/images/experience03.png') !!}" />
                                <p>3下载/安装游戏</p>
                            </div>
                            <span></span>
                            <div>
                                <img src="{!! static_res('/website/images/experience04.png') !!}" />
                                <p>4 连接设备 开始游戏</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
            @endif
        </ul>

    </div>
</div>
@endsection
