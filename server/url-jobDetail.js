const CONSTAPI = {
	// 职位详情
	jobsDetails:{
		url:"index.php?m=Applet&a=jobs_details&c=WxJobsDetails",
		method:"GET"
	},
	// 举报职位
	reportJobs:{
		url:"index.php?m=Applet&a=report_jobs&c=WxJobsDetails",
		method:"GET"
	},
	// 简历投递后查询职位电话
	telephone:{
		url:"index.php?m=Applet&a=telephone&c=WxJobsDetails",
		method:"GET"
	}
}
export default CONSTAPI