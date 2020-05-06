<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ec_cont">
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">求职状态</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="jst" :value="pick.i4" :range-key="'name'" name="current" @change="bindPickerChange4">
							<view>{{jst[pick.i4].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">工作性质</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="jn" :value="pick.i5" :range-key="'name'" name="nature" @change="bindPickerChange5">
							<view>{{jn[pick.i5].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
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
						<view class="">意向薪资</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="pay" :value="pick.i7" :range-key="'name'" name="wage" @change="bindPickerChange7">
							<view>{{pay[pick.i7].name}}</view>
						</picker>
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
						<view class="white">*</view>
						<view class="">意向行业</view>
					</view>
					<view class="box_input" @click="showBox(3)" >
						<view style="width: 400rpx;text-align: right;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">{{seltrade_cn}}</view>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存修改</button>
			</form>
		</view>
		<view class="intentback" v-if="is_drop">
			<intent-region
				v-if="regionBool"
				:listone="regionList"
				:listtwo="regionList2"
				:listthree="topReg"
				:pid="parentid"
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
				@sure="jobsure"
				@closechk="closechk2"
				@resetOrSave="ipResetOrSave"></intent-position>
			<intent-job
				v-if="tradeBool"
				:listone="tradeList1"
				:listthree="tradeList2"
				@sure="majorsure"
				@closechk="closechk3"
				@resetOrSave="tdresetOrSave"></intent-job>
		</view>
		<view class="intent" v-if="is_drop"></view>
	</view>
</template>

<script>
	import intentRegion from "../../components/intent-region/intent-region"
	import intentJob from "../../components/intent-job/intent-job"
	import intentPosition from "../../components/intent-position/intent-position"
	import http from "../../server/api-job.js"
	import httptwo from "../../server/api-resume.js"
	export default {
		components:{
			intentRegion,
			intentJob,
			intentPosition
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"编辑求职意向", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				headers:{},
				pick:{
					i1:0,
					i2:0,
					i3:0,
					i4:0,
					i5:0,
					i6:0,
					i7:0,
					i8:1,
				},
				education:[
					{id:"",name:"请选择"},
				],
				enter:[],
				end:[
					{id:"",name:"请选择"},
				],
				experience:[
					{id:"",name:"请选择"},
				],
				pay:[
					{id:"",name:"请选择"},
				],
				jst:[
					{id:"",name:"请选择"},
				],
				jn:[
					{id:"",name:"请选择"},
				],
				selregion:"请选择",
				seltrade:"请选择",
				seltrade_cn:"请选择",
				regionList:[],
				regionList2:[],
				regionList3:[],
				jobWhole:[],
				jobList1:[],
				jobList2:[],
				jobList3:[],
				jobList4:[],
				tradeList1:[],
				tradeList2:[],
				regionres:{},
				is_drop:false,
				default_district:"4.48",
				parentid:"",
				regionBool:false,
				jobBool:false,
				tradeBool:false,
				p1:0,
				p2:1,
				post_list:[],
				list_param:{},
				init_up:{},
				is_update:true
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			this.searchJobsSalary();
			this.searchJobsRegion();
			this.searchJobsPosition();
			this.ResumeBasicChoice();
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
				let jid = []
				this.post_list.map(item => {
					jid.push(`${item.p1}.${item.p2}.${item.p3}`)
				})
				let jd = jid.join(",");
				let rid = [];
				this.regionList3.map(item => {
					rid.push(item.id)
				})
				let rd = rid.join(",");
				let tid = [];
				this.tradeList2.map(item => {
					tid.push(item.id)
				});
				let td = tid.join(",");
				this.list_param = {
					intention_jobs_id:jd,
					trade:td,
					district:rd,
					nature:this.jn[fd.nature].id,
					current:this.jst[fd.current].id,
					wage:this.pay[fd.wage].id,
				};
				console.log(this.list_param);
				this.resumeEditIntention();
			},
			bindPickerChange4(e){
				this.pick.i4 = e.target.value
			},
			bindPickerChange5(e){
				this.pick.i5 = e.target.value
			},
			bindPickerChange7(e){
				this.pick.i7 = e.target.value
			},
			showBox(b){
				switch (b){
					case 3:
						this.is_drop = true
						this.regionBool = false
						this.jobBool = false
						this.tradeBool = true
						break;
					case 2:
						this.is_drop = true
						this.regionBool = true
						this.jobBool = false
						this.tradeBool = false
						break;
					case 1:
						this.is_drop = true
						this.regionBool = false
						this.jobBool = true
						this.tradeBool = false
						break;
					default:
						break;
				}
			},
			async searchJobsMore(){
				let res = await http.searchJobsMore()
				let edu = res.education;
				let exp = res.experience;
				let arr = [];
				let arr2 = [];
				for(let key in edu){
					arr.push({id:key,name:edu[key]})
				}
				for(let key in exp){
					arr2.push({id:key,name:exp[key]})
				}
				this.education = [...this.education,...arr];
				this.experience = [...this.experience,...arr2];
			},
			async searchJobsSalary(){
				let res = await http.searchJobsSalary();
				let arr = [];
				for(let key in res){
					arr.push({id:key,name:res[key]})
				}
				this.pay = [...this.pay,...arr];
			},
			resetOrSave(i){
				if(i == 1){
					this.is_drop = false
					this.regionBool = false
				}else{
					let arr = []
					this.regionList3.map(item => arr.push(item.name))
					this.selregion = arr.join(",")
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
					if(tom.use == "clickmore"){
						this.regionList3.splice(this.regionList3.findIndex(item => item === tom),1)
						tom.use = ""
					}else{
						if(this.regionList3.length<3){
							tom.use = "clickmore"
							this.regionList3 = [...this.regionList3,tom]
						}else{
							uni.showToast({
								icon:"none",
								title:"最多输入3项"
							})
						}
					}
				}
			},
			ipResetOrSave(i){
				if(i == 1){
					this.is_drop = false
					this.regionBool = false
				}else{
					let arr = []
					this.jobList1.map(item => arr.push(item.name))
					this.seltrade = arr.join(",")
					this.is_drop = false
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
					this.is_drop = true
				}else if(str == 2){
					let arr = []
					this.jobList3 = this.jobList3.map((user)=>{
						user.use = ""
						return user
					})
					let tony = this.jobList3[this.jobList3.findIndex(item => item.id === id)];
					tony.use = "clickview";
					for(let key in res){
						if(res[key].parentid == id){
							arr.push({id:res[key].id,name:res[key].categoryname,parentid:res[key].parentid,use:""})
						}
					}
					this.p2 = id;
					this.jobList4 = arr
					this.is_drop = true
					if(this.jobList4.length === 0){
						if(this.jobList1.length < 3){
							if(this.jobList1.findIndex(item => item.id === id)){
								this.jobList1 = [...this.jobList1,tony];
								this.post_list.push({p1:this.p1,p2:this.p2,p3:0})
							}else{
								uni.showToast({
									icon:"none",
									title:"已选"
								})
							}
						}else{
							uni.showToast({
								icon:"none",
								title:"最多 3 项"
							})
						}
					}
				}else{
					let tony = this.jobList4[this.jobList4.findIndex(item => item.id === id)];
					tony.use = "clickview";
					if(this.jobList1.length < 3){
						if(this.jobList1.findIndex(item => item.id === id)){
							this.jobList1 = [...this.jobList1,tony];
							this.post_list.push({p1:this.p1,p2:this.p2,p3:id})
						}else{
							uni.showToast({
								icon:"none",
								title:"已选"
							})
						}
					}else{
						uni.showToast({
							icon:"none",
							title:"最多 3 项"
						})
					}
					this.is_drop = true
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
			async resumeDisplayIntention(){
				let res = await httptwo.resumeDisplayIntention({},this.headers);
				this.selregion = res[0].district_cn;
				this.seltrade = res[0].intention_jobs;
				this.seltrade_cn = res[0].trade_cn?res[0].trade_cn:"请选择";
				this.pick.i4 = this.jst.findIndex(item => item.name === res[0].current_cn);
				this.pick.i5 = this.jn.findIndex(item => item.name === res[0].nature_cn);
				this.pick.i7 = this.pay.findIndex(item => item.id === res[0].wage);
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
				console.log(dts);
				console.log(temp_reg);
				let temp_job = []
				ijs.map((item,k) => {
					let arr = item.split(".");
					this.post_list.push({p1:arr[0],p2:arr[1],p3:arr[2]})
					temp_job.push({id:arr[2],name:ijbs[k]})
				})
				this.jobList1 = temp_job;
				let trade = res[0].trade;
				let trade_cn = res[0].trade_cn;
				if(trade != ""){
					let tds = trade.split(",");
					let tds_cn = trade_cn.split(",");
					let temp_tra = [];
					tds.map((item,k) => {
						temp_tra.push({id:item,name:tds_cn[k]})
					});
					this.tradeList2 = temp_tra;
				}
			},
			async ResumeBasicChoice(){
				let res = await httptwo.ResumeBasicChoice({},this.headers);
				let major = res.QS_trade;
				let nature = res.QS_jobs_nature;
				let current = res.QS_current;
				this.enter = res.birth;
				this.enter.unshift("请选择");
				let arr = [];
				let arr2 = [];
				let arr3 = [];
				for(let key in nature){
					arr.push({id:key,name:nature[key]})
				}
				for(let key in current){
					arr2.push({id:key,name:current[key]})
				}
				for(let key in major){
					arr3.push({id:key,name:major[key],use:""})
				}
				this.jn = [...this.jn,...arr];
				this.jst = [...this.jst,...arr2];
				this.tradeList1 = arr3;
				this.resumeDisplayIntention();
			},
			majorsure(id){
				let tom = this.tradeList1[this.tradeList1.findIndex(item => item.id === id)];
				if(tom.use == "clickmore"){
					this.tradeList2.splice(this.tradeList2.findIndex(item => item === tom),1)
					tom.use = ""
				}else{
					if(this.tradeList2.length<3){
						tom.use = "clickmore";
						this.tradeList2.push(tom);
					}else{
						uni.showToast({
							icon:"none",
							title:"最多 3 项"
						})
					}
				}
			},
			closechk3(id){
				let tom = this.tradeList1[this.tradeList1.findIndex(item => item.id === id)];
				tom.use = "";
				this.tradeList2.splice(this.tradeList2.findIndex(item => item.id === id),1);
			},
			tdresetOrSave(i){
				if(i == 1){
					this.is_drop = false
					this.tradeBool = false
				}else{
					let arr = []
					this.tradeList2.map(item => arr.push(item.name))
					this.seltrade_cn = arr.join(",")
					this.is_drop = false
				}
			},
			async resumeEditIntention(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.resumeEditIntention(this.list_param,this.headers);
				if(res.state == 1){
					uni.$emit("showtoast",{msg:'修改成功'});
					let that = this;
					// #ifndef MP-BAIDU
					setTimeout(()=>{
					// #endif
						that.customConduct();
					// #ifndef MP-BAIDU
					},2000)
					// #endif
				}
			}
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

<style lang="less">
	@import "../../common/edit.less";
</style>
