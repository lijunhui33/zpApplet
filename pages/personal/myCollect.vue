<template>
	<view :style="'min-height:'+ht+'px;background:#f5f5f5;'">
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<!-- <scroll-view v-if="ccList.length" scroll-x="true" @scroll="scroll" scroll-left="0" class="scrollcont" @scrolltolower="nextPage">
			<view class="card_body" v-for="(item,i) in ccList" :key="i">
				<card-collect :param="item" @cardClose="cardClose" @cardOption="cardOption"></card-collect>
			</view>
		</scroll-view> -->
		<swiper v-if="ccList.length" class="scrollcont" :indicator-dots="false" :autoplay="false" @change="exchange">
			<swiper-item v-for="(item,i) in ccList" :key="i">
				<view class="card_body">
					<card-collect :param="item" @cardClose="cardClose" @cardOption="cardOption"></card-collect>
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
	import cardCollect from "../../components/card-collect/card-collect.vue"
	import emptyList from "../../components/empty-list/empty-list.vue"
	import http from "../../server/api-person.js";
	export default {
		components:{
			cardCollect,
			emptyList
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"我的收藏", //当type为3或4的时候icon右边的文字
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
				list_param:{page:1},
				del_param:{},
				deliver:{}
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
			this.jobsFavorites();
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
					let res = await http.jobsFavorites(this.list_param,this.headers);
					let data = res.returnlist;
					this.isfull = res.isfull;
					this.nowPage = res.nowPage;
					let arr = [];
					data.map(item => {
						arr.push({
							id:item.jobs_id,
							did:item.did,
							uid:item.company_uid,
							cname:item.companyname,
							pay:item.wage_cn,
							jname:item.jobs_name,
							addtime:item.addtime,
							is_do:item.apply,
							is_close:item.display,
							tips:item.tips
						})
					});
					this.ccList = [...this.ccList,...arr];
				}
			},
			cardClose(e){
				let type = e.type;
				if(type == 0){
					this.del_param = {id:e.did};
					this.jobsFavoritesDel();
				}
			},
			cardOption(data){
				let is_do = data.is_do
				if(is_do == 1){
					uni.navigateTo({
						url:`/pages/tidings/myChat?uid=${data.uid}&id=${data.id}`
					});
				}else{
					this.deliver = {jid:data.id};
					this.resumeApply();
					this.jobsFavorites();
				}
			},
			async jobsFavorites(){
				let res = await http.jobsFavorites(this.list_param,this.headers);
				if(res == undefined)
					return false;
				let data = res.returnlist;
				this.isfull = res.isfull;
				this.nowPage = res.nowPage;
				let arr = [];
				data.map(item => {
					arr.push({
						id:item.jobs_id,
						did:item.did,
						uid:item.company_uid,
						cname:item.companyname,
						pay:item.wage_cn,
						jname:item.jobs_name,
						addtime:item.addtime,
						twotime:item.apply_addtime,
						is_do:item.apply,
						is_close:item.display,
						tips:item.tips
					})
				});
				this.ccList = arr;
			},
			async jobsFavoritesDel(){
				let res = await http.jobsFavoritesDel(this.del_param,this.headers);
				if(res.status == 200){
					uni.showToast({
						icon:"none",
						title:res.msg
					});
					this.ccList.splice(this.ccList.findIndex(item => item.did = this.del_param.id),1);
				}
			},
			async resumeApply(){
				let res = await http.resumeApply(this.deliver,this.headers);
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
