<?php
namespace Applet\Controller;
use Common\Controller\FrontendController;
use Applet\Controller\WxTokenController;
use Applet\Controller\WxCodeController;
use Applet\Controller\WxBizDataCrypt;
class WxLoginController extends FrontendController{

    /**
     * [login 用户登录]
     */
    public function login() {
        if (IS_POST) {
            $expire = I('request.expire', 1, 'intval');
            $url = I('request.url', '', 'trim');
            $bind_id = I('request.bind_id','intval');
            // //后台开启了极验，并且开启了会员登录验证
            // if (C('qscms_captcha_open') == 1 && (C('qscms_captcha_config.user_login') == 0 || (session('?error_login_count') && session('error_login_count') >= C('qscms_captcha_config.user_login')))) {
            //     if (true !== $reg = \Common\qscmslib\captcha::verify()) $this->ajaxReturn(0, $reg);
            // }
            $passport = $this->_user_server();
            $mobile = I('request.mobile','','trim');
            if ($mobile) {//手机验证码登录
                !fieldRegex($mobile, 'mobile') && $this->ajaxReturn(0, '手机号格式错误！');
                $vcode = I('request.mobile_vcode', 0, 'intval');
                $WxCode = A('WxCode');
                $sms = $WxCode->getToken();
                if($sms){
                    $token = substr($sms,7);
                    $check = $WxCode->regcheck($token,$vcode);
                    if($check == 'code error'){
                        $this->ajaxReturn(0, '验证码错误');
                    }else if($check == 'timeout'){
                        $this->ajaxReturn(0, '验证码已过期');
                    }
                }                
                //$smsVerify = session('login_smsVerify');
                // if(true !== $tip = verify_mobile($mobile,$smsVerify,I('request.mobile_vcode', 0, 'intval'))) $this->ajaxReturn(400, $tip);
                $map['mobile'] = $mobile;
                $user = M('Members')->where($map)->find();
                if($user){
                    $uid = $user['uid'];
                    //判断身份是企业还是个人，返回对应数据
                    // if ($user['utype'] == 1 && !$user['sitegroup_uid']) {
                    //     $company = M('CompanyProfile')->field('companyname,contact,landline_tel')->where(array('uid' => $user['uid']))->find();
                    //     $user = array_merge($user, $company);
                    // }
                    if (!$user['sitegroup_uid'] && $passport->is_sitegroup()) {
                        $temp = $passport->uc('sitegroup')->register($user);
                        $temp && M('Members')->where(array('uid' => $user['uid']))->setfield('sitegroup_uid', $temp['sitegroup_uid']);
                    }
                    // session('login_smsVerify', null);
                   	$WxToken = A('WxToken');
                    $token = $WxToken->createToken($user['uid'],$user['utype']);
                    //第三方登录绑定
                    if($bind_id !== NULL){
                        $map_bind['id'] = $bind_id;
                        $data['uid'] = $user['uid'];
                        $data['is_bind'] = 1;
                        $data['bindingtime'] = time();
                        $bind_save = M('MembersBind')->where($map_bind)->save($data);
                    }
                } elseif ($passport->is_sitegroup() && false !== $sitegroup_user = $passport->uc('sitegroup')->get($smsVerify['mobile'], 'mobile')) {
                    $this->_sitegroup_register($sitegroup_user, 'mobile');
                } else {
                    $err = '帐号不存在！';
                }
            } else {//用户名登录
                $username = I('request.username', '', 'trim');
                $password = I('request.password', '', 'trim');
                if (false === $uid = $passport->uc('default')->auth($username, $password)) {
                    $err = $passport->get_error();
                    if ($err == L('auth_null')) {
                        if ($passport->is_sitegroup()) {
                            if (false === $passport->uc('sitegroup')->auth($username, $password)) {
                                $err = $passport->get_error();
                            } else {
                                $this->_sitegroup_register($passport->_user);
                            }
                        }
                    }
                } else {
                    $user = $passport->_user;
                   	$WxToken = A('WxToken');
                    $token = $WxToken->createToken($user['uid'],$user['utype']);
                    //第三方登录绑定
                    if($bind_id !== NULL){
                        $map_bind['id'] = $bind_id;
                        $data['uid'] = $user['uid'];
                        $data['is_bind'] = 1;
                        $data['bindingtime'] = time();
                        $bind_save = M('MembersBind')->where($map_bind)->save($data);
                    }
                    if ($user['utype'] == 1 && (!$user['sitegroup_uid'])) {
                        $company = M('CompanyProfile')->field('companyname,contact,landline_tel')->where(array('uid' => $user['uid']))->find();
                        $user = array_merge($user, $company);
                    }  
                    if (!$user['sitegroup_uid'] && $passport->is_sitegroup()) {
                        $temp = $passport->uc('sitegroup')->register($user);
                        $temp && M('Members')->where(array('uid' => $user['uid']))->setfield('sitegroup_uid', $temp['sitegroup_uid']);
                    }
                }
            }
            if ($uid) {
                if (false === $this->visitor->login($uid, $expire)) $this->ajaxReturn(0, $this->visitor->getError());
                $this->ajaxReturn(200, '登录成功！', $token);
            }
            //记录登录错误次数
            if (C('qscms_captcha_open') == 1) {
                if (C('qscms_captcha_config.user_login') > 0) {
                    $error_login_count = session('?error_login_count') ? (session('error_login_count') + 1) : 1;
                    session('error_login_count', $error_login_count);
                    if (session('error_login_count') >= C('qscms_captcha_config.user_login')) {
                        $verify_userlogin = 1;
                    } else {
                        $verify_userlogin = 0;
                    }
                } else {
                    $verify_userlogin = 1;
                }
            } else {
                $verify_userlogin = 0;
            }
            $this->ajaxReturn(0, $err, $verify_userlogin);
        } else {
			$this->ajaxReturn(0, '登录失败！');
        }
    }

