const CONSTAPI = {
	// 聊天用户绑定
	bind:{
		url:"/bind",
		method:"POST"
	},
	// 聊天列表
	chatlist:{
		url:"/chatlist",
		method:"POST"
	},
	// 聊天对话列表
	messagelist:{
		url:"/messagelist",
		method:"POST"
	},
	// 聊天对话发送
	sendmsg:{
		url:"/sendmsg",
		method:"POST"
	},
	// 聊天图片发送
	uploadImg:{
		url:"/upload/image",
		method:"POST"
	},
	// 聊天对象是否在线
	online:{
		url:"/online",
		method:"POST"
	}
}
export default CONSTAPI