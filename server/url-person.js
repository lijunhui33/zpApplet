const CONSTAPI = {
	// 会员中心页
	memberIndex:{
		url:"index.php?m=Applet&a=member_index&c=WxMemberCenter",
		method:"GET"
	},
	// 刷新简历
	refreshResume:{
		url:"index.php?m=Applet&a=refresh_resume&c=WxResume",
		method:"GET"
	},
	// 公开 隐私设置
	resumePrivacy:{
		url:"index.php?m=Applet&a=resume_privacy&c=WxResume",
		method:"GET"
	},
	// 屏蔽回显
	shieldCompany:{
		url:"index.php?m=Applet&a=shield_company&c=WxMemberCenter",
		method:"GET"
	},
	// 屏蔽关键字添加
	shieldCompanyAdd:{
		url:"index.php?m=Applet&a=shield_company_add&c=WxMemberCenter",
		method:"GET"
	},
	// 屏蔽删除
	shieldCompanyDel:{
		url:"index.php?m=Applet&a=shield_company_del&c=WxMemberCenter",
		method:"GET"
	},
	// 我的申请
	myApply:{
		url:"index.php?m=Applet&a=my_apply&c=WxMemberCenter",
		method:"GET"
	},
	// 我的面试
	myInterview:{
		url:"index.php?m=Applet&a=my_interview&c=WxMemberCenter",
		method:"GET"
	},
	// 对我感兴趣
	attentionToMe:{
		url:"index.php?m=Applet&a=attention_to_me&c=WxMemberCenter",
		method:"GET"
	},
	// 职位收藏
	jobsFavorites:{
		url:"index.php?m=Applet&a=jobs_favorites&c=WxMemberCenter",
		method:"GET"
	},
	// 删除职位收藏
	jobsFavoritesDel:{
		url:"index.php?m=Applet&a=jobs_favorites_del&c=WxMemberCenter",
		method:"GET"
	},
	// 投递简历
	resumeApply:{
		url:"index.php?m=Applet&a=resume_apply&c=WxJobsDetails",
		method:"GET"
	},
	// 旧手机号码回显
	oldMobile:{
		url:"index.php?m=Applet&a=old_mobile&c=WxMemberCenter",
		method:"GET"
	},
	// 修改手机号码 验证码
	sendmobileSms:{
		url:"index.php?m=Applet&a=sendmobile_sms&c=WxMemberCenter",
		method:"GET"
	},
	// 修改手机号码
	editMobile:{
		url:"index.php?m=Applet&a=edit_mobile&c=WxMemberCenter",
		method:"GET"
	},
	// 修改密码
	editPassword:{
		url:"index.php?m=Applet&a=edit_password&c=WxMemberCenter",
		method:"GET"
	},
	// 会员注册
	register:{
		url:"index.php?m=Applet&a=register&c=WxLogin",
		method:"POST"
	},
	// 用户登录
	login:{
		url:"index.php?m=Applet&a=login&c=WxLogin",
		method:"POST"
	},
	// 第三方微信登录
	WxLogin:{
		url:"index.php?m=Applet&a=WxLogin&c=WxLogin",
		method:"POST"
	},
	// 注册 登录 验证码
	regSendSms:{
		url:"index.php?m=Applet&a=reg_send_sms&c=WxLogin",
		method:"POST"
	},
	// 微信登录
	getUserInfo:{
		url:"index.php?m=Applet&a=get_user_info&c=WxLogin",
		method:"POST"
	},
	// 微信获取手机号登录
	quickLogin:{
		url:"index.php?m=Applet&a=quick_login&c=WxLogin",
		method:"POST"
	},
	// 验证码图片
	captcha:{
		url:"index.php?m=Applet&a=captcha&c=WxLogin",
		method:"POST"
	},
	// 验证码图片2
	makeMaptcha:{
		url:"index.php?m=Applet&a=make_captcha&c=WxLogin",
		method:"GET"
	},
}
export default CONSTAPI