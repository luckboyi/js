<template lang="html">
  <div class="shoppingCar" id="shoppingCar">
    <div class="shoppingCar_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>购物车</h3>
    </div>
    <div class="shoppingList pr">
      <v-scroll :on-refresh="onRefresh" :on-infinite="onInfinite">
        <div class="inShoppingList">
          <div class="shoppingList_con pr">
            <ul v-if="cartdata.pulldata.length >0">

              <v-touch tag="li" v-for="(list,$index) in cartdata.pulldata"  :class="{leftswipe:list.isleftswipe}" class="order_list pr"  v-on:swipeleft="onSwipeLeft($index)" ref="{{list.id}}">
                <div class="order_list_head clearfix">
                  <i class="select_icon icon  fl" :class="{cur:list.isselect}" @click="selectFn(list)"></i>
                  <div class="img_con fl">
                    <img :src="list.icon">
                  </div>
                  <div class="fl title_con">{{list.username}}</div>
                </div>
                <div class="order_list_con clearfix">
                  <i class="select_icon icon  fl" :class="{cur:list.isselect}"  @click="selectFn(list)"></i>
                    <router-link :to="'/detail/'+list.id">
                      <div class="img_con fl">
                        <img :src="list.img" alt="">
                      </div>
                      <div class="goods_msg fl">
                        <p class="in_goods_msg ells2">{{list.info}}</p>
                        <p class="discount_msg">{{list.discount_msg}}<b>{{list.discount_state}}</b></p>
                        <p class="goods_num">{{list.goods_num}}</p>
                        <p class="clearfix price_con">
                          <span class="fl">￥{{list.price}}</span>
                          <span class="algorithm_con pr tac fr">
                            <i class="sub fl" @click.stop.prevent="subFn($index)">-</i>
                            <b>{{list.num}}</b>
                            <i class="add fr" @click.stop.prevent="addFn($index)">+</i>
                          </span>
                        </p>
                      </div>
                    </router-link>
                </div>
                <div class="delete pa" @click.stop="deleteFn($index)">
                  删除
                </div>
                <div class="freight">
                  <div class="no_freight" v-show="list.isfreight">免运费</div>
                  <div class="in_freight" v-show="!list.isfreight">
                    运费:<b class="fr">￥：{{list.freight}}</b>
                  </div>
                </div>
              </v-touch>
            </ul>
            <div v-if="cartdata.pulldata.length <0" class="shoppingCar_empty icon pa">
            </div>
          </div>
        </div>
        <div class="total_sub pa">
          <div class="fl total_sub_left clearfix">
            <h4 class="fl">合计:<b>￥{{totalPrice}}</b></h4>
            <div class="fr">（含运费<b>￥{{freight_sub}}</b>）</div>
          </div>
          <router-link v-if="cartdata.pulldata.address !=''" tag='div' to="/account"  class="order_btn fr tac" @click="submitBtn()">
            <div class="in_order_btn tac">结算</div>
          </router-link>
          <router-link  v-if="cartdata.pulldata.address ==''" tag='div' to="/address"  class="order_btn fr tac" @click="submitBtn()">
            <div class="in_order_btn tac">结算</div>
          </router-link>
        </div>
      </v-scroll>
    </div>
  </div>
</template>

<script>
import Scroll from './../scroll/scrooll.vue';

