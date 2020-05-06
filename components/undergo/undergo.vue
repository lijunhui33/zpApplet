<template>
	<view>
		<view class="contentview">
			<view class="one">
				<text v-for="(item,i) in param.name" :key="i">
					<text>{{item}}</text>
					<text v-if="i+1 != param.name.length" class="colorgray">|</text>
				</text>
			</view>
			<image src="../../static/image/edit.png" mode="" @click="toName(param.type,param.id)"></image>
		</view>
	</view>
</template>

<script>
	export default {
		name:"undergo",
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
