<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<com-header :param="chList" @toAttestation="toAttestation"></com-header>
		<attestation :param="attesList" v-if="is_audit==1"></attestation>
		<view class="qs_space"></view>
		<view class="quick">
			<view :class="is_info?'one checked':'one'" @click="quick(1)">
				单位介绍
				<view :class="is_info?'blue':'white'"></view>
			</view>
			<view :class="!is_info?'one checked':'one'" @click="quick(2)">
				在招职位
				<view :class="!is_info?'blue':'white'"></view>
			</view>
		</view>
		<view class="dcont" v-if="is_info">
			<title-nav :titleCont="titleOne"></title-nav>
			<view class="word">
				<text style="word-wrap:break-word">{{desc}}</text>
			</view>
		</view>
		<view class="qs_space" v-if="is_info"></view>
		<view class="dcont" v-if="is_info&&scenery.length">
			<title-nav :titleCont="titleTwo"></title-nav>
			<scroll-view scroll-x="true" @scroll="scroll" scroll-left="0" class="scrollcont">
				<image v-for="(item,i) in scenery" :key="i" :src="item.img"></image>
			</scroll-view>
		</view>
		<view class="qs_space" v-if="is_info&&scenery.length"></view>
		<view class="dcont" v-if="is_info">
			<title-nav :titleCont="titleThree"></title-nav>
			<new-text :dog="dog"></new-text>
			<map 
				:latitude="latitude" 
				:longitude="longitude" style="width: 670rpx;height: 180rpx;" 
				:markers="covers"
				@tap="tobigMap()"></map>
		</view>
		<view class="notice" v-if="!is_info">
			—— 共有{{jobs_count}}个职位 ——
		</view>
		<qs-view-chat v-for="(item,i) in ps_list" :key="i" :psList="item" @childByValue="childByValue" v-if="!is_info" @liveChat="liveChat"></qs-view-chat>
		<view class="notice" v-if="!is_info">
			这是我的底线…
		</view>
		<button open-type="share" class="nodebutton" id="share">
			<view class="sharebutton">
				<image src="../../static/image/fenxiang3.png" mode="aspectFit" class="share"></image>
				<view>分享</view>
			</view>
			
		</button>
		<image src="../../static/image/shouye2.png" class="drift" @click="showDf"></image>
	</view>
</template>

<script>
	import comHeader from "../../components/com-header/com-header"
	import attestation from "../../components/attestation/attestation.vue"
	import titleNav from "../../components/title-nav/title-nav"
	import newText from "../../components/new-text/new-text.vue"
	import qsViewChat from "../../components/qs-view-chat/qs-view-chat.vue"
	import http from "../../server/api-job.js"
	import {voluation} from "../../utils/useList.js";
	export default{
		components:{
			comHeader,
			attestation,
			titleNav,
			newText,
			qsViewChat
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"企业详情", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				chList:{
					cname:"暂无信息",
					logo:"/static/image/nocomlogo.png",
					btns:[{use:"btn2",name:"执照认证"}],
					info:"暂无信息 | 暂无信息 | 暂无信息",
					tags:["暂无信息"],
					report:true
				},
				attesList:[
					{src:"/static/image/circleyes.png",name:"企业认证",use:"qs_five_color",imgStyle:"qre_three_img"},
					{src:"/static/image/circleyes.png",name:"多重审核",use:"qs_five_color",imgStyle:"qre_three_img"},
					{src:"/static/image/circleyes.png",name:"信誉担保",use:"qs_five_color",imgStyle:"qre_three_img"},
					{src:"/static/image/circleyes.png",name:"违规严惩",use:"qs_five_color",imgStyle:"qre_three_img"},
				],
				titleOne:{
					name:"企业简介",src:"",btn:""
				},
				titleTwo:{
					name:"企业风采",src:"",btn:""
				},
				titleThree:{
					name:"工作地址",src:"",btn:""
				},
				desc:"暂无信息",
				old: {
					scrollTop: 0
				},
				dog:{src:"/static/image/location.png",name:"",use:"qs_four_color",imgStyle:"qre_four_img"},
				latitude: 39.909,
				longitude: 116.39742,
				covers: [{
					latitude: 39.909,
					longitude: 116.39742,
					iconPath: '/static/image/location.png'
				}, {
					latitude: 39.90,
					longitude: 116.39,
					iconPath: '/static/image/location.png'
				}],
				ps_list:[],
				list_param:{},
				is_info:true,
				jobs_count:0,
				scenery:[],
				is_login:false,
				is_audit:0
			}
		},
		onLoad:function(option) {
			// 暂无信息
			this.list_param = {id:option.id};
			this.companysDetails();
			if(uni.getStorageSync("token")){
				this.is_login = true
			}else{
				this.is_login = false
			}
			console.log("/pages/index/comDetail?id="+this.list_param.id);
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onShareAppMessage(res){
			if (res.from === 'button') {// 来自页面内分享按钮
				console.log(res.target)
			}
			return {
				title: this.chList.cname,
				path: "/pages/index/comDetail?id="+this.list_param.id
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
			scroll: function(e) {
				this.old.scrollTop = e.detail.scrollTop
			},
			showDf(){
				uni.switchTab({
					url:"index"
				})
			},
			toAttestation(){
				uni.navigateTo({
					url:"../resume/myAttestation?id="+this.list_param.id
				})
			},
			async companysDetails(){
				let res = await http.companysDetails(this.list_param);
				this.jobs_count = res.jobs_count;
				let data = res.otherjobslist;
				this.ps_list = voluation(data);
				let cinfo = res.companysdetails;
				this.is_audit = cinfo.audit;
				let btns = [];
				btns.push(cinfo.setmeal_id==1?{use:"btn4",name:"普通会员"}:{use:"btn1",name:"名企会员"});
				if(cinfo.audit==1){
					btns.push({use:"btn2",name:"执照认证"});
				}
				if(cinfo.report==1){
					btns.push({use:"btn3",name:"实地认证"});
				}
				this.chList = {
					cname:cinfo.companyname,
					logo:cinfo.logo,
					btns:btns,
					info:`${cinfo.nature_cn} | ${cinfo.scale_cn} | ${cinfo.trade_cn}`,
					tags:cinfo.tag_arrs?cinfo.tag_arrs.split(","):[],
					report:cinfo.report
				};
				this.desc = this.format(cinfo.contents);
				this.scenery = cinfo.img;
				this.dog.name = cinfo.address;
				this.latitude = cinfo.map_y;
				this.longitude = cinfo.map_x;
				this.covers = [{
					latitude: cinfo.map_y,
					longitude: cinfo.map_x,
					iconPath: '/static/image/location.png'
				}, {
					latitude: cinfo.map_y,
					longitude: cinfo.map_x,
					iconPath: '/static/image/location.png'
				}]
			},
			childByValue:function(i){
				uni.navigateTo({
					url:"jobDetail?id="+i
				})
			},
			format (text) {
				if (!text) {
			        return
			    }
			    return text.replace(/↵/g,'\n')
			},
			quick(t){
				if(t == 1){
					this.is_info = true
				}else{
					this.is_info = false
				}
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
			tobigMap(){
				uni.navigateTo({
					url:`/pages/index/bigMap/bigMap?lng=${this.longitude}&lat=${this.latitude}`
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/company.less";
	@import "../../common/job.less";
</style>
