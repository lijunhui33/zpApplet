<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="sub_cont_one" v-if="loading">
			<view class="notice">
				选择职位订阅条件，实现精准匹配个性化职位订制， 让我们帮你挑工作！
			</view>
			<!-- #ifdef MP-BAIDU -->
			<form @submit="formSubmit" 
				report-submit="true" 
				report-type="subscribe"
				subscribe-id="wangcaijob"
				template-id="BD1481">
			<!-- #endif -->
			<view class="edit_input">
				<view class="label">
					<view class="red">*</view>
					<view class="">意向职位</view>
				</view>
				<view class="box_input" @click="showBox(1)">
					<view>{{seltrade}}</view>
					<image src="../../static/image/back3.png" mode=""></image>
				</view>
			</view>
			<view class="qs-line"></view>
			<view class="edit_input">
				<view class="label">
					<view class="red">*</view>
					<view class="">意向地区</view>
				</view>
				<view class="box_input" @click="showBox(2)">
					<view>{{selregion}}</view>
					<image src="../../static/image/back3.png" mode=""></image>
				</view>
			</view>
			<view class="qs-line"></view>
			<view class="edit_input">
				<view class="label">
					<view class="red">*</view>
					<view class="">意向薪资</view>
				</view>
				<view class="box_input">
					<picker mode="selector" :range="education" :value="index" :range-key="'name'" @change="bindPickerChange">
						<view>{{education[index].name}}</view>
					</picker>
					<image src="../../static/image/back3.png" mode=""></image>
				</view>
			</view>
			<view class="qs-line"></view>
			<!-- #ifndef MP-BAIDU -->
			<view class="mysubmit" @click="qs_sub" v-if="is_update">
				订阅职位
			</view>
			<view class="mysubmit" @click="qs_sub_up" v-if="!is_update">
				订阅职位
			</view>
			<!-- #endif -->
			<!-- #ifdef MP-BAIDU -->
			<button form-type="submit" class="mysubmit">订阅职位</button>
			</form>
			<!-- #endif -->
		</view>
		<view class="sub_detail" v-if="!loading" :style="'min-height:'+ht+'px;background:#f5f5f5;'">
			<view style="width: 100%;height: 10rpx;"></view>
			<view class="cont">
				<image class="topimg" src="../../static/image/huabian.png" mode=""></image>
				<view class="title">
					您已成功订阅以下类型职位
				</view>
				<view class="info">
					<two-text v-for="(item,i) in one" :key="i" :dog="item"></two-text>
				</view>
				<view class="bottom">
					我们将每周五推送最新、最适合您的职位信息至您的微信，让您不再错过好工作！
				</view>
			</view>
			<view class="mysubmit" style="margin: 60rpx 40rpx 40rpx 40rpx;" @click="updateSub">
				修改订阅
			</view>
			<!-- #ifdef MP-WEIXIN -->
			<button class="mysubmit" style="margin: 60rpx 40rpx 40rpx 40rpx;" @tap="tapName" v-if="authorize">
				订阅消息通知
			</button>
			<!-- #endif -->
			<view class="reset" @click="resetLoad">
				取消订阅
			</view>
		</view>
		<view class="intentback" v-if="is_drop">
			<intent-region
				v-if="regionBool"
				:listone="regionList"
				:listtwo="regionList2"
				:listthree="topReg"
				:pid="parentid"
				:is_word="1"
				@sure="regionsure"
				@backPre="backPre"
				@closechk="closechk"
				@resetOrSave="resetOrSave"></intent-region>
			<intent-position
				v-if="jobBool"
				:listone="jobList2"
				:listtwo="jobList3"
				:listthree="jobList4"
				:listfour="topJob"
				:is_word="1"
				@sure="jobsure"
				@closechk="closechk2"
				@resetOrSave="ipResetOrSave"></intent-position>
		</view>
		<view class="intent" v-if="is_drop"></view>
	</view>
</template>

