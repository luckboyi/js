<template>
  <div class="goods_detail" id="detail">
    <div class="detail_head tac">
      <h3 class="pr"><i class="icon back_icon pa" @click="goBack()"></i>demo<i class="icon share_icon pa" @click="shareFn()"></i></h3>
    </div>
    <div class="system_notice pa pr">
      <i class="notice_icon icon pa"></i>
      <div class="">
        <ul class="clearfix" >
          <li v-for="item in data.notice" class="fl">{{item.msg}}</li>
        </ul>
      </div>
    </div>
    <div class="goods_container">
      <div class="goods_img pr">
          <ul class="clearfix ">
            <li class="fl" v-for='(item,$index) in data.goods_img' :class="{cur:$index==data.isselect}">
              <img :src="item.img"  >
            </li>
          </ul>
        <div class="page_con pa">{{data.isselect+1}}/{{data.goods_img.length}}</div>
      </div>
      <div class="goods_msg">
        <p class="goods_name">{{data.name}}</p>
        <div class="goods_price clearfix">
          <div class="goods_sales_num fl">
            累计销量：{{data.salesnum}}
          </div>
          <div class="fr redColor">
            ￥{{data.price}}
          </div>
        </div>
        <div class="goods_active">
          <ul class="clearfix">
            <li v-for="item in data.goods_active" class="pr fl" ><i class="icon pa" :class="item.class"></i>{{item.txt}}</li>
          </ul>
        </div>
      </div>
      <div class="goods_tab">
        <ul class="clearfix tac">
          <li class="fl" :class="{cur:data.iscur}" @click="tabFn('information')">商品规格</li>
          <li class="fl" :class="{cur:!data.iscur}" @click="tabFn('describe')">商品描述</li>
        </ul>
        <goodsinfor :goodsinfor="data.goodsinfor"></goodsinfor>
      </div>

    </div>
    <order :orderdata ="data.orderdata"></order>
  </div>
</template>

<script>
export default {
  name:'detail',
  data(){
    return{
      data:{
        goods_img:[
        ],
        goodsinfor:{
        },
        orderdata:{
        },
        name:'',
        salesnum:'',
        price:'',
        goods_active:[

        ],
      }

    }
  },
  created(){
    this.getDate('./../../../data/detail.txt','',this.getDetailData)
  },
  methods:{
    getDate(url,params,callback){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          console.log(typeof callback)
          if(typeof callback === 'function'){
            console.log(response.body)
            callback(JSON.parse(response.body).data);

          }
        }, response => {
          // error callback
        });
      })
    },
    getDetailData(data){
      this.data = data;
    },
    goBack(){
      //console.dir('返回')
      window.history.go(-1)
    },
    shareFn(){
      alert('分享')
    },
    tabFn(i){
      var that = this;
      that.data.iscur = !that.data.iscur;
      that.data.goodsinfor.iscur = !that.data.goodsinfor.iscur;
    }
  }
}
</script>

<style lang="scss">
$defaultCol:#ff6d70;
$greyCol:#9e9e9e;
@mixin box-sizing($sizing){
  -webkit-box-sizing:$sizing;
       -moz-box-sizing:$sizing;
            box-sizing:$sizing;
}
  $bordCol:#9e9e9e;
  .postage,.pay,.hour,.return{width: 25px;height: 25px;background-position:-242px 0;left:0; top:50%;margin-top: -12.5px;}
  .pay{background-position:-270px 0;}
  .hour{background-position: -298px 0;}
  .return{background-position: -328px 0;}
  .back_icon{background-position: -190px 0;width: 14px;height: 24px;left: 0.25rem;top:50%;margin-top: -12px;}
  .share_icon{background-position: -204px 0; width: 25px;height: 25px;right: 0.25rem;top:50%;margin-top: -12.5px;}
  .goods_detail .notice_icon{left: 0.35rem;background-position: -115px -22px;top:20px;}
  .goods_detail .system_notice{padding: 0 0.15rem ; z-index: 999;}
  .goods_detail .system_notice div{
    background: $defaultCol;
    color: #fff;
    text-indent: 0.8rem;
    width: 100%;
    border-radius: 50px;
  }
  .goods_detail{
    width: 100%;height: 100%;
    overflow: hidden;

    .detail_head{
      line-height: 0.86rem;
      height: 0.86rem;
      border-bottom: 1px solid $bordCol;
      color: #111111;
      padding: 0 0.15rem;
    }
    .notice_container{
        padding: 0 0.15rem;
        width: 100%;
        @include box-sizing(border-box);
        h4{
          width: 100%;
        }
    }
    .goods_container{
      padding: 0 0.15rem 2.75rem 0.15rem;
      height: 100%;
      overflow-x: auto;
      @include box-sizing(border-box);
      .goods_img{
        margin-top: -0.8rem;
        .page_con{
          right: 0.2rem;
          bottom: 0.2rem;
          color: $greyCol;
          font-size: 0.28rem;
        }
        ul{
            width: 100%;
            height: 6.2rem;
            overflow: hidden;
        }
        li{
          height: 6.2rem;
          width: 100%;
          display: none;
          &.cur{
            display: block;
          }
          img{
            width: 100%;
            height: 100%;

          }
        }
      }
      .goods_msg{
        margin-top: 0.2rem;
        .goods_name{
          line-height: 0.346rem;
        }
        .goods_price{
          margin:0.26rem 0 0.46rem 0;
        }
        .goods_sales_num{
          color: $greyCol;
        }
        .redColor{
          font-size: 0.36rem;
        }
      }
      .goods_active{
        text-indent: 28px;
        li{margin: 0 2px 20px 0;}
      }
      .goods_tab{
        li{
          width: 50%;
          line-height:0.7rem;
          color: $greyCol;
          font-size: 0.22rem;
          &.cur{
            background: $defaultCol;
            color: #fff;
          }
        }
      }
    }
  }

</style>
