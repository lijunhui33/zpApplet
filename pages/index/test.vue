<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<search-nav @search="search"></search-nav>
		<view class="qs-line" style="margin-top: 40rpx;margin-bottom: 20rpx;"></view>
		<view class="recommend">
			<title-nav :titleCont="titleOne"></title-nav>
			<view class="list">
				<rec-list v-for="(item,i) in recArr" :key="i" :param="item" @toCom="toCom"></rec-list>
			</view>
		</view>
		<view class="recommend" style="margin-top: 40rpx;">
			<title-nav :titleCont="titleTwo"></title-nav>
			<view class="ralist">
				<view class="rabtn" v-for="(item,i) in raArr" :key="i" @click="hotsearch(item)">
					{{item}}
				</view>
			</view>
		</view>
		<view class="recommend" style="margin-top: 40rpx;">
			<title-nav :titleCont="titleThree" @editInfo="editInfo" v-if="raArr2.length"></title-nav>
			<view class="ralist">
				<view class="rabtn" v-for="(item,i) in raArr2" :key="i" @click="hotsearch(item)">
					{{item}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import searchNav from "../../components/search-nav/search-nav"
	import titleNav from "../../components/title-nav/title-nav"
	import recList from "../../components/rec-list/rec-list"
	import http from "../../server/api-index.js";
	export default{
		components:{
			searchNav,
			titleNav,
			recList
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"职位搜索", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				titleOne:{
					name:"推荐企业",src:"",btn:"优选名企职等你来"
				},
				titleTwo:{
					name:"热门搜索",src:"",btn:""
				},
				titleThree:{
					name:"搜索记录",src:"/static/image/delete.png",btn:""
				},
				recArr:[],
				raArr:[],
				raArr2:[]
			}
		},
		onLoad() {
			this.searchSpreadOut();
			if(uni.getStorageSync("raArr2")){
				this.raArr2 = JSON.parse(uni.getStorageSync("raArr2"));
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
			async searchSpreadOut(){
				let res = await http.searchSpreadOut();
				let companylist = res.companylist;
				let hotlist = res.hotlist;
				let arr = [];
				let arr2 = [];
				companylist.map(item => {
					arr.push({id:item.id,src:item.logo,name:item.companyname,num:item.jobs_count})
				});
				this.recArr = arr;
				hotlist.map(item => {
					arr2.push(item.w_word)
				})
				this.raArr = arr2;
			},
			search(str){
				if(uni.getStorageSync("raArr2")){
					this.raArr2 = JSON.parse(uni.getStorageSync("raArr2"));
				}
				if(this.raArr2.length>7){
					this.raArr2.shift();
					this.raArr2.push(str);
				}else{
					this.raArr2.push(str);
				}
				uni.setStorageSync("raArr2",JSON.stringify(this.raArr2));
				uni.navigateTo({
					url:"job?key="+str
				})
			},
			toCom(id){
				uni.navigateTo({
					url:"comDetail?id="+id
				})
			},
			hotsearch(str){
				this.search(str)
			},
			editInfo(str){
				this.raArr2 = [];
				uni.removeStorageSync("raArr2")
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/search.less";
</style>
