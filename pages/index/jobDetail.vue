<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<jd-header :param="jhList" :photo="photo" @toReport="toReport"></jd-header>
		<attestation :param="attesList" v-if="is_audit==1"></attestation>
		<view class="qs_space"></view>
		<view class="dcont">
			<view class="jdtitle">
				<view class="one">
					职位描述
				</view>
				<view class="two">
					更新时间:   {{titleOne}}
				</view>
			</view>
			<view class="word">
				<text style="word-wrap:break-word">{{desc}}</text>
			</view>
		</view>
		<view class="qs_space"></view>
		<com-info :param="cinfo" @navtodel="navtodel"></com-info>
		<view class="zhiweituijian">
			/ 相似职位推荐 /
		</view>
		<qs-view-chat v-for="(item,i) in ps_list" :key="i" :psList="item" @childByValue="childByValue" @liveChat="liveChat"></qs-view-chat>
		<view style="width: 100%;height: 110rpx;"></view>
		<view class="footer">
			<job-footer :param="fandp" @wholeOpt="wholeOpt"></job-footer>
		</view>
		<image src="../../static/image/whole.png" class="drift" @click="showDf"></image>
		<view v-if="dfbool" class="dftop">
			<drift-click @upDfBool="opGroup"></drift-click>
		</view>
	</view>
</template>

