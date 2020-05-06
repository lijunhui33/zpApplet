<template>
	<view>
		<view class="cs_cont" @click="select(param.mode,resultInfo)">
			<view class="one">
				<view v-show="param.require" class="red">*</view>
				<view class="label">{{param.label}}</view>
			</view>
			<view class="one">
				<view class="pla">
					{{param.pla}}
				</view>
				<view v-show="false">{{param.val}}</view>
				<image v-show="param.arrow" src="../../static/image/back3.png" mode=""></image>
			</view>
		</view>
		<view class="qs-line"></view>
	</view>
</template>

<script>
	export default {
		name:"coverSelect",
		props:['param','resultInfo'],
		data() {
			return {
				
			}
		},
		methods: {
			select(str,s){
				this.$emit("csClick",str,s);
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/subscribe.less";
</style>
