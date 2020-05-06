<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="qs-line"></view>
		<view class="chat_header" v-if="false">
			<view class="">
				<image src="../../static/image/call1.png" mode=""></image>
				<view class="">
					打电话
				</view>
			</view>
			<view class="">
				<image src="../../static/image/wechat1.png" mode="aspectFit"></image>
				<view class="">
					发微信
				</view>
			</view>
		</view>
		<view class="chat_window" id="talk">
			<view v-for="(item,i) in chatList" :key="i">
				<left-chat v-if="item.status<2" :param="item"></left-chat>
				<right-chat v-if="item.status == 2" :param="item"></right-chat>
				<right-two v-if="item.status == 3" :param="item"></right-two>
				<right-img v-if="item.status > 3" :param="item"></right-img>
			</view>
		</view>
		<view style="height: 140rpx;background: #f5f5f5;"></view>
		<view class="chat_footer">
			<image src="../../static/image/photo1.png" mode="" @click="upload"></image>
			<image src="../../static/image/chatgroup1.png" mode="" @click="pop"></image>
			<view class="box">
				<input type="text" v-model="word" style="padding-left: 10rpx;"/>
				<view class="subbtn" @click="send">
					发送
				</view>
			</view>
		</view>
		<uni-popup ref="popup" type="bottom" :custom="true">
			<view class="popline" v-for="(item,i) in list_say" :key="i" @click="sendPop(item)">{{item}}</view>
		</uni-popup>
	</view>
</template>

