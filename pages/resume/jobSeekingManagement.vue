<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="nav_cont">
			<top-nav v-for="(item,i) in tnList" :key="i" :param="item" @Selected="changeItem"></top-nav>
		</view>
		<view class="qs_space"></view>
		<job-one 
			v-if="toggle==='面试邀请'" 
			v-for="(item,i) in msList" 
			:key="i" 
			:param="item"
			@toDetail="toDetail"
			@delItem="delJobOne"></job-one>
		<job-two 
			v-if="toggle==='我的投递'" 
			v-for="(item,i) in tdList" 
			:key="i" 
			:param="item"
			@toDetail="toDetail"
			@delItem="delJobTwo"></job-two>
		<job-three 
			v-if="toggle==='对我感兴趣'" 
			v-for="(item,i) in gxqList" 
			:key="i" 
			:param="item"
			@toDetail="toDetail2"
			@delItem="delJobThree" 
			@chat="chat"></job-three>
		<job-four
			v-if="toggle==='我感兴趣的'" 
			v-for="(item,i) in favList" 
			:key="i" 
			:param="item"
			@toDetail="toDetail"
			@delItem="delJobFour"></job-four>
		<uni-load-more :status="tmore"></uni-load-more>
		<uni-popup ref="popup" type="center" :custom="true">
			<view class="popbox">
				<view class="selectPop" @click="popselected(1)">
					对我感兴趣
				</view>
				<view class="selectPop" @click="popselected(2)">
					我感兴趣的
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import topNav from "../../components/top-nav/top-nav"
	import jobOne from "../../components/job-one/job-one"
	import jobTwo from "../../components/job-two/job-two"
	import jobThree from "../../components/job-three/job-three"
	import jobFour from "../../components/job-four/job-four"
	import UniPopup from "../../components/uni-popup/uni-popup.vue"
	import UniLoadMore from "../../components/uni-load-more/uni-load-more"
	import http from "../../server/api-resume.js"
	export default {
		components:{
			topNav,
			jobOne,
			jobTwo,
			jobThree,
			UniLoadMore,
			UniPopup,
			jobFour
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"求职管理", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				toggle:"面试邀请",
				tnList:[
					{name:"面试邀请",is_has:false,use:"three"},
					{name:"我的投递",is_has:false,use:"three"},
					{name:"对我感兴趣",is_has:true,use:"three"},
				],
				isfull:true,
				nowPage:1,
				tmore:"more",
				msList:[],
				tdList:[],
				gxqList:[],
				favList:[],
				list_param:{},
				headers:{}
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
			this.changeItem("面试邀请")
			if(this.isfull){
				this.tmore = "noMore"
			}
		},
		// 下拉刷新
		onPullDownRefresh(){
			this.changeItem(this.toggle)
			setTimeout(()=>{
				uni.stopPullDownRefresh();
			},1000)
		},
		// 上拉加载
		async onReachBottom(){
			if(!this.isfull){
				this.tmore = "loading"
				uni.showNavigationBarLoading();//显示加载动画
				this.list_param = {...this.list_param,page:++this.nowPage}
				this.addPage(this.toggle)
			}else{
				this.tmore = "noMore"
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
			changeItem(e){
				this.tnList = this.tnList.map((user)=>{
					user.use = "three"
					return user
				})
				this.tnList[this.tnList.findIndex(tnList => tnList.name === e)].use = "two"
				this.toggle = e
				switch (e){
					case "面试邀请":
						this.jobsInterview().then(res => {
							this.msList = res
						})
						break;
					case "我的投递":
						this.myDelivery().then(res => {
							this.tdList = res
						})
						break;
					case "对我感兴趣":
					case "我感兴趣的":
						this.$refs.popup.open();
						break;
					default:
						break;
				}
			},
			addPage(e){
				switch (e){
					case "面试邀请":
						this.jobsInterview().then(res => {
							this.msList = [...this.msList,...res]
						})
						break;
					case "我的投递":
						this.myDelivery().then(res => {
							this.tdList = [...this.tdList,...res]
						})
						break;
					case "对我感兴趣":
						this.attentionMe().then(res => {
							this.gxqList = [...this.gxqList,...res]
						})
						break;
					case "我感兴趣的":
						this.jobsFavorites().then(res => {
							this.favList = [...this.favList,...res]
						});
						break;
					default:
						break;
				}
			},
			async delJobOne(e){
				let params = {did:e}
				let res = await http.interviewDel(params,this.headers)
				if(res){
					this.msList.splice(this.msList.findIndex(item => item.did === e),1)
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			async delJobTwo(e){
				let params = {did:e}
				let res = await http.jobsApplyDel(params,this.headers)
				if(res){
					this.tdList.splice(this.tdList.findIndex(item => item.did === e),1)
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			async delJobThree(e){
				let params = {id:e};
				let res = await http.attentionMeDel(params,this.headers)
				if(res){
					this.gxqList.splice(this.gxqList.findIndex(item => item.id === e),1)
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			async delJobFour(e){
				let params = {id:e}
				let res = await http.jobsFavoritesDel(params,this.headers)
				if(res){
					this.favList.splice(this.favList.findIndex(item => item.did === e),1)
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			chat(data){
				uni.navigateTo({
					url:`/pages/tidings/myChat?uid=${data.uid}&id=${data.id}`
				})
			},
			async jobsInterview(){
				let res = await http.jobsInterview(this.list_param,this.headers)
				this.isfull = res.isfull
				this.tmore = this.isfull?"noMore":"more"
				this.nowPage = res.nowPage
				let data = res.returnlist
				let arr = []
				data.map(item => {
					arr.push({
						id:item.id,
						did:item.did,
						cname:item.company_name,
						pay:item.wage_cn,
						jname:item.jobs_name,
						addtime:"面试时间："+item.interview_time,
						is_do:item.personal_look,
						is_close:item.interview_state,
						tips:item.tips
					})
				})
				return arr
			},
			async myDelivery(){
				let res = await http.myDelivery(this.list_param,this.headers)
				this.isfull = res.isfull
				this.tmore = this.isfull?"noMore":"more"
				this.nowPage = res.nowPage
				let data = res.returnlist
				let arr = []
				data.map(item => {
					arr.push({
						id:item.id,
						did:item.did,
						cname:item.company_name,
						pay:item.wage_cn,
						jname:item.jobs_name,
						addtime:item.refreshtime,
						is_do:item.reply_status,
						tips:item.tips
					})
				})
				return arr
			},
			async attentionMe(){
				let res = await http.attentionMe(this.list_param,this.headers)
				this.isfull = res.isfull
				this.tmore = this.isfull?"noMore":"more"
				this.nowPage = res.nowPage
				let data = res.returnlist
				let arr = []
				data.map(item => {
					arr.push({
						id:item.id,
						company_id:item.company_id,
						company_uid:item.company_uid,
						cname:item.companyname,
						pay:[item.nature_cn,item.scale_cn,item.trade_cn],
						addtime:"查看时间："+item.addtime,
						is_do:item.hasdown,
						tips:item.tips,
					})
				})
				return arr
			},
			async jobsFavorites(){
				let res = await http.jobsFavorites(this.list_param,this.headers);
				this.isfull = res.isfull
				this.tmore = this.isfull?"noMore":"more"
				this.nowPage = res.nowPage
				let data = res.returnlist
				let arr = []
				data.map(item => {
					arr.push({
						id:item.jobs_id,
						did:item.did,
						cname:item.companyname,
						pay:item.wage_cn,
						jname:item.jobs_name,
						addtime:item.addtime,
						tips:item.tips,
					})
				})
				return arr
			},
			popselected(i){
				if(i == 1){
					this.attentionMe().then(res => {
						this.gxqList = res
					});
					this.toggle = "对我感兴趣";
					this.tnList[2].name = "对我感兴趣";
					this.$refs.popup.close();
				}else{
					this.jobsFavorites().then(res => {
						this.favList = res
					});
					this.toggle = "我感兴趣的";
					this.tnList[2].name = "我感兴趣的";
					this.$refs.popup.close();
				}
			},
			toDetail(id){
				uni.navigateTo({
					url:"../index/jobDetail?id="+id
				});
			},
			toDetail2(id){
				uni.navigateTo({
					url:"../index/comDetail?id="+id
				});
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/extendRes.less";
</style>