<script>
	import jdHeader from "../../components/jd-header/jd-header"
	import jobFooter from "../../components/job-footer/job-footer"
	import attestation from "../../components/attestation/attestation.vue"
	import titleNav from "../../components/title-nav/title-nav"
	import comInfo from "../../components/com-info/com-info"
	import qsViewChat from "../../components/qs-view-chat/qs-view-chat.vue"
	import driftClick from "../../components/drift-click/drift-click.vue"
	import http from "../../server/api-jobDeatil.js"
	import httptwo from "../../server/api-index.js"
	import {voluation} from "../../utils/useList.js"
	export default{
		components:{
			jdHeader,
			attestation,
			titleNav,
			comInfo,
			qsViewChat,
			jobFooter,
			driftClick
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"职位详情", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				jhList:{
					cname:"暂无信息",
					pay:"暂无信息",
					info:"暂无信息 | 暂无信息 | 暂无信息",
					tag:["暂无信息"],
					num:0
				},
				attesList:[
					{src:"/static/image/circleyes.png",name:"企业认证",use:"qs_five_color",imgStyle:"qre_three_img"},
					{src:"/static/image/circleyes.png",name:"多重审核",use:"qs_five_color",imgStyle:"qre_three_img"},
					{src:"/static/image/circleyes.png",name:"信誉担保",use:"qs_five_color",imgStyle:"qre_three_img"},
					{src:"/static/image/circleyes.png",name:"违规严惩",use:"qs_five_color",imgStyle:"qre_three_img"},
				],
				titleOne:"",
				desc:"暂无信息",
				ps_list:[],
				cinfo:{
					cname:"",
					cont:"",
					address:""
				},
				dfbool:false,
				photo:[],
				is_audit:0,
				job_id:"",
				job_uid:"",
				job_uid2:"",
				fandp:{},
				fav_param:{},
				apply_param:{},
				id:"",
				is_login:false,
			}
		},
		onLoad(option) {
			this.id = option.id;
			this.jobsDetails();
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
				title: this.jhList.cname,
				path: `/pages/index/jobDetail?id=${this.id}`
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
			childByValue:function(i){
				uni.navigateTo({
					url:"jobDetail?id="+i
				})
			},
			showDf(){
				this.dfbool = true
			},
			opGroup:function(i){
				switch (i){
					case 1:
						uni.switchTab({
							url:"index"
						})
						break;
					case 2:
						uni.switchTab({
							url:"../resume/resume"
						})
						break;
					case 3:
						this.dfbool = false
						break;
					default:
						break;
				}
			},
			toReport(s){
				uni.navigateTo({
					url:"../resume/editReport?id="+this.id
				})
			},
			async jobsDetails(){
				const token = uni.getStorageSync('token');
				let headers = {};
				if(token != ""){
					headers = {"Authorization":"Bearer "+token};
				}
				let res = await http.jobsDetails({id:this.id},headers);
				let list = res.resemblelist
				let jobd = res.jobsdetails
				this.job_id = jobd.id;
				this.job_uid = jobd.uid+"_1";
				this.job_uid2 = jobd.uid;
				this.is_audit = jobd.audit;
				this.ps_list = voluation(list);
				this.fandp = {
					is_apply:jobd.is_apply,
					favor:jobd.favor
				};
				this.jhList = {
					cname:jobd.jobs_name,
					pay:jobd.wage_cn,
					info:jobd.district_cn+" | "+jobd.experience_cn+" | "+jobd.education_cn,
					tag:jobd.tag_cn,
					num:jobd.click
				}
				this.titleOne = jobd.refreshtime
				this.desc = this.format(jobd.contents)
				this.cinfo = {
					id:jobd.company_id,
					cname:jobd.companyname,
					cont:jobd.nature_cn+" | "+jobd.scale_cn+" | "+jobd.trade_cn,
					huiyuan:"1",
					renzheng:jobd.audit,
					logo:jobd.logo,
					img_src:"/static/image/location.png",
					img_use:"qs_four_color",
					address:jobd.address,
					latitude: jobd.map_y,
					longitude: jobd.map_x,
					covers: [{
						latitude: jobd.map_y,
						longitude: jobd.map_x,
						iconPath: '/static/image/location.png'
					}, {
						latitude: jobd.map_y,
						longitude: jobd.map_x,
						iconPath: '/static/image/location.png'
					}]
				}
				this.photo = jobd.photo
			},
			format (text) {
				if (!text) {
			        return
			    }
			    return text.replace(/↵/g,'\n')
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
			wholeOpt(i){
				switch (i){
					case 1:
						break;
					case 2:
						this.fav_param = {id:this.job_id,uid:this.job_uid2};
						this.favoritejobs();
						break;
					case 3:
						if(this.fandp.is_apply == 1){
							this.telephone();
						}else{
							this.apply_param = {jid:this.job_id};
							this.resumeApply();
						}
						break;
					case 4:
						if(this.is_login){
							uni.navigateTo({
								url:`/pages/tidings/myChat?uid=${this.job_uid}&id=${this.job_id}`
							})
						}else{
							uni.navigateTo({
								url:"/pages/personal/myLogin"
							})
						}
						break;
					default:
						break;
				}
			},
			navtodel(id){
				uni.navigateTo({
					url:"comDetail?id="+id
				})
			},
			async favoritejobs(){
				const token = uni.getStorageSync('token');
				let headers = {"Content-Type":"application/x-www-form-urlencoded","Authorization":"Bearer "+token};
				let res = await httptwo.favoritejobs(this.fav_param,headers);
				if(res == "success"){
					this.fandp.favor = 1
				}else{
					this.fandp.favor = 0
				}
			},
			async resumeApply(){
				const token = uni.getStorageSync('token');
				let headers = {"Content-Type":"application/x-www-form-urlencoded","Authorization":"Bearer "+token};
				let res = await httptwo.resumeApply(this.apply_param,headers);
				if(res == "success"){
					this.fandp.is_apply = 1;
					uni.showToast({
						icon:"none",
						title:"投递成功"
					})
				}
			},
			async telephone(){
				const token = uni.getStorageSync('token');
				let headers = {"Content-Type":"application/x-www-form-urlencoded","Authorization":"Bearer "+token};
				let res = await http.telephone({jobs_id:this.job_id},headers);
				uni.makePhoneCall({
					phoneNumber:res.telephone
				})
			}
		},
	}
</script>

<style lang="less" scoped>
	@import "../../common/job.less";
</style>
