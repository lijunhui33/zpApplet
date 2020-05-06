<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="shield_word">
			{{headword}}
		</view>
		<view class="shield_cont">
			<titleNavTwo v-for="(item,i) in sdList" :key="i" :titleCont="item" @editInfo="delSelf"></titleNavTwo>
			<view class="mysubmit" @click="qs_sub(1)">
				新增屏蔽关键词
			</view>
		</view>
		<uni-popup ref="popup" type="center" :custom="true">
			<view class="loginPop">
				添加屏蔽关键词
				<input type="text" v-model="word" />
				<view class="mysubmit" @click="qs_sub(2)">
					保存
				</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	import titleNavTwo from "../../components/title-nav-two/title-nav-two"
	import http from "../../server/api-person.js"
	import UniPopup from "../../components/uni-popup/uni-popup.vue"
	export default {
		components:{
			titleNavTwo,
			UniPopup
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"屏蔽企业", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				sdList:[],
				headers:{},
				headword:"",
				word:"",
				list_param:{}
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers = {"Authorization":"Bearer "+token};
			this.shieldCompany();
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
			async delSelf(e){
				let res = await http.shieldCompanyDel({id:e},this.headers);
				if(res){
					uni.showToast({
						icon:"none",
						title:res.msg
					})
					this.sdList.splice(this.sdList.findIndex(sdList => sdList.id === e),1)
					this.shieldCompany();
				}
			},
			qs_sub(s){
				if(s == 1){
					this.$refs.popup.open()
				}else{
					this.list_param = {comkeyword:this.word},
					this.shieldCompanyAdd();
					this.shieldCompany();
				}
			},
			async shieldCompany(){
				let res = await http.shieldCompany({},this.headers);
				this.headword = `已添加 ${10-res.surplus} 个屏蔽词，还可添加 ${res.surplus} 个`;
				let list = res.list;
				let arr = [];
				list.map(item => {
					arr.push({name:item.comkeyword,src:"/static/image/wrong1.png",btn:"",id:item.id,line:true});
				});
				this.sdList = arr;
			},
			async shieldCompanyAdd(){
				let res = await http.shieldCompanyAdd(this.list_param,this.headers);
				if(res){
					this.$refs.popup.close();
					this.word = "";
					this.shieldCompany();
				}
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/extendPer.less";
</style>
