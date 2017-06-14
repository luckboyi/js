@extends('vrhelp.layout')
@section('meta')
<title>游戏库</title>
@endsection

@section('head')
<style type="text/css">
    .choosen{
        height:25px;
        overflow:hidden;
    }
    .paging{float:right;}
    .paging li{float:left;color:#fff;margin-left: 5px;cursor:pointer;}
</style>
@endsection


@section('content')
        <div class="main_con">
            <div class="vrgame_home">
                <div class="vrgame_home_head">
                    <div class="vrgame_banner pr fl">
                        <div class="in_vrgame_banner">
                            <ul class="">
                            @foreach($recommend['vrgame-slider']['data'] as $value)
                            @break
                                <li class="fl">
                                <a href="{{  $value['target_url'] }}">
                                    <div class="img_con" style="background-image:url('{!! static_image($value['image']['cover']) !!}');"></div>
                                    <div class="msg_con tac">
                                        <h4 class="f14">{{ $value['name'] }}</h4>
                                        <p class="information_con els">{{ $value['desc'] }}</p>
                                    </div>
                                    </a>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                        <div class="next icon pa cp"></div>
                        <div class="prev icon pa cp"></div>
                    </div>
                    <div class="fr">
                        <div id="slideBox" class="slideBox vrgame_right_banner">
                            <div class="hd">
                                <ul><li></li><li></li><li></li></ul>
                            </div>
                            <div class="bd">
                                <ul>
                                    @foreach($recommend['vrgame-top']['data'] as $value)
                                    <li class="pr"><div class="discount_con tac pa cd">{{ $value['name'] }}</div><a href="{{  $value['target_url'] }}" target="_blank"><img src="{!! static_image($value['image']['cover']) !!}" /></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vrgame_home_select">
                    <div class="select_result">
                        <ul class="clearfix choosen">

                        </ul>
                    </div>
                    <div class="select_con clearfix">
                        <h4 class="select_state fl f18">硬件设备</h4>
                        <ul class="clearfix tac fl choose-device">
                            @foreach(config("vrgame.support_device") as $id=>$device)
                                <li class="fl els cp f14" data-val="{{ $id }}">{{$device["name"]}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="select_con clearfix">
                        <h4 class="select_state fl f18">游戏类型</h4>
                        <ul class="clearfix tac fl choose-category">
                            @foreach(config("vrgame.class") as $id=>$class)
                                <li class="fl els cp f14" data-val="{{ $id }}">{{$class["name"]}}</li>
                            @endforeach
                        </ul>
                    </div>


                    <div class="select_con clearfix ranking_select_con">
                        <h4 class="select_state fl f18">综合排名</h4>
                        <ul class="clearfix tac fl sort">
                            <li class="fl els cp f14 pr size" data-val="size">游戏大小<i data-val="up" class="icon pa"></i></li>
                            <li class="fl els cp f14 pr play" data-val="play">下载次数<i data-val="down" class="icon pa"></i></li>
                            <li class="fl els cp f14 pr time" data-val="time">上架时间<i data-val="down" class="icon pa"></i></li>
                            <li class="fl els cp f14 pr sell" data-val="sell">购买价格<i data-val="up" class="icon pa"></i></li>
                        </ul>
                    </div>
                </div>
                <div class="vrgame_home_body">
                    <ul class="clearfix game-li">



                    </ul>
                </div>
                <div class="page"></div>
            </div>
        </div>
    </div>

@endsection


@section('javascript')
<script>
var sort_tp_arr = ['size','play','time','sell'];
var default_sort_tp = "size";
var default_sort_direction = "up";
var default_page = 1;
var total_page = 0;
var params = {tp:'vrgame'}
    $(function(){
        loadQuery()
        window.onhashchange=function(){
            loadQuery();
        };

        $(".hover_con").click(function(){
            $("body").append(detail_con);
            $(".detail_con").fadeIn();
        })
        $(".blue_close").click(function(){
            $(".detail_con").fadeOut();
        })

        $('.choose-category li').click(function() {
            var obj =  $(this);
            if(obj.attr('class').indexOf('cur')>=0) {
                params.category = 0;
            } else {
                params.category = obj.attr('data-val');
            }
            changeHash();
        })
         $('.choose-device li').click(function() {
            var obj =  $(this);
            if(obj.attr('class').indexOf('cur')>=0) {
                params.device = 0;
            } else {
                params.device = obj.attr('data-val');
            }
            changeHash();
        })
          $('.sort li').click(function() {
            var obj =  $(this);
            if(obj.attr('class').indexOf('cur')>=0) {
              var cur_direction = obj.find("i").attr('data-val');
               var next = cur_direction =="up"?"down":"up";
               obj.find("i").attr('data-val',next)
            }
            var sort_tp = obj.attr('data-val');
            var sort_direction = obj.find("i").attr('data-val');
            params.sort = sort_tp + " "+sort_direction;
            changeHash();
        })

        $(document).on('click','.mini_close',function(){
            var tp = $(this).parent().attr('data-val')
            params[tp]=0
            changeHash();
        })

        $(".vrgame_right_banner").slide({mainCell:".bd ul",autoPlay:true});
        //点击购买价格
        $('.buy_price_btn').on('click',function(){
            $('.sec_select_con').toggle();
            if($('.sec_select_con').css('display') == 'block'){
                $('.buy_price_btn').find('.triangle').addClass('uptriangle')
            }else{
                $('.buy_price_btn').find('.triangle').removeClass('uptriangle');
            }
        })
        $('.sec_select_con').on('click','p',function(e){
           var val=$(this).html();
           $('.buy_price_btn').find('b').html(val);
           $('.buy_price_btn').find('.triangle').addClass('uptriangle')
        })
        //详情页轮播
       $(".picFocus").slide({ mainCell:".bd ul",effect:"left",autoPlay:true });
        $('.editor_textarea textarea').focus(function(){
            $(this).parents('.editor_textarea').addClass('unfold_textarea')
        });
         $('.editor_textarea textarea').blur(function(){
            $(this).parents('.editor_textarea').removeClass('unfold_textarea')
        });
        //点击回复
        $('.detail_comment .reply_btn').on('click','.in_reply_btn',function(){
            $(this).find('.icon').toggleClass('fold_icon');
            $(this).parents('.detail_comment').find('.reply_msg ').toggle();
        });
        //点击点赞
        $('.detail_comment .evaluate').on('click','.icon',function(){
            if($(this).parents('.reply_btn').find('i.cur').length == 0){
                $(this).addClass('cur');
            }
        })
    })

function loadQuery() {
    params.device = getUrlHash('device',0)
    params.category = getUrlHash('category',0)
    params.sort = getUrlHash('sort',default_sort_tp+' '+default_sort_direction)
    params.page = getUrlHash('page',default_page)

    if(params.device>0) {
        $('.choose-device li').each(function(){
            if($(this).attr('data-val') == params.device) {
                $(this).addClass('cur').siblings().removeClass('cur');
                $('#device-choosen').remove();
                $(".choosen").append('<li class="pr fl" id="device-choosen" data-val="device">'+$(this).text()+'<i class="pa icon mini_close cp"></i></li>');
            }
        })
    } else {
        $('.choose-device li').removeClass('cur');
         $('#device-choosen').remove();
    }
    if(params.category>0) {
        $('.choose-category li').each(function(){
            if($(this).attr('data-val') == params.category) {
                $(this).addClass('cur').siblings().removeClass('cur');
                $('#category-choosen').remove();
                $(".choosen").append('<li class="pr fl" id="category-choosen" data-val="category">'+$(this).text()+'<i class="pa icon mini_close cp"></i></li>');

            }
        })
    } else {
        $('.choose-category li').removeClass('cur');
         $('#category-choosen').remove();
    }

    if(params.sort.length>0) {
        var tmp = params.sort.split(" ");
         var sort_tp, sort_direction;
        if(tmp.length!=2) {
            sort_tp = default_sort_tp
            sort_direction = default_sort_direction
        } else {
            sort_tp = tmp[0]
            sort_direction = tmp[1];
        }
        if($.inArray(sort_tp, sort_tp_arr )<0 || (sort_direction!="up" && sort_direction!="down")) {
            sort_tp = default_sort_tp
            sort_direction = default_sort_direction
        }

        var obj = $(".sort ."+sort_tp)
        obj.addClass('cur').siblings().removeClass('cur');
        var iobj = obj.find('i');
        iobj.removeClass("down_icon");
        iobj.removeClass("up_icon");
        iobj.addClass(sort_direction+"_icon");
    }
    var cur_url =  location.href
    if(cur_url.indexOf('#')>=0) {
        cur_url = cur_url+'&'
    } else {
         cur_url = cur_url+'#'
    }
    $.post("/vrhelp/search",params,function(res){
        if(res.code==0) {
            console.log(params.page+"/"+Math.ceil(res.data.total/20))
            var data_html = ''
            $.each(res.data.data,function(a,b){
                data_html = data_html+ '<li class="fl cp game_detail" data-val="'+b.id+'">'+
                            '<div class="img_con pr" style="background-image:url(\''+static_image(b.image.cover)+'\');">'+
                                '<div class="hover_con pa tac f14">'+
                                    '<span>查看详情</span>'+
                                '</div>'+
                            '</div>'+
                            '<div class="msg_con tac">'+
                                '<h4 class="f16 els">'+b.name+'</h4>'+
                                '<p class="els">'+b.desc+'</p>'+
                            '</div>'+
                        '</li>';
            })
            $('.game-li').html(data_html);
            total_page = Math.ceil(res.data.total/20);
            // console.log(total_page);
            lihtml = '';
            flag=0;
            if(params.page<4){
              npage=3;
            }
            else{
              npage=params.page;
            }
            for(var i = 1;i < total_page + 1;i++){
                if(i==1||i==2||i>npage-3&&i<parseInt(npage)+3){
                  if(i==params.page){
                    lihtml+='<a href="javascript:;" onclick="paging('+i+')" class="active">'+i+'</a>';
                  }
                  else{
                    lihtml+='<a href="javascript:;" onclick="paging('+i+')">'+i+'</a>';
                  }
                }
                else{
                  if(i>2&&npage-i>2&&flag==0){
                    lihtml+='<a class="ellipsis">...</a>';
                    flag=1;
                  }
                }
            }
            if(total_page-npage>2){
                    lihtml+='<a class="ellipsis">...</a>';
            }
            phtml='<div class="s_page"><a href="javascript:;" onclick="paging(\'p\')">上一页</a>';
            nhtml='<a href="javascript:;" onclick="paging(\'n\')">下一页</a></div>';
            $('.page').html(phtml+lihtml+nhtml);

        }
    },"json");
}

function paging(page){
  if(page=='p'&&default_page>1){
     default_page --;
  }
  if(page=='n'&&default_page<total_page){
     default_page ++;
  }
  if(page){
    if( !isNaN( page ) ){
      default_page = page;
    }
  }
  changeHash();
  // console.log(default_page);
  loadQuery();
}

function changeHash() {
    params.page = default_page
    var url_arr = [];
    var url;
    for (var k in params) {
        if(k!="tp" && (params[k]>0 || params[k].length>1)) {
            url_arr.push(k+'='+params[k])
        }
    }
    if(url_arr.length>0) {
        url = "#"+url_arr.join("&");
    } else {
        url = '';
    }
    location.href = url;
}

     window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"24"},"share":{},"selectShare":{"bdContainerClass":null,"bdSelectMiniList":["qzone","tsina","tqq","weixin"]}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

    $('body').css('background','rgba(255, 255, 255, 0)');

</script>
@endsection
