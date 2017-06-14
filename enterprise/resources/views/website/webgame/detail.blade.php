@extends('website.webgame.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title', $game["name"])

@if($platform=="pc")
@section('css-webgame')
<link href="{{static_res('client/style/webgameDetail.css')}}" rel="stylesheet">
@endsection
@endif

@section('webgameRight')
<div class="right_con fr detail_container webpageGame">
    <div class="detail clearfix">
        <div class="left_contain fl">
        <h3>{{$game["name"]}}</h3>
        <div class="lanrenzhijia">
            <div id="picBox" class="picBox">
                <ul class="cf">
                    @if (isset($game["img_list"]) && $game["img_list"] && is_array($game["img_list"]))
                    @foreach ($game["img_list"] as $img)
                    <li><a href="javascript:;"><img src="{{static_image($img,100)}}" /><i class="btn-link-i"></i></a></li>
                    @endforeach
                    @endif
                </ul>
            </div>
            <div id="listBox" class="listBox">
                <ul class="cf">
                    @if (isset($game["img_list"]) && $game["img_list"] && is_array($game["img_list"]))
                    @foreach ($game["img_list"] as $img)
                    <li><div class="arrow-up pa"></div><img src="{{static_image($img,100)}}" /></li>
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
    </div>
    <div class="right_contain fl">
        <div class="get_gift">
            @if(isset($myserverid) && $myserverid > 0)
            <input class="btn start-web-game" type="button" game-id="{{$game['appid']}}" game-name="{{$game["name"]}}" value="开始游戏" @if(isset($playedServer)) server-id="{{$playedServer["serverid"]}}" server-name="{{$playedServer["name"]}}" @endif/>
            @else
            <input class="btn" type="button" value="敬请期待"/>
            @endif
            <!-- <p class="pr">
                <span>
                    <input type="button" id="getGiftBtn" value="领取礼包" />
                </span>
            </p>
            <div class="clearfix">
                <a class="fl" href="http://www.vronline.com/charge" target="_blank" id="paybutton">账号充值</a>
                <a class="fr" href="http://bbs.vronline.com/forum.php" target="_blank" id="bbsbutton">进入论坛</a>
            </div> -->
            <p class="clearfix">
                <i class="fl"></i>
                @if(isset($myserverid) && $myserverid > 0)
                <span class="fl">{{$game["name"]}} @if(isset($playedServer)) {{$playedServer["name"]}} @endif</span>
                <a class="fr cur start-web-game-server" style="cursor:pointer;" game-id="{{ $game['appid'] }}" game-name="{{$game["name"]}}">其他区服></a>
                @else
                <span class="fl">暂未开放</span>
                @endif
            </p>
            <div class="clearfix">
                <a class="fl a_gift" href="javascript:;" @if(isset($giftInfoArr) && is_array($giftInfoArr) && $giftInfoArr && isset($myserverid) && $myserverid > 0) id="getGiftBtn" @endif ><i class="fl gift_icon"></i><span class="fl">领取礼包</span></a>
                <a class="fl a_charge datacenter-onclick-stat" style="cursor:pointer;" @if(isset($myserverid) && $myserverid > 0) href="http://www.vronline.com/charge" @endif target="_blank" id="paybutton" stat-actid="click_webgame_pay_button"><i class="fl gift_icon charge_icon"></i><span class="fl">账号充值</span></a>
                <a class="fr a_enter datacenter-onclick-stat" stat-actid="click_webgame_in_bbs_button" href="@if(isset($game['forumid']) && $game['forumid'])http://bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$game['forumid']}}@else http://bbs.vronline.com/forum.php @endif"  target="_blank" id="bbsbutton"><i class="fl gift_icon enter_icon"></i><span class="fl">进入论坛</span></a>
            </div>
        </div>
        <div class="content1">
            <div class="right_con">
                <h3>游戏评分</h3>
            </div>
            <div class="score clearfix">
                <span class="fl" id="score">{{number_format($game["score"],1)}}</span><i class="fl">分</i><p class="fl">（已有</p><b class="fl comment-num" id="comment-num">{{$game["score_num"]}}</b><p class="fl">人评分）</p>
            </div>
        </div>
        <div class="content2">
            <div class="right_con clearfix">
                <div class="fl">
                    <h3>游戏类型</h3>
                    <p>{{$game["first_class"]}}</p>
                </div>
                <div class="fl">
                    <h3>上线时间</h3>
                    <p>{{date("Y-m-d",strtotime($game["ctime"]))}}</p>
                </div>
            </div>
        </div>
        <div class="content1 game_introduction">
            <div class="right_con">
                <h3>游戏简介</h3>
            </div>
            <p>{{$game["content"]}}</p>
        </div>
    </div>
    <div class="fl detail_comment clearfix">
        <div class="fl comment ">
            <div class="comment_foot">
                <p class="have_comment clearfix">
                    <i class="fl language" data-name="has">已经有</i>
                    <b class="fl"></b>
                    <i class="fl language comment-num" data-name="evaluating">{{$game["all_comment_num"]}}</i>
                    <span>条测评</span>
                </p>
                @if (!isset($uid))
                <div class="comment_login">
                    <div class="in_com_login clearfix">
                        <p class="un_login fl language" data-name="un_login">未登录，请</p>
                        <p class="fl login_in_text language login-link" data-name="login_enter">登录</p>
                        <p class="fl language" data-name="or">或</p>
                        <p class="fl login_text language register-link" data-name="register">注册</p>
                    </div>
                </div>
                @elseif(!isset($game["comment"]))
                <div class="add_comment pr no-comment">
                    <textarea  cols="30" rows="10" id="content"></textarea>
                </div>
                <div class="in_comment_foot clearfix no-comment">
                    <div class="fl comment_grade pr" id="gradeCon">
                        <ul class="un_sel fl">
                            <li></li>
                        </ul>
                        <span class="grade_mouse pa grade"></span>
                        <span class="game_score clearfix score"><b class="fl" score="{{number_format($game["score"],1)}}">{{number_format($game["score"],1)}}</b><em class="fl language" data-name="score">分</em></span>
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
                <li class="fl cur language comment-tab" comment-type="hot">精彩评论(<span class="comment-num">{{$game["new_comment_num"]}}</span>)</li>
                <li class="fl language comment-tab" comment-type="new">最新评论(<span class="comment-num">{{$game["new_comment_num"]}}</span>)</li></li>
                @if (isset($uid))
                <li class="fl language comment-tab" comment-type="my">我的评测</li>
                @endif
            </ul>
            <div class="comment_body">
                <ol class="in_comment_body" id="comment_con">

                </ol>
                <div class="clearfix" id="page">
                    {{-- <span class="fl">上一页</span>
                    <ul class="fl clearfix">
                        <li class="fl li_bg">1</li>
                        <li class="fl">2</li>
                        <li class="fl">...</li>
                        <li class="fl">9</li>
                        <li class="fl">10</li>
                        <li class="fl">11</li>
                    </ul>
                    <span class="fl">下一页</span> --}}
                </div>
                <!--<div class="comment_page" id="comment_page"></div>-->
            </div>
        </div>
        <!-- 公告 -->
        <div class="fl announcement">
                <div class="clearfix">
                    <h3 class="fl">公告</h3>
                    <a class="fr datacenter-onclick-stat" stat-actid="click_webgame_in_bbs_button" href="@if(isset($game['forumid']) && $game['forumid'])http://bbs.vronline.com/forum.php?mod=forumdisplay&fid={{$game['forumid']}}@else http://bbs.vronline.com/forum.php @endif" target="_blank">进入论坛>></a>
                </div>


            @if(isset($notice) && !empty($notice) && is_array($notice))
                <ul>
                    @foreach($notice as $k=>$v)
                        <li>
                        @if($v['flag'] == 1)
                            <a target="_blank" href="{{ $v['url'] }}">
                                <span class="fl announcementCon red">{{ $v['title' ]}}</span>
                                <span class="fr announcementTime">[{{ $v['date'] }}]</span>
                            </a>
                        @else
                            <a target="_blank" href="{{ $v['url'] }}">
                                <span class="fl announcementCon">{{ $v['title' ]}}</span>
                                <span class="fr announcementTime">[{{ $v['date'] }}]</span>
                            </a>
                        @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <!--当没有公告内容时-->
                <div class="announcement_none">
                    <i></i>暂无公告
                </div>
            @endif
        </div>

        <input type="hidden" id="target-id" value="{{$game["appid"]}}">
        <input type="hidden" id="target-type" value="1">
        <input type="hidden" id="user-uid" value="{{isset($uid)?$uid:0}}">
    </div>
</div>
</div>
@endsection

@section('javascript-webgame')
<script src="{{static_res('/website/js/detail_video.js')}}"></script>
<script src="{{static_res('/common/js/tips.js')}}"></script>
<script src="{{static_res('/common/js/pagination.js')}}"></script>
<script src="{{static_res('/common/js/comment.js')}}"></script>
<script>
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
    $("#getGiftBtn").click(function() {
        window.location.href="http://www.vronline.com/website/packageReceive/{{$game["appid"]}}";
    });
    $("#startGame").click(function() {
        var json={
            gameid:{{$game["appid"]}},
            gamename: "{{$game["name"]}}",
        };
        var serverid=$(this).attr("server-id");
        if(serverid){
            json.areaid=serverid;
            json.areaname=$(this).attr("server-name");
        }
        PL.startGame(json);
    });

})
</script>
@endsection