export default {
  name:'shoppingCar',
  components : {
    'v-scroll': Scroll
  },
  data(){
    return{
      counter : 1, //默认已经显示出15条数据 count等于一是让从16条开始加载
      num : 6,  // 一次显示多少条
      pageStart : 0, // 开始页数
      pageEnd : 0, // 结束页数
      cartdata: {
        pulldata:[],// 下拉更新数据存放数组
        downdata: []  // 上拉更多的数据存放数组
      }
    }
  },
  mounted(){
    // var that = this;
    // that.$nextTick(function(){
    //   that.$http.get('./../../../data/cartdata.txt').then(response => {
    //     that.cartdata. = JSON.parse(response.body).cartdata;
    //     that.totalPrice = JSON.parse(response.body).totalPrice;
    //     that.freight_sub = JSON.parse(response.body).freight_sub;
    //   }, response => {
    //     // error callback
    //   });
    // })
      this.getListDate('./../../../data/cartdata.txt','');
  },
  computed:{
    totalPrice:function(){
      var total = 0;
      this.cartdata.pulldata.forEach(function(good){
        if(good.isselect){
          console.dir(1)
          total += good.price * good.num;
        }
      });
      return total;
    },
    freight_sub:function(){
      var freight = 0;
      this.cartdata.pulldata.forEach(function(good){
        if(good.isselect){
          freight += good.freight;
        }
      });
      return freight;
    }
  },
  methods:{
    onSwipeLeft(i){
      this.cartdata[i].isleftswipe =!this.cartdata[i].isleftswipe;
      var that = this;
      $(window).on('click',function(){
        that.cartdata.pulldata.forEach(function(item){
          item.isleftswipe = false;
        })
      })
      // document.getElementById("window").addEventListener("click", function(){
      //
      // });
    },
    getDate(url,params,callback){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          console.log(typeof callback)
          if(typeof callback === 'function'){
            //console.log(1)
            callback(JSON.parse(response.body).data);

          }
        }, response => {
          // error callback
        });
      })
    },
    getListDate(url,params){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          console.log(JSON.parse(response.body))
          this.cartdata.pulldata = JSON.parse(response.body).data.slice(0,that.num);
        }, response => {
          // error callback
        });
      })
    },
    getListCon(data){
      this.listdata = data;
    },
    onRefresh(done) {
      this.getListDate('./../../../data/listdata.txt','');
             done() // call done
    },
    onInfinite(done) {
        let vm = this;
        vm.$http.get('./../../../data/listdata.txt').then((response) => {
            vm.counter++;
            vm.pageEnd = vm.num * vm.counter;
            vm.pageStart = vm.pageEnd - vm.num;
            let arr = JSON.parse(response.body).data;
            let i = vm.pageStart;
            let end = vm.pageEnd;
            console.dir(vm.counter)
            for(; i<end; i++){
              let obj ={};
              obj = arr[i];
              vm.cartdata.downdata.push(obj);
               if((i + 1) >= JSON.parse(response.body).data.length){
                  this.$el.querySelector('.load-more').style.display = 'none';
                  return;
                }
            }
            done() // call done
             }, (response) => {
              console.log('error');
          });
     },
    deleteFn(i){
      alert(i)
    },
    goBack(){
        window.history.go(-1)
    },
    onSwipe(){

    },
    subFn(i,e){
      var that = this;
      that.cartdata[i].num--;
      if(that.cartdata[i].num<0){
        that.cartdata[i].num = 0;
        alert('确定删除此商品吗？')
      }

    },
    addFn(i,e){
      var that = this;
      that.cartdata[i].num++;

      //that.totalPrice += that.carlist[i].price*that.carlist[i].num;
    },
    selectFn(goodObj){
      var that = this;
      if(goodObj.isselect == void 0){
        that.$set(goodObj,"isselect",true)
      }else{
        goodObj.isselect = !goodObj.isselect;
      }
    }
  }

}
</script>
<style lang="scss">
@import "../../style/base.scss";
.shoppingCar{
  width: 100%;height: 100%;
}
.leftswipe{
  animation: swipeleft 1s forwards;
  -webkit-animation: swipeleft 1s forwards;
}
@keyframes swipeleft{
    form{
      left: 0;
    }
    to{
      left: -0.98rem;
    }
}
@-webkit-keyframes swipeleft{
    form{
      left: 0;
    }
    to{
      left: -0.98rem;
    }
}
.shoppingCar_head,.account_head,.address_head{
  font-size: 0.28rem;
  h3{
    line-height: 0.88rem;
    height: 0.88rem;
    font-weight: bold;
    border-bottom: 1px solid $borderCol;
  }
  .back_icon{
    margin-left: 0.15rem;
  }
}
.shoppingList{
  height: 100%;
  width: 100%;
  background: red;
  .inShoppingList{
    padding-bottom: 2rem;
    @include box-sizing(border-box);
    height: 100%;
    .shoppingList_con{
      height: 100%;
      background: lightblue;
      overflow-x: hidden;
      background: $greyBg;
    }
  }
}

.order_list{
  margin-top: 0.1rem;
  height: 3.7rem;
  background: #fff;
  .delete{
    height: 100%;
    width: 0.98rem;
    z-index: 999;
    top:0;
    color: #fff;
    line-height:3.7rem;
    right: -0.98rem;
    text-align: center;
    font-size: 0.3rem;
    background: $mainColor;
  }
  .order_list_head{
    padding: 0.06rem 0.26rem;
    @include box-sizing(border-box);
    line-height: 0.66rem;
    border-bottom: 1px solid $greyCol;
    .img_con{
      width: 0.66rem;
      height: 0.66rem;
      margin: 0 0.24rem;
      @include raduis(66rem);
      overflow: hidden;
    }
    .title_con{
      color: $titleCol;
    }
  }
  .order_list_con{
    height: 2.1rem;
    border-bottom: 1px solid $greyCol;
    padding: 0.2rem 0.26rem;
    @include box-sizing(border-box);
    .select_icon{
      margin-top: 0.6rem;
    }
    .img_con{
      width: 1.92rem;
      height:1.5rem;
      margin:0 0.24rem;
      img{
        width: 100%;
        height: 100%;
      }
    }
    .goods_msg{
      width: 58%;
      font-size: 0.2rem;
      color:#333 ;
      .discount_msg{
        font-size: 0.14rem;
        color: $greyCol;
        margin:0.01rem 0 ;
          b{
            color: $specialRedCol;
          }
      }
      .price_con{
        font-size: 0.28rem;
        margin-top: 0.08rem;
        span{
          font-size: 0.3rem;
          color: $mainColor;
          &.algorithm_con{
            font-size: 0.2rem;
            color: #333;
            width: 1.10rem;
            line-height: 0.24rem;
            border: 1px solid $greyCol;
            border-radius: 2rem;
            .sub,.add{
              width: 0.24rem;
              height: 0.24rem;
            }
          }
        }
      }
    }
  }
  .freight{
    height: 0.8rem;
    line-height: 0.8rem;
    .no_freight{
      line-height: 0.8rem;
      text-align: right;
      width: 100%;
      padding: 0 0.26rem;
      font-weight: bold;
      @include box-sizing(border-box);
    }
    .in_freight{
      padding: 0 0.26rem;
      @include box-sizing(border-box);
    }
  }
}
.total_sub{
  height: 2rem;
  background: #fff;
  width: 100%;
  bottom: 0;
  left: 0;
  padding:0 0.1rem 0 0.26rem;
  @include box-sizing(border-box);
  .total_sub_left{
    height: 2rem;
    h4{
      height: 2rem;
      font-size: 0.3rem;
      font-weight: bold;
      line-height: 1.2rem;

    }
    b{
      color: $specialRedCol;
      font-size: 0.36rem;
    }
    div{
      display: inline-block;
      height: 2rem;
      font-size: 0.24rem;
      font-weight: bold;
      line-height: 1.2rem;
      color:$greyCol ;
      b{
        font-size: 0.3rem;
      }
    }
    .order_btn{
      margin:0;
    }
  }
}
.shoppingCar_empty{
  width: 200px;
  height: 216px;
  background-position:0  -90px;
  @include center();
}
</style>
