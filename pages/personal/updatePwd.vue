<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="shield_word">
			密码修改后，请记住新密码以便下次登录时使用
		</view>
		<view class="shield_cont">
			<form @submit="formSubmit" @reset="formReset">
				<view class="ci_cont">
					<view class="one">
						<view class="label">新密码</view>
					</view>
					<input type="password" value="" name="password" placeholder="请输入" placeholder-style="text-align:right" style="text-align: right;"/>
				</view>
				<view class="qs-line"></view>
				<view class="ci_cont">
					<view class="one">
						<view class="label">确认密码</view>
					</view>
					<input type="password" value="" name="confirm" placeholder="请输入" placeholder-style="text-align:right" style="text-align: right;"/>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存</button>
			</form>
		</view>
	</view>
</template>

<script>
	import coverInput from "../../components/cover-input/cover-input"
	import http from "../../server/api-person.js"
	export default {
		components:{
			coverInput
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"修改密码", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				upList:[
					{label:"新密码",req:false,name:"new_pwd",val:""},
					{label:"确认密码",req:false,name:"sure_pwd",val:""},
				],
				list_param:{},
				headers:{},
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
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
				this.list_param = e.detail.value;
				this.editPassword();
			},
			async editPassword(){
				let res = await http.editPassword(this.list_param,this.headers);
				if(res == 1){
					uni.showToast({
						icon:"none",
						title:"修改成功"
					});
					let that = this;
					setTimeout(()=>{
						that.customConduct();
					},1500)
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/extendPer.less";
</style>
