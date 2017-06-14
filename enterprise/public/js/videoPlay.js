(function(){
  var videoPlay = {
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
      checkVideo:function(tp,id){
        this.showHtml();
        this.getData(id);
        this.htmlJS();
        this.record(this.config.uid,id);
      },
      showHtml:function(){
        console.log('this');
        if($('.video_detail').length==0){
            $('body').append('<div class="video_detail mask"><div class="bobo_paly_box oh pr">'+
            '<div class="bobo_paly_top">'+
                '<ul class="fr clearfix">'+
                    '<li class="fr icon windowclose_icon cp"></li>'+
                    '<li class="fr icon min_icon cp"></li>'+
                '</ul>'+
            '</div>'+
            '<div class="fl player oh video_play_container pr"></div>'+
            '<div class="bobo_paly_switch pa"><span class="close"></span><span class="open"></span></div>'+
            '<div class="fr bobo_paly_r">'+
                '<div class="bobo_paly_info">'+
                    '<ul>'+
                        '<li class="f14">巴哈马海岛旅游巴哈马海岛旅游巴哈马海岛旅游巴哈马海旅游巴哈马海岛旅游巴哈马海岛旅游巴哈海岛旅游巴哈马海岛旅游</li>'+
                        '<li><span class="fl good_comment pr f12 cp"><i class="icon good_comment_icon pa"></i><b>300</b></span>'+
                            '<div class="share_con oh pr">'+
                                '<i class="fl f12">分享：</i>'+
                                '<div class="bdsharebuttonbox bdshare-button-style0-24 pa" data-bd-bind="1495854859374">'+
                                    '<a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>'+
                                '</div>'+
                            '</div>'+
                        '</li>'+
                    '</ul>'+
                '</div>'+
                '<div class="bobo_recommend_play oh">'+
                    '<h4 class="f14">猜你喜欢<span class="fr f12"><i class="resh_con  icon"></i>换一批</span></h4>'+
                    '<ul>'+
                        '<li class="clearfix">'+
                            '<div class="fl pr"><img src="http://s.amazeui.org/media/i/demos/bw-2014-06-19.jpg?imageView/1/w/1000/h/1000/q/80" width="71" height="42"></div>'+
                            '<div class="fl f12 bobo_recommend_play_m oh">'+
                                '<a href="#">'+
                                    '<p class="els">巴哈马海巴哈马海岛旅游巴哈马海岛旅游巴哈马海岛旅游岛旅游</p>'+
                                    '<p class="gray els">爽啊爽啊爽翻了爽啊爽翻了爽啊爽翻了爽翻了！！</p>'+
                                '</a>'+
                            '</div>'+
                        '</li>'+
                    '</ul>'+
                '</div>'+
            '</div>'+
        '</div></div>');
        $('.video_detail').show();
      }
      else{
        $('.video_detail').show();
      }
      var p = this;
      $('.video_detail .bobo_paly_box .bobo_paly_top .windowclose_icon').click(function(){
        p.hideHtml();
       // $(".picFocus .bd ul").removeAttr("style");
      })
    },
    hideHtml:function(){
      $('.video_detail').remove();
      $('.video_play_container').html('');
      $('body').removeClass('dimmed');
    },
    htmlJS:function(){
      $('body').addClass('dimmed');

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
    //获取数据
    getData:function(id){
      var p = this;
      $.post('/vrhelp/videoDetai/'+id,{},function(data){
          // console.log(data);
          p.updateDetail(data.data);
          $.post('/vrhelp/videoListInterface',{type:data.data.video_class},function(like){
              p.updateLike(like.data);
          })
      })
    },
    //更新详细信息
    updateDetail:function(data){
      $('.bobo_paly_info ul li:first').text(data.video_intro);
      $('.bobo_paly_info ul li:last span b').text(data.agreenum);
      // $('.player').html('<video style="width:917px" src="'+data.video_link+'" controls="controls"></video>')
      //vr视频播放
      if(data.video_link.indexOf("vronline.com")>0) {
          var html = '<div class="video_vr_btn pa cp"></div><div class="valiantPhoto" id="valiantPhoto" data-video-src="'+data.video_link+'" style="width: 100%; height:100%;background: #000"></div>';
          $(".video_play_container").html(html);
          $('#valiantPhoto').Valiant360({
              clickAndDrag:true,
              muted:false,
              loop:false,
           });
      } else {
          var html = ' <iframe allowfullscreen class="valiantPhoto" src="'+data.video_link+'" style="width: 100%; height: 100%;background: #000;border:none"></iframe>';
          $(".video_play_container").html(html);
      }
    },
    //更新猜你喜欢
    updateLike:function(like){
      // console.log(like);
      html='';
      var p = this;
      $(like.videoList).each(function(k,v){
        if(k<16){
          html+='<li class="video_play clearfix page'+Math.floor(k/4+1)+'" data-val="'+v.id+'" data-page="'+Math.floor(k/4+1)+'">'+
                    '<div class="fl pr"><img src="'+p.parameter.url+'/'+v.image.cover+'" width="71" height="42"></div>'+
                    '<div class="fl f12 bobo_recommend_play_m oh">'+
                        '<a href="#">'+
                            '<p class="els">'+v.name+'</p>'+
                            '<p class="gray els">'+v.desc+'</p>'+
                        '</a>'+
                    '</div>'+
                '</li>';
        }
      })
      $('.bobo_recommend_play ul').html(html);
      $('.bobo_recommend_play ul li').hide();
      $('.bobo_recommend_play ul .page1').show();

      //换一批翻页
      $('.bobo_recommend_play h4 span').click(function(){
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
    record:function(uid,vid){
      $.get('http://www.vronline.com/vrhelp/videoRecord/'+uid+'/'+vid,{},function(){

      })
    }

  }
  window.videoPlay = videoPlay;
})(jQuery)
