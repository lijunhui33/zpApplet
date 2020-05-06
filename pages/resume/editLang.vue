<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ec_cont">
			<form @submit="formSubmit">
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">选择语种</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="language" :value="pick.i1" :range-key="'name'" name="language" @change="bindPickerChange">
							<view>{{language[pick.i1].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<view class="edit_input">
					<view class="label">
						<view class="red">*</view>
						<view class="">熟悉程度</view>
					</view>
					<view class="box_input">
						<picker mode="selector" :range="language_level" :value="pick.i2" :range-key="'name'" name="language_level" @change="bindPickerChange2">
							<view>{{language_level[pick.i2].name}}</view>
						</picker>
						<image src="../../static/image/back3.png" mode=""></image>
					</view>
				</view>
				<view class="qs-line"></view>
				<button form-type="submit" class="mysubmit">保存</button>
				<view class="deleteEdu" @click="delLanguage" v-if="!subType">删除该语言能力</view>
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
					menuText:"编辑语言能力", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				date:"请选择",
				headers:{},
				list_param:{},
				info:"",
				id:"",
				subType:true,
				pick:{i1:0,i2:0},
				language:[
					{id:"",name:"请选择"}
				],
				language_level:[
					{id:"",name:"请选择"}
				],
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
			bindPickerChange(e){
				this.pick.i1 = e.target.value
			},
			bindPickerChange2(e){
				this.pick.i2 = e.target.value
			},
			formSubmit(e){
				let fd = e.detail.value;
				let d1 = this.date.split("-");
				if(this.subType){
					this.notice = "添加成功";
					this.list_param = {
						language:this.language[this.pick.i1].id,
						level:this.language_level[this.pick.i2].id,
						language_cn:this.language[this.pick.i1].name,
						level_cn:this.language_level[this.pick.i2].name,
					};
				}else{
					this.notice = "修改成功";
					this.list_param = {
						id:this.id,
						language:this.language[this.pick.i1].id,
						level:this.language_level[this.pick.i2].id,
						language_cn:this.language[this.pick.i1].name,
						level_cn:this.language_level[this.pick.i2].name,
					};
				}
				this.resumeLanguage();
			},
			async ResumeBasicChoice(){
				let res = await http.ResumeBasicChoice({},this.headers);
				let lgg = res.language;
				let lgl = res.language_level;
				for(let key in lgg){
					this.language.push({id:key,name:lgg[key]});
				}
				for(let key in lgl){
					this.language_level.push({id:key,name:lgl[key]});
				}
				if(!this.subType)
					this.resumeDisplayLanguage();
			},
			async resumeLanguage(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.resumeLanguage(this.list_param,this.headers);
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
			async resumeDisplayLanguage(){
				let res = await http.resumeDisplayLanguage({id:this.id},this.headers);
				if(res != undefined){
					this.pick.i1 = this.language.findIndex(item => item.name === res[0].language_cn);
					this.pick.i2 = this.language_level.findIndex(item => item.name === res[0].level_cn);
				}
			},
			async delLanguage(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.delLanguage({id:this.id},this.headers);
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

