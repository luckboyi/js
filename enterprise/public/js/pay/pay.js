function creatHtmlFn(msg){
    var html;
    html = '<div class="mask_layer success_revamp_account new_window" style="display: block;">\
                <div class="popup_window" >\
                    <div class="popup_head clearfix pr hide">\
                        <h4 class="fl">提示</h4>\
                        <i class=" close pa"></i>\
                        </div>\
                        <div class="in_popup success_popup">'+msg+'</div>\
                    <div class="popup_btn success_popup_btn">\
                           <ul class="clearfix">\
                                <li class="fl sure">确定</li>\
                           </ul>\
                    </div>\
                </div>\
        </div>';
    $('body').append(html);
    $('.sure').on('click',function(){
        $('.new_window').detach();
    })
};
function successPayHtml(msg){
    var html;
    html ="<div class='mask_layer ali_window' style='display: block;'><div class='aliPay_window pr' style='border:0;background:#fff; position: absolute; top:50%;left: 50%;z-index: 999; transform: translate(-50%,-50%); width:450px; height:530px; padding:20px;'><i class='close pa'></i>" +
        "<iframe scrolling='no' s width='100%' height='100%'  frameborder='no' border='0' src='"+ msg +"'></iframe>" +
        "</div></div>";
    $('body').append(html);
    $('.mask_layer .close').on('click',function(){
        $('.ali_window').detach();
    });
};

//请求php地址
function ajaxFn(url,json,pay_method){
    
    $.ajax({
        url:url,
        type:'POST',
        dataType:'json',
        data:json,
        success:function(data){
            if(data.code != 0){
                creatHtmlFn(data.msg);
            }else{
                $('#frm_post').find('input[name="wp_pid"]').val(data.data.wp_pid);
                $('#frm_post').find('input[name="wp_uid"]').val(data.data.wp_uid);
                $('#frm_post').find('input[name="jump_url"]').val(data.data.jump_url);
                $('#frm_post').find('input[name="sign"]').val(data.data.sign);
                if(pay_method == 'ewm'){
                    payCallBack("http://pay3.xy.com/index.php?action=alipayscanvr&resource_id=1282160");
                }else if(pay_method=='wxpay'){
                    payCallBack("http://pay3.xy.com/index.php?action=wxshenzhoufumergevr&resource_id=1282160");
                }else if( pay_method =='pay_plantb'){
                    creatHtmlFn(data.msg);
                }else{
                    $('#frm_post').submit();
                }
            }
        },
        error:function(){
            creatHtmlFn('你的网络有问题，请关闭并返回重新充值！');
        }
    })
}
// //平台充值扫码支付请求
function payAlipay(pay_way,json,pay_method){
    switch (pay_way) {
        case 0:
            ajaxFn('http://pay.vronline.com/create/buyplantb',json,pay_method)
            break;
        case 1:
            ajaxFn('http://pay.vronline.com/create/buygame',json,pay_method)
            break;
        case 2:
            ajaxFn('http://pay.vronline.com/buy/buygame',json,'pay_plantb');
    }

}
//网页游戏充值

//回调
function payCallBack(url){
    var json = {};
    $("#frm_post").find('input').each(function(key){
        var fieldname = $(this).attr("name");
        var value = $(this).val()==null?"":$(this).val();
        json[fieldname] = value;
    });
    $.ajax({
        url:url,
        type:"POST",
        dataType:"json",
        data:json,
        success:function(data){
            var state = data.ret;
            if(state == 0){
                successPayHtml(data.msg);
            }else{
                creatHtmlFn(data.msg);
            }
        },
        error:function(){
            creatHtmlFn('你的网络有问题，请关闭并返回重新充值！');
        }
    })
};




// 充值的方法
function payFn(payMethod,json){
    switch (payMethod){
        case 0:
            break;
        case 1:
            break;
        case 2:
            break;
        case 3:
            break;
    }
};

