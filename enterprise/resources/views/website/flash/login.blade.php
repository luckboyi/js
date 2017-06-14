@inject('blade', 'App\Helper\BladeHelper')
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>注册登录</title>
		<link rel="stylesheet" href="{{static_res('/common/style/base.css')}}">
		<link rel="stylesheet" href="{{static_res('tg/style/weblogin.css')}}">
		<script type="text/javascript">
			// var listen = window.addEventListener || window.attachEvent,
		 //    beforeunload  = window.attachEvent ? "onbeforeunload" : "beforeunload";

		 //    listen(beforeunload,function(event){
		 //       	event.returnValue = "确定要离开了嘛？";
		 //    });
		</script>
		<script language="JavaScript" src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
		<script language="JavaScript" src="{{static_res('/common/js/messenger.js')}}"></script>
		<script language="JavaScript" src="{{static_res('/common/js/tips.js')}}"></script>
		<script type="text/javascript">
			var vronlineTg={
				appid:{{$appid}},
				serverid:{{$serverid}},
				adid:{{$adid}}
			}
			var kingnetObj = {
				advid:vronlineTg.adid,
				uid:"",
			 	sid:7,
			 	op:6
			};
		</script>
		<script src='//static.kingnetdc.com/kingnet.js?{{Config::get("staticfiles.file_version")}}' type='text/javascript'></script>
		<style type="text/css">
			.web_login .login_con ul li .errorMsg p.erro_msg_show{
				display: none;
			}
		</style>
	</head>
	<body>
		<div class="web_login">
			<div class="login_title">
				<span class="up" do="register">新用户注册</span>
				<span do="login">老用户登录</span>
			</div>
			<div class="login_con">
				<img src="{{static_res('tg/images/new.png')}}" />
				<ul>

					<li class="reg_container">
						<span>账户名</span>
						<input type="text" name="register-account" />
						<div class="errorMsg">
							<p class="erro_msg_show"><i></i><b></b></p>
						</div>
					</li>
					<li class="reg_container">
						<span>登录密码</span>
						<input type="password" name="register-pwd" />
						<div class="errorMsg">
							<p class="erro_msg_show"><i></i><b></b></p>
						</div>
					</li>
					<li class="reg_container">
						<span>确认密码</span>
						<input type="password" name="register-confirmPwd" />
						<div class="errorMsg">
							<p class="erro_msg_show"><i></i><b></b></p>
						</div>
					</li>
					<li class="reg_verify_container" style="display:none">
			            <label class="pr">
			                <span>验证码</span>
			                <input type="text" name="register-code" placeholder="输入验证码">
			                <img src="" class="pa">
			                <div class="errorMsg">
								<p class="erro_msg_show"><i></i><b></b></p>
							</div>
			            </label>
					</li>
				</ul>
			</div>
			<div class="login_con hide">
				<ul class="register_con">
					<li class="login_container">
						<span>账户名</span>
						<input type="text" name="login-name" value="{{$account}}"/>
						<div class="errorMsg">
							<p class="erro_msg_show"><i></i><b></b></p>
						</div>
					</li>
					<li class="login_container">
						<span>登录密码</span>
						<input type="password" name="login-pwd" />
						<div class="errorMsg">
							<p class="erro_msg_show"><i></i><b></b></p>
						</div>
					</li>
					<li class="login_verify_container" style="display:none">
			            <label class="pr">
			                <span>验证码</span>
			                <input type="text" name="login-code" placeholder="输入验证码">
			                <img src="" class="pa">
			                <div class="errorMsg">
								<p class="erro_msg_show"><i></i><b></b></p>
							</div>
			            </label>
					</li>
				</ul>
			</div>
			<div class="login_play_games">
				<p id="startGame" do="register">开始游戏</p>
				<div style="margin-bottom: 20px;">
					<div class="agreement">
						<i class="sel selected" id="agreement"></i>
						<span>我已阅读并同意<a href="http://www.vronline.com/user_agreement" target="_blank">《VRonline用户注册协议》</a></span>
					</div>
				</div>
				<div class="third_login">
					<a href="javascript:;" id="qqLogin"><i></i>QQ登录</a>
					<a href="javascript:;" id="wxLogin"><i class="wx"></i>微信登录</a>
					<a href="javascript:;" id="weiboLogin"><i class="wb"></i>微博登录</a>
				</div>
			</div>
		</div>
	</body>
