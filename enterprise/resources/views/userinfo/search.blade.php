@extends('layouts.admin')

@section('content')
<h3 class="page-title">
用户查询
</h3>
<ul class="breadcrumb">
    <li>
        <a href="#">首页</a>
        <span class="divider">/</span>
    </li>
    <li class="active">
        用户查询
    </li>
</ul>

<div class="row-fluid">
    <div class="span3">
        <div class="widget green">
            <div class="widget-title">
                <h4><i class="icon-search"></i> 条件搜索</h4>
            </div>
            <div class="widget-body clearfix siderForm">
                <form action="" method="post">
                    {{ csrf_field() }}
                    <div class="control-group">
                        <label class="control-label">类型：</label>
                        <div class="controls">
                            <select class="input-large m-wrap" name="type" tabindex="1">
                                <option {{(Input::old('type')==1)?"selected":""}} value="1">用户账号</option>
                                <option {{(Input::old('type')==2)?"selected":""}} value="2">UID</option>
                                <option {{(Input::old('type')==3)?"selected":""}} value="3">手机号</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">内容：</label>
                        <div class="controls">
                            <input type="text" name="value" class="medium" value="{{Input::old('value')}}">
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" style="float: right">搜索</button>
                </form>
            </div>
        </div>
    </div>
    <div class="span9">
        <div class="widget yellow">
            <div class="widget-title">
                <h4><i class="icon-info-sign"></i> 用户信息</h4>
            </div>
            <div class="widget-body">
                @if (session('error'))
                <div class="alert alert-block alert-error fade in">
                    <h4 class="alert-heading">{{ session('error') }}</h4>
                </div>
                @else
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>UID</td>
                            <td>
                                {{ isset($userinfo['uid'])?$userinfo['uid']:"-" }}
                            </td>
                            <td>账号名称</td>
                            <td>
                                {{ isset($userinfo['account'])?$userinfo['account']:"-" }}
                            </td>
                        </tr>
                        <tr>
                            <td>昵称</td>
                            <td>
                                {{ isset($userinfo['nick'])?$userinfo['nick']:"-" }}
                            </td>
                            <td>手机号</td>
                            <td>
                                {{ isset($userinfo['bindmobile'])?$userinfo['bindmobile']:"-" }}
                            </td>
                        </tr>
                        <tr>
                            <td>充值总额</td>
                            <td>
                                {{ isset($userinfo['consume'])?$userinfo['consume']:"-" }}
                            </td>
                            <td>账号余额</td>
                            <td>
                                {{ isset($userinfo['money'])?$userinfo['money']:"-" }}
                            </td>
                        </tr>
                        <tr>
                            <td>账号状态</td>
                            <td>
                                @if (isset($userinfo['status']))
                                {{$userinfo['status']?"锁定":"正常"}}
                                @else
                                -
                                @endif
                            </td>
                            <td>上次登陆时间</td>
                            <td>
                                {{ isset($userinfo['lastTime'])?$userinfo['lastTime']:"-" }}
                            </td>
                        </tr>
                        <tr>
                            <td>VR游戏数</td>
                            <td>{{ $gamecount?$gamecount:"-" }}</td>
                            <td>页游游戏数量</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>观看总时长</td>
                            <td>{{ $videototaltime?$videototaltime:"-" }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
