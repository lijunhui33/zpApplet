<template>
	<view>
		<view class="qs_view_cont" style="margin-top: 20rpx;" @click="toDetail(psList.id)">
			<view class="view_job_name">
				<view class="jobname" style="width: 460rpx;">
					{{psList.job_name}}<text v-if="psList.urgen==1" class="urgen">急</text>
				</view>
			</view>
			<new-text :dog="psList.item"></new-text>
		</view>
		<view class="qs_view_cont2" @click="toDetail(psList.id)">
			<view class="one">
				{{psList.cont}}
			</view>
			<view class="view_pay">
				{{psList.pay}}
			</view>
		</view>
		<view class="qs_view_cont">
			<view class="view_com_name" @click="toDetail(psList.id)">
				<view :class="psList.xianyan==1?'view_audit':''" style="max-width: 400rpx;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
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
		<view class="qs_space" style="margin-top: 30rpx;"></view>
	</view>
</template>

<script>
	import newText from "../new-text/new-text.vue"
	export default {
		name:"qsViewTwo",
		components:{
			newText
		},
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

<style lang="less">
	@import "../../common/constant_value.less";
	.qs_view_cont2{
		.qs-display();
		.qs-justify-content();
		.view_pay{
			color: #ff5c56;
			width: 160rpx;
			overflow: hidden;
			text-align: right;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.one{
			width: 500rpx;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
	}
</style>
