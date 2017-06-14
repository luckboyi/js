<template lang="html">
  <div class="personal_center pr">
    <div class="account_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>个人资料</h3>
    </div>
    <div class="personal_center_body">
      <ul>
        <li class="pr">
          <div class="pa input_con">
            <input type="file" accept="image/jpeg,image/jpg,image/png" name="file_head" @change="onFileChange">
          </div>
          <div class="img_con fl" :style="{background:'url('+urermsg.headimg+') no-repeat '}" >
          </div>
          <div class="img_msg fl" >
            修改头像
          </div>
          <i class="icon go_icon pa"></i>
        </li>
        <li>
          <div class="editor_name_con fl">
            昵称
          </div>
          <div class="editor_name fl pr">
            <input type="text" name="" class="pa" v-model="urermsg.nike">
          </div>
        </li>
        <li v-for="(item,index) in urermsg.option">
          <div class="editor_name_con fl">
            {{item.theme}}
          </div>
          <div class="editor_sex fl pr tac">
            <div class="clearfix">
              <div class="fl"  v-for="(list,$index) in item.themeCon" :class="{cur:$index==item.isselect}" @click="selectFn(item,$index)">{{list}}</div>
            </div>
          </div>
        </li>
        <li>
          <div class="editor_name_con fl">
            职业
          </div>
          <div class="editor_name fl pr">
            <input type="text" name="" class="pa" v-model="urermsg.profession" placeholder="填写你从事的行业">
          </div>
        </li>
      </ul>

    </div>
    <div class="set_personalmsg pa tac" @click="setBtn()">
      保存个人信息
    </div>
    <tipsComponent :tips= "tips"></tipsComponent>
  </div>
</template>

<script>

export default {
  data(){
    return{
      urermsg:{

      },
      tips:{
        isshow:false,
        msg:'',
        classObj:'suc_icon',
        callback:''
      }
    }
  },
  created(){
    this.getDate('./../../../data/userdata.txt','',this.getUserMsg);
  },
  methods:{
    getDate(url,params,callback){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          if(typeof callback === 'function'){
            //console.log(1)
            console.log(JSON.parse(response.body))
            callback(JSON.parse(response.body).data);
          }
        }, response => {
          // error callback
        });
      })
    },
    getUserMsg(data){
      let vm = this;
      vm.urermsg = data;
    },
    selectFn(obj,i){
      obj.isselect = i;
    },
    goBack(){
      this.tips={
        isshow:true,
        classObj:'erro_icon',
        msg:'还没有保存，确定退出吗？',
        callback:function(){
          window.history.go(-1);
        }
      }
    },
    setBtn(){
      this.tips={
        isshow:true,
        classObj:'suc_icon',
        msg:'保存成功，立即去个人中心',
        callback:function(){
          window.history.go(-1);
        }
      }
    },
    onFileChange(e){
      var files = e.target.files || e.dataTransfer.files;
				if(!files.length) return;
				this.createImage(files, e);
    },
    createImage: function(file, e) {
				let vm = this;
				lrz(file[0], { width: 480 }).then(function(rst) {
  					vm.urermsg.headimg = rst.base64;
  					return rst;
  				}).always(function() {
  				// 清空文件上传控件的值
  				e.target.value = null;
  			});
		},
    saveImage(){
        let vm = this;
        //数据传输操作
        let url = '';
        url = vm.imgUrls;
    }
  }
}
</script>

<style lang="scss" scoped>
@import "../../style/base.scss";
.personal_center{
  width: 100%;
  height: 100%;
  overflow: hidden;
  .input_con{
    width: 100%;
    height: 100%;
    left:0;
    top: 0;
    z-index: 99;
    input{
      width: 100%;
      height: 100%;
      opacity: 0;
    }
  }
  .personal_center_body{
    li{
      width: 100%;
      height: 1.2rem;
      padding: 0.25rem 0.1rem;
      text-indent: 1rem;
      @include box-sizing(border-box);
      border-bottom: 1px solid $borderCol;
      font-size: 0.3rem;
      color: $greyCol;
      .img_con{
        width: 0.66rem;
        height:0.66rem;
        background-size: cover;
        margin-left: 0.3rem;
        @include raduis(50px);
      }
      .img_msg,.editor_name_con{
        height: 0.66rem;
        line-height: 0.66rem;
      }
      .editor_name_con{
        text-indent: 0.4rem;

      }
      .editor_name{
        width: 80%;
        height: 100%;
        text-indent: 0.6rem;
        input{
          width: 100%;
          height: 100%;
          color: $greyCol;
        }
      }
      .editor_sex{
        margin-left: 0.6rem;
        div.fl{
          width: 1.4rem;
          height: 0.66rem;
          line-height: 0.66rem;
          text-indent: 0;
          border: 1px solid $borderCol;
          margin-right: 0.24rem;
          @include raduis(4px);
          &.cur{
            color: #fff;
            background: $mainColor;
            border-color: $mainColor;
          }
        }
      }

    }
  }
  .set_personalmsg{
    bottom: 0;
    line-height: 1rem;
    width: 100%;
    background: $mainColor;
    font-size: 0.3rem;
    color: #fff;
  }
}

</style>
