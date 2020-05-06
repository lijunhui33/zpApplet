<template>
	<view @click="toDetail(psList.id)">
		<view class="company">
			<image class="com_logo" :src="psList.logo" mode=""></image>
			<view>
				<view class="one">
					{{psList.com_name}}
				</view>
				<view class="tags">
					<view v-for="(item,i) in psList.btng" :key="i" :class="item.use">
						{{item.name}}
					</view>
				</view>
				<view class="two">
					{{psList.cont}}
				</view>
			</view>
		</view>
		<view class="qs_view_cont3" v-if="psList.tag.length" style="margin-top: 20rpx;">
			<view class="view-btn" v-for="(item,i) in psList.tag" :key="i">
				{{item}}
			</view>
		</view>
		<view class="qs-line" style="margin: 22rpx 40rpx;width: 670rpx;" v-if="psList.num != 0"></view>
		<view class="job_num" v-if="psList.num != 0">
			<view class="job_cont">
				<view class="one" style="max-width: 300rpx;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
					{{psList.job_name}}
				</view>
				<view class="two">
					等{{psList.num}}个职位正在招聘
				</view>
			</view>
			<image src="../../static/image/copyback.png" mode=""></image>
		</view>
		<view class="qs_space" style="margin-top: 30rpx;"></view>
	</view>
	
</template>

<script>
	export default {
		name:"qsViewFive",
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
	@import "../../common/famous.less";
</style>
