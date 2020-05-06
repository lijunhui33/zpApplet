<?php
namespace Applet\Controller;
class BaiduSubscribeController extends WxTokenController{
    public function _initialize() {
        parent::_initialize();
        parent::_init_visitor();
    }

	//判断是否添加过订阅
    public function have_subscribe(){
    	$uid = C('visitor.uid');
    	$map['uid'] = $uid;
    	$subscribe = M('BaiduSubscribe')->where($map)->find();
    	if($subscribe){
            $subscribe = 1;
    		$this->ajaxReturn(200, '已添加订阅信息',$subscribe);
    	}else{
            $subscribe = 0;
    		$this->ajaxReturn(0, '暂未添加订阅信息' ,$subscribe);
    	}
    }

    //订阅模板查询
    public function baiduapp_template(){
        // $map_template['type'] = 'Applet';
        // $map_template['name'] = 'baidu_template_id';
        // $template = M('config')->where($map_template)->getField('value');
        $template = C('qscms_baidu_template_id');
        $this->ajaxReturn(200, '模板ID查询成功' ,$template);
    }

    //添加订阅
    public function subscribe_add(){
        //判断后台是否添加模板ID
        // $map_config['name'] = 'baidu_template_id';
        // $map_config['type'] = 'Applet';
        // $config = M('Config')->where($map_config)->find();
        $config = C('qscms_baidu_template_id');
        if(!$config){
            $this->ajaxReturn(0, '订阅功能暂未开启');
        }
        //获取百度openid
        // $map_appid['type'] = 'Applet';
        // $map_appid['name'] = 'baidu_appkey';
        // $appkey = M('Config')->where($map_appid)->getField('value');
        $appkey = C('qscms_baidu_appkey');

        // $map_appsecret['type'] = 'Applet';
        // $map_appsecret['name'] = 'baidu_appsecret';
        // $appsecret = M('Config')->where($map_appsecret)->getField('value');
        $appsecret = C('qscms_baidu_appsecret');
        $code = I('request.code','',"trim");
        $url = 'https://spapi.baidu.com/oauth/jscode2sessionkey?client_id=' . $appkey . '&sk=' . $appsecret . '&code=' . $code;
        $result   = https_request($url);
        $jsoninfo = json_decode($result,true);
        $open_id = $jsoninfo['openid'];
        //获取百度scene_id
        $scene_id = I('request.scene_id','',"trim");
        $scene_id = $scene_id;

        //保存订阅信息
        $intention_jobs_id    = I('request.intention_jobs_id', '', 'intval');
        $intention_jobs    = I('request.intention_jobs', '', 'trim');
        $wage  = I('request.wage', '', 'intval');
        $wage_cn = I('request.wage_cn', '', 'trim');
        $district    = I('request.district', '', 'intval');
        $district_cn = I('request.district_cn', '', 'trim');

        $data['uid'] = C('visitor.uid');
        $data['intention_jobs_id']    = $intention_jobs_id;
        $data['intention_jobs']    = $intention_jobs;
        $data['wage']    = $wage;
        $data['wage_cn'] = $wage_cn;
        $data['district'] = $district;
        $data['district_cn'] = $district_cn;
        $data['open_id']    = $open_id;
        $data['scene_id']    = $scene_id;
        $data['url'] = 'pages/index/job?intention_jobs_id='.$intention_jobs_id.'&wage='.$wage.'&district='.$district.'';
        $map['uid'] = $data['uid'];
        $only = M('BaiduSubscribe')->where($map)->find();
        if($only){
            $this->ajaxReturn(400, '您已经添加过订阅信息');
        }
        $subscribe = M('BaiduSubscribe')->add($data);
        if($subscribe){
            // $map_template_id['type'] = 'Baiduapp';
            // $map_template_id['name'] = 'baidu_template_id';
            // $baiduapp_template_id = M('Config')->where($map_template_id)->getField('value');
            $baiduapp_template_id = C('qscms_baidu_template_id');
        }
        $this->ajaxReturn(200, '保存成功', $baiduapp_template_id);
    }

