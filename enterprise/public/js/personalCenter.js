$(function() {
    resizeFn();
    $(window).resize(function() {
        resizeFn();
    });

    //hover-tip
    $('.user_con .icon i').hover(function() {
        $(this).parents('.icon').find('p').show()
    }, function() {
        $(this).parents('.icon').find('p').hide()
    });

    //点击左侧进入
    $('.personal_center .left_per ul').on('click', 'li.pr', function() {
        resizeFn();
        var i = $(this).index();
        $(this).addClass('cur').siblings().removeClass('cur');
        $('.right_per li.list_con').eq(i).addClass('cur').siblings().removeClass('cur');
    });
    //点击充值
    $('.personal_center .left_per ul').on('click', 'li.charge a', function() {
        resizeFn();
        $(this).parents('li.charge').find('ol').toggle();
        $(this).parents('li.charge').find('i').toggleClass('cur');
    });
    //点击支付列表
    $('.personal_center .left_per  ol').on('click', 'li', function() {
        resizeFn();
        $(this).parents('ol').show();
        $(this).addClass('cur').siblings().removeClass('cur')
    });

    //点击充值中心 下拉
    $('.personal_center .left_per li.charge i').on('click', function() {
        resizeFn();
        $(this).toggleClass('cur');
        if ($(this).hasClass('cur')) {
            $(this).parents('li.charge').find('ol li').addClass('cur');
        }
        $(this).hasClass('cur') ? $(this).parents('li.charge').find('ol').show() : $(this).parents('li.charge').find('ol').hide();
    });


    $('.cancel').on('click', function() {
        $(this).parents('.mask_layer').hide();
        var imgDate = {};
        imgDate.w = $('.wl').find('#small').width();
        imgDate.h = $('.wl').find('#small').height();
        imgDate.l = $('.wl').find('#small').position().left;
        imgDate.t = $('.wl').find('#small').position().top;
        //console.dir(imgDate);
    });
    //点击保存

    //点击绑定成功确定或者取消关闭
    $('.bind_phoneNum .popup_btn').on('click', 'li', function() {
        if ($(this).hasClass('sure')) {
            $('.success_popup_window').show();
            $(this).parents('.bind_phoneNum').hide()
        }
    });
    //点击绑定成功确定按钮
    $('.success_popup_window .sure').on('click', function() {
        $(this).parents('.success_popup_window').hide()
    });

    //修改平台账号
    $('.revamp_btn').on('click', function() {
        $('.revamp_account').show()
    });
    //点击修改
    $('.revamp_account .sure').on('click', function() {
        $('.revamp_account').hide();
        $('.sure_revamp_account').show()
    });
    //点击确定
    $('.sure_revamp_account .sure').on('click', function() {
        $('.sure_revamp_account').hide();
    });

});

//图片预览
function previewHeadFile() {
    var preview = document.querySelector('.pre-1 img');
    var preview2 = document.querySelector('.jc-demo-box img');
    var preview3 = document.querySelector('.pre-2 img');
    var file = document.querySelector('input#file').files[0];
    var reader = new FileReader();
    reader.onloadend = function() {
        preview.src = reader.result;
        preview2.src = reader.result;
        preview3.src = reader.result;
        $('.modify_head_portrait .popup_btn .cancel').text('保存');
    };
    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
    }
}

function resizeFn() {
    var winHeight;
    if (window.innerHeight) {
        winHeight = window.innerHeight;
    } else if ((document.body) && (document.body.clientHeight)) {
        winHeight = document.body.clientHeight;
    }
    $('.personal_center_height').height(winHeight);
    $('#personal_center_scroll').tinyscrollbar();
};