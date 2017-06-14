<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 17:52
 */
?>
@extends('layouts.admin')

{{--@include('common.errors')--}}
@section('content')
    <?php
extract($_REQUEST);
$selType = isset($selType) ? $selType : "";
$tmBegin = isset($tmBegin) ? $tmBegin : "";
$tmEnd   = isset($tmEnd) ? $tmEnd : "";
?>
    @if(!isset($searchword) || $searchword == '')
        {{ $searchword = "" }}
    @endif
    <!-- BEGIN PAGE -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

        <h1>
            页游管理 - 广告位管理
            <a class="btn btn-primary pull-right accountAddBtn" href="javascript:void(0);">
                添加广告位 - [&nbsp;<span class="glyphicon glyphicon-plus icon-plus-sign-alt pointer"></span>&nbsp;]
            </a>
        </h1>
        <hr/>
        <!--视频分类添加的区块-->
        <div class="modal fade" id="accountAdd" tabindex="-1" role="dialog" aria-labelledby="accountAddLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-header widget blue widget-title"  style="color:blue">
                        <h4><i class="icon-reorder"></i>添加广告位</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{--<div class="modal-content">--}}

                    <!-- BEGIN PAGE CONTENT-->
                    <div class="row-fluid">
                        <div class="span12">
                            <!-- BEGIN SAMPLE FORMPORTLET-->
                                <div class="widget-body">
                                    <!-- BEGIN FORM-->
                                    <form class="form-horizontal" action="{{ url('uploadWebgameAd') }}" method="post" name="addForm" id="addForm" enctype="multipart/form-data">
                                        <div class="control-group">
                                            <label class="control-label">页游ID</label>
                                            <div class="controls">
                                                <input type="text" id="videoId" name="videoId" class="form-control input-large" placeholder="页游的ID号" aria-describedby="title" maxlength="32">
                                                {{--<input type="text" placeholder=".input-mini" class="input-mini" />--}}
                                                <span class="help-inline"></span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">资源URL</label>
                                            <div class="controls">
                                                <input type="text" id="videoUrl" name="videoUrl" class="form-control input-large" placeholder="资源URL地址" class="form-control" aria-describedby="mark" maxlength="512">
                                                <span class="help-inline"></span>
                                            </div>
                                        </div>
                                        {{--<div class="control-group">--}}
                                            {{--<label class="control-label">Meduam Input</label>--}}
                                            {{--<div class="controls">--}}
                                                {{--<input type="text" placeholder=".input-medium" class="input-medium" />--}}
                                                {{--<span class="help-inline">Some hint here</span>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <div class="control-group">
                                            <label class="control-label">上传广告图</label>
                                            <div class="controls">
                                                <input type="file" name="file" id="typelogo" class="form-control input-large" aria-describedby="typelogo" maxlength="32" class="form-control"/>
                                                <span class="help-inline"></span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">开始时间</label>
                                            <div class="controls">
                                                <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                                                    <input class="form-control" type="text" name="tmBegin" value="" placeholder="开始时间" id="tmBegin">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">结束时间</label>
                                            <div class="controls">
                                                <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                                                    <input class="form-control" type="text" name="tmEnd" value="" placeholder="结束时间" id="tmEnd">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
                                                </span>
                                            </div>
                                        </div>
                                        <br/>
                                        <input type="hidden" name="userid" id="userid" class="form-control" value="admin"/>
                                        <input type="hidden" name="vtid" id="vtid" class="form-control" value="{{ $vtid }}"/>
                                        <div class="alert alert-danger hide errorBox" id="addErrorBox">操作失败</div>
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-lg btn-primary" id="sureBtn"><i class="icon-ok"></i>确定</button>
                                            {{--<button type="submit" class="btn blue"><i class="icon-ok"></i> Save</button>--}}
                                            {{--<button type="button" class="btn"><i class=" icon-remove"></i> Cancel</button>--}}
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>
                            <!-- END SAMPLE FORM PORTLET-->
                        </div>
                </div>
            {{--</div>--}}
        </div>
    </div>

    <div class="alert alert-danger hide errorBox">操作失败</div>
    <div class="alert alert-success hide msgBox">操作成功</div>

    <ul class="nav nav-pills" role="tablist" id="sortList">
        @if(isset($sort) && count($sort)>0)
            @foreach($sort as $s)
                <li role="presentation" id="{{$s['vtid']}}"><a href="/webgameAd/{{$s['vtid']}}">{{$s['typename']}}</a></li>
            @endforeach
        @endif
    </ul>

    @if(isset($data) && count($data) > 0)

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
            <tr class="trHead">
                <th class="col-xs-1">{{ count($data) }}（页游编号）</th>
                <th class="col-xs-1">链接的URL</th>
                <th class="col-xs-1">上传</th>
                <th class="col-xs-1">展示图片</th>
                <th class="col-xs-1">开始时间</th>
                <th class="col-xs-1">结束时间</th>
                <th class="col-xs-1">创建者</th>
                <th class="col-xs-1">上传时间</th>
                <th class="col-xs-1 ">操作</th>
            </tr>
            @foreach ($data as $v)
                <tr>
                    <form class="form-inline" role="form" action="{{ url('uploadWebgame') }}" method="post" name="addForm" id="addForm{{ $v['content_id'] }}" enctype="multipart/form-data">
                    <td>
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputEmail2"></label>
                            <input type="text" class="form-control" id="VideoId{{ $v["content_id"] }}" placeholder="请填写" value="{{ $v["content_id"] }}">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label class="sr-only" for="exampleInputEmail2"></label>
                            <input type="text" class="form-control" name="videoName" id="videoName{{ $v["content_id"] }}" placeholder="请填写" value="{{ $v["content_url"] }}">
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-md">
                            <label class="sr-only" for="exampleInputEmail2"></label>
                            <input type="file" class="form-control" class="form-control" name="bannerUp" id="bannerUp{{ $v["content_id"] }}" aria-describedby="typelogo"/>
                        </div>
                    </td>
                        <?php
                            $url = url('404');
                            if ($v['content_id'] == 0 || $v['content_id'] == '') {
                                $url = $v['resource'];
                            }
                        ?>
                    <td onclick='location.href = "{{ $url }}"'>
                        <div class="form-group" >
                           <img src="{{ $url }}"  width="50" height="30">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            {{--<label for="exampleInputName2">开始时间&nbsp;:</label>--}}
                            <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                                <input class="form-control" type="text" value="{{ $v['opening_time'] }}" placeholder="开始时间" id="tmBegin{{ $v["content_id"] }}">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            {{--<label for="exampleInputEmail2">结束时间&nbsp;:</label>--}}
                            <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                                <input class="form-control" type="text" value="{{ $v['end_time'] }}" placeholder="结束时间" id="tmEnd{{ $v["content_id"] }}">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
                            </span>
                        </div>
                    </td>
                    @if($v['create_userid'] !== 0)
                        <td>{{ $v['create_userid'] }}</td>
                    @else
                        <td>admin</td>
                    @endif
                    <td>{{ $v["create_at"]  }}</td>

                    <td>
                        <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v["id"] }}" /><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        &nbsp;&nbsp;<a href="javascript:void(0);" class="sortDelBtn" data-uniqId="{{ $v["id"] }}"  data-userId="{{ $v['create_userid'] }}"><span class="glyphicon icon-trash" aria-hidden="true"></span></a>
                        {{--<button type="submit" class="btn btn-default" id="sureBtn{{ $v['vid'] }}">删除</button>--}}
                    </td>
                    </form>
                </tr>
            @endforeach
        </table>
    </div>
    @else
        <h3>暂无数据!</h3>
    @endif
    <!-- END PAGE -->
        {{--{!! $data->render() !!}--}}