    //订阅修改
    public function subscribe_edit(){
        //判断后台是否添加模板ID
        // $map_config['name'] = 'baidu_template_id';
        // $map_config['type'] = 'Applet';
        // $config = M('Config')->where($map_config)->find();
        $config = C('qscms_baidu_template_id');
        if(!$config){
            $this->ajaxReturn(0, '订阅功能暂未开启');
        }

        //获取百度openid
        // $map_appid['type'] = 'Applet';
        // $map_appid['name'] = 'baidu_appkey';
        // $appkey = M('Config')->where($map_appid)->getField('value');
        $appkey = C('qscms_baidu_appkey');

        // $map_appsecret['type'] = 'Applet';
        // $map_appsecret['name'] = 'baidu_appsecret';
        // $appsecret = M('Config')->where($map_appsecret)->getField('value');
        $appsecret = C('qscms_baidu_appsecret');
        $code = I('request.code','',"trim");
        $url = 'https://spapi.baidu.com/oauth/jscode2sessionkey?client_id=' . $appkey . '&sk=' . $appsecret . '&code=' . $code;
        $result   = https_request($url);
        $jsoninfo = json_decode($result,true);
        $open_id = $jsoninfo['openid'];

        //获取百度scene_id
        $scene_id = I('request.scene_id','',"trim");
        $scene_id = $scene_id;

        //保存订阅信息
        $intention_jobs_id    = I('request.intention_jobs_id', '', 'intval');
        $intention_jobs    = I('request.intention_jobs', '', 'trim');
        $wage  = I('request.wage', '', 'intval');
        $wage_cn = I('request.wage_cn', '', 'trim');
        $district    = I('request.district', '', 'intval');
        $district_cn = I('request.district_cn', '', 'trim');

        $open_id = $open_id;
        $scene_id = $scene_id;

        $data['uid'] = C('visitor.uid');
        $data['intention_jobs_id']    = $intention_jobs_id;
        $data['intention_jobs']    = $intention_jobs;
        $data['wage']    = $wage;
        $data['wage_cn'] = $wage_cn;
        $data['district'] = $district;
        $data['district_cn'] = $district_cn;
        $data['open_id']    = $open_id;
        $data['scene_id']    = $scene_id;
        $data['url'] = 'pages/index/job?intention_jobs_id='.$intention_jobs_id.'&wage='.$wage.'&district='.$district.'';

        $subscribe = M('BaiduSubscribe');
        $map['uid'] = C('visitor.uid');
        $subscribe = M('BaiduSubscribe')->where($map)->save($data);
        if($subscribe){
            // $map_template_id['type'] = 'Applet';
            // $map_template_id['name'] = 'baidu_template_id';
            // $baiduapp_template_id = M('Config')->where($map_template_id)->getField('value');
            $baiduapp_template_id = C('qscms_baidu_template_id');
        }
        $this->ajaxReturn(200, '修改成功', $baiduapp_template_id);
    }

    //订阅删除
    public function subscribe_del(){
        $map['uid'] = C('visitor.uid');
        $subscribe_del = M('BaiduSubscribe')->where($map)->delete();
        $this->ajaxReturn(200, '删除成功', $subscribe_del);
    }

    // signature 计算方法
    // function getSignature($swan_id){
    //     $map_key['type'] = 'Baiduapp';
    //     $map_key['name'] = 'baidu_appkey';
    //     $map_secret['type'] = 'Baiduapp';
    //     $map_secret['name'] = 'baidu_appsecret';
    //     $swan_id = $swan_id;
    //     $app_key = M('Config')->where($map_key)->getField('value');
    //     $app_secret = M('Config')->where($map_secret)->getField('value');
    //     $splicing = 'appkey='.$app_key.'secret_key='.$app_secret.'swanid='.$swan_id;
    //     $calculation = md5($splicing);
    //     return $calculation;
    // }

    //订阅查看
    public function subscribe_query(){
        $uid = C('visitor.uid');
        $map['uid'] = $uid;
        $subscribe = M('BaiduSubscribe')->where($map)->select();
        $this->ajaxReturn(200, '查询成功', $subscribe);
    }

}

 