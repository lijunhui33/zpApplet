<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<att-header :param="info"></att-header>
		<view class="qs_space"></view>
		<view class="wtitle">
			<view></view>工商登记信息
		</view>
		<view class="qs-line"></view>
		<att-li v-for="item in listOne" :key="item.name" :param="item"></att-li>
		<view class="qs_space"></view>
		<view class="wtitle">
			<view></view>人力资源师实地认证情况
		</view>
		<view class="qs-line"></view>
		<att-li v-for="(item,j) in listTwo" :key="j" :param="item"></att-li>
		<view class="qs_space"></view>
		<view class="wtitle">
			<view></view>实地认证照片
		</view>
		<swiper style="width: 600rpx;margin: 30rpx auto;" :indicator-dots="false" :autoplay="true" :interval="3000" :duration="1000">
			<swiper-item v-for="(item,i) in imglist" :key="i">
				<view class="swiper-item qs-img">
					<image :src="item" mode="aspectFit"></image>
				</view>
			</swiper-item>
		</swiper>
		<view class="qs-line"></view>
		<view class="qs_space"></view>
		<view class="wtitle">
			<view></view>认证师评价
		</view>
		<view class="atter_cont">
			{{atter_cont}} 
		</view>
		<view class="qs_space"></view>
	</view>
</template>

<script>
	import attHeader from "../../components/att-header/att-header.vue"
	import attLi from "../../components/att-li/att-li.vue"
	import http from "../../server/api-job.js"
	export default {
		components:{
			attHeader,
			attLi
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"企业实地认证报告", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				info:{},
				listOne:[],
				listTwo:[],
				atter_cont:"",
				list_param:{},
				imglist:[]
			}
		},
		onLoad:function(option){
			this.list_param = {com_id:option.id},
			this.companyReport();
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
			async companyReport(){
				let res = await http.companyReport(this.list_param);
				this.info = {
					cname:res.com_name,
					addtime:res.addtime
				};
				this.listOne = [
					{name:"名称",val:res.com_name},
					{name:"法人",val:res.corporate},
					{name:"主体类型",val:res.com_type},
					{name:"创立时间",val:res.reg_time},
					{name:"注册资本",val:res.reg_capital+"万"},
					{name:"注册地址",val:res.reg_address},
					{name:"办公地点",val:res.office_address},
					{name:"登记机关",val:res.registrar},
					{name:"经营范围",val:res.scope},
				];
				this.listTwo = [
					{name:"办公面积",val:res.office_area+"平米"},
					{name:"办公环境",val:res.office_env==1?"一般":res.office_env==2?"良好":"优美"},
					{name:"办公场所",val:res.workplace},
					{name:"员工人数",val:res.number},
					{name:"男女比例",val:res.sex_ratio},
					{name:"平均年龄",val:res.average_age+"岁"},
					{name:"乘车路线",val:res.route},
				];
				this.atter_cont = res.evaluation;
				this.imglist = [
					res.img
				]
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/attestation.less";
</style>
