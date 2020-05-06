<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ep_cont">
			<image src="../../static/image/updatePwd.png" mode="aspectFit" @click="toNext(1)"></image>
			<image src="../../static/image/updatePhone.png" mode="aspectFit" @click="toNext(2)"></image>
		</view>
	</view>
</template>

<script>
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"账号管理", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				
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
			toNext(s){
				if(s == 1){
					uni.navigateTo({
						url:"updatePwd"
					})
				}else{
					uni.navigateTo({
						url:"updatePhone"
					})
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
	image{
		width: 670rpx;
		height: 306rpx;
	}
</style>
