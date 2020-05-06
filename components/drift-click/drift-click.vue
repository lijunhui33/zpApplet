<template>
	<view class="dfcont">
		<image src="../../static/image/shouye.png" mode="aspectFit" @click="hide(1)"></image>
		<view class="cword" @click="hide(1)">
			首页
		</view>
		<image src="../../static/image/jianli.png" mode="aspectFit" @click="hide(2)"></image>
		<view class="cword" @click="hide(2)">
			简历
		</view>
		<image src="../../static/image/xiala2.png" mode="" class="xiala" @click="hide(3)"></image>
	</view>
</template>

<script>
	export default {
		name:"driftClick",
		data() {
			return {
				
			}
		},
		methods: {
			hide(i){
				this.$emit("upDfBool",i)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/job.less";
</style>
