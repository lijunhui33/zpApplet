import CryptoJS from '../node_modules/crypto-js/crypto-js.js'
function chooseItem(state,name){
	let item = null
	if(state == 1){
		item = {
			src:"/static/image/is_top.png",
			name:"已置顶",
			use:"qs_three_color",
			imgStyle:"qre_two_img"
		}
	}else{
		item = {
			src:"",
			name:name,
			use:"qs_four_color",
			imgStyle:""
		}
	}
	return item
}
function voluation(res){
	let arr = []
	res.map(item => {
		arr.push({
			job_name:item.jobs_name,
			urgen:item.emergency,
			pay:item.wage||item.wage_cn,
			id:item.id,
			uid:item.uid,
			cont:item.district_cn+" | "+item.experience_cn+" | "+item.education_cn,
			com_name:item.companyname,
			tag:item.tag_cn,
			xianyan:item.setmeal_id>1?1:0,
			huiyuan:item.setmeal_id>1?1:0,
			renzheng:item.audit,
			toudi:"0",
			is_top:item.stick,
			item:chooseItem(item.stick,item.refreshtime),
			distance:item.map_range||""
		})
	})
	return arr
}
function source(res){
	let arr = []
	res.map(item => {
		let tags = []
		if(item.tag != ""){
			let t = item.tag.split(",");
			t.map(item => {
				tags.push(item.slice(item.indexOf("|")+1));
			})
		}
		let btns = []
		btns.push(item.setmeal_id==1?{use:"tagc4",name:"普通会员"}:{use:"tagc1",name:"名企会员"});
		if(item.audit==1){
			btns.push({use:"tagc2",name:"执照认证"});
		}
		if(item.report==1){
			btns.push({use:"tagc3",name:"实地认证"});
		}
		arr.push({
			id:item.id,
			logo:item.logo,
			job_name:item.jobs,
			cont:`${item.nature_cn} | ${item.scale_cn} | ${item.trade_cn}`,
			com_name:item.companyname,
			tag:tags,
			num:item.jobs_count,
			btng:btns
		})
	})
	return arr
}
function getDate(time) {
	let date = new Date(time);
	let y = date.getFullYear();
	let m = date.getMonth() + 1;
	let d = date.getDate();
	let h = date.getHours();
	let mi = date.getMinutes();
	let now = Date.parse(new Date());
	let y2 = new Date().getFullYear();
	let newday = Date.parse(new Date(y2+"-01-01 00:00:00"));
	if(now - time < 3600*24*1000){
		mi = mi > 9 ? mi : '0' + mi;
		return h + ':' + mi;
	}else if((now - time) > 3600*24*1000 && (now - time) < 3600*48*1000){
		return "1天前";
	}else if(time > newday){
		d = d > 9 ? d : "0" + d;
		return m + '-' + d;
	}else{
		m = m > 9 ? m : '0' + m;
		return y + '-' + m;
	}
}

/**
 * 接口数据加密函数
 */
function encrypt(text,eckey) {
    var key = CryptoJS.enc.Latin1.parse(eckey+"qscms"); //为了避免补位，直接用16位的秘钥
	var iv = CryptoJS.enc.Latin1.parse('Qs_CMs8RcXT00XcX'); //16位初始向量
	var encrypted = CryptoJS.AES.encrypt(text, key, {
			iv: iv,
			mode:CryptoJS.mode.CBC,
			padding:CryptoJS.pad.ZeroPadding
		});
	return encrypted.toString();
}


export {
	voluation,
	source,
	getDate,
	encrypt
}