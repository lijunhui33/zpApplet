<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="fixedheader">
			<search-nav @search="search"></search-nav>
			<view class="drop_wrap">
				<view class="qs_container_title">
					<dropDownTitle v-for="(item,i) in dropList" :key="i" :param="item" @changeItem="changeItem"></dropDownTitle>
				</view>
				<view class="drop_cont" v-if="is_drop">
					<drop-up-area v-if="toggle == 1" 
						:listone="regionList" 
						:listtwo="regionList2"
						:pid="parentid"
						@sure="regionsure"
						@backPre="backPre"></drop-up-area>
					<drop-salary v-if="toggle == 2" :param="payList" @sure="sure"></drop-salary>
					<drop-salary v-if="toggle == 3" :param="natureList" @sure="sure2"></drop-salary>
					<drop-salary v-if="toggle == 4" :param="scaleList" @sure="sure3"></drop-salary>
				</view>
				<view v-if="is_drop" class="qs-close-clean" @click="cleanpro">
					清空条件
				</view>
				<view v-if="is_drop" class="qs-close-drop" :style="'height:'+closeHeight+'px'" @click="closeDrop"></view>
			</view>
		</view>
		<view style="height: 200rpx;"></view>
		<view v-for="count in 4" :key="count">
			<skeleton
				:loading="wholeloding"
				:avatarSize="skeleton1.avatarSize"
				:row="skeleton1.row"
				:showTitle="true">
			</skeleton>
		</view>
		<qs-view-five 
			v-for="(item,i) in ps_list" 
			:key="i" 
			:psList="item" 
			@childByValue="childByValue"></qs-view-five>
		<empty-list v-if="!is_empty"></empty-list>
		<uni-load-more :status="tmore" v-if="ps_list.length"></uni-load-more>
	</view>
</template>

