<?php
/*
 * $Author ：PHPYUN开发团队
 *
 * 官网: http://www.phpyun.com
 *
 * 版权所有 2009-2017 宿迁鑫潮信息技术有限公司，并保留所有权利。
 *
 * 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class compay_model extends model{

	
	function buyAutoJob($data){
		if($data['jobautoids'] && ($data['rdays'] || $data['crdays'])){

			$jobautoids=@explode(',',$data['jobautoids']);
			$jobautoids = pylode(',',$jobautoids);

			
			$autodays= intval($data['crdays']);
			if($autodays < 1){
				$autodays= intval($_POST['rdays']);
			}
			$autotype = 1; 

			if($autodays > 0 && $jobautoids){

				
				$jobs = $this->DB_select_all("company_job","`uid`='".$this->uid."' and `id` in(".$jobautoids.")","`autotime`,`id`");

				if(empty($jobs)){
					$return['error'] = '请选择正确的刷新职位！';
				}else {
					$jobnum = $this->DB_select_num("company_job","`uid`='".$this->uid."' and `id` in(".$jobautoids.")");
						
					
					$price = ceil($autodays * $this->config['job_auto'] * $jobnum);
					$price = sprintf("%.2f", $price);
						
					if ($price < 1){
						$return['error'] = '购买总金额不得小于1元！';
					} else {

						
						$dingdan=time().rand(10000,99999);
						$orderData['type']='13';
						$orderData['order_id']=$dingdan;
						$orderData['order_price']=$price;
						$orderData['order_time']=time();
						$orderData['order_state']="1";
						$orderData['order_remark']='自动刷新';
						$orderData['uid']=$this->uid;
						$orderData['did']=$this->userdid;
						$orderData['order_info']=serialize(array('jobid'=>$data['jobautoids'],'days'=>$data['crdays']?$data['crdays']:$data['rdays'],'price'=>$price));
						$id=$this->insert_into("company_order",$orderData);
 						
						if($id){
 							$orderData['id']=$id;
							$return['order']=$orderData;
						}else{
							$return['error'] = '订单生成失败！';
						}
					}
				}
			}else{
				$return['error'] = '请正确选择自动刷新职位以及刷新天数！';
			}

		} else {
			
			$return['error'] = '参数填写错误，请重新设置！';
			
		}

		return $return;
	}
	
	
	function buyZdJob($data){
		if($data['zdjobid'] && ($data['xsdays'] || $data['cxsdays'])){
			
			$jobid = $data['zdjobid'];
		
 			$xsdays=intval($data['xsdays']);
 			if($xsdays==''&&$data['cxsdays']){
			    $xsdays=intval($data['cxsdays']);
			}
			if($xsdays<1){
				$xsdays=1;
			}
  			if($xsdays > 0 && $jobid){

				
				$job = $this->DB_select_once("company_job","`uid`='".$this->uid."' and `id` ='".$jobid."'");

				if(empty($job)){
					
					$return['error'] = '请选择正确的职位置顶！';
					
				}else {
					
					$price = ceil($xsdays * $this->config['integral_job_top']);
					$price = sprintf("%.2f", $price);
						
 					if ($price < 1){
 						
						$return['error'] = '购买总金额不得小于1元！';
						
					} else {

						
						$dingdan=time().rand(10000,99999);
						$orderData['type']='10';
						$orderData['order_id']=$dingdan;
						$orderData['order_price']=$price;
						$orderData['order_time']=time();
						$orderData['order_state']="1";
						$orderData['order_remark']='置顶服务';
						$orderData['uid']=$this->uid;
						$orderData['did']=$this->userdid;
						$orderData['order_info']=serialize(array('jobid'=>$data['zdjobid'],'days'=>$data['cxsdays']?$data['cxsdays']:$data['xsdays'],'price'=>$price));
						$id=$this->insert_into("company_order",$orderData);
  						
						if($id){
							$orderData['id']=$id;
 							$return['order']=$orderData;
						}else{
							$return['error'] = '订单生成失败！';
						}
					}
				}
				
			}else{
				
				$return['error'] = '请正确选择职位置顶以及置顶的天数！';
				
			}

		} else {
			
			$return['error'] = '参数填写错误，请重新设置！';
			
		}
		return $return;
	}
	
	
	function buyRecJob($data){
		if($data['recjobid'] && ($data['recdays'] || $data['crecdays'])){
			
 			$jobid = $data['recjobid'];
		
 			$recdays=intval($data['recdays']);
 			if($recdays==''&&$data['crecdays']){
			    $recdays=intval($data['crecdays']);
			}
			if($recdays<1){
				$recdays=1;
			}
  			if($recdays > 0 && $jobid){

				
				$job = $this->DB_select_once("company_job","`uid`='".$this->uid."' and `id` ='".$jobid."'");

				if(empty($job)){
					$return['error'] = '请选择正确的职位推荐！';
				}else {
				
					$price = ceil($recdays * $this->config['com_recjob']);
					$price = sprintf("%.2f", $price);
						
 					if ($price < 1){
						$return['error'] = '购买总金额不得小于1元！';
					} else {
						
					
						$dingdan=time().rand(10000,99999);
						$orderData['type']='12';
						$orderData['order_id']=$dingdan;
						$orderData['order_price']=$price;
						$orderData['order_time']=time();
						$orderData['order_state']="1";
						$orderData['order_remark']='职位推荐';
						$orderData['uid']=$this->uid;
						$orderData['did']=$this->userdid;
						$orderData['order_info']=serialize(array('jobid'=>$data['recjobid'],'days'=>$data['crecdays']?$data['crecdays']:$data['recdays'],'price'=>$price));
						$id=$this->insert_into("company_order",$orderData);

						if($id){
							
							$orderData['id']=$id;
 							$return['order']=$orderData;
 							
						}else{
							$return['error'] = '订单生成失败！';
						}
					}
				}
			}else{
				$return['error'] = '请正确选择职位推荐以及推荐的时长！';
			}

		} else {
			$return['error'] = '参数填写错误，请重新设置！';
		}

		return $return;
	}
	

	function buyUrgentJob($data){
		if($data['ujobid'] && ($data['udays'] || $data['cudays'])){
			
			$jobid = $data['ujobid'];
			
 			$udays=intval($data['udays']);
 			if($udays==''&&$data['cudays']){
			    $udays=intval($data['cudays']);
			}
			if($udays<1){
				$udays=1;
			}
  			if($udays > 0 && $jobid){

				
				$job = $this->DB_select_once("company_job","`uid`='".$this->uid."' and `id` ='".$jobid."'");

				if(empty($job)){
					$return['error'] = '请选择正确的职位！';
				}else {
				
					$price = ceil($udays * $this->config['com_urgent']);
					$price = sprintf("%.2f", $price);
						
 					if ($price < 1){
						$return['error'] = '购买总金额不得小于1元！';
					} else {

					
						$dingdan=time().rand(10000,99999);
						$orderData['type']='11';
						$orderData['order_id']=$dingdan;
						$orderData['order_price']=$price;
						$orderData['order_time']=time();
						$orderData['order_state']="1";
						$orderData['order_remark']='紧急招聘';
						$orderData['uid']=$this->uid;
						$orderData['did']=$this->userdid;
						$orderData['order_info']=serialize(array('jobid'=>$data['ujobid'],'days'=>$data['cudays']?$data['cudays']:$data['udays'],'price'=>$price));
						$id=$this->insert_into("company_order",$orderData);
  						
						if($id){
  							$orderData['id']=$id;
 							$return['order']=$orderData;
						}else{
							$return['error'] = '订单生成失败！';
						}
					}
				}
			}else{
				$return['error'] = '请正确选择职位以及紧急招聘天数！';
			}

		} else {
			$return['error'] = '参数填写错误，请重新设置！';
		}

		return $return;
	}
	
	
	
	function buyPackOrder($data){
		if($data['id'] && $data['price']){

			$price = sprintf("%.2f", $data['price']);
			$rating = (int)$data['id'];
			if($price > 0){

				
				$packinfo = $this->DB_select_once("company_service_detail","`id`='".$data['id']."'");
				if(empty($packinfo)){
					$return['error'] = '请选择正确的增值套餐！';
				}else {
				
					$dingdan=time().rand(10000,99999);
					$orderData['type']='5';
					$orderData['order_id']=$dingdan;
					$orderData['order_price']=$price;
					$orderData['order_time']=time();
					$orderData['order_state']="1";
					$orderData['rating']=$rating;
					$orderData['order_remark']='购买增值服务';
					$orderData['uid']=$this->uid;
					$orderData['did']=$this->userdid;
					$id=$this->insert_into("company_order",$orderData);

					if($id){
						$orderData['id']=$id;
						$return['order']=$orderData;
					}else{
						$return['error'] = '订单生成失败！';
					}
				}
			}else{
				$return['error'] = '套餐金额出错！';
			}
		}else{

			$return['error'] = '参数错误，请重新选择！';
		}
		return $return;
	}
}
?>