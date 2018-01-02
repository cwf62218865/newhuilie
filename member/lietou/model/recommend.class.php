<?php
/* *
 * $Author ：PHPYUN开发团队
 *
 * 官网: http://www.phpyun.com
 *
 * 版权所有 2009-2016 宿迁鑫潮信息技术有限公司，并保留所有权利。
 *
 * 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
/*
 * 推荐简历相关操作
 */
class recommend_controller extends lietou{
    /*
     * 职位，简历关联列表
     */
	function index_action(){
		$this->public_action();

        //		职位相关信息
		$where=" `id`='".$_GET['id']."' ";
		$jobs=$this->obj->DB_select_once("company_job",$where);
        $logo = $this->obj->DB_select_once("company"," uid=".$jobs['uid'],"logo");
        $jobs['logo'] = $logo['logo'];

        // 简历搜索
        $resume=$this->obj->DB_select_all("resume","`r_status`<>'2' ","`name`,`edu`,`uid`,`exp`");
        $expect=$this->obj->DB_select_all("resume_expect","`id` in (".pylode(",",$eid).")","`id`,`job_classid`,`salary`,`height_status`");

        $this->yunset("resume",$resume);
		$this->yunset("jobs",$jobs);
		$this->yunset("js_def",3);
		if(intval($_GET['w'])==1){
			$this->lt_tpl('joblist');
		}else{
			$this->lt_tpl('recommend');
		}
	}

	/*
	 * 推荐简历
	 */
	function report_action(){
        if(!$_POST['job_id'] || !$_POST['resume_id']){
            $this->error_msg("参数错误");
        }

	    $job_id = $_POST['job_id'];
	    $resume_id = $_POST['resume_id'];
        $recommend = $this->obj->DB_select_once("userid_job","uid=".$this->uid." and resume_id=".$resume_id." and job_id=".$job_id);
        if($recommend){
            $this->error_msg("已推荐");
        }else{
            $job = $this->job_more($job_id);
            $data['job_id'] = $job_id;
            $data['job_name'] = $job['name'];
            $data['com_id'] = $job['uid'];
            $data['com_name'] = $job['com_name'];
            $data['uid'] = $this->uid;
            $data['resume_id'] = $resume_id;
            $data['identity'] = 3;
            $data['datetime'] = time();
            $r = $this->obj->insert_into("userid_job",$data);
            if($r){
                $this->success_msg("推荐成功");
            }else{
                $this->error_msg("推荐失败");
            }

        }
    }



	function opera_action(){
		$this->job();
	}

	function buyJob_action(){
		if($_POST){
			$M=$this->MODEL('compay');
			if ($_POST['jobautoids']){
				$return = $M->buyAutoJob($_POST);
			}elseif ($_POST['zdjobid']){
				$return = $M->buyZdJob($_POST);
			}elseif ($_POST['recjobid']){
				$return = $M->buyRecJob($_POST);
			}elseif ($_POST['ujobid']){
				$return = $M->buyUrgentJob($_POST);
			}

			if($return['order']['order_id'] && $return['order']['id']){
				echo json_encode(array('error'=>0,'orderid'=>$return['order']['order_id'],'id'=>$return['order']['id']));
			}else{
				echo json_encode(array('error'=>1,'msg'=>iconv('gbk','utf-8',$return['error'])));
			}
		}else{
			echo json_encode(array('error'=>1,'msg'=>iconv('gbk','utf-8','参数错误，请重试！')));
		}
	}

	function bidding_action(){

		$where="`uid`='".$this->uid."' and `xsdate`>'".time()."'";
		$urlarr=array("c"=>"job",'act'=>'bidding',"page"=>"{{page}}");
		if(trim($_GET['keyword'])){
			$where.=" and `name` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		$pageurl=Url('member',$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,"10");
		$this->yunset("rows",$rows);
		$this->yunset("js_def",3);
		$this->company_satic();
		$this->lt_tpl('bidding');
	}
	function refresh_action(){
		$nid=$this->obj->DB_update_all("company_job","`lastupdate`='".time()."'","`uid`='".$this->uid."' and `id`='".(int)$_POST['id']."'");
		if($nid){
			$this->obj->DB_update_all("company","`lastupdate`='".time()."'","`uid`='".$this->uid."'");
			echo 1;
		}
	}


	function ajax_refresh_job_action()
	{
		if(!isset($_POST['jobid'])){
			exit;
		}

		$jobid = $_POST['jobid'];
		
		$statis = $this->company_satic();

		$msg = '';
		
		if( $statis['vip_etime'] >time() || $statis['vip_etime'] ==0 ){
        	if( $statis['rating_type'] == 1 ){
            	if( $statis['breakjob_num'] > 0 ){
            		$msg = '刷新职位数还剩余' . $statis['breakjob_num'] . '个！确认刷新？';
            		$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

            		$data['status'] = 1;
        		}           	
				else{
              		if( $this->config['com_integral_online'] ==1 ){
						if( $this->config['integral_jobefresh'] > 0 ){
							if( $this->config['integral_jobedit'] > $statis['integral'] ){
								$msg = $this->config['integral_pricename'] . '不足，无法刷新！';

								$data['status'] = 2;
							}
							else{
								$msg = '本次刷新需扣除' . $this->config['integral_jobefresh'] 
									. $this->config['integral_priceunit'] . $this->config['integral_pricename']
									. '！确认刷新？';
								$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

								$data['status'] = 1;
							}
						}
						else{
							$msg = '确认要刷新？';
							$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

							$data['status'] = 1;
						}
					}
			  		else{
			   			$msg = '刷新次数已用完，是否先购买特权？';
			   			$data['url'] = 'index.php?c=right';

			   			$data['status'] = 3;
			 	 	}
			 	}
			}
			else{
				$msg = '确定刷新该职位么？';
				$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

				$data['status'] = 1;
		    }
	    }
        else{
            if( $this->config['com_integral_online'] ==1 ){
				if( $this->config['integral_jobefresh'] >0) {
					$msg = '本次刷新需扣除' . $this->config['integral_jobefresh'] 
						. $this->config['integral_priceunit'] . $this->config['integral_pricename']
						. '！确认刷新？';
					$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

					$data['status'] = 1;
				}
				else{
					$msg = '确定刷新该职位么？';
					$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

					$data['status'] = 1;
				}
		    }
		    else{
	   			$msg = '刷新次数已用完，是否先购买特权？';
	   			$data['url'] = 'index.php?c=right';

	   			$data['status'] = 3;
		    }
	    }

		
		$data['msg'] = iconv("gbk", "utf-8", $msg);
		echo json_encode($data);
		exit;
	}
}
?>