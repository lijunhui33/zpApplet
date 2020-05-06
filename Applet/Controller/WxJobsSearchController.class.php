<?php
namespace Applet\Controller;
use Common\Controller\FrontendController;
class WxJobsSearchController extends FrontendController{

    /**
     * 搜索页职位列表
     */
    public function search_jobs_list(){
        $map['分页显示'] = I('request.page','','trim');
        $map['显示数目'] = 5;
        $map['关键字'] = I('request.key','','trim');
        $map['搜索类型'] = 'jobs_commpany';
        $map['职位分类'] = I('request.jobcategory','','trim');
        $map['地区分类'] = I('request.citycategory','','trim');
        $map['工资'] = I('request.salary','','trim');
        $map['工作经验'] = I('request.experience','','trim');
        $map['学历'] = I('request.education','','trim');
        $map['标签'] = I('request.jobtag','','trim');

        $class = new \Common\qscmstag\jobs_listTag($map);
        $jobs = $class->run();

        $setmeal = M('Setmeal');
        $setmeal = $setmeal->getField('id,setmeal_img');

        $returnlist = array();
        foreach ($jobs['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['uid'] = $value['uid'];
            $arr['jobs_name'] = $value['jobs_name'];
            $arr['companyname'] = $value['companyname'];
            $arr['company_id'] = $value['company_id'];
            $arr['emergency'] = $value['emergency'];
            $arr['stick'] = $value['stick'];
            $arr['district_cn'] = $value['district_cn'];  
            $arr['experience_cn'] = $value['experience_cn'];
            $arr['education_cn'] = $value['education_cn'];
            $arr['wage_cn'] = $value['wage_cn'];
            $now = date('Y');
            $nowyear = strtotime($now.'-01-01');
            $today = strtotime(date('Y-m-d'));
            $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
            $diftime = time() - $value['refreshtime'];
            if($value['refreshtime'] < $nowyear){
                $arr['refreshtime'] = date('Y-m',$value['refreshtime']);
            }else{
                if($diftime >= 5*60 && $diftime<= 60*60){
                    $arr['refreshtime'] = intval($diftime / 60) ."分钟前";
                }else if($diftime >= 60*60 && $value['refreshtime']<= $tomorrow && $value['refreshtime'] >= $today){
                    $arr['refreshtime'] = intval($diftime / (60*60)) ."小时前";
                }else if($diftime < 5*60){
                    $arr['refreshtime'] = "刚刚";
                }else{
                    $arr['refreshtime'] = date('m-d',$value['refreshtime']);
                }
            }
            $arr['tag_cn'] = $value['tag_cn'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            $arr['audit'] = $value['audit'];
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

    /**
     * 职位搜索条件(地区)
     */
    public function search_jobs_region(){
        if(false === $region = F('district_custom_cate')) $region = D('CategoryDistrict')->custom_district_cache();

        $this->ajaxReturn(200,'成功',$region);
    }

    //默认地区
    public function default_district(){
        $default_district = C('qscms_default_district');

        $this->ajaxReturn(200,'成功',$default_district);
    }

    /**
     * 职位搜索条件(职位)
     */
    public function search_jobs_position(){
        if(false === $position = F('jobs_cate_list')) $position = D('CategoryJobs')->jobs_cate_cache();
        $position = $position['id'];
        $this->ajaxReturn(200,'成功',$position);
    }

    /**
     * 职位搜索条件(薪资)
     */
    public function search_jobs_salary(){
        $class = new \Common\qscmstag\classifyTag();
        $salary = $class->run();
        $this->ajaxReturn(200,'成功',$salary['QS_wage']);
    }

    /**
     * 职位搜索条件(更多)
     */
    public function search_jobs_more(){
        $class = new \Common\qscmstag\classifyTag();
        $data = $class->run(); 
        //学历要求
        $QS_education = $data['QS_education'];
        //工作经验
        $QS_experience = $data['QS_experience'];
        //福利待遇
        $QS_jobtag = $data['QS_jobtag'];
        //添加不限
        $unlimited = array('不限');
        $return['education'] = $QS_education + $unlimited;
        $return['experience'] = $QS_experience + $unlimited;
        $return['QS_jobtag'] = $QS_jobtag + $unlimited;
        $this->ajaxReturn(200,'成功',$return);
    }

    /**
     * 职位搜索展开相关内容显示
     */
    public function search_spread_out(){
        $com_map['排序'] = 'rtime';
        $com_map['显示数目'] = 4;
        $com_map['名企'] = 1;
        $class = new \Common\qscmstag\company_jobs_listTag($com_map);
        $companys = $class->run();

        foreach ($companys['list'] as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['companyname'] = $value['companyname'];
            $arr['setmeal_id'] = $value['setmeal_id'];
            $arr['jobs_count'] = $value['jobs_num'];
            $arr['logo'] = $value['logo'];
            $companylist[] = $arr;
        }
        //查询热门词汇
        $map['显示数目'] = 12;
        $class = new \Common\qscmstag\hotwordTag($map);
        $hotword = $class->run();

        $hotlist = array();
        foreach ($hotword as $key => $value) {
            $hot['w_word'] = $value['w_word'];
            $hot['url'] = url_rewrite('QS_jobslist',array('key'=>$value['w_word']));
            $hotlist[] = $hot;
        }
        //数据汇总
        $search_spread_out['companylist'] = $companylist;
        $search_spread_out['hotlist'] = $hotlist;
        $this->ajaxReturn(200,'成功',$search_spread_out);
    }
    
}