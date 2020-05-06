<template>
	<view class="qs_drop_title" @click="change(param.id)">
		<view :class="param.use + ' limitcont'">{{param.name}}</view>
		<image v-if="param.use == 'dropwo'" src="/static/image/btn_unselected.png" mode=""></image>
		<image v-if="param.use == 'dropwt'" src="/static/image/btn_selected.png" mode=""></image>
	</view>
</template>

<script>
	export default {
		name:"dropDownTitle",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			change(s){
				this.$emit("changeItem",s)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
