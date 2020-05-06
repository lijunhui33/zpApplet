<template>
	<view>
		<view class="wrap_cont">
			<view class="one">
				{{param.name}} 
			</view>
			<view class="two">
				{{param.val}} 
			</view>
		</view>
		<view class="qs-line"></view>
	</view>
</template>

<script>
	export default {
		name:"attLi",
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

