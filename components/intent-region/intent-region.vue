<template>
	<view>
		<view class="tabheader" v-if="is_word != 1">
			<view class="one">
				<view class="tabbox" @click="opt(1)">取消</view>
				<view class="tabnotice" v-if="is_word != 1">多选，最多 3 项</view>
			</view>
			<view class="tabbox extend" @click="opt(2)">确定</view>
		</view>
		<view class="selreg">
			<view class="btn" v-for="(item,i) in listthree" :key="i">
				{{item.name}}<uni-icons type="closeempty" size="20" @click="close(item.id)"></uni-icons>
			</view>
		</view>
		<view class="grade">
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
	import UniIcons from "../uni-icons/uni-icons.vue"
	export default {
		name:"intentRegion",
		props:['listone','listtwo','listthree','pid','is_word'],
		components:{
			UniIcons
		},
		data() {
			return {
				
			}
		},
		methods: {
			close(s){
				this.$emit("closechk",s)
			},
			back(id){
				this.$emit("backPre",id)
			},
			toName(id){
				this.$emit("sure",id)
			},
			opt(str){
				this.$emit("resetOrSave",str)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
