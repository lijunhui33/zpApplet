<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ep_cont">
			<view class="notice">
				请填写您的投诉详情，若情况属实，我们会及时处理
			</view>
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">举报原因</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="end" :value="index3" :range-key="'name'" @change="bindPickerChange3">
							<view>{{end[index3].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">联系方式</view>
					</view>
					<view class="box_input">
						<input type="text" value="" name="telephone" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input" style="height: auto;align-items: flex-start;margin-top: 40rpx;margin-bottom: 40rpx;">
					<view class="label">
						<view class="red">*</view>
						<view class="">问题描述</view>
					</view>
					<view class="box_input">
						<textarea name="content" style="width: 400rpx;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">提交举报</button>
				<view class="deleteEdu" @click="customConduct">不举报了</view>
			</form>
		</view>
	</view>
</template>

<script>
	import http from "../../server/api-jobDeatil.js"
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"举报职位", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				index3:0,
				end:[
					{id:"",name:"请选择"},
					{id:"1",name:"电话虚假(如空号、无人接听)"},
					{id:"2",name:"职介收费"},
					{id:"3",name:"虚假(如职位、待遇等虚假)"},
					{id:"4",name:"涉黄违法"},
					{id:"5",name:"网赚虚假(刷钻、刷信誉欺诈)"},
					{id:"6",name:"职介冒充"},
					{id:"7",name:"其他"}
				],
				list_param:{},
				jobs_id:""
			}
		},
		onLoad(option) {
			this.jobs_id = option.id;
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
			formSubmit: function(e) {
				let fd = e.detail.value;
				this.list_param = {
					...fd,
					jobs_id:this.jobs_id,
					report_type:this.end[this.index3].id
				};
				this.reportJobs();
			},
			bindPickerChange3(e){
				this.index3 = e.target.value
			},
			async reportJobs(){
				const token = uni.getStorageSync('token');
				let headers = {'Authorization':'Bearer '+token};
				let res = await http.reportJobs(this.list_param,headers);
				if(res.status == 200){
					uni.showToast({
						icon:"none",
						title:"操作成功"
					});
					let that = this;
					setTimeout(()=>{
						that.customConduct();
					},2000)
				}
			},
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
