<template>
	<view>
		<view class="addtime">
			{{param.addtime}}
		</view>
		<view :class="param.status==5?'right_chat':'left_chat'">
			<image :src="param.src" mode=""></image>
			<view class="bubble">
				<view class="block"></view>
				<image :src="param.cont" mode="" class="showimg" @click="test()"></image>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"rightImg",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			test(){
				uni.navigateTo({
					url:"/pages/tidings/bigImg?path="+this.param.cont
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/chat.less";
</style>
