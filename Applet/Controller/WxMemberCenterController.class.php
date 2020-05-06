<?php
namespace Applet\Controller;
use Applet\Controller\WxTokenController;
use Applet\Controller\WxCodeController;
use Common\Controller\FrontendController;
class WxMemberCenterController extends WxTokenController{

    public function _initialize() {
        parent::_initialize();
        parent::_init_visitor();
        $utype = C('visitor.utype');
        if($utype != 2){
            $this->ajaxReturn(401,'会员身份不正确,请切换个人身份');
        }
    }

    public function member_index(){
        $uid = C('visitor.uid');
        !$uid && $this->ajaxReturn(401,'请重新登录');
    	$resume = D('Resume')->get_resume_list(array('where' => array('uid' => $uid), 'limit' => 1, 'order' => 'def desc', 'countinterview' => true, 'countdown' => true, 'countapply' => true, 'views' => true, 'stick' => true, 'feedback' => true));
    	$return['photo_img'] = attach($resume[0]['photo_img'],'avatar');
        $return['fullname'] =  $resume[0]['fullname'];
        if($resume[0]['birthdate'] == NULL){
            $return['age'] = NULL;
        }else{
            $return['age'] = date('Y') - $resume[0]['birthdate'];
        }

    	$return['education_cn'] = $resume[0]['education_cn'];
    	$return['experience_cn'] = $resume[0]['experience_cn'];
        $return['countapply'] = $resume[0]['countapply'];

        if($resume[0]['countinterview'] == NULL){
            $return['countinterview'] = 0;
        }else{
            $return['countinterview'] = $resume[0]['countinterview'];
        }

        if($resume[0]['views'] == NULL){
            $return['views'] = 0;
        }else{
            $return['views'] = $resume[0]['views'];
        }
        //我的收藏
    	$map['personal_uid'] = $uid;
    	$favorites = M('PersonalFavorites')->where($map)->count();
		$return['favorites'] = $favorites;
        //简历完整度
        if($resume[0]['complete_percent'] == NULL){
            $return['complete_percent'] = 0;
        }else{
            $return['complete_percent'] = $resume[0]['complete_percent'];
        }
        //公开状态
    	$return['countapply'] = $resume[0]['countapply'];
    	if($resume[0]['display'] == 1){
    		$return['display'] = '公开';
    	}else{
    		$return['display'] = '保密';
    	}
        //计算刷新时间
        $now = date('Y');
        $nowyear = strtotime($now.'-01-01');
        $today = strtotime(date('Y-m-d'));
        $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
        $diftime = time() - $resume[0]['refreshtime'];
        if($resume[0]['refreshtime'] < $nowyear){
            $return['refreshtime'] = date('Y-m',$resume[0]['refreshtime']);
        }else{
            if($diftime >= 5*60 && $diftime<= 60*60){
                $return['refreshtime'] = intval($diftime / 60) ."分钟前";
            }else if($diftime >= 60*60 && $resume[0]['refreshtime']<= $tomorrow && $resume[0]['refreshtime'] >= $today){
                $return['refreshtime'] = intval($diftime / (60*60)) ."小时前";
            }else if($diftime < 5*60){
                $return['refreshtime'] = "刚刚";
            }else{
                $return['refreshtime'] = date('m-d h:i',$resume[0]['refreshtime']);
            }
        }
        // $rank['refreshtime'] = array('gt',$resume[0]['refreshtime']);
        // $return['rank'] = M('Resume')->where($rank)->count();
        $return['rank'] = rand(20,100);
    	$this->ajaxReturn(200,'查询成功',$return);
    }

