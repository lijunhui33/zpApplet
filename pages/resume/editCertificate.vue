<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ec_cont">
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">证书名称</view>
					</view>
					<view class="box_input">
						<input type="text" v-model="info" name="name" placeholder="请输入" placeholder-class="iright" style="text-align: right;"/>
						<view class="has"></view>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">获得时间</view>
					</view>
					<view class="box_input">
						<picker mode="date" :value="date" :start="startDate" :end="endDate" @change="bindDateChange">
							<view class="uni-input">{{date}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存</button>
				<view class="deleteEdu" @click="delCredent" v-if="!subType">删除该证书</view>
			</form>
		</view>
	</view>
</template>

<script>
	import http from "../../server/api-resume.js"
	export default {
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"编辑获得证书", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				date:"请选择",
				headers:{},
				list_param:{},
				info:"",
				id:"",
				subType:true,
			}
		},
		onLoad(option){
			this.id = option.id;
			if(option.id != undefined){
				this.subType = false
			}
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			if(!this.subType)
				this.resumeDisplayCertificate();
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
			formSubmit(e){
				let fd = e.detail.value;
				let d1 = this.date.split("-");
				if(this.subType){
					this.list_param = {
						...fd,
						year:d1[0],
						month:d1[1]
					};
				}else{
					this.list_param = {
						...fd,
						id:this.id,
						year:d1[0],
						month:d1[1]
					};
				}
				this.resumeCertificate();
			},
			async resumeCertificate(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.resumeCertificate(this.list_param,this.headers);
				if(res.state == 1){
					uni.$emit("showtoast",{msg:'操作成功'});
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
			async resumeDisplayCertificate(){
				let res = await http.resumeDisplayCertificate({id:this.id},this.headers);
				if(res != undefined){
					this.info = res[0].name;
					this.date = res[0].year + "-" + res[0].month
				}
			},
			async delCredent(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.delCredent({id:this.id},this.headers);
				if(res.status == 200){
					uni.$emit("showtoast",{msg:'操作成功'});
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

