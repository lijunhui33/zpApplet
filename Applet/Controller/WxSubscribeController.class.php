<?php
namespace Applet\Controller;
use Applet\Controller\WxTokenController;
use Applet\Controller\WxBizDataCrypt;
class WxSubscribeController extends WxTokenController{
    public function _initialize() {
        parent::_initialize();
        parent::_init_visitor();
    }

    //微信判断是否添加过订阅
    public function have_subscribe(){
    	$uid = C('visitor.uid');
    	$map['uid'] = $uid;
    	$subscribe = M('WeixinSubscribe')->where($map)->find();
    	if($subscribe){
            $subscribe = 1;
    		$this->ajaxReturn(200, '已添加订阅信息',$subscribe);
    	}else{
            $subscribe = 0;
    		$this->ajaxReturn(0, '暂未添加订阅信息' ,$subscribe);
    	}

    }

    //微信订阅模板查询
    public function weixinapp_template(){
        // $map_template['type'] = 'Applet';
        // $map_template['name'] = 'weixinapp_template_id';
        // $template = M('config')->where($map_template)->getField('value');
        $template = C('qscms_weixinapp_template_id');
        $this->ajaxReturn(200, '模板ID查询成功' ,$template);
    }

    //微信添加订阅
    public function subscribe_add(){
        //判断后台是否添加模板ID
        // $map_config['name'] = 'weixinapp_template_id';
        // $map_config['type'] = 'Applet';
        // $config = M('Config')->where($map_config)->find();
        $config = C('qscms_weixinapp_template_id');
        if(!$config){
            $this->ajaxReturn(0, '订阅功能暂未开启');
        }
        //获取微信openid
        $code = I('request.code','',"trim");

        // $map_appid['type'] = 'Applet';
        // $map_appid['name'] = 'weixinapp_appid';
        // $weixinapp_appid = M('Config')->where($map_appid)->getField('value');
        $weixinapp_appid = C('qscms_weixinapp_appid');

        // $map_appsecret['type'] = 'Applet';
        // $map_appsecret['name'] = 'weixinapp_appsecret';
        // $weixinapp_appsecret = M('Config')->where($map_appsecret)->getField('value');
        $weixinapp_appsecret = C('qscms_weixinapp_appsecret');

        $url      = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $weixinapp_appid . '&secret=' . $weixinapp_appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
        $result   = https_request($url);
        $jsoninfo = json_decode($result, true);
        $openid   = $jsoninfo['openid'];
        //保存订阅信息
        $intention_jobs_id    = I('request.intention_jobs_id', '', 'intval');
        $intention_jobs    = I('request.intention_jobs', '', 'trim');
        $wage  = I('request.wage', '', 'intval');
        $wage_cn = I('request.wage_cn', '', 'trim');
        $district    = I('request.district', '', 'intval');
        $district_cn = I('request.district_cn', '', 'trim');
        $openid    = $openid;

        $data['uid'] = C('visitor.uid');
        $data['intention_jobs_id']    = $intention_jobs_id;
        $data['intention_jobs']    = $intention_jobs;
        $data['wage']    = $wage;
        $data['wage_cn'] = $wage_cn;
        $data['district'] = $district;
        $data['district_cn'] = $district_cn;
        $data['openid']    = $openid;
        $data['url'] = 'pages/index/job?intention_jobs_id='.$intention_jobs_id.'&wage='.$wage.'&district='.$district.'';

        $map['uid'] = $data['uid'];
        $only = M('WeixinSubscribe')->where($map)->find();
        if($only){
            $this->ajaxReturn(400, '您已经添加过订阅信息');
        }
        $subscribe = M('WeixinSubscribe')->add($data);
        if($subscribe){
            // $map_template_id['type'] = 'Applet';
            // $map_template_id['name'] = 'weixinapp_template_id';
            // $weixinapp_template_id = M('Config')->where($map_template_id)->getField('value');
            $weixinapp_template_id = C('qscms_weixinapp_template_id');
        }
        $this->ajaxReturn(200, '保存成功', $weixinapp_template_id);
    }

    //微信订阅查看
    public function subscribe_query(){
        $uid = C('visitor.uid');
        $map['uid'] = $uid;
        $subscribe = M('WeixinSubscribe')->where($map)->select();
        $this->ajaxReturn(200, '查询成功', $subscribe);
    }

    //微信订阅修改
    public function subscribe_edit(){
        //判断后台是否添加模板ID
        // $map_config['name'] = 'weixinapp_template_id';
        // $map_config['type'] = 'Applet';
        // $config = M('Config')->where($map_config)->find();
        $config = C('qscms_weixinapp_template_id');
        if(!$config){
            $this->ajaxReturn(0, '订阅功能暂未开启');
        }
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
        $data['url'] = 'pages/index/job?intention_jobs_id='.$intention_jobs_id.'&wage='.$wage.'&district='.$district.'';

        $subscribe = M('WeixinSubscribe');
        $map['uid'] = C('visitor.uid');
        $subscribe = M('WeixinSubscribe')->where($map)->save($data);
        if($subscribe){
            // $map_template_id['type'] = 'Applet';
            // $map_template_id['name'] = 'weixinapp_template_id';
            // $weixinapp_template_id = M('Config')->where($map_template_id)->getField('value');
            $weixinapp_template_id = C('qscms_weixinapp_template_id');
        }
        $this->ajaxReturn(200, '修改成功', $weixinapp_template_id);
    }

    //微信订阅删除
    public function subscribe_del(){
        $map['uid'] = C('visitor.uid');
        $subscribe_del = M('WeixinSubscribe')->where($map)->delete();
        $this->ajaxReturn(200, '删除成功', $subscribe_del);
    }

    //微信修改订阅推送授权ID
    public function authorize(){
        $id = I('request.id','',"trim");
        $uid = C('visitor.uid');
        $map['id'] = $id;
        $map['uid'] = $uid;

        $data['authorize'] = 1;
        $authorize = M('WeixinSubscribe')->where($map)->save($data);
        $this->ajaxReturn(200, '修改成功', $authorize);
    }
}