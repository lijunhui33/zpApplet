<template>
	<view style="height: 830rpx;">
		<image src="../../static/image/empty_list.png" mode="aspectFit"></image>
		<view class="notice">
			定位失败没有更多信息
		</view>
	</view>
</template>

<script>
	export default {
		name:"emptyListFail",
		data() {
			return {
				
			}
		},
		methods: {
			
		}
	}
</script>

<style scoped>
image{
	width: 100%;
	height: 250rpx;
	margin: 200rpx 0 80rpx 0;
}
.notice{
	text-align: center;
	font-size: 36rpx;
	color: #aeaeae;
}
</style>
