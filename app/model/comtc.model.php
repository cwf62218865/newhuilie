<?php
/*
 * $Author ：PHPYUN开发团队
 *
 * 官网: http://www.phpyun.com
 *
 * 版权所有 2009-2016 宿迁鑫潮信息技术有限公司，并保留所有权利。
 *
 * 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class comtc_model extends model{

	
	function invite_resume($data){

		if($data['show_job'] || $data['jobid'] || $data['jobtype']){
			$jobtype=intval($data['jobtype']);
			$show_job=$data['show_job'];
			$jobid=intval($data['jobid']);
		}
		
		if($this->usertype == 2){
			$member = $this->DB_select_once("member","`uid`='{$this->uid}'","`status`");
			if($member['status'] != 1){
				$return['status'] = 7;
				return $return;
			}
		}

		if($this->usertype=='' || $this->uid==''){
				
			$return['status']=6;
				
		}elseif($show_job && $this->usertype=='2'){
				
			
			$company_job=$this->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='1' and `edate` > '".time()."' and `r_status` <> '2' and `status` <> '1'","`name`,`id`");
			
				
			if($company_job && is_array($company_job)){

				foreach($company_job as $val){
					if($jobid && $val['id'] == $jobid){
						$jobname="".yun_iconv("gbk","utf-8",$val['name'])."";
					}
				}
				$return['jobname']=$jobname;
				
			}else{

				$return['status']=5;

			}
				
		}else if($this->usertype!='2'){
			$return['status']='0';
		}

		if($return['status']==''){
 				
			$company_yq=$this->DB_select_once("userid_msg","`fid`='".$this->uid."'");
				
			if(!$company_yq['linkman'] || !$company_yq['linktel'] || !$company_yq['address']){

				$company = $this->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`linkphone`,`address`");

				if(!$company_yq['linkman']){
					$company_yq['linkman'] = $company['linkman'];
				}
				if(!$company_yq['address']){
					$company_yq['address'] = $company['address'];
				}
				if(!$company_yq['linktel']){
					if($company['linktel']){
						$company_yq['linktel'] = $company['linktel'];
					}else{
						$company_yq['linktel'] = $company['linkphone'];
					}
				}
			}
			$return['linkman']=yun_iconv("gbk","utf-8",$company_yq['linkman']);
			$return['linktel']=yun_iconv("gbk","utf-8",$company_yq['linktel']);
			$return['content']=yun_iconv("gbk","utf-8",$company_yq['content']);
			$return['address']=yun_iconv("gbk","utf-8",$company_yq['address']);
			$return['intertime']=yun_iconv("gbk","utf-8",$company_yq['intertime']);
				
			
				
			$row = $this->DB_select_once("company_statis","`uid`='".$this->uid."'","`rating`,`vip_etime`,`invite_resume`,`rating_type`");
				
			if($row['vip_etime']>time() || $row['vip_etime']=="0"){
				
				if($row['rating_type']!="2"){

					if($row['invite_resume']==0){

						if($this->config['com_integral_online']=="1"){
							$return['status']=2;
							$return['integral']=$this->config['integral_interview'];
						}else{
							$return['status']=4;
						}
						
					}else{
						
						$return['status']=3;
						
					}
					
				}else{
					
					$return['status']=3;
					
				}
				
			}else{
				if($this->config['com_integral_online']=="1"){
					$return['status']=2;
					$return['integral']=$this->config['integral_interview'];
				}else{
					$return['status']=4;
				}
			}
		}
		return $return;
	}
}
?>