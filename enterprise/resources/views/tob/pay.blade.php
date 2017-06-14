<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>{{ $merchantInfo['merchant'] }}</title>
    @if($payTp=="alipay1")
    <link rel="stylesheet" href="/weui/alipay.css"/>
    @else
    <link rel="stylesheet" href="/weui/weui.css"/>
    @endif
    <script src="/weui/zepto.min.js"></script>
    <script src="/weui/weui.min.js"></script>
</head>
<body ontouchstart>
<div class="container" id="container">
  <div class="page product-list" >
    <div class="weui-cells__title ">{{ $merchantInfo['merchant'] }}</div>
    <div class="weui-cells weui-cells_checkbox">
            @foreach($products as $product)
            <label class="weui-cell weui-check__label">
                <div class="weui-cell__hd">
                    <input type="radio" class="weui-check" name="product" value="{{ $product['id'] }}" @if($product['checked']==1) checked="true" @endif>
                    <i class="weui-icon-checked"></i>
                </div>
                <div class="weui-cell__bd">
                    <p>{{ $product['title'] }} </p>
                </div>
            </label>
            @endforeach
    </div>
    <div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" href="javascript:pay()" >支付</a>
    </div>
  </div>
</div>

<div class="page msg_error" style="display:none">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">支付失败</h2>
            <p class="weui-msg__desc"></p>
        </div>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <a href="#" class="weui-btn weui-btn_primary">重新查询</a>
            <a href="javascript:back()" class="weui-btn weui-btn_default">返回</a>
        </p>
    </div>
</div>


<div class="page msg_success" style="display:none">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">支付成功</h2>
            <p class="weui-msg__desc"></p>
        </div>
        <div class="weui-progress">
            <div class="weui-progress__bar">
                <div class="weui-progress__inner-bar js_progress" style="width:0%;"></div>
            </div>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="#" class="weui-btn weui-btn_primary"  id="btnUpload">启动游戏</a>
                <a href="javascript:back()" class="weui-btn weui-btn_default">返回</a>
            </p>
        </div>

    </div>
</div>


<div class="page footer js_show">
    <div class="page__bd page__bd_spacing">
        <div class="weui-footer weui-footer_fixed-bottom">
            <p class="weui-footer__links">
                <a href="http://www.vronline.com" class="weui-footer__link">VR助手提供技术支持</a>
            </p>
            <p class="weui-footer__text">Copyright © 2016-2017 vronline.com</p>
        </div>
    </div>
</div>

 <div id="toast" style="opacity: 0; display: none;">
      <div class="weui-mask_transparent"></div>
      <div class="weui-toast">
          <i class="weui-icon-success-no-circle weui-icon_toast"></i>
          <p class="weui-toast__content">已成功启动</p>
      </div>
  </div>


  <div id="loadingToast" style="display:none;">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast">
        <i class="weui-loading weui-icon_toast"></i>
        <p class="weui-toast__content loading-content">支付中</p>
    </div>
  </div>

<iframe name="frm_dealer" style="display:none"></iframe>
<form style="display:none" action="http://dev3.pay.xy.com/index.php?resource_id=1300160&action=" id="frm_post" method="post" accept-charset="utf-8" target="_blank"> 
  <div style="display:none"> 
  <input type="hidden" name="uid" value="" /> 
  <input type="hidden" name="sid" value="" /> 
  <input type="hidden" name="jump_url" value="" /> 
  <input type="hidden" name="game_type" value="" /> 
  <input type="hidden" name="wp_pid" value="" /> 
  <input type="hidden" name="wp_uid" value="" /> 
  <input type="hidden" name="pay_rmb" value="" /> 
  <input type="hidden" name="action" value="" /> 
  <input type="hidden" name="sign" value="" /> 
  <input type="hidden" name="user_ip" value="" /> 
  <input type="hidden" name="product_id" value="" /> 
  </div>
