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
class part_controller extends company{
	function index_action(){
		include PLUS_PATH."part.cache.php";
		if(trim($_GET['keyword'])){
			$where.=" and name like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		$urlarr['c']=$_GET['c'];
		$urlarr["page"]="{{page}}";
		$pageurl=Url('member',$urlarr);
		$rows=$this->get_page("partjob","`uid`='".$this->uid."'".$where,$pageurl,"10");
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]['salary_type']=$partclass_name[$v['salary_type']];
				$rows[$k]['type']=$partclass_name[$v['type']];
			}
		}
		$this->public_action();
		$this->yunset("rows",$rows);
		$this->company_satic();
		$this->yunset("js_def",3);
		$this->com_tpl('partlist');
	}
	function del_action(){
		if($_GET['del']||$_GET['id']){
			if(is_array($_GET['del'])){
				$del=@implode(",",$_GET['del']);
				$layer_type=1;
			}else{
				$del=(int)$_GET['id'];
				$layer_type=0;
			}
		}
		$oid=$this->obj->DB_delete_all("partjob","`id` in (".$del.") and `uid`='".$this->uid."'","");
		if($oid){
			$this->obj->DB_delete_all("part_collect","`jobid` in (".$del.") and `comid`='".$this->uid."'","");
			$this->obj->DB_delete_all("part_apply","`jobid` in (".$del.") and `comid`='".$this->uid."'","");
			$this->obj->member_log("删除兼职职位");
			$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}
	}
	function opera_action(){
		$this->part();
	}

	function ajax_refresh_part_action()
	{
		if(!isset($_POST['partid'])){
			exit;
		}

		$partid = $_POST['partid'];
		
		$statis = $this->company_satic();

		$msg = '';

		if( $statis['vip_etime'] >time() || $statis['vip_etime'] ==0 ){
        	if( $statis['rating_type'] == 1 ){
            	if( $statis['breakpart_num'] > 0 ){
            		$msg = '刷新兼职数还剩余' . $statis['breakpart_num'] . '个！确认刷新？';
            		$data['url'] = 'index.php?c=partok&act=opera&up=' . $partid;

            		$data['status'] = 1;
        		}           	
				else{
              		if( $this->config['com_integral_online'] ==1 ){
						if( $this->config['integral_partjobefresh'] > 0 ){
							$msg = '本次刷新需扣除' . $this->config['integral_partjobefresh'] 
								. $this->config['integral_priceunit'] . $this->config['integral_pricename']
								. '！确认刷新？';
							$data['url'] = 'index.php?c=partok&act=opera&up=' . $partid;

							$data['status'] = 1;
						}
						else{
							$msg = '确认要刷新？';
							$data['url'] = 'index.php?c=partok&act=opera&up=' . $partid;

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
				$msg = '确定刷新该兼职么？';
				$data['url'] = 'index.php?c=partok&act=opera&up=' . $partid;

				$data['status'] = 1;
		    }
	    }
        else{
            if( $this->config['com_integral_online'] ==1 ){
				$msg = '本次刷新需扣除' . $this->config['integral_partjobefresh'] 
					. $this->config['integral_priceunit'] . $this->config['integral_pricename']
					. '！确认刷新？';
				$data['url'] = 'index.php?c=partok&act=opera&up=' . $partid;

				$data['status'] = 1;
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