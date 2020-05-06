<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ep_cont">
			<view class="notice">
				已添加 {{usenum}} 张，还可添加 {{count-usenum}} 张。
			</view>
			<view class="upload">
				<image :src="src" @click="upload"></image>
				<view class="">
					点击选择上传图片
				</view>
			</view>
			<view class="mysubmit" @click="formAdd">
				保存
			</view>
			<view class="deleteEdu" v-if="!subType" @click="delImg">删除该作品</view>
		</view>
	</view>
</template>

<script>
	import { pathToBase64, base64ToPath } from '../../js_sdk/gsq-image-tools/image-tools/index.js'
	import http from "../../server/api-resume.js"
	import httptwo from "../../server/api-upload.js"
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"编辑照片作品", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				headers:{},
				list_param:{},
				src:"/static/image/addimg1.png",
				count:6,
				usenum:0,
				img:"",
				subType:true,
				id:""
			}
		},
		onLoad(option) {
			this.id = option.id;
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			this.resumeImgCount();
			if(option.id != undefined){
				this.subType = false;
				this.resumeDisplayImg();
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.navigateBack({
				    delta: 1,
				    animationType: 'pop-out',
				    animationDuration: 200
				});
			},
			upload(){
				let that = this;
				uni.chooseImage({
					sourceType: ["camera", "album"],
					count: 1,
					success: (res) => {
						// #ifdef MP-WEIXIN
						 pathToBase64(res.tempFilePaths[0])
							.then(base64 => {
								let b4 = base64.substring(base64.indexOf(",") + 1);
								that.list_param = {
									base64_string:b4
								};
								that.resumeImg();
							 })
							 .catch(error => {
								console.error(error);
							 });
						// #endif
						// #ifdef MP-BAIDU
						that.resumeImgUpload(res.tempFilePaths[0]);
						// #endif
					}
				});
			},
			async resumeImgUpload(dpath){
				let room = await httptwo.resumeImgUpload(dpath,this.headers);
				this.src = room.attach;
				this.img = room.savepath;
			},
			async resumeImg(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.resumeImg(this.list_param,this.headers);
				if(res){
					this.src = res.path;
					this.img = res.img;
				}
			},
			async resumeImgCount(){
				let res = await http.resumeImgCount({},this.headers);
				this.usenum = res;
			},
			async resumeImgSave(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.resumeImgSave(this.list_param,this.headers);
				if(res){
					uni.$emit("showtoast",{msg:'上传成功'});
					let that = this;
					// #ifndef MP-BAIDU
					setTimeout(()=>{
					// #endif
						that.customConduct();
					// #ifndef MP-BAIDU
					},2000)
					// #endif
				}
			},
			async resumeDisplayImg(){
				let res = await http.resumeDisplayImg({id:this.id},this.headers);
				if(res != undefined){
					this.src = res[0].img;
				}
			},
			formAdd(){
				if(this.subType){
					this.list_param = {
						img:this.img
					}
				}else{
					this.list_param = {
						id:this.id,
						img:this.img
					}
				}
				if(this.img == ""){
					uni.showToast({
						icon:"none",
						title:"图片不能为空"
					})
					return false;
				}
				this.resumeImgSave();
			},
			async delImg(){
				let res = await http.delImg({id:this.id},this.headers);
				if(res){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
					let that = this
					setTimeout(()=>{
						that.customConduct();
					},1500)
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
