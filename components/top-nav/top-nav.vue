<template>
	<view class="tn_cont" @click="toName(param.name)">
		<view class="one">
			<view class="">
				{{param.name}}
			</view>
			<image v-if="param.is_has" src="../../static/image/blackxiala.png" mode=""></image>
		</view>
		<view :class="param.use"></view>
	</view>
</template>

<script>
	export default {
		name:"topNav",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(s){
				this.$emit("Selected",s)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendRes.less";
</style>
