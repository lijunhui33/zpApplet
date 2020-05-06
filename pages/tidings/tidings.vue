<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<qs-chat v-for="(item,i) in qsChatList" :key="i" :chatList="item" @chatOnline="chatOnline"></qs-chat>
	</view>
</template>

<script>
	import qsChat from "../../components/qs-chat/qs-chat.vue"
	import http from "../../server/api-index.js";
	import socket from "../../server/api-socket.js";
	import {getDate} from "../../utils/useList.js";
	export default {
		components:{
			qsChat
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"我的职聊", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200 ,//滑动的高度限制，超过这个高度即背景全部显示
				qsChatList:[],
				connectSuccess:false,
				identity:{},
				is_login:true,
				client_id:""
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		// 下拉刷新
		onPullDownRefresh(){
			this.refresh()
		},
		onLoad() {
			// uni.$on('refresh', this.refresh);
		},
		onShow() {
			console.log("onShow");
			let token = uni.getStorageSync("token");
			if(!token){
				uni.navigateTo({
					url:"/pages/personal/myLogin?login=true"
				})
				this.is_login = false
			}else{
				this.is_login = true
				this.userList();
			}
		},
		onHide() {
			console.log("OnHide");
			uni.closeSocket();
			uni.onSocketClose(function (res) {
			  console.log('WebSocket 已关闭！');
			});
		},
		onUnload() {
			// uni.$off('refresh', this.refresh);
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.switchTab({
					url:"../index/index"
				})
			},
			chatOnline:function(str){
				uni.closeSocket();
				uni.navigateTo({
					url:"myChat?uid="+str
				})
			},
			async create(){
				await uni.connectSocket({
					url:"wss://ws.74cms.com"
				});
			},
			async open(){
				let that = this;
				await uni.onSocketOpen(function(res){
					that.connectSuccess = true;
				});
			},
			async message(){
				let that = this;
				await uni.onSocketMessage(function(res){
					let data = JSON.parse(res.data);
					if(data.type === "init"){
						that.client_id = data.client_id;
						that.bind();
					}else{
						that.chatlist();
					}
				});
			},
			async userList(){
				let that = this;
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await http.userList({},headers);
				that.identity = {
					token:res.im_token,
					access_token:res.im_access_token
				};
				// that.create();
				// that.open();
				// that.message();
				uni.connectSocket({
					url:"wss://ws.74cms.com"
				});
				uni.onSocketOpen(function(res){
					that.connectSuccess = true;
				});
				uni.onSocketMessage(function(res){
					let data = JSON.parse(res.data);
					if(data.type === "init"){
						that.client_id = data.client_id;
						that.bind();
					}else{
						that.chatlist();
					}
				});
			},
			async bind(){
				let params = {...this.identity,client_id:this.client_id};
				let res = await socket.bind(params);
				if(res.status == 1){
					this.chatlist();
				}
			},
			async chatlist(){
				let params = {...this.identity};
				let res = await socket.chatlist(params);
				if(res.status == 1){
					let data = res.data;
					let arr = [];
					data.map(item => {
						arr.push({
							uid:item.receiver_info.uid,
							src:item.receiver_info.avatar,
							xianshi:"0",
							com_name:item.receiver_info.nickname,
							renzheng:"0",
							during:getDate(item.updatetime*1000),
							cont:item.last_message,
						})
					});
					this.qsChatList = arr;
				}
			},
			refresh(e){
				this.userList();
				setTimeout(()=>{
					uni.stopPullDownRefresh();
				},2000)
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/chat.less";
</style>
