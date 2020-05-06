const CONSTAPI = {
	// 简历详情
	resumeDetails:{
		url:"index.php?m=Applet&a=resume_details&c=WxResume",
		method:"GET"
	},
	// 面试邀请
	jobsInterview:{
		url:"index.php?m=Applet&a=jobs_interview&c=WxResume",
		method:"GET"
	},
	// 删除面试邀请
	interviewDel:{
		url:"index.php?m=Applet&a=interview_del&c=WxResume",
		method:"GET"
	},
	// 我的投递
	myDelivery:{
		url:"index.php?m=Applet&a=my_delivery&c=WxResume",
		method:"GET"
	},
	// 删除我的投递
	jobsApplyDel:{
		url:"index.php?m=Applet&a=jobs_apply_del&c=WxResume",
		method:"GET"
	},
	// 对我感兴趣
	attentionMe:{
		url:"index.php?m=Applet&a=attention_me&c=WxResume",
		method:"GET"
	},
	// 删除对我感兴趣
	attentionMeDel:{
		url:"index.php?m=Applet&a=attention_me_del&c=WxResume",
		method:"GET"
	},
	// 简历基本信息添加
	ResumeBasicAdd:{
		url:"index.php?m=Applet&a=ResumeBasicAdd&c=WxResume",
		method:"POST"
	},
	// 简历基本信息添加
	ResumeExpAdd:{
		url:"index.php?m=Applet&a=ResumeExpAdd&c=WxResume",
		method:"POST"
	},
	// 筛选
	ResumeBasicChoice:{
		url:"index.php?m=Applet&a=ResumeBasicChoice&c=WxResume",
		method:"GET"
	},
	// 添加教育经历
	resumeEducation:{
		url:"index.php?m=Applet&a=resume_education&c=WxResume",
		method:"POST"
	},
	// 会显教育经历
	resumeDisplayEducation:{
		url:"index.php?m=Applet&a=resume_display_education&c=WxResume",
		method:"GET"
	},
	// 删除教育经历
	delEducation:{
		url:"index.php?m=Applet&a=del_education&c=WxResume",
		method:"POST"
	},
	// 求职意向回显
	resumeDisplayIntention:{
		url:"index.php?m=Applet&a=resume_display_intention&c=WxResume",
		method:"GET"
	},
	// 求职意向编辑
	resumeEditIntention:{
		url:"index.php?m=Applet&a=resume_edit_intention&c=WxResume",
		method:"POST"
	},
	// 工作经历添加/修改
	resumeWork:{
		url:"index.php?m=Applet&a=resume_work&c=WxResume",
		method:"POST"
	},
	// 工作经历回显
	resumeDisplayWork:{
		url:"index.php?m=Applet&a=resume_display_work&c=WxResume",
		method:"GET"
	},
	// 工作经历删除
	delWork:{
		url:"index.php?m=Applet&a=del_work&c=WxResume",
		method:"POST"
	},
	// 培训添加修改
	resumeTrain:{
		url:"index.php?m=Applet&a=resume_train&c=WxResume",
		method:"POST"
	},
	// 培训经历回显
	resumeDisplayTrain:{
		url:"index.php?m=Applet&a=resume_display_train&c=WxResume",
		method:"GET"
	},
	// 培训经历删除
	delTraining:{
		url:"index.php?m=Applet&a=del_training&c=WxResume",
		method:"POST"
	},
	// 项目经历添加修改
	resumeProject:{
		url:"index.php?m=Applet&a=resume_project&c=WxResume",
		method:"POST"
	},
	// 项目经历回显
	resumeDisplayProject:{
		url:"index.php?m=Applet&a=resume_display_project&c=WxResume",
		method:"GET"
	},
	// 项目经历删除
	delProject:{
		url:"index.php?m=Applet&a=del_project&c=WxResume",
		method:"POST"
	},
	// 自我描述添加修改
	resumeSpecialty:{
		url:"index.php?m=Applet&a=resume_specialty&c=WxResume",
		method:"POST"
	},
	// 个人描述回显
	resumeDisplaySpecialty:{
		url:"index.php?m=Applet&a=resume_display_specialty&c=WxResume",
		method:"GET"
	},
	// 证书添加
	resumeCertificate:{
		url:"index.php?m=Applet&a=resume_certificate&c=WxResume",
		method:"POST"
	},
	// 证书回显
	resumeDisplayCertificate:{
		url:"index.php?m=Applet&a=resume_display_certificate&c=WxResume",
		method:"GET"
	},
	// 证书删除
	delCredent:{
		url:"index.php?m=Applet&a=del_credent&c=WxResume",
		method:"POST"
	},
	// 语言添加修改
	resumeLanguage:{
		url:"index.php?m=Applet&a=resume_language&c=WxResume",
		method:"POST"
	},
	// 语言回显
	resumeDisplayLanguage:{
		url:"index.php?m=Applet&a=resume_display_language&c=WxResume",
		method:"GET"
	},
	// 语言删除
	delLanguage:{
		url:"index.php?m=Applet&a=del_language&c=WxResume",
		method:"POST"
	},
	// 特长标签保存
	resumeSpeciality:{
		url:"index.php?m=Applet&a=resume_speciality&c=WxResume",
		method:"POST"
	},
	// 标签回显
	resumeDisplaySpeciality:{
		url:"index.php?m=Applet&a=resume_display_speciality&c=WxResume",
		method:"GET"
	},
	// 照片作品
	resumeImg:{
		url:"index.php?m=Applet&a=resume_img&c=WxResume",
		method:"POST"
	},
	// 照片统计
	resumeImgCount:{
		url:"index.php?m=Applet&a=resume_img_count&c=WxResume",
		method:"GET"
	},
	// 图片保存数据库
	resumeImgSave:{
		url:"index.php?m=Applet&a=resume_img_save&c=WxResume",
		method:"POST"
	},
	// 我的作品回显
	resumeDisplayImg:{
		url:"index.php?m=Applet&a=resume_display_img&c=WxResume",
		method:"GET"
	},
	// 简历基本信息回显
	resumeDisplayBasis:{
		url:"index.php?m=Applet&a=resume_display_basis&c=WxResume",
		method:"GET"
	},
	// 简历基本信息编辑
	resumeEditBasis:{
		url:"index.php?m=Applet&a=resume_edit_basis&c=WxResume",
		method:"POST"
	},
	// 头像上传
	avatar:{
		url:"index.php?m=Applet&a=avatar&c=WxResume",
		method:"POST"
	},
	// 特长标签删除
	delSpeciality:{
		url:"index.php?m=Applet&a=del_speciality&c=WxResume",
		method:"POST"
	},
	// 我感兴趣的(职位收藏夹)
	jobsFavorites:{
		url:"index.php?m=Applet&a=jobs_favorites&c=WxResume",
		method:"GET"
	},
	// 删除我感兴趣的(职位收藏夹)
	jobsFavoritesDel:{
		url:"index.php?m=Applet&a=jobs_favorites_del&c=WxResume",
		method:"GET"
	},
	// 获取地区
	resumeEditResidence:{
		url:"index.php?m=Applet&a=resume_edit_residence&c=WxResume",
		method:"POST"
	},
	// 删除图片
	delImg:{
		url:"index.php?m=Applet&a=del_img&c=WxResume",
		method:"GET"
	}
}
export default CONSTAPI