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
class ajax_controller extends common{
	
	function msg_post($uid,$comid,$row=''){
		$com=$this->obj->DB_select_once("company","`uid`='".$comid."'","`uid`,`name`,`linkman`,`linktel`,`linkmail`");
		$info=$this->obj->DB_select_alls("member","resume","a.`uid`='".$uid."' and a.`uid`=b.`uid`","a.`email`,a.`moblie`,b.`name`");
		$info=$info[0];
		$data['uid']=$uid;
		$data['name']=$info['name'];
		$data['cuid']=$com['uid'];
		$data['cname']=$com['name'];
		$data['type']="yqms";
		$data['company']=$com['name'];
		$data['linkman']=$com['linkman'];
		$data['comtel']=$com['linktel'];
		$data['comemail']=$com['linkmail'];
		$data['content']=@str_replace("\n","<br/>",$row['content']);
		$data['jobname']=$row['jobname'];
		$data['username']=$row['username'];
		$data['email']=$info['email'];
		$data['moblie']=$info['moblie'];
		$this->send_msg_email($data,'','1');
	}

	function downresume_action(){
		$eid=(int)$_POST['eid'];
		$uid=(int)$_POST['uid'];
		$type=$_POST['type'];
		$data['eid']=$eid;
		$data['uid']=$uid;
		$data['comid']=$this->uid;
		$data['did']=$this->userdid;
		$data['downtime']=time();
		if(!$this->uid || !$this->username || $this->usertype!=2){
			if(!$this->uid || !$this->username){
				$arr['status']=1;
				$arr['msg']="请先登录！";
			}else if($this->usertype!='2'){
				$arr['status']=1;
				$arr['msg']="您不是企业用户，无法下载简历！";
			}
		}else{
			$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$uid."' and `p_uid`='".$this->uid."'");
			if(!empty($black)){
				$arr['status']=1;
				$arr['msg']="您已被该用户列入黑名单！";
			}
			$username=$this->obj->DB_select_once("member","`uid`='".$uid."' and `usertype`='1'",'username');
			$resume=$this->obj->DB_select_once("down_resume","`eid`='$eid' and `comid`='".$this->uid."'");
			if(is_array($resume)){
				$arr['status']=3;
			}else if($arr['status']==''){
				$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`down_resume`,`integral`,`vip_etime`,`rating`,`rating_type`");
				if($type=="integral"){
					if($row['integral']<$this->config['integral_down_resume'] && $this->config['integral_down_resume_type']=="2"){
						$arr['status']=2;
						$arr['integral']=$row['integral'];
					}else{
						$this->obj->insert_into("down_resume",$data);
						if($this->config['integral_down_resume_type']=="1"){
							$auto=true;
						}else{
							$auto=false;
						}
						$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
						$this->company_invtal($this->uid,$this->config['integral_down_resume'],$auto,"下载简历",true,2,'integral',13);
						$state_content = "新下载了 <a href=\"".Url('wap',array("c"=>"resume",'a'=>'show',"id"=>$eid))."\" target=\"_blank\">".$username['username']."</a> 的简历。";
						$this->addstate($state_content,2);
						$arr['status']=3;
						$this->obj->member_log("下载了 ".$username['username']." 的简历。",3);
						$this->warning("2");
					}
				}else{
					$arr['integral']=$this->config['integral_down_resume'];
					if($row['rating']==0){
						$arr['status']=1;
					}else{
						if($row['vip_etime']>time() || $row['vip_etime']=="0"){
							if($row['rating_type']!="2"){
								if($row['down_resume']==0){
									if($this->config['com_integral_online']=="1"){
										$arr['status']=1;
										$arr['uid']=$user['uid'];
										$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否下载？";	
									}else{
										$arr['status']=1;
										$arr['msg']="会员下载简历已用完，您可以购买增值包！";
									}
								}else{
									
									$this->obj->insert_into("down_resume",$data);
									$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
									$this->obj->DB_update_all("company_statis","`down_resume`=`down_resume`-1","uid='".$this->uid."'");
									$state_content = "新下载了 <a href=\"".Url('wap',array("c"=>"resume",'a'=>'show',"id"=>$eid))."\" target=\"_blank\">".$username['username']."</a> 的简历。";
									$this->addstate($state_content,2);
									$arr['status']=3;
									$this->obj->member_log("下载了 ".$username['username']." 的简历。",3);
									$this->warning("2");
								}
							}else{
								$this->obj->insert_into("down_resume",$data);
								$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
								$state_content = "新下载了 <a href=\"".Url('wap',array("c"=>"resume",'a'=>'show','id'=>(int)$_POST[eid]))."\" target=\"_blank\">".$username['username']."</a> 的简历。";
								$this->addstate($state_content,2);
								$arr['status']=3;
								$this->obj->member_log("下载了 ".$username['username']." 的简历。",3);
								$this->warning("2");
							}
						}else{
							if($this->config['com_integral_online']=="1"){
								$arr['status']=1;
								$arr['uid']=$user['uid'];
								$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否下载？";	
							}else{
								$arr['status']=1;
								$arr['msg']="您的等级特权已到期！";
							}
						}
							
					}
				}
			}
		}
		$arr['msg']=iconv("gbk", "utf-8",$arr['msg']);
		$arr['usertype']=$this->usertype;
		echo json_encode($arr);die;
	}
	
	function wap_job_action()
	{
		include(PLUS_PATH."job.cache.php");
		
		$data="<option value=''>--请选择--</option>";
		
		if(is_array($job_type[$_POST['id']])){
			foreach($job_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$job_name[$v]."</option>";
			}
		}
		echo $data;
	}
	
	function sava_ajaxresume_action(){
		
		if(!$this->uid || !$this->username || $this->usertype != 2){
			$arr['status']=0;
			echo json_encode($arr);die;
		}
		$jobtype= intval($_POST['jobtype']);
		if($jobtype==''||$jobtype<2){
			$jobtype=0;
		}
		$_POST['uid'] = intval($_POST['uid']);
		$data=array();
		$data['uid']=$_POST['uid'];
		$data['title']='面试邀请';
		$data['content']=yun_iconv("utf-8","gbk",$_POST['content']);
		$data['fid']=$this->uid;
		$data['datetime']=time();
		$data['address']=iconv("utf-8","gbk",$_POST['address']);
		$data['intertime']=iconv("utf-8","gbk",$_POST['intertime']);
		$data['linkman']=iconv("utf-8","gbk",$_POST['linkman']);
		$data['linktel']=iconv("utf-8","gbk",$_POST['linktel']);
		$data['jobname']=iconv("utf-8","gbk",$_POST['jobname']);
		$data['jobid']	=iconv("utf-8","gbk",$_POST['jobid']);
		$info['jobname']=iconv("utf-8","gbk",$_POST['jobname']);
		$info['username']=iconv("utf-8","gbk",$_POST['username']);
		$info['content']=$data['content'];
        $p_uid=$_POST['uid'];

		$JobM=$this->MODEL("job");
	
		$num=$JobM->GetComjobNum(array("uid"=>$this->uid,"state"=>1,"id"=>$data['jobid'],"`edate`>'".time()."'"));
		
		if($num<1){
			$arr['status']=4;
			$arr['msg']=yun_iconv("gbk","utf-8",'请选择要面试的职位！');
			echo json_encode($arr);die;
		}
		
        $black=$JobM->GetBlackOne(array("c_uid"=>$p_uid,"p_uid"=>$this->uid));
		if(!empty($black)){
			$arr['status']=9;
			echo json_encode($arr);die;
		}
		
		$umessage = $JobM->GetUseridMsgOne(array("uid"=>$p_uid,"fid"=>$this->uid,"jobid"=>intval($_POST['jobid']),'type'=>$jobtype)); 
		if(is_array($umessage)){
			$arr['status']=8;
			$arr['msg']=yun_iconv("gbk","utf-8",'此职位邀请过该人才，请不要重复邀请！');
			echo json_encode($arr);die;
		}else{
			$com=$this->MODEL()->DB_select_once("company","`uid`='".$this->uid."'","name,did");
			$resume=$this->MODEL()->DB_select_once("resume","`uid`='".$p_uid."'","name,def_job");
			$data['did']=$com['did'];
			$data['fname']=$com['name'];
			if($_POST['update_yq']=='1'){
				$data['default']=1;
			}else{
				$this->obj->DB_update_all("userid_msg","`default`='0'","`fid`='".$this->uid."'");
			}
			$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`rating`,`vip_etime`,`integral`,`invite_resume`,`rating_type`");
			
			if($row['vip_etime']>time() || $row['vip_etime']=="0"){
				
				if($row['rating_type']!="2"){

					if($row['invite_resume']==0 ){

						if($this->config['com_integral_online']=='1'){

							if($row['integral'] < $this->config['integral_interview']){
								$arr['status']=5;
								$arr['integral']=$row['integral'];
	 						}else{
								$this->obj->insert_into("userid_msg",$data);
								$historyM = $this->MODEL('history');
								$historyM->addHistory('userid_msg',$data['uid']);
								if($this->config['integral_interview_type']=="1"){
									$auto=true;
								}else{
									$auto=false;
								}
								$this->company_invtal($this->uid,$this->config['integral_interview'],$auto,"邀请会员面试",true,2,'integral',14);
								$state_content = "我刚邀请了人才 <a href=\"".Url('resume',array("c"=>"show","id"=>$resume['def_job']))."\" target=\"_blank\">".$resume['name']."</a> 面试。";
								$this->addstate($state_content,2);
								$arr['status']=3;
	 							$this->MODEL()->member_log("邀请了人才：".$resume['name'],4);
	 							$this->msg_post($_POST['uid'],$this->uid,$info);
							}
						}
					}else{
					
						$this->obj->insert_into("userid_msg",$data);
						$historyM = $this->MODEL('history');
						$historyM->addHistory('userid_msg',$data['uid']);
						$this->obj->DB_update_all("company_statis","`invite_resume`=`invite_resume`-1","uid='".$this->uid."'");
						$state_content = "我刚邀请了人才 <a href=\"".Url('resume',array("c"=>"show","id"=>$resume['def_job']))."\" target=\"_blank\">".$resume['name']."</a> 面试。";
						$this->addstate($state_content,2);
						$arr['status']=3;
						$this->MODEL()->member_log("邀请了人才：".$resume['name'],4);
						$this->msg_post($_POST['uid'],$this->uid,$info);
					}
				}else{
					$this->obj->insert_into("userid_msg",$data);
					$historyM = $this->MODEL('history');
					$historyM->addHistory('userid_msg',$data['uid']);
					$state_content = "我刚邀请了人才 <a href=\"".Url('resume',array("c"=>"show","id"=>$resume['def_job']))."\" target=\"_blank\">".$resume['name']."</a> 面试。";
					$this->addstate($state_content,2);
					$arr['status']=3;
					$this->MODEL()->member_log("邀请了人才：".$resume['name'],4);
					$this->msg_post($_POST['uid'],$this->uid,$info);
				}

			}else{
				
				if($this->config['com_integral_online']=='1'){
							
					if($row['integral'] < $this->config['integral_interview'] ){
						$arr['status']=5;
						$arr['integral']=$row['integral'];
					}else{
						$this->obj->insert_into("userid_msg",$data);
						$historyM = $this->MODEL('history');
						$historyM->addHistory('userid_msg',$data['uid']);
						if($this->config['integral_interview_type']=="1"){
							$auto=true;
						}else{
							$auto=false;
						}
						$this->company_invtal($this->uid,$this->config['integral_interview'],$auto,"邀请会员面试",true,2,'integral',14);
						$state_content = "我刚邀请了人才 <a href=\"".Url('resume',array("c"=>"show","id"=>$resume['def_job']))."\" target=\"_blank\">".$resume['name']."</a> 面试。";
						$this->addstate($state_content,2);
						$arr['status']=3;
						$this->MODEL()->member_log("邀请了人才：".$resume['name'],4);
						$this->msg_post($_POST['uid'],$this->uid,$info);
					}
				}
			}
				
			if($arr['status']=='3'){
				$Weixin=$this->MODEL('weixin');
				$Weixin->sendWxresume($data);

				
				$jobid = intval($_POST['jobid']);
				$this->obj->DB_update_all("userid_job",'`is_browse` = 2',"`com_id`={$this->uid} and `job_id`={$jobid}");
			}
		}
		echo json_encode($arr);
	}

	function forlink_action(){
		$eid=(int)$_POST['eid'];
		if(!$this->uid || !$this->username || $this->usertype==1){
			if(!$this->uid || !$this->username){
				$arr['status']=1;
				$arr['msg']="请先登录！";
			}else if($this->usertype=='1'){
				$arr['status']=1;
				$arr['msg']="您是个人用户，无法下载简历！";
			}
		}else{
			if($this->usertype == 2){
				$member = $this->obj->DB_select_once("member","`uid`='{$this->uid}'","`status`");
				if($member['status'] != 1){
					$arr['status'] = 6;
					echo json_encode($arr);die;
				}
			}
			
		  
			$resume=$this->obj->DB_select_once("down_resume","`eid`='".$eid."' and `comid`='".$this->uid."'");
			$user=$this->obj->DB_select_alls("resume","resume_expect","a.`r_status`<>'2' and a.`uid`=b.`uid` and b.`id`='".$eid."'","a.name,a.telphone,a.basic_info,a.telhome,a.email,a.uid,b.id,b.`height_status`");
			$user=$user[0];
			$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$user['uid']."' and `p_uid`='".$this->uid."'");
			if(!empty($black)){
				$arr['status']=1;
				$arr['msg']="您已被该用户列入黑名单！";
			}
			$userid_job=$this->obj->DB_select_once("userid_job","`com_id`='".$this->uid."' and `eid`='".$eid."'");
			if((!empty($resume)||!empty($userid_job))&&$arr['status']==''){
				$arr['status']=3;
			}else if($arr['status']==''){
				if($this->usertype=='2'){
					$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","vip_etime,down_resume,rating_type");
				}
				$data['eid']=$user['id'];
				$data['uid']=$user['uid'];
				$data['comid']=$this->uid;
				$data['did']=$this->userdid;
				$data['downtime']=time();
				if($row['vip_etime']>time() || $row['vip_etime']=="0"){
					if($row['rating_type']!='2'){
						if($row['down_resume']==0){
							if($this->config['com_integral_online']=="1"){
								$arr['status']=2;
								$arr['uid']=$user['uid'];
								if($this->usertype=='2'){
									$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否查看？";
								}else{
									$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_lt_downresume'].$this->config['integral_pricename']."，是否查看？";
								}
							}else{
								$arr['status']=1;
								$arr['msg']="会员下载简历已用完！";
							}
						}else{
							
							$this->obj->insert_into("down_resume",$data);
							$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
							if($this->usertype=='2'){
								$this->obj->DB_update_all("company_statis","`down_resume`=`down_resume`-1","uid='".$this->uid."'");
							}
							if($this->usertype=='2'){
								$state_content = "新下载了简历 <a href=\"".Url("wap",array('c'=>'resume','a'=>'show','id'=>(int)$user[id]))."\" target=\"_blank\">".$user['name']."</a> 。";
							}
							$this->addstate($state_content,2);
							$arr['status']=3;
							if($this->usertype=='2'){
								$this->obj->member_log("下载了简历：".$user['name'],3);
							}
							$this->warning("2");
						}
					}else{
						$this->obj->insert_into("down_resume",$data);
						$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
						if($this->usertype=='2'){
							$state_content = "新下载了简历 <a href=\"".Url("wap",array('c'=>'resume','a'=>'show','id'=>(int)$user[id]))."\" target=\"_blank\">".$user['name']."</a> 。";
						}else{
							$state_content = "新下载了高级简历 <a href=\"".Url("wap",array('c'=>'resume','a'=>'show','id'=>(int)$user[id]))."\" target=\"_blank\">".$user['name']."</a> 。";	
						}$this->addstate($state_content,2);
						$arr['status']=3;
						if($this->usertype=='2'){
							$this->obj->member_log("下载了简历：".$user['name'],3);
						}
						$this->warning("2");
					}
				}else{
					if($this->config['com_integral_online']=="1"){
						$arr['status']=2;
						$arr['uid']=$user['uid'];
						if($this->usertype=='2'){
							$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否下载？";
						}else{
							$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_lt_downresume'].$this->config['integral_pricename']."，是否下载？";
						}
					}else{
						$arr['status']=1;
						$arr['msg']="您的等级特权已到期！";
					}
				}
			}
			if($arr['status']==3){
				$html="<table>";
				$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","手机：")."</td><td>".$user['telphone']."</td></tr>";
				if($user['basic_info']=='1'){
					$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","座机：")."</td><td>".$user['telhome']."</td></tr>";
				}
				$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","邮箱：")."</td><td>".$user['email']."</td></tr>";
				$html.="</table>";
				$arr['html']=$html;
			}
		}
		$arr['msg']=iconv("gbk", "utf-8",$arr['msg']);
		$arr['usertype']=$this->usertype;
		echo json_encode($arr);die;
	}
	
	function wap_city_action(){
		include(PLUS_PATH."city.cache.php");
		
		if(is_array($city_type[$_POST['id']])){
			$data="<option value=''>--请选择--</option>";
			foreach($city_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$city_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function wap_ltjob_action(){
		include(PLUS_PATH."ltjob.cache.php");
		$data="<option value=''>--请选择--</option>";
		if(is_array($ltjob_type[$_POST['id']])){
			foreach($ltjob_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$ltjob_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function talentpool_action(){
		if($this->uid==''){
			echo 3;die;
		}elseif($this->usertype!="2"){
			echo 0;die;
		}
		$row=$this->obj->DB_select_once("talent_pool","`eid`='".(int)$_POST['eid']."' and `cuid`='".$this->uid."'");
		if(empty($row)){
			$value.="`eid`='".(int)$_POST['eid']."',";
			$value.="`uid`='".(int)$_POST['uid']."',";
			$value.="`cuid`='".$this->uid."',";
			$value.="`ctime`='".time()."'";
			$this->obj->DB_insert_once("talent_pool",$value);
			echo 1;die;
		}else{
			echo 2;die;
		}
	}
    function talent_pool_action(){
		if($this->usertype!="2"){
			echo 0;die;
		}
		$row=$this->obj->DB_select_once("talent_pool","`eid`='".(int)$_POST['eid']."' and `cuid`='".$this->uid."'");
		if(empty($row)){
			$value.="`eid`='".(int)$_POST['eid']."',";
			$value.="`uid`='".(int)$_POST['uid']."',";
			$value.="`remark`='".yun_iconv("utf-8","gbk",$_POST['remark'])."',";
			$value.="`cuid`='".$this->uid."',";
			$value.="`ctime`='".time()."'";
			$this->obj->DB_insert_once("talent_pool",$value);
			$historyM = $this->MODEL('history');
			$historyM->addHistory('talentpool',(int)$_POST['eid']);
			echo 1;die;
		}else{
			echo 2;die;
		}
	}
	function checkOncePassword_action(){
		$_POST=$this->post_trim($_POST);
		$password=md5(trim($_POST['password']));
		$id=intval($_POST['id']);
		$time=time();
        $arr=$this->obj->DB_select_once("once_job","`id`='".$id."' and `password`='".$password."'");
		
		if(isset($arr['id'])){
			if($_POST['operation_type']=="refresh"){
				if($this->config['com_xin']>$arr['sxnumber']){
					$arr=$this->obj->DB_update_all("once_job","`sxnumber`='".$arr['sxnumber']."'+1,`sxtime`='".$time."',`ctime`='".$time."'","`id`='".$id."'and `password`='".$password."'");			echo 1;die;
				}else{
					echo 3;die;
				}
			}
			if($_POST['operation_type']=="remove"){
				$arr=$this->obj->DB_delete_all("once_job","`id`='".$id."' and `password`='".$password."'");
			}
			echo 1;die;
		}else{
            echo 2;die;
        }
	}

	function checkTinyPassword_action(){
		$_POST=$this->post_trim($_POST);
		$password=md5(trim($_POST['password']));
		$id=intval($_POST['id']);
        $arr=$this->obj->DB_select_once("resume_tiny","`id`='".$id."' and `password`='".$password."'");
		if(isset($arr['id'])){
			if($_POST['operation_type']=="refresh"){
				$arr=$this->obj->DB_update_all("resume_tiny",'`time`='.time(),"`id`='".$id."'");
			}
			if($_POST['operation_type']=="remove"){
				$arr=$this->obj->DB_delete_all("resume_tiny","`id`='".$id."'");
			}
        	echo 1;die;
		}else{
            echo 2;die;
        }
	}
  function get_coupon_action(){
		$arr['coupon']='';
		if($_POST['id']){
			$rating=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['id']."'");
			if($rating['time_start']<time() && $rating['time_end']>time()){
				if($rating['coupon']>0){
					$coupon=$this->obj->DB_select_once("coupon","`id`='".$rating['coupon']."'");
				}
				$arr['coupon']=iconv("gbk","utf-8",$coupon['name']);
			}
			if($rating['time_start']<time() && $rating['time_end']>time()){
				$arr['price']=$rating['yh_price'];
				$price=$arr['price'];
			}else{
				$price=$rating['service_price'];
			}
		}
		echo json_encode($arr);die;
	}

	function indexajaxresume_action(){
		if($_POST){
			$M=$this->MODEL('comtc');
			$return=$M->invite_resume($_POST);
			if($return['status']){
				echo json_encode($return);die;
			}
		}
	}
	
	function emailcert_action(){
		session_start();
		if(!$this->uid || !$this->username){
			echo 0;die;
		}else{
			if($this->config['sy_email_set']!="1"){
				echo 3;die;
			}elseif($this->config['sy_email_cert']=="2"){
				echo 2;die;
			}elseif(!$_POST['authcode']){
				echo 5;die;
			}elseif(md5(strtolower($_POST['authcode']))!=$_SESSION['authcode'] || empty($_SESSION['authcode'])){
				echo 4;die;
			}else{
				$email=$_POST['email'];
				$randstr=rand(10000000,99999999);
				$sql['status']=0;
				$sql['step']=1;
				$sql['check']=$email;
				$sql['check2']=$randstr;
				$sql['ctime']=mktime();
				$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='1'");
				if(is_array($row)){
					$where['uid']=$this->uid;
					$where['type']='1';
					$this->obj->update_once("company_cert",$sql,$where);
					$this->obj->member_log("更新邮箱认证");
				}else{
					$sql['uid']=$this->uid;
					$sql['did']=$this->userdid;
					$sql['type']=1;
					$this->obj->insert_into("company_cert",$sql);
					$this->obj->member_log("添加邮箱认证");
				}
				$base=base64_encode($this->uid."|".$randstr."|".$this->config['coding']);
				$fdata=$this->forsend(array('uid'=>$this->uid,'usertype'=>$this->usertype));
				$data['uid']=$this->uid;
				$data['name']=$fdata['name'];
				$data['type']="cert";
				$data['email']=$email;
				$url=Url("qqconnect",array('c'=>'cert','id'=>$base),"1");
				$data['url']="<a href='".$url."'>点击认证</a> 如果您不能在邮箱中直接打开，请复制该链接到浏览器地址栏中直接打开：".$url;
				$data['date']=date("Y-m-d");
				$this->send_msg_email($data);
				echo "1";die;
			}
		}
	}
    function mobliecert_action(){
		if(!$this->config["sy_msguser"] || !$this->config["sy_msgpw"] || !$this->config["sy_msgkey"]||$this->config['sy_msg_isopen']!='1'){
			echo 4;die;
		}
		if(!$this->uid || !$this->username){
			echo 0;die;
		}else{
			$shell=$this->GET_user_shell($this->uid,$_COOKIE['shell']);
			if(!is_array($shell)){echo 5;die;}
			$moblie=$_POST[str];
			$randstr=rand(100000,999999);
			if($this->config['sy_msg_cert']=="2"){
				echo 3;die;
			}else{
				$num=$this->obj->DB_select_num("moblie_msg","`moblie`='".$moblie."' and `ctime`>'".strtotime(date("Y-m-d"))."'");
				if($num>=$this->config['moblie_msgnum']){
					echo 1;die;
				}
				$ip=fun_ip_get();
				$ipnum=$this->obj->DB_select_num("moblie_msg","`ip`='".$ip."' and `ctime`>'".strtotime(date("Y-m-d"))."'");
				if($ipnum>=$this->config['ip_msgnum']){
					echo 2;die;
				}
				$fdata=$this->forsend(array('uid'=>$this->uid,'usertype'=>$this->usertype));
				$data['uid']=$this->uid;
				$data['name']=$fdata['name'];
				$data['type']="cert";
				$data['moblie']=$moblie;
				$data['code']=$randstr;
				$data['date']=date("Y-m-d");
				$status=$this->send_msg_email($data);
				if($status=="发送成功!"){
					setcookie("moblie_code",$randstr,time()+120, "/");
					$sql['status']=0;
					$sql['step']=1;
					$sql['check']=$moblie;
					$sql['check2']=$randstr;
					$sql['ctime']=mktime();
					$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='2'");
					if(is_array($row)){
						$where['uid']=$this->uid;
						$where['type']='2';
						$this->obj->update_once("company_cert",$sql,$where);
						$this->obj->member_log("更新手机认证");
					}else{
						$sql['uid']=$this->uid;
						$sql['did']=$this->userdid;
						$sql['type']=2;
						$this->obj->insert_into("company_cert",$sql);
						$this->obj->member_log("添加手机认证");
					}
				}
				echo $status;die;
			}
		}
	}
	
	 function regcode_action(){
		if(strpos($this->config['code_web'],'注册会员')!==false){
		    session_start();
		    if($this->config['code_kind']==3){
				
				if(!gtauthcode($this->config,'mobile')){
					echo 6;die;
				}
			}else{
		        if(md5(trim($_POST['code']))!=$_SESSION['authcode'] || trim($_POST['code'])==''){
		            echo 5;die;
		        }
		    }
		}
		if($_POST['moblie']==""){
			echo 0;die;
		}
		$randstr=rand(100000,999999);

		if($this->config['sy_msguser']==""||$this->config['sy_msgpw']==""||$this->config['sy_msgkey']==""||$this->config['sy_msg_isopen']!='1'){
			echo 3;die;
		}else{
			$moblieCode = $this->obj->DB_select_once('company_cert',"`check`='".$_POST['moblie']."'");
			if((time()-$moblieCode['ctime'])<120){
				echo 4;die;
			}
			$num=$this->obj->DB_select_num("moblie_msg","`moblie`='".$_POST['moblie']."' and `ctime`>'".strtotime(date("Y-m-d"))."'");
			if($num>=$this->config['moblie_msgnum']){
				echo 1;die;
			}
			$ip=fun_ip_get();
			$ipnum=$this->obj->DB_select_num("moblie_msg","`ip`='".$ip."' and `ctime`>'".strtotime(date("Y-m-d"))."'");
			if($ipnum>=$this->config['ip_msgnum']){
				echo 2;die;
			}
			$status=$this->send_msg_email(array("moblie"=>$_POST['moblie'],"code"=>$randstr,"type"=>'regcode'));
			if($status=='发送成功!'){
				$data['did']=$this->config['did'];
				$data['uid']='0';
				$data['type']='2';
				$data['status']='0';
				$data['step']='1';
				$data['check']=$_POST['moblie'];
				$data['check2']=$randstr;
				$data['ctime']=time();
				$data['statusbody']='手机注册验证码';
				if(is_array($moblieCode) && !empty($moblieCode)){
					$this->obj->update_once("company_cert",$data,"`check`='".$_POST['moblie']."'");
				}else{
					$this->obj->insert_into("company_cert",$data);
				}
			}
			if($status){
				echo $status;die;
			}else{
				echo 3;die;
			}
		}
	}
	
	function wap_time_action(){
		if($_POST['type']=='year'){
			$data="<option value=''>请选择</option>";
			for($i=1;$i<=12;$i++){
				$data.="<option value=".$i.">".$i."</option>";
				
			}
		}
		echo $data;
	}
	function ajaxcity_action(){ 
		include(PLUS_PATH."city.cache.php");
		if(is_array($city_type[$_POST['id']])){
			$data.="<ul>";
			foreach($city_type[$_POST['id']] as $v){
				if($_POST['gettype']=="citys"){
					$data.='<li><a href="javascript:;" onclick="select_city(\''.$v.'\',\'citys\',\''.$city_name[$v].'\',\'three_city\',\'city\');">'.$city_name[$v].'</a></li>';
				}else{
					$data.='<li><a href="javascript:;" onclick="selects(\''.$v.'\',\'three_city\',\''.$city_name[$v].'\');">'.$city_name[$v].'</a></li>';
				}
			}
			$data.="</ul>";
		}
		echo $data;
	}
	function temporaryresume_action(){
	    $arr = array( 
	           'status' => '2',
               'url' => '',
               'msg' => ''
              );
		if(!$_POST['name'] || !$_POST['birthday'] || !$_POST['exp'] || !$_POST['edu'] || !$_POST['telphone']){
				$arr['status']='2';
				$arr['msg']=yun_iconv("gbk","utf-8",'请填写必要申请信息！');
		}else{
				
			if($_POST['actlogin']!='1'){
					
				session_start();
				if(!gtauthcode($this->config,'',$this->config['code_kind'])){
					if ($this->config['code_kind']=='3'){
							$arr['status']='2';
							$arr['msg']=iconv("gbk","utf-8",'请点击按钮进行验证！');
					}else{
							$arr['status']='2';
							$arr['msg']=iconv("gbk","utf-8",'验证码错误！');
					}
				 }else{
					 $res = true;
				 }
				

			}else{
				
				$res = false;
                $cert_validity = 1800;		//验证码有效时间
    			$cert_arr = $this->obj->DB_select_once("company_cert","`check`='".$_POST['telphone']."' and type='2' ORDER BY id DESC");
			
    			if (is_array($cert_arr)) {
    				if ((time()-$cert_arr['ctime']) <= $cert_validity) {
    				 	$res = $_POST['authcode'] == $cert_arr['check2'] ? true : false;
						if(!$res){
							$arr['status']='2';
							$arr['msg']=yun_iconv("gbk","utf-8",'短信验证码错误！');
						}
    				} else {
						$arr['status']='2';
						$arr['msg']=yun_iconv("gbk","utf-8",'短信验证码超时，请重新点击发送验证码！');
    				}
    			} else {
					$arr['status']='2';
					$arr['msg']=yun_iconv("gbk","utf-8",'短信验证码错误！');
    			}
			}
			
			

			if ($res) {
				$Member=$this->MODEL("userinfo");
				$ismoblie= $Member->GetMemberNum(array("moblie"=>$_POST['telphone']));
				if($ismoblie>0){
					$arr['status']='2';
					$arr['msg']=yun_iconv("gbk","utf-8",'手机已存在！');

				}else{
				
					

					$Job=$this->MODEL('job');
					$jobinfo=$Job->GetComjobOne(array('id'=>(int)$_POST['jobid']));
					$_POST['name']=yun_iconv("utf-8","gbk",$_POST['name']);
					$_POST['uname']=yun_iconv("utf-8","gbk",$_POST['uname']);
					$_POST['hy']=$jobinfo['hy'];
					if($jobinfo['job_post']){
						$_POST['job_classid']=$jobinfo['job_post'];
					}elseif($jobinfo['job1_son']){
						$_POST['job_classid']=$jobinfo['job1_son'];
					}else{
						$_POST['job_classid']=$jobinfo['job1'];
					}
					include PLUS_PATH."/user.cache.php";
					$_POST['jobstatus'] = $userdata['user_jobstatus'][0];
					$_POST['minsalary']=$jobinfo['minsalary'];
					$_POST['maxsalary']=$jobinfo['maxsalary'];
					$_POST['provinceid']=$jobinfo['provinceid'];
					$_POST['cityid']=$jobinfo['cityid'];
					$_POST['three_cityid']=$jobinfo['three_cityid'];
					$Resume=$this->MODEL("resume");
					if(intval($_POST['resumeid'])){
						$this->obj->update_once('temporary_resume',$_POST,array('id'=>intval($_POST['resumeid'])));
						$id=intval($_POST['resumeid']);
					}else{
						$id=$Resume->TemporaryResume($_POST);
					}
					$arr['status']='1';
					$arr['url']= Url('wap',array("c"=>"job","a"=>"applylogin",'jobid'=>intval($_POST['jobid']),"id"=>$id));
					$arr['msg']=yun_iconv("gbk","utf-8",'创建简历成功，请完成下一步！');
				}
			}
		}
		echo json_encode($arr);die;
	}
	

	function sendmsg_action(){
		

		if(!$this->config['sy_msg_isopen'] || !$this->config['sy_msg_login']){
			$this->layer_msg('网站未开启短信验证登录服务!');
		}else{
			session_start();
			if(!gtauthcode($this->config,'',$this->config['code_kind'])){
				if ($this->config['code_kind']=='3'){

						$this->layer_msg('请点击按钮进行验证！!');
				}else{
						$this->layer_msg('验证码错误！!');
				}
			 }else{

				$moblie=$_POST['moblie'];
				$res = $this->send_autocode($moblie, 6, 90, true);
				if($res == 5){
					$this->layer_msg('手机号有误!');
				}elseif ($res == 1) {
					$this->layer_msg('该手机号超过发送条数!');
				}elseif ($res == 2) {
					$this->layer_msg('该IP超过一天发送条数!');
				}elseif ($res == 3) {
					$this->layer_msg('手机用户不存在!');
				}elseif ($res == 4) {
					$this->layer_msg('未开启短信发送功能!');
				}elseif ($res == 6) {
					$this->layer_msg('验证码重复发送，请稍后!');
				}elseif($res == '发送成功!'){
					$this->layer_msg('发送成功!',9,0,'',2,1);
				}else{
					$this->layer_msg($res);
				}
			 }
		}
	}
	function checkuser($username,$name=''){

		$user = $this->obj->DB_select_once("member","`username`='".$username."'","`uid`");
		if($user['uid']){
			$name.="_".rand(1000,9999);
			return $this->checkuser($name,$username);
		}else{
			return $username;
		}
	}
	function userreg_action(){
		
		session_start();
		if(md5(strtolower($_POST['authcode']))!=$_SESSION['authcode']  || empty($_SESSION['authcode'])){
			unset($_SESSION['authcode']);
			$arr['status']='2';
			$arr['msg']=yun_iconv("gbk","utf-8",'验证码错误！');
		}else{
			$Resume=$this->MODEL("resume");
			$row=$Resume->SelectTemporaryResume(array("id"=>$_POST['resumeid']));
			$Member=$this->MODEL("userinfo");
			$ismoblie= $Member->GetMemberNum(array("moblie"=>$row['telphone']));
			if($ismoblie>0){
				$arr['status']='2';
				$arr['msg']=yun_iconv("gbk","utf-8",'当前手机号已被使用，请更换其他手机号！');
			}elseif(!$row['name'] || !$row['birthday'] || !$row['exp'] || !$row['edu'] || !$row['telphone']){
				$arr['status']='2';
				$arr['msg']=yun_iconv("gbk","utf-8",'请填写必要申请信息！');
			
			}else{

				$salt = substr(uniqid(rand()), -6);
				$pass = md5(md5($_POST['password']).$salt);
				$ip=fun_ip_get();
				$data=array();
				$data['username']=$this->checkuser($row['telphone']);
				$data['password']=$pass;
				$data['usertype']=1;
				$data['status']=1;
				$data['source']=12;
				$data['mobile']=$row['telphone'];
				$data['salt']=$salt;
				$data['reg_date']=time();
				$data['login_date']=time();
				$data['reg_ip']=$ip;
				$data['login_ip']=$ip;
				$data['did']=$this->config['did'];

				$userid=$Member->AddMember($data);
				if($userid){
					$Member->InsertReg("member_statis",array("uid"=>$userid,"resume_num"=>"1","did"=>$this->config['did']));
					$Member->InsertReg("resume",array("uid"=>$userid,'lastupdate'=>time()));
					$this->add_cookie($userid,$row['telphone'],$salt,"",$pass,1,1,$this->config['did']); 
					$Resume=$this->MODEL("resume");
					$row=$Resume->SelectTemporaryResume(array("id"=>$_POST['resumeid']));
					$edata['uid']=$userid;
					$edata['name']=$row['name'];
					$edata['hy']=$row['hy'];
					$edata['job_classid']=$row['job_classid'];
					$edata['provinceid']=$row['provinceid'];
					$edata['cityid']=$row['cityid'];
					$edata['three_cityid']=$row['three_cityid'];
					$edata['minsalary']=$row['minsalary'];
					$edata['maxsalary']=$row['maxsalary'];
					$edata['jobstatus']=$row['jobstatus'];
					$edata['type']=$row['type'];
					$edata['report']=$row['report'];
					$edata['defaults']=1;
					$edata['integrity']=55;
					$edata['ctime']=time();
					$edata['lastupdate']=time(); 
					$edata['did']=$this->config['did'];
					$edata['uname']=$rdata['name']=$row['uname'];
					$edata['edu']=$rdata['edu']=$row['edu'];
					$edata['exp']=$rdata['exp']=$row['exp'];
					$edata['sex']=$rdata['sex']=$row['sex'];
					$edata['birthday']=$rdata['birthday']=$row['birthday'];
					$edata['r_status']=$this->config['resume_status'];
					$edata['source']=12;
					$eid=$Resume->AddResume("resume_expect",$edata);
					$Resume->AddUserResume(array("uid"=>$userid,"eid"=>$eid,"expect"=>"1"));
					$Resume->DelTemporaryResume(array("id"=>$_POST['resumeid']));
					$rdata['def_job']=$eid;
					$rdata['resumetime']=time();
					$rdata['lastupdate']=time();
					$rdata['telphone']=$row['telphone'];
					$rdata['email']=$row['email'];
					$rdata['living']=$row['living'];
					$Member->UpdateUserinfo(array("usertype"=>"1","values"=>$rdata),array("uid"=>$userid));
					$Member->UpdateMember(array("moblie"=>$row['telphone'],"email"=>$row['email']),array("uid"=>$userid));
					if($this->config['integral_reg']>0){
						$Member->company_invtal($userid,$this->config['integral_reg'],true,"注册赠送",true,2,'integral',23);
					}
					$this->get_integral_action($userid,"integral_login","会员登录");
					if($this->config['integral_userinfo']>0){
						$this->company_invtal($userid,$this->config['integral_userinfo'],true,"首次填写基本资料",true,2,'integral',25);
					}			
					$this->get_integral_action($userid,"integral_add_resume","发布简历");
					$jobid=(int)$_POST['jobid'];
					$Job=$this->MODEL("job");
					$comjob=$Job->GetComjobOne(array("id"=>$jobid));
					$value['job_id']=$jobid;
					$value['com_name']=$comjob['com_name'];
					$value['job_name']=$comjob['name'];
					$value['com_id']=$comjob['uid'];
					$value['uid']=$userid;
					$value['eid']=$eid;
					$value['datetime']=mktime();
					$nid=$Job->AddUseridJob($value);
					$historyM = $this->MODEL('history');
					$historyM->addHistory('useridjob',$_POST['jobid']);
					$arr['status']='1';
					$arr['url']= Url('wap',array("c"=>"job","a"=>"view",'id'=>intval($_POST['jobid'])));
					$arr['msg']=yun_iconv("gbk","utf-8",'申请成功！');

					if($comjob['link_type']=='1'){
						$ComM=$this->MODEL("company");
						$job_link=$ComM->GetCompanyInfo(array("uid"=>$comjob['uid']),array("field"=>"`linkmail` as email,`linktel` as link_moblie"));
					}else{
						$job_link=$Job->GetComjoblinkOne(array("jobid"=>$jobid,"is_email"=>"1"),array("field"=>"`email`,`link_moblie`"));
					}
					if($this->config['sy_email_set']=="1"){
						if($job_link['email']){
							$contents=@file_get_contents(Url("resume",array("c"=>"sendresume","job_link"=>'1',"id"=>$eid)));
							$smtp = $this->email_set();
							$smtpusermail =$this->config['sy_smtpemail'];
							$sendid = $smtp->sendmail($job_link['email'],"您收到一份新的求职简历！——".$this->config['sy_webname'],$contents);
							if($sendid){
								$state = '1';
							}else{
								$state = '0';
							}
							$this->obj->insert_into("email_msg",array('uid'=>$comjob['uid'],'name'=>$comjob['com_name'],'cuid'=>'','cname'=>'','email'=>$job_link['email'],'title'=>"您收到一份新的求职简历！——".$this->config['sy_webname'],'content'=>$contents,'state'=>$state,'ctime'=>time(),'smtpserver'=>$smtp->user));
						}
					}
					if($this->config['sy_msg_isopen']=='1'){
						if($job_link['link_moblie']){
							$data=array('uid'=>$comjob['uid'],'name'=>$comjob['com_name'],'cuid'=>'','cname'=>'','type'=>'sqzw','jobname'=>$comjob['name'],'date'=>date("Y-m-d"),'moblie'=>$job_link['link_moblie']);
							$this->send_msg_email($data);
						}
					}
				}else{
					$arr['status']='3';
					$arr['msg']=yun_iconv("gbk","utf-8",'申请失败！');
				}
			}
			
		}
		
		echo json_encode($arr);die;
	}
	
	function pl_action(){
		$_POST['content']=iconv("utf-8", "gbk",$_POST['content']);
		$_POST['com_name']=iconv("utf-8", "gbk",$_POST['com_name']);
		$_POST['job_name']=iconv("utf-8", "gbk",$_POST['job_name']);
		if($this->uid==''||$this->username==''){
				echo 3;die;
			}
			if($this->usertype!="1"){
				echo 0;die;
			}
			$M=$this->MODEL("job");
			$black=$M->GetBlackOne(array('p_uid'=>$this->uid,'c_uid'=>(int)$_POST['job_uid']));
			if(!empty($black)){
				echo 7;die;
			}
			if(trim($_POST['content'])==""){
				echo 2;die;
			}
			if(trim($_POST['authcode'])==""){
				echo 4;die;
			}
			session_start();
			if(md5(strtolower($_POST['authcode']))!=$_SESSION['authcode'] || empty($_SESSION['authcode'])){
				echo 5;die;
			}
			$id=$M->AddMsg(array('uid'=>$this->uid,'username'=>$this->username,'jobid'=>trim($_POST['jobid']),'job_uid'=>trim($_POST['job_uid']),'content'=>trim($_POST['content']),'com_name'=>trim($_POST['com_name']),'job_name'=>trim($_POST['job_name']),'type'=>'1','datetime'=>time()));
			if($id){echo 1;die;}else{echo 6;die;}
	}
	
	function wap_train_action(){
		include(PLUS_PATH."subject.cache.php");
		$data="<option value=''>--请选择--</option>";
		if(is_array($subject_type[$_POST['id']])){
			foreach($subject_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$subject_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function unlock_action(){
	    session_start();
	    $srcstr = "0123456789";
	    mt_srand();
	    $strs = "";
	    for ($i = 0; $i < 4; $i++) {
	        $strs .= $srcstr[mt_rand(0, 9)];
	    }
	    $_SESSION["unlock"] = md5(strtolower($strs));
	    echo $strs;
	}
	function atn_action(){
		$id=(int)$_POST['id'];
		if($id>0){
			if($this->uid&&$this->username){
				if($this->usertype!="1"){
					echo 0;die;
				}
				if($_POST['id']==$this->uid){
					echo 4;die;
				}
				$atninfo = $this->obj->DB_select_once("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
				$user=$this->obj->DB_select_once("member","`uid`='".$id."'","`usertype`");
				
				$table="company";
				
				$comurl = Url('ask',array("c"=>"friend","a"=>"myquestion","uid"=>$id));
	
				if(is_array($atninfo)&&!empty($atninfo)){
					$this->obj->DB_delete_all("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
					$this->obj->DB_update_all($table,"`ant_num`=`ant_num`-1","`uid`='".$id."'");
					$content="取消了对<a href=\"".$comurl."\" target=\"_bank\">".$name."</a>的关注！";
					$this->addstate($content,2);
					$msg_content = "用户 ".$this->username." 取消了对你的关注！";
					$this->automsg($msg_content,(int)$_POST['id']);
					$this->obj->member_log("取消了对".$name."的关注！");
					echo "2";die;
				}else{
					$this->obj->DB_insert_once("atn","`uid`='".$this->uid."',`sc_uid`='".$id."',`usertype`='".(int)$this->usertype."',`sc_usertype`='".$user['usertype']."',`time`='".time()."'");
					$this->obj->DB_update_all($table,"`ant_num`=`ant_num`+1","`uid`='".$id."'");
					$content="关注了<a href=\"".$comurl."\" target=\"_bank\">".$name."</a>";
					$this->addstate($content,2);
					$msg_content = "用户 ".$this->username." 关注了你！";
					$this->automsg($msg_content,(int)$_POST['id']);
					$this->obj->member_log("关注了".$name);
					echo "1";die;
				}
			}else{
				echo "3";die;
			}
		}
	}
	function atn_company_action(){
	    $id=(int)$_POST['id'];
	    if($id>0){
	        if($this->uid){
	            if($this->usertype!='1'){
	                echo '4';die;
	            }
	            $atninfo = $this->obj->DB_select_once("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
	            $comurl = $this->config['sy_weburl']."/company/index.php?id=".$id;
	            $company=$this->obj->DB_select_once("company","`uid`='".$id."'","`name`");
	            $name = $company['name'];
	            if(is_array($atninfo)&&$atninfo){
	                $this->obj->DB_delete_all("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
	                $this->obj->DB_update_all('company',"`ant_num`=`ant_num`-1","`uid`='".$id."'");
	                $content="取消了对<a href=\"".$comurl."\" target=\"_bank\">".$name."</a>关注";
	                $this->addstate($content,2);
	                $msg_content = "用户 ".$this->username." 取消了对你的关注！";
	                $this->automsg($msg_content,$id);
	                $this->obj->member_log("取消了对".$name."关注");
	                echo "2";die;
	            }else{
	                $this->obj->DB_insert_once("atn","`uid`='".$this->uid."',`sc_uid`='".$id."',`usertype`='".(int)$this->usertype."',`sc_usertype`='2',`time`='".time()."'");
	                $this->obj->DB_update_all('company',"`ant_num`=`ant_num`+1","`uid`='".$id."'");
	                $content="关注了<a href=\"".$comurl."\" target=\"_bank\">".$name."</a>";
	                $this->addstate($content,2);
	                $msg_content = "用户 ".$this->username." 关注了你！";
	                $this->automsg($msg_content,$id);
	                $this->obj->member_log("关注了".$name);
	                echo "1";die;
	            }
	        }else{
	            echo "3";die;
	        }
	    }
	}
 	
	
	function getjob_action(){
		include(PLUS_PATH."job.cache.php");
		if(is_array($job_type[$_POST['id']])){	
		    if($_POST['type']=="jobone_son"){				
				$data.='<li onclick="check_job_li(\''.$_POST['id'].'\',\'jobone\');"><a href="javascript:;">全部</a></li>';
			}
			if($_POST['type']=="job_post"){				
				$data.='<li onclick="check_job_li(\''.$_POST['id'].'\',\'jobone_son\');"><a href="javascript:;">全部</a></li>';
			}			
			foreach($job_type[$_POST['id']] as $v){				
				if($_POST['type']=="jobone_son"){
					if(!empty($job_type[$v])){

						$data.='<li class="qc'.$v.'" onclick="Categoryt(\''.$v.'\',\''.$job_name[$v].'\',\'job_post\');"><a href="javascript:;">'.$job_name[$v].'</a></li>';
					}else{
						
						$data.='<li onclick="check_job_li(\''.$v.'\',\'job1_son\');"><a href="javascript:;">'.$job_name[$v].'</a></li>';
					}
						
					}else{
						$data.='<li onclick="check_job_li(\''.$v.'\',\'job_post\');"><a href="javascript:;">'.$job_name[$v].'</a></li>';
				    }				
			}			
		}else{
			if($_POST['type']=="jobone_son"){				
				$data.='<li onclick="check_job_li(\''.$_POST['id'].'\',\'jobone\');"><a href="javascript:;">全部</a></li>';
			}
			if($_POST['type']=="job_post"){				
				$data.='<li onclick="check_job_li(\''.$_POST['id'].'\',\'jobone_son\');"><a href="javascript:;">全部</a></li>';
			}
		}
		echo $data;
	}
	
	
	function getltjob_action(){
		include(PLUS_PATH."ltjob.cache.php");
		if(is_array($ltjob_type[$_POST['id']])){	
		    if($_POST['type']=="jobtwo"){				
			$data.='<li onclick="check_ltjob_li(\''.$_POST['id'].'\',\'jobone\');"><a href="javascript:;">全部</a></li>';
			}		
			foreach($ltjob_type[$_POST['id']] as $v){				
				if($_POST['type']=="jobtwo"){
						$data.='<li class="qc'.$v.'" onclick="check_ltjob_li(\''.$v.'\',\'jobtwo\');"><a href="javascript:;">'.$ltjob_name[$v].'</a></li>';
					}			
			}			
		}
		echo $data;
	}
	
	
	function getcity_action(){
		include(PLUS_PATH."city.cache.php");
		if(is_array($city_type[$_POST['id']])){	
			if($_POST['type']=="cityid"){				
				$data.='<li onclick="check_city_li(\''.$_POST['id'].'\',\'provinceid\');"><a href="javascript:;">全部</a></li>';
			}
			if($_POST['type']=="three_cityid"){				
				$data.='<li onclick="check_city_li(\''.$_POST['id'].'\',\'cityid\');"><a href="javascript:;">全部</a></li>';
			}		
			foreach($city_type[$_POST['id']] as $v){				
				if($_POST['type']=="cityid"){
					if(!empty($city_type[$v])){
						$data.='<li class="qc'.$v.'" onclick="gradet(\''.$v.'\',\''.$city_name[$v].'\',\'three_cityid\');"><a href="javascript:;">'.$city_name[$v].'</a></li>';
					}else{
						$data.='<li onclick="check_city_li(\''.$v.'\',\'cityid\');"><a href="javascript:;">'.$city_name[$v].'</a></li>';
					}
					
				}else{
					$data.='<li onclick="check_city_li(\''.$v.'\',\'three_cityid\');"><a href="javascript:;">'.$city_name[$v].'</a></li>';	
				}
			}		
		}else{
			if($_POST['type']=="cityid"){				
				$data.='<li onclick="check_city_li(\''.$_POST['id'].'\',\'provinceid\');"><a href="javascript:;">全部</a></li>';
			}
			if($_POST['type']=="three_cityid"){				
				$data.='<li onclick="check_city_li(\''.$_POST['id'].'\',\'cityid\');"><a href="javascript:;">全部</a></li>';
			}
		}
		echo $data;
	}	
	
	function zphcom_action(){
		$bid=(int)$_GET['bid'];
		$Zph=$this->MODEL("zph");
		$space=$Zph->GetZphspaceOnce(array("id"=>$bid));
		$sid=$Zph->GetZphspaceOnce(array("id"=>$space['keyid']));
		if(!$this->uid || !$this->username || $this->usertype!=2){
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","您还没有登录，<a href='javascript:void(0);' onclick=\"".$this->config['sy_weburl']."/wap/index.php?c=login\" style='color:#1d50a1'>请先登录</a>！");
		}elseif(!$_GET['jobid']){
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","你还没有选择职位");
		}else{
			$User=$this->MODEL("userinfo");
			$statis=$User->GetUserstatisOne(array("uid"=>$this->uid),array("usertype"=>"2"));
			if($statis['rating_type']!=2){
				if($statis['zph_num']>=1){
					$bmtype=2;
				}else{
					if($this->config['com_integral_online']=='1'){
						$bmtype=1;
						if($space['price']>$statis['integral']){
							$arr['status']=0;
							$arr['content']=iconv("gbk","utf-8","你的".$this->config['integral_pricename']."不足，请先充值！");
							echo json_encode($arr);die;
						}
					}else{
						$arr['status']=0;
						$arr['content']=iconv("gbk","utf-8","你的招聘会报名次数已用完！");
						echo json_encode($arr);die;
					}
				}
			}
			$zphcom=$Zph->GetZphComOnce(array("uid"=>$this->uid,"zid"=>(int)$_POST['zid']));
			if(!empty($zphcom)){
				$arr['status']=0;
				$arr['content']=iconv("gbk","utf-8","您已经参与该招聘会");
			}else{
				$jobidarr=@explode(",",$_GET['jobid']);
				$array=array();
				foreach($jobidarr as $v){
					if(!in_array($v,$array)){
						$array[]=$v;
					}
				}
				$info=$Zph->GetZphOnce(array("id"=>(int)$_GET['zid']),array("field"=>"`sid`,`did`"));
				if($sid['keyid']!=$info['sid']){
					$arr['status']=0;
					$arr['content']=iconv("gbk","utf-8","非法操作！");
				}else{
					$bid=$Zph->GetZphComOnce(array("zid"=>(int)$_GET['zid'],"bid"=>(int)$_GET['bid']));
					if(!empty($bid)){
						$arr['status']=0;
						$arr['content']=iconv("gbk","utf-8","该展位已被预定，请选择其他展位！");
					}else{
						$sql['did']=$info['did'];
						$sql['uid']=$this->uid;
						$sql['zid']=(int)$_GET['zid'];
						$sql['bid']=(int)$_GET['bid'];
						$sql['sid']=$sid['keyid'];
						$sql['cid']=$space['keyid'];
						$sql['jobid']=pylode(",",$array);
						$sql['ctime']=mktime();
						$sql['status']=0;
						if($bmtype==1){
							$sql['price']=$space['price'];
						}
						$id=$this->obj->insert_into("zhaopinhui_com",$sql);
						if($id){
							if($bmtype==2){
								$User->UpdateUserStatis(array("`zph_num`=`zph_num`-1"),array("uid"=>$this->uid),array("usertype"=>"2"));
							}else if($bmtype==1&&$space['price']){
								$this->company_invtal($this->uid,$space['price'],false,"招聘会报名",true,2,'integral');//积分操作记录
							}
							$arr['status']=1;
							$arr['content']=iconv("gbk","utf-8","报名成功,等待管理员审核");
							$this->obj->member_log("报名招聘会");
						}else{
							$arr['status']=0;
							$arr['content']=iconv("gbk","utf-8","报名失败,请稍后重试");
						}
					}
				}
			}
		}
		echo json_encode($arr);
	}
	
}	
?>