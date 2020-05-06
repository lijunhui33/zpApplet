<?php
namespace Applet\Controller;
use Common\Controller\FrontendController;
use Applet\Controller\WxTokenController;
class WxJobsDetailsController extends WxTokenController{

    /**
     * 职位详情页面
     */
    public function jobs_details(){
        //职位详情查询
        $id = I('request.id','','trim');
        $map['职位id'] = $id;
        $class = new \Common\qscmstag\jobs_showTag($map);
        $jobs = $class->run();

        //查看简历头像查询
        $photo = M("Resume");
        $map['photo_img'] = array('NEQ','');
        $photo = $photo->where($map)->getField('uid,photo_img');
        $photolist = array();
        foreach ($photo as $key => $value) {
            $photolist[] = attach($value,'avatar');
        }
        if($jobs['click'] > 3){
            $cresult = array_rand($photolist,4);
            $photo = array();
            $photo[] = $photolist[$cresult[0]];
            $photo[] = $photolist[$cresult[1]];
            $photo[] = $photolist[$cresult[2]];
            $photo[] = $photolist[$cresult[3]];
            $resultlist[] = $photo;
        }else if($jobs['click'] == 3){
            $cresult = array_rand($photolist,3);
            $photo = array();
            $photo[] = $photolist[$cresult[0]];
            $photo[] = $photolist[$cresult[1]];
            $photo[] = $photolist[$cresult[2]];
        }else if($jobs['click'] == 2){
            $cresult = array_rand($photolist,2);
            $photo = array();
            $photo[] = $photolist[$cresult[0]];
            $photo[] = $photolist[$cresult[1]];
        }else if($jobs['click'] == 1){
            $cresult = array_rand($photolist);
            $photo = array();
            $photo[] = $photolist[$cresult];
        }else if($jobs['click'] == 0){
            $photo = array();
        }
        $returnlist = array();
        $returnlist['jobs_name'] = $jobs['jobs_name'];
        $negotiable = $array['negotiable'] = $jobs['negotiable'];
            if($negotiable == 1){
                $returnlist['wage_cn'] = '面议';
            }else{
                $returnlist['wage_cn'] = $jobs['wage_cn'];
        }
        $returnlist['id'] = $jobs['id'];
        $returnlist['uid'] = $jobs['uid'];
        $returnlist['district_cn'] = $jobs['district_cn'];
        $returnlist['experience_cn'] = $jobs['experience_cn'];
        $returnlist['education_cn'] = $jobs['education_cn'];
        $returnlist['tag_cn'] = $jobs['tag_cn'];
        $returnlist['click'] = $jobs['click'];
        $returnlist['photo'] = $photo;//consultant
        $returnlist['audit'] = $jobs['company']['audit'];//只有1的时候显示认证图片
        $returnlist['contents'] = $jobs['contents'];
        $returnlist['refreshtime'] = date('m-d',$jobs['refreshtime']);
        $returnlist['company_id'] = $jobs['company']['id'];
        $returnlist['companyname'] = $jobs['company']['companyname'];
        $returnlist['logo'] = $jobs['company']['logo'];
        $setmeal_id['id'] = $jobs['company']['setmeal_id'];
        $setmeal = M('Setmeal');
        $setmeal = $setmeal->where($setmeal_id)->getField('setmeal_img');
        $returnlist['setmeal_img'] = attach($setmeal,'setmeal_img');
        $returnlist['famous'] = $jobs['company']['famous'];
        $returnlist['nature_cn'] = $jobs['company']['nature_cn'];
        $returnlist['scale_cn'] = $jobs['company']['scale_cn'];
        $returnlist['trade_cn'] = $jobs['company']['trade_cn'];
        $returnlist['address'] = $jobs['company']['address'];
        $returnlist['map_open'] = $jobs['company']['map_open'];
        $returnlist['map_x'] = $jobs['company']['map_x'];
        $returnlist['map_y'] = $jobs['company']['map_y'];
        $returnlist['map_zoom'] = $jobs['company']['map_zoom'];
        //判断是否登录，小程序使用token，只能单独判断
        $WxToken = A('WxToken');
        $istoken = $WxToken->getToken();
        if($istoken){
             $token = $WxToken->_init_visitor();
             $uid = C('visitor.uid');
             //职位是否投递apply
             $condition['personal_uid'] = $uid;
             $condition['jobs_id'] = $id;
             $apply = M('PersonalJobsApply')->where($condition)->find();
                 if($apply){
                    $returnlist['is_apply'] = 1;
                 }else{
                    $returnlist['is_apply'] = 0;
                 }
             //简历是否收藏favorites
             $favorites = M('PersonalFavorites')->where($condition)->find();
                 if($favorites){
                    $returnlist['favor'] = 1;
                 }else{
                    $returnlist['favor'] = 0;
                 }
        }else{
            $returnlist['is_apply'] = $jobs['is_apply'];
            $returnlist['favor'] = $jobs['favor'];
        }
        //相似职位推荐
        $where['显示数目'] = 5;
        $where['职位分类'] = $jobs['jobcategory'];
        $where['去除id'] = $jobs['id'];
        $class = new \Common\qscmstag\jobs_listTag($where);
        $resemble = $class->run();

        $setmeal = M('Setmeal');
        $setmeal = $setmeal->getField('id,setmeal_img');

        $resemblelist = array();
        foreach ($resemble['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['uid'] = $value['uid'];
            $arr['jobs_name'] = $value['jobs_name'];
            $arr['companyname'] = $value['companyname'];
            $arr['company_id'] = $value['company_id'];
            $arr['emergency'] = $value['emergency'];
            $arr['district_cn'] = $value['district_cn'];  
            $arr['experience_cn'] = $value['experience_cn'];
            $arr['education_cn'] = $value['education_cn'];
            $negotiable = $array['negotiable'] = $value['negotiable'];
                if($negotiable == 1){
                    $arr['wage_cn'] = '面议';
                }else{
                    $arr['wage_cn'] = $value['minwage'].'-'.$value['maxwage'];
            }
            $arr['tag_cn'] = $value['tag_cn'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            $arr['audit'] = $value['audit'];
            $resemblelist[] = $arr;
        }
        //数据汇总
        // $result = ['jobsdetails'=>$returnlist,'resemblelist'=>$resemblelist];
        $result['jobsdetails'] = $returnlist;
        $result['resemblelist'] = $resemblelist;
        $this->ajaxReturn(200,'成功',$result);
    }

    /**
     * [ resume_apply 简历投递]
     */
    public function resume_apply(){
        $WxToken = A('WxToken');
        $token = $WxToken->_init_visitor();
        $jid = I('request.jid', '', 'intval');
        $rid = C('visitor.pid');
        if(!$rid){
            $this->ajaxReturn(0,'请先登录');
        }
        !$jid && $this->ajaxReturn(0,'请选择要投递的职位！');
        $this->_resume_apply_exe($jid,$rid);
    }

    protected function _resume_apply_exe($jid,$rid){
        $reg = D('PersonalJobsApply')->jobs_apply_add($jid,C('visitor'),$rid);
        if(!$reg['state'] && $reg['complete']){// 完整度不够
            $this->ajaxReturn(1,$reg['error'],array('complete'=>$reg['complete']));
        }
        !$reg['state'] && $this->ajaxReturn(1,$reg['error'],array('create'=>$reg['create']));
        $reg['data']['failure'] && $this->ajaxReturn(0,$reg['data']['list'][$jid]['tip']);
        $this->ajaxReturn(200,'投递成功！','success');
    }

    /**
     * 职位详情页面职位收藏
     */
    public function favoritejobs(){
        $WxToken = A('WxToken');
        $token = $WxToken->_init_visitor();
        $jid = I('request.id', '', 'intval');
        !$jid && $this->ajaxReturn(0, '请选择职位！');
        $uid  = C('visitor.uid');
        if(!$uid){
            $this->ajaxReturn(0,'请先登录');
        }
        $has  = D('PersonalFavorites')->where(array('jobs_id' => $jid, 'personal_uid' => $uid))->find();
        $user = D("members")->where(array("uid" => $uid))->find();
        if ($has) {
            D('PersonalFavorites')->where(array('jobs_id' => $jid, 'personal_uid' => $uid))->delete();
            $this->ajaxReturn(200, '取消收藏成功！', 'cancel');
        } else {
            $reg = D('PersonalFavorites')->add_favorites($jid, $user);
            !$reg['state'] && $this->ajaxReturn(0, $reg['error']);
            $this->ajaxReturn(200, '收藏成功！','success');
        }
    }

    /**
     * 举报职位
     */
    public function report_jobs(){
        $WxToken = A('WxToken');
        $token = $WxToken->_init_visitor();
        $jobs_id = I('request.jobs_id','','trim');
        if (!$jobs_id) {
            $this->ajaxReturn(0, '参数错误！');
        }
        $report_type = I('request.report_type', 1, 'intval');
        $content = I('request.content', '', 'trim');
        !$content && $this->ajaxReturn(0, '请填写备注说明！');
        $telephone = I('request.telephone', '', 'trim');
        !$telephone && $this->ajaxReturn(0, '请填写联系电话！');
        $data['jobs_id'] = $jobs_id;
        $data['report_type'] = $report_type;
        $data['telephone'] = $telephone;
        $data['content'] = $content;
        $r = D('Report')->add_report($data, C('visitor'));
        if($r['state'] == 1){
            $this->ajaxReturn(200, $r['msg']);
        }
        $this->ajaxReturn($r['state'], $r['msg']);
    }

    //简历投递后查询职位电话 
    public function telephone(){
        $WxToken = A('WxToken');
        $token = $WxToken->_init_visitor();
        $uid = C('visitor.uid');
        $jobs_id = I('request.jobs_id','','trim');
        !$jobs_id && $this->ajaxReturn(0, '请选择职位！');
        //职位是否投递apply
        $condition['personal_uid'] = $uid;
        $condition['jobs_id'] = $jobs_id;
        $apply = M('PersonalJobsApply')->where($condition)->find();
        if($apply){
            $jobs_map['职位id'] = $jobs_id;
            $class = new \Common\qscmstag\jobs_showTag($jobs_map);
            $jobs = $class->run();
            $tel_show = $jobs['contact']['telephone_show'];
            if($tel_show == 1){
                $return['telephone'] = $jobs['contact']['telephone'];
                $this->ajaxReturn(200,'电话查询成功',$return);
            }else{
                $this->ajaxReturn(400,'对方电话保密，暂时不能拨打电话');
            }
        }else{
            $this->ajaxReturn(400,'请投递简历');
        } 
    }
    
}