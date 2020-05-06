<template>
	<view>
		<!-- <navigation-custom :config="config" :scrollTop="scrollTop" @customConduct="customConduct" :scrollMaxHeight="scrollMaxHeight"></navigation-custom> -->
		<view class="ep_cont">
			<view class="notice">
				已添加 {{seltag.length}} 个标签，还可添加 {{count-seltag.length}} 个。
			</view>
			<view class="specbtn">
				<view v-for="(item,i) in tag" :key="i" :class="item.use" @click="mySel(i)">
					<text>{{item.name}}</text>
					<image v-if="item.use" src="../../static/image/mysure.png" mode="aspectFit" class="roomImage"></image>
				</view>
				<navigator url="addSpeciality" hover-class="none">
					<view class="add">
						<image src="../../static/image/myadd.png" mode="" class="roomImage"></image>
						<text>自定义标签</text>
					</view>
				</navigator>
				<view v-for="(item,i) in custom" :key="i" :class="item.use" @click="mySel2(i)">
					<text>{{item.name}}</text>
					<image v-if="item.use" src="../../static/image/mysure.png" mode="aspectFit" class="roomImage"></image>
				</view>
			</view>
			<view class="mysubmit" @click="formadd">
				保存
			</view>
		</view>
	</view>
</template>

<script>
	import UniIcons from "../../components/uni-icons/uni-icons.vue"
	import http from "../../server/api-resume.js"
	export default {
		components:{
			UniIcons
		},
		data() {
			return {
				config:{
					type:3, //type 1，3胶囊 2，4无胶囊模式
					menuIcon:"/static/image/back.png", //当type为3或者4的时候左边的icon文件位置，注意位置与当前页面不一样
					menuText:"编辑特长标签", //当type为3或4的时候icon右边的文字
				},
				scrollTop:0 ,// 当linear为true的时候需要通过onpagescroll传递参数
				scrollMaxHeight:200, //滑动的高度限制，超过这个高度即背景全部显示
				headers:{},
				list_param:{},
				tag:[],
				count:6,
				seltag:[],
				custom:[]
			}
		},
		onLoad(){
			const token = uni.getStorageSync('token');
			this.headers['Authorization'] = 'Bearer '+token;
			this.ResumeBasicChoice();
			uni.$on('add', this.add);
		},
		onUnload(){
			uni.$off('add', this.add);
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		},
		methods: {
			//当config type 为 4或者3 的时候 自定义methods
			customConduct(){
				uni.navigateBack({
				    delta: 1,
				    animationType: 'pop-out',
				    animationDuration: 200
				});
			},
			async ResumeBasicChoice(){
				let res = await http.ResumeBasicChoice({},this.headers);
				let rtg = res.QS_resumetag;
				let arr = [];
				for(let key in rtg){
					arr.push({id:key,name:rtg[key],use:""})
				}
				this.tag = arr;
				this.resumeDisplaySpeciality();
			},
			mySel(index){
				if(this.tag[index].use == "selected"){
					this.tag[index].use = "";
					this.seltag.splice(this.seltag.findIndex(item => item === this.tag[index]),1);
				}else{
					if(this.seltag.length >= 6){
						uni.showToast({
							icon:"none",
							title:`最多选中${this.count}个`
						})
						return false;
					}
					this.tag[index].use = "selected";
					this.seltag.push(this.tag[index]);
				}
			},
			mySel2(index){
				if(this.custom[index].use == "selected"){
					this.seltag.splice(this.seltag.findIndex(item => item.name === this.custom[index].name),1);
					this.custom.splice(this.custom.findIndex(item => item === this.custom[index]),1);
				}else{
					if(this.seltag.length >= 6){
						uni.showToast({
							icon:"none",
							title:`最多选中${this.count}个`
						})
						return false;
					}
					this.custom[index].use = "selected";
					this.seltag.push(this.custom[index]);
				}
			},
			async resumeDisplaySpeciality(){
				let res = await http.resumeDisplaySpeciality({},this.headers);
				if(res[0].tag != ""){
					let tag = res[0].tag;
					let tag_cn = res[0].tag_cn;
					let tags = tag.split(",");
					let tag_cns = tag_cn.split(",");
					tags.map((item,k) => {
						if(item == ""){
							this.custom.push({id:"",name:tag_cns[k],use:"selected"});
						}else{
							this.tag[this.tag.findIndex(dom => dom.id === item)].use = "selected"
						}
						this.seltag.push({id:item,name:tag_cns[k],use:""});
					})
				}
			},
			add(e){
				if(this.seltag.length >= 6){
					uni.showToast({
						icon:"none",
						title:`最多选中${this.count}个`
					})
					return false;
				}
				this.custom.push({id:"",name:e.name,use:"selected"});
				let dom = this.seltag;
				this.seltag = dom.concat(this.custom.filter(v => !dom.includes(v)))
			},
			async resumeSpeciality(){
				this.headers['content-type'] = 'application/x-www-form-urlencoded';
				let res = await http.resumeSpeciality(this.list_param,this.headers);
				if(res.status == 200){
					uni.$emit("showtoast",{msg:'操作成功'});
					let that = this;
					// #ifndef MP-BAIDU
					setTimeout(()=>{
					// #endif
						that.customConduct();
					// #ifndef MP-BAIDU
					},2000)
					// #endif
				}
			},
			formadd(){
				let tag = [];
				let tag_cn = [];
				this.seltag.map(item => {
					tag.push(item.id);
					tag_cn.push(item.name)
				});
				this.list_param = {
					tag_cn:tag_cn.join(","),
					tag:tag.join(",")
				};
				this.resumeSpeciality();
			}
		}
	}
</script>

<style lang="less">
	@import "../../common/edit.less";
</style>
