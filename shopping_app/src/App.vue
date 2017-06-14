<template>
  <div id="app">
    <router-view ></router-view>
  </div>
</template>

<script>
export default {
  name: 'app',
  data () {
    return {
      msg: 'Welcome to Your Vue.js App'
    }
  }
}
</script>
<style>
</style>
<style lang="scss">
@import './style/base.css' ;
$borderCol:#eaeaea;
$mainColor:#ff6d70;
.home_icon,.buy_icon,.order_icon,.my_icon{
  width: 28px;height: 20px;
  background-position: 0 0;
  display: inline-block;
  left:50%;
  margin-left: -14px;
  top:6px;
}
.buy_icon{background-position: -28px 0;}
.order_icon{background-position: -56px 0;}
.my_icon{background-position: -84px 0;}
@mixin box-sizing($sizing){
  -webkit-box-sizing:$sizing;
       -moz-box-sizing:$sizing;
            box-sizing:$sizing;
}
#app{
  width: 100%;
  height: 100%;
  overflow: hidden;
  .nav{
    position: fixed;
    z-index: 999;
    bottom:0;
    width:100%;
    height: 1rem;
    background: #fff;
    border-top: 1px solid $borderCol;
    @include box-sizing(border-box);
    li{
      font-size:0.16rem;
      width:25%;
      padding-top: 28px;
      &.router-link-active{
        color:$mainColor;
        .icon{
          background-position-y:-20px;
        }
      }
    }
  }
}
</style>
