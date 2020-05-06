<template>
	<view>
		<view class="tabheader">
			<view class="one">
				<view class="tabbox" @click="opt(1)">取消</view>
				<view class="tabnotice">多选，最多 3 项</view>
			</view>
			<view class="tabbox extend" @click="opt(2)">确定</view>
		</view>
		<view class="selreg">
			<view class="btn" v-for="(item,i) in listthree" :key="i">
				{{item.name}}<uni-icons type="closeempty" size="20" @click="close(item.id)"></uni-icons>
			</view>
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
		name:"intentJob",
		props:['listone','listtwo','listthree','pid'],
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
			opt(str){
				this.$emit("resetOrSave",str)
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