    //第三方登录 (微信绑定)
    // public function get_user_info(){
    //     $code = I('request.code','',"trim");
    //     $encryptedData = $_POST['encryptedData'];
    //     $iv = I('request.iv');
    //     $url      = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . C('qscms_weixinapp_appid') . '&secret=' . C('qscms_weixinapp_appsecret') . '&js_code=' . $code . '&grant_type=authorization_code';
    //     $result   = https_request($url);
    //     $jsoninfo = json_decode($result, true);
    //     $sessionKey   = $jsoninfo['session_key'];

    //     $appid = C('qscms_weixinapp_appid');
    //     $class = A('WxBizDataCrypt');
    //     $class->__construct($appid, $sessionKey,$data);
    //     $user_info = $class->decryptData($encryptedData, $iv);
    //     $info = json_decode($user_info,true);
    //     $map_bind['openId'] = $info['openId'];
    //     $map_bind['unionId'] = $info['unionId'];
    //     if($map_bind){
    //         $map_bind['_logic'] = 'or';
    //         $map_bind['_complex'] = $map_bind;
    //     }else{
    //         $this->ajaxReturn(0, '参数错误');
    //     }
    //     $bind_query = M('MembersBind')->where($map_bind)->find();
    //     $return = [];
    //     if(!$bind_query){
    //         $data['uid'] = 0;
    //         $data['type'] = 'weixin';
    //         $data['keyid'] = '';
    //         $info['info'] = array('openid'=>$info['openId'],'keyname'=>$info['nickName'],'gender'=>$info['gender'],'language'=>$info['language'],'city'=>$info['city'],'province'=>$info['province'],'country'=>$info['country'],'headimgurl'=>$info['avatarUrl']);
    //         $data['info'] = serialize($info['info']);
    //         $data['bindingtime'] = 0;
    //         $data['focustime'] = 0;
    //         $data['openid'] = $info['openId'];
    //         $data['unionid'] = $info['unionId'];
    //         $data['is_bind'] = 0;
    //         $data['is_focus'] = 0;
    //         $bind_add = M('MembersBind')->add($data);
    //         $return = [is_bind=>$data['is_bind'],id=>$bind_add];
    //     }else{
    //         if($bind_query['is_bind'] == 0){
    //             $return = [is_bind=>$bind_query['is_bind'],id=>$bind_query['id']];
    //             $this->ajaxReturn(200, '请绑定账号',$return);
    //         }else{
    //             $info['info'] = array('openid'=>$info['openId'],'keyname'=>$info['nickName'],'gender'=>$info['gender'],'language'=>$info['language'],'city'=>$info['city'],'province'=>$info['province'],'country'=>$info['country'],'headimgurl'=>$info['avatarUrl']);
    //             $data['info'] = serialize($info['info']);
    //             $bind_save = M('MembersBind')->where(array('type' => 'weixin', 'unionid' => $info['unionid']))->save($data);

