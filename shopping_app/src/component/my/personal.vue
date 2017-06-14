<template lang="html">
  <div class="personal_center">
    <div class="user_container tac pr">
      <div class="user_bg pa pr"  :style="{background:'url('+userdata.img+') center no-repeat '}">
        <div class=""></div>
      </div>
      <div class="user_con pa">
        <router-link tag="div" to="/personalmsg" class="img_con" :style="{background:'url('+userdata.img+') center no-repeat '}">
        </router-link>
        <div class="user_msg_con">
          <p>{{userdata.name}}</p>
          <div class="pr user_address"><i  class="icon pa location_icon"></i>来自{{userdata.address}}</div>
        </div>
      </div>
    </div>
    <div class="editor_con">
      点击头像，完善您的个人资料，让别人好好认识你把！
    </div>
    <div class="user_list_con">
      <ul>
        <router-link tag="li" to="/member" class="pr"><i class="icon member_icon pa"></i>聚宝会员<i class="icon go_icon pa"></i></router-link>
        <router-link tag="li" to="/address" class="pr"><i class="icon addresscar_icon pa"></i>收货地址<i class="icon go_icon pa"></i></router-link>
      </ul>
    </div>
    <foot></foot>

  </div>
</template>

<script>

export default {
  data(){
    return{
      userdata:{
        img:'http://zhengyi.date/plante/src/image/gameimg1.png',
        name:'郑毅',
        address:'上海是电热嗯嗯'
      }
    }
  },
  methods:{

  }
}
</script>

<style lang="scss" scoped>
@import "../../style/base.scss";
.personal_center{

  .user_list_con{
    li{
      width: 100%;
      padding: 0 0.1rem 0 1rem;
      height: 1.18rem;
      line-height: 1.18rem;
      border-bottom: 1px solid $borderCol;
      @include box-sizing(border-box);

    }

  }
  .user_con{
      @include center();
      top: 40% !important;
      color: $mainColor;
      .user_msg_con{
        p{
          font-size: 0.3rem;
          margin: 0.3rem 0 0.2rem 0;
        }
      }
  }
  .editor_con{
    width: 100%;
    line-height: 0.88rem;
    background: $mainColor;
    padding: 0 0.1rem;
    color: #fff;
    font-size: 0.24rem;
    @include box-sizing(border-box);
  }
  .location_icon{
    width: 11px;
    height: 15px;
    background-position:-28px -70px;
    left: -15px;
  }
  .user_address{
    display: inline-block;
  }
  .img_con{
    width:1.5rem;
    height:1.5rem;
    border: 2px solid #fff;
    display: inline-block;
    padding: 1px;
    @include raduis(1.5rem);
  }
  .user_container{
    height: 5.2rem;
    width: 100%;
    .user_bg{
      width: 100%;
      height: 100%;
      background-size: cover !important;
      z-index: 0;
      div{
        -webkit-filter: blur(3px);
        -moz-filter: blur(3px);
        -ms-filter: blur(3px);
        filter: blur(3px);
        background: inherit;
        cursor: -webkit-grab; cursor: -moz-grab; cursor: grab;
        overflow: hidden;
        width: 100%;
        height: 100%;
      }
    }
  }
}

</style>
