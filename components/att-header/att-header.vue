<template>
	<view class="attHd">
		<view class="one">
			企业实地认证报告
		</view>
		<view class="two">
			{{param.cname}}
		</view>
		<view class="">
			<text style="color: #999999;padding-right: 12rpx;">认证时间</text>{{param.addtime}}
		</view>
		<image src="../../static/image/gongzhang.png" mode=""></image>
	</view>
</template>

<script>
	export default {
		name:"attHeader",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/attestation.less";
</style>
