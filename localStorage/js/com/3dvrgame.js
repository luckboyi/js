$(function(){
    $('.vr_playgame').on('click','.btn_con span.f26',function(){
        $('.add_game_mask ').show()
    });
    //引导页
    $('.vrgame_tips').on('click','.next_btn',function(){
        $(this).parents('.in_vrgame_tips').next('.in_vrgame_tips').addClass('cur').siblings('').removeClass('cur');
    })
    $('.vrgame_tips').on('click','.last_next_btn',function(){
        $(this).parents('.mask').hide();
    })
})