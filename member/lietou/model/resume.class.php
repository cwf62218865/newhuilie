<?php
/* *
 * $Author ：PHPYUN开发团队
 *
 * 官网: http://www.phpyun.com
 *
 * 版权所有 2009-2017 宿迁鑫潮信息技术有限公司，并保留所有权利。
 *
 * 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class resume_controller extends lietou{
//    function index_action(){
//		include(CONFIG_PATH."db.data.php");
//		unset($arr_data['sex'][3]);
//		$this->yunset("arr_data",$arr_data);
//        $uptime=array(1=>'今天',3=>'最近3天',7=>'最近7天',30=>'最近一个月',90=>'最近三个月');
//        $this->yunset('uptime',$uptime);
//        $CacheM=$this->MODEL('cache');
//        $CacheList=$CacheM->GetCache (array('city','user','job','hy'));
//        $date=date("Y",0);
//        $time=date("Y",time());
//        $this->yunset("date",$date);
//        $this->yunset("time",$time);
//        $this->yunset($CacheList);
//        $this->yunset("type",$_GET['type']);
//        $this->public_action();
//        $this->yunset("js_def",5);
//        $this->lt_tpl('resume');
//    }

    function index_action(){
        $this->industry_cache();
        $this->subject_cache();
        $resume = $this->obj->get_page();
        $this->yunset("js_def",5);
        $this->lt_tpl('hr');
    }

    function input_action(){
        $member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`uid`,`status`");
        $this->yunset("member",$member);
        $this->lt_tpl('input_resume');
    }

    function recently_action(){
        $this->industry_cache();
        $this->subject_cache();
        $where="`com_id`='".$this->uid."'";
        if(intval($_GET['resumetype'])){
            if(intval($_GET['resumetype'])==1){
                $resumeexp=$this->obj->DB_select_all("resume_expect","`r_status`<>'2'  and `height_status`<>2","`id`");
            }elseif(intval($_GET['resumetype'])==2){
                $resumeexp=$this->obj->DB_select_all("resume_expect","`r_status`<>'2' and `height_status`=2","`id`");
            }
            if(is_array($resumeexp) && !empty($resumeexp)){
                foreach($resumeexp as $v){
                    $reid[]=$v['id'];
                }
            }
            $where.=" and eid in (".pylode(',',$reid).")  ";
            $urlarr['resumetype']=intval($_GET['resumetype']);
        }
        if(trim($_GET['keyword'])){
            $resume=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".trim($_GET['keyword'])."%'","`name`,`edu`,`uid`,`exp`");
            if(is_array($resume) && !empty($resume)){
                foreach($resume as $v){
                    $uid[]=$v['uid'];
                }
            }
            $urlarr['keyword']=trim($_GET['keyword']);
            $where.=" and uid in (".pylode(',',$uid).")  ";
        }
        if($_GET['jobid']){
            $jobid=@explode('-', $_GET['jobid']);
            if (!array_key_exists('1', $jobid)) $jobid['1'] = 1;
            $where .=" and `job_id`=" . $jobid['0'] . " and `type`=" . $jobid['1'] . " ";
            $urlarr['jobid']=$_GET['jobid'];
        }
        if($_GET['state']){
            $where.=" and `is_browse`=".intval($_GET['state'])."  ";
            $urlarr['state']=$_GET['state'];
        }
        $this->public_action();
        $urlarr['c']="hr";
        $urlarr['page']="{{page}}";
        $pageurl=Url('member',$urlarr);
        $rows=$this->get_page("userid_job",$where." ORDER BY is_browse asc,datetime desc",$pageurl,"10");

        $jobs2=$this->obj->DB_select_all('company_job','`uid`='.$this->uid,"`id`,`name`");
        foreach ($jobs2 as $key=>$val){
            $jobs2[$key]['type']=1;
        }
        $JobList=$jobs2;
        if(is_array($rows) && !empty($rows)){
            $uid=$eid=array();
            foreach($rows as $val){
                $eid[]=$val['eid'];
                $uid[]=$val['uid'];
            }
            if(empty($resume)){
                $resume=$this->obj->DB_select_all("resume","`r_status`<>'2'  and `uid` in (".pylode(",",$uid).")","`name`,`edu`,`uid`,`exp`");
            }
            $expect=$this->obj->DB_select_all("resume_expect","`id` in (".pylode(",",$eid).")","`id`,`job_classid`,`salary`,`height_status`");
            $userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".pylode(",",$uid).")","uid,jobid");
            if(is_array($resume)){
                include(PLUS_PATH."user.cache.php");
                include(PLUS_PATH."job.cache.php");
                $expectinfo=array();
                foreach($expect as $key=>$val){
                    $jobids=@explode(',',$val['job_classid']);
                    $jobname=array();
                    foreach($jobids as $k=>$v){
                        if($k<5){
                            $jobname[]=$job_name[$v];
                        }
                    }
                    $expectinfo[$val['id']]['jobname']=@implode('、',$jobname);
                    $expectinfo[$val['id']]['salary']=$userclass_name[$val['salary']];
                    $expectinfo[$val['id']]['height_status']=$val['height_status'];
                }
                foreach($rows as $k=>$v){
                    $rows[$k]['jobname']=$expectinfo[$v['eid']]['jobname'];
                    $rows[$k]['salary']=$expectinfo[$v['eid']]['salary'];
                    $rows[$k]['height_status']=$expectinfo[$v['eid']]['height_status'];
                    foreach($resume as $val){
                        if($v['uid']==$val['uid']){
                            $rows[$k]['name']=$val['name'];
                            $rows[$k]['edu']=$userclass_name[$val['edu']];
                            $rows[$k]['exp']=$userclass_name[$val['exp']];
                        }
                    }
                    foreach($userid_msg as $val){
                        if($v['uid']==$val['uid'] && $val['jobid']==$v['job_id']){
                            $rows[$k]['userid_msg']=1;
                        }
                    }
                }
            }
            $jobnum=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."'");
        }
        if($JobList&&is_array($JobList)&&$jobid['0']){
            foreach($JobList as $val){
                if($jobid['0']==$val['id']){
                    $current=$val;
                }
            }
        }
        $JobM=$this->MODEL("job");
        $company_job=$JobM->GetComjobList(array("uid"=>$this->uid,"state"=>1,"`edate`>'".time()."' and `r_status`<>'2' and `status`<>'1'"),array("field"=>"`name`,`id`"));
        $this->yunset("company_job",$company_job);
        $this->yunset(array('current'=>$current,'rows'=>$rows,'JobList'=>$JobList,'StateList'=>array(array('id'=>1,'name'=>'未查看'),array('id'=>2,'name'=>'已查看'),array('id'=>3,'name'=>'等待通知'),array('id'=>4,'name'=>'条件不符'),array('id'=>5,'name'=>'无法联系'))));
        $this->company_satic();
        $this->yunset("js_def",5);
        $this->yunset("jobnum",$jobnum);
        $this->lt_tpl('hr');
    }

    function recommend_action(){
        $this->industry_cache();
        $this->subject_cache();
        $where="`com_id`='".$this->uid."'";
        if(intval($_GET['resumetype'])){
            if(intval($_GET['resumetype'])==1){
                $resumeexp=$this->obj->DB_select_all("resume_expect","`r_status`<>'2'  and `height_status`<>2","`id`");
            }elseif(intval($_GET['resumetype'])==2){
                $resumeexp=$this->obj->DB_select_all("resume_expect","`r_status`<>'2' and `height_status`=2","`id`");
            }
            if(is_array($resumeexp) && !empty($resumeexp)){
                foreach($resumeexp as $v){
                    $reid[]=$v['id'];
                }
            }
            $where.=" and eid in (".pylode(',',$reid).")  ";
            $urlarr['resumetype']=intval($_GET['resumetype']);
        }
        if(trim($_GET['keyword'])){
            $resume=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".trim($_GET['keyword'])."%'","`name`,`edu`,`uid`,`exp`");
            if(is_array($resume) && !empty($resume)){
                foreach($resume as $v){
                    $uid[]=$v['uid'];
                }
            }
            $urlarr['keyword']=trim($_GET['keyword']);
            $where.=" and uid in (".pylode(',',$uid).")  ";
        }
        if($_GET['jobid']){
            $jobid=@explode('-', $_GET['jobid']);
            if (!array_key_exists('1', $jobid)) $jobid['1'] = 1;
            $where .=" and `job_id`=" . $jobid['0'] . " and `type`=" . $jobid['1'] . " ";
            $urlarr['jobid']=$_GET['jobid'];
        }
        if($_GET['state']){
            $where.=" and `is_browse`=".intval($_GET['state'])."  ";
            $urlarr['state']=$_GET['state'];
        }
        $this->public_action();
        $urlarr['c']="hr";
        $urlarr['page']="{{page}}";
        $pageurl=Url('member',$urlarr);
        $rows=$this->get_page("userid_job",$where." ORDER BY is_browse asc,datetime desc",$pageurl,"10");

        $jobs2=$this->obj->DB_select_all('company_job','`uid`='.$this->uid,"`id`,`name`");
        foreach ($jobs2 as $key=>$val){
            $jobs2[$key]['type']=1;
        }
        $JobList=$jobs2;
        if(is_array($rows) && !empty($rows)){
            $uid=$eid=array();
            foreach($rows as $val){
                $eid[]=$val['eid'];
                $uid[]=$val['uid'];
            }
            if(empty($resume)){
                $resume=$this->obj->DB_select_all("resume","`r_status`<>'2'  and `uid` in (".pylode(",",$uid).")","`name`,`edu`,`uid`,`exp`");
            }
            $expect=$this->obj->DB_select_all("resume_expect","`id` in (".pylode(",",$eid).")","`id`,`job_classid`,`salary`,`height_status`");
            $userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".pylode(",",$uid).")","uid,jobid");
            if(is_array($resume)){
                include(PLUS_PATH."user.cache.php");
                include(PLUS_PATH."job.cache.php");
                $expectinfo=array();
                foreach($expect as $key=>$val){
                    $jobids=@explode(',',$val['job_classid']);
                    $jobname=array();
                    foreach($jobids as $k=>$v){
                        if($k<5){
                            $jobname[]=$job_name[$v];
                        }
                    }
                    $expectinfo[$val['id']]['jobname']=@implode('、',$jobname);
                    $expectinfo[$val['id']]['salary']=$userclass_name[$val['salary']];
                    $expectinfo[$val['id']]['height_status']=$val['height_status'];
                }
                foreach($rows as $k=>$v){
                    $rows[$k]['jobname']=$expectinfo[$v['eid']]['jobname'];
                    $rows[$k]['salary']=$expectinfo[$v['eid']]['salary'];
                    $rows[$k]['height_status']=$expectinfo[$v['eid']]['height_status'];
                    foreach($resume as $val){
                        if($v['uid']==$val['uid']){
                            $rows[$k]['name']=$val['name'];
                            $rows[$k]['edu']=$userclass_name[$val['edu']];
                            $rows[$k]['exp']=$userclass_name[$val['exp']];
                        }
                    }
                    foreach($userid_msg as $val){
                        if($v['uid']==$val['uid'] && $val['jobid']==$v['job_id']){
                            $rows[$k]['userid_msg']=1;
                        }
                    }
                }
            }
            $jobnum=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."'");
        }
        if($JobList&&is_array($JobList)&&$jobid['0']){
            foreach($JobList as $val){
                if($jobid['0']==$val['id']){
                    $current=$val;
                }
            }
        }
        $JobM=$this->MODEL("job");
        $company_job=$JobM->GetComjobList(array("uid"=>$this->uid,"state"=>1,"`edate`>'".time()."' and `r_status`<>'2' and `status`<>'1'"),array("field"=>"`name`,`id`"));
        $this->yunset("company_job",$company_job);
        $this->yunset(array('current'=>$current,'rows'=>$rows,'JobList'=>$JobList,'StateList'=>array(array('id'=>1,'name'=>'未查看'),array('id'=>2,'name'=>'已查看'),array('id'=>3,'name'=>'等待通知'),array('id'=>4,'name'=>'条件不符'),array('id'=>5,'name'=>'无法联系'))));
        $this->company_satic();
        $this->yunset("js_def",5);
        $this->yunset("jobnum",$jobnum);
        $this->lt_tpl('hr');
    }

    function myself_action(){
        $this->industry_cache();
        $this->subject_cache();
        $where="`com_id`='".$this->uid."'";
        if(intval($_GET['resumetype'])){
            if(intval($_GET['resumetype'])==1){
                $resumeexp=$this->obj->DB_select_all("resume_expect","`r_status`<>'2'  and `height_status`<>2","`id`");
            }elseif(intval($_GET['resumetype'])==2){
                $resumeexp=$this->obj->DB_select_all("resume_expect","`r_status`<>'2' and `height_status`=2","`id`");
            }
            if(is_array($resumeexp) && !empty($resumeexp)){
                foreach($resumeexp as $v){
                    $reid[]=$v['id'];
                }
            }
            $where.=" and eid in (".pylode(',',$reid).")  ";
            $urlarr['resumetype']=intval($_GET['resumetype']);
        }
        if(trim($_GET['keyword'])){
            $resume=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".trim($_GET['keyword'])."%'","`name`,`edu`,`uid`,`exp`");
            if(is_array($resume) && !empty($resume)){
                foreach($resume as $v){
                    $uid[]=$v['uid'];
                }
            }
            $urlarr['keyword']=trim($_GET['keyword']);
            $where.=" and uid in (".pylode(',',$uid).")  ";
        }
        if($_GET['jobid']){
            $jobid=@explode('-', $_GET['jobid']);
            if (!array_key_exists('1', $jobid)) $jobid['1'] = 1;
            $where .=" and `job_id`=" . $jobid['0'] . " and `type`=" . $jobid['1'] . " ";
            $urlarr['jobid']=$_GET['jobid'];
        }
        if($_GET['state']){
            $where.=" and `is_browse`=".intval($_GET['state'])."  ";
            $urlarr['state']=$_GET['state'];
        }
        $this->public_action();
        $urlarr['c']="hr";
        $urlarr['page']="{{page}}";
        $pageurl=Url('member',$urlarr);
        $rows=$this->get_page("userid_job",$where." ORDER BY is_browse asc,datetime desc",$pageurl,"10");

        $jobs2=$this->obj->DB_select_all('company_job','`uid`='.$this->uid,"`id`,`name`");
        foreach ($jobs2 as $key=>$val){
            $jobs2[$key]['type']=1;
        }
        $JobList=$jobs2;
        if(is_array($rows) && !empty($rows)){
            $uid=$eid=array();
            foreach($rows as $val){
                $eid[]=$val['eid'];
                $uid[]=$val['uid'];
            }
            if(empty($resume)){
                $resume=$this->obj->DB_select_all("resume","`r_status`<>'2'  and `uid` in (".pylode(",",$uid).")","`name`,`edu`,`uid`,`exp`");
            }
            $expect=$this->obj->DB_select_all("resume_expect","`id` in (".pylode(",",$eid).")","`id`,`job_classid`,`salary`,`height_status`");
            $userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".pylode(",",$uid).")","uid,jobid");
            if(is_array($resume)){
                include(PLUS_PATH."user.cache.php");
                include(PLUS_PATH."job.cache.php");
                $expectinfo=array();
                foreach($expect as $key=>$val){
                    $jobids=@explode(',',$val['job_classid']);
                    $jobname=array();
                    foreach($jobids as $k=>$v){
                        if($k<5){
                            $jobname[]=$job_name[$v];
                        }
                    }
                    $expectinfo[$val['id']]['jobname']=@implode('、',$jobname);
                    $expectinfo[$val['id']]['salary']=$userclass_name[$val['salary']];
                    $expectinfo[$val['id']]['height_status']=$val['height_status'];
                }
                foreach($rows as $k=>$v){
                    $rows[$k]['jobname']=$expectinfo[$v['eid']]['jobname'];
                    $rows[$k]['salary']=$expectinfo[$v['eid']]['salary'];
                    $rows[$k]['height_status']=$expectinfo[$v['eid']]['height_status'];
                    foreach($resume as $val){
                        if($v['uid']==$val['uid']){
                            $rows[$k]['name']=$val['name'];
                            $rows[$k]['edu']=$userclass_name[$val['edu']];
                            $rows[$k]['exp']=$userclass_name[$val['exp']];
                        }
                    }
                    foreach($userid_msg as $val){
                        if($v['uid']==$val['uid'] && $val['jobid']==$v['job_id']){
                            $rows[$k]['userid_msg']=1;
                        }
                    }
                }
            }
            $jobnum=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."'");
        }
        if($JobList&&is_array($JobList)&&$jobid['0']){
            foreach($JobList as $val){
                if($jobid['0']==$val['id']){
                    $current=$val;
                }
            }
        }
        $JobM=$this->MODEL("job");
        $company_job=$JobM->GetComjobList(array("uid"=>$this->uid,"state"=>1,"`edate`>'".time()."' and `r_status`<>'2' and `status`<>'1'"),array("field"=>"`name`,`id`"));
        $this->yunset("company_job",$company_job);
        $this->yunset(array('current'=>$current,'rows'=>$rows,'JobList'=>$JobList,'StateList'=>array(array('id'=>1,'name'=>'未查看'),array('id'=>2,'name'=>'已查看'),array('id'=>3,'name'=>'等待通知'),array('id'=>4,'name'=>'条件不符'),array('id'=>5,'name'=>'无法联系'))));
        $this->company_satic();
        $this->yunset("js_def",5);
        $this->yunset("jobnum",$jobnum);
        $this->lt_tpl('hr');
    }


    function add_action(){

        $_POST = $this->array_iconv("utf-8","gbk",$_POST);
//        var_dump($_POST);exit();
        $data['name'] = $_POST['name']?$_POST['name']:$this->error_msg("请输入姓名");
        $data['sex'] = $_POST['sex']?$_POST['sex']:$this->error_msg("请选择性别");
        $data['email'] = $_POST['email'];
        $data['birthday'] = $_POST['birthDayStr'];
        $data['living'] =  $_POST['cityId'];
        $data['edu'] = $_POST['degree'];
        $data['telphone'] = $_POST['mobile']?$_POST['mobile']:$this->error_msg("请输入手机号");
        $data['uid'] = $this->uid;
        $resume_id = $this->obj->insert_into("resume",$data);       //id 作为后面附表的uid

        if($resume_id){

            if($_POST['intent']){
                $data = "";
                $data['resume_id'] = $resume_id;
                $data['uid'] = $this->uid;
                $data['job_classid']= $_POST['intent']['hopeCallings'];
                $data['intention_city'] = $_POST['intent']['hopeCitys'];
                $data['hy'] = $_POST['intent']['hopeIndustry'];
                $data['wage_hope'] = $_POST['intent']['curMoney'];
                $data['moneyMonthes'] = $_POST['intent']['moneyMonthes'];
                $data['wage_current'] = $_POST['intent']['hopeMoney'];
                $data['jobstatus'] = $_POST['jobState'];
                $data['lastupdate'] = time();
                $data['ctime'] = time();
                $data['uname'] = $_POST['name'];
                $data['r_status'] = 1;
                $data['defaults'] =1;
                $data['birthday'] = $_POST['birthDayStr'];
                $resume_eid = $this->obj->insert_into("resume_expect",$data);   //id 作为后面附表的eid

                if($resume_eid){

                    if($_POST['workExp']){
                        foreach ($_POST['workExp'] as $list){
                            $data = "";
                            $data['resume_id'] = $resume_id;
                            $data['uid'] = $this->uid;
                            $data['eid'] = $resume_eid;
                            $data['name'] = $list['companyName'];
                            $data['title'] = $list['posName'];
                            $data['sdate'] = strtotime($list['startDateStr']);
                            $data['edate'] = strtotime($list['endDateStr']);
                            $data['content'] = $list['workDes'];
                            $this->obj->insert_into("resume_work",$data);
                        }

                    }

                    if($_POST['eduExps']){
                        foreach ($_POST['workExp'] as $list){
                            $data = "";
                            $data['resume_id'] = $resume_id;
                            $data['uid'] = $this->uid;
                            $data['eid'] = $resume_eid;
                            $data['name'] = $list['name'];
                            $data['title'] = $list['title'];
                            $data['sdate'] = strtotime($list['sdate']);
                            $data['edate'] = strtotime($list['edate']);
                            $data['specialty'] = $list['specialty'];
                            $this->obj->insert_into("resume_edu",$data);
                        }
                    }

                    if($_POST['proExp']){
                        foreach ($_POST['proExp'] as $list){
                            $data = "";
                            $data['resume_id'] = $resume_id;
                            $data['uid'] = $this->uid;
                            $data['eid'] = $resume_eid;
                            $data['name'] = $list['proName'];
                            $data['title'] = $list['title'];
                            $data['sdate'] = strtotime($list['startDateStr']);
                            $data['edate'] = strtotime($list['endDateStr']);
                            $data['content'] = $list['content'];
                            $this->obj->insert_into("resume_project",$data);
                        }

                    }
                }
            }


            $this->success_msg("添加成功");
        }else{
            $this->error_msg("添加失败");
        }
//        $data['current'] = $_POST['jobState'];
//        $data['degree'] = $_POST['degree'];
//        $data['intention_jobs'] = $_POST['intent']['hopeCallings'];
//        $data['intention_city'] = $_POST['intent']['hopeCitys'];
//        $data['wage_hope'] = $_POST['intent']['curMoney'];
//        $data['moneyMonthes'] = $_POST['intent']['moneyMonthes'];
//        $data['wage_current'] = $_POST['intent']['hopeMoney'];
//        $data['additions'] = $_POST['extra']['extraInfo'];
//        $data['introduce'] = $_POST['extra']['selfEvaluation'];
//        $data['uid'] = $_POST['hunterId'];
//        var_dump($data);exit();
//        $data['proExp'] = $_POST['proExp'];
//        $data['workExp'] = $_POST['workExp'];
//        $data['proExp'] = $_POST['proExp'];
//        $r = $this->obj->insert_into("pt_resume",$data);
//        if($r){
//            echo 1;exit();
//        }
//        var_dump($data);exit();
    }
}