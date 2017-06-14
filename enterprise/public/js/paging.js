(function(){
	var paging = {
		//插件参数
	  config:{
	    total_page:1,//总页数
			page:1,//当前页
			target_obj:null,//html目标
			callback:function(){},//回调
	  },
		//hash参数
		hash:{
		},
		//初始化
	  init:function(config){
			this.readHash();
			//初始化hash的page默认为1
			if(undefined==this.hash.page){
				this.hash.page = 1;
			}
			//获取插件参数
	    this.config = $.extend({},this.config,config);
			if(''==this.config.page){
				this.config.page = 1;
			}
	    var p =this;
			// console.log(this.config.page);
			// console.log(this.config);
			this.paging();
	  },
		//读取当前的hash
		readHash:function(){
			p = this;
			hash = window.location.hash.substr(1);
			arr = hash.split("&");
			$(arr).each(function(k,v){
				temp = v.split('=');
				p.hash[temp[0]] = temp[1];
			});
		},
		//生成分页的html
		paging:function(){
			if(this.config.target_obj==null){
				console.log('无目标');
				return false;
			}
			//分页
			lihtml = '';
			flag=0;
			//为了实际的分页效果特殊优化
			if(this.config.page<4){
				npage=3;
			}
			else{
				npage=this.config.page;
			}
			//循环分页
			for(var i = 1;i < this.config.total_page + 1;i++){
					if(i==1||i==2||i>npage-3&&i<parseInt(npage)+3){
						if(i==this.config.page){
							lihtml+='<a href="javascript:;" onclick="paging.changePage('+i+')" class="active">'+i+'</a>';
						}
						else{
							lihtml+='<a href="javascript:;" onclick="paging.changePage('+i+')">'+i+'</a>';
						}
					}
					else{
						if(i>2&&npage-i>2&&flag==0){
							lihtml+='<a class="ellipsis">...</a>';
							flag=1;
						}
					}
			}
			if(this.config.total_page-npage>2){
							lihtml+='<a class="ellipsis">...</a>';
			}
			phtml='<div class="s_page"><a href="javascript:;" onclick="paging.changePage(\'p\')">上一页</a>';
			nhtml='<a href="javascript:;" onclick="paging.changePage(\'n\')">下一页</a></div>';
			$('.s_page').html(phtml+lihtml+nhtml);
		},
		//改变当前页
		changePage:function(page){
			// console.log(page);
			if(page=='p'&&this.hash.page>1){
		     this.hash.page --;
		  }
		  if(page=='n'&&this.hash.page<this.config.total_page){
		     this.hash.page ++;
		  }
		  if(page){
		    if( !isNaN( page ) ){
		      this.hash.page = page;
		    }
		  }
			// console.log(this.hash.page);
		  this.changeHash();
		},
		//修改hash
		changeHash:function(){
	    var url_arr = [];
	    var url;
	    for (var k in this.hash) {
	    		url_arr.push(k+'='+this.hash[k])
	    }
	    if(url_arr.length>0) {
	        url = "#"+url_arr.join("&");
	    } else {
	        url = '';
	    }
	    location.href = url;
			this.changeCallback();
		},
		//修改hash后的回调
		changeCallback:function(){
			this.config.callback();
		}
	}
	window.paging = paging;
})(jQuery)