</html>
<script>
	$(function(){
		kad.customEvent('openLoginFrame');
		//注册和登录切换
		var aSpan=$(".login_title span");
		var aCon=$(".login_con");
		var now=0;
		aSpan.click(function(){
			var todo=$(this).attr("do");
			$("#startGame").attr("do",todo);
			now=$(this).index();
			tab();
		});
		function tab(){
			aSpan.removeClass('up');
			aCon.addClass('hide');
			aSpan.eq(now).addClass('up');
			aCon.eq(now).removeClass('hide');
			if(now==1){
				$(".agreement").hide();
			}else{
				$(".agreement").show();
			}
		};

		@if ($account)
			aSpan[1].click();
		@endif

		$("#startGame").click(function(event) {
			/* Act on the event */
			var todo=$(this).attr("do");

			var data=window.vronlineTg;
			var info={};
			if(todo=="register"){
				data.account=$("input[name='register-account']").val();
				data.pwd=$("input[name='register-pwd']").val();
				data.confirmPwd=$("input[name='register-confirmPwd']").val();
				data.code = $("input[name='register-code']").val();
				info.account=data.account;
			}else{
				data.name=$("input[name='login-name']").val();
				data.pwd=$("input[name='login-pwd']").val();
				info.name=data.name;
				data.code = $("input[name='login-code']").val();
			}

			kad.customEvent('click',{
				todo:todo,
				info:info
			});

			var check=window.checkData(data,todo);

			if(!check){
				return false
			}

			var callFun="call_"+todo;
			window[callFun](data);

		});

		$("input").blur(function(){
			var param=$(this).attr("name").split("-");
			var type=param[0];
			var name=param[1];
			var value=$(this).val();

			if(name=="account"&&window.checkAccout!=value){
				window.checkAccout=0;
			}
			var data={};
			data[name]=value;
			checkData(data,type);
		});

		$("#agreement").click(function(){
			if($(this).hasClass("selected")){
				$(this).removeClass("selected");
			}else{
				$(this).addClass("selected");
			}
		})
	});

	var checkAccout=0;

	function checkAccountRepeat(account){
		$.ajax({
			url: '/api/account',
			type: 'post',
			dataType: 'json',
			data: {account: account},
			async:false
		})
		.done(function(data) {
			if (data.code == 0) {
				window.checkAccout=account;
			}else{
				window.checkAccout=0;
			}
		})
		.fail(function() {
			window.checkAccout=0;
		});
	}

	/**
	 * 客服端访问时加载的通用js
	 */
	var messenger = new Messenger("login", 'vronline-tg');
	messenger.addTarget(window.parent, 'flash');

	var allowParam={
		login:{
			name:function(name){
				if(!name){
					this.errmsg = "账号不能为空";
                    return false;
				}
				return true;
			},
			pwd:function(pwd){
				if(!pwd){
					this.errmsg = "密码不能为空";
                    return false;
				}
				return true;
			}
		},
		register:{
			account:function(account){
				if (account == "") {
                    this.errmsg = "昵称不能为空";
                    return false;
                }
                var rs = /^[a-zA-Z\u4E00-\u9FA50-9][a-zA-Z\u4E00-\u9FA50-9_]*$/;
                if (!rs.test(account)) {
                    this.errmsg = "只允许中英文、数字、下划线，且不能以下划线开头"
                    return false;
                }
                var a = account.length;
                if (account.match(/[^\x00-\xff]/ig) != null) {
                    var b = account.match(/[^\x00-\xff]/ig).length;
                    a = a + b * 2
                }
                if (a < 6 || a > 18) {
                    this.errmsg = "昵称长度只能6~18个字符";
                    return false;
                }

                if(window.checkAccout==0){
	                window.checkAccountRepeat(account);
	                if(checkAccout==0){
	                	this.errmsg = "用户名已存在";
	                    return false;
	                }
            	}
                return true;
			},
			pwd:function(pwd){
				if (pwd == "") {
                    this.errmsg = "密码不能为空";
                    return false;
                }
                if (pwd.length < 6 || pwd.length > 16) {
                    this.errmsg = "输入6-16位密码";
                    return false;
                }
                return true;
			},
			confirmPwd:function(confirmPwd){
				if(confirmPwd!=$("input[name='register-pwd']").val()){
					this.errmsg = "重复输入密码不正确";
                    return false;
				}
				return true;
			}
		}
	}


	function checkData(data,type){
		var result,errmsg,error=0;
		$.each(data, function(k,e){
			if(typeof window.allowParam[type][k]=="function"){

				result=window.allowParam[type][k](e);

				var errormsgCon=$("input[name='"+type+"-"+k+"']").next(".errorMsg").find(".erro_msg_show");

				if(!result){
					errmsg=window.allowParam[type].errmsg;
					errormsgCon.find("b").text(errmsg);
					errormsgCon.show();
					error=1;
				}else{
					errormsgCon.hide();
				}
			}
		});

		if(error){
			return false;
		}
		return true;
	}

	function call_register(data){

		if(!$("#agreement").hasClass("selected")){
			myAlert("请同意《VRonline用户注册协议》","提示");
			return false;
		}

		kad.customEvent('reg');

		var w=window.open();

		$.post('/flash/api/register', data, function(data, textStatus, xhr) {
			/*optional stuff to do after success */
			if(textStatus!="success"){
				myAlert("提交失败，请重试","注册失败");
				w.close();
				return false;
			}
			if(data.code!=0){
				if(data.code == 1115) {
                    $(".reg_verify_container img").attr('src',data.data.img+"?w=84&h=38&v="+Math.random());
                    $(".reg_container").hide();
                    $(".reg_verify_container input").val('');
                    $(".reg_verify_container").show();
				} else {
					myAlert(data.msg,"注册失败");
				}
				w.close();
				return false;
			}

			kad.customEvent('regSuccess');

			var href="http://www.vronline.com/start/"+data.data.appid+"/"+data.data.serverid;
			w.location=href;

			var msg = {login:true};

			messenger.targets["flash"].send(JSON.stringify(msg));

		},"json");
	}

	function call_login(data){
		var w=window.open();
		$.post('/flash/api/login', data, function(data, textStatus, xhr) {
			/*optional stuff to do after success */
			if(textStatus!="success"){
				myAlert("提交失败，请重试","注册失败");
				w.close();
				return false;
			}
			if(data.code!=0){
				if(data.code == 1115) {
                    $(".login_verify_container img").attr('src',data.data.img+"?w=84&h=38&v="+Math.random());
                    $(".login_container").hide();
                    $(".login_verify_container input").val('');
                    $(".login_verify_container").show();
				} else {
					myAlert(data.msg,"注册失败");
				}
				w.close();
				return false;
			}
			var href="http://www.vronline.com/start/"+data.data.appid+"/"+data.data.serverid;
			w.location=href;
			var msg = {login:true};
			messenger.targets["flash"].send(JSON.stringify(msg));
		},"json");
	}

	function myAlert(content,title){
		var config = {
            headerMsg: title,
            msg: content,
            model: "tips"
        }

        tipsFn.init(config);
	}

	function geturl(type){
		var appid=vronlineTg.appid;
		var serverid=vronlineTg.serverid;
		var adid=vronlineTg.adid;

        var url="http://passport.vronline.com/auth/tg/"+type;
        url+="?appid="+appid;
        url+="&serverid="+serverid;
        url+="&adid="+adid;
        return url;
	}

	// qq登录
	$("#qqLogin").click(function() {
        var url=geturl("qq");
        window.open(url);
    });

    // 微信登录
    $("#wxLogin").click(function() {
    	var url=geturl("wx");
    	window.open(url);
    });

    // 微博登录
    $("#weiboLogin").click(function() {
    	var url=geturl("weibo");
        window.open(url);
    });

</script>
