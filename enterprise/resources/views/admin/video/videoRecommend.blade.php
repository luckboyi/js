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
    <!-- BEGIN PAGE -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

        <h1>
            视频管理 - 推荐管理
        </h1>
        <!--视频查询的区块-->
    </div>

    <!--视频修改的区块-->
    <!-- BEGIN PAGE -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <hr/>
        <!--视频分类添加的区块-->
        <div class="modal fade" id="accountAdd" tabindex="-1" role="dialog" aria-labelledby="accountAddLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-header widget blue widget-title"  style="color:blue">
                    <h4><i class="icon-reorder"></i>修改推荐位</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            {{--<div class="modal-content">--}}

            <!-- BEGIN PAGE CONTENT-->
                <div class="row-fluid">
                    <div class="span12">
                        <!-- BEGIN SAMPLE FORMPORTLET-->
                        <div class="widget-body">
                            <!-- BEGIN FORM-->
                            <form class="form-horizontal" action="{{ url('recommendAdd') }}" method="post" name="addForm" id="addForm" enctype="multipart/form-data">
                                <div class="control-group">
                                    <label class="control-label">视频ID</label>
                                    <div class="controls">
                                        <input type="text" id="contentId" name="videoId" class="form-control input-large" placeholder="视频的ID号" aria-describedby="title" maxlength="32">
                                        {{--<input type="text" placeholder=".input-mini" class="input-mini" />--}}
                                        <span class="help-inline"></span>
                                    </div>
                                </div>
                                {{--<div class="control-group">--}}
                                    {{--<label class="control-label">资源URL</label>--}}
                                    {{--<div class="controls">--}}
                                        {{--<input type="text" id="videoUrl" name="videoUrl" class="form-control input-large" placeholder="资源URL地址" class="form-control" aria-describedby="mark" maxlength="512">--}}
                                        {{--<span class="help-inline"></span>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

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
                                <input type="hidden" name="uniqId" id="uniqId" class="form-control" value=""/>
                                <input type="hidden" name="sortId" id="sortId" class="form-control" value=""/>
                                <div class="alert alert-danger hide errorBox" id="addErrorBox">操作失败</div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-lg btn-primary" id="sureBtn"><i class="icon-ok"></i>确定</button>
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


    @if(isset($data) && count($data) > 0)
        @foreach($data as $k=>$v)
            <div class="row-fluid" style="width: 90%; margin-left: 5%;">
                <hr>
                <h2>{{ $sort[$k]['sortName'] }}({{ count($data[$k]) }})</h2>
                <!--BEGIN METRO STATES-->
                @if(!empty($data[$k]))
                <div class="metro-nav metro-fix-view">
                    <div class="metro-nav-block nav-block-green long">
                            <div style="width: 100%; height: 85%;text-align: center;">
                                <img src="@if(isset($v[0]['videoInfo'][0]['videobiglogo'])){{ $v[0]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif" height="100%" title="@if(isset($v[0]['videoInfo'][0]['videoname'])){{ $v[0]['videoInfo'][0]['videoname'] }}@else无@endif">
                            </div>
                            <div style="margin-top: -8px; line-height: 12px;">
                                <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                    <span title="开始时间">{{ $v[0]['opening_time'] }}</span>
                                </div>
                                <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                    <span title="结束时间">{{ $v[0]['end_time'] }}</span>
                                </div>
                                <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[0]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[0]['content_id'] }}" data-openTm="{{ $v[0]['opening_time'] }}" data-endTm="{{ $v[0]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                            </div>
                    </div>
                    <div class="metro-nav-block nav-block-blue double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[1]['videoInfo'][0]['videobiglogo'])){{ $v[1]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif"  title="@if(isset($v[1]['videoInfo'][0]['videoname'])){{ $v[1]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[1]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[1]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[1]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[1]['content_id'] }}" data-openTm="{{ $v[1]['opening_time'] }}" data-endTm="{{ $v[1]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                            {{--<button type="submit" class="btn btn-lg btn-primary" style="margin-left:5%;" id="sureBtn">修改</button>--}}
                        </div>
                    </div>

                    <div class="metro-nav-block nav-block-red double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[2]['videoInfo'][0]['videobiglogo'])){{ $v[2]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif"  title="@if(isset($v[2]['videoInfo'][0]['videoname'])){{ $v[2]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[2]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[2]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[2]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[2]['content_id'] }}" data-openTm="{{ $v[2]['opening_time'] }}" data-endTm="{{ $v[2]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>

                    <div class="metro-nav-block nav-olive double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[3]['videoInfo'][0]['videobiglogo'])){{ $v[3]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif" title="@if(isset($v[3]['videoInfo'][0]['videoname'])){{ $v[3]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[3]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[3]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[3]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[3]['content_id'] }}" data-openTm="{{ $v[3]['opening_time'] }}" data-endTm="{{ $v[3]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>
                    <div class="metro-nav-block nav-block-purple double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[4]['videoInfo'][0]['videobiglogo'])){{ $v[4]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif"  title="@if(isset($v[4]['videoInfo'][0]['videoname'])){{ $v[4]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[4]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[4]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[4]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[4]['content_id'] }}" data-openTm="{{ $v[4]['opening_time'] }}" data-endTm="{{ $v[4]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>

                    <div class="metro-nav-block nav-deep-red double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[5]['videoInfo'][0]['videobiglogo'])){{ $v[5]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif"  title="@if(isset($v[5]['videoInfo'][0]['videoname'])){{ $v[5]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[5]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[5]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[5]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[5]['content_id'] }}" data-openTm="{{ $v[5]['opening_time'] }}" data-endTm="{{ $v[5]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>
                    <div class="metro-nav-block nav-deep-gray double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[6]['videoInfo'][0]['videobiglogo'])){{ $v[6]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif" title="@if(isset($v[6]['videoInfo'][0]['videoname'])){{ $v[6]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[6]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[6]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[6]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[6]['content_id'] }}" data-openTm="{{ $v[6]['opening_time'] }}" data-endTm="{{ $v[6]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>
                    <div class="metro-nav-block nav-light-purple double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[7]['videoInfo'][0]['videobiglogo'])){{ $v[7]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif"  title="@if(isset($v[7]['videoInfo'][0]['videoname'])){{ $v[7]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[7]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[7]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[7]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[7]['content_id'] }}" data-openTm="{{ $v[7]['opening_time'] }}" data-endTm="{{ $v[7]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>

                    <div class="metro-nav-block nav-light-brown double">
                        <div style="width: 100%; height: 85%;text-align: center;">
                            <img src="@if(isset($v[8]['videoInfo'][0]['videobiglogo'])){{ $v[8]['videoInfo'][0]['videobiglogo'] }}@else{{ url('/images/404') }}@endif"  title="@if(isset($v[8]['videoInfo'][0]['videoname'])){{ $v[8]['videoInfo'][0]['videoname'] }}@else无@endif">
                        </div>
                        <div style="margin-top: -8px; line-height: 12px;">
                            <div style="width: 30%;height: 15%;margin-left:5%;float: left;">
                                <span title="开始时间">{{ $v[8]['opening_time'] }}</span>
                            </div>
                            <div style="width: 30%;height: 15%; margin-left:5%;float: left;">
                                <span title="结束时间">{{ $v[8]['end_time'] }}</span>
                            </div>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v[8]['id'] }}" data-sortId="{{ $k }}"  data-contentId="{{ $v[8]['content_id'] }}" data-openTm="{{ $v[8]['opening_time'] }}" data-endTm="{{ $v[8]['end_time'] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>

                @else
                    <h3>暂无数据!</h3>
                @endif
            </div>
        @endforeach
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


        // 详情页日期选择
        $("#selType").change(function(){
            var selDate = $(this).val();
            if (selDate == "date") {
                $("#dateSelBox").addClass("collapsed").removeClass("collapse");
            } else {
                $("#dateSelBox").addClass("collapse").removeClass("collapsed");
            }
        });


        //查询搜索信息
        $("body").on("click", "#itemSelBtn", function() {
//            var uniqId = $(this).attr("data-uniqId");
//            var userId = $(this).attr("data-userId");
            var searchword = $("#searchword").val();

            var url = "videoSearch/" + searchword;
            if(searchword == '' || searchword == '请输入视频名/或视频id号') {
                alert("请输入有效的电影名/电影id号");
            } else {
                pubApi.jumpUrl(url);
            }

            var paramObj = {
                "action" : "search",
                "searchword" : searchword,
                //"userid" : userId,
            };
            //var ajaxUrl = "video/videoSearch/";

            //location.href = url;
//            pubApi.ajaxPost(ajaxUrl, paramObj, function(result) {
//                if(result.code == 0){
//                    pubApi.reload();
//                } else {
//                    confirm("删除失败");
//                }
//            }, function() {
//                pubApi.showError();
//            });
        });

        //编辑推荐位输入框
        $("body").on("click", "a.accountEditBtn", function() {
            var uniqId = $(this).attr("data-uniqId"),
                    contentId = $(this).attr("data-contentId"),
                    openTm = $(this).attr("data-openTm"),
                    sortId = $(this).attr("data-sortId"),
                    endTm = $(this).attr("data-endTm");
            $("#uniqId").val(uniqId);
            $("#tmBegin").val(openTm);
            $("#tmEnd").val(endTm);
            $("#sortId").val(sortId);
            $("#contentId").val(contentId);
            $('#accountAdd').modal({backdrop: 'static', keyboard: false, show : true});
            return false;
        });

        //添加广告位信息
        $("body").on("click", "#sureBtn", function() {
            var uniqId = $("#uniqId").val(),
                    videoId = $("#contentId").val(),
                    tmBegin = $("#tmBegin").val(),
                    sortId = $("#sortId").val(),
                    tmEnd = $("#tmEnd").val();

            if ($.trim(videoId) == "" || $.trim(uniqId) == "" || $.trim(tmBegin) == "" || $.trim(tmBegin) == "" ){
                pubApi.showDomError($("#addErrorBox"), "非法参数");
                return false;
            }
//            addForm.submit();
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
                        //$("body").append("&nbsp;&nbsp;<img src='"+result.data.url+"' alt='' width='100' /><input type='hidden' name='image[]' value='"+result.data.url+"'  />");
                    }
                },
                error:function(result){
                    alert(result.message);
                }
            };
            $("#addForm").ajaxForm(options).submit(function(){return false;});
        });

    </script>
@endsection
