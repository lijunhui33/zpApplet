<?php
namespace Applet\Controller;
use Applet\Controller\WxTokenController;
class WxResumeController extends WxTokenController{
   
    public function _initialize() {
        parent::_initialize();
        parent::_init_visitor();
        $utype = C('visitor.utype');
        if($utype != 2){
            $this->ajaxReturn(401,'会员身份不正确,请切换个人身份');
        }
    }

    /**
     * [_is_resume 检测简历是否存在]
     * @return boolean [false || 简历信息(按需要添加字段)]
     */
    protected function _is_resume($pid){
        $map['id'] = $pid;
        $is_resume = M('Resume')->where($map)->count();
        if($is_resume > 0){
            $is_resume = true; 
        }else{
            $is_resume = false; 
        }
        return $is_resume;
    }

	/*
    添加简历基本信息下拉选项
     */
    public function ResumeBasicChoice(){
        $category = D('Category')->get_category_cache();
        //年份
        $birthdate_arr = range(date('Y') - 16, date('Y') - 65);
        //求职状态
        $QS_current = $category['QS_current'];
        //工作性质
        $QS_jobs_nature = $category['QS_jobs_nature'];
        //语言选择
        $language = $category['QS_language'];
        //语言熟练程度选择
        $language_level = $category['QS_language_level'];
        //行业选择
        $QS_trade = $category['QS_trade'];
        //简历标签
        $QS_resumetag = $category['QS_resumetag'];
        //工作经验
        $QS_experience = $category['QS_experience'];
        //学历要求
        $QS_education = $category['QS_education'];
        //专业分类
        $major = M('CategoryMajor')->getField('id,categoryname');

        $data['birth'] = $birthdate_arr;
        $data['QS_current'] = $QS_current;
        $data['QS_jobs_nature'] = $QS_jobs_nature;
        $data['language'] = $language;
        $data['language_level'] = $language_level;
        $data['QS_trade'] = $QS_trade;
        $data['QS_resumetag'] = $QS_resumetag; 
        $data['QS_experience'] = $QS_experience;
        $data['QS_education'] = $QS_education;
        $data['major'] = $major;
        $this->ajaxReturn(200, '成功', $data);
    }

    public function ResumeBasicAdd(){
        $fullname    = I('request.fullname', '', 'trim');
        $sex   = I('request.sex', '', 'intval');
        $birthdate    = I('request.birthdate', '', 'intval');
        $education    = I('request.education', '', 'intval');
        $experience    = I('request.experience', '', 'intval');
        $weixin    = I('request.weixin', '', 'trim');
        $current    = I('request.current', '', 'intval');
        $nature   = I('request.nature', '', 'intval');
        $intention_jobs    = I('request.intention_jobs', '', 'trim');
        $wage  = I('request.wage', '', 'intval');
        $district    = I('request.district', '', 'trim');

        if(!$intention_jobs || !$district){
            $this->ajaxReturn(0, '请填写完整信息');
        }

        $data['fullname']    = $fullname;

        if(preg_match("/^[\x7f-\xff]+$/", $data['fullname']) == false) {
            $this->ajaxReturn(0, '用户名只能是中文');
        } 

        $data['sex']    = $sex;
        $data['birthdate']    = $birthdate;
        $data['education']    = $education;
        $data['experience']    = $experience;
        $data['weixin']    = $weixin;
        $data['current']    = $current;
        $data['nature']    = $nature;
        $data['intention_jobs_id']    = $intention_jobs;
        $data['wage']    = $wage;
        $data['district']    = $district;
        $data['def']    = 1;
        $return = D('Resume')->add_resume($data, C('visitor')); 
        if($return['state'] == 0){
            $this->ajaxReturn(0, '简历创建失败');
        }else{
            $this->ajaxReturn(200, '简历创建成功',$return);
        }
    }

