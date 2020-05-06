<?php
namespace Applet\Controller;

/**
 * 对微信小程序用户加密数据的解密示例代码.
 *
 * @copyright Copyright (c) 1998-2014 Tencent Inc.
 */





class WxBizDataCryptController
{
    protected $appid;
	protected $sessionKey;
	protected $error;

	public function get_error()
	{
		return $this->error;
	}	


	/**
	 * 构造函数
	 * @param $sessionKey string 用户在小程序登录后获取的会话密钥
	 * @param $appid string 小程序的appid
	 */
	public function __construct( $appid, $sessionKey)
	{
		$this->sessionKey = $sessionKey;
		$this->appid = $appid;
	}


	/**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
     *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function decryptData( $encryptedData, $iv, &$data)
	{
		if (strlen($this->sessionKey) != 24) {
			$this->error = '-41001,encodingAesKey 非法';
			return false;
		}
		$aesKey=base64_decode($this->sessionKey);

		if (strlen($iv) != 24) {
			$this->error = '-41002,encodingAesKey 非法';
			return false;
		}
		$aesIV=base64_decode($iv);
		$aesCipher=base64_decode($encryptedData);
		$result=openssl_decrypt($aesCipher,"AES-128-CBC",$aesKey,1,$aesIV);
		$dataObj=json_decode( $result );
		if($dataObj  == NULL)
		{
			$this->error = '-41003,aes 解密失败';
			return false;
		}
		if( $dataObj->watermark->appid != $this->appid )
		{
			$this->error = '-41003,aes 解密失败';
			return false;
		}
		$data = $result;
		return $data;
	}

}