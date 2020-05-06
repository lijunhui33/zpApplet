<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ec_cont">
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">原手机号</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="old_ph" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">新手机号</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="new_ph" name="mobile" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">图形验证码</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="imgcode" placeholder="请输入" style="width: 180rpx;"/>
						<image :src="imgcodePic" :style="{width:width+'px',height:height+'px'}" @click="makeMaptcha"></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">验证码</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="code" name="mobile_code" placeholder="请输入" style="width: 240rpx;"/>
						<text v-show="show" style="color: #1787fb;margin-left: 30rpx;" @click="getCode()">获取验证码</text>
						<text v-show="!show" style="color: #e2e2e2;margin-left: 30rpx;">等待 {{count}} s</text>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存</button>
			</form>
		</view>
	</view>
</template>

<script>
	import http from "../../server/api-person.js";
	import {encrypt} from "../../utils/useList.js";
	export default {
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"修改手机号", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				headers:{},
				old_ph:0,
				new_ph:"",
				code:"",
				show: true,
				count: '',
				timer: null,
				code_param:{},
				up_param:{},
				is_code:false,
				vcode:"",
				width:120,height:45,imgcode:"",
				imgcodePic:"",
				codetoken:"",
				code_headers:""
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
			this.code_headers = {"Authorization":"Bearer "+token};
			this.oldMobile();
			this.makeMaptcha();
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
			formSubmit: function(e) {
				if(this.is_code){
					this.headers = {...this.headers,VCODE:this.vcode};
					this.up_param = e.detail.value;
					this.editMobile();
				}
			},
			async oldMobile(){
				let res = await http.oldMobile({},this.headers);
				this.old_ph = res
			},
			getCode(){
				this.code_headers = {...this.code_headers,"CAPTCHA":this.codetoken};
				this.code_param = {mobile:this.new_ph,data:this.imgcode};
				this.sendmobileSms();
				const TIME_COUNT = 60;
				if (!this.timer) {
					this.count = TIME_COUNT;
					this.show = false;
					this.timer = setInterval(() => {
					if (this.count > 0 && this.count <= TIME_COUNT) {
						this.count--;
					} else {
						this.show = true;
						clearInterval(this.timer);
						this.timer = null;
					}
					}, 1000)
				}
			},
			async sendmobileSms(){
				let res = await http.sendmobileSms(this.code_param,this.code_headers);
				if(res.status == 900){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
					this.makeMaptcha();
					if(this.timer != null){
						this.show = true;
						clearInterval(this.timer);
						this.timer = null;
					}
					return false;
				}
				if(res){
					this.vcode = res;
					this.is_code = true;
					// #ifndef MP-BAIDU
					this.refresh();
					// #endif
					// #ifdef MP-BAIDU
					uni.setStorageSync("imgcode","验证码发送");
					// #endif
					uni.showToast({
						icon:"none",
						title:"验证码发送成功"
					})
				}
			},
			async editMobile(){
				let res = await http.editMobile(this.up_param,this.headers);
				if(res == 1){
					uni.removeStorageSync("imgcode");
					uni.showToast({
						icon:"none",
						title:"修改成功"
					});
					let that = this;
					setTimeout(()=>{
						that.customConduct();
					},1500)
				}
			},
			async makeMaptcha(){
				let res = await http.makeMaptcha();
				this.imgcodePic = res.attach;
				this.codetoken = res.token;
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
