<template>
	<view>
		<view class="addtime">
			{{param.addtime}}
		</view>
		<view class="right_chat" @click="toJob(param.jobs_id)">
			<image :src="param.src" mode=""></image>
			<view class="bubble">
				<view class="block"></view>
				<view class="tonow">
					<view>
						{{param.jobsname}} 
					</view>
					<view class="red">
						{{param.wage}}
					</view>
					<view class="bottom">
						{{param.info}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"rightChat",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toJob(i){
				uni.navigateTo({
					url:"/pages/index/jobDetail?id="+i
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/chat.less";
</style>