<script>
	import searchNav from "../../components/search-nav/search-nav"
	import qsViewFive from "../../components/qs-view-five/qs-view-five.vue"
	import dropDownTitle from "../../components/drop-down-title/drop-down-title.vue"
	import dropSalary from "../../components/drop-salary/drop-salary.vue"
	import dropJob from "../../components/drop-job/drop-job.vue"
	import dropUpArea from "../../components/drop-up-area/drop-up-area.vue"
	import dropMore from "../../components/drop-more/drop-more.vue"
	import UniLoadMore from "../../components/uni-load-more/uni-load-more.vue"
	import http from "../../server/api-job.js"
	import Skeleton from "../../components/J-skeleton/J-skeleton.vue"
	import emptyList from "../../components/empty-list/empty-list.vue"
	import {source} from "../../utils/useList.js";
	export default{
		components:{
			searchNav,
			qsViewFive,
			dropDownTitle,
			dropSalary,
			dropJob,
			dropUpArea,
			dropMore,
			UniLoadMore,
			emptyList,
			Skeleton
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"名企推荐", //当type为3或4的时候icon右边的文字
				},
				ps_list:[],
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				themeColor: '#000000',
				titleColor: '#666666',
				filterResult: '',
				height:100,
				heighttwo:100,
				dropList:[
					{id:1,name:"地区",use:"dropwo"},
					{id:2,name:"行业",use:"dropwo"},
					{id:3,name:"性质",use:"dropwo"},
					{id:4,name:"规模",use:"dropwo"},
				],
				list_param:{
					page:1
				},
				payList:[],
				natureList:[],
				scaleList:[],
				regionList:[],
				regionList2:[],
				regionres:{},
				is_drop:false,
				toggle:"",
				temp_param:{},
				isfull:false,
				nowPage:1,
				tmore:"more",
				default_district:"4.48",
				parentid:"",
				wholeloding:true,
				skeleton1 : {
					avatarSize: '50px',
					row: 3,
					showTitle: true,
				},
				is_empty:true
			}
		},
		// 下拉刷新
		onPullDownRefresh(){
			this.nowPage = 1;
			this.list_param = {...this.list_param,page:this.nowPage};
			this.searchCompanyList()
		},
		// 上拉加载
		async onReachBottom(){
			if(!this.isfull){
				this.tmore = "loading"
				// uni.showNavigationBarLoading();//显示加载动画
				this.list_param = {...this.list_param,page:++this.nowPage}
				let res = await http.searchCompanyList(this.list_param)
				let list = res.returnlist
				this.ps_list = [...this.ps_list,...source(list)]
				this.isfull = res.isfull
				this.nowPage = res.nowPage
			}else{
				this.tmore = "noMore"
			}
		},
		onLoad:function(option) {
			this.searchCompanyList();
			this.searchJobsRegion();
			this.searchCompanyPosition();
			this.searchCompanyNature();
			this.searchCompanyScale();
			try {
			    const res = uni.getSystemInfoSync();
				let dec = 750/res.windowWidth;
				this.height = res.windowHeight + 6 - 900/dec;
				this.heighttwo = res.windowHeight + 42 - 900/dec;
			} catch (e) {
			    // error
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onShareAppMessage(res){
			if (res.from === 'button') {// 来自页面内分享按钮
				console.log(res.target)
			}
			return {
				title: this.config.menuText,
				path: "/pages/index/famous"
			}
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
			async searchCompanyList(){
				let res = await http.searchCompanyList(this.list_param)
				let list = res.returnlist
				this.ps_list = source(list)
				this.isfull = res.isfull
				this.nowPage = res.nowPage
				uni.stopPullDownRefresh();
				if(this.isfull){
					this.tmore = "noMore"
				}
				this.wholeloding = false
				if(this.ps_list.length == 0){
					this.is_empty = false
				}else{
					this.is_empty = true
				}
			},
			changeItem(str){
				this.dropList = this.dropList.map((user)=>{
					user.use = "dropwo"
					return user
				})
				this.dropList[this.dropList.findIndex(item => item.id === str)].use = "dropwt"
				this.is_drop = true
				this.toggle = str
			},
			sure(e){
				this.payList = this.payList.map((user)=>{
					user.use = ""
					return user
				})
				let change = this.payList[this.payList.findIndex(item => item.id === e)];
				change.use = "clickview";
				this.dropList[1].name = change.name;
				this.list_param = {...this.list_param,"trade":e,page:1};
				this.searchCompanyList()
				this.is_drop = false
			},
			sure2(e){
				this.natureList = this.natureList.map((user)=>{
					user.use = ""
					return user
				})
				let change = this.natureList[this.natureList.findIndex(item => item.id === e)];
				change.use = "clickview";
				this.dropList[2].name = change.name;
				this.list_param = {...this.list_param,"nature":e,page:1};
				this.searchCompanyList()
				this.is_drop = false
			},
			sure3(e){
				this.scaleList = this.scaleList.map((user)=>{
					user.use = ""
					return user
				})
				let change = this.scaleList[this.scaleList.findIndex(item => item.id === e)];
				change.use = "clickview";
				this.dropList[3].name = change.name;
				this.list_param = {...this.list_param,"scale":e,page:1};
				this.searchCompanyList()
				this.is_drop = false
			},
			regionsure(id){
				let res = this.regionres
				this.regionList = this.regionList.map((user)=>{
					user.use = ""
					return user
				})
				let tom = this.regionList[this.regionList.findIndex(item => item.id === id)]
				tom.use = "clickmore"
				let data = res[id]
				if(data){
					let nextarr = []
					for(let key in data){
						nextarr.push({id:key,name:data[key].categoryname,parentid:data[key].parentid,use:""})
					}
					this.regionList = nextarr
					this.regionList2.push({id:tom.id,name:tom.name,parentid:tom.parentid,use:""})
					this.parentid = tom.parentid
				}else{
					this.dropList[0].name = tom.name;
					this.list_param = {...this.list_param,citycategory:id,page:1};
					this.searchCompanyList()
					this.is_drop = false
				}
			},
			async searchCompanyPosition(){
				let res = await http.searchCompanyPosition()
				let arr = []
				arr.push({id:"",name:"不限",use:""})
				for(let key in res){
					arr.push({id:key,name:res[key],use:""})
				}
				this.payList = arr
			},
			async searchCompanyNature(){
				let res = await http.searchCompanyNature()
				let arr = []
				arr.push({id:"",name:"不限",use:""})
				for(let key in res){
					arr.push({id:key,name:res[key],use:""})
				}
				this.natureList = arr
			},
			async searchCompanyScale(){
				let res = await http.searchCompanyScale()
				let arr = []
				arr.push({id:"",name:"不限",use:""})
				for(let key in res){
					arr.push({id:key,name:res[key],use:""})
				}
				this.scaleList = arr
			},
			async searchJobsRegion(){
				this.default_district = await http.defaultDistrict();
				let res = await http.searchJobsRegion()
				let arr = []
				let titleArr = []
				let dis = this.default_district
				let diss = dis.split(".")
				let data = res[diss[diss.length-1]]
				for(let key in data){
					arr.push({id:key,name:data[key].categoryname,parentid:data[key].parentid,use:""})
				}
				let one = res[0][diss[0]]
				titleArr.push({id:one.id,name:one.categoryname,parentid:one.parentid,use:""})
				for(let i=0;i<diss.length;i++){
					if(i+1 < diss.length){
						let two = res[diss[i]][diss[i+1]]
						titleArr.push({id:two.id,name:two.categoryname,parentid:two.parentid,use:""})
					}
				}
				this.regionList = arr
				this.regionList2 = titleArr
				this.parentid = diss[diss.length-2] || "0"
				this.regionres = res
			},
			childByValue:function(i){
				uni.navigateTo({
					url:"comDetail?id="+i
				})
			},
			search(str){
				this.is_drop = false
				this.list_param = {...this.list_param,key:str};
				this.searchCompanyList()
			},
			backPre(id){
				let arr = []
				let res = this.regionres
				let data = res[id]
				for(let key in data){
					arr.push({id:key,name:data[key].categoryname,parentid:data[key].parentid,use:""})
				}
				this.regionList2.splice(this.regionList2.findIndex(item => item.parentid === id),1)
				this.parentid = this.regionList2.length?this.regionList2[this.regionList2.length-1].parentid:0
				this.regionList = arr
			},
			closeDrop(){
				this.is_drop = false
			},
			cleanpro(){
				this.dropList = [
					{id:1,name:"地区",use:"dropwo"},
					{id:2,name:"行业",use:"dropwo"},
					{id:3,name:"性质",use:"dropwo"},
					{id:4,name:"规模",use:"dropwo"},
				];
				this.regionList.map(item => item.use = "");
				this.natureList.map(item => item.use = "");
				this.scaleList.map(item => item.use = "");
				this.payList.map(item => item.use = "");
				this.list_param = {};
				this.regionList2 = [];
				let res = this.regionres[0];
				let arr = [];
				for(let key in res){
					arr.push({id:key,name:res[key].categoryname,parentid:res[key].parentid,use:""})
				}
				this.regionList = arr;
				this.searchCompanyList();
				this.is_drop = false;
			}
		},
		computed:{
			closeHeight(){
				if(this.toggle == 1){
					return this.height
				}else{
					return this.heighttwo
				}
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/famous.less";
	@import "../../common/search.less";
</style>
