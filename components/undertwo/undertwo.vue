<template>
	<view>
		<view class="contentview">
			<view class="one">
				<text v-for="(item,i) in param.schoolname" :key="i">
					<text>{{item}}</text>
					<text v-if="i+1 != param.schoolname.length" class="colorgray">|</text>
				</text>
			</view>
			<image src="../../static/image/edit.png" mode="" @click="toName(param.type,param.id)"></image>
		</view>
		<view class="qs_common_lr">
			<view style="color: #888888;">
				{{param.time}}
			</view>
			<view style="color: #888888;">
				{{param.during}}
			</view>
		</view>
		<view class="qs_common_lr" style="color: #666666;font-size: 30rpx;">
			{{param.major}}
		</view>
	</view>
</template>

<script>
	export default {
		name:"undertwo",
		props:{
			param:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toName(str,id){
				let data = {type:str,id:id}
				this.$emit("update",data)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
