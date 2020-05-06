<template>
	<view class="intention">
		<view style="color: #888888;flex: 1;">
			{{intent.title}}
		</view>
		<view style="flex: 3;">
			{{intent.name}}
		</view>
	</view>
</template>

<script>
	export default {
		name:"intentionLi",
		props:{
			intent:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/resume.less";
</style>
