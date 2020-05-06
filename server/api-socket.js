import socket from "../utils/socket.js"
import constApi from "./url-socket.js"

const http = {}

for(let key in constApi){
	let api = constApi[key]
	http[key] = async function(params){
		let response = await socket.globalRequest(api.url,api.method,params)
		return response
	}
}

export default http