@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')
@section('title')错误@endsection
@section('css')
   <style type="text/css">
 .errorinfo a{
    font-size: 14px;
    margin: 0 10px;
 }
</style>
@endsection

@section('content')
    <div style="margin: 80px 0; text-align: center;" class="errorinfo">
        <p style="color:#fff;font-size: 16px; height: 80px;">您的登录账号信息与需要充值的账号信息不符合，只能为登录用户进行充值，是否重新登录</p>
        <p style="font-size: 14px; height: 80px;"><a href="{{url("logout")}}?referer=login?referer=charge">重新登入</a><a href="{{url("charge")}}?appid={{$appid}}&serverid={{$serverid}}">继续充值</a></p>
    </div>
@endsection
