<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VRonline-开放平台</title>
    <link rel="stylesheet" href="{{static_res('/btobEdition/css/base.css')}}" />
    <link rel="stylesheet" href="{{static_res('/btobEdition/css/index.css')}}/">
</head>
<body>
    <div class="banner_container">
        <div class="nav">
            <div class="in_nav clearfix">
                <div class="logo fl">
                    <a href="/"><img src="{{static_res('/btobEdition/images/logo.png')}}"></a>
                </div>
                <ul class="in_nav_con fr">
                    <li class="fl home">
                        <a href="//www.vronline.com">VRonline首页</a>
                    </li>
                    @if(!$uid)
                    <li class="fl">
                        <a href="javascript:;" id="loginShowBtn">登录</a>
                    </li>
                    <li class="fl division">|</li>
                    <li class="fl">
                        <a href="javascript:;" id="regShowBtn">注册</a>
                    </li>
                    @else
                    <li class="fl">
                        <a href="/enter">{{$account}}</a>
                    </li>
                    <li class="fl">
                        <a href="/logout">退出</a>
                    </li>
                    @endif
                    <li class="fl">
                        <a href="javascript:;" class="defBlueCol pr"><i class="icon download_icon pa"></i>下载企业版</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--轮播-->
        <div class="banner_con pr">
            <div class="in_banner_con pr">
                <ul>
                    @foreach($banners as $banner)
                    <?php $i = 0;
$i++;?>
                    <li class="{{$i==1?"cur":""}}">
                        <a href="{{$banner["target_url"]}}" style="background: url('{{static_image($banner["cover"])}}')  no-repeat center"></a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="banner_btn pa tac">
                <ol class="clearfix">
                </ol>
            </div>
        </div>
        <!--轮播-->
    </div>
    <div class="content"></div>
    @if($uid && !$merchantInfo)
    <div class="suc_login shade_div">
        <div class="in_shade_div pa">
            <div class="header clearfix pr">
                <i class="close_icon icon fr "></i>
            </div>
            <div class="body">
                <p class="tal">
                    你好，<b>用户名</b>：
                </p>
                <div>
                    <p>欢迎您使用VR助手体验店管理系统！</p>
                    <p>您当前的账号还不是体验店专属管理账号，</p>
                    <p>想要使用更多功能，请申请体验店专属账号。</p>
                </div>
                <p class="foot">
                    <span class="enter">申请体验店专属账号</span>
                    <a href="javascript:;" class="next_time">下次再说</a>
                </p>
            </div>
        </div>
    </div>
    @endif
</body>
</html>
<script type="text/javascript" src="{{static_res('/common/js/messenger.js')}}"></script>
<script language="JavaScript" src="{{static_res('/news/js/jquery-1.12.3.min.js')}}"></script>
<script type="text/javascript" src="{{static_res('/webgames/js/login.js')}}"></script>
<script type="text/javascript">
    (function($){
        $.fn.bannerPlay = function(options){
            var defaults = {
                auto:true,
                time:2000,
                cur:0,
                banner_class:'cur',
                current:'cur',
                timeshow:500
            }
            var opts = $.extend(defaults,options);
            var base = this ;
            var obj = $(this);
            var aul = obj.find('ul');
            var ali = aul.find('li');
            var aol = obj.find('ol');
            var len = ali.length;
            var timer;
            var index = opts.cur;
            //创建btn
            for(var i= 0 ; i < len ; i++){
                aol.append('<li class="fl"></li>');
            }
            base.init = function(){
                aol.find('li').eq(index).addClass('cur').siblings().removeClass('cur');
                aul.find('li').eq(index).addClass('cur').siblings().removeClass('cur');
                var _this = this;
                //hover的时候
                _this.hover();
                //如果支持自动播放
                if(opts.auto == true){
                    _this.animation();
                }else{
                    _this.clearAnimation();
                }
            };
            //hover
            base.hover = function(){
                var _this = this ;
                $("ol li",_this).hover(function(){
                    $(this).addClass(opts.current).siblings('').removeClass(opts.current);
                    var i = index = $(this).index();
                    $('ul li',_this).eq(i).addClass(opts.banner_class).siblings('').removeClass(opts.banner_class)
                    _this. change(i);

                },function(){
                    _this.animation();
                });
                $('ul li',_this).hover(function(){
                    _this.clearAnimation()
                },function(){
                    _this.animation()
                })
            }
            base.animation = function(){
                var _this = this;

                _this.clearAnimation();
                //循环定时器
                timer = setInterval(function(){
                    index = index+1;
                    if(index> len-1){
                        index = 0;
                    }
                    _this.change(index);
                }, opts.time);

            };
            base.change = function(i){
                var _this = this;
                $('ul li',_this).eq(i).css({zIndex: '9'}).addClass(opts.banner_class).animate({
                    opacity: 1,
                },0).siblings('').css({zIndex: '1'}).removeClass(opts.banner_class).animate({
                    opacity: 0,
                },0);
                $('ol li',_this).eq(i).addClass(opts.current).siblings('').removeClass(opts.current);
            }
            base.clearAnimation = function(){
                clearInterval(timer);
            };
            base.init();
        }
    })(jQuery);

    $('.banner_con').bannerPlay();


</script>
<script>
    $(function(){
        //点击关闭
        $('.close_icon').on('click',function(){
           $(this).parents('.shade_div').hide();
        });
        //点击下一次再说
        $('.next_time').on('click',function(){
            $(this).parents('.shade_div').hide();
        });

        $(".enter").click(function(event) {
            window.location.href="/enter";
        });
    });
    webgameLogin.init({
        popDomain:"tob.vronline.com",
        showThird:false
    });

</script>
