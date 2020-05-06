<template>
	<view>
		<scroll-view scroll-y="true" class="more">
			<view class="title">学历要求</view>
			<view class="btn_group">
				<view v-for="(item,i) in moreone" :key="i" :class="item.use+' none'" @click="toName(item.id,1)">
					{{item.name}}
				</view>
			</view>
			<view class="title">工作经验</view>
			<view class="btn_group">
				<view v-for="(item,i) in moretwo" :key="i" :class="item.use+' none'" @click="toName(item.id,2)">
					{{item.name}}
				</view>
			</view>
			<view class="title">福利待遇</view>
			<view class="btn_group">
				<view v-for="(item,i) in morethree" :key="i" :class="item.use+' none'" @click="toName(item.id,3)">
					{{item.name}}
				</view>
			</view>
		</scroll-view>
		<view class="morebtng">
			<view class="one" @click="opt(1)">取消</view>
			<view class="two" @click="opt(2)">保存</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"dropMore",
		props:['moreone','moretwo','morethree'],
		data() {
			return {
				
			}
		},
		methods: {
			toName(id,str){
				let data = JSON.stringify({id:id,str:str})
				this.$emit("sure",data)
			},
			opt(flag){
				this.$emit("saveOrReset",flag)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
