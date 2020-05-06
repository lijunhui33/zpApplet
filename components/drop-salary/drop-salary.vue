<template>
	<view>
		<scroll-view scroll-y="true" class="salary">
			<view v-for="(item,i) in param" :key="i" :class="item.use" @click="toName(item.id)">
				{{item.name}}
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		name:"dropSalary",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(s){
				this.$emit("sure",s)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
