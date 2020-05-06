<template>
	<view class="person-header">
		<view class="container">
			<image :src="perInfo.src" mode="" class="headericon"></image>
			<view>
				<view class="cont">
					<view class="person-name">
						{{perInfo.name}}
					</view>
					<view style="margin-left: 20rpx;">
						{{perInfo.info}}
					</view>
				</view>
				<view class="person-cont">
					{{perInfo.cont}}
				</view>
			</view>
		</view>
		<view class="edit">
			<image src="../../static/image/edit.png" mode="" @click="toRes"></image>
		</view>
	</view>
</template>

<script>
	export default {
		name:"resumeHeader",
		props:{
			perInfo:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toRes(){
				this.$emit("editBasic")
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/resume.less";
</style>
