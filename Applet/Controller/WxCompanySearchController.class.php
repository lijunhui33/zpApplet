<?php
namespace Applet\Controller;
use Common\Controller\FrontendController;
class WxCompanySearchController extends FrontendController{

	 /**
     * 名企推荐列表页
     */
    public function search_company_list(){
        $map['分页显示'] = I('request.page','','trim');
        $map['显示数目'] = 5;
        $map['关键字'] = I('request.key','','trim');
        $map['地区分类'] = I('request.citycategory','','trim');
        $map['行业'] = I('request.trade','','trim');
        $map['企业性质'] = I('request.nature','','trim');
        $map['企业规模'] = I('request.scale','','trim');
        $class = new \Common\qscmstag\company_listTag($map);
        $companys = $class->run();

        $returnlist = array();
        $where['status'] = 1;

        if(C('apply.Report')){
            $report = M('CompanyReport')->where($where)->getField('com_id,id');
        }else{
            $report = array();
        }

        foreach ($companys['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['uid'] = $value['uid'];
            $arr['companyname'] = $value['companyname'];
            $arr['logo'] = $value['logo'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            if(isset($report[$value['id']]) && $report[$value['id']]){
                $arr['report'] = 1;
            }else{
                $arr['report'] = 0;
            }
            $arr['audit'] = $value['audit'];
            $arr['nature_cn'] = $value['nature_cn'];
            $arr['scale_cn'] = $value['scale_cn'];
            $arr['trade_cn'] = $value['trade_cn'];
            $arr['tag'] = $value['tag'];
            $arr['jobs'] = $value['jobs']['jobs_name'];
            $arr['jobs_count'] = $value['jobs_count'];
            $returnlist[] = $arr;
        }
        $return['returnlist']=$returnlist;
        $return['default_district'] = C('qscms_default_district');
        $return['default_district_spell'] = C('qscms_default_district_spell');
        $return['nowPage'] = $companys['page_params']['nowPage'];
        $return['totalPages'] = $companys['page_params']['totalPages'];
        $return['isfull'] = $companys['page_params']['isfull'];
        $this->ajaxReturn(200,'成功',$return);
    }

    // /**
    //  * 企业搜索条件(地区)
    //  */
    // public function search_company_region(){
    //     if(false === $region = F('district_custom_cate')) $region = D('CategoryDistrict')->custom_district_cache();

    //     $this->ajaxReturn(200,'成功',$region);
    // }

    /**
     * 企业搜索条件(行业)
     */
    public function search_company_position(){
        $class = new \Common\qscmstag\classifyTag();
        $trade = $class->run();
        $QS_trade = $trade['QS_trade'];
        $this->ajaxReturn(200,'成功',$QS_trade);
    }

    /**
     * 企业搜索条件(性质)
     */
    public function search_company_nature(){
        //学历要求
        $class = new \Common\qscmstag\classifyTag();
        $nature = $class->run();
        $QS_company_type = $nature['QS_company_type'];

        $this->ajaxReturn(200,'成功',$QS_company_type);
    }

    /**
     * 企业搜索条件(规模)
     */
    public function search_company_scale(){
        //学历要求
        $class = new \Common\qscmstag\classifyTag();
        $scale = $class->run();
        $QS_scale = $scale['QS_scale'];

        $this->ajaxReturn(200,'成功',$QS_scale);
    }

}