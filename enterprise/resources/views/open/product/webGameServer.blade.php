@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')

@section('head')
<link rel="stylesheet" href="{{static_res('/open/assets/art-dialog/css/ui-dialog.css')}}" />
<link rel="stylesheet" href="{{static_res('/open/assets/datetimepicker/jquery.datetimepicker.min.css')}}" />
<script src="{{static_res('/open/assets/art-dialog/js/dialog-min.js')}}"></script>
<script src="{{static_res('/open/js/common.js')}}"></script>
<script src="{{static_res('/open/assets/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{static_res('/open/assets/jquery-validation/additional-methods.min.js')}}"></script>
<script src="{{static_res('/open/assets/jquery-validation/messages_zh.js')}}"></script>
<script src="{{static_res('/open/assets/datetimepicker/jquery.datetimepicker.full.min.js')}}"></script>
@endsection

@section('content')
    <!--内容-->
    <div class="container container-list">
        <div class="container-head clearfix">
            <button type="button" class="btn btn-editserver">
                <span class="icon icon-small"><i class="icon icon-small icon-plus"></i></span> 添加服务器
            </button>
        </div>
        <div class="in_con">
            <h4 class="f14">产品详情</h4>
            <div class="content product-detail">
                <p>
                     <span class="product-title">{{ $detail["name"] }}</span>
                     <span class="product-id">APP ID:{{ $detail["appid"] }}</span>
                     {!! $blade->showStat($detail["stat"],"style") !!}
                       @if($detail["stat"]==3) {{ $detail["msg"] }} @endif
                </p>
                <p>
                    <span>创建时间：{{ $detail["ctime"] }}</span>
                    <span>上线时间：{{ $blade->showDateTime($detail["send_time"]) }}</span>
                </p>
                <p>
                     <span class="product-id">APP KEY:{{ $detail["appkey"] }}</span>
                    <span class="product-id">PAY KEY:{{ $detail["paykey"] }}</span>
                </p>
            </div>
        </div>
        <div class="table-con">
            <table class="personal-table" border="0">
                <tr class="title">
                    <th>服务器ID</th>
                    <th>名称</th>
                    <th>游戏服务器地址</th>
                    <th>服务器状态</th>
                    <th>是否新服</th>
                    <th>是否推荐</th>
                    <th>上线时间</th>
                    <th>操作</th>
                </tr>
                @if($servers->count()<1)
                    <tr><td colspan="8">暂无数据</td></tr>
                @else
                    @foreach ($servers as $server)
                    <tr>
                    <td class="server-id">{{ $server["serverid"] }}</td>
                    <td class="server-name">{{ $server["name"] }}</td>
                    <td class="server-domain" data-src="{{ $server["payurl"] }}">{{ $server["domain"] }}</td>
                    <td class="server-status" data-src="{{ $server["status"] }}">{!! $blade->showServerSatus($server["status"]) !!}</td>
                    <td class="server-isnew" data-src="{{ $server["isnew"] }}">{{ $blade->showIsNew($server["isnew"]) }}</td>
                    <td class="server-recommend" data-src="{{ $server["recommend"] }}">{{ $blade->showRecommend($server["recommend"]) }}</td>
                    <td class="server-start">{{ $blade->showDateTime($server["start"]) }}</td>
                    <td><a href="javascript:;" class="edit-recommend">编辑</a></td>
                    </tr>
                    @endforeach
                @endif
            </table>
             <div class="page">
                {!! $servers->render() !!}
            </div>
        </div>

         <div class="button-con" style="margin-top:20px">
            <button type="button" class="btn btn-history">返回</button>
        </div>
    </div>

    <div style="display: none" id="editTemp">
        <form class="form-horizontal">
        <div class="control-group">
                <label class="control-label">服务器ID：</label>
                <div class="controls">
                    <input type="text"  class="medium edit-id"  value="" >
                    <input type="hidden"  class="medium edit-old-id"  value="" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">服务器名称：</label>
                <div class="controls">
                    <input type="text"  class="medium edit-name" name="name" value="" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">服务器地址：</label>
                <div class="controls">
                    <input type="text" name="edit-domain" class="medium edit-domain" value="" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">发货回调地址：</label>
                <div class="controls">
                    <input type="text" name="edit-pay" class="medium edit-pay" value="" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">服务器状态：</label>
                <div class="controls">
                    <select class="edit-status" name="edit-status">
                        <option value="0">正常</option>
                        <option value="3">拥挤</option>
                        <option value="6">繁忙</option>
                        <option value="9">维护</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否新服</label>
                <div class="controls">
                    <label class="checkbox line">
                        <input type="checkbox" class="edit-isnew" value="1">
                    </label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">是否推荐</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" class="edit-isrecommend" value="1">
                    </label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">上线时间：</label>
                <div class="controls">
                    <input class="medium edit-start" name="edit-start" type="text" value="">
                </div>
            </div>
        </form>
    </div>
