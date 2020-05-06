const CONSTAPI = {
	// 首页职位列表
	hotJobList:{
		url:"index.php?m=Applet&a=hot_job_list&c=WxIndex",
		method:"GET"
	},
	// 首页职位列表(最新)
	newestJobList:{
		url:"index.php?m=Applet&a=newest_job_list&c=WxIndex",
		method:"GET"
	},
	// 首页轮播广告---
	indexAd:{
		url:"index.php?m=Applet&a=index_ad&c=WxIndex",
		method:"GET"
	},
	// 首页点击职位搜索
	searchSpreadOut:{
		url:"index.php?m=Applet&a=search_spread_out&c=WxJobsSearch",
		method:"GET"
	},
	// 会话token
	userList:{
		url:"index.php?m=Applet&a=user_list&c=WxChat",
		method:"GET"
	},
	// 会话快捷回复
	quickReply:{
		url:"index.php?m=Applet&a=quick_reply&c=WxChat",
		method:"GET"
	},
	// 聊天自动发送
	message:{
		url:"index.php?m=Applet&a=message&c=WxChat",
		method:"GET"
	},
	// 职位详情页面职位收藏
	favoritejobs:{
		url:"index.php?m=Applet&a=favoritejobs&c=WxJobsDetails",
		method:"GET"
	},
	// 职位详情页面投递简历
	resumeApply:{
		url:"index.php?m=Applet&a=resume_apply&c=WxJobsDetails",
		method:"GET"
	},
	// 职位详情页面职位收藏
	delBase64Img:{
		url:"index.php?m=Applet&a=del_base64_img&c=WxChat",
		method:"GET"
	},
	// 网站配置信息
	webCfg:{
		url:"index.php?m=Applet&a=web_cfg&c=WxIndex",
		method:"GET"
	}
}
export default CONSTAPI