<html lang="en"><!--<![endif]--><!-- BEGIN HEAD --><head>
   <meta charset="utf-8">
   <title>页游注册</title>
   <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <meta content="" name="description">
   <meta content="" name="author">
   <meta name="_token" content="{{ csrf_token() }}"/>
   <!-- 引用jquery -->
   <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.min.js"></script>
</head>

<!-- 这里写中间内容 -->


<!-- 这里写中间内容 -->

<script type="text/javascript">

// 登录按钮提交事件
$(":button[type=submit]").click( function () {

        // 得到name和password值
        var name = $(":input[name=name]").val();
        var pwd = $(":input[name=pwd]").val();
        var confirPwd = $(":input[name=confirPwd]").val();

        if (name.length == 0 || pwd.length == 0 || confirPwd.length == 0) {
            alert('用户名密码不能为空');return;
        }
        if (pwd != confirPwd) {
        	alert('密码和确认密码不一致');return;
        }

        $.ajax({
            type: 'get',
            url: '/web/regiser',
            data: { name : name, pwd : pwd, confirPwd : confirPwd},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {
                    
                    // 如果注册成功，就跳到用户中心-用户资料
                    location.href="/web";

                }else{
                    alert(data.msg);
                }
            },
            error: function(xhr, type){
            }
            });
});

</script>
