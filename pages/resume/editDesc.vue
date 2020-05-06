<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ec_cont">
			<form @submit="formSubmit">
				<textarea class="big_input" name="specialty" v-model="desc" placeholder="详细描述一下你自己，字数控制在10-500字内。" />
				<view class="qs-line"></view>
				<view class="dtitle">
					可以试着从这几个思路去描述：
				</view>
				<view class="detailLi">你认为值得称道的工作细节；</view>
				<view class="detailLi">你曾经克服的最大挑战；</view>
				<view class="detailLi">你曾经引以为豪的个人项目或事迹；</view>
				<view class="detailLi">其他你认为能展示你优势的事情。</view>
				<button form-type="submit" class="mysubmit">保存</button>
			</form>
		</view>
	</view>
</template>

<script>
	import http from "../../server/api-resume.js"
	export default {
		components:{
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"编辑自我描述", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				headers:{},
				list_param:{},
				desc:""
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			this.resumeDisplaySpecialty();
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
				let fd = e.detail.value
				this.list_param = fd;
				this.resumeSpecialty();
			},
			async resumeSpecialty(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.resumeSpecialty(this.list_param,this.headers);
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
			async resumeDisplaySpecialty(){
				let res = await http.resumeDisplaySpecialty({},this.headers);
				if(res){
					this.desc = res[0].specialty
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
