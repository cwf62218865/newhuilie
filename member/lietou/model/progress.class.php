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
class progress_controller extends lietou{
	function index_action(){
		$this->public_action();
		$this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
		$uptime=array('1'=>'今天','3'=>'最近3天','7'=>'最近7天','30'=>'最近一个月','90'=>'最近三个月');
                $this->yunset("uptime",$uptime);
		$urlarr=array("c"=>"job","page"=>"{{page}}");
		$where="`uid`='".$this->uid."' ";
		if($_GET['keyword']){
			$where .= " and `name` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['w']==4){
			$where .= " and `status`='1'";
			$urlarr['w']=$_GET['w'];
		}elseif($_GET["w"]==2){
			$where .= " and `edate`<'".time()."'";
			$urlarr['w']=2;
		}elseif($_GET["w"]==1){
			$where .= "  and `status`='0' and `state`='1'";
			$urlarr['w']=1;
		}elseif($_GET["w"]==5){
			$where .= "  and 1";
			$urlarr['w']=5;
		}else{
			$where .= " and `state`='".$_GET['w']."'";
			$urlarr['w']=$_GET['w'];
		}
		$pageurl=Url('member',$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,'10');
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
		$this->yunset("rows",$rows);
		$this->yunset("urgent",$urgent);
		$this->yunset("maxfen",$maxfen);
		$this->company_satic();
		$this->yunset("js_def",2);
		$this->lt_tpl('progress_all');
	}

	//推荐中的简历
	function progress_ing_action(){
    		$this->public_action();
    		$this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
    		$uptime=array('1'=>'今天','3'=>'最近3天','7'=>'最近7天','30'=>'最近一个月','90'=>'最近三个月');
                    $this->yunset("uptime",$uptime);
    		$urlarr=array("c"=>"job","page"=>"{{page}}");
    		$where="`uid`='".$this->uid."' ";
    		if($_GET['keyword']){
    			$where .= " and `name` like '%".trim($_GET['keyword'])."%'";
    			$urlarr['keyword']=$_GET['keyword'];
    		}
    		if($_GET['w']==4){
    			$where .= " and `status`='1'";
    			$urlarr['w']=$_GET['w'];
    		}elseif($_GET["w"]==2){
    			$where .= " and `edate`<'".time()."'";
    			$urlarr['w']=2;
    		}elseif($_GET["w"]==1){
    			$where .= "  and `status`='0' and `state`='1'";
    			$urlarr['w']=1;
    		}elseif($_GET["w"]==5){
    			$where .= "  and 1";
    			$urlarr['w']=5;
    		}else{
    			$where .= " and `state`='".$_GET['w']."'";
    			$urlarr['w']=$_GET['w'];
    		}
    		$pageurl=Url('member',$urlarr);
    		$rows=$this->get_page("company_job",$where,$pageurl,'10');
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
    		$this->yunset("rows",$rows);
    		$this->yunset("urgent",$urgent);
    		$this->yunset("maxfen",$maxfen);
    		$this->company_satic();
    		$this->yunset("js_def",2);
    		$this->lt_tpl('progress_ing');
    	}

    	//推荐成功的简历
        function progress_suc_action(){
            $this->public_action();
            $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
            $uptime=array('1'=>'今天','3'=>'最近3天','7'=>'最近7天','30'=>'最近一个月','90'=>'最近三个月');
                    $this->yunset("uptime",$uptime);
            $urlarr=array("c"=>"job","page"=>"{{page}}");
            $where="`uid`='".$this->uid."' ";
            if($_GET['keyword']){
                $where .= " and `name` like '%".trim($_GET['keyword'])."%'";
                $urlarr['keyword']=$_GET['keyword'];
            }
            if($_GET['w']==4){
                $where .= " and `status`='1'";
                $urlarr['w']=$_GET['w'];
            }elseif($_GET["w"]==2){
                $where .= " and `edate`<'".time()."'";
                $urlarr['w']=2;
            }elseif($_GET["w"]==1){
                $where .= "  and `status`='0' and `state`='1'";
                $urlarr['w']=1;
            }elseif($_GET["w"]==5){
                $where .= "  and 1";
                $urlarr['w']=5;
            }else{
                $where .= " and `state`='".$_GET['w']."'";
                $urlarr['w']=$_GET['w'];
            }
            $pageurl=Url('member',$urlarr);
            $rows=$this->get_page("company_job",$where,$pageurl,'10');
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
            $this->yunset("rows",$rows);
            $this->yunset("urgent",$urgent);
            $this->yunset("maxfen",$maxfen);
            $this->company_satic();
            $this->yunset("js_def",2);
            $this->lt_tpl('progress_suc');
        }
    //推荐失败的简历
            function progress_fal_action(){
                $this->public_action();
                $this->yunset($this->MODEL('cache')->GetCache(array('city','com')));
                $uptime=array('1'=>'今天','3'=>'最近3天','7'=>'最近7天','30'=>'最近一个月','90'=>'最近三个月');
                        $this->yunset("uptime",$uptime);
                $urlarr=array("c"=>"job","page"=>"{{page}}");
                $where="`uid`='".$this->uid."' ";
                if($_GET['keyword']){
                    $where .= " and `name` like '%".trim($_GET['keyword'])."%'";
                    $urlarr['keyword']=$_GET['keyword'];
                }
                if($_GET['w']==4){
                    $where .= " and `status`='1'";
                    $urlarr['w']=$_GET['w'];
                }elseif($_GET["w"]==2){
                    $where .= " and `edate`<'".time()."'";
                    $urlarr['w']=2;
                }elseif($_GET["w"]==1){
                    $where .= "  and `status`='0' and `state`='1'";
                    $urlarr['w']=1;
                }elseif($_GET["w"]==5){
                    $where .= "  and 1";
                    $urlarr['w']=5;
                }else{
                    $where .= " and `state`='".$_GET['w']."'";
                    $urlarr['w']=$_GET['w'];
                }
                $pageurl=Url('member',$urlarr);
                $rows=$this->get_page("company_job",$where,$pageurl,'10');
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
                $this->yunset("rows",$rows);
                $this->yunset("urgent",$urgent);
                $this->yunset("maxfen",$maxfen);
                $this->company_satic();
                $this->yunset("js_def",2);
                $this->lt_tpl('progress_fal');
            }
	

}
?>