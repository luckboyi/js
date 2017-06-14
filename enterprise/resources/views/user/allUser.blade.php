@extends('layouts.admin')

@section('content')
<div class="row-fluid">
  <div class="span12">
    <h3 class="page-title">
    权限分配
    </h3>
    <ul class="breadcrumb">
      <li>
        <a href="#">首页</a>
        <span class="divider">/</span>
      </li>
      <li class="active">
        权限分配
      </li>
    </ul>
    <!-- END PAGE TITLE & BREADCRUMB-->
  </div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->

<div id="page-wraper">
  <div class="row-fluid">
    <div class="span12">
      <!-- BEGIN BASIC PORTLET-->
      <div class="widget orange">
        <div class="widget-title">
          <h4><i class="icon-reorder"></i>用户组</h4>
        </div>
        <div class="widget-body">
          <table class="table table-striped table-bordered table-advance table-hover">
            <tbody>
              <!-- 如果超过5个元素就添加一行 -->
              <tr>
                @foreach($allUser as $key=>$value)
                <td><a href="/ajax/showPerm?userId={{ $value['id'] }}" id="{{ $value['id'] }}">{{ $value['name']}}</a></td>
                @endforeach
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- END BASIC PORTLET-->
    </div>
  </div>
</div>
@endsection
