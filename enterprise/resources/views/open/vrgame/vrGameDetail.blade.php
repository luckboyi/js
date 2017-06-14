@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')
@section('content')
 <!--内容-->
    <div class="container">
        <div class="in_con">
            <h4 class="f14">产品详情</h4>
            <div class="content product-detail">
                <p>
                    <span class="product-title">{{ $detail["name"] }}</span>
                     <span class="product-id">APP ID:{{ $detail["appid"] }}</span>
                     {!! $blade->showStat($detail["stat"],"style") !!}
                      @if($detail["stat"]==3) {{ $detail["msg"] }} @endif
                </p>
                <p>
                    <span>创建时间：{{ $detail["ctime"] }}</span>
                    <span>上线时间：{{ $blade->showDateTime($detail["send_time"]) }}</span>
                </p>
                <p>
                     <span class="product-id">APP KEY:{{ $detail["appkey"] }}</span>
                    <span class="product-id">PAY KEY:{{ $detail["paykey"] }}</span>
                </p>
                <div class="product-manage clearfix" data-id="{{ $detail["appid"] }}">
                    <dl class="btn-vrgame-info" data-src="base">
                        <dt>
                            <span class="product-manage-icon icon-info"></span>
                            <span class="product-manage-title">基本信息</span>
                        </dt>
                        <dd>产品基础信息，包括名称分类等，上线后无法修改</dd>
                    </dl>
                    <dl class="btn-vrgame-info" data-src="res">
                        <dt>
                            <span class="product-manage-icon icon-source"></span>
                            <span class="product-manage-title">图标素材</span>
                        </dt>
                        <dd>平台上显示的图标，可在上线后进行修改</dd>
                    </dl>
                    <dl class="btn-vrgame-info" data-src="version">
                        <dt>
                            <span class="product-manage-icon icon-server"></span>
                            <span class="product-manage-title">版本控制</span>
                        </dt>
                        <dd>上传最新的客户端版本</dd>
                    </dl>
                    <dl class="btn-vrgame-info"  data-src="copyright">
                        <dt>
                            <span class="product-manage-icon icon-copyright"></span>
                            <span class="product-manage-title">电子合同</span>
                        </dt>
                        <dd>电子合同以及版权文件</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="button-con">
            <button type="button" class="btn @if($detail["stat"]!=1)  cur @endif  btn-review" @if($detail["stat"]==1)  disabled="true" @endif>@if($detail["stat"]==1) 正在审核 @else 提交审核 @endif</button>
            @if($detail['send_time']==0)<button type="button" class="btn btn-online @if($detail["stat"]==5)  cur @endif"  @if($detail["stat"]!=5) disabled="true"  @endif >发布上线</button> @endif

        </div>
    </div>
    <script>
    var appid = {{ $detail["appid"] }};
    $(function(){
        $(".btn-review").click(function(){
           $.post(Open.urls.webGameReview+appid,{},function(data){
                if(data.code==0) {
                    location.reload();
                } else {
                     Open.showMessage(data.msg);
                }
            },"json")
        });
        $(".btn-online").click(function(){
           $.post(Open.urls.webGameOnline+appid,{},function(data){
                if(data.code==0) {
                     Open.showMessage("发布成功，请到线上查看",1500,function(){
                        location.reload();
                     });
                } else {
                     Open.showMessage(data.msg);
                }
            },"json")
        });
    })
    </script>
@endsection
