import baseUrl from "./config.js";
const upload = {}

upload.globalRequest = (url, filepath,headers) => {
	/*权限判断 因为有的接口请求头可能需要添加的参数不一样，所以这里做了区分
	1 == 不通过access_token校验的接口
	2 == 文件下载接口列表
	3 == 验证码登录 */
	return uni.uploadFile({
		url: baseUrl + url, //仅为示例，非真实的接口地址
		filePath: filepath,
		name: 'file',
		header:headers,
	}).then(res => {
		if (res[1].data.status == 200 && res[1].statusCode == 200) {
			if(res[1].data.data)
				return res[1].data.data
			else
				return res[1].data
		} else if (res[1].data.status == 0 && res[1].statusCode == 200){
			uni.showToast({
				title: res[1].data.msg,
				icon: 'none'
			})
		} else {
			throw res[1].data
		}
	}).catch(parmas => {
　　　　　　switch (parmas.status) {
　　　　　　　　case 401:
　　　　　　　　case 402:
　　　　　　　　　　  uni.clearStorageSync()
					if(type == 1){
						uni.showToast({
	　　　　　　　　　　　　  title: "请登录",
	　　　　　　　　　　　　  icon: 'none'
	　　　　　　　　　　  })
					}else{
						uni.navigateTo({
							url:"/pages/personal/myLogin"
						})
					}
　　　　　　　　　　  break;
　　　　　　　　default:
　　　　　　　　　　  uni.showToast({
　　　　　　　　　　　　  title: parmas.msg,
　　　　　　　　　　　　  icon: 'none'
　　　　　　　　　　  })
				    return Promise.reject()
　　　　　　　　　　  break;
　　　　　　}
　　})
}
export default upload