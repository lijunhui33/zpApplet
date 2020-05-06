<template>
	<view>
		<view class="qs_start">
			<swiper class="qs_image" :indicator-dots="true" :autoplay="true" :interval="3000" :duration="1000">
				<swiper-item v-for="(item,i) in ads" :key="i">
					<view class="swiper-item">
						<image class="qs_image" mode="aspectFill" :src="item.content"></image>
					</view>
				</swiper-item>
			</swiper>
			<navigator url="test" hover-class="none">
				<image class="qs_search" src="../../static/image/search.png" mode="aspectFit"></image>
			</navigator>
		</view>
		<view class="qs_nav">
			<qs-nav v-for="(item,i) in qs_list" :key="i"
				:qs_url="item.url"
				:qs_image="item.src"
				:qs_text="item.name"></qs-nav>
		</view>
		<view class="qs_space"></view>
		<view class="qs_switch">
			<view class="qs_sitem qs_content" @click="toggle(1)">
				<view :class="qs_bool?'qs_text':''">
					最新
				</view>
				<image :src="qs_bool?'/static/image/selected.png':'/static/image/unselected.png'"></image>
			</view>
			<view class="qs_sitem qs_content" @click="toggle(2)">
				<view :class="!qs_bool?'qs_text':''">
					急聘
				</view>
				<image :src="!qs_bool?'/static/image/selected.png':'/static/image/unselected.png'"></image>
			</view>
			<view class="qs_sitemright" v-if="!is_login" @click="showLoginBox">
				立即登录
			</view>
		</view>
		<qs-view-chat v-for="(item,i) in ps_list" :key="i" 
			:psList="item" 
			@childByValue="childByValue"
			@liveChat="liveChat"></qs-view-chat>
		<navigator url="./job" class="lookmore">查看更多</navigator>
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
				<button class="btn owntwo" @click="login">
					<image src="../../static/image/phone2.png" mode="aspectFit"></image>
					<view class="">
						手机号登录/注册
					</view>
				</button>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import qsNav from "../../components/qs-nav/qs-nav.vue";
	import qsViewChat from "../../components/qs-view-chat/qs-view-chat.vue";
	import http from "../../server/api-index.js";
	import httptwo from "../../server/api-person.js";
	import UniPopup from "../../components/uni-popup/uni-popup.vue"
	import {voluation} from "../../utils/useList.js";
	export default {
		components:{
			qsNav,
			qsViewChat,
			UniPopup
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					fontcolor:"#333333",
					// menuIcon:"", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"骑士人才系统", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				qs_list:[
					{url:"/pages/index/job",src:"/static/image/youxuanzhiwei.png",name:"优选职位"},
					{url:"/pages/index/famousEnterprises",src:"/static/image/tongchengzhaopin.png",name:"同城招聘"},
					{url:"/pages/index/famous",src:"/static/image/mingqituijian.png",name:"名企推荐"},
					{url:"/pages/index/subscribe",src:"/static/image/dingyuezhiwei.png",name:"订阅职位"}
				],
				qs_bool:true,
				ps_list:[],
				ads:[{content:"/static/image/backgroud1.png"}],
				is_login:false,
				webname:"",
				code_param:{},
			}
		},
		onLoad() {
			this.newestJob();
			this.indexAd();
			this.webCfg();
			// #ifdef MP-WEIXIN
			uni.login({
				provider:"weixin",
				success(res) {
				}
			})
			// #endif
		},
		onShow() {
			if(uni.getStorageSync("token")){
				this.is_login = true
			}else{
				this.is_login = false
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
				title: this.webname,
				path: "/pages/index/index"
			}
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
			},
			toggle(index){
				if(index==2){
					this.qs_bool = false;
					this.hotJob()
				}else{
					this.qs_bool = true;
					this.newestJob()
				}
			},
			childByValue:function(i){
				uni.navigateTo({
					url:"jobDetail?id="+i,
					animationType: 'fade-in',
					animationDuration: 200
				})
			},
			async hotJob(){
				let res = await http.hotJobList()
				this.ps_list = voluation(res)
			},
			async newestJob(){
				let res = await http.newestJobList()
				this.ps_list = voluation(res)
			},
			async indexAd(){
				let res = await http.indexAd()
				this.ads = res;
			},
			liveChat(data){
				if(this.is_login){
					uni.navigateTo({
						url:`/pages/tidings/myChat?uid=${data.uid}&id=${data.id}`
					})
				}else{
					uni.navigateTo({
						url:"/pages/personal/myLogin"
					})
				}
			},
			showLoginBox(){
				this.$refs.popup.open();
			},
			async webCfg(){
				let res = await http.webCfg();
				this.webname = res;
				uni.setNavigationBarTitle({
					title:res
				})
			},
			login(){
				uni.navigateTo({
					url:"../personal/myRegister"
				})
			},
			async quickLogin(){
				let headers = {"content-type":"application/x-www-form-urlencoded"};
				let res = await httptwo.quickLogin(this.code_param,headers);
				if(res){
					uni.setStorageSync("token",res);
					this.is_login = true;
					this.$refs.popup.close();
				}
			},
			async getPhonenum(e){
				let code = await uni.login({
					provider:"weixin"
				});
				console.log(code);
				if(e.detail.errMsg == "getPhoneNumber:ok"){
					this.code_param = {code:code[1].code,encryptedData:e.detail.encryptedData,iv:e.detail.iv};
					this.quickLogin();
				}else{
					uni.showToast({
						icon:"none",
						title:"请去微信认证手机"
					})
				}
			}
		}
	}
</script>