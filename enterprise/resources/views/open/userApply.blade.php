@extends('layouts.third')

@section('content')

    <!-- BEGIN PAGE CONTAINER-->
    <style type="text/css">

    </style>
    <!-- BEGIN PAGE -->
    <div class="row-fluid">
        <!-- BEGIN SAMPLE FORMPORTLET-->
        <div class="widget green">
            <div class="widget-title">
                <h4><i class="icon-reorder"></i> 平台账号</h4>
                    <span class="tools">
                    <a href="javascript:;" class="icon-chevron-down"></a>
                    <a href="javascript:;" class="icon-remove"></a>
                    </span>
            </div>
            <div class="bio widget-body">
                <h2>平台账号</h2>
                <p><label>平台账号 </label> kira</p>
                <input type="hidden" id="userId" value="100001"/>
                <p><label>绑定手机 </label> <a href="#">绑定手机</a></p>
                <div class="space15"></div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <!-- BEGIN SAMPLE FORMPORTLET-->
            <div class="widget green">
                <div class="widget-title">
                    <h4><i class="icon-reorder"></i> 个人信息</h4>
                <span class="tools">
                <a href="javascript:;" class="icon-chevron-down"></a>
                <a href="javascript:;" class="icon-remove"></a>
                </span>
                </div>

                <div class="widget-body">
                    <!-- BEGIN FORM-->
                    <form class="form-horizontal" role="form"  action="{{ url('uploadUserInfo') }}" method="post" name="addForm" id="addForm1" enctype="multipart/form-data">
                        <div class="control-group">
                            <label class="control-label">姓名</label>
                            <div class="controls">
                                <input type="text" name="userName" id="userName" placeholder="与身份证上姓名保持一致" class="input-xxlarge" />
                                <span class="help-inline">Some hint here</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">身份证</label>
                            <div class="controls">
                                <input type="text" name="idCard" id="idCard" placeholder="请填写个人身份证" class="input-xxlarge" />
                                <span class="help-inline">Some hint here</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">电子邮箱</label>
                            <div class="controls">
                                <input type="text" name="email" id="email" placeholder="请填写有效邮箱，用于接收平台审核通知等重要信息" class="input-xxlarge" />
                                <span class="help-inline">Some hint here</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">联系地址</label>
                            <div id="distpicker3" class="controls">
                                <select class="input-medium m-wrap" tabindex="1" name="province" id="province10"></select>
                                <select class="input-medium m-wrap" tabindex="1" name="city" id="city10"></select>
                                <select class="input-medium m-wrap" tabindex="1" name="district" id="district10"></select>

                                <span class="help-inline">选择地区省市</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"></label>
                            <div class="controls">
                                <input type="text" name="address" id="address" placeholder="请填写有效地址，该地址作为联系您和回寄纸质协议等使用" class="input-xxlarge" />
                                <span class="help-inline">不能为空！</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">手持身份证照片</label>
                            <div class="controls">
                                <img src="" id="img0" width="100" height="80" style="display: none"/>
                                <input type="file" name="file" id="file" placeholder="" class="input-xxlarge" />
                                <span class="help-inline"></span>
                            </div>
                        </div>

                        <input type="hidden" name="uid" value="100003" />
                        <input type="hidden" name="type" value="2" />
                        <div class="control-group">

                        </div>
                        <div class="form-actions">
                            <p class="" style="height:50px;margin-bottom:30px;" ><input type="radio" name="agree" id="agree" value="1" checked /> <a href="#" style="line-height: 80px;">同意接受XXXXXXXX开发者协议</a></p>
                            {{--<button type="submit" class="btn blue"><i class="icon-arrow-left"></i>&nbsp;上一步</button>--}}
                            <button type="button" id="sureBtn" class="btn">下一步&nbsp;<i class="icon-arrow-right"></i> </button>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')
    <script type="text/javascript">
        //添加广告位信息
        $("body").on("click", "#sureBtn", function() {
            var userName = $("#userName ").val(),
                    userId = $("#userId").val(),
                    email = $("#email").val(),
                    idCard = $("#idCard").val();
            if ($.trim(email) == "" && $.trim(idCard) == "" && $.trim(userId) == ""){
                //pubApi.showDomError($("#addErrorBox"), "非法参数");
                alert("非法参数");
                return false;
            }
            addForm.submit();
            $("#addForm1").submit(function(){
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
                    console.dir(result);
//                    if(result.code !== 0){
//                        console.dir(result);
//                    }else{
//                        //pubApi.reload();
//                    }
                },
                error:function(result){
                    console.dir(result);
                }
            };
//            $("#addForm1").ajaxForm(options).submit(function(){
//                return false;
//            });
            $("#addForm1").ajaxForm(options).submit(function(){
                //return false;
            });
            // 绑定表单提交事件处理器
        });

        $("#file").change(function(){
            $("#img0").show();
            var objUrl = getObjectURL(this.files[0]) ;
            console.log("objUrl = "+objUrl) ;
            if (objUrl) {
                $("#img0").attr("src", objUrl) ;
            }
        }) ;
        //建立一個可存取到該file的url
        function getObjectURL(file) {
            var url = null ;
            if (window.createObjectURL!=undefined) { // basic
                url = window.createObjectURL(file) ;
            } else if (window.URL!=undefined) { // mozilla(firefox)
                url = window.URL.createObjectURL(file) ;
            } else if (window.webkitURL!=undefined) { // webkit or chrome
                url = window.webkitURL.createObjectURL(file) ;
            }
            return url ;
        }
    </script>
     <script type="text/javascript" src="{{static_res('/open/assets/distpicker.min.js')}}"></script>
    <script type="text/javascript" src="{{static_res('/common/js/main.js')}}"></script> <!--引入地区选择插件-->

@endsection
