<template>
	<view class="per_completion" :style="personPro<40?'background: #fff7f5;':personPro<70?'background: #f0f7ff;':'background: #FFF8DC;'">
		<view class="completion">
			<view class="cont">
				<view>
					完整度：
				</view>
				<view v-if="personPro<40" style="color: #ff5527;">
					{{personPro}}%
				</view>
				<view v-else-if="personPro<70" style="color: #1788fb;">
					{{personPro}}%
				</view>
				<view v-else-if="personPro<80" style="color: #F0AD4E;">
					{{personPro}}%
				</view>
			</view>
			<view v-if="personPro==0" class="desc" style="color: #ff5527;">
				简历质量登录后可见
			</view>
			<view v-else-if="personPro<40" class="desc" style="color: #ff5527;">
				简历质量低于40%的求职者
			</view>
			<view v-else-if="personPro<70" class="desc" style="color: #1788fb;">
				简历质量超过40%的求职者
			</view>
			<view v-else-if="personPro<80" class="desc" style="color: #F0AD4E;">
				简历质量超过70%的求职者
			</view>
		</view>
		<view class="per_progress">
			<progress v-if="personPro<40" :percent="personPro" activeColor="#ff5527" backgroundColor="#ffeeea" active stroke-width="6" />
			<progress v-else-if="personPro<70" :percent="personPro" activeColor="#1788fb" backgroundColor="#daecff" active stroke-width="6" />
			<progress v-else-if="personPro<80" :percent="personPro" activeColor="#F0AD4E" backgroundColor="#ffcc99" active stroke-width="6" />
		</view>
	</view>
</template>

<script>
	export default {
		name:"personCompletion",
		props:{
			personPro:[String, Number]
		},
		data() {
			return {
				
			}
		},
		methods: {
			
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/person.less";
</style>
