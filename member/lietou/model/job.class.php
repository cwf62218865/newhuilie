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
class job_controller extends lietou{

    function index_action(){
        global $config;

		$this->public_action();
        $this->industry_cache();
        $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
        $this->com_cache();

        $uptime=array('1'=>'����','3'=>'���3��','7'=>'���7��','30'=>'���һ����','90'=>'���������');
        $this->yunset("uptime",$uptime);
		$urlarr=array("c"=>"job","page"=>"{{page}}");
		$where="1";

		$pageurl=Url('member',$urlarr);
//		$rows=$this->get_page("company_job",$where,$pageurl,'10');

        $page=$_GET['page']<1?1:$_GET['page'];
        $ststrsql=($page-1)*10;
        $num=$this->obj->DB_select_num("company_job",$where);
        $this->yunset("total",$num);
        if($num>10){
            $pages=ceil($num/10);
            $pagenav=Page($page,$num,10,$pageurl,$notpl=false,$this->tpl,"pagenav");
            $this->yunset("pages",$pages);
        }

        $rows=$this->obj->DB_select_all(company_job,"$where limit $ststrsql,10");
        $jobs = $this->jobs_parse($rows);


        $this->yunset("rows",$jobs);
//        return $rows;

		if(is_array($rows) && !empty($rows)){
			$jobids=array();
			foreach($rows as $v){
				$jobids[]=$v['id'];
			}
			$jobnum=$this->obj->DB_select_all("userid_job","`job_id` in(".pylode(',',$jobids).") and `com_id`='".$this->uid."' GROUP BY `job_id`","`job_id`,count(`id`) as `num`");
			foreach($rows as $k=>$v){
				if($v['autotime']>time()){
					$rows[$k]['autodate']=date("Y-m-d",$v['autotime']);
				}
				if($v['xsdate']>time()){
					$rows[$k]['xs']=1;
				}
				$rows[$k]['jobnum']=0;
				foreach($jobnum as $val){
					if($v['id']==$val['job_id']){
						$rows[$k]['jobnum']=$val['num'];
					}
				}
				$rows[$k]['type']=1;
			}
		}
		
		$maxfen=$this->obj->DB_select_once("company_job","`state`='1' and `sdate`<'".mktime()."' and `r_status`<>'2' and `edate`>'".mktime()."' order by `xuanshang` desc",'xuanshang');
		$urgent=$this->config['com_urgent'];
			
		$audit=$this->obj->DB_select_num("company_job","`uid`='".$this->uid."' and `state`=0");
		$this->yunset("audit",$audit);
		$this->yunset("urgent",$urgent);
		$this->yunset("maxfen",$maxfen);
        $this->yunset("js_def",3);
		$this->company_satic();
        $this->lt_tpl('joblist');
//		if(intval($_GET['w'])==1){
//			$this->lt_tpl('joblist');
//		}else{
//			$this->lt_tpl('job');
//		}
	}


