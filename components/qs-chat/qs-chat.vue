<template>
	<view>
		<view class="chat-item" @click="toChat(chatList.uid)">
			<view class="logo">
				<image :src="chatList.src" mode=""></image>
			</view>
			<view class="to_logo_right">
				<view class="container">
					<view class="text-left">
						<view :class="chatList.xianshi==1?'view_audit':''">
							{{chatList.com_name}}
						</view>
						<image v-if="chatList.renzheng==1" src="../../static/image/renzheng.png" mode=""></image>
					</view>
					<view class="text-right">
						{{chatList.during}}
					</view>
				</view>
				<view class="bottom-text">
					{{chatList.cont}}
				</view>
			</view>
		</view>
		<view class="qs-line"></view>
	</view>
</template>

<script>
	export default {
		name:"qsChat",
		props:{
			chatList:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toChat(str){
				this.$emit("chatOnline",str)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/chat.less";
</style>
