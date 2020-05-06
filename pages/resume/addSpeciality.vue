<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ep_cont">
			<view class="notice">
				自定义标签最多8个字。
			</view>
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">自定义标签</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="tagname" name="tagname" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存</button>
			</form>
		</view>
	</view>
</template>

<script>
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"添加自定义标签", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				tagname:""
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.navigateBack({
				    delta: 1,
				    animationType: 'pop-out',
				    animationDuration: 200
				});
			},
			formSubmit(e){
				if(this.tagname.length>8){
					uni.showToast({
						icon:"none",
						title:"最多8个字"
					})
					return;
				}
				uni.$emit("add",{
					name:this.tagname
				});
				this.customConduct();
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
