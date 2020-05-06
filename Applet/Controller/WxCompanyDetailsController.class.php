<?php
namespace Applet\Controller;
use Common\Controller\FrontendController;
class WxCompanyDetailsController extends FrontendController{

    /**
     * 企业详情页面
     */
    public function companys_details(){
        //企业详情查询
        $id = I('request.id','','trim');
        $map['企业id'] = $id;
        $class = new \Common\qscmstag\company_showTag($map);
        $companys = $class->run();
        
        $img = $companys['img'];
        $imglist = [];
        foreach ($img as $key => $value) {
            $arr['img'] = $value['img'];
            $imglist[] = $arr;
        }

        $returnlist = array();
        $returnlist['companyname'] = $companys['companyname'];
        $returnlist['logo'] = $companys['logo'];
        $returnlist['setmeal_id'] = $companys['setmeal_id'];
        $returnlist['audit'] = $companys['audit'];
        $data['com_id'] = $id;
        $data['status'] = 1;
        if(C('apply.Report')){
            $report = M('CompanyReport')->where($data)->find();
            if($report){
                $returnlist['report'] = 1;
            }else{
                $returnlist['report'] = 0;
            }
        }else{
            $returnlist['report'] = 0;
        }
        $returnlist['nature_cn'] = $companys['nature_cn'];
        $returnlist['scale_cn'] = $companys['scale_cn'];
        $returnlist['trade_cn'] = $companys['trade_cn'];
        $returnlist['tag_arrs'] = $companys['tag_arrs'];
        $returnlist['contents'] = $companys['contents'];
        $returnlist['img'] = $imglist;
        $returnlist['address'] = $companys['address'];
        $returnlist['map_open'] = $companys['map_open'];
        $returnlist['map_x'] = $companys['map_x'];
        $returnlist['map_y'] = $companys['map_y'];
        $returnlist['map_zoom'] = $companys['map_zoom'];

        //在招职位
        $where['会员uid'] = $companys['uid'];
        $class = new \Common\qscmstag\jobs_listTag($where);
        $otherjobs = $class->run();

        $otherjobslist = array();
        foreach ($otherjobs['list'] as $key => $value) {
            $array['id'] = $value['id'];
            $array['uid'] = $value['uid'];
            $array['jobs_name'] = $value['jobs_name'];
            $array['companyname'] = $value['companyname'];
            $array['company_id'] = $value['company_id'];
            $array['district_cn'] = $value['district_cn'];  
            $array['experience_cn'] = $value['experience_cn'];
            $array['education_cn'] = $value['education_cn'];
            $negotiable = $arry['negotiable'] = $value['negotiable'];
            if($negotiable == 1){
                $array['wage'] = '面议';
            }else{
                $array['wage'] = $value['minwage'].'-'.$value['maxwage'];
            }
            $array['tag_cn'] = $value['tag_cn'];
            $array['famous'] = $value['famous'];
            $otherjobslist[] = $array;
        }

        //统计企业下在招职位个数
        $jobs_count = count($otherjobslist);
        
        //数据汇总
        $result['companysdetails'] = $returnlist;
        $result['jobs_count'] = $jobs_count;
        $result['otherjobslist'] = $otherjobslist;
        // $result = ['companysdetails'=>$returnlist,'jobs_count'=>$jobs_count,'otherjobslist'=>$otherjobslist];
        $this->ajaxReturn(200,'成功',$result);
    }

    /**
     * 企业认证详细信息
     */
    public function company_report(){
        if(!C('apply.Report')) $this->ajaxReturn(200,'暂未安装认证插件',array());
        $report = M('CompanyReport');
        $map['com_id'] = I('request.com_id','','trim');
        $map['status'] = 1;
        $report = $report->where($map)->find();

        $returnlist = array();
        $returnlist['addtime'] = date('Y-m-d',$report['addtime']);
        $returnlist['com_name'] = $report['com_name'];
        $returnlist['corporate'] = $report['corporate'];
        $returnlist['com_type'] = $report['com_type'];
        $returnlist['reg_time'] = date('Y-m-d',$report['reg_time']);
        $returnlist['reg_capital'] = $report['reg_capital'];
        $returnlist['reg_address'] = $report['reg_address'];
        $returnlist['office_address'] = $report['office_address'];
        $returnlist['registrar'] = $report['registrar'];
        $returnlist['scope'] = $report['scope'];
        $returnlist['office_area'] = $report['office_area'];
        $returnlist['office_env'] = $report['office_env'];//（1一般2良好3优美）
        $returnlist['workplace'] = $report['workplace'];
        $returnlist['number'] = $report['number'];
        $returnlist['sex_ratio'] = $report['sex_ratio'];
        $returnlist['average_age'] = $report['average_age'];
        $returnlist['route'] = $report['route'];
        $returnlist['img'] = attach($report['img'],'images');
        $returnlist['evaluation'] = $report['evaluation'];
        $returnlist['certifier'] = $report['certifier'];

        $this->ajaxReturn(200,'成功',$returnlist);
    }

}