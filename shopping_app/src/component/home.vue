<template>
	<div id="home">
		<foot></foot>
		<div class="top_con px">
			<div class="search_bar_con">
				<div class="search_bar pr">
					<i class="search_icon pa"></i>
					<input type="text" placeholder="搜索笔记，商品和用户">
				</div>
			</div>
			<div class="nav_bar">
				<ul class="clearfix">
					<router-link tag = 'li' v-for="(item,index) in navList"  :key="item.id" class="fl" :to="'/home/'+item.id" @click.stop="navClick()">{{item.title}}</router-link>
				</ul>
			</div>
			<div class="system_notice pr">
				<i class="notice_icon icon pa"></i>
				<ul class="clearfix"  id="system_notice">
					<li v-for="item in notice" class="fl">{{item.msg}}</li>
				</ul>
			</div>
		</div>
		<div class="in_home" id="in_home">

			<router-view></router-view>
		</div>
	</div>
</template>
<script>
import jquery from  '../js/jquery-1.12.3.min.js';

window.$ = jquery;
//var hei = document.getElementById('in_home').offsetTop;
//console.dir(hei);
//var hei = $('in_home');

 export default{
    name:'home',
    data(){
      return{
				navList:[
					{
						title:'推荐',
						state:'home',
						id:1
					},
					{
						title:'零食',
						state:'list',
						id:2
					},
					{
						title:'化妆品',
						state:'list',
						id:3
					},
					{
						title:'衣服',
						state:'list',
						id:4
					}
				],
				notice:[{msg:'夏季活动，四折优惠'},
				{msg:'夏季活动，6折优惠'},{msg:'夏季活动，8折优惠'}]
      }
    },
		created:function(){
			var that =this ;
			setTimeout(function(){
				that.noticeScroll();
			},10);
		},
		methods:{
			navClick(){
				console.dir(1)
			},
			noticeScroll:function(){
				var that = this,wid =0;
				var $item  =  document.querySelectorAll('#system_notice');
				var  $items=  document.querySelectorAll('#system_notice li');
				//console.log($items.length)
				for(var i= 0 ; i< $items.length ; i ++){
					wid +=parseInt($items[i].offsetWidth);
				}
				console.log(wid);


				$item[0].style.width = (wid+240)+'px';
				function donghua(){
					var len =0;
					if( wid= len){
						len = 0;
					}else{
						len =wid;
					}
					$("#system_notice li").eq(0).animate({'margin-left':-(len)},5000,function(){
					 $("#system_notice").eq(0).css({"margin-left":0});
			 		});
				}
				setInterval(function(){
					donghua()
				},5000)

			}
		}
 }
</script>
<style lang="scss">
$mainColor:#ff6d70;
$borderCol:#eaeaea;
.notice_icon{
	width: 20px;height: 20px;
	background-position: -115px 0;
	top:50%;
	margin-top: -10px;
	left:0.15rem;
}
@mixin box-sizing($sizing){
  -webkit-box-sizing:$sizing;
       -moz-box-sizing:$sizing;
            box-sizing:$sizing;
}
@mixin border-bottom($px,$col){
	border-bottom: $px solid $col;
}
@mixin border-top($px,$col){
	border-top: $px solid $col;
}
#home{
	height: 100%;
	width: 100%;
	overflow: hidden;
}
.top_con{
	width: 100%;
	height: 1.68rem;
}
.search_bar_con{
	width: 100%;
	background: $mainColor;
	padding: 0 0.15rem;
	.search_bar{
		@include box-sizing(border-box);
		padding: 0.12rem 0;
		.search_icon{
			width: 0.4rem;
			height: 0.4rem;
			top:50%;
			margin-top: -0.2rem;
			left:0.2rem;
			background:url('http://zhengyi.date/shopping/dist/search_icon.png') no-repeat center;
			background-size: cover;
		}
		input{
			line-height: 0.6rem;
			color:#fff;
			width: 100%;
			background: rgba(250,250,250,.2);
			font-size: 0.3rem;
			text-indent: 0.8rem;
			border-radius: 1px;
			&::-webkit-input-placeholder{
				color:#fff;
			}

		}
	}
}
.nav_bar{
	@include border-bottom(1px,$borderCol);
	background: #fff;
	ul{
		li{
			font-size: 0.26rem;
			color:#9e9e9e;
			line-height: 0.84rem;
			@include border-bottom(2px,rgba(0,0,0,0));
			padding: 0 0.2rem;
			&.router-link-active{
				@include border-bottom(2px,$mainColor);

			}
		}
	}

}
.system_notice{
	line-height: 0.6rem;
	padding:0 0.15rem 0 0.75rem;
	overflow: hidden;
	ul{
		width: 200rem;
		height: 0.6rem;
		margin: 0.1rem 0;
		li{
			padding-right:2.4rem;
		}
	}
}
.in_home{
	width: 100%;
	height: 100%;
	padding: 2.50rem 0 1rem 0;
	overflow: hidden;

}



</style>
