<template>
	<view class="jobone_cont">
		<view class="over" v-if="!param.tips">
			<view class="overBody" @click="toJob(param.company_id)">
				<view class="onew">{{param.cname}}</view>
				<view class="owright" :style="param.is_do==0?'color: #cacaca;':'color: #ef7a2c;'">
					{{param.is_do==0?"未下载":"已下载"}}
				</view>
			</view>
			<view class="overBody">
				<view class="twow" style="color: #666666;" @click="toJob(param.company_id)">
					<text v-for="(item,i) in param.pay" :key="i">
						<text>{{item}}</text><text class="verticalLine" v-if="i+1 != param.pay.length">|</text>
					</text>
				</view>
				<view class="twright">
					<view class="five" @click="toName(param.id)">
						<image src="../../static/image/delete.png" mode=""></image>
						<view class="">
							删除
						</view>
					</view>
				</view>
			</view>
			<view class="overBody">
				<view class="threew" @click="toJob(param.company_id)">{{param.addtime}}</view>
				<view class="zhiliao" @click="chat(param.id,param.company_uid)">
					职聊
				</view>
			</view>
		</view>
		<view class="over" v-if="param.tips">
			{{param.tips}}
		</view>
		<view class="qs_space"></view>
	</view>
</template>

<script>
	export default {
		name:"jobThree",
		props:['param'],
		data() {
			return {
				list:[
					{color:"#ef7a2c",name:"待反馈"},
					{color:"#08c47b",name:"合适"},
					{color:"#ef7a2c",name:"未接通"},
					{color:"#ff5c56",name:"不合适"},
					{color:"#ef7a2c",name:"企业未查看"},
					{color:"#1787fb",name:"待定"},
				]
			}
		},
		methods: {
			toName(str){
				this.$emit("delItem",str)
			},
			chat(id,uid){
				let data = {id:id,uid:uid+"_1"};
				this.$emit("chat",data)
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
