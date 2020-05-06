<template>
	<view class="cd_cont">
		<view class="one">
			<view>
				<view class="nwone">
					{{param.cname}}
				</view>
				<view class="group">
					<view v-for="(item,i) in param.btns" :key="i" :class="item.use">{{item.name}}</view>
				</view>
			</view>
			<image :src="param.logo" mode="aspectFit"></image>
		</view>
		<view class="nwtwo">
			{{param.info}}
		</view>
		<view class="colony">
			<view v-for="(item,i) in param.tags" :key="i">{{item}}</view>
		</view>
		<view class="qs-line" v-if="param.report==1"></view>
		<view class="bottom" v-if="param.report==1">
			<view class="nwthree">
				该企业{{param.report==1?"已":"未"}}通过实地认证
			</view>
			<view class="imggroup" @click="toName()" v-if="param.report==1">
				<view class="nwfour">
					查看认证报告
				</view>
				<image src="../../static/image/btnback.png" mode=""></image>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"comHeader",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(){
				this.$emit("toAttestation")
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/company.less";
</style>
