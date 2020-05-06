<template>
	<view>
		<view class="addtime">
			{{param.addtime}}
		</view>
		<view class="right_chat" @click="toResume">
			<image :src="param.src" mode=""></image>
			<view class="bubble">
				<view class="block"></view>
				<view class="tonow">
					<view>
						{{param.resumename}}<image :src="param.sex==1?'/static/image/gender_m.png':'/static/image/gender_w.png'" mode=""></image>
					</view>
					<view class="bottom">
						{{param.info}}
					</view>
					<view class="qs-line"></view>
					<view class="bottom">
						意向地区:{{param.district}}
					</view>
					<view class="bottom">
						意向职位:{{param.intention}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"rightTwo",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toResume(){
				uni.switchTab({
					url:"/pages/resume/resume"
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/chat.less";
</style>
