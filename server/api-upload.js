import upload from "../utils/upload.js"
import constApi from "./url-upload.js"

const http = {}

for(let key in constApi){
	let api = constApi[key]
	http[key] = async function(params,headers){
		let response = await upload.globalRequest(api.url,params,headers)
		return response
	}
}

export default http