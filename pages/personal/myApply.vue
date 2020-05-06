<template>
	<view :style="'min-height:'+ht+'px;background:#f5f5f5;'">
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<!-- <scroll-view 
			v-if="ccList.length" 
			scroll-x="true" 
			@scroll="scroll" 
			:scroll-left="slide_dis" 
			class="scrollcont" 
			@scrolltolower="nextPage">
			<view class="card_body" v-for="(item,i) in ccList" :key="i">
				<card-three :param="item" @cardOption="cardOption"></card-three>
			</view>
		</scroll-view> -->
		<swiper v-if="ccList.length" class="scrollcont" :indicator-dots="false" :autoplay="false" @change="exchange">
			<swiper-item v-for="(item,i) in ccList" :key="i">
				<view class="card_body">
					<card-three :param="item" @cardOption="cardOption"></card-three>
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
	import cardThree from "../../components/card-three/card-three.vue";
	import emptyList from "../../components/empty-list/empty-list.vue"
	import http from "../../server/api-person.js";
	export default {
		components:{
			cardThree,
			emptyList
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"我的申请", //当type为3或4的时候icon右边的文字
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
				slide_dis:0,
				list_param:{page:1},
				cardLeft: 0, //现移动距离
				startX: 0, //起始位置
				endX: 0, //结束位置
				nowLeft: 0, //移动时距离
				disX: 0, //总移动距离
				cardWidth: 0,//卡片（移动）宽度
		
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
			this.myApply();
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
					let res = await http.myApply(this.list_param,this.headers);
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
							addtime:item.apply_addtime,
							addtime2:item.personal_look_time,
							twotime:item.reply_time,
							is_do:item.personal_look_time?1:0,
							success:item.is_reply,
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
			async myApply(){
				let res = await http.myApply(this.list_param,this.headers);
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
						addtime:item.apply_addtime,
						addtime2:item.personal_look_time,
						twotime:item.reply_time,
						is_do:item.personal_look_time?1:0,
						success:item.is_reply,
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

<style lang="less" scoped>
	@import "../../common/extendPer.less";
</style>
