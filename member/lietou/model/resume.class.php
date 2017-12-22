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
class resume_controller extends company{
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
}