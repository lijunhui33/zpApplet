<?php
namespace Applet\Controller;
use Common\Controller\BaseController;
class WxTokenController extends BaseController{

    public function _initialize() {
        parent::_initialize();
    }

    private function WxTokenheader(){
        return [            
            "type" => "token",        
            "alg"  => "HS256"
        ];
    }

    /**
     * create payload
     * @param $uid
     * @param $utype
     * @return array
     */
    private function payload($uid, $utype)
    {
        return [            
            "iss"       => C('qscms_site_domain'),         
            "iat"       => $_SERVER['REQUEST_TIME'],           
            "exp"       => $_SERVER['REQUEST_TIME'] + 60*60*24,            
            "uid" => $uid,            
            "utype"  => $utype
        ];
    }

     /**
     * encode data
     * @param $data
     * @return string
     */
    private function encode($data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * generate a signature
     * @param WxTokenheader
     * @param $payload
     * @param string $secret
     * @return string
     */
    private function signature($WxTokenheader, $payload, $secret = 'secret')
    {
        return hash_hmac('sha256', $WxTokenheader.$payload, $secret);
    }
    
    /**
     * generate a token
     * @param $uid
     * @param $utype
     * @return string
     */
    public function createToken($uid, $utype)
    {
        $WxTokenheader = $this->encode($this->WxTokenheader());     
        $payload = $this->encode($this->payload($uid, $utype));        
        $signature = $this->signature($WxTokenheader, $payload); 
        return $WxTokenheader . '.' .$payload . '.' . $signature;
    }

	/**
     * check a token
     * @param $jwt
     * @param string $key
     * @return array|string
     */
    public function checkToken($jwt, $key = 'secret')
    {
        $access_token = explode('.', $jwt);         
        if (count($access_token) != 3)       
        	return 'token invalid';        
        $WxTokenheader64 = $access_token[0];
        $payload64 = $access_token[1]; 
        $sign = $access_token[2];
        if ($this->signature($WxTokenheader64,$payload64) !== $sign)            
        	return 'token invalid';        
        $WxTokenheader = json_decode(base64_decode($WxTokenheader64), JSON_OBJECT_AS_ARRAY);
        $payload = json_decode(base64_decode($payload64), JSON_OBJECT_AS_ARRAY);   
        if ($WxTokenheader['type'] != 'token' || $WxTokenheader['alg'] != 'HS256')            
        	return 'token invalid';               
        if (isset($payload['exp']) && $payload['exp'] < time())            
        	return 'timeout';
        if($payload['iss'] != C('qscms_site_domain'))  
            return 'issuer error';       
        return [           
	        'uid' => $payload['uid'],            
	        'utype' =>$payload['utype']
        ];
    }

	/**
     * get a token
     * @return null
     */
    public function getToken()
    {
        $token = null;        
        if (isset($_SERVER['HTTP_AUTHORIZATION']))            
        $token = $_SERVER['HTTP_AUTHORIZATION'];    
        return $token;
    }

    /**
     * [_correlation 用户注册相关]
     */
    protected function _correlation($data, $continue = true) {
        if (false === $this->visitor->login($data['uid'])) {
            if ($continue) {
                IS_AJAX && $this->ajaxReturn(0, $this->visitor->getError());
                $this->error($this->visitor->getError(), 'register');
            }
            return false;
        }
        //同步登陆
        $this->_user_server()->synlogin($data['uid']);
    }
    
    /**
     * 初始化访问者
     */
    protected function _init_visitor(){
        $bearer = $this->getToken();
        $token = substr($bearer,7);
        $uid = $this->checkToken($token);
        if($uid == 'token invalid'){
            $this->ajaxReturn(401,'身份验证失败');
        }
        if($uid == 'timeout'){
            $this->ajaxReturn(402,'登录已超时');
        }
        if($uid == 'issuer error'){
            $this->ajaxReturn(403,'发行商验证失败');
        }
        $field = 'uid,utype,username,email,mobile,password,last_login_ip,terminal,last_login_time,registration_id,sms_num,status,avatars,consultant';
        $visitor = M('Members')->field($field)->where(array('uid'=>$uid['uid']))->find();
        $user_info = D('Resume')->where(array('uid' => $uid['uid'], 'def' => 1))->find();
            $avatar_default = $user_info['sex'] == 1 ? 'no_photo_male.png' : 'no_photo_female.png';
            if ($visitor['avatars']) {
                $visitor['avatar'] = $visitor['avatars'];
                $visitor['avatars'] = attach($visitor['avatars'], 'avatar');
                $visitor['is_avatars'] = 1;
            } else {
                $visitor['avatars'] = attach($avatar_default, 'resource');
                $visitor['is_avatars'] = 0;
            }
            $visitor['pid'] = $user_info['id'];
            $visitor['major'] = $user_info['major'];
            $visitor['marriage'] = $user_info['marriage'];
            $visitor['householdaddress'] = $user_info['householdaddress'];
            $visitor['residence'] = $user_info['residence'];
            $visitor['fullname'] = $user_info['fullname'];
            $visitor['complete_percent'] = $user_info['complete_percent'];
            $visitor['level'] = $user_info['level'];
            $visitor['points'] = D('MembersPoints')->get_user_points($uid['uid']);
            $issign = D('MembersHandsel')->check_members_handsel_day(array('uid' => $uid['uid'], 'htype' => 'task_sign'));
            $visitor['issign'] = $issign ? 1 : 0;
            $visitor['utype'] = $visitor['utype'];
            C('visitor',$visitor);
    }   

}