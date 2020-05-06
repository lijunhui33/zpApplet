<?php
namespace Applet\Controller;
use Applet\Controller\MobileController;
use Applet\Controller\WxTokenController;
class WxChatController extends WxTokenController{
	// 初始化函数
	public function _initialize(){
		parent::_initialize();
		parent::_init_visitor();
	}

	/**
	 * [user_list 历史会话列表]
	 */
	public function user_list(){
		$userList = D('ImUser')->get_user_info();
		$return['im_token'] = $userList['user']['im_token'];
		$return['im_access_token'] = $userList['user']['im_access_token'];
		$this->ajaxReturn(200,'获取token成功',$return);
	}

	/**
	 * [message 快捷回复]
	 */
	public function quick_reply(){
		$utype = C('visitor.utype');
		$map['utype'] = $utype;
		$reply = M('ImText')->where($map)->getField('id,content');
		$this->ajaxReturn(200,'快捷回复获取成功',$reply);
	}

	/** 把网络图片图片转成base64
     * @param string $img 图片地址
     * @return string
     */
    /*网络图片转为base64编码*/
    public function imgtobase64(){
	        if(!$_FILES){
                $this->ajaxReturn(1,'OPTIONS请求成功');
            }
            $savepath = 'imgtobase64/';
            $upload = new \Common\ORG\UploadFile();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->uploadReplace=true;//存在同名文件是否是覆盖 
            $upload->allowExts      =     array('png','gif','bmp','jpg','jpeg');// 设置附件上传类型
            $upload->rootPath  =     QSCMS_DATA_PATH.'upload/'; // 设置附件上传根目录
            $upload->savePath  =     QSCMS_DATA_PATH.'upload/'.$savepath; // 设置附件上传（子）目录
            $upload->thumbPrefix = '';//缩略图的文件前缀，默认为thumb_
            $upload->thumbSuffix = '_thumb';//缩略图的文件后缀，默认为空 
            $upload->thumbExt = '';//指定缩略图的扩展名
            $upload->thumbRemoveOrigin = true;//生成缩略图后是否删除原图 
            $upload->saveRule = uniqid();
            // 上传文件 
            $info   =   $upload->uploadOne($_FILES['file']);
            if(!$info) {// 上传错误提示错误信息
                $this->ajaxReturn(400,$upload->getErrorMsg());
            }else{// 上传成功
                $return['savepath'] = $savepath.$info[0]['savename'];
                $return['attach'] = attach($info[0]['savename'],'imgtobase64');
            }
            $img = $return['attach'];
	        $imageInfo = getimagesize($img);
	        $base64 = "" . chunk_split(base64_encode(file_get_contents($img)));
	        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . base64_encode(file_get_contents($img));
	        $base64_return['savename'] = $info[0]['savename'];
	        $base64_return['base64_image'] = $base64_image;
	        if($base64_image){        
		        $this->ajaxReturn(200,'转换base64成功',$base64_return);
	        }else{
	        	$this->ajaxReturn(0,'转换base64失败');
	        }
	    }

	//删除base64转码后的图片
	public function del_base64_img(){
		$img_name = I('request.img_name');
    	$path = QSCMS_DATA_PATH.'upload/imgtobase64/'.$img_name;
    	$boole = unlink($path);
    	if($boole == true){
    		$this->ajaxReturn(200,'转码图片删除成功');
    	}else{
    		$this->ajaxReturn(0,'转码图片删除失败,请手动删除');
    	}
	}

	/**
	 * [message 会话]
	 */
	public function message(){
		$id = I('get.id','','trim');
		$jobs_id['id'] = $id;
		$jobs_uid = M('Jobs')->where($jobs_id)->getField('uid');
        $uid = intval($jobs_uid);
		if(!$uid)$this->ajaxReturn(0,'请选择用户！');
		$map_uid['uid'] = $uid;
		$sendUser = M('Members')->field('uid,username,avatars,utype')->where($map_uid)->find();
		$utype = explode('_',$uids)[1];
		$utype && $sendUser['utype'] = $utype;
		if(!$sendUser) $this->ajaxReturn(0,'用户不存在或已被管理员删除！');
			$company = M('CompanyProfile')->where($map_uid)->find();
        	$company['companyname'] && $sendUser['username'] = $company['companyname'];
        	$company['logo'] && $sendUser['avatars'] = '';
			//添加自动回复start
			if($id){
				$jobs=M('Jobs')->where(array('uid'=>$map_uid['uid'],'id'=>$id))->find();
				if($jobs['minwage'] && $jobs['maxwage']){
					$wage=$jobs['minwage'].'-'.$jobs['maxwage'];
				}else{
					$wage='面议';
				}
				$city = explode('/',$jobs['district_cn']);
                $district = end($city);
				$send_jobs1=array(
					'jobsname'=>$jobs['jobs_name'],
					'wage'=>$wage,
					'district'=>$district,
					'experience'=>$jobs['experience_cn'],
					'education'=>$jobs['education_cn'],
					'jobsurl'=>url_rewrite('QS_jobsshow',array('id'=>$jobs['id'])),
					'jobs_id'=>$jobs['id'],
					'type'=>'1',
				);
				$send_jobs=json_encode($send_jobs1);

				$resume = M('Resume')->field('id,fullname,sex,birthdate,education_cn,experience_cn,district_cn,intention_jobs')->where(array('uid'=>C('visitor.uid')))->limit(1)->find();
				$resume['fullname'] && $sendUser['username'] = $resume['fullname'];
				$age=date("Y")-$resume['birthdate'];
				$send_resume1=array(
					'resumename'=>$resume['fullname'],
					'sex'=>$resume['sex'],
					'age'=>$age,
					'education'=>$resume['education_cn'],
					'experience'=>$resume['experience_cn'],
					'district'=>$resume['district_cn'],
					'intention'=>$resume['intention_jobs'],
					'resumeurl'=>url_rewrite('QS_resumeshow',array('id'=>$resume['id'],'style'=>'resumeim')),
					'resume_id'=>$resume['id'],
					'type'=>'2',
				);
				$send_resume=json_encode($send_resume1);
			}
			//end
		
		if($sendUser['avatars']){
            $sendUser['avatars'] = attach($sendUser['avatars'],'avatar');
        }elseif($sendUser['utype'] == 1){
        	$sendUser['avatars'] = $company['logo'] ? attach($company['logo'],'company_logo') : attach('no_logo.png','resource');
        }elseif($sendUser['utype'] == 2){
            $avatar_default = $sex==1?'no_photo_male.png':'no_photo_female.png';
            $sendUser['avatars'] = attach($avatar_default,'resource');
        }else{
        	$sendUser['avatars'] = attach('no_photo_male.png','resource');
        }
        $im = new \Common\qscmslib\im();
        if(false == $imuser = $im->get_user_info($sendUser)){
        	$this->ajaxReturn(0,$im->getError());
        }
        $sendUser['uid'] .= '_' . $sendUser['utype'];
		//添加快捷语句start
		$message = M('ImText')->where(array('utype'=>C('visitor.utype')))->select();
		foreach($message as $key => $val){
			$content[]=$val['content'];
		}
		$msg=implode('-',$content);
		$duringtime=time()-7*3600*24;
		//end
		$return = ['send_resume'=>$send_resume,'send_jobs'=>$send_jobs,'msg'=>$msg,'duringtime'=>$duringtime];
		$this->ajaxReturn(200,'查询成功',$return);
	}
	
}