    /*
    添加简历教育经历/工作经历 
     */
    public function ResumeExpAdd(){
        //教育经历
        $uid = C('visitor.uid');
        $pid = I('request.pid', '', 'intval');
        foreach (array('school', 'speciality') as $val) {
                $eduarr[$val] = I('request.' . $val);
        }
        foreach (array('education','edustartyear', 'edustartmonth','education_cn','eduendyear', 'eduendmonth', 'edutodate') as $val) {
                $eduarr[$val] = I('request.' . $val);
        }
        foreach($eduarr as $k=>$v){
            if($v){
                  $edu = true;
                  break;
            }
        }
        if($edu){
            // 选择至今就不判断结束时间了
            if ($eduarr['edutodate'] == 1) {
                if (!$eduarr['edustartyear'] || !$eduarr['edustartmonth']) $this->ajaxReturn(0, '请选择就读时间！');
                if ($eduarr['edustartyear'] > intval(date('Y'))) $this->ajaxReturn(0, '就读开始时间不允许大于毕业时间！');
                if ($eduarr['edustartyear'] == intval(date('Y')) && $eduarr['edustartmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '就读开始时间需小于毕业时间！');
                $eduarr = array(
                    'school'=>$eduarr['school'],
                    'speciality'=>$eduarr['speciality'],
                    'education'=>$eduarr['education'],
                    'education_cn'=>$eduarr['education_cn'],
                    'startyear'=>$eduarr['edustartyear'],
                    'startmonth'=>$eduarr['edustartmonth'],
                    'endyear'=>$eduarr['eduendyear'],
                    'endmonth'=>$eduarr['eduendmonth'],
                    'todate'=>$eduarr['edutodate']
                );
                $eduarr['uid'] = $uid;
                $eduarr['pid'] = $pid;
            }else{
                if (!$eduarr['edustartyear'] || !$eduarr['edustartmonth'] || !$eduarr['eduendyear'] || !$eduarr['eduendmonth']) $this->ajaxReturn(0, '请选择就读时间！');

                if ($eduarr['edustartyear'] > intval(date('Y'))) $this->ajaxReturn(0, '就读开始时间不允许大于当前时间！');
                if ($eduarr['edustartyear'] == intval(date('Y')) && $eduarr['edustartmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '就读开始时间需小于当前时间！');

                if ($eduarr['edustartyear'] > $eduarr['eduendyear']) $this->ajaxReturn(0, '就读开始时间不允许大于毕业时间！');
                if ($eduarr['edustartyear'] == $eduarr['eduendyear'] && $eduarr['edustartmonth'] >= $eduarr['eduendmonth']) $this->ajaxReturn(0, '就读开始时间需小于毕业时间！');
                $eduarr = array(
                    'school'=>$eduarr['school'],
                    'speciality'=>$eduarr['speciality'],
                    'education'=>$eduarr['education'],
                    'education_cn'=>$eduarr['education_cn'],
                    'startyear'=>$eduarr['edustartyear'],
                    'startmonth'=>$eduarr['edustartmonth'],
                    'endyear'=>$eduarr['eduendyear'],
                    'endmonth'=>$eduarr['eduendmonth'],
                    'todate'=>$eduarr['edutodate']
                );
                $eduarr['uid'] = $uid;
                $eduarr['pid'] = $pid;
            }

            if (false === D('ResumeEducation')->create($eduarr)){
                $this->ajaxReturn(0,D('ResumeEducation')->getError());
            }
        }

        //工作经历
        foreach (array('companyname', 'achievements','jobs') as $val) {
                $exparr[$val] = I('request.' . $val);
        }
        foreach (array('expstartyear', 'expstartmonth', 'expendyear', 'expendmonth', 'exptodate') as $val) {
                $exparr[$val] = I('request.' . $val);
        }
        foreach($exparr as $k=>$v){
            if($v){
                $exp = true;
                break;
            }
        }

        if($exp){
            // 选择至今就不判断结束时间了
            if ($exparr['exptodate'] == 1) {
                if (!$exparr['expstartyear'] || !$exparr['expstartmonth']) $this->ajaxReturn(0, '请选择工作时间！');
                if ($exparr['expstartyear'] > intval(date('Y'))) $this->ajaxReturn(0, '工作开始时间不允许大于当前时间！');
                if ($exparr['expstartyear'] == intval(date('Y')) && $exparr['expstartmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '工作开始时间需小于当前时间！');
                $exparr = array(
                    'companyname'=>$exparr['companyname'],
                    'achievements'=>$exparr['achievements'],
                    'jobs'=>$exparr['jobs'],
                    'startyear'=>$exparr['expstartyear'],
                    'startmonth'=>$exparr['expstartmonth'],
                    'endyear'=>$exparr['expendyear'],
                    'endmonth'=>$exparr['expendmonth'],
                    'todate'=>$exparr['exptodate']
                );
                $exparr['uid'] = $uid;
                $exparr['pid'] = $pid;
            } else {
                if (!$exparr['expstartyear'] || !$exparr['expstartmonth'] || !$exparr['expendyear'] || !$exparr['expendmonth']) $this->ajaxReturn(0, '请选择工作时间！');

                if ($exparr['expstartyear'] > intval(date('Y'))) $this->ajaxReturn(0, '工作开始时间不允许大于当前时间！');
                if ($exparr['expstartyear'] == intval(date('Y')) && $exparr['expstartmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '工作开始时间需小于当前时间！');
                if ($exparr['expendyear'] > intval(date('Y'))) $this->ajaxReturn(0, '工作结束时间不允许大于当前时间！');
                if ($exparr['expendyear'] == intval(date('Y')) && $exparr['expendmonth'] > intval(date('m'))) $this->ajaxReturn(0, '工作结束时间不允许大于当前时间！');

                if ($exparr['expstartyear'] > $exparr['expendyear']) $this->ajaxReturn(0, '工作开始时间不允许大于结束时间！');
                if ($exparr['expstartyear'] == $exparr['expendyear'] && $exparr['expstartmonth'] >= $exparr['expendmonth']) $this->ajaxReturn(0, '工作开始时间需小于结束时间！');
                $exparr = array(
                    'companyname'=>$exparr['companyname'],
                    'achievements'=>$exparr['achievements'],
                    'jobs'=>$exparr['jobs'],
                    'startyear'=>$exparr['expstartyear'],
                    'startmonth'=>$exparr['expstartmonth'],
                    'endyear'=>$exparr['expendyear'],
                    'endmonth'=>$exparr['expendmonth'],
                    'todate'=>$exparr['exptodate']
                );
                $exparr['uid'] = $uid;
                $exparr['pid'] = $pid;
            }

            if (false === D('ResumeWork')->create($exparr)){
                $this->ajaxReturn(0,D('ResumeWork')->getError());
            }
        }
        if($edu){
            $eduadd = D('ResumeEducation')->add_resume_education($eduarr, C('visitor'));
            $eduresult = true;
        }
        if($exp){
            $expadd = D('ResumeWork')->add_resume_work($exparr, C('visitor'));
            $expresult = true;
        }
        if($eduresult && $expresult){
            $this->ajaxReturn(200, '教育和工作经历添加成功');
        }
        if($eduresult){
            $this->ajaxReturn(200, '教育经历添加成功请完善工作经历');
        }
        if($expresult){
            $this->ajaxReturn(200, '工作经历添加成功请完善教育经历');
        }
    }

    /*
    简历详细页
     */
    public function resume_details(){
        $map['简历id'] = C('visitor.pid');
        $class = new \Common\qscmstag\resume_showTag($map);
        $resume = $class->run();
        //基本信息
        $base = array();
        $base['photo_img'] = attach($resume['photo_img'],'avatar');
        $base['fullname'] = $resume['fullname'];
        $base['sex_cn'] = $resume['sex_cn'];
        $base['marriage_cn'] = $resume['marriage_cn'];
        $base['age'] = $resume['age'];
        $base['education_cn'] = $resume['education_cn'];
        $base['experience_cn'] = $resume['experience_cn'];
        $base['telephone'] = $resume['telephone_'];
        $base['weixin'] = $resume['weixin_'];
        $base['display'] = $resume['display'];
        //求职意向
        $intention = array();
        $intention['current_cn'] = $resume['current_cn'];
        $intention['nature_cn'] = $resume['nature_cn'];
        $intention['district_cn'] = $resume['district_cn'];
        $intention['wage_cn'] = $resume['wage_cn'];
        $intention['intention_jobs'] = $resume['intention_jobs'];
        $intention['trade_cn'] = $resume['trade_cn'];
        //教育经历
        $edu = $resume['education_list'];
        //工作经历
        $exp = $resume['work_list'];
        //培训经历
        $tra = $resume['training_list'];
        //项目经历
        $pro = $resume['project_list'];
        //自我描述
        $spe = $resume['specialty'];
        //获得证书
        $cre = $resume['credent_list'];
        //语言能力
        $lan = $resume['language_list'];
        //特长标签
        $tag = $resume['tag_cn'];
        //照片/作品
        $where['uid'] = C('visitor.uid');
        $where['resume_id'] = C('visitor.pid');
        $img = M('ResumeImg')->where($where)->field()->select();
        $imglist = array();
        foreach ($img as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['img'] = attach($value['img'],'resume_img');
            $arr['title'] = $value['title'];
            $arr['audit'] = $value['audit'];

            $imglist[] = $arr;
        }

        $return['base'] = $base;
        $return['intention'] = $intention;
        $return['edu'] = $edu;
        $return['exp'] = $exp;
        $return['tra'] = $tra;
        $return['pro'] = $pro;
        $return['spe'] = $spe;
        $return['cre'] = $cre;
        $return['lan'] = $lan;
        $return['tag'] = $tag;
        $return['imglist'] = $imglist;
        $this->ajaxReturn(200, '成功',$return);
    }

    /*
    简历基本信息回显
    */
    public function resume_display_basis() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $pid = C('visitor.pid');
        $uid = C('visitor.uid');
        $map['id'] = $pid;
        $map['uid'] = $uid;
        $basis = M('Resume')->where($map)->field('photo_img,fullname,sex_cn,birthdate,education,experience,telephone,weixin,email,qq,residence,householdaddress,marriage,major,height,idcard')->select();
        $return = array();
        foreach ($basis as $key => $value) {
            $arr['photo_img'] = attach($value['photo_img'],'avatar');
            $arr['fullname'] = $value['fullname'];
            $arr['sex_cn'] = $value['sex_cn'];
            $arr['birthdate'] = $value['birthdate'];
            $arr['education'] = $value['education'];
            $arr['experience'] = $value['experience'];
            $arr['telephone'] = $value['telephone'];
            $arr['weixin'] = $value['weixin'];
            $arr['email'] = $value['email'];
            $arr['qq'] = $value['qq'];
            $arr['residence'] = $value['residence'];
            $arr['householdaddress'] = $value['householdaddress'];
            $arr['marriage'] = $value['marriage'];
            $arr['major'] = $value['major'];
            if($value['height'] == 0){
                $arr['height'] = '';
            }else{
                $arr['height'] = $value['height'];
            }
            $arr['idcard'] = $value['idcard'];
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '查询成功', $return);
    }

    /*
    简历基本信息编辑
    */
    public function resume_edit_basis() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        if (IS_POST) {
            $ints = array('sex', 'birthdate', 'education', 'major', 'experience', 'height', 'marriage');
            $trims = array('fullname', 'residence', 'email', 'householdaddress', 'qq', 'weixin', 'idcard');
            foreach ($ints as $val) {
                $setsqlarr[$val] = I('post.' . $val, 0, 'intval');
            }
            foreach ($trims as $val) {
                $setsqlarr[$val] = I('post.' . $val, '', 'trim,badword');
            }
            if(preg_match("/^[\x7f-\xff]+$/", $setsqlarr['fullname']) == false) {
                $this->ajaxReturn(0, '用户名只能是中文');
            } 
            $uid = C('visitor.uid');
            if (C('qscms_audit_edit_resume') != "-1") D('ResumeEntrust')->set_resume_entrust($pid, $uid);//添加简历自动投递功能
            $rst = D('Resume')->save_resume($setsqlarr, $pid, C('visitor'));
            if ($rst['state']) $this->ajaxReturn(200, '数据保存成功！', $rst);
            $this->ajaxReturn(0, $rst['error']);
        }
    }


    /*简历基本信息编辑(现居住地址 微信获取)
     *
     *@param  [type] $lat [纬度]
     *@param  [type] $lng [经度]
     *@return [type]      [description]
     */
    public function resume_edit_residence(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $lat = I('request.lat','trim');
        $lng = I('request.lng','trim');
        $address = '';
        if($lat && $lng){
            $arr = $this->changeToBaidu($lat,$lng);
            $map_ak = C('qscms_applet_map_key');
            $url = 'http://api.map.baidu.com/reverse_geocoding/v3/?ak='.$map_ak.'&output=json&coordtype=wgs84ll&location='.$arr['y'].','.$arr['x'].'';
			$content = file_get_contents($url);
            $place = json_decode($content,true);
            $province = $place['result']['addressComponent']['province'];
            $city = $place['result']['addressComponent']['city'];
            $district = $place['result']['addressComponent']['district'];
            $address = $province.$city.$district;
            if(!$address){
                $this->ajaxReturn(0, '位置获取失败');
            }
            $address_data['residence'] = $address;
            if (false === D('Resume')->create($address_data)){
                $this->ajaxReturn(800,D('Resume')->getError());
            }
            $map['id'] = C('visitor.pid');
            $map['uid'] = C('visitor.uid');
            $residence_edit = D('Resume')->where($map)->save($address_data);
                $user = C('visitor');
                //写入会员日志
                write_members_log($user , 'resume', '修改简历现居住地址（简历id：' . $user['pid'] . '）', false, array('resume_id' => $user['pid']));
                $this->ajaxReturn(200, '修改成功',$address_data);
        }
    }

    /*[changeToBaidu 转换为百度经纬度]
     *@param  [type] $lat [description]
     *@param  [type] $lng [description]
     *@return [type]      [description]
     */
    public function changeToBaidu($lat,$lng){
        $map_ak = C('qscms_applet_map_key');
        $apiurl = 'http://api.map.baidu.com/geoconv/v1/?coords='.$lng.','.$lat.'&from=1&to=5&ak='.$map_ak;
        $file = file_get_contents($apiurl);
        $arrpoint = json_decode($file, true);
        return $arrpoint['result'][0];
    }

    /*
    简历求职意向回显
    */
    public function resume_display_intention(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $uid = C('visitor.uid');
        $map['id'] = $pid;
        $map['uid'] = $uid;
        $intention = M('Resume')->where($map)->field('current_cn,nature_cn,district,district_cn,wage,wage_cn,intention_jobs_id,intention_jobs,trade,trade_cn')->select();
        $this->ajaxReturn(200, '查询成功',$intention);
    }

    /*
    简历求职意向编辑
    */
    public function resume_edit_intention() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        if (IS_POST) {
            $uid = C('visitor.uid');
            $pid = C('visitor.pid');
            !$pid && $this->ajaxReturn(0, '请选择简历！');
            $setsqlarr['intention_jobs_id'] = I('post.intention_jobs_id', '', 'trim,badword');
            $setsqlarr['trade'] = I('post.trade', '', 'trim,badword');//期望行业
            $setsqlarr['district'] = I('post.district', '', 'trim,badword');//工作地区
            $setsqlarr['nature'] = I('post.nature', 0, 'intval');//工作性质
            $setsqlarr['current'] = I('post.current', 0, 'intval');
            $setsqlarr['wage'] = I('post.wage', 0, 'intval');//期望薪资
            if (C('qscms_audit_edit_resume') != "-1") D('ResumeEntrust')->set_resume_entrust($pid, $uid);//添加简历自动投递功能
            $rst = D('Resume')->save_resume($setsqlarr, $pid, C('visitor'));
            if ($rst['state']) $this->ajaxReturn(200, '求职意向修改成功！', $rst);
            $this->ajaxReturn(0, $rst['error']);
        }
    }

    /*
    教育经历回显
    */
    public function resume_display_education(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id','intval');
        $uid = C('visitor.uid');
        $map['pid'] = $pid;
        $map['uid'] = $uid;
        $map['id'] = $id;
        $education = M('ResumeEducation')->where($map)->field('school,speciality,education,startyear,startmonth,endyear,endmonth,todate')->select();

        $this->ajaxReturn(200, '查询成功',$education);
    }

    /*
    教育经历添加/修改(传ID)
    */
    public function resume_education(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id', 0, 'intval');
        if (IS_POST) {
            $setsqlarr['uid'] = C('visitor.uid');
            $setsqlarr['school'] = I('post.school', '', 'trim,badword');
            $setsqlarr['speciality'] = I('post.speciality', '', 'trim,badword');
            $setsqlarr['education'] = I('post.education', 0, 'intval');
            $setsqlarr['startyear'] = I('post.startyear', 0, 'intval');
            $setsqlarr['startmonth'] = I('post.startmonth', 0, 'intval');
            $setsqlarr['endyear'] = I('post.endyear', 0, 'intval');
            $setsqlarr['endmonth'] = I('post.endmonth', 0, 'intval');
            $setsqlarr['todate'] = I('post.todate', 0, 'intval'); // 至今

            // 选择至今就不判断结束时间了
            if ($setsqlarr['todate'] == 1) {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth']) $this->ajaxReturn(0, '请选择就读时间！');
                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '就读开始时间不允许大于毕业时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '就读开始时间需小于毕业时间！');
            } else {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth'] || !$setsqlarr['endyear'] || !$setsqlarr['endmonth']) $this->ajaxReturn(0, '请选择就读时间！');

                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '就读开始时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '就读开始时间需小于当前时间！');
                if ($setsqlarr['startyear'] > $setsqlarr['endyear']) $this->ajaxReturn(0, '就读开始时间不允许大于毕业时间！');
                if ($setsqlarr['startyear'] == $setsqlarr['endyear'] && $setsqlarr['startmonth'] >= $setsqlarr['endmonth']) $this->ajaxReturn(0, '就读开始时间需小于毕业时间！');
            }
            $education = D('Category')->get_category_cache('QS_education');
            $setsqlarr['education_cn'] = $education[$setsqlarr['education']];
            $setsqlarr['pid'] = $pid;
            if ($id) {
                $setsqlarr['id'] = $id;
                $name = 'save_resume_education';
            } else {
                $educationcount = M('ResumeEducation')->where(array('pid' => $setsqlarr['pid'], 'uid' => $setsqlarr['uid']))->count();
                if ($educationcount >= 6) $this->ajaxReturn(0, '教育经历不能超过6条！');
                $name = 'add_resume_education';
            }
            $reg = D('ResumeEducation')->$name($setsqlarr, C('visitor'));
            if ($reg['state']) {
                $this->ajaxReturn(200, '教育经历保存成功！', $reg);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }
    }

    /*
    证书回显
    */
    public function resume_display_certificate(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id','intval');
        $uid = C('visitor.uid');
        $map['pid'] = $pid;
        $map['uid'] = $uid;
        $map['id'] = $id;
        $certificate = M('ResumeCredent')->where($map)->select();
        $this->ajaxReturn(200, '查询成功',$certificate);
    }

    /*
    * 证书添加/修改(传ID)
    */
    public function resume_certificate() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id', 0, 'intval');
        if (IS_POST) {
            $setsqlarr['uid'] = C('visitor.uid');
            $setsqlarr['name'] = I('post.name', '', 'trim,badword');
            $setsqlarr['year'] = I('post.year', '', 'trim,badword');
            $setsqlarr['month'] = I('post.month', '', 'trim,badword');
            if (!$setsqlarr['year'] || !$setsqlarr['month']) $this->ajaxReturn(0, '请选择获得证书时间！');
            if ($setsqlarr['year'] > intval(date('Y'))) $this->ajaxReturn(0, '获得证书时间不能大于当前时间！');
            if ($setsqlarr['year'] == intval(date('Y')) && $setsqlarr['month'] > intval(date('m'))) $this->ajaxReturn(0, '获得证书时间不能大于当前时间！');
            $setsqlarr['pid'] = $pid;
            if ($id) {
                $setsqlarr['id'] = $id;
                $name = 'save_resume_credent';
            } else {
                $credentcount = M('ResumeCredent')->where(array('pid' => $pid, 'uid' => $setsqlarr['uid']))->count();
                if ($credentcount >= 6) $this->ajaxReturn(0, '证书不能超过6条！');
                $name = 'add_resume_credent';
            }
            $reg = D('ResumeCredent')->$name($setsqlarr, C('visitor'));
            if ($reg['state']) {
                $this->ajaxReturn(200, '证书保存成功！', $reg);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }
    }

    /*
    特长标签回显
    */
    public function resume_display_speciality(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $map['id'] = C('visitor.pid');
        $map['uid'] = C('visitor.uid');
        $tag = M('Resume')->where($map)->field('tag,tag_cn')->select();
        $this->ajaxReturn(200,'查询成功',$tag);
    }

    /*
    * 简历特长标签保存
    */
    public function resume_speciality() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        if (IS_POST) {
            $uid = C('visitor.uid');
            $setarr['tag_cn']= I('post.tag_cn', '', 'trim');
            $setarr['tag'] = I('post.tag', '', 'trim');
            foreach ($tag as $key => $val) {
                $setarr['tag_cn'] .= ",{$tags[$val]}";
            }
            $setarr['tag_cn'] = ltrim($setarr['tag_cn'], ',');
            if (!$setarr['tag_cn']) $s = 2;
            $resume_mod = D('Resume');
            $tag = explode(',',$setarr['tag']);
            $tag_count = count($tag);
            if($tag_count > 6){
                $this->ajaxReturn(0, '简历标签不能超过6条');
            }
            if (false !== $resume_mod->where(array('id' => C('visitor.pid'), 'uid' => $uid))->save($setarr)) {
                //才情start
                if($tag){
                    foreach ($tag as $k1 => $v1) {
                        $talent_api = new \Common\qscmslib\talent;
                        $talent_api->act='resume_tag_add';
                        $talent_api->data = array(
                            'pid'=>C('visitor.uid'),
                            'tag'=>$v1
                        );
                        $talent_api->send();
                        unset($talent_api);
                    }
                }
                //才情end
                $resume_mod->check_resume($uid, C('visitor.uid'));//更新简历完成状态
                //写入会员日志
                write_members_log(C('visitor'), 'resume', '保存简历特长标签（简历id：' . C('visitor.pid') . '）', false, array('resume_id' => C('visitor.pid')));
                $this->ajaxReturn(200, '简历特长标签修改成功！');
            }
            $this->ajaxReturn(0, '保存失败！');
        }

    }


    function find_by_array_search($array,$find){
        return array_search($find,$array);
    }

    //特长标签删除
    public function del_speciality(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $tag_cn = I('request.tag_cn','trim');
        $map['id'] = C('visitor.pid');
        $map['uid'] = C('visitor.uid');
        $tag = M('Resume')->where($map)->field('tag,tag_cn')->select();
        $befor = explode(',', $tag[0]['tag_cn']);
        $index = $this->find_by_array_search($befor,$tag_cn);
        $befor_id = explode(',', $tag[0]['tag']);
        // unset($befor[$index]);
        // unset($befor_id[$index]);
        $data['id'] = C('visitor.pid');
        unset($befor_id[$index]);
        unset($befor[$index]);
        $data['tag'] = implode(',',$befor_id);
        $data['tag_cn'] = implode(',',$befor);
        $resume_tag = M('Resume')->where($map)->save($data);
        //写入会员日志
        write_members_log(C('visitor'), 'resume', '删除简历特长标签（简历id：' . C('visitor.pid') . '）', false, array('resume_id' => C('visitor.pid')));
        $this->ajaxReturn(200,'简历标签修改成功');

    }


    /*
    培训经历回显
    */
    public function resume_display_train(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id','intval');
        $uid = C('visitor.uid');
        $map['pid'] = $pid;
        $map['uid'] = $uid;
        $map['id'] = $id;
        $train = M('ResumeTraining')->where($map)->field('agency,course,description,startyear,startmonth,endyear,endmonth,todate')->select();
        $this->ajaxReturn(200, '查询成功',$train );
    }

    /*
    *培训经历添加/修改(传ID)
    */
    public function resume_train() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id', 0, 'intval');
        if (IS_POST) {
            $setsqlarr['uid'] = C('visitor.uid');
            $setsqlarr['agency'] = I('post.agency', '', 'trim,badword');
            $setsqlarr['course'] = I('post.course', '', 'trim,badword');
            $setsqlarr['description'] = I('post.description', '', 'trim,badword');
            $setsqlarr['startyear'] = I('post.startyear', 0, 'intval');
            $setsqlarr['startmonth'] = I('post.startmonth', 0, 'intval');
            $setsqlarr['endyear'] = I('post.endyear', 0, 'intval');
            $setsqlarr['endmonth'] = I('post.endmonth', 0, 'intval');
            $setsqlarr['todate'] = I('post.todate', 0, 'intval'); // 至今
            // 选择至今就不判断结束时间了
            if ($setsqlarr['todate'] == 1) {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth']) $this->ajaxReturn(0, '请选择培训时间！');
                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '培训开始时间不允许大于毕业时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '培训开始时间需小于毕业时间！');
            } else {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth'] || !$setsqlarr['endyear'] || !$setsqlarr['endmonth']) $this->ajaxReturn(0, '请选择培训时间！');
                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '培训开始时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '培训开始时间需小于当前时间！');
                if ($setsqlarr['endyear'] > intval(date('Y'))) $this->ajaxReturn(0, '培训结束时间不允许大于当前时间！');
                if ($setsqlarr['endyear'] == intval(date('Y')) && $setsqlarr['endmonth'] > intval(date('m'))) $this->ajaxReturn(0, '培训结束时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] > $setsqlarr['endyear']) $this->ajaxReturn(0, '培训开始时间不允许大于毕业时间！');
                if ($setsqlarr['startyear'] == $setsqlarr['endyear'] && $setsqlarr['startmonth'] >= $setsqlarr['endmonth']) $this->ajaxReturn(0, '培训开始时间需小于毕业时间！');
            }
            if (false === $resume = $this->_is_resume($pid)) $this->ajaxReturn(0, '请先填写简历基本信息！');
            $setsqlarr['pid'] = $pid;
            if ($id) {
                $setsqlarr['id'] = $id;
                $name = 'save_resume_training';
            } else {
                $trainingcount = M('ResumeTraining')->where(array('pid' => $setsqlarr['pid'], 'uid' => $setsqlarr['uid']))->count();
                if ($trainingcount >= 6) $this->ajaxReturn(0, '培训经历不能超过6条！');
                $name = 'add_resume_training';
            }
            $reg = D('ResumeTraining')->$name($setsqlarr, C('visitor'));
            if ($reg['state']) {
                $this->ajaxReturn(200, '培训经历保存成功！', $reg);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }
    }

    /*
    工作经历回显
    */
    public function resume_display_work(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id','intval');
        $uid = C('visitor.uid');
        $map['pid'] = $pid;
        $map['uid'] = $uid;
        $map['id'] = $id;
        $work = M('ResumeWork')->where($map)->field('companyname,jobs,achievements,startyear,startmonth,endyear,endmonth,todate')->select();
        $this->ajaxReturn(200, '查询成功',$work );
    }

    /*
    *工作经历添加/修改(传ID)
    */
    public function resume_work() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id', 0, 'intval');
        if (IS_POST) {
            $setsqlarr['uid'] = C('visitor.uid');
            $setsqlarr['companyname'] = I('post.companyname', '', 'trim,badword');
            $setsqlarr['jobs'] = I('post.jobs', '', 'trim,badword');
            $setsqlarr['achievements'] = I('post.achievements', '', 'trim,badword');
            $setsqlarr['startyear'] = I('post.startyear', 0, 'intval');
            $setsqlarr['startmonth'] = I('post.startmonth', 0, 'intval');
            $setsqlarr['endyear'] = I('post.endyear', 0, 'intval');
            $setsqlarr['endmonth'] = I('post.endmonth', 0, 'intval');
            $setsqlarr['todate'] = I('post.todate', 0, 'intval'); // 至今
            // 选择至今就不判断结束时间了
            if ($setsqlarr['todate'] == 1) {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth']) $this->ajaxReturn(0, '请选择工作时间！');
                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '工作开始时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '工作开始时间需小于当前时间！');
            } else {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth'] || !$setsqlarr['endyear'] || !$setsqlarr['endmonth']) $this->ajaxReturn(0, '请选择工作时间！');

                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '工作开始时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '工作开始时间需小于当前时间！');
                if ($setsqlarr['endyear'] > intval(date('Y'))) $this->ajaxReturn(0, '工作结束时间不允许大于当前时间！');
                if ($setsqlarr['endyear'] == intval(date('Y')) && $setsqlarr['endmonth'] > intval(date('m'))) $this->ajaxReturn(0, '工作结束时间不允许大于当前时间！');

                if ($setsqlarr['startyear'] > $setsqlarr['endyear']) $this->ajaxReturn(0, '工作开始时间不允许大于结束时间！');
                if ($setsqlarr['startyear'] == $setsqlarr['endyear'] && $setsqlarr['startmonth'] >= $setsqlarr['endmonth']) $this->ajaxReturn(0, '工作开始时间需小于结束时间！');
            }
            if (false === $resume = $this->_is_resume($pid)) $this->ajaxReturn(0, '请先填写简历基本信息！');
            $setsqlarr['pid'] = $pid;
            if ($id) {
                $setsqlarr['id'] = $id;
                $name = 'save_resume_work';
            } else {
                $workcount = M('ResumeWork')->where(array('pid' => $setsqlarr['pid'], 'uid' => $setsqlarr['uid']))->count();
                if ($workcount >= 6) $this->ajaxReturn(0, '工作经历不能超过6条！');
                $name = 'add_resume_work';
            }
            $reg = D('ResumeWork')->$name($setsqlarr, C('visitor'));
            if ($reg['state']) {
                $this->ajaxReturn(200, '工作经历保存成功！', $reg);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }
    }

    /*
    项目经历回显
    */
    public function resume_display_project(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id','intval');
        $uid = C('visitor.uid');
        $map['pid'] = $pid;
        $map['uid'] = $uid;
        $map['id'] = $id;
        $project = M('ResumeProject')->where($map)->field('projectname,role,description,startyear,startmonth,endyear,endmonth,todate')->select();
        $this->ajaxReturn(200, '查询成功',$project );
    }

    /*
    *项目经历添加/修改(传ID)
    */
    public function resume_project() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id', 0, 'intval');
        if (IS_POST) {
            $setsqlarr['uid'] = C('visitor.uid');
            $setsqlarr['projectname'] = I('post.projectname', '', 'trim,badword');
            $setsqlarr['role'] = I('post.role', '', 'trim,badword');
            $setsqlarr['description'] = I('post.description', '', 'trim,badword');
            $setsqlarr['startyear'] = I('post.startyear', 0, 'intval');
            $setsqlarr['startmonth'] = I('post.startmonth', 0, 'intval');
            $setsqlarr['endyear'] = I('post.endyear', 0, 'intval');
            $setsqlarr['endmonth'] = I('post.endmonth', 0, 'intval');
            $setsqlarr['todate'] = I('post.todate', 0, 'intval'); // 至今
            // 选择至今就不判断结束时间了
            if ($setsqlarr['todate'] == 1) {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth']) $this->ajaxReturn(0, '请选择项目时间！');
                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '项目开始时间不允许大于结束时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '项目开始时间需小于结束时间！');
            } else {
                if (!$setsqlarr['startyear'] || !$setsqlarr['startmonth'] || !$setsqlarr['endyear'] || !$setsqlarr['endmonth']) $this->ajaxReturn(0, '请选择项目时间！');
                if ($setsqlarr['startyear'] > intval(date('Y'))) $this->ajaxReturn(0, '项目开始时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] == intval(date('Y')) && $setsqlarr['startmonth'] >= intval(date('m'))) $this->ajaxReturn(0, '项目开始时间需小于当前时间！');
                if ($setsqlarr['endyear'] > intval(date('Y'))) $this->ajaxReturn(0, '项目结束时间不允许大于当前时间！');
                if ($setsqlarr['endyear'] == intval(date('Y')) && $setsqlarr['endmonth'] > intval(date('m'))) $this->ajaxReturn(0, '项目结束时间不允许大于当前时间！');
                if ($setsqlarr['startyear'] > $setsqlarr['endyear']) $this->ajaxReturn(0, '项目开始时间不允许大于结束时间！');
                if ($setsqlarr['startyear'] == $setsqlarr['endyear'] && $setsqlarr['startmonth'] >= $setsqlarr['endmonth']) $this->ajaxReturn(0, '项目开始时间需小于结束时间！');
            }
            if (false === $resume = $this->_is_resume($pid)) $this->ajaxReturn(0, '请先填写简历基本信息！');
            $setsqlarr['pid'] = $pid;
            if ($id) {
                $setsqlarr['id'] = $id;
                $name = 'save_resume_project';
            } else {
                $projectcount = M('ResumeProject')->where(array('pid' => $setsqlarr['pid'], 'uid' => $setsqlarr['uid']))->count();
                if ($projectcount >= 6) $this->ajaxReturn(0, '项目经历不能超过6条！');
                $name = 'add_resume_project';
            }
            $reg = D('ResumeProject')->$name($setsqlarr, C('visitor'));
            if ($reg['state']) {
                $this->ajaxReturn(200, '项目经历保存成功！', $reg);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }
    }

    /*
    个人描述回显
    */
    public function resume_display_specialty(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }$resume = $this->_is_resume();
        $id = $pid;
        $uid = C('visitor.uid');
        $map['id'] = $id;
        $map['uid'] = $uid;
        $specialty = M('Resume')->where($map)->field('specialty')->select();
        $this->ajaxReturn(200, '查询成功',$specialty );
    }

    /*
    *个人描述修改
    */
    public function resume_specialty() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        if (IS_POST) {
            $specialty = I('post.specialty', '', 'trim,badword');
            !$specialty && $this->ajaxReturn(0, '请输入自我描述!');
            $rst = D('Resume')->save_resume(array('specialty' => $specialty), $pid, C('visitor'));
            if (!$rst['state']) $this->ajaxReturn(0, $rst['error']);
            write_members_log(C('visitor'), 'resume', '保存简历自我描述（简历id：' . $pid . '）', false, array('resume_id' => $pid));
            $this->ajaxReturn(200, '简历自我描述修改成功', $rst);
        }
    }

    /*
    语言回显
    */
    public function resume_display_language(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $pid = C('visitor.pid');
        $id = I('request.id','intval');
        $uid = C('visitor.uid');
        $map['pid'] = $pid;
        $map['uid'] = $uid;
        $map['id'] = $id;
        $language = M('ResumeLanguage')->where($map)->field('language_cn,level_cn')->select();
        $this->ajaxReturn(200, '查询成功',$language );
    }

    /*
    语言添加/修改(传ID)
    */
    public function resume_language() {
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $id = I('request.id', 0, 'intval');
        $category = D('Category')->get_category_cache();
        if (IS_POST) {
            $count = M('ResumeLanguage')->where(array('pid' => C('visitor.pid'), 'uid' => C('visitor.uid')))->count('id');
            if ($count > 6) $this->ajaxReturn(0, '语言能力不能超过6条！');
            $language['uid'] = C('visitor.uid');
            $language['pid'] = C('visitor.pid');
            $language['language'] = I('post.language', 0, 'intval');
            $language['level'] = I('post.level', 0, 'intval');
            $language['language_cn'] = $category['QS_language'][$language['language']];
            $language['level_cn'] = $category['QS_language_level'][$language['level']];
            if($id){
                $language['id'] = $id;
                $name = 'save_resume_language';
                $lan = M('ResumeLanguage')->where(array('id'=>$id))->find();
                if($lan['language'] != $language['language']){
                    $is = M('ResumeLanguage')->where(array('pid'=>C('visitor.pid'),'uid'=>C('visitor.uid'),'language'=>$language['language']))->find();
                    $is && $this->ajaxReturn(0,'此语言能力已添加，请重新选择！');
                }
            }else{
                $name = 'add_resume_language';
                $is = M('ResumeLanguage')->where(array('pid'=>C('visitor.pid'),'uid'=>C('visitor.uid'),'language'=>$language['language']))->find();
                $is && $this->ajaxReturn(0,'语言能力不能重复添加！');
            }
            $reg = D('ResumeLanguage')->$name($language, C('visitor'));
            if ($reg['state']) {
                $this->ajaxReturn(200, '语言能力保存成功！', $reg);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }
    }

    /**
     * [avatar 头像上传保存]
     */
    public function avatar() {
        $config_params = array(
            'upload_ok' => false,
            'save_path' => '',
            'show_path' => ''
        );
        $uid = C('visitor.uid');
        $savePicName = md5($uid . time()) . '.jpg';
        $pic = base64_decode($_POST['base64_string']);
        $size = explode(',', C('qscms_avatar_size'));
        if (C('qscms_qiniu_open') == 1) {
            $qiniu = new qiniu(array(
                'stream' => true
            ));
            $config_params['save_path'] = $config_params['show_path'] = $qiniu->uploadStream($pic, $savePicName);
            if ($config_params['save_path']) {
                foreach ($size as $val) {
                    $thumb_name = $qiniu->getThumbName($config_params['save_path'], $val, $val);
                    $qiniu->uploadStream($pic, $thumb_name, $val, $val, true);
                }
                $config_params['upload_ok'] = true;
            } else {
                $config_params['info'] = $qiniu->getError();
            }
        } else {
            //日期路径
            $date = date('ym/d/');
            $save_avatar = C('qscms_attach_path') . 'avatar/' . $date;//图片存储路径
            if (!is_dir($save_avatar)) {
                mkdir($save_avatar, 0777, true);
            }
            $filename = $save_avatar . $savePicName;
            file_put_contents($filename, $pic);
            $image = new \Common\ORG\ThinkImage();
            foreach ($size as $val) {
                $image->open($filename)->thumb($val, $val, 3)->save($filename . "_" . $val . "x" . $val . ".jpg");
            }
            $config_params['save_path'] = $date . $savePicName;
            $config_params['show_path'] = attach($config_params['save_path'], 'avatar') . '?' . time();
            $config_params['upload_ok'] = true;
        }
        if ($config_params['upload_ok']) {
            $setsqlarr['avatars'] = $config_params['save_path'];
            $old_avatar = D('Members')->where(array('uid' => $uid))->getfield('avatars');
            $photo = M('Resume')->field('photo_audit,photo_display')->where(array('uid' => $uid, 'def' => 1))->find();
            if ($photo['photo_display'] == 1) {
                $setsqlarr['photo'] = 1;
            } else {
                $setsqlarr['photo'] = 0;
            }
            $setsqlarr['photo_audit'] = 2;
            if (true !== $reg = D('Members')->update_user_info($setsqlarr, C('visitor'))) $this->ajaxReturn(0, $reg);
            $user_resume_list = D('Resume')->where(array('uid' => $uid))->select();
            foreach ($user_resume_list as $key => $value) {
                D('Resume')->check_resume($uid, $value['id']);//更新简历完成状态
            }
            $im = new \Common\qscmslib\im();
            $im->refresh($uid);
            D('TaskLog')->do_task(C('visitor'), 'upload_avatar');
            write_members_log(C('visitor'), '', '保存个人头像');
            $data = array('path' => $config_params['show_path'], 'img' => $config_params['save_path']);
            $this->ajaxReturn(200, L('upload_success'), $data);
        } else {
            $this->ajaxReturn(0, $config_params['info']);
        }
    }

    /*
    *照片作品统计
    */
    public function resume_img_count(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $map['uid'] = C('visitor.uid');
        $map['resume_id'] = C('visitor.pid');
        $resume_img = M('ResumeImg')->where($map)->count();
        $this->ajaxReturn(200, '已上传图片数量',$resume_img);
    }

   /**
     * [resume_img 个人简历图片文件上传]
     * @return [type] [description]
     */
    public function resume_img() {
        $config_params = array(
            'upload_ok' => false,
            'save_path' => '',
            'show_path' => ''
        );
        $filename = uniqid() . '.jpg';
        $uid = C('visitor.uid');
        $pic = base64_decode($_POST['base64_string']);
        $size = explode(',', C('qscms_resume_img_size'));
        if (C('qscms_qiniu_open') == 1) {
            $qiniu = new qiniu(array(
                'stream' => true
            ));
            $config_params['save_path'] = $config_params['show_path'] = $qiniu->uploadStream($pic, $filename);
            if ($config_params['save_path']) {
                foreach ($size as $val) {
                    $thumb_name = $qiniu->getThumbName($config_params['save_path'], $val, $val);
                    $qiniu->uploadStream($pic, $thumb_name, $val, $val, true);
                }
                $config_params['upload_ok'] = true;
            } else {
                $config_params['info'] = $qiniu->getError();
            }
        } else {
            //日期路径
            $date = date('ym/d/');
            $save_avatar = C('qscms_attach_path') . 'resume_img/' . $date;//图片存储路径
            if (!is_dir($save_avatar)) {
                mkdir($save_avatar, 0777, true);
            }
            file_put_contents($save_avatar . $filename, $pic);
            $image = new \Common\ORG\ThinkImage();
            $path = $save_avatar . $filename;
            foreach ($size as $val) {
                $image->open($path)->thumb($val, $val, 3)->save("{$path}_{$val}x{$val}.jpg");
            }
            $config_params['save_path'] = $date . $filename;
            $config_params['show_path'] = attach($config_params['save_path'], 'resume_img');
            $config_params['upload_ok'] = true;
        }
        if ($config_params['upload_ok']) {
            $data = array('path' => $config_params['show_path'], 'img' => $config_params['save_path']);
            $this->ajaxReturn(200, L('upload_success'), $data);
        } else {
            $this->ajaxReturn(0, $config_params['info']);
        }
    }

    //图片保存数据库
    public function resume_img_save(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $img = I('request.img','','trim');
        $img_id = I('request.id','','intval');
        $map['resume_id'] = C('visitor.pid');
        $map['uid'] = C('visitor.uid');
        if($img_id){
            $data['id'] = $img_id;
            $data['uid'] = C('visitor.uid');
            $data['resume_id'] = C('visitor.pid');
            $data['img'] = $img;
            $data['title'] = '我的作品';
            $data['addtime'] = time();
            $data['audit'] = 2;
            $img_save = M('ResumeImg')->save($data);
            if($img_save){
            $this->ajaxReturn(200, '图片修改成功',$img_save);
            }else{
                $this->ajaxReturn(0, '图片修改失败',$img_save);
            }
        }else{
            $data['uid'] = C('visitor.uid');
            $data['resume_id'] = C('visitor.pid');
            $data['img'] = $img;
            $data['title'] = '我的作品';
            $data['addtime'] = time();
            $data['audit'] = 2;
            $resume_img = M('ResumeImg')->where($map)->count();
            if($resume_img < 6){
                $img_save = M('ResumeImg')->add($data);
                if($img_save){
                $this->ajaxReturn(200, '图片上传成功',$img_save);
                }else{
                    $this->ajaxReturn(0, '图片上传失败',$img_save);
                } 
            }else{
                $this->ajaxReturn(0, '图片上传不能超过6张',$img_save);
            }
        }
    }

    /*
    我的作品回显
    */
    public function resume_display_img(){
        $pid = C('visitor.pid');
        $resume = $this->_is_resume($pid);
        if($resume == false){
            $this->ajaxReturn(0, '请选择正确简历');
        }
        $img_id = I('request.id','','intval');
        $map['id'] = $img_id;
        $map['uid'] = C('visitor.uid');
        $map['resume_id'] = C('visitor.pid');
        $img = M('ResumeImg')->where($map)->field()->select();
        $return = array();
        foreach ($img as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['img'] = attach($value['img'],'resume_img');
            $return[] = $arr;
        }
        $this->ajaxReturn(200, '查询成功',$return);
    }

    /**
     * [_del_data 删除简历信息]
     */
    protected function _del_data($type) {
        $id = I('request.id', 0, 'intval');
        $pid = C('visitor.pid');
        if (!$pid || !$id) $this->ajaxReturn(0, '请求缺少参数！');
        if (IS_POST) {
            $uid = C('visitor.uid');
            if (M($type)->where(array('id' => $id, 'uid' => $uid, 'pid' => $pid))->delete()) {
                switch ($type) {
                    case 'ResumeEducation':
                        write_members_log($user, 'resume', '删除简历教育经历（简历id：' . $pid . '）', false, array('resume_id' => $pid));
                        break;
                    case 'ResumeWork':
                        write_members_log($user, 'resume', '删除简历工作经历（简历id：' . $pid . '）', false, array('resume_id' => $pid));
                        break;
                    case 'ResumeTraining':
                        write_members_log($user, 'resume', '删除简历培训经历（简历id：' . $pid . '）', false, array('resume_id' => $pid));
                        break;
                    case 'ResumeProject':
                        write_members_log($user, 'resume', '删除简历项目经历（简历id：' . $pid . '）', false, array('resume_id' => $pid));
                        break;
                    case 'ResumeLanguage':
                        write_members_log($user, 'resume', '删除简历语言能力（简历id：' . $pid . '）', false, array('resume_id' => $pid));
                        break;
                    case 'ResumeCredent':
                        write_members_log($user, 'resume', '删除简历证书（简历id：' . $pid . '）');
                        break;
                }
                $resume_mod = D('Resume');
                $resume_mod->check_resume($uid, $pid);//更新简历完成状态
                $this->ajaxReturn(200, '删除成功！');
            } else {
                $this->ajaxReturn(0, '删除失败！');
            }
        }
    }

    //删除教育经历
    public function del_education() {
        $this->_del_data('ResumeEducation');
    }

    //删除工作经历
    public function del_work() {
        $this->_del_data('ResumeWork');
    }

    //删除培训经历
    public function del_training() {
        $this->_del_data('ResumeTraining');
    }

    //删除项目经历
    public function del_project() {
        $this->_del_data('ResumeProject');
    }

    //删除语言能力
    public function del_language() {
        $this->_del_data('ResumeLanguage');
    }

    //删除证书
    public function del_credent() {
        $this->_del_data('ResumeCredent');
    }

    //删除简历附件
    public function del_img() {
        if (IS_POST) {
            $img_id = I('request.id', 0, 'intval');
            !$img_id && $this->ajaxReturn(0, '请选择要删除的图片！');
            $uid = C('visitor.uid');
            $img_mod = M('ResumeImg');
            $row = $img_mod->where(array('id' => $img_id, 'uid' => $uid))->field('img,resume_id')->find();
            $size = explode(',', C('qscms_resume_img_size'));
            @unlink(C('qscms_attach_path') . "photo/" . $row['img']);
            if (C('qscms_qiniu_open') == 1) {
                $qiniu = new \Common\ORG\qiniu;
                $qiniu->delete($row['img']);
            }
            foreach ($size as $val) {
                @unlink(C('qscms_attach_path') . "photo/{$row['img']}_{$val}x{$val}.jpg");
                if (C('qscms_qiniu_open') == 1) {
                    $thumb_name = $qiniu->getThumbName($row['img'], $val, $val);
                    $qiniu->delete($thumb_name);
                }
            }
            if (false === $img_mod->where(array('id' => $img_id, 'uid' => $uid))->delete()) $this->ajaxReturn(0, '删除失败！');
            //写入会员日志
            write_members_log(C('visitor'), 'resume', '删除简历附件（简历id：' . intval($row['resume_id']) . '）', false, array('resume_id' => intval($row['resume_id'])));
            D('Resume')->check_resume(C('visitor.uid'), intval($row['resume_id']));//更新简历完成状态
            $this->ajaxReturn(200, '删除成功！');
        }
    }

    //刷新简历
     public function refresh_resume() {
        $pid = C('visitor.pid');
        !$pid && $pid = M('Resume')->where(array('uid' => C('visitor.uid')))->order('def desc')->limit(1)->getField('id');
        !$pid && $this->ajaxReturn(0, '简历不存在！');
        $uid = C('visitor.uid');
        $r = D('Resume')->get_resume_list(array('where' => array('uid' => $uid, 'id' => $pid), 'field' => 'id,title,audit,display'));
        !$r && $this->ajaxReturn(0, "选择的简历不存在！");
        $r[0]['_audit'] != 1 && $this->ajaxReturn(0, "审核中或未通过的简历无法刷新！");
        $r[0]['display'] != 1 && $this->ajaxReturn(0, "简历已关闭，无法刷新！");
        $refresh_log = M('RefreshLog');
        $refrestime = $refresh_log->where(array('uid' => $uid, 'type' => 2001))->order('addtime desc')->getfield('addtime');
        $duringtime = time() - $refrestime;
        $space = C('qscms_per_refresh_resume_space') * 60;
        $today = strtotime(date('Y-m-d'));
        $tomorrow = $today + 3600 * 24;
        $count = $refresh_log->where(array('uid' => $uid, 'type' => 2001, 'addtime' => array('BETWEEN', array($today, $tomorrow))))->count();
        if(C('qscms_per_refresh_resume_time') != 0 && ($count >= C('qscms_per_refresh_resume_time'))) {
            $this->ajaxReturn(0, "每天最多可刷新 " . C('qscms_per_refresh_resume_time') . " 次，您今天已达到最大刷新次数！");
        }elseif ($duringtime <= $space && $space != 0) {
            $this->ajaxReturn(0, C('qscms_per_refresh_resume_space') . " 分钟内不允许重复刷新简历！");
        }elseif(false !== D('Resume')->refresh_resume($pid, C('visitor'))){
            $this->ajaxReturn(200, '刷新简历成功！');
        }else{
            $this->ajaxReturn(0, '刷新简历失败！');
        }
    }

    /**
     *  隐私设置
     */
    public function resume_privacy() {
        $pid = C('visitor.pid');
        !$pid && $this->ajaxReturn(0, '请选择简历!');
        $setsqlarr['display'] = I('request.display', 0, 'intval');
        $uid = C('visitor.uid');
        $where = array('id' => $pid, 'uid' => $uid);
        if(false !== M('Resume')->where($where)->save($setsqlarr)) {
            $reg = D('Resume')->resume_index($pid);
            if(!$reg['state']) $this->ajaxReturn(0, $reg['error']);
            //写入会员日志
            write_members_log(C('visitor'), 'resume', '保存显示/隐藏设置（简历id：' . $pid . '）', false, array('resume_id' => $pid));
            $this->ajaxReturn(200, '显示/隐藏设置成功!');
        }else{
            $this->ajaxReturn(0, '显示/隐藏设置失败，请重新操作!');
        }
    }

    /**
    * 面试邀请
    */
    public function jobs_interview() {
        $where['resume_uid'] = C('visitor.uid');
        $company_interview_mod = D('CompanyInterview');
        $interview = $company_interview_mod->get_invitation_pre($where);
        $list['isfull'] = $interview['page_params']['isfull'] ;
        if ($list['list'] = $interview['list']) {
            $interview['page_params']['actualPage'] > $interview['page_params']['totalPages'] && $list['list'] = array();
        foreach ($interview['list'] as $key => $value) {
            $map['id'] = $value['jobs_id'];
            $jobs = M('Jobs')->where($map)->find();
            if($jobs){
                $arr['id'] = $value['jobs_id'];
                $arr['did'] = $value['did'];
                $arr['jobs_name'] = $value['jobs_name'];
                $arr['wage_cn'] = $value['wage_cn'];
                $arr['company_name'] = $value['company_name'];
                $H = date('H',$value['interview_time']);
                if($H > 7 && $H < 12){
                    $array['interview_H'] = '上午';
                }else{
                    $array['interview_H'] = '下午';
                }
                $arr['interview_time'] = date('Y-m-d',$value['interview_time']) .' '. $array['interview_H'] . date('H:i',$value['interview_time']);
                if(time() < $value['interview_time']){
                    $arr['interview_state'] = 1;
                }else{
                    $arr['interview_state'] = 0;
                } 
                $arr['personal_look'] = $value['personal_look'];
                $returnlist[] = $arr;
            }else{
                $array['id'] = $value['jobs_id'];
                $array['did'] = $value['did'];
                $array['tips'] = '该职位不存在或已被删除';
                $returnlist[] = $array;
            }
        }
        $returnarr = ['returnlist'=>$returnlist]; 
        $returnarr['nowPage'] = $interview['page_params']['nowPage'];
        $returnarr['totalPages'] = $interview['page_params']['totalPages'];
        $returnarr['isfull'] = $interview['page_params']['isfull'];
        $this->ajaxReturn(200, "面试邀请获取成功", $returnarr);
        }
        $this->ajaxReturn(0, "没有更多信息！",$returnarr);
    }

    /**
     * [interview_del 删除面试邀请]
     */
    public function interview_del() {
        $yid = I('request.did', '', 'trim,badword');
        !$yid && $this->ajaxReturn(0, "你没有选择项目！");
        $rst = D('CompanyInterview')->del_interview($yid, C('visitor'));
        if (intval($rst['state']) == 1) {
            $this->ajaxReturn(200, "删除成功！");
        } else {
            $this->ajaxReturn(0, "删除失败！");
        }
    }

    /**
     * 我的投递
     */
    public function my_delivery() {
        $where['personal_uid'] = C('visitor.uid');
        $personal_apply_mod = D('PersonalJobsApply');
        $apply_list = $personal_apply_mod->get_apply_jobs($where);
        $list['isfull'] = $apply_list['page_params']['isfull'];
        if($list['list'] = $apply_list['list']){
            $apply_list['page_params']['actualPage'] > $apply_list['page_params']['totalPages'] && $list['list'] = array();
            foreach ($apply_list['list'] as $key => $value){
            $map['id'] = $value['jobs_id'];
            $jobs = M('Jobs')->where($map)->find();
            if($jobs){
                $arr['id'] = $value['jobs_id'];
                $arr['did'] = $value['did'];
                $arr['jobs_name'] = $value['jobs_name'];
                $arr['wage_cn'] = $value['wage_cn'];
                $arr['company_name'] = $value['company_name'];
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
                if ($value['personal_look'] == '1') {
                    $arr['reply_status'] = 0;//"企业未查看";
                }else{
                    if ($value['is_reply'] == '0') {
                        $arr['reply_status'] = 1;//"待反馈";
                    }elseif ($value['is_reply'] == '1') {
                        $arr['reply_status'] = 2;//"合适";
                    }elseif ($value['is_reply'] == '2') {
                        $arr['reply_status'] = 3;//"不合适";
                    }elseif ($value['is_reply'] == '3') {
                        $arr['reply_status'] = 4;//"待定";
                    }elseif ($value['is_reply'] == '4') {
                        $arr['reply_status'] = 5;//"未接通";
                    }
                } 
                if($value['minwage'] == '0'){
                    $arr['wage_cn'] = "面议";
                }else{
                    $arr['wage_cn'] = $value['minwage'][0].'K-'.$value['maxwage'][0].'K';
                }
                $returnlist[] = $arr;
            }else{
                $array['id'] = $value['jobs_id'];
                $array['did'] = $value['did'];
                $array['tips'] = '该职位不存在或已被删除';
                $returnlist[] = $array;
            }
        }
        $returnarr['returnlist'] = $returnlist;
        $returnarr['nowPage'] = $apply_list['page_params']['nowPage'];
        $returnarr['totalPages'] = $apply_list['page_params']['totalPages'];
        $returnarr['isfull'] = $apply_list['page_params']['isfull'];
        $this->ajaxReturn(200, "申请的职位获取成功", $returnarr);
        }
        $this->ajaxReturn(0, "没有更多信息！",$returnarr);
    }

    /**
    * [jobs_apply_del 删除已申请职位]
    */
    public function jobs_apply_del() {
        $yid = I('request.did', '', 'trim,badword');
        !$yid && $this->ajaxReturn(0, "你没有选择项目！");
        $n = D('PersonalJobsApply')->del_jobs_apply($yid, C('visitor'));
        if ($n['state'] == 1) {
            $this->ajaxReturn(200, "删除成功！");
        } else {
            $this->ajaxReturn(0, "删除失败！");
        }
    }

    /**
    * 对我感兴趣
    */
    public function attention_me(){
        $where['resume_uid'] = C('visitor.uid');
        $view_list = D('ViewResume')->m_get_view_resume($where);
        $list['isfull'] = $view_list['page_params']['isfull'];
        if ($list['list'] = $view_list['list']) {
            $view_list['page_params']['actualPage'] > $view_list['page_params']['totalPages'] && $list['list'] = array();
            foreach ($view_list['list'] as $key => $value){
                $arr['company_id'] = $value['company_id'];
                if($arr['company_id'] !== NULL){
                    $arr['id'] = $value['id'];
                    $arr['company_uid'] = $value['uid'];
                    $arr['company_id'] = $value['company_id'];
                    $arr['companyname'] = $value['companyname'];
                    $arr['nature_cn'] = $value['nature_cn'];
                    $arr['scale_cn'] = $value['scale_cn'];
                    $arr['trade_cn'] = $value['trade_cn'];
                    $arr['addtime'] = date('Y-m-d',$value['addtime']);
                    $arr['hasdown'] = $value['hasdown'];
                    $returnlist[] = $arr;
                }else{
                    $array['id'] = $value['id'];
                    $array['tips'] = '该公司不存在';
                    $returnlist[] = $array;
                }
            }
                $returnarr['returnlist'] = $returnlist;
                $returnarr['nowPage'] = $view_list['page_params']['nowPage'];
                $returnarr['totalPages'] = $view_list['page_params']['totalPages'];
                $returnarr['isfull'] = $view_list['page_params']['isfull']; 
                $this->ajaxReturn(200, "简历被关注信息获取成功", $returnarr);
        }
            $this->ajaxReturn(0, "没有更多信息！",$returnarr);
    }

    /**
     * 删除谁在关注我
     */
    public function attention_me_del() {
        $yid = I('request.id', '', 'trim,badword');
        !$yid && $this->ajaxReturn(0, "你没有选择项目！");
        $reg = D('ViewResume')->personal_del_view_resume($yid);
        if ($reg['state'] == 1) {
            $this->ajaxReturn(200, "删除成功！");
        } else {
            $this->ajaxReturn(0, "删除失败！");
        }
    }

    /**
     * 我感兴趣的(职位收藏夹)
     */
    public function jobs_favorites() {
        $where['personal_uid'] = C('visitor.uid');
        $favorites = D('PersonalFavorites')->get_favorites($where);
        $list['isfull'] = $favorites['page_params']['isfull'];
        if ($list['list'] = $favorites['list']) {
            $favorites['page_params']['actualPage'] > $favorites['page_params']['totalPages'] && $list['list'] = array();
            foreach ($favorites['list'] as $key => $value) {
                $arr['company_id'] = $value['company_id'];
                if($arr['company_id'] !== NULL){
                $arr['did'] = $value['did'];
                $arr['jobs_id'] = $value['jobs_id'];
                $arr['jobs_name'] = $value['jobs_name'];
                if($value['minwage'] == '0'){
                    $arr['wage_cn'] = "面议";
                }else{
                    $arr['wage_cn'] = $value['minwage'].''.$value['maxwage'];
                }
                $arr['companyname'] = $value['companyname'];
                $now = date('Y');
                $nowyear = strtotime($now.'-01-01');
                $today = strtotime(date('Y-m-d'));
                $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
                $diftime = time() - $value['addtime'];
                if($value['addtime'] < $nowyear){
                    $arr['addtime'] = date('Y-m',$value['addtime']);
                }else{
                    if($diftime >= 5*60 && $diftime<= 60*60){
                        $arr['addtime'] = intval($diftime / 60) ."分钟前";
                    }else if($diftime >= 60*60 && $value['addtime']<= $tomorrow && $value['addtime'] >= $today){
                        $arr['addtime'] = intval($diftime / (60*60)) ."小时前";
                    }else if($diftime < 5*60){
                        $arr['addtime'] = "刚刚";
                    }else{
                        $arr['addtime'] = date('m-d',$value['addtime']);
                    }
                }
                $returnlist[] = $arr;
                }else{
                    $array['did'] = $value['did'];
                    $array['tips'] = '该公司不存在';
                    $returnlist[] = $array;
                }
            }
            $return['returnlist'] = $returnlist;
            $return['nowPage'] = $favorites['page_params']['nowPage'];
            $return['totalPages'] = $favorites['page_params']['totalPages'];
            $return['isfull'] = $favorites['page_params']['isfull']; 
            $this->ajaxReturn(200, "获取职位收藏夹成功", $return);
        }
        $this->ajaxReturn(0, "没有更多信息！",$return);
    }

    /**
     * 删除我感兴趣的(职位收藏夹)
     */
    public function jobs_favorites_del() {
        $did = I('request.id', '', 'intval,badword');
        !$did && $this->ajaxReturn(0, "你没有选择项目！");
        $reg = D('PersonalFavorites')->del_favorites($did, C('visitor'));
        if ($reg['state'] === true) {
            $this->ajaxReturn(200, "删除成功！");
        } else {
            $this->ajaxReturn(0, $reg['error']);
        }
    }

    //作品图片上传(百度)
    public function resume_img_upload(){
        if(!$_FILES){
                $this->ajaxReturn(1,'OPTIONS请求成功');
            }
            $savepath = date('ym/d/');
            $upload = new \Common\ORG\UploadFile();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->uploadReplace=true;//存在同名文件是否是覆盖 
            $upload->allowExts      =     array('png','gif','bmp','jpg','jpeg');// 设置附件上传类型
            $upload->rootPath  =     QSCMS_DATA_PATH.'upload/'; // 设置附件上传根目录
            $upload->savePath  =     QSCMS_DATA_PATH.'upload/resume_img/'.$savepath; // 设置附件上传（子）目录
            $upload->thumbPrefix = '';//缩略图的文件前缀，默认为thumb_
            $upload->thumbSuffix = '_thumb';//缩略图的文件后缀，默认为空 
            $upload->thumbExt = '';//指定缩略图的扩展名
            $upload->thumbRemoveOrigin = true;//生成缩略图后是否删除原图 
            $upload->saveRule = uniqid();
            // 上传文件 
            $info   =   $upload->uploadOne($_FILES['file']);
            if(!$info) {// 上传错误提示错误信息
                $this->ajaxReturn(400,$upload->getErrorMsg());
            }else{// 上传成功
                $return['savepath'] = $savepath.$info[0]['savename'];
                $return['attach'] = attach($savepath.$info[0]['savename'],'resume_img');
                $this->ajaxReturn(200,'上传成功！',$return);
            }
    }

    //简历头像上传(百度)
    public function photo_img_upload(){
        if(!$_FILES){
                $this->ajaxReturn(1,'OPTIONS请求成功');
            }
            $savepath = date('ym/d/');
            $upload = new \Common\ORG\UploadFile();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->uploadReplace=true;//存在同名文件是否是覆盖 
            $upload->allowExts      =     array('png','gif','bmp','jpg','jpeg');// 设置附件上传类型
            $upload->rootPath  =     QSCMS_DATA_PATH.'upload/'; // 设置附件上传根目录
            $upload->savePath  =     QSCMS_DATA_PATH.'upload/avatar/'.$savepath; // 设置附件上传（子）目录
            $upload->thumbPrefix = '';//缩略图的文件前缀，默认为thumb_
            $upload->thumbSuffix = '_thumb';//缩略图的文件后缀，默认为空 
            $upload->thumbExt = '';//指定缩略图的扩展名
            $upload->thumbRemoveOrigin = true;//生成缩略图后是否删除原图 
            $upload->saveRule = uniqid();
            // 上传文件 
            $info   =   $upload->uploadOne($_FILES['file']);
            if(!$info) {// 上传错误提示错误信息
                $this->ajaxReturn(400,$upload->getErrorMsg());
            }else{// 上传成功
                $info_avatar_data['photo_img'] = $savepath.$info[0]['savename'];
                $info_avatar_data['photo'] = 1;
                $map['id'] = C('visitor.pid');
                $map['uid'] = C('visitor.uid');
                $photo_img = M('Resume')->where($map)->save($info_avatar_data);
                $this->ajaxReturn(200,'上传成功！',$photo_img);
            }
    }

}