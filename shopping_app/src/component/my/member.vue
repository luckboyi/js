<template lang="html">
  <div class="memeber">
    <div class="account_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>实货会员</h3>
    </div>
    <div class="member_con tac">
      <table>
        <tr>
          <th>会员制</th>
          <th>会员制</th>
          <th>会员制</th>
        </tr>
        <tr>
          <td>VIP1</td>
          <td>
            <p>新注册用户</p>
            <p>免费注册一个月</p>
          </td>
          <td>
            <p>购买时允许填写1个</p>
            <p>收货地址，允许修改</p>
          </td>
        </tr>
        <tr>
          <td>VIP2</td>
          <td>

            15元/月
          </td>
          <td>
            <p>购买时允许填写2个</p>
            <p>收货地址，允许修改</p>
          </td>
        </tr>

      </table>
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return{

    }
  },
  methods:{
    goBack(){
      window.history.go(-1);
    }
  }
}
</script>

<style lang="scss" scoped>
@import "../../style/base.scss";
.memeber{height: 100%;}
.member_con{
  padding:  0.15rem;

  height: 100%;
  background: $greyBg;
  @include  box-sizing(border-box);
  table{
    width: 100%;
    border: 1px solid #ccc;
    th,td{
      width: 25%;
      border: 1px solid #ccc;
      line-height: 1rem;
      p{
        line-height: 0.3rem;
      }
    }
    td:nth-child(2),td:nth-child(3){
      width: 35%;
    }

  }
}
</style>
