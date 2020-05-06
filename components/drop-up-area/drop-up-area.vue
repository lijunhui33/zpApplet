<template>
	<view>
		<view class="grade" v-if="listtwo.length">
			<view>
				当前选择：
				<text v-for="(item,i) in listtwo" :key="i">
					{{item.name}} <text v-if="i+1 != listtwo.length">></text>
				</text>
			</view>
			<view class="back" @click="back(pid)">返回上一级</view>
		</view>
		<scroll-view scroll-y="true" class="regionnav">
			<view v-for="(item,i) in listone" :key="i" :class="item.use+' none'" @click="toName(item.id)">
				{{item.name}}
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		name:"dropUpArea",
		props:['listone','listtwo','pid', 'isShowBack'],
		data() {
			return {
				
			}
		},
		methods: {
			back(id){
				this.$emit("backPre",id)
			},
			toName(id){
				this.$emit("sure",id)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
