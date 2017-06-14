<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="http://pic.vronline.com/common/style/base.css">
    <link rel="stylesheet" href="http://pic.vronline.com/client/css/client.css">
    <script src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>

</head>
<body>
<!--已经反馈成功-->
<div class="mask success_bounced_msg" id="divSuccess">
    <div class="suc_bounced_con">
        <div class="bounced_head pr ">
            <h3 class=" language" data-name="success_bounced"></h3>
            <p class="close_btn "></p>
        </div>
        <div class="bounced_body">
            <p class="language" data-name="suc_bounced">谢谢您提的宝贵意见,我们会尽快跟进解决！</p>
        </div>
        <div class="bounced_foot tac">
            <ul class="clearfix tac">
                <li class="fl language cancel" data-name="sure" id="btnSuccess">确定</li>
            </ul>
        </div>
    </div>
</div>
<!--意见反馈S-->
<div class="mask bounced_advice show" id="divAdvice">
    <div class="bounced ">
        <div class="advice">
            <div class="advice_head ">
                <h3 class=" appendCon" data-name="manage_advice"></h3>
                <div class="close_btn"></div>
            </div>
            <div class="bounced_con">
                <div class="addGame-body clearfix">
                    <p class="language" data-name="your_problem"></p>
                    <textarea id="content"></textarea>
                    <p class="language" data-name="contact_way"></p>
                    <div class="clearfix advice_write">
                        <input id="mobile" class="fl" type="text" placeholder="请输入手机号" name="phoneNum">
                        <input id="qq" class="fl" type="text" placeholder="请输入QQ号" name="">
                        <span class="erroColor">手机号或qq号必须填写一项，以便及时向你反馈处理信息！</span>
                    </div>
                    <p class="language" data-name="upload_photo"></p>
                    <div class="advice_img clearfix">
                        <div class="pr fl">
                            <p class="close_btn"></p>
                            <input type="file"  onchange="previewFile1()" class="file1">
                            <img src="" class="image1">
                        </div>
                        <div class="pr fl">
                            <p class="close_btn"></p>
                            <input type="file"  onchange="previewFile2()"  class="file2">
                            <img src="" class="image2">
                        </div>
                        <div class="pr fl">
                            <p class="close_btn"></p>
                            <input type="file"  onchange="previewFile3()"  class="file3">
                            <img src="" class="image3">
                        </div>
                        <div class="pr fl">
                            <p class="close_btn"></p>
                            <input type="file"  onchange="previewFile4()"  class="file4">
                            <img src="" class="image4">
                        </div>
                        <div class="pr fl">
                            <p class="close_btn"></p>
                            <input type="file"  onchange="previewFile5()"  class="file5">
                            <img src="" class="image5">
                        </div>
                    </div>
                </div>
                <div class="in-foot clearfix">
                    <div class="cancel fr cn language" data-name="cancel" id="btnCencel">取消</div>
                    <div class="browse fr cn language" data-name="sure" id="btnOk">确定</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--意见反馈E-->
