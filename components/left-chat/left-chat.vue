<template>
	<view>
		<view class="addtime">
			{{param.addtime}}
		</view>
		<view :class="param.status==1?'right_chat':'left_chat'">
			<image :src="param.src" mode=""></image>
			<view class="bubble">
				<view class="block"></view>
				<view class="">
					{{param.cont}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"leftChat",
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
	@import "../../common/chat.less";
</style>
