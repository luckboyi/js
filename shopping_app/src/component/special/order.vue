<template lang="html">
  <div class="order_container">
    <div class="account_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>聚宝订单</h3>
    </div>
    <div class="order_sel tac">
      <ul class="clearfix">

        <li class="fl" :class="{cur:isCur}" @click="selThreeFn()">近三个月订单</li>
        <li class="fl" :class="{cur:!isCur}"  @click="selAllFn()">全部订单</li>
      </ul>
    </div>
    <div class="order_list_con">
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return{
      isCur:true,
    }
  },
  methods:{
    goBack(){
      window.history.go(-1);
    },
    selThreeFn(){
      var that = this;
      that.isCur = ! that.isCur;
      var orderDate ;
      that.$http.get('./../../../data/order.txt').then(response => {
        that.$router.app.$emit('updateOrder',response.body.orderlist);
      }, response => {
        // error callback
      });
    },
    selAllFn(){
      var that = this;
      that.isCur = ! that.isCur;
      that.$http.get('./../../../data/order2.txt').then(response => {
        that.$router.app.$emit('updateOrder',response.body.orderlist);
      }, response => {
        // error callback
      });
    }
  }
}
</script>

<style lang="scss" scoped>
@import "../../style/base.scss";
.order_container{
  height: 100%;
  .order_sel{
    li{
      width: 50%;
      line-height: 0.7rem;
      color: $greyCol;
      &.cur{
        background: $mainColor;
        color: #fff;
      }
    }
  }
  .order_list_con{
    height: 100%;
    padding-bottom: 1.5rem;
    overflow-x: auto;
  }
}
</style>
