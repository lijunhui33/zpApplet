const socket = {};
const sb_url = "https://im.74cms.com";

socket.globalRequest = (url, method, data) => {
	/*权限判断 因为有的接口请求头可能需要添加的参数不一样，所以这里做了区分
	1 == 不通过access_token校验的接口
	2 == 文件下载接口列表
	3 == 验证码登录 */
	return uni.request({
		url: sb_url+url,
		method,
		data: data,
		header:{"Content-Type":"application/x-www-form-urlencoded"},
		dataType: 'json'
	}).then(res => {
		console.log(res);
		if (res[1].data.status == 1 && res[1].statusCode == 200) {
			return res[1].data
		}  else {
			uni.showToast({
				icon:"none",
				title:res[1].data.msg
			})
		}
	})
}
export default socket;