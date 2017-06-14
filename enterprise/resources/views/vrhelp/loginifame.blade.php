<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="//pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
</head>
<body>
<script type="text/javascript">
window.addEventListener('message', function(event){
   if(typeof(event.data)=="object") {
   	var res = event.data
   	if(typeof(res.tp)!="undefined") {
   		switch (res.tp) {
   			case "login":
   				login(res.data)
   			break;
   		}
   	}
   }
}, false);

function login(obj){
	if(obj.client=="common") {

		$.post("/api/login",{name:obj.account,pwd:obj.pwd,remember:1,code:'',client:obj.client},function(res){
			if(res.code==0) {
        if(obj.keeploginstate==1) {
           res.data.save = true
           res.data.pwd = obj.pwd
        }
				window.parent.postMessage({tp:"login_ok",data:res.data},'*');
			} else {
				window.parent.postMessage({tp:"login_err",data:{msg:res.msg}},'*');
			}
		},"json");
	} else {
    var login_json = {account:obj.mobile,sms:obj.sms}
    $.post("http://passport.vronline.com/user/login/sms",{json:JSON.stringify(login_json)},function(res){
      if(res.code==0) {
        window.parent.postMessage({tp:"login_ok",data:res.data},'*');
      } else {
        window.parent.postMessage({tp:"login_err",data:{msg:res.msg}},'*');
      }
    },"json");
	}
}
</script>
</body>
</html>
