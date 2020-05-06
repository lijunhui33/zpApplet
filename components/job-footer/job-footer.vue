<template>
	<view class="jdfooter">
		<button open-type="share" class="btn" @click="opt(1)">
			<image src="../../static/image/fenxiang.png" class="topimg"></image>
			<view>分享</view>
		</button>
		<view class="btn" @click="opt(2)">
			<image :src="param.favor?'/static/image/shoucang2.png':'/static/image/shoucang.png'" class="topimg"></image>
			<view>收藏</view>
		</view>
		<view class="sendres" @click="opt(3)">
			{{param.is_apply?"打电话":"投简历"}}
		</view>
		<view class="livechat" @click="opt(4)">
			和TA聊
		</view>
	</view>
</template>

<script>
	export default {
		name:"jobFooter",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			opt(index){
				this.$emit("wholeOpt",index)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/job.less";
</style>
