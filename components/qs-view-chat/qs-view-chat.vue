<template>
	<view>
		<view class="qs_view_cont" style="margin-top: 20rpx;" @click="toDetail(psList.id)">
			<view class="view_job_name">
				<image class="is_top" v-if="psList.is_top==1" src="../../static/image/addtop.png"></image>
				<view class="jobname">
					{{psList.job_name}}<text v-if="psList.urgen==1" class="urgen">急</text>
				</view>
			</view>
			<view class="view_pay">
				{{psList.pay}}
			</view>
		</view>
		<view class="qs_view_cont2" @click="toDetail(psList.id)">
			<view class="qs_cominfo">
				{{psList.cont}}
			</view>
		</view>
		<view class="qs_view_cont">
			<view class="view_com_name">
				<view :class="psList.xianyan==1?'view_audit':''" @click="toDetail(psList.id)" style="max-width: 400rpx;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
					{{psList.com_name}}
				</view>
				<image v-if="psList.huiyuan==1" src="../../static/image/huiyuan.png" mode="aspectFit"></image>
				<image v-if="psList.renzheng==1" src="../../static/image/renzheng.png" mode="aspectFit"></image>
			</view>
			<view class="liaogongzuo" @click="chat(psList.id,psList.uid)">
				聊工作
			</view>
		</view>
		<view class="qs_view_cont3" v-if="psList.tag.length">
			<view class="view-btn" v-for="(item,i) in psList.tag" :key="i">
				{{item}}
			</view>
		</view>
		<view v-if="psList.line!=1" class="qs_space" style="margin-top: 30rpx;"></view>
		<view v-if="psList.line==1" class="qs-line" style="margin: 30rpx 40rpx;width: 670rpx;"></view>
	</view>
</template>

<script>
	export default {
		name:"qsViewChat",
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
			},
			chat(id,uid){
				let data = {id:id,uid:uid+"_1"};
				this.$emit("liveChat",data);
			}
		}
	}
</script>

<style>

</style>
