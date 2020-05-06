<template>
	<view class="jobone_cont">
		<view class="top_block" v-if="!param.tips">
			<view style="flex: 3;" @click="toJob(param.id)">
				<view class="onew">{{param.jname}}</view>
				<view class="twow">{{param.pay}}</view>
				<view class="threew">{{param.cname}}</view>
			</view>
			<view>
				<view class="four" :style="param.is_do==1?'color: #cacaca;':'color: #ef7a2c;'">{{param.is_do==1?"已查看":"未查看"}}</view>
				<view class="five" @click="toName(param.did)">
					<image src="../../static/image/delete.png" mode=""></image>
					<view class="">
						删除
					</view>
				</view>
			</view>
		</view>
		<view :class="param.is_close==1?'bottom':'bottom_other'" v-if="!param.tips">
			<view>{{param.addtime}}</view>
			<view class="yesint">{{param.is_close==1?"可面试":"已过期"}}</view>
		</view>
		<view class="top_block" v-if="param.tips">
			{{param.tips}}
		</view>
		<view class="qs_space"></view>
	</view>
</template>

<script>
	export default {
		name:"jobOne",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(str){
				this.$emit("delItem",str)
			},
			toJob(id){
				this.$emit("toDetail",id)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendRes.less";
</style>
