<template>
	<view  @click="toDetail(psList.id)">
		<view class="qs_view_cont" style="margin-top: 20rpx;">
			<view class="view_job_name">
				<image class="is_top" v-if="psList.is_top==1" src="../../static/image/addtop.png"></image>
				<view class="jobname">
					{{psList.job_name}}
				</view>
				<image v-if="psList.urgen==1" src="../../static/image/urgen.png"></image>
			</view>
			<view class="view_pay">
				{{psList.pay}}
			</view>
		</view>
		<view class="qs_view_cont2">
			<view class="one">
				{{psList.cont}}
			</view>
		</view>
		<view class="qs_view_cont">
			<view class="view_com_name">
				<view :class="psList.xianyan==1?'view_audit':''" style="max-width: 400rpx;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
					{{psList.com_name}}
				</view>
				<image v-if="psList.huiyuan==1" src="../../static/image/huiyuan.png" mode="aspectFit"></image>
				<image v-if="psList.renzheng==1" src="../../static/image/renzheng.png" mode="aspectFit"></image>
			</view>
			<view class="view_com_name">
				{{psList.distance}}
			</view>
		</view>
		<view class="qs_view_cont3" v-if="psList.tag.length">
			<view class="view-btn" v-for="(item,i) in psList.tag" :key="i">
				{{item}}
			</view>
		</view>
		<view class="qs_space" style="margin-top: 30rpx;"></view>
	</view>
</template>

<script>
	export default {
		name:"qsViewThree",
		props:{
			psList:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toDetail(str){
				this.$emit('childByValue', str)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/constant_value.less";
	.qs_view_cont2{
		.qs-display();
		.qs-justify-content();
		.one{
			width: 660rpx;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap; 
		}
	}
</style>
