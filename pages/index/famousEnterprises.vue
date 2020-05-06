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
					<drop-job v-if="toggle == 2" 
						:listone="jobList2" 
						:listtwo="jobList3"
						:listthree="jobList4"
						@sure="jobsure"></drop-job>
					<drop-salary v-if="toggle == 3" :param="payList" @sure="sure"></drop-salary>
					<drop-more v-if="toggle == 4" 
						:moreone="moreList" 
						:moretwo="moreList2" 
						:morethree="moreList3"
						@sure="moresure"
						@saveOrReset="saveOrReset"></drop-more>
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
				avatarSize="false"
				:row="skeleton1.row"
				:showTitle="true">
			</skeleton>
		</view>
		<qs-view-three 
			v-for="(item,i) in ps_list" 
			:key="i" 
			:psList="item" 
			@childByValue="childByValue"></qs-view-three>
		<empty-list v-if="!is_empty"></empty-list>
		<emptyListFail v-if="is_local"></emptyListFail>
		<uni-load-more :status="tmore" v-if="ps_list.length"></uni-load-more>
	</view>
</template>

<script>
	import searchNav from "../../components/search-nav/search-nav"
	import qsViewThree from "../../components/qs-view-three/qs-view-three.vue"
	import dropDownTitle from "../../components/drop-down-title/drop-down-title.vue"
	import dropSalary from "../../components/drop-salary/drop-salary.vue"
	import dropJob from "../../components/drop-job/drop-job.vue"
	import dropUpArea from "../../components/drop-up-area/drop-up-area.vue"
	import dropMore from "../../components/drop-more/drop-more.vue"
	import Skeleton from "../../components/J-skeleton/J-skeleton.vue"
	import UniLoadMore from "../../components/uni-load-more/uni-load-more.vue"
	import http from "../../server/api-job.js"
	import emptyList from "../../components/empty-list/empty-list.vue"
	import emptyListFail from "../../components/empty-list/empty-list-fail.vue"
	import {voluation} from "../../utils/useList.js";
	export default{
		components:{
			searchNav,
			qsViewThree,
			dropDownTitle,
			dropSalary,
			dropJob,
			dropUpArea,
			dropMore,
			UniLoadMore,
			emptyList,
			emptyListFail,
			Skeleton
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"职位搜索", //当type为3或4的时候icon右边的文字
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
					{id:2,name:"职位",use:"dropwo"},
					{id:3,name:"薪资",use:"dropwo"},
					{id:4,name:"更多",use:"dropwo"},
				],
				list_param:{
					page:1
				},
				payList:[],
				jobWhole:[],
				jobList2:[],
				jobList3:[],
				jobList4:[],
				moreList:[],
				moreList2:[],
				moreList3:[],
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
				jobtag:[],
				wholeloding:true,
				skeleton1 : {
					avatarSize: '52px',
					row: 3,
					showTitle: true,
				},
				is_empty:true,
				is_local:false
			}
		},
		// 下拉刷新
		onPullDownRefresh(){
			this.nowPage = 1;
			this.list_param = {...this.list_param,page:this.nowPage};
			this.searchJobsList()
		},
		// 上拉加载
		async onReachBottom(){
			if(!this.isfull){
				this.tmore = "loading"
				// uni.showNavigationBarLoading();//显示加载动画
				this.list_param = {...this.list_param,page:++this.nowPage}
				let res = await http.nearbyJobsList(this.list_param)
				let list = res.returnlist
				this.ps_list = [...this.ps_list,...voluation(list)]
				this.isfull = res.isfull
				this.nowPage = res.nowPage
			}else{
				this.tmore = "noMore"
			}
			uni.hideNavigationBarLoading();
		},
		onLoad:function(option) {
			let that = this
			uni.getLocation({
				type: 'wgs84',
				success: function (res) {
					that.is_local = false;
					that.list_param = {...that.list_param,lat:res.latitude,lng:res.longitude}
					that.searchJobsList();
				},
				fail:function(){
					that.wholeloding = false;
					that.is_local = true;
				}
			});
			this.searchJobsSalary();
			this.searchJobsRegion();
			this.searchJobsPosition();
			this.searchJobsMore();
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
				path: "/pages/index/famousEnterprises"
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
			async searchJobsList(){
				let data = await http.nearbyJobsList(this.list_param)
				let list = data.returnlist
				this.ps_list = voluation(list)
				this.isfull = data.isfull
				this.nowPage = data.nowPage
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
				this.dropList[2].name = change.name;
				this.list_param = {...this.list_param,"salary":e,page:1};
				this.searchJobsList()
				this.is_drop = false
			},
			jobsure(e){
				let data = JSON.parse(e)
				let id = data.id
				let str = data.str
				let res = this.jobWhole
				if(str == 1){
					let arr = []
					this.jobList2 = this.jobList2.map((user)=>{
						user.use = ""
						return user
					})
					this.jobList2[this.jobList2.findIndex(item => item.id === id)].use = "clickview"
					for(let key in res){
						if(res[key].parentid == id){
							arr.push({id:res[key].id,name:res[key].categoryname,use:""})
						}
					}
					this.jobList3 = arr
					// this.is_drop = true
				}else if(str == 2){
					let arr = []
					this.jobList3 = this.jobList3.map((user)=>{
						user.use = ""
						return user
					})
					let change = this.jobList3[this.jobList3.findIndex(item => item.id === id)];
					change.use = "clickview";
					for(let key in res){
						if(res[key].parentid == id){
							arr.push({id:res[key].spell,name:res[key].categoryname,parentid:res[key].parentid,use:""})
						}
					}
					this.jobList4 = arr
					if(this.jobList4.length === 0){
						this.dropList[1].name = change.name;
						this.list_param = {...this.list_param,jobcategory:id,page:1};
						this.searchJobsList()
						this.is_drop = false
					}
					// this.is_drop = true
				}else{
					this.jobList4 = this.jobList4.map((user)=>{
						user.use = ""
						return user
					})
					let change = this.jobList4[this.jobList4.findIndex(item => item.id === id)];
					change.use = "clickview";
					this.dropList[1].name = change.name;
					this.list_param = {...this.list_param,jobcategory:id,page:1};
					this.searchJobsList()
					this.is_drop = false
				}
			},
			moresure(e){
				let data = JSON.parse(e)
				let id = data.id
				let flag = data.str
				switch (flag){
					case 1:
						let tom1 = this.moreList[this.moreList.findIndex(item => item.id === id)];
						if(tom1.use == "clickmore"){
							tom1.use = "";
							this.temp_param = {...this.temp_param,"education":""};
						}else{
							this.moreList = this.moreList.map((user)=>{
								user.use = ""
								return user
							})
							this.moreList[this.moreList.findIndex(item => item.id === id)].use = "clickmore"
							this.temp_param = {...this.temp_param,"education":id}
							this.is_drop = true
						}
						break;
					case 2:
						let tom2 = this.moreList2[this.moreList2.findIndex(item => item.id === id)];
						if(tom2.use == "clickmore"){
							tom2.use = "";
							this.temp_param = {...this.temp_param,"experience":""};
						}else{
							this.moreList2 = this.moreList2.map((user)=>{
								user.use = ""
								return user
							})
							this.moreList2[this.moreList2.findIndex(item => item.id === id)].use = "clickmore"
							this.temp_param = {...this.temp_param,"experience":id}
							this.is_drop = true
						}
						break;
					case 3:
						let tom3 = this.moreList3[this.moreList3.findIndex(item => item.id === id)];
						if(tom3.use == "clickmore"){
							tom3.use = "";
							this.jobtag.splice(this.jobtag.findIndex(item => item === id),1);
							let rom = this.jobtag.join(",");
							this.temp_param = {...this.temp_param,"jobtag":rom};
						}else{
							this.moreList3 = this.moreList3.map((user)=>{
								user.use = ""
								return user
							})
							this.moreList3[this.moreList3.findIndex(item => item.id === id)].use = "clickmore"
							this.temp_param = {...this.temp_param,"jobtag":id};
							this.is_drop = true
						}
						break;
					default:
						break;
				}
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
					nextarr.push({id:"",name:"不限"});
					for(let key in data){
						nextarr.push({id:key,name:data[key].categoryname,parentid:data[key].parentid,use:""})
					}
					this.regionList = nextarr
					this.regionList2.push({id:tom.id,name:tom.name,parentid:tom.parentid,use:""})
					this.parentid = tom.parentid
				}else{
					this.dropList[0].name = tom.name;
					this.list_param = {...this.list_param,citycategory:id,page:1};
					this.searchJobsList()
					this.is_drop = false
				}
			},
			async searchJobsSalary(){
				let res = await http.searchJobsSalary()
				let arr = []
				arr.push({id:"",name:"全部",use:""})
				for(let key in res){
					arr.push({id:key,name:res[key],use:""})
				}
				this.payList = arr
			},
			async searchJobsRegion(){
				this.default_district = await http.defaultDistrict();
				let res = await http.searchJobsRegion()
				let arr = []
				let titleArr = []
				let dis = this.default_district
				let diss = dis.split(".")
				let data = res[diss[diss.length-1]]
				arr.push({id:"",name:"不限"})
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
				console.log(this.parentid);
			},
			async searchJobsPosition(){
				let res = await http.searchJobsPosition()
				let arr = []
				let arr2 = []
				for(let key in res){
					if(res[key].parentid == 0){
						arr.push({id:res[key].id,name:res[key].categoryname,use:""})
					}
					if(res[key].parentid == 1){
						arr2.push({id:res[key].id,name:res[key].categoryname,use:""})
					}
				}
				this.jobWhole = res
				this.jobList2 = arr
				this.jobList3 = arr2
			},
			async searchJobsMore(){
				let res = await http.searchJobsMore()
				let arredu = []
				let arrtag = []
				let arrexp = []
				let edu = res.education
				let tag = res.QS_jobtag
				let exp = res.experience
				for(let key in edu){
					if(exp[key] == "不限"){
						arredu.push({id:key,name:exp[key],use:"clickmore"})
					}else{
						arredu.push({id:key,name:edu[key],use:""})
					}
				}
				for(let key in tag){
					if(exp[key] == "不限"){
						arrtag.push({id:key,name:exp[key],use:"clickmore"})
					}else{
						arrtag.push({id:key,name:tag[key],use:""})
					}
				}
				for(let key in exp){
					if(exp[key] == "不限"){
						arrexp.push({id:key,name:exp[key],use:"clickmore"})
					}else{
						arrexp.push({id:key,name:exp[key],use:""})
					}
				}
				this.moreList = arredu
				this.moreList2 = arrexp
				this.moreList3 = arrtag
			},
			saveOrReset(state){
				if(state == 1){
					this.moreList = this.moreList.map((user)=>{
						user.use = ""
						return user
					})
					this.moreList2 = this.moreList2.map((user)=>{
						user.use = ""
						return user
					})
					this.moreList3 = this.moreList3.map((user)=>{
						user.use = ""
						return user
					})
					this.is_drop = false
				}else{
					this.list_param = {...this.list_param,...this.temp_param,page:1};
					this.searchJobsList()
					this.is_drop = false
				}
			},
			childByValue:function(i){
				uni.navigateTo({
					url:"jobDetail?id="+i
				})
			},
			search(str){
				this.is_drop = false
				this.list_param = {...this.list_param,key:str},
				this.searchJobsList()
			},
			backPre(id){
				console.log(id);
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
					{id:2,name:"职位",use:"dropwo"},
					{id:3,name:"薪资",use:"dropwo"},
					{id:4,name:"更多",use:"dropwo"},
				];
				this.regionList.map(item => item.use = "");
				this.jobList2.map(item => item.use = "");
				this.jobList3.map(item => item.use = "");
				this.jobList4.map(item => item.use = "");
				this.payList.map(item => item.use = "");
				this.moreList.map(item => item.use = "");
				this.moreList2.map(item => item.use = "");
				this.moreList3.map(item => item.use = "");
				this.list_param = {};
				this.regionList2 = [];
				let res = this.regionres[0];
				let arr = [];
				for(let key in res){
					arr.push({id:key,name:res[key].categoryname,parentid:res[key].parentid,use:""})
				}
				this.regionList = arr;
				let that = this
				uni.getLocation({
					type: 'wgs84',
					success: function (res) {
						that.list_param = {...that.list_param,lat:res.latitude,lng:res.longitude};
						that.searchJobsList();
					}
				});
				this.is_drop = false;
			}
		},
		computed:{
			closeHeight(){
				if(this.toggle == 1 || this.toggle == 4){
					return this.height
				}else{
					return this.heighttwo
				}
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/search.less";
</style>
