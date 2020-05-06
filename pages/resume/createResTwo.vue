<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<form @submit="formSubmit">
			<view class="ec_cont">
				<view class="groupTitle">
					教育经历
				</view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">学校名称</view>
					</view>
					<view class="box_input">
						<input type="text" name="school" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">所学专业</view>
					</view>
					<view class="box_input">
						<input type="text" name="speciality" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">取得学历</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="education" :value="pick.i1" :range-key="'name'" name="education" @change="bindPickerChange">
							<view>{{education[pick.i1].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">入学时间</view>
					</view>
					<view class="box_input">
						<picker mode="date" :value="date" :start="startDate" :end="endDate" @change="bindDateChange">
							<view class="uni-input">{{date}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">毕业时间</view>
					</view>
					<view class="box_input">
						<picker mode="date" :value="date2" :start="startDate" :end="endDate" @change="bindDateChange2">
							<view class="uni-input">{{date2}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="addtitle" v-if="showExp" @click="showOrhide(1)">
					+ 添加工作经历
				</view>
				<view v-if="!showExp">
					<view class="job_experience">
						<view class="groupTitle">
							最新工作经历
						</view>
						<image src="../../static/image/wrong1.png" mode="" @click="showOrhide(2)"></image>
					</view>
					<view class="edit_input">
						<view class="label">
							<view class="red">*</view>
							<view class="">公司名称</view>
						</view>
						<view class="box_input">
							<input type="text" value="" name="companyname" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
							<view class="has"></view>
						</view>
					</view>
					<view class="qs-line"></view>
					<view class="edit_input">
						<view class="label">
							<view class="red">*</view>
							<view class="">职位名称</view>
						</view>
						<view class="box_input">
							<input type="text" value="" name="jobs" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
							<view class="has"></view>
						</view>
					</view>
					<view class="qs-line"></view>
					<view class="edit_input">
						<view class="label">
							<view class="red">*</view>
							<view class="">入职时间</view>
						</view>
						<view class="box_input">
							<picker mode="date" :value="date3" :start="startDate" :end="endDate" @change="bindDateChange3">
								<view class="uni-input">{{date3}}</view>
							</picker>
							<image src="../../static/image/back3.png" mode=""></image>
						</view>
					</view>
					<view class="qs-line"></view>
					<view class="edit_input">
						<view class="label">
							<view class="red">*</view>
							<view class="">离职时间</view>
						</view>
						<view class="box_input">
							<picker mode="date" :value="date4" :start="startDate" :end="endDate" @change="bindDateChange4">
								<view class="uni-input">{{date4}}</view>
							</picker>
							<image src="../../static/image/back3.png" mode=""></image>
						</view>
					</view>
					<view class="qs-line"></view>
					<view class="edit_input" style="height: auto;align-items: flex-start;margin-top: 40rpx;margin-bottom: 40rpx;">
						<view class="label">
							<view class="red">*</view>
							<view class="">工作职责</view>
						</view>
						<view class="box_input">
							<textarea value="" placeholder="请输入工作职责" name="achievements" style="width: 400rpx;" placeholder-style="text-align:right"/>
							<view class="has"></view>
						</view>
					</view>
					<view class="qs-line"></view>
				</view>
				<button form-type="submit" class="mysubmit">完成</button>
			</view>
		</form>
	</view>
</template>

<script>
	import http from "../../server/api-job.js"
	import httptwo from "../../server/api-resume.js"
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"创建简历 2/2", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				showExp:true,
				pick:{
					i1:0,
					i2:0,
					i3:0,
					i4:0,
					i5:0,
				},
				education:[
					{id:"",name:"请选择"},
				],
				date:"请选择",
				date2:"请选择",
				date3:"请选择",
				date4:"请选择",
				headers:{},
				list_param:{},
				pid:""
			}
		},
		onLoad(option){
			this.pid = option.pid;
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			this.searchJobsMore();
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		computed: {
			startDate() {
				return this.getDate('start');
			},
			endDate() {
				return this.getDate('end');
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
			formSubmit: function(e) {
				let fd = e.detail.value;
				const currentDate = this.getDate({
					format: true
				})
				let edutodate = 0;
				let eduendyear;
				let eduendmonth;
				let exptodate = 0;
				let expendyear;
				let expendmonth;
				if(this.date2 == currentDate){
					edutodate = 1;
					eduendyear = 0;
					eduendmonth = 0;
				}else{
					let d2 = this.date2.split("-");
					edutodate = 0;
					eduendyear = d2[0];
					eduendmonth = d2[1];
				}
				let d1 = this.date.split("-");
				this.list_param = {
					pid:this.pid,
					school:fd.school,
					speciality:fd.speciality,
					education:this.education[fd.education].id,
					education_cn:this.education[fd.education].name,
					edustartyear:d1[0],
					edustartmonth:d1[1],
					eduendyear:eduendyear,
					eduendmonth:eduendmonth,
					edutodate:edutodate
				}
				if(!this.showExp){
					let d3 = this.date3.split("-");
					if(this.date4 == currentDate){
						exptodate = 1;
						expendyear = 0;
						expendmonth = 0;
					}else{
						let d4 = this.date4.split("-");
						exptodate = 0;
						expendyear = d4[0];
						expendmonth = d4[1];
					}
					this.list_param = {
						...this.list_param,
						companyname:fd.companyname,
						achievements:fd.achievements,
						jobs:fd.jobs,
						expstartyear:d3[0],
						expstartmonth:d3[1],
						expendyear:expendyear,
						expendmonth:expendmonth,
						exptodate:exptodate
					}
				}
				this.ResumeExpAdd();
			},
			bindPickerChange(e){
				this.pick.i1 = e.target.value
			},
			bindPickerChange2(e){
				this.pick.i2 = e.target.value
			},
			bindPickerChange3(e){
				this.pick.i3 = e.target.value
			},
			showOrhide(e){
				if(e == 1)
					this.showExp = false
				else
					this.showExp = true
			},
			getDate(type) {
				const date = new Date();
				let year = date.getFullYear();
				let month = date.getMonth() + 1;
				let day = date.getDate();
	
				if (type === 'start') {
					year = year - 59;
				} else if (type === 'end') {
					year = year;
				}
				month = month > 9 ? month : '0' + month;;
				day = day > 9 ? day : '0' + day;
				return `${year}-${month}-${day}`;
			},
			bindDateChange: function(e) {
				this.date = e.target.value
			},
			bindDateChange2: function(e) {
				this.date2 = e.target.value
			},
			bindDateChange3: function(e) {
				this.date3 = e.target.value
			},
			bindDateChange4: function(e) {
				this.date4 = e.target.value
			},
			async searchJobsMore(){
				let res = await http.ResumeBasicChoice({},this.headers)
				let edu = res.QS_education;
				let arr = [];
				for(let key in edu){
					arr.push({id:key,name:edu[key]})
				}
				this.education = [...this.education,...arr];
			},
			async ResumeExpAdd(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.ResumeExpAdd(this.list_param,this.headers);
				if(res!=undefined && res.status == 200){
					uni.switchTab({
						url:"../personal/personal"
					})
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
