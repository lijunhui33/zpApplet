const CONSTAPI = {
	// 职位列表
	searchJobsList:{
		url:"index.php?m=Applet&a=search_jobs_list&c=WxJobsSearch",
		method:"GET"
	},
	// 地区筛选
	searchJobsRegion:{
		url:"index.php?m=Applet&a=search_jobs_region&c=WxJobsSearch",
		method:"GET"
	},
	// 职位筛选
	searchJobsPosition:{
		url:"index.php?m=Applet&a=search_jobs_position&c=WxJobsSearch",
		method:"GET"
	},
	// 薪资筛选
	searchJobsSalary:{
		url:"index.php?m=Applet&a=search_jobs_salary&c=WxJobsSearch",
		method:"GET"
	},
	// 更多筛选条件
	searchJobsMore:{
		url:"index.php?m=Applet&a=search_jobs_more&c=WxJobsSearch",
		method:"GET"
	},
	// 同城招聘
	nearbyJobsList:{
		url:"index.php?m=Applet&a=nearby_jobs_list&c=WxIndex",
		method:"GET"
	},
	// 名企推荐
	searchCompanyList:{
		url:"index.php?m=Applet&a=search_company_list&c=WxCompanySearch",
		method:"GET"
	},
	// 企业搜索条件 行业
	searchCompanyPosition:{
		url:"index.php?m=Applet&a=search_company_position&c=WxCompanySearch",
		method:"GET"
	},
	// 企业搜索条件 性质
	searchCompanyNature:{
		url:"index.php?m=Applet&a=search_company_nature&c=WxCompanySearch",
		method:"GET"
	},
	// 企业搜索条件 规模
	searchCompanyScale:{
		url:"index.php?m=Applet&a=search_company_scale&c=WxCompanySearch",
		method:"GET"
	},
	// 判断是否添加过订阅
	haveSubscribe:{
		url:"index.php?m=Applet&a=have_subscribe&c=WxSubscribe",
		method:"GET"
	},
	// 查看订阅
	subscribeQuery:{
		url:"index.php?m=Applet&a=subscribe_query&c=WxSubscribe",
		method:"GET"
	},
	// 添加订阅
	subscribeAdd:{
		url:"index.php?m=Applet&a=subscribe_add&c=WxSubscribe",
		method:"GET"
	},
	// 修改订阅
	subscribeEdit:{
		url:"index.php?m=Applet&a=subscribe_edit&c=WxSubscribe",
		method:"GET"
	},
	// 删除订阅
	subscribeDel:{
		url:"index.php?m=Applet&a=subscribe_del&c=WxSubscribe",
		method:"GET"
	},
	// 企业详情
	companysDetails:{
		url:"index.php?m=Applet&a=companys_details&c=WxCompanyDetails",
		method:"GET"
	},
	// 企业实地认证报告
	companyReport:{
		url:"index.php?m=Applet&a=company_report&c=WxCompanyDetails",
		method:"GET"
	},
	// 微信模板id
	AppletTemplate:{
		url:"index.php?m=Applet&a=Applet_template&c=WxSubscribe",
		method:"GET"
	},
	// 百度判断是否添加过订阅
	bdhaveSubscribe:{
		url:"index.php?m=Applet&a=have_subscribe&c=BaiduSubscribe",
		method:"GET"
	},
	// 百度查看订阅
	bdsubscribeQuery:{
		url:"index.php?m=Applet&a=subscribe_query&c=BaiduSubscribe",
		method:"GET"
	},
	// 百度添加订阅
	bdsubscribeAdd:{
		url:"index.php?m=Applet&a=subscribe_add&c=BaiduSubscribe",
		method:"GET"
	},
	// 百度修改订阅
	bdsubscribeEdit:{
		url:"index.php?m=Applet&a=subscribe_edit&c=BaiduSubscribe",
		method:"GET"
	},
	// 百度删除订阅
	bdsubscribeDel:{
		url:"index.php?m=Applet&a=subscribe_del&c=BaiduSubscribe",
		method:"GET"
	},
	// 微信订阅模板
	weixinappTemplate:{
		url:"index.php?m=Applet&a=weixinapp_template&c=WxSubscribe",
		method:"GET"
	},
	// 微信订阅按钮清除
	authorize:{
		url:"index.php?m=Applet&a=authorize&c=WxSubscribe",
		method:"GET"
	},
	// 学历经验添加筛选条件
	ResumeBasicChoice:{
		url:"index.php?m=Applet&a=ResumeBasicChoice&c=WxResume",
		method:"GET"
	},
	// 默认地区
	defaultDistrict:{
		url:"index.php?m=Applet&a=default_district&c=WxJobsSearch",
		method:"GET"
	}
}
export default CONSTAPI