</form>
<script type="text/javascript">
var lock = false;
var timeout = 0;
var products = {!! $jsonStr !!};
var toastLoading = $('#loadingToast');
var payTp = "{{ $payTp }}";
var payAction;
var baseUrl = "http://test3.vronline.com";
var merchantid = '{{ $merchantid }}';
var appid = '{{ $appid }}';
var terminal_sn = '{{ $terminal_sn }}';
var product_id;
var selectProduct = '{{ $selectProduct }}';
$(function(){
  var $progress = $('.js_progress'),
        $btnUpload = $('#btnUpload');
    var progress = 0;

    function next() {
        if(progress > 100){
            progress = 0;
            tips();
            $btnUpload.removeClass('weui-btn_disabled');
            return;
        }
        $progress.css({width: progress + '%'});
        progress = ++progress;
        setTimeout(next, 20);

    }
    $btnUpload.on('click', function(){
        if ($btnUpload.hasClass('weui-btn_disabled')) return;

        $btnUpload.addClass('weui-btn_disabled');
        next();
    });
    if(selectProduct!='') {
      loadok();
    }
})

function pay() {
  var target;
  if(payTp == "alipay") {
    payAction = 'alipayh5vr';
    target = "frm_dealer";
  } else {
    payAction = 'wechath5vr';
     target = "_blank";
  }
  product_id = $('input[name="product"]:checked').val();
  var product = products[product_id];
  var params = JSON.stringify({uid:merchantid,game_type:appid,wp_uid:terminal_sn,pay_rmb:product.price,action:payAction,product_id:product_id});
  $.post(baseUrl+'/create/create2bOrder',{params:params},function(res){
    $("input[name=uid]").val(res.data.uid);
    $("input[name=sid]").val(res.data.sid);
    $("input[name=jump_url]").val(res.data.jump_url);
    $("input[name=game_type]").val(appid);
    $("input[name=wp_pid]").val(res.data.wp_pid);
    $("input[name=wp_uid]").val(res.data.wp_uid);
    $("input[name=pay_rmb]").val(product.price);
    $("input[name=sign]").val(res.data.sign);
    $("input[name=action]").val(payAction);
    $("input[name=product_id]").val(product_id);
    $("input[name=user_ip]").val(res.data.user_ip);

    $("#frm_post").attr('action',$("#frm_post").attr('action')+payAction);
    $("#frm_post").attr('target',target);
    $("#frm_post").submit();
    if (toastLoading.css('display') != 'none') return;
    toastLoading.fadeIn(100);
    timeout = 0;
    lock = true;
    queryOrder(res.data.orderid);
  },"json");
}

function tips() {
    var $toast = $('#toast');
    if ($toast.css('display') != 'none') return;
    $toast.fadeIn(100);
    setTimeout(function () {
        $toast.fadeOut(100);
    }, 2000);
}

function payok() {
  if(lock==false) {
    return
  }
  lock = false;
  toastLoading.fadeOut(100);
  $(".page.product-list").hide();
  $(".page.msg_success").show();
  $(".weui-msg__desc").html("您已成功支付"+products[product_id].title);
}


function loadok() {
  $(".page.product-list").hide();
  $(".page.msg_success").show();
  $(".weui-msg__desc").html("您已成功支付"+products[selectProduct].title);
}

function payerr() {
  if(lock==false) {
    return
  }
  lock = false;
  toastLoading.fadeOut(100);
  $(".page.product-list").hide();
  $(".page.msg_error").show();
  $(".weui-msg__desc").html(products[product_id].title+"支付失败");
}

function back() {
  $(".js_progress").css("width","0%");
  $(".page.product-list").show();
  $(".page.msg_success").hide();
  $(".page.msg_error").hide();
}

function queryOrder(orderId) {
  console.log(timeout)
  if(lock==false) {
    return
  }
  if(timeout>30000) {
    payerr()
    return
  }
  setTimeout(function() {
    $.get(baseUrl+'/result2b/'+orderId,function(res) {
      timeout += 1000;
      if(res.code==0) {
        payok();
      } else if (res.code==2) {
        queryOrder(orderId);
      } else if(res.code==3) {
        payerr();
      } else {
        payerr();
      }
    },"json");
  },1000)
}
</script>
</body>
</html>