    //             $WxToken = A('WxToken');
    //             $token = $WxToken->createToken($bind_query['uid'],2);
    //             $return = [is_bind=>$bind_query['is_bind'],token=>$token];
    //             $this->ajaxReturn(200, '登录成功！', $return);
    //         }
    //     }

    // }

    //微信快捷登录(通过微信获取手机号码登录，不涉及绑定)
    public function quick_login(){
        $code = I('request.code','',"trim");
        $encryptedData = $_POST['encryptedData'];
        $iv = I('request.iv');
        $url      = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . C('qscms_weixinapp_appid') . '&secret=' . C('qscms_weixinapp_appsecret') . '&js_code=' . $code . '&grant_type=authorization_code';
        $result   = https_request($url);
        $jsoninfo = json_decode($result, true);
        $sessionKey   = $jsoninfo['session_key'];

        $appid = C('qscms_weixinapp_appid');
        $class = A('WxBizDataCrypt');
        $class->__construct($appid, $sessionKey);
        //$class = new WxBizDataCrypt($appid, $sessionKey);
        $user_info = $class->decryptData($encryptedData, $iv,$data);
        if($user_info === false){
            $this->ajaxReturn(0,'参数错误',$class->get_error());
        }     
        $info = json_decode($user_info,true);

        $mobile = $info['purePhoneNumber'];
        $map_member['mobile'] = $mobile;
        $member = M('Members')->where($map_member)->find();
        if(!$member){
            $this->ajaxReturn(110, '账号还未注册');
        }
        $WxToken = A('WxToken');
        $token = $WxToken->createToken($member['uid'],$member['utype']);
        $this->ajaxReturn(200, '登录成功',$token);
    }

