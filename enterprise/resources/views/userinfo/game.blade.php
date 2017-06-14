@extends('layouts.admin')
@inject('blade', 'App\Helper\BladeHelper')
@section('content')
<h3 class="page-title">
游戏查询
</h3>
<ul class="breadcrumb">
    <li>
        <a href="#">首页</a>
        <span class="divider">/</span>
    </li>
    <li class="active">
        游戏查询
    </li>
</ul>

<div class="row-fluid">
    <div class="span3">
        <div class="widget green">
            <div class="widget-title">
                <h4><i class="icon-search"></i> 条件搜索</h4>
            </div>
            <div class="widget-body clearfix siderForm ">
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
                <h4><i class="icon-info-sign"></i> 游戏信息</h4>
            </div>
            <div class="widget-body">
                @if (session('error'))
                <div class="alert alert-block alert-error fade in">
                    <h4 class="alert-heading">{{ session('error') }}</h4>
                </div>
                @elseif (!isset($gameArr))
                <div class="alert alert-block alert-info fade in">
                    <h4 class="alert-heading">请搜索用户</h4>
                </div>
                @elseif (count($gameArr)>0)
                <div class="row-fluid">
                    <div class="span12">
                        <!--BEGIN TABS-->
                        <div class="tabbable custom-tab">
                            <div class="tab-content">
                                <div id="tab_1_1">
                                    @foreach ($gameArr as $game)
                                    <div class="row-fluid" style="margin:20px; ">
                                        <div class="span4">
                                            <img src="{{ $game->gicon }}" alt="" style="max-height:200px; max-width:100%">
                                        </div>
                                        <div class="span8">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td>游戏名称</td>
                                                        <td>{{ $game->gname }}</td>
                                                        <td>购买时间</td>
                                                        @if (isset($game->buytime))
                                                        <td>{{ date("Y-m-d H:i:s",$game->buytime)}}</td>
                                                        @else
                                                        <td>-</td>
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <td>游戏时长：</td>
                                                        @if (isset($game->totaltime))
                                                        <td>{{ $blade->time2second($game->totaltime)}}</td>
                                                        @else
                                                        <td>-</td>
                                                        @endif
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                    <tr>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!--END TABS-->
                    </div>
                </div>
                @else
                <div class="alert alert-block alert-warning fade in">
                    <h4 class="alert-heading">该用户没有游戏</h4>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
