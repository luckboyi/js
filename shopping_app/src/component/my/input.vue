
<template>
	<h5 class="content-header">图片列表</h5>
	<div class="image-list">
		<div class="list-default-img" v-show="isPhoto" @click.stop="addPic">
			<img src="./images/icon_photo.png" />
			<span>请选择或者拍照上传照片</span>
			<input type="file" accept="image/jpeg,image/jpg,image/png" capture="camera" @change="onFileChange"  style="display: none;">
		</div>
		<ul class="list-ul" v-show="!isPhoto">
			<li class="list-li" v-for="(iu, index) in imgUrls">
				<a class="list-link" @click='previewImage(iu)'>
					<img :src="iu">
				</a>
				<span class="list-img-close" @click='delImage(index)'></span>
			</li>
			<li class="list-li-add">
				<span class="add-img" @click.stop="addPic"></span>
			</li>
		</ul>
	</div>
    <div class="add-preview" v-show="isPreview" @click="closePreview">
		<img :src="previewImg">
	</div>

</template>

<script>

	export default {
		data: function () {
			return {
				imgUrls: [],
				urlArr: [],
				isPhoto: true,
				btnTitle: '',
				isModify: false,
				previewImg:'',
				isPreview: false
			}
		},
		watch: {
			imgUrls: 'toggleAddPic'
		},
		methods: {
			toggleAddPic: function() {
				let vm = this;
				if(vm.imgUrls.length >= 1) {
					vm.isPhoto = false;
				} else {
					vm.isPhoto = true;
				}
			},
			addPic: function(e) {
				let vm = this;
				$('input[type=file]').trigger('click');
				return false;
			},
			onFileChange: function(e) {
				var files = e.target.files || e.dataTransfer.files;
				if(!files.length) return;
				this.createImage(files, e);
			},
			createImage: function(file, e) {
				let vm = this;
				lrz(file[0], { width: 480 }).then(function(rst) {
					vm.imgUrls.push(rst.base64);
					return rst;
				}).always(function() {
				// 清空文件上传控件的值
				e.target.value = null;
			});
			},
			delImage: function(index) {
				let vm = this;
				let btnArray = ['取消', '确定'];
				mui.confirm('确定删除该图片?','提示', btnArray, function(e) {
					if (e.index == 1) {
						vm.imgUrls.splice(index, 1);
					}
				})

			},
			previewImage: function(url){
				let vm = this;
				vm.isPreview = true;
				vm.previewImg = url;
			},
			closePreview: function(){
				let vm = this;
				vm.isPreview = false;
				vm.previewImg = "";
			},
			saveImage: function(){
				let vm = this;
				let urlArr = [],
				imgUrls = this.imgUrls;

				for(let i = 0; i < imgUrls.length; i++) {
					if(imgUrls[i].indexOf('file') == -1) {
						urlArr.push(imgUrls[i].split(',')[1]);
					} else {
						urlArr.push(imgUrls[i]);
					}
				}

				//数据传输操作
			}
		}
	}
</script>
