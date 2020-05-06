<template>
	<view class="search_cont">
		<input class="qs_input" 
			v-model="cont" 
			type="text"
			placeholder-style="color:#c2c2c2" 
			placeholder="输入想要搜索的内容" 
			confirm-type="search"
			@confirm="search"/>
	</view>
</template>

<script>
	export default {
		name:"searchNav",
		data() {
			return {
				cont:""
			}
		},
		methods: {
			search(){
				this.$emit("search",this.cont)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/search.less";
</style>
