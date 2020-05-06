<template>
	<view>
		<view class="person_option" @click="toName(qs_option.name)">
			<view class="left">
				<image :src="qs_option.src" mode=""></image>
				<view>
					{{qs_option.name}}
				</view>
			</view>
			<view class="right">
				<view>
					<view v-if="qs_option.one" class="tag">
						<view class="one">
							{{qs_option.to}}
						</view>
						<view class="two">
							{{qs_option.tw}}
						</view>
					</view>
					<view v-if="qs_option.two" class="dom">
						{{qs_option.dom}}
					</view>
				</view>
				<image src="../../static/image/copyback2.png" mode=""></image>
			</view>
		</view>
		<view class="person-line"></view>
	</view>
</template>

<script>
	export default {
		name:"personOption",
		props:{
			qs_option:{
				type:Object
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			toName(str){
				this.$emit("myOption",str)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/person.less";
</style>
