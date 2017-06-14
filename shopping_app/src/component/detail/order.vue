<template lang="html">
  <div class="order_con clearfix" id="order_con">
    <div class="fl clearfix">
      <h4 class="fl">{{orderdata.state}}</h4>
      <span class=“fl” v-for="(item,i) in orderdata.nums" :class="{cur:item.iscur}" @click="selFn(i)">{{item.num}}</span>
    </div>
    <div class="fr clearfix" >
      <router-link tag="div" to="/shoppingCar"  class="order_car fl" @click="carFn()" >
        <div class="in_order_car pr">
          <i class="icon car_icon pa"></i>
          <b class="num pa tac" v-if="orderdata.paynum >0">{{orderdata.paynum}}</b>
        </div>
      </router-link>
      <router-link tag='div' to="/account" class="order_btn fl tac" @click="submitBtn()">
        <div class="in_order_btn tac">结算</div>
      </router-link>
    </div>
  </div>
</template>
<script>
export default {
  name:'order',
  props:["orderdata"],
  methods:{
    selFn(i){
      var that = this ;
      that.orderdata.nums[i].iscur = ! that.orderdata.nums[i].iscur;
      for(var k = 0 ; k<that.orderdata.nums.length; k++ ){
        if(k != i){
          that.orderdata.nums[k].iscur = !that.orderdata.nums[i].iscur;
        }
      }
      // if(that.orderdata.nums[i].iscur == true){
      //   that.orderdata.paynum = parseInt(that.orderdata.paynum)+1;
      // }else{
      //   that.orderdata.paynum = parseInt(that.orderdata.paynum)-1;
      //   if(that.orderdata.paynum <=0){
      //     that.orderdata.paynum =0;
      //   }
      // }
    },
    carFn(){
      //alert(1)

    },
    submitBtn(){
      alert(2)
    }
  }
}
</script>

<style lang="scss">
  $mainCol:#ff6d70;

  @mixin center($px1,$px2) {
    left:$px1;
    top:$px2;
    transform: translate(-$px1,-$px2);
    -webkit-transform: translate(-$px1,-$px2);
  }
  @mixin radius($px){
    border-radius:$px;
    -webkit-border-radius:$px;
  }
  .car_icon{
    height: 26px;
    width: 26px;
    background-position: -360px 0;
    @include center(50%,50%);
  }
  .order_con{
    position: fixed;
    bottom: 0;
    height: 0.9rem;
    background: #fff;
    width: 100%;
    .in_order_car{
      .num{
        width: 0.4rem;
        height: 0.4rem;
        font-size: 0.02rem;
        line-height: 0.4rem;
        border: 1px solid $mainCol;
        background: #fff;
        right: 0;
        top:-0.2rem;
        color: $mainCol;
        @include radius(100%);
      }
    }
    .fl{
      h4{
        font-size: 0.28rem;
        line-height: 0.9rem;
        text-indent: 0.35rem;
      }
      span{
        display: inline-block;
        padding: 0.1rem 0.2rem;
        color: #9e9e9e;
        border: 1px solid #9e9e9e;
        margin: 0.2rem 0 0 0.2rem;
        @include radius(0.08rem);
        &.cur{
          background: $mainCol;
          color: #fff;
          border:1px solid $mainCol;
        }
      }
    }
    .fr{
      margin-top: -0.2rem;
      div.fl{
        border: 1px solid $mainCol;
        padding:1px;
        @include radius(50px);
        color:#fff;
        margin: 0 0.1rem;
        div{
          width: 0.9rem;
          height: 0.9rem;
          background: $mainCol;
          @include radius(50px);
          line-height: 0.9rem;
        }
      }
    }
  }
  .order_btn {
    border: 1px solid $mainCol;
    padding:1px;
    @include radius(50px);
    color:#fff;
    margin: 0 0.1rem;
    .in_order_btn {
      width: 0.9rem;
      height: 0.9rem;
      background: $mainCol;
      @include radius(50px);
      line-height: 0.9rem;
    }
  }
</style>