    //我的申请
    public function my_apply(){
        $where['personal_uid'] = C('visitor.uid');
        $personal_apply_mod = D('PersonalJobsApply');
        $apply_list = $personal_apply_mod->get_apply_jobs($where);
        $list['isfull'] = $apply_list['page_params']['isfull'];
        if($list['list'] = $apply_list['list']){
            $apply_list['page_params']['actualPage'] > $apply_list['page_params']['totalPages'] && $list['list'] = array();
            foreach ($apply_list['list'] as $key => $value){
            $map['id'] = $value['jobs_id'];
            $jobs = M('Jobs')->where($map)->find();
            if($jobs){
                $arr['id'] = $value['jobs_id'];
                $arr['did'] = $value['did'];
                $arr['jobs_name'] = $value['jobs_name'];
                if($value['minwage'] == '0'){
                    $arr['wage_cn'] = "面议";
                }else{
                    $arr['wage_cn'] = $value['minwage'][0].'K-'.$value['maxwage'][0].'K';
                }
                $arr['company_name'] = $value['company_name'];
                if($value['apply_addtime'] == 0){
                    $arr['apply_addtime'] = NULL;
                }else{
                    $arr['apply_addtime'] = date('Y-m-d H:i',$value['apply_addtime']);
                }
                if($value['personal_look_time'] == 0){
                    $arr['personal_look_time'] = NULL;
                }else{
                    $arr['personal_look_time'] = date('Y-m-d H:i',$value['personal_look_time']);
                }
                if($value['reply_time'] == 0){
                    $arr['reply_time'] = NULL;
                }else{
                    $arr['reply_time'] = date('Y-m-d H:i',$value['reply_time']);
                }
                $arr['is_reply'] = $value['is_reply'];
                $arr['personal_look'] = $value['personal_look'];
                //personal_look = 1  企业未查看
                //is_reply = 0 待反馈
                //is_reply = 1 合适
                //is_reply = 2 不合适
                //is_reply = 3 待定
                //is_reply = 4 未接通
                $returnlist[] = $arr;
            }else{
                $array['id'] = $value['jobs_id'];
                $array['did'] = $value['did'];
                $array['tips'] = '该职位不存在或已被删除';
                $returnlist[] = $array;
            }
        }
        $return['returnlist'] = $returnlist;
        $return['nowPage'] = $apply_list['page_params']['nowPage'];
        $return['totalPages'] = $apply_list['page_params']['totalPages'];
        $return['isfull'] = $apply_list['page_params']['isfull']; 
        $this->ajaxReturn(200, "申请的职位获取成功", $return);
        }
        $list['list'] = array();
        $this->ajaxReturn(0, "没有更多信息！",$return);
    }


    /**
    * 面试邀请
    */
    public function my_interview() {
        $where['resume_uid'] = C('visitor.uid');
        $company_interview_mod = D('CompanyInterview');
        $interview = $company_interview_mod->get_invitation_pre($where);
        $list['isfull'] = $interview['page_params']['isfull'] ;
        if ($list['list'] = $interview['list']) {
            $interview['page_params']['actualPage'] > $interview['page_params']['totalPages'] && $list['list'] = array();
        foreach ($interview['list'] as $key => $value) {
            $map['id'] = $value['jobs_id'];
            $jobs = M('Jobs')->where($map)->find();
            if($jobs){
                $arr['id'] = $value['jobs_id'];
                $arr['did'] = $value['did'];
                $arr['jobs_name'] = $value['jobs_name'];
                $arr['wage_cn'] = $value['wage_cn'];
                $arr['company_name'] = $value['company_name'];
                $H = date('H',$value['interview_time']);
                if($H > 7 && $H < 12){
                    $array['interview_H'] = '上午';
                }else{
                    $array['interview_H'] = '下午';
                }
                $arr['interview_time'] = date('Y-m-d',$value['interview_time']).' '.$array['interview_H'].' '.date('H:i',$value['interview_time']);
                $arr['contact'] = $value['telephone'].'('.$value['contact'].')';
                $arr['address'] = $value['address'];
                //interview_state = 1  查看职位
                //interview_state = 0  面试时间已过
                if(time() < $value['interview_time']){
                    $arr['interview_state'] = '1';
                    $arr['jobs_id'] = $value['jobs_id'];
                }else{
                    $arr['interview_state'] = '0';
                    $arr['jobs_id'] = $value['jobs_id'];
                } 
                $returnlist[] = $arr;
            }else{
                $array['id'] = $value['jobs_id'];
                $array['did'] = $value['did'];
                $array['tips'] = '该职位不存在或已被删除';
                $returnlist[] = $array;
            }
        }
        $return['returnlist'] = $returnlist;
        $return['nowPage'] = $interview['page_params']['nowPage'];
        $return['totalPages'] = $interview['page_params']['totalPages'];
        $return['isfull'] = $interview['page_params']['isfull']; 
        $this->ajaxReturn(200, "面试邀请获取成功", $return);
        }
        $list['list'] = array();
        $this->ajaxReturn(0, "没有更多信息！",$return);
    }

