@extends('layouts.admin')

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
        账号解封
    </li>
</ul>

@if (session('status'))
<div class="alert alert-block alert-success fade in">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <h4 class="alert-heading">{{ session('status') }}</h4>
</div>
@endif

@if (session('error'))
<div class="alert alert-block alert-error fade in">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <h4 class="alert-heading">{{ session('error') }}</h4>
</div>
@endif

<div class="widget green">
    <div class="widget-title">
        <h4><i class="icon-reorder"></i> 账号解封</h4>
    </div>
    <div class="widget-body">
        <form action="" class="form-horizontal" method="post">
            {{ csrf_field() }}
            <div class="control-group">
                <label class="control-label">UID：</label>
                <div class="controls">
                    <input type="text" name="uids" class="medium" value="{{Input::old('uids')}}" placeholder="多账号用;间隔">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 确认</button>
            </div>
        </form>
    </div>
</div>

@endsection