    /**
     * [register 会员注册]
     */
    public function register() {
        // if (!I('request.org', '', 'trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']) {
        //     redirect(build_mobile_url(array('c' => 'Members', 'a' => 'register')));
        // }
        $bind_id = I('request.bind_id','intval');
        if (C('qscms_closereg')) {
            IS_AJAX && $this->ajaxReturn(0, '网站暂停会员注册，请稍后再次尝试！');
            $this->error("网站暂停会员注册，请稍后再次尝试！");
        }
        if (IS_POST){
            $data['utype'] = I('request.utype', 2, 'intval');
            $data['mobile'] = I('request.mobile', '', 'trim');
            !$data['mobile'] && $this->ajaxReturn(0, '请填写手机号!');
            $vcode = I('request.mobile_vcode', 0, 'intval');
            $WxCode = A('WxCode');
            $sms = $WxCode->getToken();
            if($sms){
                $token = substr($sms,7);
                $check = $WxCode->regcheck($token,$vcode);

                if($check == 'code error'){
                    $this->ajaxReturn(0, '验证码错误');
                }else if($check == 'timeout'){
                    $this->ajaxReturn(0, '验证码已过期');
                }
            }
            //$smsVerify = session('reg_smsVerify');
            // if(true !== $tip = verify_mobile($data['mobile'],$smsVerify,I('request.mobile_vcode', 0, 'intval'))) $this->ajaxReturn(0, $tip);
            //后台开启注册填写密码
            if (C('qscms_register_password_open')) {
                $data['password'] = I('request.password', '', 'trim');
                !$data['password'] && $this->ajaxReturn(0, '请输入密码!');
            }
            //注册帐号
            $passport = $this->_user_server('');
            if (false === $data = $passport->register($data)) {
                $this->ajaxReturn(0, $passport->get_error());
            }
            
            //第三方帐号注册
            if ('bind' == I('request.org', '', 'trim') &&  cookie('members_bind_info')) {
                $user_bind_info = object_to_array(cookie('members_bind_info'));
                $user_bind_info['uid'] = $data['uid'];
                $oauth = new \Common\qscmslib\oauth($user_bind_info['type']);
                $oauth->bindUser($user_bind_info);
                $this->_save_avatar($user_bind_info['temp_avatar'], $data['uid']);//临时头像转换
                cookie('members_bind_info', NULL);//清理绑定COOKIE
                session('members_bind_info', NULL);//清理绑定session
            }
            // session('reg_smsVerify', null);
           	$WxToken = A('WxToken');
            $token = $WxToken->createToken($data['uid'],$data['utype']);
            //第三方登录绑定
            if($bind_id !== NULL){
                $map_bind['id'] = $bind_id;
                $bind_data['uid'] = $data['uid'];
                $bind_data['is_bind'] = 1;
                $bind_data['bindingtime'] = time();
                $bind_save = M('MembersBind')->where($map_bind)->save($bind_data);
            }
            D('Members')->user_register($data);//积分、套餐、分配客服等初始化操作
            $this->_correlation($data);//同步登录
			/*
			******************chm    strat*********
			*/
			if((C('qscms_open_give_gift')==1) && (C('qscms_is_give_gift')==1) && (C('qscms_is_give_gift_value')<>'')){
				$is_give_gift_value = C('qscms_is_give_gift_value');
				$gift_id =explode(',',$is_give_gift_value);
				$issue_data['gift_type']=2;//1单发企业=专享优惠券 ； 2新用户开了送=新用户专享； 3活动批量发=活动专享
				$static_data['admin_id']=1;
				$static_data['gift_id']=implode(",",$gift_id);
				//$static_data['issue_id']=substr($str,0,-1);
				$static_data['uid']=$data['uid'];
				$static_data['addtime']=time();
				$static_id = M('GiftStatic')->add($static_data);
				$succ=0;$fals=0;$str="";
				$gift_issue = M('GiftIssue')->getField('id,issue_num');
				$max_issue_num_id = M('GiftIssue')->max('id');
				$max_issue_num = $gift_issue[$max_issue_num_id];
				$gifts = M("Gift")->getField('id,gift_name,price,setmeal_name,setmeal_id,effectivetime');
				foreach($gifts as $k=>$v){
					$id=$v['id'];
					$gift_arr[$id]=$v;
				}
				$now_issue_num_id=$max_issue_num_id+1;	
				foreach($gift_id as $keys=>$vals){
					$gift_where['id'] = $vals;
					$issue_data['gift_id']=$vals;
					$issue_data['gift_setmeal_id']=$gift_arr[$vals]['setmeal_id'];
					$issue_data['admin_id']=1;
					$issue_data['is_used']=2;
					$issue_data['addtime']=time();
					$issue_data['static_id']=$static_id;
					$issue_data['deadtime']=$issue_data['addtime']+$gift_arr[$vals]['effectivetime']*60*60*24;			
					if(strlen($now_issue_num_id)<10){
						$issue_num = $now_issue_num_id;
						$len = strlen($val['id']);
						for($i=8;$i>=$len;$i--){
							$issue_num = '0'.$issue_num;
						}
					}else{
						$issue_num = $now_issue_num_id;
					}
					$now_issue_num_id++;
					$issue_data['issue_num'] = $issue_num;
					$issue_data['uid']=$data['uid'];
					$insertid = M('GiftIssue')->add($issue_data);
					if($insertid){
						$succ++;
						$str.=$insertid.",";
					}else{
						$fals++;
					}
				}
				if($succ>0){
                    $user_info = D('Members')->find($uid);
					//站内信
                    $setsqlarr_pms['message'] = "恭喜您获得".$succ."张套餐优惠券！";
                    D('Pms')->write_pmsnotice($user_info['uid'], $user_info['username'], $setsqlarr_pms['message'],1);
					//sms
                    $sms = D('SmsConfig')->get_cache();
                    if ($sms['set_gift'] == 1) {
                        $send_sms = true;
                        if (C('qscms_company_sms') == 1) {
                            if ($user_info['sms_num'] == 0) {
                                $send_sms = false;
                            }
                        }
                        if ($send_sms == true) {
							$r = D('Sms')->sendSms('notice', array('mobile' => $user_info['mobile'], 'tpl' => 'set_gift', 'data' => array('username' => $user_info['username'],'succ' => $succ)));
                            if ($r === true) {
                                D('Members')->where(array('uid' => $uid))->setDec('sms_num');
                            }
                        }
                    }
					//微信
                    if(C('apply.Weixin')){
                        $map['uid'] = $data['uid'];		
						$remind = M('GiftIssue')->where($map)->order('addtime desc')->find();
						D('Weixin/TplMsg')->set_gift($val, '套餐优惠券', date('Y-m-d H:i',$remind['addtime']).'至'.date('Y-m-d H:i',$remind['deadtime']));
                    }
				}
			}
			/*
			******************chm    end*********
			*/
            if(!C('qscms_register_password_open')){
                $sendSms['tpl']='set_register_resume';
                $sendSms['data']=array('username'=>$data['username'].'','password'=>$data['password']);
                $sendSms['mobile']=$data['mobile'];
                D('Sms')->sendSms('captcha',$sendSms);
            }
            $this->ajaxReturn(200, '会员注册成功！', $token);
        }
    }

