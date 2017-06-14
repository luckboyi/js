@extends('open.nav')

@section('head')
<link rel="stylesheet" href="{{static_res('/open/assets/art-dialog/css/ui-dialog.css')}}" />
<script src="{{static_res('/open/assets/art-dialog/js/dialog-min.js')}}"></script>
<script src="{{static_res('/open/js/common.js')}}"></script>
@endsection


@section('content')
<!--内容-->
<div class="container">
    <div class="in_con">
        <h4 class="f14">产品详情</h4>
        <div class="content product-detail pr">
            <p>
                <span class="product-title">{{$webgame['name']}}</span>
                <span class="product-id">产品ID：{{$webgame['appid']}}</span>
                <span class="product-status success"><i></i>{{$webgame['status_dec']}}</span>
            </p>
            <p>
                <span>创建时间：{{ $webgame['ctime'] }}</span>
                @if ($webgame['push_time'] > 0)
                <span>上线时间：{{ date("Y-m-d H:i:s", $webgame['push_time']) }}</span>
<!--                     <span class="product-status fail"><i></i>已上线</span> -->
                @else
                <span class="product-status fail"><i></i>未上线</span>
                @endif
            </p>
            @if(isset($webgame['stat']) && $webgame['stat'] == 1)
            <p class="pa sub_btn">
                <span class="pass" onclick="javascript: goPass();">通过</span>
                <span class="reject" onclick="javascript: goReject();">驳回</span>
            </p>
            @endif
        </div>
    </div>
    <div class="in_con">
        <h4 class="f14 clearfix pr">
            <span class="">产品详情</span>
            <span class=" pa nav">
                <i class="cur">产品详情</i>
                <i><a href='{{url("/review/$tag/info/right/$appid")}}'>软件著作权</a></i>
                <i><a href='{{url("/review/$tag/info/agreement/$appid")}}'>电子合同</a></i>
            </span>
        </h4>
        <div class="content product-detail product-detail-info">
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">产品名称：</span>
                    <span>{{$webgame['name']}}</span>
                </p>
               <p class="clearfix">
                   <span class="title fl">主体名称：</span>
                   <span>{{ $webgame['company'] }}</span>
               </p>
                <p class="clearfix">
                    <span class="title fl">产品类型：</span>
                    <span>{{ $webgame['category'] }}</span>
                </p>
                <p class="clearfix">
                    <span class="title fl">产品简介：</span>
                    <span>{{$webgame['content']}}</span>
                </p>
            </div>
             <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏LOGO：</span>
                    <span>在热门游戏以及游戏列表显示</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl pr">
                        <span class="pic-icon"></span>
                        @if(isset($webgame["logo"]))
                        <a href="{{ static_image($webgame["logo"]) }}" target="_blank"><img src="{{ static_image($webgame['logo']) }}" class="pa" height="auto" width="100%"></a>
                        @endif
                    </div>
                </div>
            </div>
             <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏卡片：</span>
                    <span>在热门游戏以及游戏列表显示</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl pr">
                        <span class="pic-icon"></span>
                        @if(isset($webgame["card"]))
                        <a href="{{ static_image($webgame["card"]) }}" target="_blank"><img src="{{ static_image($webgame['card']) }}" class="pa" height="auto" width="100%"></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏ICON：</span>
                    <span>在客户端显示的128*128的ICON</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl">
                        <span class="pic-icon"></span>
                        @if(isset($webgame["icon"]))
                        <a href="{{ static_image($webgame["icon"]) }}" target="_blank"><img src="{{ static_image($webgame['icon']) }}" class="pa" height="auto" width="100%"></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏排行：</span>
                    <span>在客户端显示的238*60的ICON</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl">
                        <span class="pic-icon"></span>
                        @if(isset($webgame["rank"]))
                        <a href="{{ static_image($webgame["rank"]) }}" target="_blank"><img src="{{ static_image($webgame['rank']) }}" class="pa" height="auto" width="100%"></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏背景：</span>
                    <span>游戏背景</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl">
                        <span class="pic-icon"></span>
                        @if(isset($webgame["bg"]))
                        <a href="{{ static_image($webgame["bg"]) }}" target="_blank"><img src="{{ static_image($webgame['bg']) }}" class="pa" height="auto" width="100%"></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏背景：</span>
                    <span>游戏背景</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl">
                        <span class="pic-icon"></span>
                        @if(isset($webgame["bg2"]))
                        <a href="{{ static_image($webgame["bg2"]) }}" target="_blank"><img src="{{ static_image($webgame['bg2']) }}" class="pa" height="auto" width="100%"></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">截图轮播图：</span>
                    <span>在游戏中轮播的截图</span>
                </p>
                <div class="pic-con clearfix ">
                    @if(isset($webgame["slider"]) and $webgame["slider"])
                        @foreach($webgame["slider"] as $v)
                        <div class='pic fl'><a href="{{ static_image($v) }}" target="_blank"><img src="{{ static_image($v) }}" class="pa" height="auto" width="100%" /></a></div>
                        @endforeach
                    @endif
                </div>
            </div>
             <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">宣传轮播图：</span>
                    <span>宣传轮播图</span>
                </p>
                <div class="pic-con clearfix ">
                    @if(isset($webgame["screenshots"]) and $webgame["screenshots"])
                        @foreach($webgame["screenshots"] as $v)
                        <div class='pic fl'><a href="{{ static_image($v) }}" target="_blank"><img src="{{ static_image($v) }}" class="pa" height="auto" width="100%" /></a></div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
<script>
var appid = {{ $appid }};
function goPass() {
    myDialog.alert("确定通过审核吗",function() {
        callPass("pass","");
    });
}

function goReject() {
    myDialog.dialog({
        id: "edit",
        title: "请填写驳回原因",
        content: '<textarea class="reject-text" style="width:350px;height:150px" placeholder="请填写驳回原因"></textarea>',
        cancelDisplay: false,
        ok: function() {
            var msg = $(".reject-text").val();
            if(msg.length<4) {
                Open.showMessage("请填写驳回原因");
            } else {
                callPass("deny",msg);
            }
            return true;
        },
        okValue: '驳回',
        cancel:function() {

        }
    });
}

function callPass(action,msg) {
    $.post(Open.urls.reviewWebGameSubmit+appid,{ action: action, msg: msg },function(data){
        if (data.code == 0)
        {
            Open.showMessage(data.msg);
        } else {
            Open.showMessage(data.msg);
        }
        setTimeout(function(){
           Open.navigation.reviewProduct("{{$tag}}");
        }, 3000);
    },"json");
}
</script>
@endsection