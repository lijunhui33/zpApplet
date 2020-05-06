<template>
	<view>
		<view>
			<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
			<resume-header :perInfo="personInfo" @editBasic="editOne"></resume-header>
			<view class="qs_container">
				<newText v-for="(item,i) in one" :key="i" :dog="item"></newText>
			</view>
			<view class="qs_space" style="margin-top: 30rpx;"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleOne" @editInfo="wEdit"></title-nav>
				<intention-li v-for="(item,i) in two" :key="i" :intent="item"></intention-li>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleTwo" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in eduArr" :key="i">
					<undertwo :param="item" @update="editPage"></undertwo>
					<view v-if="i+1 != eduArr.length" class="qs-line" style="margin-top: 20rpx;"></view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleThree" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in expArr" :key="i">
					<undertwo :param="item" @update="editPage"></undertwo>
					<view v-if="i+1 != expArr.length" class="qs-line" style="margin-top: 20rpx;"></view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleFour" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in traArr" :key="i">
					<undertwo :param="item" @update="editPage"></undertwo>
					<view v-if="i+1 != traArr.length" class="qs-line" style="margin-top: 20rpx;"></view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleFive" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in proArr" :key="i">
					<undertwo :param="item" @update="editPage"></undertwo>
					<view v-if="i+1 != proArr.length" class="qs-line" style="margin-top: 20rpx;"></view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change" v-if="!desc">
				<title-nav :titleCont="titleSix" @editInfo="wEdit"></title-nav>
			</view>
			<view class="qs_container_change" v-if="desc">
				<title-nav :titleCont="titleSeven" @editInfo="wEdit"></title-nav>
				<view class="qs_common_lr" style="color: #666;">
					{{desc}}
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleEight" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in creArr" :key="i">
					<undergo :param="item" @update="editPage"></undergo>
					<view v-if="i+1 != creArr.length" class="qs-line" style="margin: 20rpx 0;"></view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleTen" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in lanArr" :key="i">
					<undergo :param="item" @update="editPage"></undergo>
					<view v-if="i+1 != lanArr.length" class="qs-line" style="margin: 20rpx 0;"></view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleTwelve" @editInfo="wEdit"></title-nav>
				<view class="resume_point">
					<strong-point v-for="(item,i) in point" :key="i" :param="item" @delPro="delPro"></strong-point>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="qs_container_change">
				<title-nav :titleCont="titleThirteen" @editInfo="wEdit"></title-nav>
				<view v-for="(item,i) in imgArr" :key="i">
					<resume-img :param="item" @update="editPage"></resume-img>
					<view v-if="i+1 != imgArr.length" class="qs-line" style="margin: 20rpx 0;"></view>
				</view>
			</view>
			<view class="qs_space" style="height: 180rpx;"></view>
		</view>
		<image v-if="!is_more" class="qs_more" src="../../static/image/more.png" mode="" @click="upMore()"></image>
		<view v-if="is_more" class="shelter">
		</view>
		<popup v-if="is_more" class="qs_option" v-on:childByValue="childByValue"></popup>
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
						<image :src="is_private==1?'/static/image/displayselect.png':'/static/image/mysure.png'" mode="aspectFit"></image>
					</view>
					<view class="dtcont" @click="myResume(2)">
						<view>简历保密</view>
						<image :src="is_private!=1?'/static/image/displayselect.png':'/static/image/mysure.png'" mode="aspectFit"></image>
					</view>
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import resumeHeader from "../../components/resume-header/resume-header.vue"
	import newText from "../../components/new-text/new-text.vue"
	import titleNav from "../../components/title-nav/title-nav"
	import intentionLi from "../../components/intention-li/intention-li"
	import undergo from "../../components/undergo/undergo"
	import undertwo from "../../components/undertwo/undertwo"
	import strongPoint from "../../components/strong-point/strong-point"
	import resumeImg from "../../components/resume-img/resume-img"
	import popup from "../../components/popup/popup"
	import http from "../../server/api-resume.js"
	import httptwo from "../../server/api-person.js"
	import baseUrl from "../../utils/config.js"
	import UniPopup from "../../components/uni-popup/uni-popup.vue"
	export default {
		components:{
			resumeHeader,
			newText,
			titleNav,
			intentionLi,
			undergo,
			strongPoint,
			resumeImg,
			popup,
			undertwo,
			UniPopup
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"简历", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				personInfo:{
					src:"/static/image/defaulticon.png",
					name:"",
					info:"",
					cont:"",
				},
				one:[
					{src:"/static/image/linkphone.png",name:"暂无",use:"qs_one_color"},
					{src:"/static/image/linkweixin.png",name:"暂无",use:"qs_one_color"},
				],
				titleOne:{
					name:"求职意向",src:"/static/image/edit.png",btn:""
				},
				titleTwo:{
					name:"教育经历",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleThree:{
					name:"工作经历",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleFour:{
					name:"培训经历",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleFive:{
					name:"项目经历",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleSix:{
					name:"自我描述",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleSeven:{
					name:"自我描述",src:"/static/image/edit.png",btn:""
				},
				titleEight:{
					name:"获得证书",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleTen:{
					name:"语言能力",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleTwelve:{
					name:"特长标签",src:"/static/image/copyadd.png",btn:"添加"
				},
				titleThirteen:{
					name:"照片/作品",src:"/static/image/copyadd.png",btn:"添加"
				},
				two:[],
				eduArr:[],
				expArr:[],
				traArr:[],
				proArr:[],
				creArr:[],
				lanArr:[],
				imgArr:[],
				desc:"",
				point:[],
				is_more:false,
				is_private:0,
				is_login:false,
			}
		},
		// 下拉刷新
		onPullDownRefresh(){
			this.resumeDetails()
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		onShow(){
			console.log("onShow");
			let token = uni.getStorageSync("token");
			if(!token){
				uni.navigateTo({
					url:"/pages/personal/myLogin?login=true"
				})
				this.is_login = false
			}else{
				this.is_login = true
				this.resumeDetails();
				uni.$on("showtoast",this.newShow);
			}
		},
		onUnload() {
			uni.$off("showtoast",this.newShow);
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.switchTab({
					url:"../index/index"
				})
			},
			upMore(){
				this.is_more = true;
			},
			editOne(){
				uni.navigateTo({
					url:"editBasic"
				})
			},
			async resumeDetails(){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await http.resumeDetails({},headers);
				// 基本信息
				let base = res.base
				// 求职意向
				let intention = res.intention
				// 教育经历
				let edu = res.edu
				// 工作经历
				let exp = res.exp
				// 培训经历
				let tra = res.tra
				// 项目经历
				let pro = res.pro
				// 自我描述
				let spe = res.spe
				// 证书
				let cre = res.cre
				// 语言
				let lan = res.lan
				// 特长
				let tag = res.tag
				// 照片
				let img = res.imglist
				this.desc = spe
				this.point = tag
				this.is_private = base.display
				this.personInfo = {
					src:base.photo_img?base.photo_img:"/static/image/defaulticon.png",
					name:base.fullname || "暂无信息",
					info:`${base.sex_cn} / ${base.marriage_cn}`,
					cont:`${base.age}岁 | ${base.education_cn} |${base.experience_cn}`,
				}
				this.one[0].name = base.telephone
				this.one[1].name = base.weixin?base.weixin:"暂无"
				this.two = [
					{title:"求职状态：",name:intention.current_cn},
					{title:"工作性质：",name:intention.nature_cn},
					{title:"工作地区：",name:intention.district_cn},
					{title:"期望薪资：",name:intention.wage_cn},
					{title:"期望职位：",name:intention.intention_jobs},
					{title:"期望行业：",name:intention.trade_cn},
				];
				let arr = []
				edu.map(edu => {
					arr.push({
						id:edu.id,
						schoolname:[edu.school],
						time:edu.todate==1?`${edu.startyear}-${edu.startmonth} 至今`:`${edu.startyear}-${edu.startmonth} 至 ${edu.endyear}-${edu.endmonth}`,
						during:`[${edu.duration}]`,
						major:`${edu.education_cn} | ${edu.speciality}`,
						type:"edu"
					})
				})
				this.eduArr = arr
				let arr2 = []
				exp.map(edu => {
					arr2.push({
						id:edu.id,
						schoolname:[edu.jobs,edu.companyname],
						time:edu.todate==1?`${edu.startyear}-${edu.startmonth} 至今`:`${edu.startyear}-${edu.startmonth} 至 ${edu.endyear}-${edu.endmonth}`,
						during:`[${edu.duration}]`,
						major:edu.achievements,
						type:"exp"
					})
				})
				this.expArr = arr2
				let arr3 = []
				tra.map(edu => {
					arr3.push({
						id:edu.id,
						schoolname:[edu.course,edu.agency],
						time:edu.todate==1?`${edu.startyear}-${edu.startmonth} 至今`:`${edu.startyear}-${edu.startmonth} 至 ${edu.endyear}-${edu.endmonth}`,
						during:`[${edu.duration}]`,
						major:edu.description,
						type:"tra"
					})
				})
				this.traArr = arr3
				let arr4 = []
				pro.map(edu => {
					arr4.push({
						id:edu.id,
						schoolname:[edu.role,edu.projectname],
						time:edu.todate==1?`${edu.startyear}-${edu.startmonth} 至今`:`${edu.startyear}-${edu.startmonth} 至 ${edu.endyear}-${edu.endmonth}`,
						during:`[${edu.duration}]`,
						major:edu.description,
						type:"pro"
					})
				})
				this.proArr = arr4
				let arr5 = []
				cre.map(edu => {
					arr5.push({
						id:edu.id,
						name:[`${edu.year}-${edu.month}`,edu.name],
						type:"cre"
					})
				})
				this.creArr = arr5
				let arr6 = []
				lan.map(edu => {
					arr6.push({
						id:edu.id,
						name:[edu.language_cn,edu.level_cn],
						type:"lan"
					})
				})
				this.lanArr = arr6
				let arr7 = []
				img.map(edu => {
					arr7.push({
						id:edu.id,
						title:edu.title,
						src:edu.img,
						audit:edu.audit==1?"审核通过":edu.audit==2?"等待审核":"审核未通过",
						type:"img"
					})
				})
				this.imgArr = arr7;
				uni.stopPullDownRefresh();
			},
			childByValue:function(i){
				switch(i){
					case 1:
						this.refreshResume()
						this.resumeDetails()
						this.is_more = false;
						break;
					case 2:
						this.$refs.display.open()
						this.is_more = false;
						break;
					case 3:
						uni.navigateTo({
							url:"jobSeekingManagement"
						})
						break;
					case 4:
						this.is_more = false;
						break;
				}
			},
			wEdit:function(e){
				switch (e){
					case "求职意向":
						uni.navigateTo({
							url:"editJobIntension"
						})
						break;
					case "教育经历":
						uni.navigateTo({
							url:"editEducation"
						})
						break;
					case "工作经历":
						uni.navigateTo({
							url:"editExp"
						})
						break;
					case "培训经历":
						uni.navigateTo({
							url:"editTrain"
						})
						break;
					case "项目经历":
						uni.navigateTo({
							url:"editProject"
						})
						break;
					case "自我描述":
						uni.navigateTo({
							url:"editDesc"
						})
						break;
					case "获得证书":
						uni.navigateTo({
							url:"editCertificate"
						})
						break;
					case "语言能力":
						uni.navigateTo({
							url:"editLang"
						})
						break;
					case "特长标签":
						uni.navigateTo({
							url:"editSpeciality"
						})
						break;
					case "照片/作品":
						uni.navigateTo({
							url:"editPhoto"
						})
						break;
					default:
						break;
				}
			},
			editPage(data){
				let type = data.type
				let id = data.id
				switch (type){
					case "edu":
						uni.navigateTo({
							url:"editEducation?id="+id
						})
						break;
					case "exp":
						uni.navigateTo({
							url:"editExp?id="+id
						})
						break;
					case "tra":
						uni.navigateTo({
							url:"editTrain?id="+id
						})
						break;
					case "pro":
						uni.navigateTo({
							url:"editProject?id="+id
						})
						break;
					case "cre":
						uni.navigateTo({
							url:"editCertificate?id="+id
						})
						break;
					case "lan":
						uni.navigateTo({
							url:"editLang?id="+id
						})
						break;
					case "img":
						uni.navigateTo({
							url:"editPhoto?id="+id
						})
						break;
					default:
						break;
				}
			},
			async delPro(str){
				const token = uni.getStorageSync('token');
				let headers = {"Content-Type":"application/x-www-form-urlencoded","Authorization":"Bearer "+token};
				let res = await http.delSpeciality({tag_cn:str},headers);
				if(res.status == 200){
					this.point.splice(this.point.findIndex(item => item === str),1)
				}
			},
			async refreshResume(){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token}
				let res = await httptwo.refreshResume({},headers)
				if(res){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
				}
			},
			async resumePrivacy(){
				let params = {}
				if(this.is_private == 1){
					params = {display:0}
				}else{
					params = {display:1}
				}
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token}
				let res = await httptwo.resumePrivacy(params,headers)
				if(res){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
					this.resumeDetails();
				}
			},
			newShow(e){
				uni.showToast({
					icon:"none",
					title:e.msg
				});
			},
			closeDis(){
				this.$refs.display.close();
			},
			myResume(i){
				if(i == 1){
					this.is_private = 0
				}else{
					this.is_private = 1
				}
				this.resumePrivacy();
				this.$refs.display.close();
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/resume.less";
</style>