    // 注册发送短信/找回密码 短信
    public function reg_send_sms() {
        if ($uid = I('request.uid', 0, 'intval')) {
            $mobile = M('Members')->where(array('uid' => $uid))->getfield('mobile');
            !$mobile && $this->ajaxReturn(0, '用户不存在！');
        } else {
            $mobile = I('request.mobile','', 'trim');
            !$mobile && $this->ajaxReturn(0, '请填手机号码！');
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
        $sms_type = I('request.sms_type', 'reg', 'trim');
        $rand = getmobilecode();
        switch ($sms_type) {
            case 'reg':
                //验证手机号是否注册
                $map_mobile['mobile'] = $mobile;
                $only = M('Members')->where($map_mobile)->find();
                if($only){
                    $this->ajaxReturn(900, '您的手机号已注册,如不是本人注册,请联系管理员');
                }
                $sendSms['time'] = 180;
                $sendSms['tpl'] = 'set_register';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
            case 'gsou_reg':
                $sendSms['tpl'] = 'set_register';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
            case 'getpass':
                $sendSms['tpl'] = 'set_retrieve_password';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
            case 'login':
                if (!$uid = M('Members')->where(array('mobile' => $mobile))->getfield('uid')) {
                    // if (false === $sitegroup_user = $passport->uc('sitegroup')->get($smsVerify['mobile'], 'mobile')) {
                    //     $this->ajaxReturn(0, '您输入的手机号未注册会员');
                    // }
                    $this->ajaxReturn(900, '您输入的手机号未注册会员');
                }
                $sendSms['time'] = 120;
                $sendSms['tpl'] = 'set_login';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
        }

        $WxCode = A('WxCode');
        $smsVerify = $WxCode->createToken($sendSms['data']['rand'],$sendSms['time']);
        $sms = $WxCode->getToken();
        if($sms){
            $token = substr($sms,7);
            $check = $WxCode->checkToken($token);
            if($check == 'wait'){
                $this->ajaxReturn(0, '请等待'.$sendSms['time'].'秒');
            }else if($check == 'timeout'){
                $this->ajaxReturn(401, '验证码已过期');
            }
        }
        // $smsVerify = session($sms_type . '_smsVerify');
        // if ($smsVerify && $smsVerify['mobile'] == $mobile && time() < $smsVerify['time'] + 60) $this->ajaxReturn(0, '60秒内仅能获取一次短信验证码,请稍后重试');
        $sendSms['mobile'] = $mobile;
        if (true === $reg = D('Sms')->sendSms('captcha', $sendSms)) {
            // session($sms_type . '_smsVerify', array('rand' => substr(md5($rand), 8, 16), 'time' => time(), 'mobile' => $mobile));
            // session('_verify_num_check',null);
            $this->ajaxReturn(200, '手机验证码发送成功！',$smsVerify);
        } else {
            $this->ajaxReturn(0, $reg);
        }
    }
    

    protected function _getRandomString($len, $chars=null){
        if (is_null($chars)){
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        } 
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)]; 
        }
        return $str;
    }


    //生成二维码图片
    public function make_captcha(){
        // 设置验证码生成几位
        $num = 4;
        // 设置宽度
        $width = 120;
        // 设置高度
        $height = 45;
        //生成验证码，也可以用mt_rand(1000,9999)随机生成
        $Code = $this->_getRandomString(4);
        $WxCaptcha = A('WxCaptcha');
        $Verify = $WxCaptcha->createToken($Code,120);
        // 设置头部
        header("Content-type: image/png");
        // 创建图像（宽度,高度）
        $img = imagecreate(120,45);
        //创建颜色 （创建的图像，R，G，B）
        $GrayColor = imagecolorallocate($img,255,255,255);
        $BlackColor = imagecolorallocate($img, 0, 0, 0);
        $BrColor = imagecolorallocate($img,52,52,52);
        //填充背景（创建的图像，图像的坐标x，图像的坐标y，颜色值）
        imagefill($img,0,0,$GrayColor);
        //设置边框
        imagerectangle($img,0,0,$width-1,$height-1, $BrColor);
        //随机绘制两条虚线 五个黑色，五个淡灰色
        // $style = array ($BlackColor,$BlackColor,$BlackColor,$BlackColor,$BlackColor,$GrayColor,$GrayColor,$GrayColor,$GrayColor,$GrayColor);
        // imagesetstyle($img, $style);
        // imageline($img,0,mt_rand(0,$height),$width,mt_rand(0,$height),IMG_COLOR_STYLED);
        // imageline($img,0,mt_rand(0,$height),$width,mt_rand(0,$height),IMG_COLOR_STYLED);
        //随机生成干扰的点
        for ($i=0; $i < 200; $i++) {
            $PointColor = imagecolorallocate($img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($img,mt_rand(0,$width),mt_rand(0,$height),$PointColor);
        }
        //将验证码随机显示
        for ($i = 0; $i < $num; $i++) {
            $x = ($i*$width/$num)+mt_rand(5,12);
            $y = mt_rand(5,10);
            imagestring($img,7,$x,$y,substr($Code,$i,1),$BlackColor); 
        }

        if(!$img){
            $this->ajaxReturn(0,'验证码生成失败');
        }
        //保存路径
        $savePath = QSCMS_DATA_PATH.'upload/captcha/';
        //判断目录是否存在 不存在就创建
        is_dir($savePath) OR mkdir($savePath, 0777, true); 
        //保存图片
        $info = imagepng($img,QSCMS_DATA_PATH.'upload/captcha/'.$Code.'.png');

        if(!$info) {// 上传错误提示错误信息
            $this->ajaxReturn(400,'验证码生成失败',$info);
        }else{// 上传成功
            $imgname = $Code.'.png';
            $return['attach'] = attach($imgname,'captcha');
            $return['token'] = $Verify;
            $this->ajaxReturn(200,'验证码生成成功',$return);
        }
    }


}