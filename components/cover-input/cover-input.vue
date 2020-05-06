<template>
	<view>
		<view class="ci_cont">
			<view class="one">
				<view :class="param.req?'red':'white'">*</view>
				<view class="label">{{param.label}}</view>
			</view>
			<view class="two">
				<input type="text" 
					v-model="val" 
					placeholder="请输入" 
					style="text-align: right;"
					placeholder-style="text-align:right"
					@input="test(param.name,val)"
					@blur="valige(param.req,param.label,param.name)"/>
				<image :src="param.al?'../../static/image/back3.png':'../../static/image/wechat2.png'" mode=""></image>
			</view>
		</view>
		<view class="qs-line"></view>
	</view>
</template>

<script>
	export default {
		name:"coverInput",
		props:['param'],
		data() {
			return {
				val:""
			}
		},
		methods: {
			test(s,v){
				this.$emit("postData",s,v)
			},
			valige(bool,s,v){
				if(bool){
					let str = this.val.trim()
					console.log(str)
					if(str == ""){
						uni.showToast({
							title: s+"必填",
							icon:"none"
						})
						this.$emit("disableFun",v,true)
					}else{
						this.$emit("disableFun",v,false)
					}
					
				}
			}
		}
	}
</script>

<style>

</style>
