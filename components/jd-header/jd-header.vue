<template>
	<view class="jdheader">
		<view class="one">
			{{param.cname}}
		</view>
		<view class="two">
			{{param.pay}}
		</view>
		<view class="three">
			{{param.info}}
		</view>
		<view class="btnwrap">
			<view class="btn" v-for="(item,i) in param.tag" :key="i">{{item}}</view>
		</view>
		<view class="qs-line" style="margin: 30rpx 0 16rpx 0;"/>
		<view class="lookgroup">
			<view class="person">
				<view class="perpics">
					<image src="../../static/image/finish.png" class="perimg1"></image>
					<image v-for="(item,i) in photo" :key="i" :src="item" :class="'perimg'+(i+2)"></image>
				</view>
				<view class="perlook">
					{{param.num}}人查看
				</view>
			</view>
			<view class="jubao" @click="jubao(param.cname)">
				<image src="../../static/image/warning.png" mode=""></image>
				<view class="word">
					举报
				</view>
				<image src="../../static/image/btnback.png" mode="aspectFit"></image>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"jdHeader",
		props:['param','photo'],
		data() {
			return {
				
			}
		},
		methods: {
			jubao(s){
				this.$emit("toReport",s)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/job.less";
</style>