    /**
    * 对我感兴趣
    */
    public function attention_to_me(){
        $where['resume_uid'] = C('visitor.uid');
        $view_list = D('ViewResume')->m_get_view_resume($where);
        $list['isfull'] = $view_list['page_params']['isfull'];
        if ($list['list'] = $view_list['list']) {
            $view_list['page_params']['actualPage'] > $view_list['page_params']['totalPages'] && $list['list'] = array();
            $map_report['status'] = 1;
            if(C('apply.Report')){
                $report = M('CompanyReport')->where($map_report)->getField('com_id,id');
            }else{
                $report = array();
            }
            foreach ($view_list['list'] as $key => $value){
                $arr['company_id'] = $value['company_id'];
                if($arr['company_id'] !== NULL){
                    $arr['id'] = $value['id'];
                    $arr['company_id'] = $value['company_id'];
                    $arr['company_uid'] = $value['uid'];
                    $arr['companyname'] = $value['companyname'];
                    $arr['setmeal_id'] = $value['setmeal_id'];
                    $arr['audit'] = $value['audit'];
                    if(isset($report[$value['id']]) && $report[$value['company_id']]){
                       $arr['report'] = 1; 
                    }else{
                       $arr['report'] = 0;
                    }
                    $arr['nature_cn'] = $value['nature_cn'];
                    $arr['scale_cn'] = $value['scale_cn'];
                    $arr['trade_cn'] = $value['trade_cn'];
                    $arr['addtime'] = date('Y-m-d',$value['addtime']);
                    //hasdown = 1 已下载
                    //hasdown = 0 未下载
                    if($value['hasdown'] == 1){
                        $arr['downtime'] = date('Y-m-d',$value['down_addtime']);
                        $arr['hasdown'] = '1';
                    }else{
                        $arr['downtime'] = 0;
                        $arr['hasdown'] = '0';
                    }
                    $returnlist[] = $arr;
                }else{
                    $array['id'] = $value['id'];
                    $array['tips'] = '该公司不存在';
                    $returnlist[] = $array;
                }
            }
            $return['returnlist'] = $returnlist;
            $return['nowPage'] = $view_list['page_params']['nowPage'];
            $return['totalPages'] = $view_list['page_params']['totalPages'];
            $return['isfull'] = $view_list['page_params']['isfull']; 
            $this->ajaxReturn(200, "简历被关注信息获取成功", $return);
        }
            $this->ajaxReturn(0, "没有更多信息！",$return);
        }


    /**
     * 职位收藏夹
     */
    public function jobs_favorites() {
        $where['personal_uid'] = C('visitor.uid');
        $favorites = D('PersonalFavorites')->get_favorites($where);
        $list['isfull'] = $favorites['page_params']['isfull'];
        if ($list['list'] = $favorites['list']) {
            $favorites['page_params']['actualPage'] > $favorites['page_params']['totalPages'] && $list['list'] = array();
            foreach ($favorites['list'] as $key => $value) {
                $arr['company_id'] = $value['company_id'];
                $arr['company_uid'] = $value['company_uid'];
                if($arr['company_id'] !== NULL){
                    $arr['did'] = $value['did'];
                    $arr['jobs_id'] = $value['jobs_id'];
                    $arr['jobs_name'] = $value['jobs_name'];
                    $arr['display'] = $value['display'];
                    if($value['minwage'] == '0'){
                        $arr['wage_cn'] = "面议";
                    }else{
                        $arr['wage_cn'] = $value['minwage'].''.$value['maxwage'];
                    }
                    $arr['companyname'] = $value['companyname'];
                    $arr['addtime'] = date('Y-m-d H:i',$value['addtime']);
                    $apply = M('PersonalJobsApply')->where(array('jobs_id'=>$value['jobs_id'],'personal_uid'=>$value['personal_uid']))->find();
                    //apply = 1 已投递
                    //apply = 0 未投递
                    if($apply){
                       $arr['apply'] = 1; 
                       $arr['apply_addtime'] = date('Y-m-d H:i',$apply['apply_addtime']); 
                    }else{
                       $arr['apply'] = 0;
                       $arr['apply_addtime'] = 0; 
                    }
                    $returnlist[] = $arr;
                }else{
                    $array['did'] = $value['did'];
                    $array['tips'] = '该公司不存在';
                    $returnlist[] = $array;
                }
            }
            $return['returnlist'] = $returnlist;
            $return['nowPage'] = $favorites['page_params']['nowPage'];
            $return['totalPages'] = $favorites['page_params']['totalPages'];
            $return['isfull'] = $favorites['page_params']['isfull'];  
            $this->ajaxReturn(200, "获取职位收藏夹成功", $return);
        }
        $list['list'] = array();
        $this->ajaxReturn(0, "没有更多信息！",$return);
    }

