<template>
	<view>
		<view v-if="!param.tips">
			<view class="card_cont">
				{{param.jname}}
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
					<image :src="param.success!=0?'/static/image/stepl1.png':'/static/image/stepl2.png'" mode="" class="stepl"></image>
					<image :src="param.success!=0?'/static/image/stepc1.png':'/static/image/stepc2.png'" mode="" class="stepc"></image>
				</view>
				<view class="word">
					<view class="one">
						<view class="jname">
							投递简历
						</view>
						<view class="time">
							{{param.addtime}} 
						</view>
					</view>
					<view class="one">
						<view :class="param.is_do==1?'jname':'nodata'">
							{{param.is_do==1?"企业已查看":"企业待查看"}}
						</view>
						<view class="time">
							{{param.addtime2}}
						</view>
					</view>
					<view class="one" style="margin-bottom: 0;">
						<view :class="param.success!=0?'jname':'nodata'">
							{{list[param.success]}}
						</view>
						<view class="time" v-show="param.twotime">
							{{param.twotime}}
						</view>
					</view>
				</view> 
			</view>
			<view class="card_btn" @click="tom(param.id)">
				查看职位
			</view>
		</view>
		<view class="nodata" v-if="param.tips">
			<image src="../../static/image/empty1.png" mode="center"></image>
			<view class="one">
				{{param.tips}}
			</view>
			<view class="tips">
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
		name:"cardThree",
		props:['param'],
		data() {
			return {
				list:[
					"企业待处理",
					"合适",
					"不合适",
					"待定",
					"未接通",
				]
			}
		},
		methods: {
			tola(s){
				this.$emit("cardClose",s)
			},
			tom(s){
				this.$emit("cardOption",s)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendPer.less";
</style>
