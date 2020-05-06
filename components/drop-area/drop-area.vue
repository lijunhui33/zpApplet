<template>
	<view class="djnav">
		<scroll-view scroll-y="true" class="dropnav">
			<view v-for="(item,i) in listone" :key="i" :class="item.use+' none'" @click="toName(item.id,1)">
				{{item.name}}
			</view>
		</scroll-view>
		<scroll-view scroll-y="true" class="dropnav dntwo">
			<view v-for="(item,i) in listtwo" :key="i" :class="item.use+' ntwo'" @click="toName(item.id,2)">
				{{item.name}}
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		name:"dropArea",
		props:['listone','listtwo'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(id,str){
				console.log(id,str)
				let data = JSON.stringify({id:id,str:str})
				this.$emit("sure",data)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
