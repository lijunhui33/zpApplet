<template>
	<view class="two_left_text">
		<image :src="dog.src" :class="dog.imgStyle" mode="aspectFit"></image>
		<view :class="dog.use" style="flex: 1;">
			{{dog.name}}
		</view>
	</view>
</template>

<script>
	export default {
		name:"twoText",
		props:{
			dog:{
				type:Object
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

<style>

</style>
