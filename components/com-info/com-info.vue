<template>
	<view class="dcont">
		<view class="com_cont" @click="tonav(param.id)">
			<image :src="param.logo" mode=""></image>
			<view class="info">
				<view class="one">
					<view class="view_com_name">
						<view>
							{{param.cname}} 
						</view>
						<image v-if="param.huiyuan==1"  src="../../static/image/huiyuan.png" mode="aspectFit"></image>
						<image v-if="param.renzheng==1" src="../../static/image/renzheng.png" mode="aspectFit"></image>
					</view>
					<view class="winfo">{{param.cont}}</view>
				</view>
				<image src="../../static/image/back3.png" mode=""></image>
			</view>
		</view>
		<view style="width: 100%;height: 1px;background: #f5f5f5;margin: 30rpx 0 10rpx 0;"></view>
		<view>
			<view class="new_left_text">
				<image :src="param.img_src" class="qre_four_img" mode="aspectFit"></image>
				<view :class="param.img_use" style="width: 630rpx;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">
					{{param.address}}
				</view>
			</view>
		</view>
		<map 
			:latitude="param.latitude" 
			:longitude="param.longitude" style="width: 670rpx;height: 180rpx;" 
			:markers="param.covers"
			@tap="tobigMap()"></map>
	</view>
</template>

<script>
	import newText from "../new-text/new-text"
	export default {
		name:"comInfo",
		components:{
			newText
		},
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			tonav(id){
				this.$emit("navtodel",id);
			},
			tobigMap(){
				console.log("tobig");
				uni.navigateTo({
					url:`/pages/index/bigMap/bigMap?lng=${this.param.longitude}&lat=${this.param.latitude}`
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/job.less";
</style>