    /**
     * 删除职位收藏夹
     */
    public function jobs_favorites_del() {
        $did = I('request.id', '', 'trim,badword');
        !$did && $this->ajaxReturn(400, "你没有选择项目！");
        $reg = D('PersonalFavorites')->del_favorites($did, C('visitor'));
        if ($reg['state'] === true) {
            $this->ajaxReturn(200, "删除成功！");
        } else {
            $this->ajaxReturn(0, $reg['error']);
        }
    }

    /**
     * [shield_company 屏蔽企业]
     */
    public function shield_company() {
        $keywords = M('PersonalShieldCompany')->where(array('uid' => C('visitor.uid')))->select();
        $this->ajaxReturn(200, '屏蔽企业信息获取成功！',array('list'=>$keywords,'surplus'=>10 - count($keywords)));
    }

    /**
     * [shield_company_add 添加屏蔽企业关键字]
     */
    public function shield_company_add() {
        $keyword = I('request.comkeyword', '', 'trim');
        !$keyword && $this->ajaxReturn(0, '企业关键字不能为空！');
        $data['uid'] = C('visitor.uid');
        if (10 <= $count = M('PersonalShieldCompany')->where($data)->count()) $this->ajaxReturn(0, '您最多屏蔽 10 个企业关键词！');
        $data['comkeyword'] = $keyword;
        $shield_mod = D('PersonalShieldCompany');
        if (false === $shield_mod->create($data)) $this->ajaxReturn(0, $shield_mod->getError());
        if (false === $data['id'] = $shield_mod->add()) $this->ajaxReturn(0, '企业关键字添加失败，请重新添加！');
        //写入会员日志
        write_members_log(C('visitor'), '', '添加屏蔽企业（关键字：' . $keyword . '）');
        $this->ajaxReturn(200, '企业关键字添加成功！', $data);
    }

    /**
     * [shield_company_del 删除屏蔽企业关键字]
     */
    public function shield_company_del() {
        $keyword_id = I('request.id', 0, 'intval');
        !$keyword_id && $this->ajaxReturn(0, '请选择关键字！');
        if ($reg = M('PersonalShieldCompany')->where(array('id' => $keyword_id, 'uid' => C('visitor.uid')))->delete()) {
            //写入会员日志
            write_members_log(C('visitor'), '', '删除屏蔽企业（id：' . $keyword_id . '）');
            $this->ajaxReturn(200, '关键字删除成功！');
        }
        $reg === false && $this->ajaxReturn(0, '关键字删除失败！');
        $this->ajaxReturn(0, '关键字不存在或已经删除！');
    }

    /**
     * 修改密码
     */
    public function edit_password(){
        $uid = C('visitor.uid');
        $password = I("request.password",'',"trim");
        $confirm = I("request.confirm",'',"trim");
        if($password !== $confirm){
            $this->ajaxReturn(0,"密码请填写一致");
        }
        $randstr = M("Members")->where(array("uid"=>$uid))->getfield("pwd_hash");
        $password_new = D("Members")->make_md5_pwd($password, $randstr);
        $result = M("Members")->where(array("uid"=>$uid))->setField("password",$password_new);
        if($result == 1){
            $this->ajaxReturn(200,"密码修改成功",$result);
        }else{
            $this->ajaxReturn(0,"密码修改失败",$result);
        }
    }

