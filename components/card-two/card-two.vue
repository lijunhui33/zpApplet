<template>
	<view>
		<view v-if="!param.tips">
			<view class="card_cont">
				<view class="">
					{{param.jname}}
				</view>
			</view>
			<view class="card_pay">
				{{param.pay}}
			</view>
			<view class="com_name">
				{{param.cname}} 
			</view>
			<view class="card_list">
				<two-text v-for="(item,i) in param.one" :key="i" :dog="item"></two-text>
			</view>
			<view class="card_notice">
				提示：若有事不能按时面试，请及时与企业沟通
			</view>
			<view :class="param.is_do==1?'card_btn':'card_btn_two'" @click="tom(param.id)">
				{{param.is_do==1?"查看职位":"面试时间已过"}}
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
	import twoText from "../../components/two-text/two-text.vue"
	export default {
		components:{
			twoText
		},
		name:"cardTwo",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			tom(s){
				this.$emit("cardOption",s)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendPer.less";
</style>
