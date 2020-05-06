<template>
	<view>
		<view class="tabheader" v-if="is_word != 1">
			<view class="one">
				<view class="tabbox" @click="opt(1)">取消</view>
				<view class="tabnotice" v-if="is_word != 1">多选，最多 3 项</view>
			</view>
			<view class="tabbox extend" @click="opt(2)">确定</view>
		</view>
		<view class="selreg">
			<view class="btn" v-for="(item,i) in listfour" :key="i">
				{{item.name}}<uni-icons type="closeempty" size="20" @click="close(item.id)"></uni-icons>
			</view>
		</view>
		<view class="djnav">
			<scroll-view scroll-y="true" class="dropnav">
				<view v-for="(item,i) in listone" :key="i" :class="item.use+' none'" @click="toName(item.id,1)">
					{{item.name}}
				</view>
			</scroll-view>
			<scroll-view scroll-y="true" class="dropnav dntwo">
				<view v-for="(item,i) in listtwo" :key="i" @click="toName(item.id,2)">
					<view :class="item.use+' ntwo'">{{item.name}}</view>
					<view v-for="(tom,k) in listthree" :key="k" v-if="item.id == tom.parentid" :class="{clickview:arr.includes(tom.id)}" @click="toName(tom.id,3)" style="padding-left: 50rpx;">
						{{tom.name}}
					</view>
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import UniIcons from "../uni-icons/uni-icons.vue"
	export default {
		name:"intentPosition",
		props:['listone','listtwo','listthree','listfour','pid','is_word'],
		components:{
			UniIcons
		},
		data() {
			return {
				arr : []
			}
		},
		methods: {
			close(s){
				this.arr=this.arr.filter(function (ele){return ele != s;});
				this.$emit("closechk",s)
			},
			opt(str){
				this.$emit("resetOrSave",str)
			},
			toName(id,str){
				let data = {id:id,str:str};
				if(str == 3){
					if(this.arr.includes(id)){
						this.arr=this.arr.filter(function (ele){return ele != id;});
						this.$emit("closechk",id)
					}else{
						this.arr.push(id);
						this.$emit("sure",data)
					}
				}else{
					this.$emit("sure",data)
				}
			}
		}
	}
</script>

<style lang="less" scoped>
	@import "../../common/drop.less";
</style>