<script>
	import intentRegion from "../../components/intent-region/intent-region"
	import intentPosition from "../../components/intent-position/intent-position"
	import coverSelect from "../../components/cover-select/cover-select.vue"
	import wPicker from "@/components/w-picker/w-picker.vue"
	import twoText from "../../components/two-text/two-text.vue"
	import http from "../../server/api-job.js"
	import httptwo from "../../server/api-have.js"
	export default{
		components:{
			coverSelect,
			wPicker,
			twoText,
			intentRegion,
			intentPosition
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"订阅职位", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				headers:{},
				index:0,
				education:[{id:"",name:"请选择"}],
				one:[
					{src:"/static/image/diqu.png",name:"",use:"qs_one_color"},
					{src:"/static/image/gongzi.png",name:"",use:"qs_one_color"},
					{src:"/static/image/gongzuoxingzhi.png",name:"",use:"qs_one_color"},
				],
				loading:true,
				ht:0,
				selregion:"请选择",
				seltrade:"请选择",
				regionList:[],
				regionList2:[],
				regionList3:[],
				jobWhole:[],
				jobList1:[],
				jobList2:[],
				jobList3:[],
				jobList4:[],
				regionres:{},
				is_drop:false,
				default_district:"4.48",
				parentid:"",
				regionBool:false,
				jobBool:false,
				p1:0,
				p2:1,
				post_list:[],
				list_param:{},
				init_up:[],
				is_update:true,
				intjobid:{},
				intregid:{},
				baiducode:"",
				authorize:true,
				sub_id:""
			}
		},
		onLoad() {
			const token = uni.getStorageSync('token');
			if(token){
				this.headers = {"Authorization":"Bearer "+token};
				// #ifdef MP-WEIXIN
				this.haveSubscribe();
				// #endif
				// #ifdef MP-BAIDU
				this.bdhaveSubscribe();
				// 用户首次登陆小程序同步 百度 App登陆态
				swan.login({
					success: res => {
						console.log('login success', res);
					},
					fail: err => {
						console.log('login fail', err);
					}
				});
				// #endif
				this.searchJobsRegion();
				this.searchJobsPosition();
				this.searchJobsSalary();
				try {
				    const res = uni.getSystemInfoSync();
					this.ht = res.windowHeight;
				} catch (e) {
				    // error
				}
			}else{
				uni.navigateTo({
					url:"/pages/personal/myLogin?login=true"
				})
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
				path: "/pages/index/subscribe"
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
			verification(){
				if(this.seltrade.trim() == "请选择"){
					uni.showToast({
						icon:"none",
						title:"请选择意向职位"
					})
					return false
				}else if(this.selregion.trim() == "请选择"){
					uni.showToast({
						icon:"none",
						title:"请选择意向地区"
					})
					return false
				}else if(this.index == 0){
					uni.showToast({
						icon:"none",
						title:"请选择意向薪资"
					})
					return false
				}
				return true
			},
			// #ifdef MP-WEIXIN
			async weixinappTemplate(){
				let res = await http.weixinappTemplate({},this.headers);
				uni.setStorageSync("temp_id",res);
			},
			async qs_sub(){
				let bool = this.verification();
				if(!bool){
					return false
				}
				let data = await uni.login({
					provider: 'weixin'
				});
				let sd = this.education[this.index];
				this.list_param = {
					code:data[1].code,
					intention_jobs_id:this.intjobid.id,
					intention_jobs:this.intjobid.name,
					wage:sd.id,
					wage_cn:sd.name,
					district:this.intregid.id,
					district_cn:this.intregid.name
				}
				this.subscribeAdd();
			},
			tapName(e){
				let temp_id = uni.getStorageSync("temp_id");
				this.WxAuthorize();
				 wx.requestSubscribeMessage({
					tmplIds: [temp_id],
					success: (res) => {
					  if (res[temp_id] === 'accept'){
						wx.showToast({
						  title: '订阅OK！',
						  duration: 1000
						})
					  }
					},
					fail(err) {
					  //失败
					  console.error(err);
					}
				 })
			},
			async haveSubscribe(){
				let res = await httptwo.haveSubscribe({},this.headers)
				console.log(res);
				if(res.status == 200){
					this.loading = false;
					this.weixinappTemplate();
					this.subscribeQuery();
				}else{
					this.loading = true
				}
			},
			async subscribeQuery(){
				let res = await http.subscribeQuery({},this.headers);
				console.log(res);
				if(res){
					if(res[0].authorize == "1"){
						this.authorize = false
					}else{
						this.authorize = true
					}
					this.sub_id = res[0].id
					this.init_up = res
					if(res[0].district_cn == undefined || res[0].district_cn == "undefined"){
						this.one[0].name = "未选择"
					}else{
						this.one[0].name = res[0].district_cn
					}
					this.one[1].name = res[0].wage_cn || "未选择"
					if(res[0].intention_jobs == undefined || res[0].intention_jobs == "undefined"){
						this.one[2].name = "未选择"
					}else{
						this.one[2].name = res[0].intention_jobs
					}
				}
			},
			async WxAuthorize(){
				let res = await http.authorize({id:this.sub_id},this.headers);
				console.log(res);
				if(res){
					this.authorize = false
				}
			},
			qs_sub_up(){
				let bool = this.verification();
				if(!bool){
					return false
				}
				let sd = this.education[this.index];
				this.list_param = {
					intention_jobs_id:this.intjobid.id,
					intention_jobs:this.intjobid.name,
					wage:sd.id,
					wage_cn:sd.name,
					district:this.intregid.id,
					district_cn:this.intregid.name
				}
				this.subscribeEdit();
			},
			async subscribeAdd(){
				let res = await http.subscribeAdd(this.list_param,this.headers);
				uni.setStorageSync("temp_id",res);
				this.haveSubscribe();
			},
			async subscribeDel(){
				let res = await http.subscribeDel({},this.headers);
			},
			async subscribeEdit(){
				let res = await http.subscribeEdit(this.list_param,this.headers);
				this.haveSubscribe();
			},
			// #endif
			showBox(b){
				switch (b){
					case 2:
						this.is_drop = true
						this.regionBool = true
						this.jobBool = false
						break;
					case 1:
						this.is_drop = true
						this.regionBool = false
						this.jobBool = true
						break;
					default:
						break;
				}
			},
			resetLoad(){
				// #ifndef MP-BAIDU
				this.subscribeDel();
				this.haveSubscribe();
				// #endif
				// #ifdef MP-BAIDU
				this.bdsubscribeDel();
				this.bdhaveSubscribe();
				// #endif
			},
			resetOrSave(i){
				if(i == 1){
					this.selregion = "请选择";
					this.intregid = {id:"",name:""};
					this.is_drop = false
					this.regionBool = false
				}else{
					this.is_drop = false
					this.regionBool = false
				}
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
			regionsure(id){
				let res = this.regionres
				let tom = this.regionList[this.regionList.findIndex(item => item.id === id)]
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
					this.selregion = tom.name;
					this.intregid = tom;
					this.is_drop = false;
				}
			},
			ipResetOrSave(i){
				if(i == 1){
					this.seltrade = "请选择";
					this.intjobid = {id:"",name:""};
					this.is_drop = false
					this.regionBool = false
				}else{
					this.is_drop = false
					this.regionBool = false
				}
			},
			async searchJobsPosition(){
				let res = await http.searchJobsPosition();
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
				this.jobWhole = res;
				this.jobList2 = arr;
				this.jobList2[0].use = "clickview";
				this.jobList3 = arr2;
			},
			jobsure(data){
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
					this.p1 = id;
					this.jobList3 = arr
				}else if(str == 2){
					let arr = []
					this.jobList3 = this.jobList3.map((user)=>{
						user.use = ""
						return user
					})
					this.jobList3[this.jobList3.findIndex(item => item.id === id)].use = "clickview"
					for(let key in res){
						if(res[key].parentid == id){
							arr.push({id:res[key].id,name:res[key].categoryname,parentid:res[key].parentid,use:""})
						}
					}
					this.p2 = id;
					this.jobList4 = arr
				}else{
					let tony = this.jobList4[this.jobList4.findIndex(item => item.id === id)];
					tony.use = "clickview";
					this.seltrade = tony.name;
					this.intjobid = tony;
					this.is_drop = false;
				}
			},
			closechk2(id){
				this.jobList1.splice(this.jobList1.findIndex(item => item.id === id),1)
				this.post_list.splice(this.post_list.findIndex(item => item.p3 === id),1)
			},
			closechk(id){
				let tom = this.regionList[this.regionList.findIndex(item => item.id === id)]
				if(tom){
					tom.use = ""
				}
				this.regionList3.splice(this.regionList3.findIndex(item => item.id === id),1)
			},
			async searchJobsSalary(){
				let res = await http.searchJobsSalary();
				let arr = []
				arr.push({id:"",name:"请选择"})
				for(let key in res){
					arr.push({id:key,name:res[key]})
				}
				this.education = arr
			},
			bindPickerChange(e){
				this.index = e.target.value
			},
			updateSub(){
				let res = this.init_up
				this.selregion = res[0].district_cn || "请选择";
				this.seltrade = res[0].intention_jobs || "请选择";
				this.intjobid = {id:res[0].intention_jobs_id,name:res[0].intention_jobs};
				this.intregid = {id:res[0].district,name:res[0].district_cn};
				if(res[0].wage == "0"){
					this.index = 0;
				}else{
					this.index = this.education.findIndex(item => item.id === res[0].wage);
				}
				let district = res[0].district;
				let intention_jobs_id = res[0].intention_jobs_id;
				let dts = district.split(",");
				let ijs = intention_jobs_id.split(",");
				let dcn = res[0].district_cn.split(",");
				let ijbs = res[0].intention_jobs.split(",");
				let temp_reg = []
				dts.map((item,k) => {
					let arr = item.split(".");
					temp_reg.push({id:arr[arr.length-1],name:dcn[k]})
				});
				this.regionList3 = temp_reg
				let temp_job = []
				ijs.map((item,k) => {
					let arr = item.split(".");
					this.post_list.push({p1:arr[0],p2:arr[1],p3:arr[2]})
					temp_job.push({id:arr[2],name:ijbs[k]})
				})
				this.jobList1 = temp_job;
				this.is_update = false
				this.loading = true
			},
			
			// #ifdef MP-BAIDU
			async baiduLogin(){
				let that = this;
				await swan.login({
				    success: res => {
						that.baiducode = res.code
				    }
				});
			},
			async formSubmit(e){
				let bool = this.verification();
				if(!bool){
					return false
				}
				let that = this;
				swan.login({
				    success: res => {
						let sd = that.education[that.index];
						that.list_param = {
							code:res.code,
							scene_id:e.detail.formId,
							intention_jobs_id:that.intjobid.id,
							intention_jobs:that.intjobid.name,
							wage:sd.id,
							wage_cn:sd.name,
							district:that.intregid.id,
							district_cn:that.intregid.name
						};
						if(that.is_update){
							that.bdsubscribeAdd();
						}else{
							that.bdsubscribeEdit();
						}
				    }
				});
			},
			async bdhaveSubscribe(){
				let res = await httptwo.bdhaveSubscribe({},this.headers);
				console.log(res);
				if(res.status == 200){
					this.loading = false;
					this.bdsubscribeQuery();
				}else{
					this.loading = true
				}
			},
			async bdsubscribeQuery(){
				let res = await http.bdsubscribeQuery({},this.headers)
				this.init_up = res
				this.one[0].name = res[0].district_cn || "未选择"
				this.one[1].name = res[0].wage_cn || "未选择"
				this.one[2].name = res[0].intention_jobs || "未选择"
			},
			async bdsubscribeAdd(){
				let res = await http.bdsubscribeAdd(this.list_param,this.headers);
				if(res){
					this.bdhaveSubscribe()
				}
			},
			async bdsubscribeDel(){
				let res = await http.bdsubscribeDel({},this.headers);
			},
			async bdsubscribeEdit(){
				let res = await http.bdsubscribeEdit(this.list_param,this.headers);
				if(res){
					this.bdhaveSubscribe()
				}
			},
			// #endif
		},
		computed:{
			topJob(){
				return this.jobList1
			},
			topReg(){
				return this.regionList3
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/subscribe.less";
	@import "../../common/edit.less";
</style>
