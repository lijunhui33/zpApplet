<template>
	<view class="rimg">
		<image class="under" :src="param.src" mode=""></image>
		<view style="flex: 3;">
			<view class="contentview">
				<view class="one">
					{{param.title}}
				</view>
				<image src="../../static/image/edit.png" mode="" @click="toName(param.type,param.id)"></image>
			</view>
			<view style="color: #888888;font-size: 30rpx;">
				{{param.audit}}
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"resumeImg",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(t,id){
				let data = {type:t,id:id};
				this.$emit("update",data);
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/resume.less";
	@import "../../common/drop.less";
</style>
