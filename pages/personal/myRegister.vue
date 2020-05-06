<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="login_cont">
			<view class="title">
				欢迎注册求职者会员
			</view>
			<form @submit="formSubmit">
				<view class="subline input_one">
					<input type="text" v-model="phone" name="mobile" placeholder="请输入手机号"/>
					<image src="../../static/image/close3.png" @click="reset"></image>
				</view>
				<view class="qs-line"></view>
				<view class="subline input_one">
					<input type="text" v-model="imgcode" placeholder="验证码"/>
					<image :src="imgcodePic" :style="{width:width+'px',height:height+'px'}" @click="makeMaptcha"></image>
				</view>
				<view class="qs-line"></view>
				<view class="subline input_one">
					<input type="text" value="" name="mobile_vcode" placeholder="请输入验证码"/>
					<view class="getcode">
						<text v-show="show" style="color: #1787fb;margin-left: 30rpx;" @click="getCode()">获取验证码</text>
						<text v-show="!show" style="color: #e2e2e2;margin-left: 30rpx;">等待 {{count}} s</text>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="subline">
					<input type="password" value="" name="password" password="true" placeholder="请输入密码"/>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">立即注册</button>
				<navigator url="myLogin" hover-class="none" class="regjober">
					已有账号，立即登录
				</navigator>
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
					menuText:"会员注册", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				phone:"",
				show: true,
				count: '',
				timer: null,
				code_param:{},
				is_code:false,
				list_param:{},
				headers:{"Content-Type":"application/x-www-form-urlencoded"},
				code_headers:{"Content-Type":"application/x-www-form-urlencoded"},
				width:120,height:45,imgcode:"",
				imgcodePic:"",
				codetoken:""
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onLoad(option) {
			if(option.notice != undefined){
				uni.showToast({
					icon:"none",
					title:"您未注册过，请先注册"
				})
			}
			this.makeMaptcha();
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
			reset(){
				this.phone = ""
			},
			formSubmit: function(e) {
				let bool = this.verification(e);
				if(!bool){
					return false
				}
				this.list_param = e.detail.value;
				if(this.is_code){
					this.register();
				}
			},
			getCode(){
				let bool = this.vertwo();
				if(!bool){
					return false
				}
				this.code_headers = {...this.code_headers,"CAPTCHA":this.codetoken};
				this.code_param = {mobile:this.phone,data:this.imgcode};
				this.regSendSms();
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
			vertwo(){
				if(!this.phone.trim()){
					uni.showToast({
						icon:"none",
						title:"手机号不能为空"
					})
					return false
				}else if(!(/^1[3456789]\d{9}$/.test(this.phone))){
					uni.showToast({
						icon:"none",
						title:"请正确输入手机号"
					})
					return false
				}else if(!this.imgcode.trim()){
					uni.showToast({
						icon:"none",
						title:"图片验证码不能为空"
					})
					return false
				}
				return true
			},
			verification(e){
				if(!this.phone.trim()){
					uni.showToast({
						icon:"none",
						title:"手机号不能为空"
					})
					return false
				}else if(!(/^1[3456789]\d{9}$/.test(this.phone))){
					uni.showToast({
						icon:"none",
						title:"请正确输入手机号"
					})
					return false
				}else if(!this.imgcode.trim()){
					uni.showToast({
						icon:"none",
						title:"图片验证码不能为空"
					})
					return false
				}else if(!e.detail.value.mobile_vcode){
					uni.showToast({
						icon:"none",
						title:"验证码不能为空"
					})
					return false
				}else if(!e.detail.value.password){
					uni.showToast({
						icon:"none",
						title:"密码不能为空"
					})
					return false
				}else if(!this.is_code){
					uni.showToast({
						icon:"none",
						title:"验证码获取失败"
					})
					return false
				}
				return true
			},
			async regSendSms(){
				let res = await http.regSendSms(this.code_param,this.code_headers);
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
				this.headers = {...this.headers,"Authorization":"Bearer "+res};
				if(res){
					this.is_code = true;
					uni.showToast({
						icon:"none",
						title:"手机验证码发送成功！"
					})
				}
			},
			async register(){
				let res = await http.register(this.list_param,this.headers);
				try{
					uni.setStorageSync("token",res)
					if(res){
						uni.switchTab({
							url:"personal"
						})
					}
				}catch(e){
					//TODO handle the exception
				}
			},
			async makeMaptcha(){
				let res = await http.makeMaptcha();
				this.imgcodePic = res.attach;
				this.codetoken = res.token;
			},
		}
	}
</script>

<style lang="less">
	@import "../../common/person.less";
</style>
