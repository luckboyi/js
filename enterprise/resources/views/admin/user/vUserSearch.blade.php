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

            <h2>
                用户查询 - 用户的视频记录信息
            </h2>
            <!--视频查询的区块-->

        </div>
        <!--查询的输入框-->
    <hr/>
    <div class="form-inline" >
        视频分类 :
        <select class="form-control form-group w300" name="selType" id="selType">
            <option value="all" {{$selType == 'all' ? "selected":""}}>全部视频</option>
            @foreach($sort as $v)
                <option value="{{ $v['vtid'] }}" {{$selType == $v['vtid'] ? "selected":""}}>{{ $v['typename'] }}</option>
            @endforeach

        </select>
        <input class="form-control form-group w300" id="searchword"  name="searchword" placeholder="请输入用户id" value="{{ $uid }}"/>
        <input class="btn btn-md btn-primary form-group w100" id="itemSelBtn" value="查询"/>
    </div>


    <div class="alert alert-danger hide errorBox">操作失败</div>
    <div class="alert alert-success hide msgBox">操作成功</div>

    @if(isset($data) && count($data) > 0)

        @foreach($data as $v)
            <hr>
            <div class="row-fluid" style="margin:10px auto;">

                <div class="span3">
                    <img src="{{ $v['videoLogo'] }}" width="100%" title="{{ $v['videoName'] }}">
                </div>
                <div class="span7">
                    <!-- BEGIN BASIC PORTLET-->
                    <div class="widget green">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i> 用户的视频记录</h4>
                                <span class="tools">
                                    <a href="javascript:;" class="icon-chevron-down"></a>
                                    <a href="javascript:;" class="icon-remove"></a>
                                </span>
                        </div>
                        <div class="widget-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td>视频名称</td>
                                    <td>{{ $v['videoName'] }}</td>
                                    <td>上次观看时间</td>
                                    <td>{{ $v['preTm'] }}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    {{--<td rowspan="2">1</td>--}}
                                    <td>付费金额</td>
                                    <td>???</td>
                                    <td>观看总时长</td>
                                    <td>{{ $v['costTm'] }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END BASIC PORTLET-->
                </div>

            </div>
        @endforeach
    @else
        <h3>暂无数据!</h3>
    @endif
    <!-- END PAGE -->
    <div class="span9">

        <?php
        if(isset($uid) && $uid !== '') {
            echo $paginator->render();
        }
        ?>
    </div>

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
            forceParse: 0
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
            var sort = $('#selType').val();
            var searchword = $("#searchword").val();

            var url = "vUserSearch/" + sort + "/" + searchword;
            if(searchword == '' || searchword == '请输入用户id') {
                alert("请输入有效的用户id");
            } else {
                pubApi.jumpUrl(url);
            }

            var paramObj = {
                "action" : "search",
                "searchword" : searchword,
                //"userid" : userId,
            };
        });
    </script>
@endsection
