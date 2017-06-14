<template lang="html">
  <div class="delete_con pr" v-if="tips.isshow">
    <div class="in_delete_con pa tac pr">
      <div class="icon close_icon pa" @click="tips.isshow = false"></div>
      <div class=" icon_con" >
        <i class="icon" :class="[tips.classObj]" v-if="tips.classObj !=''"></i>
      </div>
      <p>{{tips.msg}}</p>
      <div class="tips_btn">
        <div @click= "sureFn(tips.callback)">确定</div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props:["tips"],
  methods:{
    sureFn(callback){
      var that = this;
      that.tips.isshow = false;
      if(typeof callback === 'function'){
        callback();
      }
    },
  }
}
</script>

<style lang="scss" scoped>
@import "../../style/base.scss";
.delete_con{
  position: fixed;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.5);
  z-index: 999;
  top:0;
  left: 0;
  .in_delete_con{
    width:80%;
    background: #fff;
    padding: 0.3rem 0 0.3rem 0;
    @include center();
    @include raduis(10px);
    .icon_con{
      width: 100%;
      padding: 1rem 0 0.3rem 0;
    }
    .close_icon {
      right: 10px;
      top:10px;
    }
    p{
      font-size: 0.36rem;
      color: $mainColor;
      margin:0 0 0.3rem 0;
      width: 90%;
      height: 0.8rem;
      line-height: 0.5rem;
      display: inline-block;
    }
    .tips_btn{
      div{
        display: inline-block;
        width: 80%;
        line-height: 0.9rem;
        font-size: 0.4rem;
        color: #fff;
        background: $mainColor;
        @include raduis(6px);
      }
    }
    .delete_btn{
      width: 100%;
      ul{
        display: inline-block;
        margin-top: 0.2rem;
        font-size: 0.3rem;
        li{
          margin: 0 0.2rem;
          width: 1.2rem;
          line-height: 0.6rem;
          color: #fff;
          background: $mainColor;
          @include raduis(4px);
          &:hover{
            opacity: 0.8;
          }
        }
      }
    }
  }
}
</style>