@endsection

@section('javascript')
<script type="text/javascript">
var appid = {{ $detail["appid"] }};

$(function(){

    $.datetimepicker.setLocale('ch');

    $(".btn-editserver").click(function() {
        Open.editServer();
    });

     $(".edit-recommend").click(function() {
        var obj = $(this).parent().parent();
        Open.editServer(obj)
    });

});

Open.validate=function(){
    var _this=this;
    _this.validator = $("#editCon").validate({
        rules: {
            "edit-id":{
                number:true
            },
            "name": {
                required: true,
                rangelength: [2, 30]
            },
            "edit-domain":{
                url: true,
                rangelength: [5, 100]
            },
            "edit-pay":{
                url: true,
                rangelength: [5, 100]
            },
            "edit-status":{
                required: true
            },
            "edit-start":{
                required: true
            }
        },
        submitHandler: function() {
            var obj = $("#editCon");
            var serverid = obj.find(".edit-id").val();
            var oldserverid = obj.find(".edit-old-id").val();
            var name = obj.find(".edit-name").val();
            var domain = obj.find(".edit-domain").val();
            var payurl = obj.find(".edit-pay").val();
            var status = parseInt(obj.find(".edit-status").val());
            var is_new = Number(obj.find(".edit-isnew").prop("checked"));
            var is_recommend =  Number(obj.find(".edit-isrecommend").prop("checked"));
            var start = obj.find(".edit-start").val();

            var req = {serverid:serverid,name:name,payurl:payurl,domain:domain,status:status,is_recommend:is_recommend,is_new:is_new,start:start}
            if(serverid!="") {
                req.oldserverid = oldserverid
            }
            $.post(Open.urls.webGameServerSave+appid,req,function(data){
                if(data.code==0) {
                     _this.showMessage("编辑成功",1500,function() {
                        location.reload();
                     });
                }else if(data.code==2){
                    _this.validator.showErrors({
                      "name": "服务器名称不能相同",
                    });
                }else{
                     _this.showMessage(data.msg);
                }
            },"json")
            return false;
        }
    });
}

Open.editServer=function(editObj) {
    var _this=this;
    if(typeof(editObj)!="undefined") {
        var serverid = editObj.find(".server-id").html();
        var name = editObj.find(".server-name").html();
        var payurl = editObj.find(".server-domain").attr("data-src")
        var domain = editObj.find(".server-domain").html();
        var status = editObj.find(".server-status").attr("data-src");
        var is_new = editObj.find(".server-isnew").attr("data-src");
        var is_recommend =  editObj.find(".server-recommend").attr("data-src");
        var sell_time = editObj.find(".server-start").html();
    }
    var editTempClone = $("#editTemp").clone();
    editTempClone.find(".form-horizontal").attr("id", "editCon");
    var editTempHtml = editTempClone.html();
    editTempClone.remove();
    myDialog.dialog({
        id: "edit",
        title: " ",
        content: editTempHtml,
        cancelDisplay: false,
        ok: function() {
            $("#editCon").submit();
            return false;
        },
        okValue: '发布',
        onshow: function() {
             var obj = $("#editCon");
            if(typeof(editObj)!="undefined") {
                obj.find(".edit-id").val(serverid);
                obj.find(".edit-old-id").val(serverid);
                obj.find(".edit-name").val(name);
                obj.find(".edit-domain").val(domain);
                obj.find(".edit-pay").val(payurl);
                obj.find(".edit-start").val(sell_time);
                var  ops =  obj.find(".edit-status").find("option")
                $.each(ops,function(index,op) {
                   if($(op).val()==status) {
                       $(op).attr('selected','selected');
                   }
                })
                if(is_new==1) {
                     obj.find(".edit-isnew").prop("checked",true)
                } else {
                    obj.find(".edit-isnew").prop("checked",false)
                }
                if(is_recommend==1) {
                     obj.find(".edit-isrecommend").prop("checked",true)
                } else {
                    obj.find(".edit-isrecommend").prop("checked",false)
                }
            }
            obj.find(".edit-start").datetimepicker({
                format:'Y-m-d H:i:s'
            });
            _this.validate();
        },
        cancel:function() {
            var obj = $("#editCon");
            obj.find(".edit-start").datetimepicker("destroy");
        }
    });
}
</script>
@endsection