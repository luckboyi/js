$(function(){
    //点击右侧设置
    $('.plateform_btn').on('click','li',function(){
        // alert(1);
        if($(this).hasClass('windowclose_icon')){
            PL.callFun('mainframe', 'close','');
        }else if($(this).hasClass('max_icon')){
            PL.callFun('common', 'max','');

        }else if($(this).hasClass('min_icon ')){
            PL.callFun('common', 'min','');
        }else if($(this).hasClass('vrtools_icon ')){
            PL.callFun('mainframe', 'showhidetool', '');
        }else if($(this).hasClass('vrbtn_icon')){
            PL.callFun('mainframe', 'vrBtn','');
        }
    });
    $('.left_plateform_btn').on('click','',function(){

    });

    $(".search_text").keypress(function() {
        if(event.keyCode==13) {
            search($(this).next())
        }
    });
    $('.back_icon').click(function(){
         window.history.back();
    })
    $('.refresh_icon').click(function(){
       window.location.reload();
    })
    //点击搜索
    $('.header ').on('click','.search_icon',function(){
        search($(this))
    });
    $('.search_con').on('click','span',function(e){
        $('.search_con').find('ul').slideToggle(100);
        e.stopPropagation()
        e.preventDefault()
    });
    $('.search_con').on('click','li',function(){
        $(this).parents('ul').slideUp(100);
        var txt = $(this).html();
        $('.search_con').find('span b').text(txt);
        $(this).addClass('cur').siblings().removeClass('cur');
    })
    $(window).on('click',function(){
        $('.search_con').find('ul').slideUp(100);
        $('.plateform_btn .set_icon').find('ol').hide();        
    });
    //设置
    $('.close_mask').on('click',function(){
        $(this).parents('.mask').hide();
    })
    $('.plateform_btn').on('click','.set_icon',function(e){
        $(this).find('ol').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
    
    $('.plateform_btn').on('click','ol li',function(){
        var i = $(this).index();
        //console.log(i)
        if(i == 0){
            $('.set_mask').show();
        }else if(i ==1){
            $('.update_platform').show();
            $('.suc_update').show();
        }else if(i ==2){
            $('.add_game').show();
        }else if(i == 3){
            $('.aboutUs_con').show();
        }
    })
    $('.set_language_con').on('click',function(e){
        $('.language_setlist ').toggle();
        e.stopPropagation()
        e.preventDefault()
    })
    $('.language_setlist').on('click','p',function(e){
        var text = $(this).html();
        $('.set_language_con').find('b').html(text);
        $(this).parents('.language_setlist').hide();
    })
    $('.set_mask .set_head').on('click','span',function(){
        var i = $(this).index();
        $(this).addClass('cur').siblings().removeClass('cur');
        $('.set_mask').find('.set_body ').eq(i).addClass('cur').siblings().removeClass('cur');
        if($('.set_nav').find('.vr_game_list.cur').hasClass('wow')){
            $('.set_mask').find('.set_body .wow_set_con').show();
            $('.set_mask').find('.set_body .ow_set_con').hide()
        }else{
            $('.set_mask').find('.set_body .wow_set_con').hide();
            $('.set_mask').find('.set_body .ow_set_con').show()
        }
    });
    $('.set_mask').on('click','.vr_game_list',function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        $(this).parents('li').addClass('cur').siblings().removeClass('cur');
        if($(this).hasClass('wow')){
             $('.set_mask').find('.set_body .wow_set_con').show();
            $('.set_mask').find('.set_body .ow_set_con').hide()
        }else{
            $('.set_mask').find('.set_body .wow_set_con').hide();
            $('.set_mask').find('.set_body .ow_set_con').show()
        }
    });
    $('.set_nav').on('click','li',function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        var i = $(this).index();
        $(this).parents('.set_mask').find('ol li').eq(i).addClass('cur').siblings().removeClass('cur');    
    })

})

function setDateFn(data){
    $(data).each(function(key,val){
        if(val.languageState == 1033){
            $('.set_language_con b').html('英文')
        }else{
            $('.set_language_con b').html('中文')
        };
        if(val.serviceState == 1){
            $('.equipmentauto_set').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.equipmentauto_set').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        if(val.autoState == 1){
            $('.close_btn_set').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.close_btn_set').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        if(val.openVronline == 1){
            $('.openVRonline_set').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.openVRonline_set').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        if(val.closeplatselected == 1){
            $('.close_plateform').find('.slect_con').eq(0).addClass('cur').siblings().removeClass('cur');
        }else{
            $('.close_plateform').find('.slect_con').eq(1).addClass('cur').siblings().removeClass('cur');
        };
        $('.setway_con input').val(val.dir)
    });   
    $('.set_mask ').show();
}

function search(obj) {
    var val = obj.prev('input').val();
    var typeid = obj.parents('.search_con').find('ul li.cur').attr('typeid');
    if(val !=''){
        if(typeid == 1){
            $('#main').attr('src','http://www.vronline.com/vrhelp/searchGame?name='+val+'&page=1')
        }else if(typeid == 2){
            $('#main').attr('src','http://www.vronline.com/vrhelp/searchVideo?name='+val+'&page=1')
        }
    }else{
    }   
}
