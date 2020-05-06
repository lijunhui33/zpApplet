<template>
	<view>
		<view class="title_nav" @click="toName(titleCont.id)">
			<view>
				{{titleCont.name}}
			</view>
			<view class="cont">
				<image :src="titleCont.src" mode=""></image>
				<view class="qs_two_color">
					{{titleCont.btn}}
				</view>
			</view>
		</view>
		<view v-if="titleCont.line" class="qs-line"></view>
	</view>
</template>

<script>
	export default {
		name:"titleNavTwo",
		props:{
			titleCont:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toName(str){
				this.$emit("editInfo",str)
			}
		}
	}
</script>

<style>

</style>
