<?php
namespace Applet\Controller;
use Common\Controller\BaseController;
class WxCaptchaController extends BaseController{

    private function WxTokenheader(){
        return [            
            "type" => "token",        
            "alg"  => "HS256"
        ];
    }


    /**
     * create payload
     * @param $code
     * @param $utype
     * @return array
     */
    private function payload($code,$time)
    {
        return [            
            "iss"       => "renqichao",            
            "iat"       => $_SERVER['REQUEST_TIME'],           
            "exp"       => $_SERVER['REQUEST_TIME'] + $time,            
            "code" => $code
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
    private function signature($WxTokenheader, $payload, $secret = 'Captcha')
    {
        return hash_hmac('sha256', $WxTokenheader.$payload, $secret);
    }


    /**
     * generate a token
     * @param $uid
     * @return string
     */
    public function createToken($code,$time)
    {
        $WxTokenheader = $this->encode($this->WxTokenheader());     
        $payload = $this->encode($this->payload($code, $time));        
        $signature = $this->signature($WxTokenheader, $payload);
        return $WxTokenheader . '.' .$payload . '.' . $signature;
    }

	/**
     * check a token
     * @param $jwt
     * @param string $key
     * @return array|string
     */
    public function checkToken($jwt, $key = 'Captcha'){
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
        if (time() > $payload['iat'] && time() < $payload['exp'])            
        	return 'wait';    
        if ($WxTokenheader['type'] != 'token' || $WxTokenheader['alg'] != 'HS256')            
        	return 'token invalid';               
        if (isset($payload['exp']) && $payload['exp'] < time())            
    		return 'timeout';      
		if($payload['iss'] !== 'renqichao')  
			return 'issuer error';  
        return [           
	        'code' => $payload['code'],            
	        'time' =>$payload['time']
        ];
    }

	/**
     * check a token
     * @param $jwt
     * @param string $key
     * @return array|string
     */
    public function regcheck($jwt,$vcode,$key = 'Captcha'){
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
        if (strtolower($payload['code']) !== strtolower($vcode))
        	return 'code error'; 
        if ($WxTokenheader['type'] != 'token' || $WxTokenheader['alg'] != 'HS256')            
        	return 'token invalid';               
        if (isset($payload['exp']) && $payload['exp'] < time())            
    		return 'timeout';
		if($payload['iss'] !== "renqichao")  
			return 'issuer error';  
        return [           
	        'code' => $payload['code'],            
	        'time' =>$payload['exp'] - $payload['iat']
        ];
    }


    /**
     * get a token
     * @return null
     */
    public function get_captcha_Token()
    {
        $token = null;        
        if (isset($_SERVER['HTTP_CAPTCHA']))            
        $token = $_SERVER['HTTP_CAPTCHA'];    
        return $token;
    }

}