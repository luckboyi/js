(function($){
	var comment={};
	comment.config={
    page:1,
    len:5,
    commentContainer:'#in_comment_con',
    type:'',
    id:'',
    targetid:'',
    userid:'',
	}
	comment.isSending= 0 ;
	comment.init = function(config){
    var _this = this ;
    //获取配置参数
    _this.config = $.extend({},_this.config,config);
    var data={
      target_id:'',
      type:'',
      page:'',
      len:''
    }
    data.target_id = _this.config.target_id;
    data.type = _this.config.type;
    data.page = _this.config.page;
    data.len = _this.config.len;
    _this.getDate(data);
		//点击回复
		_this.clickFn();

	}
	comment.sendCommend = function(data,content,currentdate){
		var that = this;
		if(that.isSending){
			return false ;
		}
		that.isSending = 1;
		$.ajax({
			url:'http://www.vronline.com/newcomment/add',//发送好评的数据
			dataType:'json',
			data:data,
      xhrFields: {
        withCredentials: true
      },
      crossDomain: true,
      success:function(data){

        if(data.code == 0){

          that.addCommentHtml(data.data,content,currentdate);
        }else{
           if(typeof(data.msg)!="undefined") {
						 		alert(data.msg)
                // loiNotifly(data.msg)
            } else {
								alert("发表失败")
                // loiNotifly("发表失败")
            }
        }
      },
      error:function(){
        console.dir('发表失败')
      }
		})
	}
  comment.addCommentHtml = function(val,content,currentdate){
		// console.log(val);
    var that = this;
    var html = '<li class="comment_list clearfix" id="'+val.cid+'">'+
				 				    '<div class="fl img_con" style="background-image:url('+val.face+');background-size: 100%;"></div>'+
				 				    '<div class="fr msg_con">'+
				 				        '<p class="comment_user">'+val.nick+'评论于'+currentdate+'</p>'+
				 				        '<p class="comment_msg">'+
				 				           content+
				 				        '</p>'+
				 				        '<div class="clearfix reply_con cp clearfix">'+
				 				            '<div class="fl pr reply_btn clearfix">'+
				 				                '<div class="in_reply_btn fl pr">'+
				 				                    '回复'+
				 				                    '<i class="icon  unfold_icon "></i>'+
				 				                '</div>'+
				 				            '</div>'+
				 				            '<div class="reply_msg clearfix comment_area" style="display:block" id="reply_'+val.uid+'">'+
				 						                '<div class="reply_input clearfix">'+
				 						                    '<div class="editor_textarea fr pr ">'+
				 						                        '<textarea placeholder="来说两句吧~"></textarea>'+
				 						                        '<div class="editor_btn f18 tac cp pa plbuttonimg" >回复</div>'+
				 						                    '</div>'+
				 						                '</div>'+
				 														'</div>'+
				 				    '</div>'+
				 				'</li>';
      $(that.config.commentContainer).prepend(html);
      $('.game_resource_commend_con').find('textarea').val('');
  }
  comment.replyCommend = function(obj,data,content,currentdate){
    var that = this;
    $.ajax({
      url:'http://www.vronline.com/newcomment/addreply',
      dataType:'json',
      type:'post',
      data:data,
      xhrFields: {
        withCredentials: true
      },
      crossDomain: true,
      success:function(data){
        if(data.code == 1301){
          console.dir('请登录')
        }
        if(data.code == 0){
          var json={
            target_id:'',
            type:'',
            page:'',
            len:''
          }

          that.replyHtml(obj,data.data,content,currentdate);
        }
      },
      error:function(){
        $(this).prev('textarea').val('评论提交失败，请检查网络并重新提交').css({
          color: '#e74b48',
        });
      }
    })
  }

  comment.replyHtml = function(obj,val,content,currentdate){
    //console.dir(val)
    var html = '<div class="fl img_con" style="background-image:url('+val.face+');background-size: 100%;"></div>'+
								'<div class="fr msg_con">'+
										'<p class="comment_user">'+val.nick+'评论于'+currentdate+'</p>'+
										'<p class="comment_msg">'+
										content+
										'</p>'+
								'</div>'+
						 '</div>';
    $(obj).find('.comment_area').append(html);
    $(obj).find('.plbothidetwo ').hide();
  }
  comment.getDate = function(data){
    var that = this ;
    $.ajax({
      url:'http://www.vronline.com/newcomment/get',
      type:'get',
      dataType:'json',
      data:data,
      success:function(data){
        //console.dir(data);

				//修改总条数
				$('.comment_body ul li b').text('('+data.data.total+')');
				$(that.config.commentContainer).html('');
        // console.dir(data)
        if(data.code != 0){
					// console.dir(2)
          that.emptyDate('没有更多评论了');
          $('#load_more').hide();
          return false ;
        }
        if(data.data.comment.length <1){
          // console.dir(1)
          that.emptyDate('没有更多评论了');
          $('#load_more').hide();
          return false ;
        }
        var i = 0 ;
        var arr = data.data.comment;
        var html;
        that.createHtml(arr);

        $('#load_more').show();
      },
      error:function(){
        that.emptyDate('加载失败，请检查网络');
      }
    })
  };
  comment.createHtml = function(val){
		// console.log('create');
    var that = this;
    var html='';
    for(var i = 0 ; i< val.length ; i++){
      html += '<li class="comment_list clearfix" id="'+val[i].id+'">'+
		    '<div class="fl img_con" style="background-image:url('+val[i].face+');background-size: 100%;"></div>'+
		    '<div class="fr msg_con">'+
		        '<p class="comment_user">'+val[i].nick+'评论于'+val[i].ctime+'</p>'+
		        '<p class="comment_msg">'+
		           val[i].content+
		        '</p>'+
		        '<div class="clearfix reply_con cp clearfix">'+
		            '<div class="fl pr reply_btn clearfix">'+
		                '<div class="in_reply_btn fl pr">'+
		                    '回复'+
		                    '<i class="icon  unfold_icon "></i>'+
		                '</div>'+
		            '</div>'+
		            '<div class="reply_msg clearfix comment_area" style="display:block" id="reply_'+val[i].uid+'">'+
				                '<div class="reply_input clearfix">'+
				                    '<div class="editor_textarea fr pr ">'+
				                        '<textarea placeholder="来说两句吧~"></textarea>'+
				                        '<div class="editor_btn f18 tac cp pa plbuttonimg" >回复</div>'+
				                    '</div>'+
				                '</div>';
												for(var k= 0 ; k< val[i].reply_to.length ; k++){
				                html+='<div class="fl img_con" style="background-image:url('+val[i].reply_to[k].fface+');background-size: 100%;"></div>'+
						                '<div class="fr msg_con">'+
						                    '<p class="comment_user">'+val[i].reply_to[k].fnick+'评论于'+val[i].reply_to[k].fts+'</p>'+
						                    '<p class="comment_msg">'+
						                    val[i].reply_to[k].reply+
						                    '</p>'+
						                '</div>'+
						             '</div>';
											 }
				        html+='</div>'+
		    '</div>'+
		'</li>';
    }
		// console.log(that.config.commentContainer);
          $(that.config.commentContainer).append(html);
  }
  comment.emptyDate = function(msg){
    var html = '<div class="comment commMore1"><a href="javascript:;">'+msg+'</a></div>';
    var that = this;
    $(that.config.commentContainer).append(html);
  };
  comment.clickFn = function(){
    var that = this;
    //点击回复按钮
    $('body').on('click','.reply_btn',function(){
      $(this).parents('.comment_list').find('.plbothidetwo').slideToggle();
    });
    //点击提交
    $('body').on('click','.plbuttonimg',function(){
      var val = $(this).prev('textarea').val();
      var id = $(this).parents('.comment_list').attr('id');
      var data = {
        content:'',
        cid:''
      }
      data.content = val;
      data.cid = id;
      if(that.config.userid !=''){
          if(val.length < 15){

            // that.sendCommend(data);
            $(this).prev('textarea').val('评论内容不能少于15个字，请重新输入').css({
              color: '#e74b48',
            });
          }else{
            //提交发送信息
            var obj = $(this).parents('.comment_list');
            function getNowFormatDate() {
                var date = new Date();
                var seperator1 = "-";
                var seperator2 = ":";
                var month = date.getMonth() + 1;
                var strDate = date.getDate();
                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                if (strDate >= 0 && strDate <= 9) {
                    strDate = "0" + strDate;
                }
                var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
                        + " " + date.getHours() + seperator2 + date.getMinutes()
                        + seperator2 + date.getSeconds();
                        //console.dir(currentdate)
                that.replyCommend(obj,data,val,currentdate);
                return currentdate;
            }
            getNowFormatDate();



          }
      }else{
        $(this).prev('textarea').val('您未登录,请先登录').css({
            color: '#e74b48',
        });
      }

      $('body').find('textarea').each(function(index, el) {
        $(this).focus(function(event) {
          $(this).val('').css({color:'#323232'})
        });
    });

    });
    //获取焦点的时候
    $('body').find('textarea').each(function(index, el) {
        $(this).focus(function(event) {
          $(this).val('').css({color:'#323232'})
        });
    });
    //点击加载更多
      var page = that.config.page;
      $('body').on('click','#load_more',function(){
          //alert(1)
          //console.dir(11)
          page= page+1;
          //console.dir(page)
          var data={
          target_id:'',
          type:'',
          page:'',
          len:''
        }
        data.target_id = that.config.target_id;
        data.type = that.config.type;
        data.page = page;
        data.len = that.config.len;
        that.getDate(data);

      })
    //点击评论
    $('body').on('click','#btn_commentadd',function(){
      var val = $(this).prev('textarea').val();
      var data ={
        content:'',
        target_id:'',
        type:'',
      }
      data.content = val;
      data.target_id = that.config.target_id;
      data.type = that.config.type;
      if(that.config.userid !=''){
        if(val.length <15){
           $(this).prev('textarea').val('评论内容不能少于15个字，请重新输入').css({
            color: '#e74b48',
          });
        }else{
          function getNowFormatDate() {
                var date = new Date();
                var seperator1 = "-";
                var seperator2 = ":";
                var month = date.getMonth() + 1;
                var strDate = date.getDate();
                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                if (strDate >= 0 && strDate <= 9) {
                    strDate = "0" + strDate;
                }
                var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
                        + " " + date.getHours() + seperator2 + date.getMinutes()
                        + seperator2 + date.getSeconds();
                        //console.dir(currentdate)
                that.sendCommend(data,val,currentdate);
                //that.replyCommend(obj,data,val,currentdate);
                return currentdate;
            }
            getNowFormatDate();
        }
      }else{
        $(this).prev('textarea').val('您未登录,请先登录').css({
            color: '#e74b48',
        });
      }

    })
  }

	window.Comment = comment;
})(jQuery)
