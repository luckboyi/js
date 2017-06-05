$(function(){
    $('body').on('click','.download_nav li',function(){
        $(this).addClass('cur').siblings().removeClass('cur');
        var i = $(this).index();
        $('.in_download_body').find('ol').eq(i).addClass('cur').siblings().removeClass('cur');
    })
})