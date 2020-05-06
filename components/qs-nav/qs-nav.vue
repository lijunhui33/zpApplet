<template>
	<view class="nav_content">
		<navigator :url="qs_url">
			<image :src="qs_image" mode="aspectFit"></image>
			<view>
				{{qs_text}}
			</view>
		</navigator>
	</view>
</template>

<script>
	export default {
		name:"qsNav",
		props:{
			qs_image:{
				type:String
			},
			qs_text:{
				type:String
			},
			qs_url:{
				type:String
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

<style lang="less">
	.nav_content{
		font-size: 26rpx;
		text-align: center;
		image{
			width: 95rpx;
			height: 95rpx;
		}
	}
	
</style>