@endsection

@section('javascript')
    <script type="text/javascript">
        //日期选择初始化
        $(".timePicker").datetimepicker({
            language:  'zh-CN',
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            minView: 2,
            forceParse: 0,
            format: 'yyyy-mm-dd hh:ii:ss'
        });


        $("#sortList li").click(function() {
            $(this).siblings('li').removeClass('active');  // 删除其他兄弟元素的样式
            $(this).addClass('active');                            // 添加当前元素的样式
        });

        //添加banner按钮的active属性
        var str = window.location.pathname;
        var index = str .lastIndexOf("\/");
        str  = str.substring(index, str . length);
        var liId = '#' + str.replace(/\//g,'');

        if(str.replace(/\//g,'') !== 'videoAd') {
            $(liId).addClass('active');
        } else {
            $("#sortList li:nth-child(1)").attr("class","active");
        }



        // 详情页日期选择
        $("#selType").change(function(){
            var selDate = $(this).val();
            if (selDate == "date") {
                $("#dateSelBox").addClass("collapsed").removeClass("collapse");
            } else {
                $("#dateSelBox").addClass("collapse").removeClass("collapsed");
            }
        });

        //添加视频类别
        $("body").on("click", "a.accountAddBtn", function() {
            $("#uniqId").val("");
            $("#title").val("");
            $("#mark").val("").attr("readonly", false);
            $('#accountAdd').modal({backdrop: 'static', keyboard: false, show : true});
            return false;
        });

        //添加广告位信息
        $("body").on("click", "#sureBtn", function() {
            var uniqId = $("#uniqId").val(),
                    videoId = $("#videoId").val(),
                    videoUrl = $("#videoUrl").val();
            if($.trim(videoId) == "") {
                videoId = 0;
            }
            if ( $.trim(videoUrl) == ""){
                pubApi.showDomError($("#addErrorBox"), "非法参数");
                return false;
            }
//            addForm.submit();
            $("#addForm").submit(function(){
                if($("input[type=file]").val()==""){
                    alert("请选择要上传的图片！！");
                    return false;
                }
            });
            var options = {
                type:"POST",
                dataType:"json",
                resetForm:true,
                success:function(result){
                    if(result.code !== 0){
                        alert(result.msg);
                    }else{
                        pubApi.reload();
                        //alert('提交成功');
                    }
                },
                error:function(result){
                    alert(result.message);
                }
            };
            $("#addForm").ajaxForm(options).submit(function(){return false;});
        });

        $("body").on("click", "a.sortDelBtn", function() {
            var uniqId = $(this).attr("data-uniqId");
            //var userId = $(this).attr("data-userId");
            if (confirm("确认删除??")) {
                var paramObj = {
                    "action" : "Del",
                    "id" : uniqId,
                    //"userid" : userId,
                };
                var ajaxUrl = "{{ url('webgameAdDel') }}";
                pubApi.ajaxPost(ajaxUrl, paramObj, function(result) {
                    if(result.code == 0){
                        pubApi.reload();
                    } else {
                        confirm("删除失败");
                    }
                }, function() {
                    pubApi.showError();
                });
            }
        });
    </script>
@endsection
