<template>
	<view class="jobone_cont">
		<view class="over" v-if="!param.tips">
			<view class="overBody" @click="toJob(param.id)">
				<view class="onew">{{param.jname}}</view>
				<view class="owright" style="color: #999999;">{{param.addtime}}</view>
			</view>
			<view class="overBody" @click="toJob(param.id)">
				<view class="twow">{{param.pay}}</view>
				<view class="twright" :style="'color:'+list[param.is_do].color+';'">{{list[param.is_do].name}}</view>
			</view>
			<view class="overBody">
				<view class="threew">{{param.cname}}</view>
				<view class="five" @click="toName(param.did)">
					<image src="../../static/image/delete.png" mode=""></image>
					<view class="">
						删除
					</view>
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
		name:"jobTwo",
		props:['param'],
		data() {
			return {
				list:[
					{color:"#ef7a2c",name:"企业未查看"},
					{color:"#ef7a2c",name:"待反馈"},
					{color:"#08c47b",name:"合适"},
					{color:"#ff5c56",name:"不合适"},
					{color:"#1787fb",name:"待定"},
					{color:"#ef7a2c",name:"未接通"},
				]
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
