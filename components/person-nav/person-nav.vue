<template>
	<view class="nav_content" @click="toName(personClick.name)">
		<view class="num">
			{{personClick.src}}
		</view>
		<view class="name">
			{{personClick.name}}
		</view>
	</view>
</template>

<script>
	export default {
		name:"personNav",
		props:{
			personClick:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toName(str){
				this.$emit("myGroup",str)
			}
		}
	}
</script>

<style lang="less">
	.nav_content{
		text-align: center;
		.num{
			font-size: 50rpx;
			line-height: 80rpx;
		}
		.name{
			font-size: 32rpx;
			color: #999999;
		}
	}
</style>
