<?php
namespace Applet\Controller;
use Common\Controller\FrontendController;
class WxIndexController extends FrontendController{

    /*
    获取网站配置
    */
    public function web_cfg(){
        $list = C();
        foreach ($list as $key => $val) {
            if (strpos($key, 'QSCMS_') !== false) {
                $arr[strtolower($key)] = $val;
            }
        }
        // $arr['backgroundColor'] = C('qscms_weixinapp_top_color') ? C('qscms_weixinapp_top_color') : "#0180CF";
        // $arr['fontColor']       = "#ffffff";
        // $arr['recommend']       = C('qscms_weixinapp_index_login_recommend'); //千人千面
        // $arr['index_jobstype']  = C('qscms_weixinapp_index_jobs_type'); //new,nearby,allowance
        // $arr['logo_home_url'] = attach(C('qscms_logo_home'), 'resource');
        $return = $arr['qscms_site_name'];
        $this->ajaxReturn(200, '获取数据成功', $return);
    }


    /**
     * 个人首页职位列表(急聘)
     */
    public function hot_job_list(){
        $map['紧急招聘'] = 1;
        $map['显示数目'] = 5;
        $class = new \Common\qscmstag\jobs_listTag($map);
        $jobs = $class->run();

        $returnlist = array();
        foreach ($jobs['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['uid'] = $value['uid'];
            $arr['jobs_name'] = $value['jobs_name'];
            $arr['companyname'] = $value['companyname'];
            $arr['company_id'] = $value['company_id'];
            $arr['emergency'] = $value['emergency'];
            $arr['district_cn'] = $value['district_cn'];  
            $arr['experience_cn'] = $value['experience_cn'];
            $arr['education_cn'] = $value['education_cn'];
            $arr['wage_cn'] = $value['wage_cn'];
            $arr['tag_cn'] = $value['tag_cn'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            $arr['audit'] = $value['audit'];
            $returnlist[] = $arr;
        }
        $this->ajaxReturn(200,'成功',$returnlist);
    }
 
    /**
     * 个人首页职位列表(最新)
     */
    public function newest_job_list(){
        $map['显示数目'] = 5;
        $class = new \Common\qscmstag\jobs_listTag($map);
        $jobs = $class->run();

        $returnlist = array();
        foreach ($jobs['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['uid'] = $value['uid'];
            $arr['jobs_name'] = $value['jobs_name'];
            $arr['companyname'] = $value['companyname'];
            $arr['company_id'] = $value['company_id'];
            $arr['emergency'] = $value['emergency'];
            $arr['district_cn'] = $value['district_cn'];  
            $arr['experience_cn'] = $value['experience_cn'];
            $arr['education_cn'] = $value['education_cn'];
            $arr['wage_cn'] = $value['wage_cn'];
            $arr['tag_cn'] = $value['tag_cn'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            $arr['audit'] = $value['audit'];
            $returnlist[] = $arr;
        }
        $this->ajaxReturn(200,'成功',$returnlist);
    }

    /**
     * 首页轮播广告
     */
    public function index_ad(){
        $Ad = M('AdWeixinapp');
        $Ad = $Ad->select();
        $return = array();
        foreach ($Ad as $key => $value) {
            $arr['starttime'] = $value['starttime'];
            $arr['deadline'] = $value['deadline'];
            $arr['is_display'] = $value['is_display'];
            if($arr['is_display'] == 1 && $arr['starttime']<time() && $arr['deadline']>time() || $arr['deadline'] == 0){
               $array['content'] = attach($value['content'],'attach_img'); 
               $return[] = $array;
            }
        }
        $this->ajaxReturn(200,'成功',$return);
    }

    /**
     * 个人首页同城职位
     */
    public function nearby_jobs_list(){
        $map['分页显示'] = I('request.page','','trim');
        $map['显示数目'] = 5;
        $lng = I('request.lng','0','trim,badword');
        $lat = I('request.lat','0','trim,badword');
        $map['经度'] = $lng;//112.732929
        $map['纬度'] = $lat;//37.714684
        $map['搜索范围'] = 10;
        $map['关键字'] = I('request.key','','trim');
        $map['职位分类'] = I('request.jobcategory','','trim');
        $map['地区分类'] = I('request.citycategory','','trim');
        $map['工资'] = I('request.salary','','trim');
        $map['工作经验'] = I('request.experience','','trim');
        $map['学历'] = I('request.education','','trim');
        $map['标签'] = I('request.jobtag','','trim');
        $class = new \Common\qscmstag\jobs_listTag($map);
        $jobs = $class->run();
        
        $returnlist = array();
        foreach ($jobs['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['uid'] = $value['uid'];
            $arr['jobs_name'] = $value['jobs_name'];
            $arr['companyname'] = $value['companyname'];
            $arr['company_id'] = $value['company_id'];
            $arr['emergency'] = $value['emergency'];
            $arr['district_cn'] = $value['district_cn'];  
            $arr['experience_cn'] = $value['experience_cn'];
            $arr['education_cn'] = $value['education_cn'];
            $arr['wage_cn'] = $value['wage_cn'];
            $arr['tag_cn'] = $value['tag_cn'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            $arr['audit'] = $value['audit'];
            $arr['map_range'] = $value['map_range'];
            $returnlist[] = $arr;
        }
        $return['returnlist'] = $returnlist;
        $return['default_district'] = C('qscms_default_district');
        $return['default_district_spell'] = C('qscms_default_district_spell');
        $return['nowPage'] = $jobs['page_params']['nowPage'];
        $return['totalPages'] = $jobs['page_params']['totalPages'];
        $return['isfull'] = $jobs['page_params']['isfull'];
        $this->ajaxReturn(200,'成功',$return);
    }

}