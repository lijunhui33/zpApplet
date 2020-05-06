<template>
	<view :style="'min-height:'+ht+'px;background:#f5f5f5;'">
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<!-- <scroll-view v-if="ccList.length" scroll-x="true" @scroll="scroll" scroll-left="0" class="scrollcont" @scrolltolower="nextPage">
			<view class="card_body" v-for="(item,i) in ccList" :key="i">
				<card-four :param="item" @cardOption="cardOption"></card-four>
			</view>
		</scroll-view> -->
		<swiper v-if="ccList.length" class="scrollcont" :indicator-dots="false" :autoplay="false" @change="exchange">
			<swiper-item v-for="(item,i) in ccList" :key="i">
				<view class="card_body">
					<card-four :param="item" @cardOption="cardOption"></card-four>
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
	import cardFour from "../../components/card-four/card-four.vue"
	import emptyList from "../../components/empty-list/empty-list.vue"
	import http from "../../server/api-person.js";
	export default {
		components:{
			cardFour,
			emptyList
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"对我感兴趣", //当type为3或4的时候icon右边的文字
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
			this.attentionToMe();
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
					let res = await http.attentionToMe(this.list_param,this.headers);
					let data = res.returnlist;
					this.isfull = res.isfull;
					this.nowPage = res.nowPage;
					let arr = [];
					data.map(item => {
						let btns = []
						btns.push(item.setmeal_id!=1?{use:"tagc4",name:"普通会员"}:{use:"tagc1",name:"名企会员"});
						if(item.audit==1){
							btns.push({use:"tagc2",name:"执照认证"});
						}
						if(item.report==1){
							btns.push({use:"tagc3",name:"实地认证"});
						}
						arr.push({
							id:item.company_id,
							uid:item.company_uid,
							cname:item.companyname,
							btns:btns,
							info:`${item.nature_cn} | ${item.scale_cn} | ${item.trade_cn}`,
							addtime:item.addtime,
							twotime:item.downtime,
							is_do:item.hasdown
						})
					});
					this.ccList = [...this.ccList,...arr];
				}
			},
			cardOption(e){
				if (e.type == 0) {
					uni.navigateTo({
						url:"../index/comDetail?id="+e.id
					})
				} else{
					uni.navigateTo({
						url:`/pages/tidings/myChat?uid=${e.id}`
					});
				}
			},
			async attentionToMe(){
				let res = await http.attentionToMe(this.list_param,this.headers);
				if(res == undefined)
					return false;
				let data = res.returnlist;
				this.isfull = res.isfull;
				this.nowPage = res.nowPage;
				let arr = [];
				data.map(item => {
					let btns = []
					btns.push(item.setmeal_id!=1?{use:"tagc4",name:"普通会员"}:{use:"tagc1",name:"名企会员"});
					if(item.audit==1){
						btns.push({use:"tagc2",name:"执照认证"});
					}
					if(item.report==1){
						btns.push({use:"tagc3",name:"实地认证"});
					}
					arr.push({
						id:item.company_id,
						uid:item.company_uid,
						cname:item.companyname,
						btns:btns,
						info:`${item.nature_cn} | ${item.scale_cn} | ${item.trade_cn}`,
						addtime:item.addtime,
						twotime:item.downtime,
						is_do:item.hasdown,
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