	//�����е�ְλ
        function serving_job_action(){

            $this->industry_cache();

            $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));

            $urlarr=array("c"=>"favorite","page"=>"{{page}}");
            $StateNameList=array('0'=>'�ȴ����','1'=>'��Ƹ��','2'=>'�ѽ���','3'=>'δͨ��');
            $StatusNameList = array('1' => '���¼�', '2' => '��Ƹ��');//�¸��汾���Ǻϲ� company_job ��state��status�����ֶ�

            $uptime=array('1'=>'����','3'=>'���3��','7'=>'���7��','30'=>'���һ����','90'=>'���������');
            $this->yunset("uptime",$uptime);

            $pageurl=Url('member',$urlarr);
            $rows=$this->get_page("fav_job","`uid`='".$this->uid."' order by id desc",$pageurl,"20");

            if($rows&&is_array($rows)){
                include PLUS_PATH."/lt.cache.php";
                include PLUS_PATH."/com.cache.php";
                foreach($rows as $val){
                    if($val['type']==1){
                        $com_jobid[]=$val['job_id'];
                    }else{
                        $lt_jobid[]=$val['job_id'];
                    }
                }
                $lt_job=$this->obj->DB_select_all("lt_job","`id` in(".pylode(',',$lt_jobid).")","`id`,`minsalary`,`maxsalary`,`provinceid`,`cityid`,`status`");
                $company_job=$this->obj->DB_select_all("company_job","`id` in(".pylode(',',$com_jobid).")","`id`,`minsalary`,`maxsalary`,`provinceid`,`cityid`,`state`,`status`");
                foreach($rows as $key=>$val){

                    $rows[$key]['statename']='�ѹر�';
                    foreach($company_job as $v){
                        if($val['job_id']==$v['id']){
                            $rows[$key]['minsalary']=$v['minsalary'];
                            $rows[$key]['maxsalary']=$v['maxsalary'];
                            $rows[$key]['provinceid']=$v['provinceid'];
                            $rows[$key]['cityid']=$v['cityid'];
                            $rows[$key]['statename']=$StateNameList[$v['state']];
                            if($v['status'] == 1){
                                $rows[$key]['statename']= '���¼�';
                            }

                        }
                    }
                    foreach($lt_job as $v){
                        if($val['job_id']==$v['id']){
                            $rows[$key]['minsalary']=$v['minsalary'];
                            $rows[$key]['maxsalary']=$v['maxsalary'];
                            $rows[$key]['provinceid']=$v['provinceid'];
                            $rows[$key]['cityid']=$v['cityid'];
                            $rows[$key]['statename']=$StateNameList[$v['status']];
                        }
                    }
                }
            }


            $num=$this->obj->DB_select_num("fav_job","`uid`='".$this->uid."'");
            $this->obj->DB_update_all("member_statis","fav_jobnum='".$num."'","`uid`='".$this->uid."'");
            $this->yunset("rows",$rows);
            $this->yunset("js_def",3);
            $this->lt_tpl('serving_job');
        }

    //�ղ�ְλ
    function fav_job_action(){

        $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
        $this->industry_cache();

        $urlarr=array("c"=>"favorite","page"=>"{{page}}");
        $StateNameList=array('0'=>'�ȴ����','1'=>'��Ƹ��','2'=>'�ѽ���','3'=>'δͨ��');
        $StatusNameList = array('1' => '���¼�', '2' => '��Ƹ��');//�¸��汾���Ǻϲ� company_job ��state��status�����ֶ�

        $uptime=array('1'=>'����','3'=>'���3��','7'=>'���7��','30'=>'���һ����','90'=>'���������');
        $this->yunset("uptime",$uptime);

        $pageurl=Url('member',$urlarr);
        $rows=$this->get_page("fav_job","`uid`='".$this->uid."' order by id desc",$pageurl,"20");

        if($rows&&is_array($rows)){
            include PLUS_PATH."/lt.cache.php";
            include PLUS_PATH."/com.cache.php";
            foreach($rows as $val){
                if($val['type']==1){
                    $com_jobid[]=$val['job_id'];
                }else{
                    $lt_jobid[]=$val['job_id'];
                }
            }
            $lt_job=$this->obj->DB_select_all("lt_job","`id` in(".pylode(',',$lt_jobid).")","`id`,`minsalary`,`maxsalary`,`provinceid`,`cityid`,`status`");
            $company_job=$this->obj->DB_select_all("company_job","`id` in(".pylode(',',$com_jobid).")","`id`,`minsalary`,`maxsalary`,`provinceid`,`cityid`,`state`,`status`");
            foreach($rows as $key=>$val){

                $rows[$key]['statename']='�ѹر�';
                foreach($company_job as $v){
                    if($val['job_id']==$v['id']){
                        $rows[$key]['minsalary']=$v['minsalary'];
                        $rows[$key]['maxsalary']=$v['maxsalary'];
                        $rows[$key]['provinceid']=$v['provinceid'];
                        $rows[$key]['cityid']=$v['cityid'];
                        $rows[$key]['statename']=$StateNameList[$v['state']];
                        if($v['status'] == 1){
                            $rows[$key]['statename']= '���¼�';
                        }

                    }
                }
                foreach($lt_job as $v){
                    if($val['job_id']==$v['id']){
                        $rows[$key]['minsalary']=$v['minsalary'];
                        $rows[$key]['maxsalary']=$v['maxsalary'];
                        $rows[$key]['provinceid']=$v['provinceid'];
                        $rows[$key]['cityid']=$v['cityid'];
                        $rows[$key]['statename']=$StateNameList[$v['status']];
                    }
                }
            }
        }


        $num=$this->obj->DB_select_num("fav_job","`uid`='".$this->uid."'");
        $this->obj->DB_update_all("member_statis","fav_jobnum='".$num."'","`uid`='".$this->uid."'");
        $this->yunset("rows",$rows);
        $this->yunset("js_def",3);
        $this->lt_tpl('favorite');
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