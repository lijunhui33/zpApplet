<template>
	<view class="renzheng">
		<image src="../../static/image/renzheng2.png" mode=""></image>
		<view class="group">
			<new-text v-for="(item,i) in param" :key="i" :dog="item"></new-text>
		</view>
	</view>
</template>

<script>
	import newText from "../new-text/new-text"
	export default {
		name:"attestation",
		components:{
			newText
		},
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
	@import "../../common/job.less";
</style>
