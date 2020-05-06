<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<person-header :perInfo="personInfo" @pop="openPopup"></person-header>
		<view class="qs-line"></view>
		<view class="person_nav">
			<person-nav v-for="(item,i) in qs_list" :key="i" :personClick="item" @myGroup="wGroup"></person-nav>
		</view>
		<view class="qs_space"></view>
		<person-completion :personPro="personPro" v-if="personPro<70"></person-completion>
		<view class="qs_space" v-if="personPro<70"></view>
		<person-option v-for="(item,i) in perOption" :key="i" :qs_option="item" @myOption="wOption"></person-option>
		<view class="qs_space"></view>
		<view class="logout" @click="logout" v-if="personInfo.is_login">
			退出登录
		</view>
		<view class="qs_space" v-if="personInfo.is_login"></view>
		<uni-popup ref="popup" type="center" :custom="true">
			<view class="loginPop">
				登录/注册{{webname}}
				<!-- #ifdef MP-WEIXIN -->
				<button type='primary' open-type="getPhoneNumber" class="btn ownone" @getphonenumber="getPhonenum">
					<image src="../../static/image/wechat2.png" mode="aspectFit"></image>
					<view class="">
						微信账号快速登录
					</view>
				</button>
				<!-- #endif -->
				<button class="btn owntwo" @click="login(2)">
					<image src="../../static/image/phone2.png" mode="aspectFit"></image>
					<view class="">
						手机号登录/注册
					</view>
				</button>
			</view>
		</uni-popup>
		<uni-popup ref="display" type="center" :custom="true">
			<view class="displayPop">
				<view class="dtcont">
					<view class="distitle">
						简历公开设置
					</view>
					<image src="../../static/image/displayclose.png" @click="closeDis"></image>
				</view>
				<view class="nextcont">
					<view class="dtcont" @click="myResume(1)">
						<view>简历公开</view>
						<image :src="perOption[1].dom=='公开'?'/static/image/displayselect.png':'/static/image/mysure.png'" mode="aspectFit"></image>
					</view>
					<view class="dtcont" @click="myResume(2)">
						<view>简历保密</view>
						<image :src="perOption[1].dom!='公开'?'/static/image/displayselect.png':'/static/image/mysure.png'" mode="aspectFit"></image>
					</view>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import personHeader from "../../components/person-header/person-header.vue"
	import personNav from "../../components/person-nav/person-nav.vue"
	import personCompletion from "../../components/person-completion/person-completion.vue"
	import personOption from "../../components/person-option/person-option.vue"
	import UniPopup from "../../components/uni-popup/uni-popup.vue"
	import http from "../../server/api-person.js"
	import httptwo from "../../server/api-index.js";
	import baseUrl from "../../utils/config.js"
	export default {
		components:{
			personHeader,
			personNav,
			personCompletion,
			personOption,
			UniPopup
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"会员中心", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				personInfo:{
					src:"/static/image/defaulticon.png",
					name:"未登录/注册",
					cont:"您还未登录...",
					is_login:false
				},
				qs_list:[],
				personPro:0,
				perOption:[],
				code_param:{},
				temp_code:"",
				webname:"",
				upRank:10
			}
		},
		onLoad(){
			this.reload();
			this.webCfg();
			let token = uni.getStorageSync("token");
			if(!token){
				this.$refs.popup.open()
			}
			// #ifdef MP-WEIXIN
			uni.login({
				provider:"weixin",
				success(res) {
				}
			})
			// #endif
		},
		onShow(){
			this.memberIndex();
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		// 下拉刷新
		onPullDownRefresh(){
			this.memberIndex()
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.switchTab({
					url:"../index/index"
				})
			},
			wOption:function(e){
				if(this.personInfo.is_login){
					switch (e){
						case "刷新简历":
							this.refreshResume()
							break;
						case "公开状态":
							this.$refs.display.open()
							break;
						case "屏蔽企业":
							uni.navigateTo({
								url:"shield"
							})
							break;
						case "账号管理":
							uni.navigateTo({
								url:"myAccount"
							})
							break;
						default:
							break;
					}
				}else{
					this.$refs.popup.open()
				}
			},
			wGroup:function(e){
				switch (e){
					case "面试邀请":
						uni.navigateTo({
							url:"myInterview"
						})
						break;
					case "对我感兴趣":
						uni.navigateTo({
							url:"myAppreciate"
						})
						break;
					case "我的申请":
						uni.navigateTo({
							url:"myApply"
						})
						break;
					case "我的收藏":
						uni.navigateTo({
							url:"myCollect"
						})
						break;
					default:
						break;
				}
			},
			openPopup(str){
				if(!str){
					this.$refs.popup.open()
				}
			},
			async getPhonenum(e){
				let code = await uni.login({
					provider:"weixin"
				});
				if(e.detail.errMsg == "getPhoneNumber:ok"){
					this.code_param = {code:code[1].code,encryptedData:e.detail.encryptedData,iv:e.detail.iv};
					this.quickLogin();
				}else{
					uni.showToast({
						icon:"none",
						title:"请去微信认证手机"
					})
				}
			},
			async quickLogin(){
				let headers = {"content-type":"application/x-www-form-urlencoded"};
				let res = await http.quickLogin(this.code_param,headers);
				if(res){
					uni.setStorageSync("token",res);
					this.memberIndex();
					this.$refs.popup.close();
				}
			},
			async login(i){
				switch (i){
					case 1:
						break;
					default:
						uni.navigateTo({
							url:"myLogin"
						})
						break;
				}
			},
			async memberIndex(){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await http.memberIndex({},headers,1);
				if(res&&res.complete_percent==0){
					this.$refs.popup.close();
					this.reload(0);
					uni.navigateTo({
						url:"../resume/createResOne"
					});
				}else if(res&&res.complete_percent>0){
					this.$refs.popup.close();
					this.personInfo.src = res.photo_img?this.completion(res.photo_img):"/static/image/defaulticon.png"
					this.personInfo.name = res.fullname
					this.personInfo.cont = res.age+"岁 | "+res.education_cn+" | "+res.experience_cn
					this.personInfo.is_login = true
					this.qs_list[0].src = res.countapply
					this.qs_list[1].src = res.countinterview
					this.qs_list[2].src = res.views
					this.qs_list[3].src = res.favorites
					this.personPro = res.complete_percent
					this.perOption[0].to = `上次更新: ${res.refreshtime}`
					this.upRank = res.rank;
					if (res.refreshtime == "刚刚") {
						this.perOption[0].tw = `您的简历排名上升${res.rank}名`
					}else{
						this.perOption[0].tw = `简历排名靠后，刷新可上升${res.rank}名`
					}
					this.perOption[1].dom = res.display
				}else{
					this.reload();
				}
				uni.stopPullDownRefresh();
			},
			completion(url){
				let path;
				if(url.substr(0,7).toLowerCase() == "http://" || url.substr(0,8).toLowerCase() == "https://"){
					path = url;
				}else{
					path = baseUrl+url;
				}
				return path;
			},
			async refreshResume(){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await http.refreshResume({},headers);
				console.log(res);
				if(res){
					this.perOption[0].to = "上次更新: 刚刚"
					this.perOption[0].tw = `您的简历排名上升${this.upRank}名`
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			async resumePrivacy(){
				let params = {}
				let str = ""
				if(this.perOption[1].dom == "公开"){
					params = {display:0};
					str = "保密"
				}else{
					params = {display:1};
					str = "公开"
				}
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token}
				let res = await http.resumePrivacy(params,headers)
				if(res){
					this.perOption[1].dom = str
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			logout(){
				uni.clearStorageSync();
				this.reload();
			},
			reload(t=1){
				this.personInfo = {
					src:"/static/image/defaulticon.png",
					name:t?"未登录/注册":"暂无信息",
					cont:t?"您还未登录...":"暂无信息",
					is_login:t?false:true
				};
				this.qs_list = [
					{src:"0",name:"我的申请"},
					{src:"0",name:"面试邀请"},
					{src:"0",name:"对我感兴趣"},
					{src:"0",name:"我的收藏"}
				];
				this.personPro = 0;
				this.perOption = [
					{src:"/static/image/shuaixin2.png",name:"刷新简历",one:true,two:false,to:"上次更新:无",tw:"简历排名靠后，刷新可上升0名"},
					{src:"/static/image/gonkaizhuangtai.png",name:"公开状态",one:false,two:true,dom:"公开"},
					{src:"/static/image/pingbiqiye.png",name:"屏蔽企业",one:false,two:false},
					{src:"/static/image/xiugaimima.png",name:"账号管理",one:false,two:false}
				];
			},
			async getUserInfo(){
				let headers = {"content-type":"application/x-www-form-urlencoded"};
				let res = await http.getUserInfo(this.code_param,headers);
				if(res.is_bind == 1){
					uni.setStorageSync("token",res.token);
					this.memberIndex();
					this.$refs.popup.close();
				}else{
					uni.navigateTo({
						url:"myLogin?id="+res.id
					})
				}
			},
			async webCfg(){
				let res = await httptwo.webCfg();
				this.webname = res;
			},
			closeDis(){
				this.$refs.display.close();
			},
			myResume(i){
				if(i == 1){
					this.perOption[1].dom = "保密"
				}else{
					this.perOption[1].dom = "公开"
				}
				this.resumePrivacy();
				this.$refs.display.close();
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/person.less";
</style>