<script>
	import leftChat from "../../components/left-chat/left-chat"
	import rightChat from "../../components/right-chat/right-chat"
	import rightTwo from "../../components/right-two/right-two"
	import rightImg from "../../components/right-img/right-img"
	import http from "../../server/api-index.js";
	import socket from "../../server/api-socket.js";
	import httptwo from "../../server/api-upload.js"
	import {getDate} from "../../utils/useList.js";
	import UniPopup from "../../components/uni-popup/uni-popup.vue"
	import { pathToBase64, base64ToPath } from '../../js_sdk/gsq-image-tools/image-tools/index.js'
	export default{
		components:{
			leftChat,
			rightChat,
			rightTwo,
			rightImg,
			UniPopup
		},
		data(){
			return{
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"我的职聊", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				chatList:[],
				word:"",
				connectSuccess:false,
				identity:{},
				client_id:"",
				uid:"",
				send_param:{},
				img_param:{},
				online_param:{},
				del_param:{},
				auto_param:{},
				list_say:[],
				auto_bool:false,
				savename:""
			}
		},
		onLoad(option) {
			console.log("onLoad");
			this.uid = option.uid;
			// this.uid = "116_1";
			this.userList();
			this.quickReply();
			if(option.id != undefined){
				this.auto_bool = true;
				this.auto_param = {id:option.id};
			}
		},
		onHide() {
			console.log("OnHide");
		},
		onUnload() {
			console.log("onUnload");
			uni.closeSocket();
			uni.onSocketClose(function (res) {
			  console.log('WebSocket 已关闭！');
			});
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.closeSocket();
				uni.$emit('refresh', {data:2});
				uni.navigateBack({
				    delta: 1,
				    animationType: 'pop-out',
				    animationDuration: 200
				});
			},
			send(){
				this.send_param = {...this.identity,uid:this.uid,message:this.word,type:"text"};
				this.sendmsg();
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
						that.messagelist();
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
				console.log(that.identity);
				// that.create();
				// that.open();
				// that.message();
				uni.connectSocket({
					url:"wss://ws.74cms.com"
				});
				uni.onSocketOpen(function(res){
					console.log(res);
					that.connectSuccess = true;
				});
				uni.onSocketMessage(function(res){
					let data = JSON.parse(res.data);
					console.log(data);
					if(data.type === "init"){
						that.client_id = data.client_id;
						uni.setStorageSync("client_id",data.client_id);
						that.bind();
					}else{
						let client_id = uni.getStorageSync("client_id");
						console.log(client_id);
						console.log(that.client_id);
						that.messagelist();
					}
				});
			},
			async bind(){
				let params = {...this.identity,client_id:this.client_id};
				let res = await socket.bind(params);
				console.log(res);
				if(res.status == 1){
					this.messagelist();
				}
			},
			async sendmsg(){
				let res = await socket.sendmsg(this.send_param);
				console.log(res);
				if(res.status == 1){
					this.word = "";
					this.messagelist();
				}
			},
			async uploadImg(){
				let res = await socket.uploadImg(this.img_param);
				console.log(res);
				if(res.status == 1){
					this.send_param = {...this.identity,uid:this.uid,message:res.data,type:"img"};
					this.sendmsg();
					// #ifdef MP-BAIDU
					this.delBase64Img();
					// #endif
				}
			},
			upload(){
				let that = this;
				uni.chooseImage({
					sourceType: ["camera", "album"],
					count: 1,
					success: (res) => {
						// #ifndef MP-BAIDU
						 pathToBase64(res.tempFilePaths[0])
							.then(base64 => {
								let b4 = base64.substring(base64.indexOf(",") + 1);
								that.img_param = {
									...that.identity,
									base64_string:b4
								};
								that.uploadImg();
							 })
							 .catch(error => {
								console.error(error);
							 });
						// #endif
						// #ifdef MP-BAIDU
						that.imgtobase64(res.tempFilePaths[0]);
						// #endif
					}
				});
			},
			async imgtobase64(dpath){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await httptwo.imgtobase64(dpath,headers);
				if(res){
					let b4 = res.base64_image.substring(res.base64_image.indexOf(",") + 1);
					this.del_param = {img_name:res.savename};
					this.img_param = {
						...this.identity,
						base64_string:b4
					};
					this.uploadImg();
				}
			},
			async quickReply(){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await http.quickReply({},headers);
				for(let key in res){
					this.list_say.push(res[key]);
				}
			},
			async delBase64Img(){
				const token = uni.getStorageSync('token');
				let headers = {"Authorization":"Bearer "+token};
				let res = await http.delBase64Img(this.del_param,headers);
			},
			async online(){
				let res = await socket.online(this.online_param);
				// console.log(res);
			},
			async messagetwo(){
				const token = uni.getStorageSync('token');
				let headers = {"Content-Type":"application/x-www-form-urlencoded","Authorization":"Bearer "+token};
				let res = await http.message(this.auto_param,headers);
				let arr = [
					{name:JSON.stringify(JSON.parse(res.send_jobs)),type:"json"},
					{name:res.msg,type:"text"},
					{name:JSON.stringify(JSON.parse(res.send_resume)),type:"json"},
				];
				let that = this;
				arr.map(item => {
					that.send_param = {...that.identity,uid:that.uid,message:item.name,type:item.type};
					that.sendmsg();
				})
			},
			pop(){
				this.$refs.popup.open();
			},
			sendPop(str){
				this.send_param = {...this.identity,uid:this.uid,message:str,type:"text"};
				this.sendmsg();
				this.$refs.popup.close();
			},
			async messagelist(){
				let params = {...this.identity,uid:this.uid};
				let res = await socket.messagelist(params);
				let data = res.data;
				if(this.auto_bool && data.length == 0){
					this.messagetwo();
				}
				let arr = [];
				data.map(item => {
					let message_type = item.message_type;
					let sender_info_uid = item.sender_info.uid;
					if(this.uid == sender_info_uid){
						if(message_type == "text"){
							arr.push({src:item.sender_info.avatar,cont:item.message,addtime:getDate(item.addtime*1000),status:"0"})
						}else{
							arr.push({src:item.sender_info.avatar,cont:item.message,addtime:getDate(item.addtime*1000),status:"4"})
						}
					}else{
						if(message_type == "text"){
							arr.push({src:item.sender_info.avatar,cont:item.message,addtime:getDate(item.addtime*1000),status:"1"})
						}else if(message_type == "json"){
							let msg = JSON.parse(item.message);
							if(msg.type == 1){
								arr.push({
									src:item.sender_info.avatar,
									jobsname:msg.jobsname,
									wage:msg.wage,
									jobs_id:msg.jobs_id,
									info:msg.district+"|"+msg.experience+"|"+msg.education,
									addtime:getDate(item.addtime*1000),
									status:"2",
								})
							}else{
								arr.push({
									src:item.sender_info.avatar,
									resumename:msg.resumename,
									sex:msg.sex,
									intention:msg.intention,
									district:msg.district,
									info:msg.age+"岁|"+msg.education+"|"+msg.experience,
									addtime:getDate(item.addtime*1000),
									status:"3",
								})
							}
						}else{
							arr.push({src:item.sender_info.avatar,cont:item.message,addtime:getDate(item.addtime*1000),status:"5"})
						}
					}
					this.chatList = arr;
				})
			},
		},
		watch:{
			Talk() {
				this.$nextTick(() => {
					var container = this.$el.querySelector('#talk')
					container.scrollTop = container.scrollHeight
				})
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/chat.less";
</style>
