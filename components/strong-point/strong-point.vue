<template>
	<view class="speciality">
		<view class="">
			{{param}}
		</view>
		<image src="../../static/image/close.png" mode="" @click="del(param)"></image>
	</view>
</template>

<script>
	export default {
		name:"strongPoint",
		props:['param'],
		data() {
			return {
				
			}
		},
		methods: {
			del(i){
				this.$emit("delPro",i)
			}
		}
	}
</script>

<style>

</style>
