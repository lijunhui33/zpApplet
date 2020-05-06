<template>
	<view>
		<view class="plist" @click="close(1)">
			<view class="">
				刷新简历
			</view>
			<image src="../../static/image/shuaxin2.png" mode=""></image>
		</view>
		<view class="plist" @click="close(2)">
			<view class="">
				隐私设置
			</view>
			<image src="../../static/image/yingsishezhi.png" mode=""></image>
		</view>
		<view class="plist" @click="close(3)">
			<view class="">
				求职管理
			</view>
			<image src="../../static/image/qiuzhiguanli.png" mode=""></image>
		</view>
		<view class="plist" @click="close(4)">
			<image src="../../static/image/close2.png" mode="" ></image>
		</view>
	</view>
</template>

<script>
	export default {
		name:"popup",
		data() {
			return {
				
			}
		},
		methods: {
			close(i){
				this.$emit('childByValue', i)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/resume.less";
</style>
