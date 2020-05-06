<template>
	<view>
		<view v-if="!param.tips">
			<view class="card_cont">
				<view class="">
					{{param.jname}}
				</view>
				<image :src="param.is_do==1?'/static/image/shoucang3.png':'/static/image/close.png'" @click="tola(param.is_do,param.did)"></image>
			</view>
			<view class="card_pay">
				{{param.pay}}
			</view>
			<view class="com_name">
				{{param.cname}} 
			</view>
			<view class="card_step">
				<view class="room">
					<image src="../../static/image/stepc1.png" mode="" class="stepc"></image>
					<image :src="param.is_do==1?'/static/image/stepl1.png':'/static/image/stepl2.png'" mode="" class="stepl"></image>
					<image :src="param.is_do==1?'/static/image/stepc1.png':'/static/image/stepc2.png'" mode="" class="stepc"></image>
				</view>
				<view class="word">
					<view class="one">
						<view class="jname">
							收藏职位
						</view>
						<view class="time">
							{{param.addtime}} 
						</view>
					</view>
					<view class="one" style="margin-bottom: 0;">
						<view :class="param.is_do==1?'jname':'nodata'">
							{{param.is_do==1?"已投递":"未投递"}}
						</view>
						<view class="time" v-if="param.is_do==1">
							{{param.twotime}}
						</view>
					</view>
				</view> 
			</view>
			<view class="card_notice">
				{{param.is_close==1?"":"投递后不能石沉大海，立即与HR沟通释疑！"}}
			</view>
			<view :class="param.is_close!=1?'card_btn_two':'card_btn'" @click="tom(param.is_do,param.is_close,param.id,param.uid)">
				{{param.is_close!=1?"该职位已关闭":param.is_do==1?"开启职聊":"投递简历"}}
			</view>
		</view>
		<view class="nodata" v-if="param.tips">
			<image src="../../static/image/empty1.png" mode="center"></image>
			<view class="one">
				{{param.tips}}
			</view>
			<view class="notice">
				提示：主动出击更容易获得好职位！
			</view>
			<navigator url="../../pages/index/job" class="card_btn">
				找工作
			</navigator>
		</view>
	</view>
</template>

<script>
	export default {
		name:"cardCollect",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			tola(s,did){
				let data = {type:s,did:did}
				this.$emit("cardClose",data)
			},
			tom(s,t,id,uid){
				let data;
				if(t == 1){
					if(s == 1){
						data = {is_do:s,id:id,uid:uid+"_1"};
					}else{
						data = {is_do:s,id:id};
					}
					this.$emit("cardOption",data)
				}
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendPer.less";
</style>
