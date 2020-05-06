import request from "../utils/request.js"
import constApi from "./url-job.js"

const http = {}

for(let key in constApi){
	let api = constApi[key]
	http[key] = async function(params,headers){
		let response = await request.globalRequest(api.url,api.method,params,headers)
		return response
	}
}

export default http