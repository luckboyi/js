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
    $tmEnd = isset($tmEnd) ? $tmEnd : "";
    ?>
    @if(!isset($searchword) || $searchword == '')
        {{ $searchword = "" }}
    @endif
    <!-- BEGIN PAGE -->
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

            <h1>
                视频管理 - 所有视频搜索
            </h1>
            <!--视频查询的区块-->

        </div>
        <!--查询的输入框-->
    <hr/>
    <div class="form-inline">
        <!--日期 :
        <select class="form-control form-group w300" name="selType" id="selType">
            <option value="week" <?php echo $selType == "week" ? "selected" : ""?>>最近一周</option>
            <option value="month" <?php echo $selType == "month" ? "selected" : ""?>>最近一个月</option>
            <option value="date" <?php echo $selType == "date" ? "selected" : ""?>>自定义日期</option> -->
        <!--</select>-->
        <input class="form-control form-group w300" id="searchword"  name="searchword" placeholder="请输入视频名/或视频id号" value="{{$searchword}}"/>
        <input class="btn btn-md btn-primary form-group w100" id="itemSelBtn" value="查询"/>
    </div>

    <hr/>
    <div class="<?php echo $selType == "date" ? "collapsed" : "collapse"?> martop10" id="dateSelBox">
        <div class="well">
            <div class="form-inline">
                <div class="form-group">
                    <label for="exampleInputName2">开始时间&nbsp;:</label>
                <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="<?php echo $tmBegin;?>" placeholder="开始时间" id="tmBegin" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </span>
                </div>
                &nbsp; - &nbsp;
                <div class="form-group">
                    <label for="exampleInputEmail2">结束时间&nbsp;:</label>
                <span class="input-group date timePicker" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="<?php echo $tmEnd;?>" placeholder="结束时间" id="tmEnd" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </span>
                </div>
            </div>
        </div>
    </div>
    <hr/>

    <div class="alert alert-danger hide errorBox">操作失败</div>
    <div class="alert alert-success hide msgBox">操作成功</div>

    @if(isset($data) && count($data) > 0)
        <table class="table table-bordered">
            <tr class="trHead">
                <th class="col-md-1">{{ count($data) }}（vid）</th>
                <th class="col-md-1">视频名字</th>
                <th class="col-md-1">视频描述</th>
                <th class="col-md-1">展示图片</th>
                <th class="col-md-1">审核状态</th>
                <th class="col-md-1">添加人</th>
                <th class="col-md-1">观看次数</th>
                <th class="col-md-1">创建时间</th>
                <th class="col-md-1">操作</th>
            </tr>
            @foreach ($data as $v)
                <tr>
                    <td>{{ $v["vid"] }}</td>
                    <td>{{ $v["videoname"] }}</td>
                    <td>{{ $v["vintroduction"] }}</td>
                    <td>{{ $v["videologo"] }}</td>
                    <td>{{ $v["ispassed"] }}</td>
                    @if($v['vuid'] !== 0)
                        <td>{{ $v['vuid'] }}</td>
                    @else
                        <td>admin</td>
                    @endif
                    <td>{{ $v["viewednum"]  }}</td>
                    <td>{{ date("Y-m-d H:i", $v["publishedtime"])  }}</td>
                    <td>
                        <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v["vid"] }}" data-typename="{{ $v["videoname"] }}" data-typedesc="{{ $v["vintroduction"] }}" data-typeurl="{{ $v["videologo"] }}" data-ispassed="{{ $v["ispassed"] }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                        &nbsp;&nbsp;<a href="javascript:void(0);" class="sortDelBtn" data-uniqId="{{ $v["vid"] }}"  data-userId="{{ $v['vuid'] }}"><span class="glyphicon icon-trash" aria-hidden="true"></span></a>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <h3>暂无数据!</h3>
    @endif
    <!-- END PAGE -->
        {!! $data->render() !!}
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
    </script>
@endsection
