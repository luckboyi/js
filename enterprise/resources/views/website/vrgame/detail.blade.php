@extends('website.vrgame.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')VR 游戏@endsection


<div class="applink_content_wrap">
<div class="applink_content">
    <i class="close pa" onclick="tickApp(1)"></i>
    <ul>
    <li><span class="top-text">是否安装了VR助手？</span></li>
    <li>
    <a href="vronline://type=loadgame?gameid={{ $game['appid'] }}"><button type="button" class="fl choice"><span>是的，</span><br>我已安装VR助手</button></a>
    <a href="{{url('down')}}" target="_blank"><button type="button" class="fr choice"><span>没有，</span><br>我还没安装VR助手</button></a>
    </li>
    <li class="line"></li>
    <li><div class="logo-zs fl"></div><div class="introduce fr"> 市面上最大，最全的VR游戏和VR视频资源的内容平台。独创的3D游戏VR模式，目前支持《魔兽世界》，《守望先锋》等游戏在主流VR设备上体验。</div></li>
    </ul>
</div>
</div>
@section('vrgameRight')
<div class="right_con fr detail_container">
    <div class="detail clearfix">
        <div class="left_contain fl">
            <h3 >{{$game['name']}}</h3>
            <div class="lanrenzhijia">
                <div id="picBox" class="picBox">
                    <ul class="cf">
                        @if(isset($game['imginfo']["slider"]) && is_array($game['imginfo']["slider"]))
                        @foreach ($game['imginfo']["slider"] as $img)
                        <li><a href="javascript:;"><img src="{{ static_image($img,100) }}" /><i class="btn-link-i"></i></a></li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                <div id="listBox" class="listBox">
                    <ul class="cf">
                        @if(isset($game['imginfo']["slider"]) && is_array($game['imginfo']["slider"]))
                        @foreach ($game['imginfo']["slider"] as $img)
                        <li><div class="arrow-up pa"></div><img src="{{ static_image($img,100) }}" /></li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                <div class="detail_btn">
                    <span id="prev" class="btn prev"></span>
                    <span id="next" class="btn next"></span>
                    <span class="btn_center" id="center"><em></em></span>
                </div>
            </div>
            <div class="detail_comment clearfix">
                <div class="fl comment ">
                    <div class="comment_foot">
                        <p class="have_comment clearfix">
                            <i class="fl language" data-name="has">已经有</i>
                            <b class="fl"></b>
                            <i class="fl language comment-num" data-name="evaluating">{{$game['comment_info']["all_comment_num"]}}</i>
                            <span>条测评</span>
                        </p>
                        @if (!isset($uid))
                        <div class="comment_login" style="display: block; position: static;">
                            <div class="in_com_login clearfix">
                                <p class="un_login fl language" data-name="un_login">未登录，请</p>
                                <p class="fl login_in_text language login-link" data-name="login_enter">登录</p>
                                <p class="fl language" data-name="or">或</p>
                                <p class="fl login_text language register-link" data-name="register">注册</p>
                            </div>
                        </div>
                        @elseif(!isset($game['comment_info']["comment"]))
                        <div class="add_comment pr no-comment">
                            <textarea  cols="30" rows="10" id="content"></textarea>
                        </div>
                        <div class="in_comment_foot clearfix no-comment">
                            <div class="fl comment_grade pr" id="gradeCon">
                                <ul class="un_sel fl">
                                    <li></li>
                                </ul>
                                <span class="grade_mouse pa grade"></span>
                                <span class="game_score clearfix score"><b class="fl" score="{{number_format($game['score'] ,0)}}">{{number_format($game['score'] ,0)}}</b><em class="fl language" data-name="score">分</em></span>
                            </div>
                            <div class="fr clearfix comment_btn">
                                <p class="fl language" data-name="like_game"></p>
                                <span class="fl" style='line-height:34px;'>你喜欢这款游戏么？</span>
                                <ul class="fl clearfix">
                                    <li class="fl cur language isGood" is-good="1">喜欢</li>
                                    <li  class="fl language isGood" is-good="2">不喜欢</li>
                                </ul>
                                <p class="fl send language" data-name="send" id="sendComment">发送</p>
                            </div>
                        </div>
                        <div class="has-comment" style="display: none">您已经评论过了</div>
                        @else
                        <div class="has-comment">您已经评论过了</div>
                        @endif
                    </div>
                    <ul class="comment_head clearfix">
                        <li class="fl cur language comment-tab" comment-type="hot">精彩评论(<span class="comment-num">{{$game['comment_info']["all_comment_num"]}}</span>)</li>
                        <li class="fl language comment-tab" comment-type="new">最新评论(<span class="comment-num">{{$game['comment_info']["new_comment_num"]}}</span>)</li>
                        @if (isset($uid))
                        <li class="fl language comment-tab" comment-type="my">我的评测</li>
                        @endif
                    </ul>
                    <div class="comment_body">
                        <ol class="in_comment_body" id="comment_con">
                            {{-- 评论内容 --}}
                        </ol>
                        <div class="clearfix" id="page">
                            {{-- 翻页 --}}
                        </div>
                    </div>
                </div>

                <input type="hidden" id="target-id" value="{{$game['appid']}}">
                <input type="hidden" id="target-type" value="2">
                <input type="hidden" id="user-uid" value="{{isset($uid)?$uid:0}}">
            </div>
        </div>
        <div class="right_contain fl">
            <div class="get_gift download_plateform">
                <a href="javascript:tickApp({{ $game['appid'] }})" ><input class="btn" type="button" value="开始游戏" /></a>
                <div class="clearfix">
                    <a class="fl hide" href="http://www.vronline.com/charge" target="_blank">账号充值</a>
                    <a class="fr hide" href="http://bbs.vronline.com/forum.php" target="_blank">进入论坛</a>
                </div>
                {{-- <p class="pr">
                    <span>
                        <input type="button" value="领取礼包" />
                    </span>
                </p> --}}
            </div>
            <div class="content0 content1">
                  <div class="right_con">
                      <h3>游戏价格</h3>
                  </div>
                  <div class="game_price clearfix tac ">
                      <div class="game_discount_con @if($game['sell'] > 0 && $game['sell']< $game['original_sell']) @else hide @endif">
                    <div class="game_discount fl f18 " style="width:60px;line-height:36px;background:#49b21a;">
                    -@if($game['sell'] > 0 && $game['original_sell'] != 0) {{ intval(($game['original_sell']-$game['sell'])/$game['original_sell']*100)}}@else 1 @endif%
                    </div>
                        <div class="game_price fl " style="width:60px; height:36px;background:rgba(2, 2, 10, 0.4);">
                           <p style="text-decoration:line-through; color:#d6e0ea;">{{$game['original_sell']*$payRate}}</p>
                           <p class="f14" style="color:#2cdef5;">{{$game['sell']*$payRate}}<i style="height:15px;margin-left:4px;">V</i></p>
                        </div>
                      </div>
                      <div class="fl game_price f22 @if($game['sell'] < $game['original_sell'] || $game['sell'] == 0) hide @endif" style="line-height:36PX; padding:0 20px;background:rgba(2, 2, 10, 0.4); color:#2cdef5;" >
                          {{$game['sell']*$payRate}}<i style="height:26px;margin-left:4px;">V</i>
                      </div>
                      <div class="fl gane_free f20 @if($game['sell'] == 0) @else hide @endif" style="line-height:36PX; padding:0 20px;background:rgba(2, 2, 10, 0.4); color:#2cdef5;">
                        免费
                      </div>
                  </div>
                </div>
            <div class="content1">
                <div class="right_con">
                    <h3>游戏评分</h3>
                </div>
                <div class="score clearfix">
                    <span class="fl" id="score">{{number_format($game['score'] ,1)}}</span><i class="fl">分</i><p class="fl">（已有</p><b class="fl comment-num" id="comment-num">{{$game['comment_info']["all_comment_num"]}}</b><p class="fl">人评分）</p>
                </div>
            </div>
            <div class="content2">
                <div class="right_con clearfix">
                    <h3 class="fl" style="margin-right: 28px">支持设备</h3>
                </div>
                <ul class="con_list clearfix">
                    @if(isset($game['device_types']) && is_array($game['device_types']))
                    @foreach ($game['device_types'] as $type)
                    <li title="{{$type}}"><a href="javascript:;">{{$type}}</a></li>
                    @endforeach
                    @endif
                </ul>
            </div>
            <div class="content1">
                <div class="right_con">
                    <h3>游戏简介</h3>
                </div>
                <p class="summary">{{$game['content']}}</p>
            </div>
            <div class="content1">
                <div class="right_con">
                    <h3>系统需求</h3>
                </div>
                <div class="configure">
                    @if($platform=="pc")
                    <div class="clearfix configure_title">
                        {{--
                        <span class="fl">最低配置</span> --}}
                        <span class="fl clearbg">本地配置</span>
                        <span class="fl">推荐配置</span>
                    </div>
                    <div class="configure_con local_configure_con configure_show">
                        {{-- @if (is_array($game['mini_device']))
                        @foreach ($game['mini_device'] as $k=>$v)
                        <p><span>{{$k}}:</span><span>{{$v}}</span></p>
                        @endforeach
                        @endif --}}
                    </div>
                    <div class="configure_con">
                        @if (is_array($game['recomm_device']))
                        @foreach ($game['recomm_device'] as $k=>$v)
                        <p><span>{{$k}}</span><span class="Configure_details" title="{{$v}}"">{{$v}}</span></p><!--i标签不带class="lack"属性，为对勾√，反之为×-->
                        @endforeach
                        @endif
                    </div>
                    @else
                    <div class="clearfix configure_title">
                        {{--
                        <span class="fl">最低配置</span> --}}
                        <span class="fl clearbg">推荐配置</span>
                    </div>
                    <div class="configure_con configure_show">
                        @if (is_array($game['recomm_device']))
                        @foreach ($game['recomm_device'] as $k=>$v)
                        <p><span>{{$k}}</span><span class="Configure_details" title="{{$v}}">{{$v}}</span></p><!--i标签不带class="lack"属性，为对勾√，反之为×-->
                        @endforeach
                        @endif
                    </div>
                    @endif
                    {{--
                    <div class="configure_con">
                        @if (is_array($game['mini_device']))
                        @foreach ($game['mini_device'] as $k=>$v)
                        <p><span>{{$k}}:</span><span>{{$v}}</span></p>
                        @endforeach
                        @endif
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript-vrgame')
<script src="{{static_res('/website/js/detail_video.js')}}"></script>
<script src="{{static_res('/common/js/tips.js')}}"></script>
<script src="{{static_res('/common/js/pagination.js')}}"></script>
<script src="{{static_res('/common/js/comment.js')}}"></script>
<script type="text/javascript" src="{{static_res('/pay/minipay.js')}}"></script>

<script>
var appid={{$game['appid']}};
var logo = static_image('vrgameimg/pub/'+appid+'/logo?'+Math.random());
var json={
    game_id:appid,
    logo:logo,
};

PL.callFun('gamelistframe', 'item_clicked', json);

@if($platform=="pc")
window.messenger.listen(function(msg) {
    console.log("receive msg")
    console.log(msg)
     obj = $.parseJSON(msg);
    if(typeof(obj.appid)!="undefined") {
        obj.url = '//image.vronline.com/vrgameimg/pub/'+obj.itemid+'/logo?v='+Math.random()
        VRminipay.open({
            appid:obj.appid,
            serverid:obj.serverid,
            openid:obj.openid,
            extra1:obj.extra1,
            isdev:obj.isdev,
            item:obj.item,
            itemid:obj.itemid,
            paytoken:obj.paytoken,
            url:obj.url,
            price:product_price,
            num:1,
            total:product_price,
            from:"vrgame"
        });
    } else {
        vrdb.set('localsys',obj);
        getconfigHtml(obj);
    }
});
@endif

var product_price =  {{$game['sell']}};

$(function(){
    $("#listBox li").first().addClass("on");
    banner();
    Comment.init();
    $(".login-link").click(function(){
        var obj={
            type:"login",
            referer:"vrgame/{{$game['appid']}}"
        }
        PL.callFun('loginframe', 'prelogin', obj);
    });
    $(".register-link").click(function(){
        var obj={
            type:"register",
            referer:"vrgame/{{$game['appid']}}"
        }
        PL.callFun('loginframe', 'prelogin', obj);
    });

    @if($platform=="pc")
    loadLocalCfg()
    $(".configure_title span").click(function(){
        var index=$(this).index();
        $(this).addClass('clearbg').siblings().removeClass('clearbg');
        $(".configure_con").eq(index).addClass('configure_show').siblings().removeClass('configure_show');
    });

    @endif
});
$(".applink_content_wrap").hide();
function tickApp(close) {
    if(typeof(close)!="undefined" && close==1) {
        $(".applink_content_wrap").hide();
    } else {
        $(".applink_content_wrap").show();
    }

}

function getconfigHtml(data){
    $('body').find('.local_configure_con').html('');
    $(data).each(function(index, el) {
        var html = '<p><span>'+el.txt+'</span><span class="Configure_details" title="'+el.msg+'">'+el.msg+'</span>';
        // if(el.confirm == 1){
        //     html+='<i></i></p>';
        // }else{
        //     html+='<i class="lack"></i></p>'
        // };
        $('body').find('.local_configure_con ').append(html);
    });
}


function loadLocalCfg() {
    localCfg = vrdb.get('localsys')
    if(typeof(localCfg)!="undefined") {
        getconfigHtml(localCfg)
    } else {
        var configureObj={
            os:"{{isset($game['recomm_device']["操作系统"])?$game['recomm_device']["操作系统"]:""}}",
            cpu:"{{isset($game['recomm_device']["处理器"])?$game['recomm_device']["处理器"]:""}}",
            mem:"{{isset($game['recomm_device']["内存"])?$game['recomm_device']["内存"]:""}}",
            dx:"{{isset($game['recomm_device']["DirectX版本"])?$game['recomm_device']["DirectX版本"]:""}}",
            gpu:"{{isset($game['recomm_device']["显卡"])?$game['recomm_device']["显卡"]:""}}"
        }
        PL.callFun("gameframe", "remsys", configureObj);
    }
    return ;
}

</script>
@endsection
