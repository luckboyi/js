 $(function(){
        //左侧列表
        //点击头像的下拉
        $('.user_name_con').on('click','p',function(){
            $(this).find('.triangle').toggleClass('cur');
            $(this).parents('.user_name_con').find('ul').toggle();
        })
        //点击左侧选项
        $('.left_item_nav').on('click','li.pr',function(){
            var i = $(this).index();
            $(this).addClass('cur').siblings().removeClass('cur');
        });
        //点击收缩
        $('body').on('click','.left_item_nav h3',function(){
            $(this).find('.white_down_icon').toggleClass('white_up_icon');
            $(this).next('ol').toggle();
        });

        //点击弹框关闭
        $('body').on('click','.mask   .close_mask',function(){
            $(this).parents('.mask').hide();
           //alert(2)
        })
 })
 