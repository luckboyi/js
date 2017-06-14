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
        <h2 style="margin-left: 5%;">
            Banner管理 - 推荐管理
        </h2>
        <a class="btn btn-primary pull-right sortAddBtn" href="javascript:void(0);" style="margin-right: 5%;">
            添加banner类别 - [&nbsp;<span class="glyphicon glyphicon-plus icon-plus-sign-alt pointer"></span>&nbsp;]
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
                            <form class="form-horizontal" action="{{ url('schedule/add') }}" method="post" name="addForm" id="addForm" enctype="multipart/form-data">
                                <div class="control-group">
                                    <label class="control-label">推荐ID</label>
                                    <div class="controls">
                                        <input type="text" id="qId" name="itemid" class="form-control input-large" placeholder="推荐页游/端游/视频ID号" aria-describedby="title" maxlength="32">
                                        <span class="help-inline"></span>
                                    </div>
                                </div>

								<div class="control-group">
									<label class="control-label">推荐位排序</label>
									<div class="controls">
										<input type="text" id="weight" name="weight" class="form-control input-large" value="" placeholder="序列号越小越靠前" aria-describedby="title" maxlength="8">
										<span class="help-inline"></span>
									</div>
								</div>
                                <div class="control-group">
                                    <label class="control-label">上传banner图</label>
                                    <div class="product-row clearfix" style="">
                                        <div class="pic-con clearfix" id="banner-container">
                                            @if(isset($detail["bg"]))
                                                <div class='pic fl preview'><a href="{{ $detail["bg"] }}" target="_blank"><img src="{{ $detail["bg"] }}" /></a></div>
                                            @endif
                                            <div class='pic fl' id="banner-upload">
                                                <span class="pic-icon"></span>
                                                <span class="pic-txt">1920*1080 JPG</span>
                                                <span class="pic-txt underline" >上传图片</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Banner跳转地址</label>
                                    <div class="controls">
                                        <input type="text" id="targetUrl" name="targetUrl" class="form-control input-large" value="" placeholder="Banner跳转地址：若是自己的可以不填写" aria-describedby="title" maxlength="128">
                                        <span class="help-inline"></span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">开始时间</label>
                                    <div class="controls">
                                                <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                                                    <input class="form-control" type="text" name="start" value="" placeholder="开始时间" id="tmBegin">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar icon-calendar"></span></span>
                                                </span>
                                    </div>
                                </div>
                                <input type="hidden" name="showpic" id="addshowpic" class="form-control" value=""/>
                                <input type="hidden" name="userid" id="userid" class="form-control" value="admin"/>
                                <input type="hidden" name="id" id="uniqId" class="form-control" value=""/>
                                <input type="hidden" name="posid" id="posid" class="form-control" value=""/>
                                <input type="hidden" name="action" id="action" class="form-control" value="banner"/>
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
                                    <label class="control-label">推荐位类别</label>
                                    <div class="controls">
                                        <select class="form-control form-group w300" name="type" id="sortType" hidden>
                                            @if(isset($getSortByType) && !empty($getSortByType))
                                                @foreach($getSortByType as $gk=>$gv)
                                                    <option value="{{ $gv['type'] }}">{{ $gv['desc'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">推荐位或分类标识</label>
                                    <div class="controls">
                                        <select class="form-control form-group w300" name="sortmark" id="sortMark">
                                            <option value="1">推荐位</option>
                                            <option value="2">游戏/视频分类</option>
                                            <option value="3">游戏/视频分类banner</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">上传分类图</label>
                                    {{--<div class="controls">--}}
                                        <div class="product-row clearfix" style="">
                                            <div class="pic-con clearfix" id="bg-container">
                                                @if(isset($detail["bg"]))
                                                    <div class='pic fl preview'><a href="{{ $detail["bg"] }}" target="_blank"><img src="{{ $detail["bg"] }}" /></a></div>
                                                @endif
                                                <div class='pic fl' id="bg-upload">
                                                    <span class="pic-icon"></span>
                                                    <span class="pic-txt">1920*1080 JPG</span>
                                                    <span class="pic-txt underline" >上传图片</span>
                                                </div>
                                            </div>
                                        </div>
                                    {{--</div>--}}
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
                                <input type="hidden" name="showpic" id="showpic" class="form-control" value=""/>
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
        <h3>Banner分类</h3>
        <ul class="nav nav-pills">
            {{--<li class="active"><a href="#">Regular link</a></li>--}}
            @if(isset($getSortByType) && !empty($getSortByType))
                @foreach($getSortByType as $gk=>$gv)
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" role="button" id="drop4" class="dropdown-toggle">{{ $gv['desc'] }}<b class="caret"></b></a>
                        <ul aria-labelledby="drop4" role="menu" class="dropdown-menu" id="menu1">
                            @if(isset($sort[$gk]) && !empty($sort[$gk]))
                                @foreach($sort[$gk] as $sk =>$sv)
                                    <li role="presentation" id="{{$sv['posid']}}" ><a href="/bannerRecommend/{{$sv['code']}}" class="@if($sv['posid'] == $posid) btn-info @endif">{{$sv['desc']}}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
    <ul class="nav nav-pills" role="tablist" id="sortList">
        <!--END PILLS NAV-->
		{{--@if(isset($sort) && count($sort)>0)--}}
			{{--@foreach($sort as $k=>$s)--}}
				{{--<li role="presentation" id="{{$s['posid']}}" ><a href="/recommendWebGame/{{$s['code']}}" class="@if($s['posid'] == $posid) btn btn btn-info @endif">{{$s['desc']}}</a></li>--}}
			{{--@endforeach--}}
		{{--@endif--}}
	</ul>
    @if(isset($data) && count($data) > 0)
            <div class="row-fluid" style="width: 90%; margin-left: 5%;">
                <hr>
                <h2>{{ $desc }}({{ count($data) }})<span style="margin-left:30px;"><span id="publicPosid" style="display: none;">{{ $posid }}</span></span><button class="btn btn btn-info" id="publicBtn"><i class="icon-ok icon-white" ></i> 发布</button></h2>
                <!--BEGIN METRO STATES-->
                @if(!empty($data))
                    @foreach($data as $k=>$v)
                        <div class="metro-nav metro-fix-view" style="margin-bottom: 20px;">
                                <div class="row-fluid span3" style="float: left;margin-right:1%;">
                                    <div class="" style="height:280px;">
                                        <!-- BEGIN BASIC PORTLET-->
                                        {{--<div class="widget orange">--}}
                                            {{--<div class="widget-title">--}}
                                                {{--<h4><i class="icon-reorder"></i>修改和添加位置</h4>--}}
                                                {{--<span class="tools">--}}
                                                    {{--<a href="javascript:;" class="icon-chevron-down"></a>--}}
                                                    {{--<a href="javascript:;" class="icon-remove"></a>--}}
                                                {{--</span>--}}
                                            {{--</div>--}}
                                            <div class="widget-body">
                                                <table class="table table-striped table-bordered table-advance table-hover">
                                                    <thead>
                                                    <tr style="text-align:center">
                                                        <th colspan="3"><img src="@if(isset($v['showPic'])) {{ $v['showPic'] }} @else {{ url('images/404') }} @endif"  title="@if(isset($v['name'])){{ $v['name'] }} @else 无 @endif" style="width: 320px;height: 116px;"></th>
                                                    </tr>
                                                    </thead>
                                                    <tr>
                                                        <td>游戏名字</td>
                                                        <td class="hidden-phone"><i class="icon-question-sign"></i> 开始时间</td>
                                                        <td>修改</td>
                                                    </tr>

                                                    <tbody>
                                                    @if(isset($v['tmp']) || !empty($v['tmp']))
                                                        @foreach($v['tmp'] as $tk=>$tv)
                                                            <tr>
                                                                <td><a href="#">@if($tv['name'] != ''){{ $tv['name'] }} @else 无该游戏 @endif </a></td>
                                                                <td>{{ date('Y-m-d H:i:s', $tv['start']) }}</td>
                                                                <td>
                                                                    <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $tv['id'] }}" data-targeturl="{{ $tv['target_url'] }}" data-itemId="{{ $tv['itemid'] }}" data-posId="{{ $posid }}"  data-weight="{{ $k}}" data-openTm="{{ date('Y-m-d H:i:s', $tv['start']) }}" data-endTm=""><button class="btn btn-primary"><i class="icon-pencil"></i></button></a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <th colspan="3" style="text-align: right;"><a href="javascript:void(0);" class="accountAddBtn" data-uniqId="" data-posId="{{ $posid }}"  data-weight="{{ $k }}" data-openTm="" data-endTm=""><button class="btn btn-primary"><i class="icon-plus"></i></button></a></th>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- END BASIC PORTLET-->
                                    {{--</div>--}}
                                </div>
                        </div>
                    @endforeach
                @else
                    <h3>暂无数据!</h3>
                @endif
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
                //pubApi.reload();
            }, function() {
                pubApi.showError('部分发布失败');
                //pubApi.reload();
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
                targetUrl = $(this).attr("data-targeturl"),
                posId = $(this).attr("data-posId");
            $("#uniqId").val(uniqId);
            $("#qId").val(itmeid);
            $("#tmBegin").val(openTm);
            $("#posid").val(posId);
            $("#targetUrl").val(targetUrl);
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

        var stat = 0;
        var operr = "资料审核中 无法修改"
        //上传图片的js模块
        var bgOp = {name:"bg",cos:{bucket:"vronline",remote:"/bannersort/",randName:true,signUrl:"http://admin.vronline.com/upload",isSave:false}}
        if(stat==1) {
            bgOp.err = operr
        }
        var bgUploader = Open.upload(bgOp,function(up, file, info) {
            var previewObj = $("#bg-container").find(".preview");
            if(previewObj.length>0) {
                previewObj.html('<a href="'+info.access_url+'" target="_blank"><img src="'+info.access_url+'" width="130px"/></a>');
            } else {
                console.log(info.access_url);
                $("#showpic").val(info.access_url);
                $("#bg-container").prepend('<div class="pic fl preview"><a href="'+info.access_url+'" target="_blank"><img src="'+info.access_url+'" /></a></div>');
            }
        },function(up,err,msg){
            Open.showMessage(msg)
        })

        var bannerOp = {name:"banner",cos:{bucket:"vronline",remote:"/bannerimg/index/",randName:true,signUrl:"http://admin.vronline.com/upload",isSave:false}}
        if(stat==1) {
            bannerOp.err = operr
        }

        var bannerUploader = Open.upload(bannerOp,function(up, file, info) {
            var previewObj = $("#banner-container").find(".preview");
            if(previewObj.length>0) {
                previewObj.html('<a href="'+info.access_url+'" target="_blank"><img src="'+info.access_url+'" width="130px"/></a>');
            } else {
                console.log(info.access_url);
                $("#addshowpic").val(info.access_url);
                $("#banner-container").prepend('<div class="pic fl preview"><a href="'+info.access_url+'" target="_blank"><img src="'+info.access_url+'" /></a></div>');
            }
        },function(up,err,msg){
            Open.showMessage(msg)
        })
    </script>
@endsection
