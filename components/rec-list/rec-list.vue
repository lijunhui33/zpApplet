<template>
	<view class="rec_list" @click="toName(param.id)">
		<image :src="param.src" mode=""></image>
		<view class="">
			<view class="rec_top">
				{{param.name}}
			</view>
			<view class="rec_bottom">
				<view style="color: #51a5fc;">
					{{param.num}}
				</view>
				个在招职位
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"recList",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(id){
				this.$emit("toCom",id)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/search.less";
</style>
