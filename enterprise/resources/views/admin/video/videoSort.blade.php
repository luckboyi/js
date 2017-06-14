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
                视频管理 - 分类管理
                <a class="btn btn-primary pull-right accountAddBtn" href="javascript:void(0);">
                    添加分类 - [&nbsp;<span class="glyphicon glyphicon-plus icon-plus-sign-alt pointer"></span>&nbsp;]
                </a>
            </h1>
            <hr>
            <!--视频分类添加的区块-->
            <div class="modal fade" id="accountAdd" tabindex="-1" role="dialog" aria-labelledby="accountAddLabel">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="accountAddLabel">添加分类</h4>
                        </div>
                        <form action="uploadSort" method="post" name="addForm" id="addForm" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">类别名称</span>
                                    <input type="text" id="typename" name="typename" class="form-control" placeholder="类别名称" aria-describedby="title" maxlength="32">
                                </div>
                                <br />
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">类别描述</span>
                                    <!--<input type="text" id="typedesc" name="typedesc" class="form-control" placeholder="类别描述" aria-describedby="mark" maxlength="32">-->
                                    <textarea  id="typedesc" name="typedesc" class="form-control" placeholder="类别描述" aria-describedby="mark" maxlength="560" rows="3"></textarea>
                                </div>
                                <br/>
                                @if(isset($data) && count($data) > 0)
                                   <input type="hidden" name="vtid" value="{{ end($data)['vtid'] + 1 }}" />
                                @endif
                                <input type="hidden" name="userid" value="0" />
                                <input type="hidden" name="submit" value="sureBtn" />
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">上传图片（56*56）</span>
                                    <input type="file" name="typelogo" id="typelogo" class="form-control" aria-describedby="typelogo" maxlength="32" class="form-control"/>
                                </div>
                                <br/>
                                <div class="input-group input-group-md hidden">
                                    <input type="text" name="userid" id="userid" class="form-control" value="admin"/>
                                </div>
                                <div class="alert alert-danger hide errorBox" id="addErrorBox">操作失败</div>
                            </div>
                            <input type="hidden" name="type" class="form-control" value="addAd"/>
                            <div class="modal-footer">
                                <input type="hidden" name="uniqId" id="uniqId"/>
                                <button type="submit" class="btn btn-lg btn-primary" id="sureBtn">确定</button>
                            </div>
                            <div class="input-group input-group-md">

                            </div>
                    </form>
                </div>
            </div>
            </div>
        </div>

        @if(isset($data) && count($data) > 0)
            <table class="table table-bordered">
                <tr class="trHead">
                    <th class="col-md-1">{{ count($data) }}（vtid）</th>
                    <th class="col-md-1">分类名</th>
                    <th class="col-md-1">分类名描述</th>
                    <th class="col-md-1">描述图片</th>
                    <th class="col-md-1">审核状态</th>
                    <th class="col-md-1">添加人</th>
                    <th class="col-md-1">显示顺序</th>
                    <th class="col-md-1">创建时间</th>
                    <th class="col-md-1">操作</th>
                </tr>
                @foreach ($data as $v)
                    <tr>
                        <td>{{ $v["vtid"] }}</td>
                        <td>{{ $v["typename"] }}</td>
                        <td>{{ $v["typedescription"] }}</td>
                        <td>{{ $v["typelogo"] }}</td>
                        <td>{{ $v["ispassed"] }}</td>
                        @if($v['userid'] !== 0)
                            <td>{{ $v['userid'] }}</td>
                        @else
                            <td>admin</td>
                        @endif
                        <td>{{ $v["sequence"]  }}</td>
                        <td>{{ date("Y-m-d H:i", $v["tmcreate"])  }}</td>
                        <td>
                            <a href="javascript:void(0);" class="accountEditBtn" data-uniqId="{{ $v["vtid"] }}" data-typename="{{ $v["typename"] }}" data-typedesc="{{ $v["typedescription"] }}" data-typeurl="{{ $v["typelogo"] }}" data-ispassed="{{ $v["ispassed"] }}" data-sequence="{{ $v["sequence"]  }}"><span class="glyphicon icon-pencil" aria-hidden="true"></span></a>
                            &nbsp;&nbsp;<a href="javascript:void(0);" class="sortDelBtn" data-uniqId="{{ $v["vtid"] }}"  data-userId="{{ $v['userid'] }}"><span class="glyphicon icon-trash" aria-hidden="true"></span></a>
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <h3>暂无数据!</h3>
        @endif
    <!-- END PAGE -->
@endsection

@section('javascript')
    <script type="text/javascript">
        //添加视频类别
        $("body").on("click", "a.accountAddBtn", function() {
            $("#uniqId").val("");
            $("#title").val("");
            $("#mark").val("").attr("readonly", false);
            $('#accountAdd').modal({backdrop: 'static', keyboard: false, show : true});
            return false;
        });
        //添加分类信息
        $("body").on("click", "#sureBtn", function() {
            var uniqId = $("#uniqId").val(),
                    typename = $("#typename").val(),
                    typedesc = $("#typedesc").val();
            if ($.trim(typename) == "" || $.trim(typedesc) == ""){
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
                        //$("body").append("&nbsp;&nbsp;<img src='"+result.data.url+"' alt='' width='100' /><input type='hidden' name='image[]' value='"+result.data.url+"'  />");
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
            var userId = $(this).attr("data-userId");
            if (confirm("确认删除??")) {
                var paramObj = {
                    "action" : "typeDel",
                    "vtid" : uniqId,
                    "userid" : userId,
                };
                var ajaxUrl = "video/videoSortDel";
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
