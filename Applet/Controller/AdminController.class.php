<?php
namespace Applet\Controller;
use Common\Controller\ConfigbaseController;
class AdminController extends ConfigbaseController{
	public function _initialize() {
        parent::_initialize();
        $this->_mod = D('Config');
        $this->_name = 'Config';
    }
    //微信小程序基本信息修改
    public function index(){
        $this->_edit();
        $this->display();
    }

    //百度小程序设置修改保存
    public function baidu_index(){
        $this->_edit();
        $this->display();
    }

    //百度地图AK修改保存
    public function map_index(){
        $this->_edit();
        $this->display();
    }

    //小程序支付开关修改保存
    public function pay_index(){
        $this->_edit();
        $this->display();
    }

    // /**
    //  * 导航列表
    //  */
    // public function nav_list(){
    //     $list = D('NavigationWeixinapp')->order('ordid desc,id asc')->select();
    //     $this->assign('list',$list);
    //     $this->display();
    // }
    
    // /**
    //  * 全部保存
    //  */
    // public function nav_save_all(){
    //     $id = I('post.id');
    //     $show_name = I('post.show_name');
    //     $display = I('post.display');
    //     $ordid = I('post.ordid');
    //     if (is_array($id) && count($id)>0)
    //     {
    //         $model = D('NavigationWeixinapp');
    //         foreach($id as $k=>$v)
    //         {
    //             $setsqlarr['show_name']=trim($show_name[$k]);
    //             $setsqlarr['display']=intval($display[$k]);
    //             $setsqlarr['ordid']=intval($ordid[$k]);
    //             $model->where(array('id'=>array('eq',intval($id[$k]))))->save($setsqlarr);
    //         }
    //     }
    //     $this->returnMsg(1,'保存成功！');
    // }
    // /**
    //  * 修改导航
    //  */
    // public function nav_edit(){
    //     $this->_name = 'NavigationWeixinapp';
    //     parent::edit();
    // }

}
?>