</body>
</html>
<script>
    function previewFile1(){
        var preview = document.querySelector('img.image1');
        var file  = document.querySelector('input.file1').files[0];
        var reader = new FileReader();
        var url =$('.image1').attr('src') ;
        reader.onloadend = function () {
            preview.src = reader.result;
            $('.image1').show();
            $('.image1').prevAll().show();
        };
        if(file){
            reader.readAsDataURL(file);
        }else{
            preview.src = '';
        }
        // var
    }
    function previewFile2(){
        var preview = document.querySelector('img.image2');
        var url =$('.image2').attr('src') ;
        var file  = document.querySelector('input.file2').files[0];
        var reader = new FileReader();
        reader.onloadend = function () {
            preview.src = reader.result;
            $('.image2').show();
            $('.image2').prevAll().show();
        };
        if(file){
            reader.readAsDataURL(file);
        }else{
            preview.src = '';
        }
    }
    function previewFile3(){
        var preview = document.querySelector('img.image3');
        var file  = document.querySelector('input.file3').files[0];
        var url =$('.image3').attr('src') ;
        var reader = new FileReader();
        reader.onloadend = function () {
            preview.src = reader.result;
            $('.image3').show();
            $('.image3').prevAll().show();
        };
        if(file){
            reader.readAsDataURL(file);
        }else{
            preview.src = '';
        }
    }
    function previewFile4(){
        var preview = document.querySelector('img.image4');
        var file  = document.querySelector('input.file4').files[0];
        var reader = new FileReader();
        var url =$('.image4').attr('src') ;
        reader.onloadend = function () {
            preview.src = reader.result;
            $('.image4').show();
            $('.image4').prevAll().show();
        };
        if(file){
            reader.readAsDataURL(file);
        }else{
            preview.src = '';
        }
    }
    function previewFile5(){
        var preview = document.querySelector('img.image5');
        var file  = document.querySelector('input.file5').files[0];
        var reader = new FileReader();
        var url =$('.image5').attr('src') ;
        $('.image5').show();
        $('.image5').prevAll().show();
        reader.onloadend = function () {
            preview.src = reader.result;
        };
        if(file){
            reader.readAsDataURL(file);
        }else{
            preview.src = '';
        }
    }
    //删除已上传的图片
    $(function(){
        function imgFileHide(obj){
            obj.hide();
            obj.parent('div.fl').find('img').attr('src','');
            obj.parent('div.fl').find('img').hide();
        }
        $('.close_btn').on('click',function(){
            imgFileHide($(this))
        });
        //点击确定
        /*$('.bounced_advice').find('.in-foot .browse').on('click',function(){
            //alert(1);
            var json ={
                uId:'',
                txt:'',
                phoneNum:'',
                qq:'',
                src:[]
            };
            var uId = $('.header .user').attr('u-id');
            var txt = $('.bounced_advice').find('textarea').val();
            var phoneNum =$('.bounced_advice').find('input[name="phoneNum"]').val();
            var qq = $('.bounced_advice').find('input[name="qq"]').val();
            var arr = new Array();
            var src1 = $('.bounced_advice').find('.image1').attr('src');
            var src2 = $('.bounced_advice').find('.image2').attr('src');
            var src3 = $('.bounced_advice').find('.image3').attr('src');
            var src4 = $('.bounced_advice').find('.image4').attr('src');
            var src5 = $('.bounced_advice').find('.image5').attr('src');
            arr[0] = src1 ;
            arr[1] = src2;
            arr[2] = src3;
            arr[3] =src4;
            arr[4] = src5;
            json.uId = uId;
            json.txt = txt;
            json.phoneNum = phoneNum;
            json.qq = qq;
            json.src = arr;
            //feedback(json);
        });*/
        /*$('.success_bounced_msg').on('click','.close_btn ',function(){
            $(this).parents('.success_bounced_msg').hide();
        });*/
        /*$('.success_bounced_msg').on('click','.sure ',function(){
            $("#divSuccess").removeClass('show');
        });*/
		$("#btnSuccess").click( function () { 
			$("#divSuccess").removeClass('show'); 
		});

		// 点击意见反馈取消按钮
		$("#btnCencel").click( function () { 
			$("#divAdvice").removeClass('show'); 
		});
    })


	// 点击确认按钮，判断事件
	$("#btnOk").click( function () { 
		var flag = validate();
		if (!flag) {
			return false;
		}

		var urls=[];  // 图片集合

		$("img").each(function(i){
			var src = $("img:eq(" + i + ")").attr('src');
			if (src.length > 0) {
				urls.push(src);
			};
		})
		var content = $("#content").val();
		var mobile = $("#mobile").val();
		var qq = $("#qq").val();

		// 提交意见反馈
		$.ajax({
	        type: 'post',
	        url: '/ajax/adviceAjax',
	        data: { content : content,mobile : mobile,qq : qq,urls : urls},
	        dataType: 'json',
	        headers: {  // header属性，是为了避免跨站伪造请求攻击写的
	        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
	        },
	        success: function(data){
	            if (data.code == 0) {
	               	//显示提交成功框
	               	$("#divAdvice").removeClass('show');
	               	$("#divSuccess").addClass('show');


	            }else{
	                alert(data.msg)

	            }
	        },
	        error: function(xhr, type){
	        }
	    });


	});

	function validate(){

		var content = $("#content").val();
		var mobile = $("#mobile").val();
		var qq = $("#qq").val();

		if (content.length == 0) {
        	$(".erroColor").text('字数不能低于15个字');
            return false;
        }
        if (mobile.length == 0 && qq.length == 0) {
        	$(".erroColor").text('手机和qq必须留一个');
        	return false;
        }

        return true;
	}

</script>