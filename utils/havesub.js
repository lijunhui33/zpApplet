import baseUrl from "./config.js";
const request = {}

request.globalRequest = (url, method, data, headers, type) => {
	/*权限判断 因为有的接口请求头可能需要添加的参数不一样，所以这里做了区分
	1 == 不通过access_token校验的接口
	2 == 文件下载接口列表
	3 == 验证码登录 */
	return uni.request({
		url: baseUrl + url,
		method,
		data: data,
		header:headers,
		dataType: 'json'
	}).then(res => {
		if (res[1].statusCode == 200) {
			return res[1].data
		} else {
			throw res[1].data
		}
	}).catch(parmas => {
　　　　　　switch (parmas.status) {
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
export default request