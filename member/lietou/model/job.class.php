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



        if($_GET['hy']){
            $where .= " and hy=".intval($_GET['hy']);
        }elseif ($_GET['city']){
            $where .= " and provinceid=".intval($_GET['city']);
        }elseif ($_GET['salary']){

        }elseif ($_GET['uptime']){

        }elseif ($_GET['keyword']){
            $where .= " and (name like '%".$_GET['keyword']."%' or com_name like '%".$_GET['keyword']."%')";
        }

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
	}


	//�����е�ְλ
        function serving_job_action(){
            global $config;
            $this->public_action();
            $this->industry_cache();
            $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
            $this->com_cache();
            $uptime=array('1'=>'����','3'=>'���3��','7'=>'���7��','30'=>'���һ����','90'=>'���������');
            $this->yunset("uptime",$uptime);
            $urlarr=array("c"=>"job","page"=>"{{page}}");


            $pageurl=Url('member',$urlarr);
            $page=$_GET['page']<1?1:$_GET['page'];
            $ststrsql=($page-1)*10;
            $num=$this->obj->DB_select_num("userid_job","identity=3 and uid=".$this->uid);
            $userid_job=$this->obj->DB_select_all("userid_job","uid=".$this->uid,"job_id");
            foreach ($userid_job as $li){
                $job_ids[] = $li['job_id'];
            }
            $job_ids = implode(",",$job_ids);
            $this->yunset("total",$num);
            if($num>10){
                $pages=ceil($num/10);
                $pagenav=Page($page,$num,10,$pageurl,$notpl=false,$this->tpl,"pagenav");
                $this->yunset("pages",$pages);
            }

            $where="id in(".$job_ids.")";
            if($_GET['hy']){
                $where .= " and hy=".intval($_GET['hy']);
            }elseif ($_GET['city']){
                $where .= " and provinceid=".intval($_GET['city']);
            }elseif ($_GET['salary']){

            }elseif ($_GET['uptime']){

            }elseif ($_GET['keyword']){
                $where .= " and (name like '%".$_GET['keyword']."%' or com_name like '%".$_GET['keyword']."%')";
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
            $this->lt_tpl('serving_job');
        }

    //�ղ�ְλ
    function fav_job_action(){

        global $config;

        $this->public_action();
        $this->industry_cache();
        $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
        $this->com_cache();

        $uptime=array('1'=>'����','3'=>'���3��','7'=>'���7��','30'=>'���һ����','90'=>'���������');
        $this->yunset("uptime",$uptime);
        $urlarr=array("c"=>"job","page"=>"{{page}}","act"=>"fav_job");
        $pageurl=Url('member',$urlarr);
        $page=$_GET['page']<1?1:$_GET['page'];
        $ststrsql=($page-1)*10;
        $num=$this->obj->DB_select_num("fav_job","uid=".$this->uid);
        $fav_job=$this->obj->DB_select_all("fav_job","uid=".$this->uid,"job_id");
        foreach ($fav_job as $li){
            $job_ids[] = $li['job_id'];
        }
        $job_ids = implode(",",$job_ids);
        $this->yunset("total",$num);
        if($num>10){
            $pages=ceil($num/10);
            $pagenav=Page($page,$num,10,$pageurl,$notpl=false,$this->tpl,"pagenav");
            $this->yunset("pages",$pages);
        }
        $where="id in(".$job_ids.")";


//		$rows=$this->get_page("company_job",$where,$pageurl,'10');

        if($_GET['hy']){
            $where .= " and hy=".intval($_GET['hy']);
        }elseif ($_GET['city']){
            $where .= " and provinceid=".intval($_GET['city']);
        }elseif ($_GET['salary']){

        }elseif ($_GET['uptime']){

        }elseif ($_GET['keyword']){
            $where .= " and (name like '%".$_GET['keyword']."%' or com_name like '%".$_GET['keyword']."%')";
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
    }


    //ȡ���ղ�\
    function del_fav_action(){
        $job_id = $_POST['id'];
        $fav = $this->obj->DB_select_once("fav_job","uid=".$this->uid." and job_id=".$job_id);
        if($fav){
            $this->obj->DB_delete_all("fav_job","uid=".$this->uid." and job_id=".$job_id);
            echo 1;exit();
        }else{
            $this->error_msg("���ȹ�ע��ְλ");
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