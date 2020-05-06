<template>
	<view :style="'min-height:'+ht+'px;background:#f5f5f5;'">
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<!-- <scroll-view v-if="ccList.length" scroll-x="true" @scroll="scroll" scroll-left="0" class="scrollcont" @scrolltolower="nextPage">
			<view class="card_body" v-for="(item,i) in ccList" :key="i">
				<card-two :param="item" @cardOption="cardOption"></card-two>
			</view>
		</scroll-view> -->
		<swiper v-if="ccList.length" class="scrollcont" :indicator-dots="false" :autoplay="false" @change="exchange">
			<swiper-item v-for="(item,i) in ccList" :key="i">
				<view class="card_body">
					<card-two :param="item" @cardOption="cardOption"></card-two>
				</view>
			</swiper-item>
		</swiper>
		<empty-list v-if="!ccList.length"></empty-list>
		<view class="wnotice" v-if="ccList.length">
			<view class="wrap">
				<view class="right">
					右滑查看下一份
				</view>
				<image src="../../static/image/back3.png" mode=""></image>
			</view>
		</view>
	</view>
</template>

<script>
	import cardTwo from "../../components/card-two/card-two.vue"
	import emptyList from "../../components/empty-list/empty-list.vue"
	import http from "../../server/api-person.js";
	export default {
		components:{
			cardTwo,
			emptyList
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"面试邀请", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				ht:0,
				old: {
					scrollTop: 0
				},
				ccList:[],
				headers:{},
				isfull:false,
				nowPage:1,
				list_param:{page:1}
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
			this.myInterview();
			try {
			    const res = uni.getSystemInfoSync();
				this.ht = res.windowHeight;
			} catch (e) {
			    // error
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
			scroll: function(e) {
				this.old.scrollTop = e.detail.scrollTop
			},
			async nextPage(){
				if(this.isfull){
					uni.showToast({
						icon:"none",
						title:"没有更多了"
					})
				}else{
					this.list_param = {...this.list_param,page:++this.nowPage};
					let res = await http.myInterview(this.list_param,this.headers);
					let data = res.returnlist;
					this.isfull = res.isfull;
					this.nowPage = res.nowPage;
					let arr = [];
					data.map(item => {
						arr.push({
							id:item.id,
							did:item.did,
							cname:item.company_name,
							pay:item.wage_cn,
							jname:item.jobs_name,
							one:[
								{src:"/static/image/time3.png",name:item.interview_time,use:"qs_one_color"},
								{src:"/static/image/call3.png",name:item.contact,use:"qs_one_color"},
								{src:"/static/image/diqu.png",name:item.address,use:"qs_one_color"},
							],
							is_do:item.interview_state,
							tips:item.tips
						})
					});
					this.ccList = [...this.ccList,...arr];
				}
			},
			cardOption(e){
				uni.navigateTo({
					url:"../index/jobDetail?id="+e
				})
			},
			async myInterview(){
				let res = await http.myInterview(this.list_param,this.headers)
				if(res == undefined)
					return false;
				let data = res.returnlist;
				this.isfull = res.isfull;
				this.nowPage = res.nowPage;
				let arr = [];
				data.map(item => {
					arr.push({
						id:item.id,
						did:item.did,
						cname:item.company_name,
						pay:item.wage_cn,
						jname:item.jobs_name,
						one:[
							{src:"/static/image/time3.png",name:item.interview_time,use:"qs_one_color"},
							{src:"/static/image/call3.png",name:item.contact,use:"qs_one_color"},
							{src:"/static/image/diqu.png",name:item.address,use:"qs_one_color"},
						],
						is_do:item.interview_state,
						tips:item.tips
					})
				});
				this.ccList = arr;
			},
			exchange(e){
				if (e.detail.current == this.ccList.length-1) {
					this.nextPage();
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/extendPer.less";
</style>
