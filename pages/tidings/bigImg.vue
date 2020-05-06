<template>
	<view class="">
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<image :src="imgpath" mode="aspectFit" :style="'height:'+ht+'px;width:670rpx;margin:0 40rpx'" ></image>
	</view>
</template>

<script>
	export default{
		onLoad(option) {
			if(option.path != undefined){
				this.imgpath = option.path
			}
			try {
			    const res = uni.getSystemInfoSync();
				this.ht = res.windowHeight;
			} catch (e) {
			    // error
			}
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"我的图片", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				imgpath:"",
				ht:1000
			}
		},
		methods:{
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.navigateBack({
				    delta: 1,
				    animationType: 'pop-out',
				    animationDuration: 200
				});
			},
		}
	}
</script>

<style>
</style>
