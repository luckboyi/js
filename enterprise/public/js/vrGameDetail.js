(function(){
  var vrGameDetail = {
      config:{
        uid:0,
      },
      parameter:{
        url:'http://image.vronline.com',
        like:1,
      },
      init:function(config){
        this.config = $.extend({},this.config,config);
        var p =this;
      },
      //检查详情页
      checkDetail:function(tp,id){
        this.id = id;
        this.showHtml();
        
        this.commentInit(id);
        this.getData(tp,id);
        window._bd_share_config = {
          "common": {
              "bdSnsKey": {},
              "bdText": "",
              "bdMini": "2",
              "bdMiniList": false,
              "bdPic": "",
              "bdStyle": "0",
              "bdSize": "24"
          },
          "share": {},
          "selectShare": {
              "bdContainerClass": null,
              "bdSelectMiniList": ["qzone", "tsina", "tqq", "weixin"]
          }
      };
      with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];

      },
      commentInit:function(id){
        // 评论
        Comment.init({
            userid:this.config.uid,
            target_id:id,
            type:'client_vrgame',
        });
      },
      //获取数据
      getData:function(tp,id){
        var p = this;
        $.post('/vrhelp/gameDetail/'+id,{},function(data){
            clientCall('gamelistframe', 'item_clicked', {game_id:id,logo:static_image(data.data.img.rank)});
            p.updateDetail(data.data);
            //猜你喜欢
            $.post('/vrhelp/search',{tp:'vrgame',category:data.data.class.id},function(like){
                p.updateLike(like.data);
            })
        })
        //大家都在玩
        $.post('/vrhelp/search',{tp:'vrgame',sort:'play up'},function(top){
            p.updateTop(top.data);
        })
      },
      updateDownLoad(obj) {
        if(obj.gameid != this.id) {
          return ;
        } else {

          if(typeof(this.game_download_status)!="undefined" && this.game_download_status.buy==0) {
            return ;
          } else {
            if(typeof(obj.fun)=="undefined") {
              return ;
            }
            switch(obj.fun) {
              case "unstall":
                $('.install_game').show().siblings().hide();
              break;
              case "install":
                $('.play_game').show().siblings().hide();
              break;
              case "downloading":
                $('.down_progress_con').show().siblings().hide();
              break;
              case "pause":
                $('.down_progress_con').show().siblings().hide();
              break;
              case "starting":
              case "started":
              case "verchecking":
              case "update":
                $('.play_game').show().siblings().hide();
              break;
            }
            this.game_download_status = obj
            this.game_download_status.buy = 1
            this.game_download_status.vrmode = 1;
          }
        }
      },
      tickGame() {
        if(typeof(this.game_download_status)=="undefined") {
          return ;
        }
       switch(this.game_download_status.fun) {
            case "unstall":
            case "downloading":
            case "pause":
               leftCall('gameframe','gamefuncclicked',{game_id:this.id,vrmode:this.game_download_status.vrmode,state:this.game_download_status.state,action:'install'})
            break;
            case "install":
            case "starting":
            case "started":
              console.log("gameing");
               //disable
            break;
        }
      },
      updateTop:function(top){
        html='';
        var p = this;
        $(top.data).each(function(k,v){
          if(k<2){
            html += '<li class="clearfix">'+
                '<div class="img_con fl" style="background:url(\''+p.parameter.url+'/'+v.image.rank+'\');"></div>'+
                '<div class="fr msg_con">'+
                    '<p class="els">'+v.name+'</p>'+
                    '<p class="els">已有多人在玩</p>'+
                '</div>'+
            '</li>';
          }
        })
        $('.all_play ul').html(html);
      },
      updateLike:function(like){
          html='';
          var p = this;
          $(like.data).each(function(k,v){
            if(k<16){
              html+='<li class="pr game_detail page'+Math.floor(k/4+1)+'" data-val="'+v.id+'" data-page="'+Math.floor(k/4+1)+'" style="background-image:url(\''+p.parameter.url+'/'+v.image.logo+'\');">'+
                        '<div class="msg_con pa">'+
                            '<div class="fl els">'+v.name+'</div>'+
                            '<div class="fr els tar">下载量:'+v.play+'</div>'+
                        '</div>'+
                    '</li>';
            }
          })
          // console.log(html);
          $('.recommend_game ul').html(html);
          $('.recommend_game ul li').hide();
          $('.recommend_game ul .page1').show();

          //换一批翻页
          $('.recommend_game h4 span').click(function(){
            obj = this;
            $(obj).find('i').addClass('resh_con_trans');
            setTimeout(function () {
              page = p.parameter.like;
              n_page = parseInt(page)+1;
              $(obj).parent().parent().find('ul li').hide();
              $(obj).parent().parent().find('ul .page'+n_page).show();
              p.parameter.like = n_page%4;
              $(obj).find('i').removeClass('resh_con_trans');
            }, 600);
          })
      },
      //更新弹窗内容
      updateDetail:function(data){
        // console.log(data);
        var imgUrl='//image.vronline.com/';
        //更新文字内容
        $('.detail_msg .img_con').css('background','url('+imgUrl+data.img.rank+') no-repeat');
        $('.detail_msg .msg_con h4').text(data.name);
        $('.detail_msg .game_language .fl b').text(data.product_com);
        $('.detail_msg .game_language .fr b').text(data.lang);
        if(data.class.name!=undefined) {
           $('.detail_msg .game_type_con .fr b').text(data.class.name);
        }

        $('.detail_msg .game_type_con .fl b').text(data.issuing_com);
        $('.detail_msg .game_infor span b').text(data.content);

        //拼接轮播图
        bd = '';
        $.each(data.img.slider,function(k,v){
          // console.log(v);
          bd += '<li><a target="_blank" href="#"><img src="'+imgUrl+v+'" /></a></li>';
        })
        $('.picFocus .bd ul').html(bd);

        hd = '';
        $.each(data.img.slider,function(k,v){
          // console.log(v);
          hd += '<li><img src="'+imgUrl+v+'" /></li>';
        })
        $('.picFocus .hd ul').html(hd);

        //拼接设备和配件
        equipment_con = '';
        $.each(data.equipment,function(k,v){
          equipment_con += '<li class="fl">'+v.name+'</li>';
        })
        game_equipment = '';
        $.each(data.accessories,function(k,v){
          game_equipment += '<li class="fl">'+v.name+'</li>';
        })
        $('.equipment_con ul').html(equipment_con);
        $('.game_equipment ul').html(game_equipment);

        //推荐配置
        mini_device=JSON.parse(data.mini_device);
        mini = '';
        $.each(mini_device,function(k,v){
          mini += '<p class="clearfix"><span class="fl els">'+k+'</span><span class="fr els">'+v+'</span></p>';
        })
        recomm_device=JSON.parse(data.recomm_device);
        recomm = '';
        $.each(recomm_device,function(k,v){
          recomm += '<p class="clearfix"><span class="fl els">'+k+'</span><span class="fr els">'+v+'</span></p>';
        })
        $(".play_num").text(data.play)
        $(".game_size").text('大小: '+data.size)

        if(data.isbuy==0) {
           if(data.sell>0) {
             $(".buy_game").show();
             $(".game_price").text('￥'.data.sell);
             this.game_download_status = {buy:0};
           } else {
             $(".install_game").show().siblings().hide();
           }
        } else {
          $(".install_game").show().siblings().hide();
        }
        $('.config_con ol li:first').html(mini);
        $('.config_con ol li:last').html(recomm);
        $('.game-modal').show();
        $('body').addClass('dimmed');
        this.htmlJS();
      },
      hideHtml:function(){
        // console.log('hideHtml');
        $('.game-modal').remove();

        $('body').removeClass('dimmed');
      },
      showHtml:function(){
        if($('.game-modal').length==0){
          $('body').append('<div class="detail_con mask  pr  game-modal">'+
          '<div class="in_detail_con pa pr clearfix">'+
              '<div class="icon blue_close pa cp"></div>'+
              '<div class="in_detail">'+
                  '<div class="left_detail fl">'+
                      '<div class="left_detail_con ">'+
                          '<div class="detail_msg clearfix">'+
                              '<div class="img_con fl" style=""></div>'+
                              '<div class="fr msg_con">'+
                                  '<h4 class="f22">蝙蝠侠：阿卡姆VR</h4>'+
                                  '<p class="game_language clearfix">'+
                                      '<span class="fl els">制作公司:<b></b></span>'+
                                      '<span class="fr els">游戏语言:<b>英文</b></span>'+
                                  '</p>'+
                                  '<p class="game_type_con clearfix">'+
                                      '<span class="fl els">发行公司:<b></b></span>'+
                                      '<span class="fr els">游戏类型：<b>未知</b></span>'+
                                  '</p>'+
                                  '<p class="game_infor clearfix">'+
                                      '<span class="fl els">游戏简介:<b></b></span>'+
                                  '</p>'+
                                  '<div class="equipment_con clearfix tac">'+
                                      '<b class="fl">硬件设备：</b>'+
                                      '<ul class="els clearfix fl">'+
                                          '<li class="fl">ocluegrift</li>'+
                                          '<li class="fl">大朋E3</li>'+
                                          '<li class="fl">ocluegrift</li>'+
                                          '<li class="fl">ocluegrift</li>'+
                                          '<li class="fl">ocluegrift</li>'+
                                      '</ul>'+
                                  '</div>'+
                                  '<div class="game_equipment clearfix tac">'+
                                      '<b class="fl">游戏配件：</b>'+
                                      '<ul class="els clearfix fl">'+
                                          '<li class="fl">ocluegrift</li>'+
                                          '<li class="fl">ocluegrift</li>'+
                                          '<li class="fl">ocluegrift</li>'+
                                          '<li class="fl">ocluegrift</li>'+
                                      '</ul>'+
                                  '</div>'+
                              '</div>'+
                          '</div>'+
                      '</div>'+
                      '<div class="detail_img_con">'+
                          '<div class="picFocus pr">'+
                              '<div class="bd">'+
                                  '<ul>'+
                                  '</ul>'+
                              '</div>'+
                              '<div class="hd pa">'+
                                  '<a class="prev icon" href="javascript:void(0)"></a>'+
                                  '<ul>'+
                                  '</ul>'+
                                  '<a class="next icon" href="javascript:void(0)"></a>'+
                              '</div>'+
                          '</div>'+
                      '</div>'+
                      '<div class="detail_comment">'+
                          '<div class="comment_textarea clearfix">'+
                              '<div class="img_con fl" style="background-image:url(\''+this.config.face+'\');"></div>'+
                              '<div class="editor_textarea fr pr ">'+
                                  '<textarea placeholder="来说两句吧~"></textarea>'+
                                  '<div class="editor_btn f18 tac cp pa " id="btn_commentadd" >发布</div>'+
                              '</div>'+
                          '</div>'+
                          '<div class="comment_body">'+
                              '<ul class="clearfix">'+
                                  '<li  class="fl cur f16 pr cp">最新评论<b></b><i class="triangle pa"></i></li>'+
                              '</ul>'+
                              '<ol class="comment_body_con" id="in_comment_con">'+

                              '</ol>'+
                              '<div class="comment commMore2" id="load_more" style="display:none;"><a class="commMoreA"  href="javascript:;">加载更多</a></div>'+
                          '</div>'+
                      '</div>'+
                  '</div>'+
                  '<div class="right_detail_con fr">'+
                      '<div class="download_con tac">'+
                          '<h4>已有<b class="f18"><label class="play_num"></label>人</b>在玩</h4>'+
                          '<div class="download_btn" data-val="">'+
                              '<div class="buy_game pr cp ">'+
                                  '<i class="icon pa buy_icon fl"></i>'+
                                  '<div class="fr tal">'+
                                      '<p class="f20 ">立即购买</p>'+
                                      '<p class="size game_price"></p>'+
                                  '</div>'+
                              '</div>'+
                              '<div class="install_game pr cp" onclick="tickGame()">'+
                                  '<i class="icon pa install_icon fl"></i>'+
                                  '<div class="fr tal">'+
                                      '<p class="f20 ">立即下载</p>'+
                                      '<p class="size game_size"></p>'+
                                  '</div>'+
                              '</div>'+
                              '<div class="down_progress_con" onclick="tickGame()" >'+
                                  '<p class="in_down_progress_con">'+
                                      '<span style="" class="pr">'+
                                          '<i class="pa"></i>'+
                                      '</span>'+
                                      '<span class="progress_num">100%</span>'+
                                  '</p>'+
                                  '<p class="clearfix progress_msg ">'+
                                      '<b class="fl">30M</b>'+
                                      '<b class="fl">/</b>'+
                                      '<b class="fl game_size"></b>'+
                                      '<span class="fr">'+
                                          '<i class="icon fr close_icon cp"></i>'+
                                          '<i class="icon fr pause_icon play_icon cp"></i>'+
                                      '</span>'+
                                  '</p>'+
                              '</div>'+
                              '<div class="play_game pr cp f20" onclick="tickGame()">'+
                                  '<i class="triangle fl pa"></i>'+
                                  '开始游戏'+
                              '</div>'+
                          '</div>'+
                          '<div class="comment_con clearfix">'+
                              '<span class="fl good_comment pr f14 cp"><i class="icon good_comment_icon pa"></i>300</span>'+
                              '<span class="fr negative_comment pr f14 cp" ><i class="icon negative_comment_icon pa"></i>200</span>'+
                          '</div>'+
                          '<div class="share_con">'+
                              '<i class="fl f14">分享</i>'+
                              '<div class="bdsharebuttonbox bdshare-button-style0-24"><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a></div>'+
                              '<script></script>'+
                          '</div>'+
                      '</div>'+
                      '<div class="friend_play_game">'+
                          // '<p class="in_friend_play f16 clearfix">'+
                          //     '<span class="els fl  cd">3个好友在玩</span>'+
                          //     '<span class="els fr cp">我的vr女友</span>'+
                          //     '<span class="friend_list pr">'+
                          //         '<i class="pa" style="background-image:url(\'http://zhengyi.date/plante/src/image/gameimg2.png\');"></i>'+
                          //         '<b class="fr f12">99+在玩</b>'+
                          //     '</span>'+
                          // '</p>'+
                          '<div class="all_play">'+
                              '<h4 class="f16">大家都在玩</h4>'+
                              '<ul>'+
                                  '<li class="clearfix">'+
                                      '<div class="img_con fl" ></div>'+
                                      '<div class="fr msg_con">'+
                                          '<p class="els"></p>'+
                                          '<p class="els">已有多让人在玩</p>'+
                                      '</div>'+
                                  '</li>'+
                              '</ul>'+
                          '</div>'+
                          '<div class="config_con">'+
                              '<ul class="clearfix">'+
                                  '<li class="fl cur pr cp">推荐配置<i class="triangle pa"></i></li>'+
                                  '<li class="fl pr cp">我的配置<i class="triangle pa"></i></li>'+
                              '</ul>'+
                              '<ol>'+
                                  '<li class="local_config cur">'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">操作系统</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">处理器</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">内存</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">DirectX版本</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">显卡</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                  '</li>'+
                                  '<li class="recommend_config">'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">操作系统</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">处理器</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">内存</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">DirectX版本</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                      '<p class="clearfix">'+
                                          '<span class="fl els">显卡</span>'+
                                          '<span class="fr els">Microsoft Windows 7</span>'+
                                      '</p>'+
                                  '</li>'+
                              '</ol>'+
                          '</div>'+
                          '<div class="recommend_game">'+
                              '<h4 class="f16">猜你喜欢<span class="fr f12"><i class="resh_con  icon"></i><b>换一批</b></span></h4>'+
                              '<ul>'+
                                  '<li class="pr" >'+
                                      '<div class="msg_con pa">'+
                                          '<div class="fl els"></div>'+
                                          '<div class="fr els tar"></div>'+
                                      '</div>'+
                                  '</li>'+
                              '</ul>'+
                          '</div>'+
                      '</div>'+
                  '</div>'+
              '</div>'+
          '</div>'+
      '</div>')
    }
    else{
      $('.game-modal').show();
      $('body').addClass("dimmed");
    }
    var p = this;
    $('.game-modal .blue_close').click(function(){
      p.hideHtml();
     // $(".picFocus .bd ul").removeAttr("style");
    })
  },
  // 轮播js
      htmlJS:function(){

        //详情页轮播
       this.slide =  $(".picFocus").slide({ mainCell:".bd ul",effect:"left",autoPlay:true });

        $('.editor_textarea textarea').focus(function(){
            $(this).parents('.editor_textarea').addClass('unfold_textarea')
        });
         $('.editor_textarea textarea').blur(function(){
            $(this).parents('.editor_textarea').removeClass('unfold_textarea')
        });
        //点击回复
        // $('.detail_comment .reply_btn').on('click','.in_reply_btn',function(){
        //     $(this).find('.icon').toggleClass('fold_icon');
        //     $(this).parents('.detail_comment').find('.reply_msg ').toggle();
        // });
        //点击点赞
        $('.detail_comment .evaluate').on('click','.icon',function(){
            if($(this).parents('.reply_btn').find('i.cur').length == 0){
                $(this).addClass('cur');
            }
        });

        //推荐配置事件
        $('.config_con ul li:first').click(function(){
          $('.config_con ul li:first').addClass('cur');
          $('.config_con ul li:last').removeClass('cur');
          $('.config_con ol li:first').addClass('cur').show();
          $('.config_con ol li:last').removeClass('cur').hide();
        })
        $('.config_con ul li:last').click(function(){
          $('.config_con ul li:first').removeClass('cur');
          $('.config_con ul li:last').addClass('cur');
          $('.config_con ol li:first').hide();
          $('.config_con ol li:last').show();
        })
      },
}
  window.vrGameDetail = vrGameDetail;
})(jQuery)
