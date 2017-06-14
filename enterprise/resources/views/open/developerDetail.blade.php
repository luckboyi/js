@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')
@section('head')
<script src="{{static_res('/open/assets/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{static_res('/open/assets/jquery-validation/additional-methods.min.js')}}"></script>
<script src="{{static_res('/open/assets/jquery-validation/messages_zh.js')}}"></script>
@endsection
@section('content')
    <div class="container">
        <div class="in_con">
            <h4 class="f14">平台账号</h4>
            <div class="content">
                <p class="clearfix">
                    <span class="title fl">产品账号：</span>
                    <span>{{ $data['uid'] }}</span>
                </p>
                <p class="clearfix">
                    <span class="title fl">账号类型：</span>
                    <span>@if($type==1) 公司账号 @else 个人账号 @endif</span>
                </p>
            </div>
        </div>
        <div class="in_con">
            <h4 class="f14">@if($type==1) 公司信息 @else 个人信息 @endif</h4>
            <div class="content">
                @if($type==1)
                <p class="clearfix">
                    <span class="title fl">公司名称：</span>
                    <input type="text" value="{{ $data['name'] }}" readonly>
                </p>
                <p class=" clearfix">
                    <span class="title fl">营业执照注册号：</span>
                    <input type="text" value="{{ $data['idcard'] }}" readonly>
                </p>
                <p class="clearfix">
                    <span class="title fl">联系人：</span>
                    <input type="text" value="{{ $data['contacts'] }}" readonly>
                </p>
                @else
                <p class="clearfix">
                    <span class="title fl">姓名：</span>
                    <input type="text" value="{{ $data['name'] }}" readonly>
                </p>
                <p class=" clearfix">
                    <span class="title fl">身份证：</span>
                    <input type="text" value="{{ $data['idcard'] }}" readonly>
                </p>
                @endif
                <p class="clearfix">
                    <span class="title fl">电子邮箱：</span>
                    <input type="text" value="{{ $data['email'] }}" readonly>
                </p>
                <p class="clearfix">
                    <span class="title fl">联系地址：</span>
                    <input type="text" value="{{ $data['address'] }}" readonly>
                </p>
                <p class="clearfix ">
                    <span class="title fl">@if($type==1) 营业执照照片 @else 手持身份证照片 @endif：</span>
                    <span class="img_box pr">
                        <input type="file"  name="file" class="pa">
                        <img src="{{ $data['url']['credentials'] }}" >
                    </span>
                </p>
            </div>
        </div>
    </div>
@endsection