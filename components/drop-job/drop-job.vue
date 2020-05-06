<template>
	<view class="djnav">
		<scroll-view scroll-y="true" class="dropnav">
			<view v-for="(item,i) in listone" :key="i" :class="item.use+' none'" @click="toName(item.id,1)">
				{{item.name}}
			</view>
		</scroll-view>
		<scroll-view scroll-y="true" class="dropnav dntwo">
			<view v-for="(item,i) in listtwo" :key="i" @click="toName(item.id,2)">
				<view :class="item.use+' ntwo'">{{item.name}}</view>
				<view v-for="(tom,k) in listthree" :key="k" v-if="item.id == tom.parentid" :class="tom.use+' nthree'" @click="toName(tom.id,3)">
					{{tom.name}}
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	export default {
		name:"dropJob",
		props:['listone','listtwo','listthree'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(id,str){
				let data = JSON.stringify({id:id,str:str})
				this.$emit("sure",data)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
