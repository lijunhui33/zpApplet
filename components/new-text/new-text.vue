<template>
	<view class="new_left_text">
		<image :src="dog.src" :class="dog.imgStyle" mode="aspectFit"></image>
		<view :class="dog.use">
			{{dog.name}}
		</view>
	</view>
</template>

<script>
	export default {
		name:"newText",
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