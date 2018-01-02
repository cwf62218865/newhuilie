<?php
/* *
 * $Author ��PHPYUN�����Ŷ�
 *
 * ����: http://www.phpyun.com
 *
 * ��Ȩ���� 2009-2016 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
 *
 * ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
/*
 * �Ƽ�������ز���
 */
class recommend_controller extends lietou{
    /*
     * ְλ�����������б�
     */
	function index_action(){
		$this->public_action();

        //		ְλ�����Ϣ
		$where=" `id`='".$_GET['id']."' ";
		$jobs=$this->obj->DB_select_once("company_job",$where);
        $logo = $this->obj->DB_select_once("company"," uid=".$jobs['uid'],"logo");
        $jobs['logo'] = $logo['logo'];

        // ��������
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
	 * �Ƽ�����
	 */
	function report_action(){
        if(!$_POST['job_id'] || !$_POST['resume_id']){
            $this->error_msg("��������");
        }

	    $job_id = $_POST['job_id'];
	    $resume_id = $_POST['resume_id'];
        $recommend = $this->obj->DB_select_once("userid_job","uid=".$this->uid." and resume_id=".$resume_id." and job_id=".$job_id);
        if($recommend){
            $this->error_msg("���Ƽ�");
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
                $this->success_msg("�Ƽ��ɹ�");
            }else{
                $this->error_msg("�Ƽ�ʧ��");
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
			echo json_encode(array('error'=>1,'msg'=>iconv('gbk','utf-8','�������������ԣ�')));
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
            		$msg = 'ˢ��ְλ����ʣ��' . $statis['breakjob_num'] . '����ȷ��ˢ�£�';
            		$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

            		$data['status'] = 1;
        		}           	
				else{
              		if( $this->config['com_integral_online'] ==1 ){
						if( $this->config['integral_jobefresh'] > 0 ){
							if( $this->config['integral_jobedit'] > $statis['integral'] ){
								$msg = $this->config['integral_pricename'] . '���㣬�޷�ˢ�£�';

								$data['status'] = 2;
							}
							else{
								$msg = '����ˢ����۳�' . $this->config['integral_jobefresh'] 
									. $this->config['integral_priceunit'] . $this->config['integral_pricename']
									. '��ȷ��ˢ�£�';
								$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

								$data['status'] = 1;
							}
						}
						else{
							$msg = 'ȷ��Ҫˢ�£�';
							$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

							$data['status'] = 1;
						}
					}
			  		else{
			   			$msg = 'ˢ�´��������꣬�Ƿ��ȹ�����Ȩ��';
			   			$data['url'] = 'index.php?c=right';

			   			$data['status'] = 3;
			 	 	}
			 	}
			}
			else{
				$msg = 'ȷ��ˢ�¸�ְλô��';
				$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

				$data['status'] = 1;
		    }
	    }
        else{
            if( $this->config['com_integral_online'] ==1 ){
				if( $this->config['integral_jobefresh'] >0) {
					$msg = '����ˢ����۳�' . $this->config['integral_jobefresh'] 
						. $this->config['integral_priceunit'] . $this->config['integral_pricename']
						. '��ȷ��ˢ�£�';
					$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

					$data['status'] = 1;
				}
				else{
					$msg = 'ȷ��ˢ�¸�ְλô��';
					$data['url'] = 'index.php?c=job&act=opera&up=' . $jobid;

					$data['status'] = 1;
				}
		    }
		    else{
	   			$msg = 'ˢ�´��������꣬�Ƿ��ȹ�����Ȩ��';
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