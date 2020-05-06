<template>
	<view class="person-header">
		<view class="container" @click="toName(perInfo.is_login)">
			<image :src="perInfo.src" mode="" class="headericon"></image>
			<view>
				<view class="person-name">
					{{perInfo.name}}
				</view>
				<view class="person-cont">
					{{perInfo.cont}}
				</view>
			</view>
		</view>
		<view class="edit" @click="toResume">
			<image src="../../static/image/back3.png" mode=""></image>
		</view>
	</view>
</template>

<script>
	export default {
		name:"personHeader",
		props:{
			perInfo:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toName(str){
				this.$emit("pop",str)
			},
			toResume(){
				// 判断是否登录
				if(this.perInfo.is_login){
					uni.switchTab({
						url:"/pages/resume/resume"
					})
				} else {
					this.$emit("pop",this.perInfo.is_login)
				}
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/person.less";
</style>
