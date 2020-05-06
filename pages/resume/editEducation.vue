<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ec_cont">
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">学校名称</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="info.s1" name="school" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
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
						<input type="text" v-model="info.s2" name="speciality" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
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
				<button form-type="submit" class="mysubmit">保存修改</button>
				<view class="deleteEdu" @click="delEducation" v-if="!subType">删除该经历</view>
			</form>
		</view>
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
					menuText:"编辑教育经历", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				pick:{i1:0},
				education:[
					{id:"",name:"请选择"},
				],
				date:"请选择",
				date2:"请选择",
				headers:{},
				list_param:{},
				subType:true,
				id:"",
				info:{s1:"",s2:""},
				notice:""
			}
		},
		onLoad(option){
			this.id = option.id;
			if(option.id != undefined){
				this.subType = false
			}
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
				if(this.subType){
					let edutodate = 0;
					let eduendyear;
					let eduendmonth;
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
					this.notice = "添加成功";
					let d1 = this.date.split("-");
					this.list_param = {
						school:fd.school,
						speciality:fd.speciality,
						education:this.education[fd.education].id,
						startyear:d1[0],
						startmonth:d1[1],
						endyear:eduendyear,
						endmonth:eduendmonth,
						todate:edutodate
					}
				}else{
					let edutodate = 0;
					let eduendyear;
					let eduendmonth;
					if(this.date2 == currentDate || this.date2 == "至今"){
						edutodate = 1;
						eduendyear = 0;
						eduendmonth = 0;
					}else{
						let d2 = this.date2.split("-");
						edutodate = 0;
						eduendyear = d2[0];
						eduendmonth = d2[1];
					}
					this.notice = "修改成功";
					let d1 = this.date.split("-");
					this.list_param = {
						id:this.id,
						school:fd.school,
						speciality:fd.speciality,
						education:this.education[fd.education].id,
						startyear:d1[0],
						startmonth:d1[1],
						endyear:eduendyear,
						endmonth:eduendmonth,
						todate:edutodate
					}
				}
				this.resumeEducation();
			},
			bindPickerChange(e){
				this.pick.i1 = e.target.value
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
			async searchJobsMore(){
				let res = await http.ResumeBasicChoice({},this.headers)
				let edu = res.QS_education;
				let arr = [];
				for(let key in edu){
					arr.push({id:key,name:edu[key]})
				}
				this.education = [...this.education,...arr];
				if(!this.subType)
					this.resumeDisplayEducation();
			},
			async resumeEducation(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.resumeEducation(this.list_param,this.headers);
				if(res.state == 1){
					uni.$emit("showtoast",{msg:this.notice});
					let that = this;
					// #ifndef MP-BAIDU
					setTimeout(()=>{
					// #endif
						that.customConduct();
					// #ifndef MP-BAIDU
					},2000)
					// #endif
				}
			},
			async resumeDisplayEducation(){
				let res = await httptwo.resumeDisplayEducation({id:this.id},this.headers);
				console.log(res);
				let eid = res[0].education;
				let e = this.education.findIndex(item => item.id === eid);
				this.pick.i1 = e
				this.info = {s1:res[0].school,s2:res[0].speciality};
				if(res[0].todate === "1"){
					this.date2 = "至今"
				}else{
					this.date2 = res[0].endyear+"-"+res[0].endmonth
				}
				this.date = res[0].startyear+"-"+res[0].startmonth
			},
			async delEducation(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.delEducation({id:this.id},this.headers);
				if(res.status == 200){
					uni.$emit("showtoast",{msg:'删除成功'});
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
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