    /**
    *   修改手机号
    */
    public function edit_mobile(){
        $uid = C('visitor.uid');
        $mobile = I("request.mobile",'',"trim");
        $code_sms = I('request.mobile_code', 0, 'intval');
        !$code_sms && $this->ajaxReturn(0, '验证码不能为空！');
        $WxCode = A('WxCode');
        $sms = $WxCode->get_vcode_Token();
        if($sms){
            $token = $sms;
            $check = $WxCode->regcheck($token,$code_sms);
            if($check == 'code error'){
                $this->ajaxReturn(400, '验证码错误');
            }else if($check == 'timeout'){
                $this->ajaxReturn(400, '验证码已过期');
            }else if($check == 'token invalid'){
                $this->ajaxReturn(400, '验证码无效');
            }else if($check == 'issuer error'){
                $this->ajaxReturn(400, '发行机构错误');
            }
        }  
        $Members_mobile = M("Members")->where(array("uid"=>$uid))->setField("mobile",$mobile);
        $Resume_result = M("Resume")->where(array("uid"=>$uid))->setField("telephone",$mobile);
        $result = $Resume_result * $Members_mobile;
        if($result == 1){
            $this->ajaxReturn(200,"手机号码修改成功",$result);
        }else{
            $this->ajaxReturn(0,"手机号码修改失败",$result);
        }
    }

    //修改手机号回显旧手机号码
    public function old_mobile(){
        $oldmobile = C('visitor.mobile');
        $this->ajaxReturn(200,"查询成功",$oldmobile);
    }

    /**
     * 修改手机号码发送短信
     */
     public function sendmobile_sms() {
        $mobile = I('request.mobile','trim');
        if(!$mobile){
            $this->ajaxReturn(0, '请填手机号码！');
        }
        //防盗刷图形验证码
        $data = I('request.data', '', 'trim');
        !$data && $this->ajaxReturn(0, '请填写图形验证码!');
        $WxCaptcha = A('WxCaptcha');
        $captcha = $WxCaptcha->get_captcha_Token();
        if($captcha){
            $check = $WxCaptcha->regcheck($captcha,$data);
            if($check == 'code error'){
                $this->ajaxReturn(900, '图形验证码错误');
            }else if($check == 'timeout'){
                $this->ajaxReturn(900, '图形验证码已过期');
            }
        }

        if (!fieldRegex($mobile, 'mobile')) $this->ajaxReturn(0, '手机号错误！');
        //判断手机号是否注册
        $is_exist = M("Members")->where(array("mobile"=>$mobile))->getfield("mobile");
        if($is_exist){
             $this->ajaxReturn(0, '手机号已存在，请更换手机号!');
        }
        $map_sitename['name'] = 'site_name';
        $sitename = M('Config')->where($map_sitename)->getfield('value',true);
        $rand = getmobilecode();
        $sendSms['mobile'] = $mobile;
        $sendSms['tpl'] = 'set_mobile_auth';
        $sendSms['time'] = 180;
        $sendSms['data'] = array('rand' => $rand . '', 'sitename' => $sitename[0]);
        $WxCode = A('WxCode');
        $smsVerify = $WxCode->createToken($sendSms['data']['rand'],$sendSms['time']);
        $sms = $WxCode->get_vcode_Token(); 
        if($sms){
            $token = $sms;
            $check = $WxCode->regcheck($token);
            if($check == 'wait'){
                $this->ajaxReturn(0, '请等待'.$sendSms['time'].'秒');
            }else if($check == 'timeout'){
                $this->ajaxReturn(401, '验证码已过期');
            }
        }
        if (true === $reg = D('Sms')->sendSms('captcha', $sendSms)) {
            $this->ajaxReturn(200, '手机验证码发送成功！',$smsVerify);
        } else {
            $this->ajaxReturn(0, $reg);
        }
    }




}