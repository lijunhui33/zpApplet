<template>
	<view>
		<navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom>
		<view class="login_cont">
			<view class="title">
				欢迎登录{{webname}}
			</view>
			<form @submit="formSubmit">
				<view class="subline input_one">
					<input type="text" v-model="phone" name="username" placeholder="请输入用户名/手机号"/>
					<image src="../../static/image/close3.png" mode="" @click="resetphone"></image>
				</view>
				<view class="qs-line"></view>
				<view class="subline">
					<input type="text" value="" name="password" password="true" placeholder="请输入账号密码"/>
				</view>
				<view class="qs-line"></view>
				<navigator url="myCode" hover-class="none" class="code">
					使用手机验证码登录 >
				</navigator>
				<button form-type="submit" class="mysubmit">立即登录</button>
				<navigator url="myRegister" hover-class="none" class="regjober">
					免费注册求职者会员
				</navigator>
			</form>
		</view>
	</view>
</template>

<script>
	import http from "../../server/api-person.js"
	import httptwo from "../../server/api-index.js";
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"会员登录", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				phone:"",
				list_param:{},
				is_send:true,
				is_login:false,
				webname:""
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onLoad(option) {
			this.webCfg();
			if(option.id != undefined){
				uni.showToast({
					icon:"none",
					title:"请绑定"
				});
				this.list_param = {bind_id:option.id};
			}else if(option.login != undefined){
				this.is_login = option.login;
			}
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				if(!this.is_login){
					uni.navigateBack({
						delta: 1,
						animationType: 'pop-out',
						animationDuration: 200
					});
				}else{
					uni.switchTab({
						url:"/pages/index/index"
					})
				}
			},
			async webCfg(){
				let res = await httptwo.webCfg();
				this.webname = res;
			},
			async formSubmit(e) {
				this.verification(e);
				if(this.is_send){
					let formdata = e.detail.value;
					this.list_param = {...this.list_param,...formdata};
					let headers = {"content-type":"application/x-www-form-urlencoded"};
					let res = await http.login(this.list_param,headers)
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
				}
			},
			verification(e){
				if(!this.phone.trim()){
					uni.showToast({
						icon:"none",
						title:"用户名/手机号不能为空"
					})
					this.is_send = false
				}else if(!e.detail.value.password){
					uni.showToast({
						icon:"none",
						title:"密码不能为空"
					})
					this.is_send = false
				}else{
					this.is_send = true
				}
			},
			resetphone(){
				this.phone = ""
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/person.less";
</style>
