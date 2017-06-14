<html lang="en"><!--<![endif]--><!-- BEGIN HEAD --><head>
   <meta charset="utf-8">
   <title>注册页面</title>
   <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <meta content="" name="description">
   <meta content="" name="author">
   <meta name="_token" content="{{ csrf_token() }}"/>
   <link href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
   <link href="{{asset('assets/bootstrap/css/bootstrap-responsive.min.css')}}" rel="stylesheet">
   <link href="{{asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
   <link href="{{asset('css/style.css')}}" rel="stylesheet">
   <link href="{{asset('css/style-responsive.css')}}" rel="stylesheet">
   <link href="{{asset('css/style-default.css')}}" rel="stylesheet" id="style_color">

   <!-- 引用jquery -->
   <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.min.js"></script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="lock">
    <div class="lock-header">
        <!-- BEGIN LOGO -->
        <a class="center" id="logo" href="index.html">
            <img class="center" alt="logo" src="{{asset('images/logo.png')}}">
        </a>
        <!-- END LOGO -->
    </div>
    <div class="login-wrap">
        <div class="metro single-size red">
            <div class="locked">
                <i class="icon-lock"></i>
                <span>注册</span>
            </div>
        </div>
        <form action="{{action('UserController@postLogin')}}" class="panel-body wrapper-lg" method="GET">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="metro double-size green">
            <form action="index.html">
                <div class="input-append lock-input">
                    <input type="text" name="name" class="" placeholder="Username">
                </div>
            </form>
        </div>
        <div class="metro double-size yellow">
            <form action="index.html">
                <div class="input-append lock-input">
                    <input type="password" name="pwd" class="" placeholder="Password">
                </div>
            </form>
        </div>
        <div class="metro single-size terques login">
            <button type="submit" class="btn login-btn">
                注册
                <i class=" icon-long-arrow-right"></i>
            </button>
        </div>
        </form>
    </div>

<!-- END BODY -->
</body></html>


<script type="text/javascript">

// 登录按钮提交事件
$(":button[type=submit]").click( function () {

        // 得到name和password值
        var name = $(":input[name=name]").val();
        var pwd = $(":input[name=pwd]").val();

        if (name.length == 0 || pwd.length == 0) {
            alert('用户名密码不能为空');return;
        }

        $.ajax({
            type: 'get',
            url: '/ajax/create',
            data: { name : name, pwd : pwd},
            dataType: 'json',
            headers: {  // header属性，是为了避免跨站伪造请求攻击写的
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(data){
                if (data.status == 1) {
                	$(":input[name=name]").val('');
                	$(":input[name=pwd]").val('');
                    alert('创建用户成功');
                }else{
                    alert(data.msg);
                }
            },
            error: function(xhr, type){
            }
            });
});

</script>