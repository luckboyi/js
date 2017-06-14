<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/10/13
 * Time: 17:52
 */
?>
@extends('layouts.admin')

{{--@include('common.errors')--}}
@section('content')
    <!-- BEGIN PAGE -->
    <div class="col-sm-12 col-sm-offset-3 col-md-10 col-md-offset-2 main">


    </div>

    <!--视频修改的区块-->
    <!-- BEGIN PAGE -->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h2>
            推荐菜单和视频分类
        </h2>
        <a class="btn btn-primary pull-right sortAddBtn" href="javascript:void(0);" style="margin-right: 5%;">
            添加推荐分类菜单类别 - [&nbsp;<span class="glyphicon glyphicon-plus icon-plus-sign-alt pointer"></span>&nbsp;]
        </a>
        <!--推荐位添加和修改的区块-->
        <div class="modal fade" id="accountAdd" tabindex="-1" role="dialog" aria-labelledby="accountAddLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-header widget blue widget-title"  style="color:blue">
                    <h4><i class="icon-reorder"></i>修改推荐位</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            {{--<div class="modal-content">--}}

            <!-- BEGIN PAGE CONTENT-->
                <div class="row-fluid form-windows">
                    <div class="span12">
                        <!-- BEGIN SAMPLE FORMPORTLET-->
                        <div class="widget-body">
                            <!-- BEGIN FORM-->
                            <form class="form-horizontal" action="{{ url('recommend/sortAdd') }}" method="post" name="addForm" id="addForm" enctype="multipart/form-data">
                                <div class="control-group">
                                    <label class="control-label">推荐位描述（填写英文字符）</label>
                                    <div class="controls">
                                        <input type="text" id="sortCode" name="code" class="form-control input-large" placeholder="推荐位描述，例：well-chosen-video" aria-describedby="title" maxlength="64">
                                        <span class="help-inline"></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">分类或推荐位描述(简单描述)</label>
                                    <div class="controls">
                                        <textarea name="desc" id="sortDesc" placeholder='推荐位描述，例：精选视频'></textarea>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">推荐位置描述(详细描述描述)</label>
                                    <div class="controls">
                                        <textarea name="detail" id="detailDesc" placeholder='推荐位描述，例：精选视频，是我们从所有视频中挑选的精彩视频'></textarea>
                                    </div>
                                </div>
                                <br/>
                                <input type="hidden" name="userid" id="sortuserid" class="form-control" value="admin"/>
                                <input type="hidden" name="banner_posid" id="banner_posid" class="form-control" value=""/>
                                <input type="hidden" name="posid" id="sortposid" class="form-control" value=""/>
                                <div class="alert alert-danger hide errorBox" id="addErrorBox">操作失败</div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-lg btn-primary" id="sortSureBtn"><i class="icon-ok"></i>确定</button>
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
        <!--推荐位分类添加的区块-->
        <div class="modal fade" id="sortAdd" tabindex="-1" role="dialog" aria-labelledby="sortAddLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-header widget blue widget-title"  style="color:blue">
                    <h4><i class="icon-reorder"></i>修改推荐位</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            {{--<div class="modal-content">--}}

            <!-- BEGIN PAGE CONTENT-->
                <div class="row-fluid sort-form-windows">
                    <div class="span12">
                        <!-- BEGIN SAMPLE FORMPORTLET-->
                        <div class="widget-body">
                            <!-- BEGIN FORM-->
                            <form class="form-horizontal" action="{{ url('recommend/sortAdd') }}" method="post" name="sortAddForm" id="sortAddForm" enctype="multipart/form-data">
                                <div class="control-group">
                                    <label class="control-label">推荐位描述（填写英文字符）</label>
                                    <div class="controls">
                                        <input type="text" id="sortCode" name="code" class="form-control input-large" placeholder="推荐位描述，例：well-chosen-video" aria-describedby="title" maxlength="64">
                                        <span class="help-inline"></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">推荐位或分类标识</label>
                                    <div class="controls">
                                        <select class="form-control form-group w300" name="sortmark" id="sortMark">
                                            <option value="0">菜单分类</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="sortTypeNav" class="control-group">
                                    <label class="control-label">菜单类别描述</label>
                                    <div class="controls">
                                        <input type="text" id="sortType" name="type" class="form-control input-large" placeholder="菜单类别描述，例：video,..." aria-describedby="title" maxlength="64">
                                    </div>
                                </div>

                                {{--<div class="control-group">--}}
                                    {{--<label class="control-label">上传展示图</label>--}}
                                    {{--<div class="controls">--}}
                                        {{--<input type="file" name="file" id="typelogo" class="form-control input-large" aria-describedby="typelogo" maxlength="32" class="form-control"/>--}}
                                        {{--<span class="help-inline"></span>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<br/>--}}
                                <div class="control-group">
                                    <label class="control-label">分类或推荐位描述(简单描述)</label>
                                    <div class="controls">
                                        <textarea name="desc" id="sortDesc" placeholder='推荐位描述，例：精选视频'></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">推荐位置描述(详细描述描述)</label>
                                    <div class="controls">
                                        <textarea name="detail" id="detailDesc" placeholder='推荐位描述，例：精选视频，是我们从所有视频中挑选的精彩视频'></textarea>
                                    </div>
                                </div>
                                <br/>
                                <input type="hidden" name="userid" id="sortuserid" class="form-control" value="admin"/>
                                <input type="hidden" name="id" id="sortuniqId" class="form-control" value=""/>
                                <input type="hidden" name="posid" id="sortposid" class="form-control" value=""/>
                                <div class="alert alert-danger hide errorBox" id="addErrorBox">操作失败</div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-lg btn-primary" id="sortSureBtn"><i class="icon-ok"></i>确定</button>
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


    <!--BEGIN PILLS NAV-->
    <div class="bs-docs-example" style="margin-left: 5%;">
        {{--<h4>分类筛选</h4>--}}
        {{--<ul class="nav nav-pills">--}}
            {{--<li class="active"><a href="#">Regular link</a></li>--}}
            {{--@if(isset($getSortByType) && !empty($getSortByType))--}}
                {{--@foreach($getSortByType as $gk=>$gv)--}}
                    {{--<li class="dropdown">--}}
                        {{--<a href="#" data-toggle="dropdown" role="button" id="drop4" class="dropdown-toggle">{{ $gv['desc'] }}<b class="caret"></b></a>--}}
                        {{--<ul aria-labelledby="drop4" role="menu" class="dropdown-menu" id="menu1">--}}

                        {{--</ul>--}}
                    {{--</li>--}}
                {{--@endforeach--}}
            {{--@endif--}}
        {{--</ul>--}}
    </div>
    <br>
    <br>
    <hr>
    <h3>推荐位的菜单</h3>
    @if(isset($getSortByType) && count($getSortByType) > 0)
        <table class="table table-bordered">
            <tr class="trHead">
                <th class="col-md-1">{{ count($getSortByType) }}（code）</th>
                <th class="col-md-1">推荐/分类(posid)</th>
                <th class="col-md-1">描述</th>
                <th class="col-md-1">推荐/分类type</th>
            </tr>
            @foreach ($getSortByType as $v)
                <tr>
                    <td>{{ $v["code"] }}</td>
                    <td>{{ $v["posid"] }}</td>
                    <td>{{ $v["desc"] }}</td>
                    <td>{{ $v["type"] }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <h3>暂无数据!</h3>
    @endif
    <h3>视频分类详情（只有添加分类banner功能）</h3>
    @if(isset($videoSortArr) && count($videoSortArr) > 0)
        <table class="table table-bordered">
            <tr class="trHead">
                <th class="col-md-1">{{ count($videoSortArr) }}（id）</th>
                <th class="col-md-1">分类名</th>
                <th class="col-md-1">描述</th>
                <th class="col-md-1">图片</th>
                <th class="col-md-1">操作</th>
            </tr>
            @foreach ($videoSortArr as $v)
                <tr>
                    <td>{{ $v["id"] }}</td>
                    <td>{{ $v["name"] }}</td>
                    <td>{{ $v["desc"] }}</td>
                    <td>{{ $v["img"] }}</td>
                    <td>@if($v['ifcode'] == '') <span class="addSortBanner glyphicon  icon-plus-sign-alt pointer" data-sortId="{{ $v['id'] }}" data-sortCode="{{ $v['code'] }}" data-action="add" data-name="{{ $v["name"] }}"></span> @else  <span class="delSortBanner glyphicon icon-trash  pointer" data-sortId="{{ $v['id'] }}" data-sortCode="{{ $v['code'] }}" data-action="del"></span>  @endif </td>
                </tr>
            @endforeach
        </table>
    @else
        <h3>暂无数据!</h3>
    @endif
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
        //发布推荐位
        $("body").on("click", "#publicBtn", function() {

            var posid = $('#publicPosid').text();
            var paramObj = {
//                "action" : "public",
//                "posid" : posid,
            };
            var ajaxUrl = "http://admin.vronline.com/recommend/publish/" + posid;

            //location.href = url;

            pubApi.ajax(ajaxUrl, paramObj, function(result) {
                if(result.code == 0){
                    alert("发布成功");
                } else {
                    confirm("部分发布失败");
                }
                pubApi.reload();
            }, function() {
                pubApi.showError('部分发布失败');
                pubApi.reload();
            });
        });
        $('.form-windows').hide();
        $('.sort-form-windows').hide();

        //添加推荐位的分类按钮

        $("body").on("click", "a.sortAddBtn", function() {
//            var uniqId = $(this).attr("data-uniqId"),
//                    weight = $(this).attr("data-weight"),
//                    openTm = $(this).attr("data-openTm"),
//                    itmeid = $(this).attr("data-itemId"),
//                    posId = $(this).attr("data-posId");

            $('.sort-form-windows').show();
            $('#sortAdd').modal({backdrop: 'static', keyboard: false, show : true});
            return false;
        });
        //编辑推荐位输入框
        $("body").on("click", "a.accountEditBtn", function() {
            $('#addForm').attr("action","{{ url('recommend/schedule/set') }}");
            var uniqId = $(this).attr("data-uniqId"),
                weight = $(this).attr("data-weight"),
                openTm = $(this).attr("data-openTm"),
                itmeid = $(this).attr("data-itemId"),
                posId = $(this).attr("data-posId");
            $("#uniqId").val(uniqId);
            $("#qId").val(itmeid);
            $("#tmBegin").val(openTm);
            $("#posid").val(posId);
            $("#weight").val(weight);
            $('.form-windows').show();
            $('#accountAdd').modal({backdrop: 'static', keyboard: false, show : true});
            return false;
        });

        //编辑推荐位输入框
        $("body").on("click", "a.accountAddBtn", function() {
            //数据出事化
            $("#uniqId").val("");
            $("#qId").val("");
            $("#tmBegin").val("");
            $("#posid").val("");
            $("#weight").val("");
            $('#addForm').attr("action","{{ url('recommend/schedule/add') }}");
            var uniqId = $(this).attr("data-uniqId"),
                weight = $(this).attr("data-weight"),
                posId = $(this).attr("data-posId");

            $("#posid").val(posId);
            $("#weight").val(weight);
            $('.form-windows').show();
            $('#accountAdd').modal({backdrop: 'static', keyboard: false, show : true});
            return false;
        });

        //添加广告位信息
        $("body").on("click", "#sureBtn", function() {
            var weight = $("#weight").val(),
                tmBegin = $("#tmBegin").val(),
                posId = $("#posid").val();

            if ($.trim(weight) == "" || $.trim(tmBegin) == "" || $.trim(posId) == "" ){
                pubApi.showDomError($("#addErrorBox"), "非法参数");
                return false;
            }
            if (weight<1 || weight>9){
                pubApi.showDomError($("#addErrorBox"), "位置id错误");
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
                        //$('.form-windows').hide();
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

        //添加推荐位的信息分类sortSureBtn
        $("body").on("click", "#sortSureBtn", function() {
            var code = $("#sortCode").val(),
                    type = $("#sortType").val(),
                    desc = $("#sortDesc").val();

            if ($.trim(code) == "" || $.trim(type) == "" || $.trim(desc) == "" ){
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
                        $('.form-windows').hide();
                        pubApi.reload();
                        //alert('提交成功');
                    }
                },
                error:function(result){
                    alert(result.message);
                }
            };
            $("#sortAddForm").ajaxForm(options).submit(function(){return false;});
        });

        //添加视频分类banner的位置数据
        $("body").on("click", ".addSortBanner", function() {
            var code = $(this).attr("data-sortCode"),
                type = 'banner',
                desc = $(this).attr("data-name") + 'banner',
                detail = $(this).attr("data-name"),
                action = $(this).attr("data-action");

            var paramObj = {
                "action" : action,
                "code" : code,
                "type" : type,
                "desc" : desc,
                "sortmark" : 3,
                "detail" : detail,

            };
            var ajaxUrl = "{{ url('recommend/sortAdd') }}";

            pubApi.ajaxPost(ajaxUrl, paramObj, function(result) {
                if(result.code == 0){
                    pubApi.reload();
                } else {
                    confirm("添加失败");
                }
            }, function() {
                pubApi.showError();
            });
        });
    </script>
@endsection
