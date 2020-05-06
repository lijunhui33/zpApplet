<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<form @submit="formSubmit">
			<view class="ec_cont">
				<view class="edit_input" style="height: 120rpx;">
					<view class="label">
						<view class="white">*</view>
						<view class="">简历照片</view>
					</view>
					<view class="box_input">
						<image :src="binfo.photo_img" mode="" class="head_img" @click="upload"></image>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">姓名</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.fullname" name="fullname" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">性别</view>
					</view>
					<view class="box_input">
						<view class="gender">
							<view :class="pick.i8==1?'genderbtn selected':'genderbtn'" @click="selsex(1)">
								<image :src="pick.i8==1?'/static/image/menun.png':'/static/image/man.png'" mode=""></image>
								<view>男</view>
							</view>
							<view :class="pick.i8!=1?'genderbtn selected':'genderbtn'" @click="selsex(0)">
								<image :src="pick.i8==1?'/static/image/womenun.png':'/static/image/woman.png'" mode=""></image>
								<view>女</view>
							</view>
						</view>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">出生年份</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="enter" :value="pick.i1" name="birthdate" @change="bindPickerChange">
							<view>{{enter[pick.i1]}}{{!isNaN(enter[pick.i1])?"年":""}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">最高学历</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="education" :value="pick.i2" :range-key="'name'" name="education" @change="bindPickerChange2">
							<view>{{education[pick.i2].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">工作经验</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="experience" :value="pick.i3" :range-key="'name'" name="experience" @change="bindPickerChange3">
							<view>{{experience[pick.i3].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">联系微信</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.weixin" name="weixin" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
			</view>
			<view class="qs_space"></view>
			<view class="wanshan">
				<image src="../../static/image/wanshan.png" mode=""></image>
				<view class="">
					您还有待完善的信息哦
				</view>
			</view>
			<view class="ec_cont" style="padding-top: 0;">
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">联系邮箱</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.email" name="email" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">联系QQ</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.qq" name="qq" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">现居住地</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.residence" name="residence" placeholder="手动输入" placeholder-class="iright" style="text-align: right;width: 280rpx;"/>
						<!-- #ifdef MP-WEIXIN -->
						<text style="padding: 0 10rpx;">|</text>
						<text style="color: #1787fb;font-size: 26rpx;" @click="getLocal">微信获取</text>
						<!-- #endif -->
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">籍贯</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.householdaddress" name="householdaddress" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">婚姻状况</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="marry" :value="pick.i4" :range-key="'name'" name="marriage" @change="bindPickerChange4">
							<view>{{marry[pick.i4].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">所学专业</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="major" :value="pick.i5" :range-key="'name'" name="major" @change="bindPickerChange5">
							<view>{{major[pick.i5].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">身高(CM)</view>
					</view>
					<view class="box_input">
						<input type="number" v-model="binfo.height" name="height" placeholder="请输入数字" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="white">*</view>
						<view class="">身份证号</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="binfo.idcard" name="idcard" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存修改</button>
			</view>
		</form>
	</view>
</template>

<script>
	import http from "../../server/api-job.js"
	import httptwo from "../../server/api-resume.js"
	import httpthree from "../../server/api-upload.js"
	import { pathToBase64, base64ToPath } from '../../js_sdk/gsq-image-tools/image-tools/index.js'
	export default {
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"编辑基本资料", //当type为3或4的时候icon右边的文字
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
					i8:1,
				},
				education:[
					{id:"",name:"请选择"},
				],
				enter:[],
				experience:[
					{id:"",name:"请选择"},
				],
				marry:[
					{id:"0",name:"请选择"},
					{id:1,name:"未婚"},
					{id:2,name:"已婚"},
					{id:3,name:"保密"},
				],
				major:[
					{id:"0",name:"请选择"},
				],
				list_param:{},
				binfo:{
					fullname:"",email:"",height:"",householdaddress:"",idcard:"",qq:"",residence:"",telephone:"",weixin:"",photo_img:"/static/image/defaulticon.png"
				},
				img_param:{},
				local_param:{}
			}
		},
		onLoad() {
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			this.searchJobsMore();
			this.ResumeBasicChoice();
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
			formSubmit: function(e) {
				this.list_param = {
					...this.binfo,
					birthdate:this.enter[this.pick.i1],
					education:this.education[this.pick.i2].id,
					major:this.major[this.pick.i5].id,
					experience:this.experience[this.pick.i3].id,
					marriage:this.marry[this.pick.i4].id,
					sex:this.pick.i8
				};
				this.resumeEditBasis();
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
			bindPickerChange4(e){
				this.pick.i4 = e.target.value
			},
			bindPickerChange5(e){
				this.pick.i5 = e.target.value
			},
			async searchJobsMore(){
				let res = await http.ResumeBasicChoice({},this.headers)
				let edu = res.QS_education;
				let exp = res.QS_experience;
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
			async ResumeBasicChoice(){
				let res = await httptwo.ResumeBasicChoice({},this.headers);
				let major = res.major;
				this.enter = res.birth;
				this.enter.unshift("请选择");
				let arr = [];
				for(let key in major){
					arr.push({id:key,name:major[key]})
				}
				this.major = [...this.major,...arr];
				this.resumeDisplayBasis();
			},
			selsex(t){
				if(t == 1){
					this.pick.i8 = 1;
				}else{
					this.pick.i8 = 2;
				}
			},
			async resumeDisplayBasis(){
				let res = await httptwo.resumeDisplayBasis({},this.headers);
				this.binfo = {
					fullname:res[0].fullname,
					email:res[0].email,
					height:res[0].height,
					householdaddress:res[0].householdaddress,
					idcard:res[0].idcard,
					qq:res[0].qq,
					residence:res[0].residence,
					telephone:res[0].telephone,
					weixin:res[0].weixin,
					photo_img:res[0].photo_img?res[0].photo_img:"/static/image/defaulticon.png"
				};
				this.pick.i1 = this.enter.findIndex(item => item == res[0].birthdate);
				this.pick.i2 = this.education.findIndex(item => item.id === res[0].education);
				this.pick.i3 = this.experience.findIndex(item => item.id == res[0].experience);
				this.pick.i4 = this.marry.findIndex(item => item.id == res[0].marriage);
				this.pick.i5 = this.major.findIndex(item => item.id == res[0].major);
				this.pick.i8 = res[0].sex_cn=="男"?1:2
			},
			async resumeEditBasis(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.resumeEditBasis(this.list_param,this.headers);
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
			},
			upload(){
				let that = this;
				uni.chooseImage({
					sourceType: ["camera", "album"],
					count: 1,
					success: (res) => {
						// #ifdef MP-WEIXIN
						 pathToBase64(res.tempFilePaths[0])
							.then(base64 => {
								let b4 = base64.substring(base64.indexOf(",") + 1);
								that.img_param = {
									base64_string:b4
								};
								that.avatar();
							 })
							 .catch(error => {
								console.error(error);
							 });
						// #endif
						// #ifdef MP-BAIDU
						that.photoImgUpload(res.tempFilePaths[0]);
						// #endif
					}
				});
			},
			async photoImgUpload(dpath){
				let res = await httpthree.photoImgUpload(dpath,this.headers);
				if(res == 1){
					this.resumeDisplayBasis();
				}
			},
			async avatar(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.avatar(this.img_param,this.headers);
				if(res){
					this.binfo.photo_img = res.path
				}
			},
			async resumeEditResidence(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await httptwo.resumeEditResidence(this.local_param,this.headers);
				if(res){
					this.binfo.residence = res.residence;
					uni.showToast({
						title:"修改成功",
						icon:"none"
					})
				}
			},
			getLocal(){
				let that = this;
				uni.getLocation({
					type: 'wgs84',
					success: function (res) {
						that.local_param = {lat:res.latitude,lng:res.longitude};
						that.resumeEditResidence();
					}
				});
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
