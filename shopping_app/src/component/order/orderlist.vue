<template lang="html">
  <div class="history_order_list pr">
    <v-scroll :on-refresh="onRefresh" :on-infinite="onInfinite">
      <ul>
        <li v-for="(item,index) in orderlist">
          <div class="order_num clearfix">
            <h4 class="fl">订单号：{{item.order_num}}</h4>
            <div class="fr">{{item.time}}</div>
          </div>
          <div class="list_con ">
            <div class="in_list_con clearfix">
              <div class="img_con fl"  :style="{background:'url('+item.img+') center no-repeat '}"></div>
              <div class="msg_con">
                <p class="in_goods_msg els">{{item.info}}</p>
                <p class="discount_msg">{{item.discount_msg}}<b>{{item.discount_state}}</b></p>
                <p class="goods_num">{{item.goods_num}}</p>
                <p class="clearfix price_con">
                  <span class="fl">￥{{item.price}}</span>
                  <span class="fl">(运费：￥{{item.freight}})</span>
                  <span class="fr">X{{item.num}}</span>
                </p>
              </div>
            </div>
            <div class="order_state clearfix">
              <div class="shipped icon fl"></div>
              <div class="order_num">
                <p>运单号码：392039560815</p>
                <p>韵达快递</p>
              </div>
            </div>
          </div>
        </li>
        <li v-for="(item,index) in downdata">
          <div class="order_num clearfix">
            <h4 class="fl">订单号：{{item.order_num}}</h4>
            <div class="fr">{{item.time}}</div>
          </div>
          <div class="list_con ">
            <div class="in_list_con clearfix">
              <div class="img_con fl"  :style="{background:'url('+item.img+') center no-repeat '}"></div>
              <div class="msg_con">
                <p class="in_goods_msg els">{{item.info}}</p>
                <p class="discount_msg">{{item.discount_msg}}<b>{{item.discount_state}}</b></p>
                <p class="goods_num">{{item.goods_num}}</p>
                <p class="clearfix price_con">
                  <span class="fl">￥{{item.price}}</span>
                  <span class="fl">(运费：￥{{item.freight}})</span>
                  <span class="fr">X{{item.num}}</span>
                </p>
              </div>
            </div>
            <div class="order_state clearfix">
              <div class="shipped icon fl"></div>
              <div class="order_num">
                <p>运单号码：392039560815</p>
                <p>韵达快递</p>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </v-scroll>

  </div>
</template>

<script>
import Scroll from '../scroll/scrooll.vue';
export default {
  props:["type"],
  data(){
    return{
      counter : 1, //默认已经显示出15条数据 count等于一是让从16条开始加载
      num : 5,  // 一次显示多少条
      pageStart : 0, // 开始页数
      pageEnd : 0, // 结束页数
      orderlist: [], // 下拉更新数据存放数组
      downdata: []  // 上拉更多的数据存放数组
    }
  },
  methods:{
    getDate(url,params){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          that.orderlist =JSON.parse(response.body).orderlist.slice(0,that.num);
        }, response => {
          // error callback
        });
      })
    },
    onRefresh(done) {
      console.log(this.type)
      if(this.type == 0){
        this.getDate('./../../../data/order.txt','');

      }else{
        this.getDate('./../../../data/order2.txt','');
      }
             done() // call done
    },
    onInfinite(done) {
        let vm = this;
        vm.$http.get('./../../../data/order.txt').then((response) => {
            vm.counter++;
            vm.pageEnd = vm.num * vm.counter;
            vm.pageStart = vm.pageEnd - vm.num;
            let arr = JSON.parse(response.body).orderlist;
            let i = vm.pageStart;
            let end = vm.pageEnd;
            console.dir(vm.counter)
            for(; i<end; i++){
              let obj ={};
              obj = arr[i];
              vm.downdata.push(obj);
               if((i + 1) >= JSON.parse(response.body).orderlist.length){
                  this.$el.querySelector('.load-more').style.display = 'none';
                  return;
                }
            }
            done() // call done
             }, (response) => {
              console.log('error');
          });
     },
    updateDate(){
      var that = this;
      that.$router.app.$on('updateOrder',function(res){
        that.orderlist = res;
      })
    },
  },
  mounted(){
    if(this.type == 0){
      this.getDate('./../../../data/order.txt','');

    }else{
      this.getDate('./../../../data/order2.txt','');
    }
  },
  created(){
    var that = this;
    that.updateDate();

    //that.getDate();
  },
  components : {
    'v-scroll': Scroll
  }
}
</script>

<style lang="scss" scoped>
@import "../../style/base.scss";
.shipped{width: 150px;height: 50px;background-position:-210px -33px;}
.non_payment{width: 35px;height: 50px;background-position: -375px -33px;}
.non_payment{}
.history_order_list{
  height: 100%;
  width: 100%;
  padding-bottom: 1.6rem;
  .yo-scroll{background: #fff;}
  li{
    padding: 0 0.15rem;
    border-bottom: 1px solid $borderCol;
    .order_num{
      border-bottom: 1px solid $borderCol;
      line-height: 0.8rem;
      color: $greyCol;
    }
    .list_con{
      padding: 0.25rem;
      .img_con{
        width: 1.9rem;
        height: 1.8rem;
      }
      .msg_con{
        width: 100%;
        padding-left: 2rem;
        @include box-sizing(border-box);
        .in_goods_msg{
          font-size: 0.32rem;

        }
        .discount_msg{
          margin:0.1rem 0;
          color: $greyCol;
          b{color:$specialRedCol;}
        }
        .goods_num{
          font-size: 0.32rem;
        }
        .price_con{
          span{
            margin-top: 0.14rem;
            &:nth-child(1){
              font-size: 0.4rem;
              color:$specialRedCol;
            }
            &:nth-child(2){
              margin-top: 0.24rem;
            }
            &:nth-child(3){
              font-size: 0.4rem;
            }
          }
        }
      }
      .order_state{
        margin-top: 0.2rem;
        width: 100%;
        height: 1.2rem;
      }
      .order_num{padding-left: 160px;text-align: right;line-height: 30px;border: none;}
    }
  }
}
</style>
