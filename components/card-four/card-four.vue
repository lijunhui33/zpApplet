<template>
	<view>
		<view v-if="!param.tips">
			<view class="card_four">
				{{param.cname}}
			</view>
			<view class="card_btn_group">
				<view v-for="(item,i) in param.btns" :key="i" :class="item.use">{{item.name}}</view>
			</view>
			<view class="com_name">
				{{param.info}} 
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
							查看简历
						</view>
						<view class="time">
							{{param.addtime}} 
						</view>
					</view>
					<view class="one" style="margin-bottom: 0;">
						<view :class="param.is_do==1?'jname':'nodata'">
							{{param.is_do==1?"企业已下载简历":"企业未下载"}}
						</view>
						<view class="time">
							{{param.twotime}}
						</view>
					</view>
				</view> 
			</view>
			<view class="card_notice">
				{{param.is_do==1?"提示：企业已下载您的简历，建议立即沟通！":"提示：企业未下载您的简历，建议主动出击！"}}
			</view>
			<view class="card_btn" @click="tom(param.is_do,param.id,param.uid)">
				{{param.is_do==1?"开启职聊":"查看在招职位"}}
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
		name:"cardFour",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			tola(s){
				this.$emit("cardClose",s)
			},
			tom(s,id,uid){
				let data;
				if(s == 1){
					data = {type:s,id:uid+"_1"}
				}else{
					data = {type:s,id:id}
				}
				this.$emit("cardOption",data)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendPer.less";
</style>
