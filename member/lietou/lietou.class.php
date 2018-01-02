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
class lietou extends common{
	
	function public_action(){
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);

		$lietou=$this->obj->DB_select_once("lietou","`uid`='".$this->uid."'");

		$atn=$this->obj->DB_select_once("atn","`uid`='".$this->uid."'");
		$guweninfo=$this->obj->DB_select_once("company_consultant","`id` ='".$lietou['conid']."'");
		if($guweninfo['logo']){
		    $guweninfo['logo']=str_replace("./",$this->config['sy_weburl']."/",$guweninfo['logo']);
		}else{
		    $guweninfo['logo']=$this->config['sy_weburl'].'/'.$this->config['sy_guwen'];
		}
		$qq=explode(',', $this->config['sy_qq']);
		$this->yunset('kfqq',$qq[0]);
		include(PLUS_PATH."menu.cache.php");
		$report=$this->obj->DB_select_once("report","p_uid='".$this->uid."' order by inputtime desc");
		$this->yunset("report",$report);
		$this->yunset("lietou",$lietou);
		$this->yunset("atn",$atn);
		$this->yunset("guweninfo",$guweninfo);		
	}

	function company_satic(){
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
		if($statis['rating']){
			$rating=$this->obj->DB_select_once("company_rating","`id`='".$statis['rating']."'");
		}
		$statis['rating_type'] = $rating['type'];
		if($statis['vip_etime']<time()){
			if($statis['vip_etime']>'1'){
				$nums=0;
			}else if($statis['rating_type']=='1'  &&  $statis['vip_etime']<'1'){
				$nums=1;
			}
			if($nums<1){
				
				if($this->config['com_vip_done']=='0'){
					$data['job_num']=$data['down_resume']=$data['invite_resume']=$data['editjob_num']=$data['breakjob_num']=$data['part_num']=$data['editpart_num']=$data['breakpart_num']='0';
					
					$statis['rating_name']=$data['rating_name']="非会员";
					
					$statis['rating_type']=$statis['rating']=$data['rating_type']=$data['rating']="";
					
					$statis['vip_etime']=$data['vip_etime']="";
					
					$where['uid']=$this->uid;
					
					$this->obj->update_once("company_statis",$data,$where);
					
				}elseif ($this->config['com_vip_done']=='1'){
					
					$ratingM = $this->MODEL('rating');
					
					$rat_value=$ratingM->rating_info();
					
					$this->obj->DB_update_all("company_statis",$rat_value,"`uid`='".$this->uid."'");
				}
				
			}
		}
		if($statis['autotime']>=time()){
			$statis['auto'] = 1;
		}
		if($statis['vip_etime']>time() || $statis['vip_etime']==0){
			if($statis['rating_type']=="2"){
				$addltjobnum=$addjobnum=$addpartjobnum='1';
			}else{
				if($statis['job_num']>0){
					$addjobnum='1';
				}else{
					if($this->config['com_integral_online']=='1'){
						$addjobnum='2';
					}else{
						$addjobnum='0';
					}
				}
				
				if($statis['part_num']>0){
					$addpartjobnum='1';
				}else{
					if($this->config['com_integral_online']=='1'){
						$addpartjobnum='2';
					}else{
						$addpartjobnum='0';
					}
				}
			}
		}else {
			if($this->config['com_integral_online']=='1'){ 
				$addjobnum=$addpartjobnum=$addltjobnum='2';
			}else{
				$addjobnum=$addpartjobnum=$addltjobnum='0';
			}
		} 
		$statis['addjobnum']=$addjobnum;
		$statis['addltjobnum']=$addltjobnum;
		$statis['addpartjobnum']=$addpartjobnum;
		$statis['pay_format']=number_format($statis['pay'],2);
		$statis['integral_format']=number_format($statis['integral']);
		$this->yunset("addltjobnum",$addltjobnum);
		$this->yunset("addjobnum",$addjobnum);
		$this->yunset("addpartjobnum",$addpartjobnum);
		$this->yunset("statis",$statis);
		$this->yunset("rating",$rating);
		return $statis;
	}
	
	function get_com($type,$statis=array()){
		if($statis['uid']==''){
			$statis=$this->company_satic();
		}
		if($statis['rating_type']==""&&$statis['rating']){
			
			$rating=$this->obj->DB_select_once("company_rating","`id`='".$statis['rating']."'");
			$this->obj->DB_update_all("company_statis","`rating_type`='".$rating['type']."'","`uid`='".$this->uid."'");
			$statis['rating_type']=$rating['type'];
			
		}
		if($statis['rating_type'] && $statis['rating']){
			if($type==1){
				if($statis['rating_type']=='1' && $statis['job_num']>0 && ($statis['vip_etime']<1 || $statis['vip_etime']>=time())){
					$value="`job_num`=`job_num`-1";
				}elseif($statis['rating_type']=='2' && $statis['vip_etime']>time()){
					$value="";
				}else{
					$this->intergal($type,$statis);
				}
			}elseif($type==3){
				if($statis['rating_type']=='1' && $statis['breakjob_num']>0 && ($statis['vip_etime']<1 || $statis['vip_etime']>=time())){
					$value="`breakjob_num`='".($statis['breakjob_num']-1)."'";
				}else if($statis['rating_type']=='2' && $statis['vip_etime']>time()){
					$value="";
				}else{
					$this->intergal($type,$statis);
				}
			}elseif($type==7){
				if($statis['rating_type']=='1' && $statis['part_num']>0 && ($statis['vip_etime']<1 || $statis['vip_etime']>=time())){
					$value="`part_num`='".($statis['part_num']-1)."'";
				}else if($statis['rating_type']=='2' && $statis['vip_etime']>time()){
					$value="";
				}else{
					$this->intergal($type,$statis);
				}
			}elseif($type==9){
				if($statis['rating_type']=='1' && $statis['breakpart_num']>0 && ($statis['vip_etime']<1 || $statis['vip_etime']>=time())){
					$value="`breakpart_num`='".($statis['breakpart_num']-1)."'";
				}else if($statis['rating_type']=='2' && $statis['vip_etime']>time()){
					$value="";
				}else{
					$this->intergal($type,$statis);
				}
			}
			if($value){
				$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
			}
		}else{
			$this->intergal($type,$statis);
		}
	}
	
	function intergal($type,$statis){
		
		$data=array("1"=>array('msg'=>'会员发布职位用完,购买会员服务更快捷！','url'=>'index.php?c=job&w=1'),"3"=>array('msg'=>'会员刷新职位用完！','url'=>'index.php?c=pay'),"4"=>array('msg'=>'会员发布猎头职位用完，购买会员服务更快捷！','url'=>'index.php?c=job&w=1'),"6"=>array('msg'=>'会员刷新猎头职位用完！','url'=>'index.php?c=job&w=1'),"7"=>array('msg'=>'会员发布兼职职位用完，购买会员服务更快捷！','url'=>'index.php?c=part'),"9"=>array('msg'=>'会员刷新兼职职位用完！','url'=>'index.php?c=part'));
		if($this->config['com_integral_online']=="1"){
			if($type==1 && $this->config['integral_job']){
				if($this->config['integral_job_type']=="1"){
					$auto=true;
				}else if($statis['integral']<$this->config['integral_job']){
					$this->ACT_layer_msg("你的".$this->config['integral_pricename']."不够发布职位！",8,"index.php?c=pay");
				}else if($statis['integral']>=$this->config['integral_job']){
					$auto=false;
				}
				
				$nid=$this->company_invtal($this->uid,$this->config['integral_job'],$auto,"发布职位",true,2,'integral',6);
			
			}elseif($type==3 && $this->config['integral_jobefresh']){
				if($this->config['integral_jobefresh_type']=="1"){
					$auto=true;
				}else if($statis['integral']<$this->config['integral_jobefresh']){
					if($_GET){
						$this->layer_msg("你的".$this->config['integral_pricename']."不够刷新职位！",8,0,"index.php?c=pay");
					}else{
						$this->ACT_layer_msg("你的".$this->config['integral_pricename']."不够刷新职位！",8,"index.php?c=pay");
					}
				}else{
					$auto=false;
				}
				
				$nid=$this->company_invtal($this->uid,$this->config['integral_jobefresh'],$auto,"刷新职位",true,2,'integral',8);
				
			}elseif($type==7 && $this->config['integral_partjob']){
				if($this->config['integral_partjob_type']=="1"){
					$auto=true;
				}if($statis['integral']<$this->config['integral_partjob']){
					$this->ACT_layer_msg("你的".$this->config['integral_pricename']."不够发布兼职职位！",8,"index.php?c=pay");
				}else{
					$auto=false;
				}
				$nid=$this->company_invtal($this->uid,$this->config['integral_partjob'],$auto,"发布兼职职位",true,2,'integral',18);
			}elseif($type==9 && $this->config['integral_partjobefresh']){
				if($this->config['integral_partjobefresh_type']=="1"){
					$auto=true;
				}else if($statis['integral']<$this->config['integral_partjobefresh']){
					$this->ACT_layer_msg("你的".$this->config['integral_pricename']."不够刷新兼职职位！",8,"index.php?c=pay");
				}else{
					$auto=false;
				}
				$nid=$this->company_invtal($this->uid,$this->config['integral_partjobefresh'],$auto,"刷新兼职职位",true,2,'integral',20);
			}
		}else{
			$this->ACT_layer_msg($data[$type]['msg'],8,$data[$type]['url']);
		}
	}
		
	
	function lt_tpl($tpl){
		$this->yuntpl(array('member/lietou/'.$tpl));
	}
	
	function get_user(){
		$rows=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		if(!$rows['name'] || !$rows['address'] || !$rows['pr']){
			$this->ACT_msg("index.php?c=info","请先完善企业资料！");
		}
		return $rows;
	}




	function job(){
		if($_GET['r_uid']){
			if($_GET['r_reason']==""){
				$this->ACT_layer_msg("举报内容不能为空！",8,"index.php?c=down");
			}
			$data['p_uid']=(int)$_GET['r_uid'];
			$data['inputtime']=time();
			$data['c_uid']=$this->uid;
			$data['did']=$this->userid;
			$data['eid']=(int)$_GET['eid'];
			$data['r_name']=$_GET['r_name'];
			$data['usertype']=(int)$this->usertype;
			$data['username']=$this->username;
			$data['r_reason']=$_GET['r_reason'];
			$haves=$this->obj->DB_select_once("report","`p_uid`=".$data['p_uid']." and `c_uid`=".$data['c_uid']." and `usertype`=".$data['usertype']."","id");
			if(is_array($haves)){
				$this->ACT_layer_msg("您已经举报过该用户！",8,"index.php?c=down");
			}else{
				$nid=$this->obj->insert_into("report",$data);
				if($nid){
					$this->obj->member_log("举报人才《".$_GET['r_name']."》");
					$this->ACT_layer_msg("操作成功！",9,"index.php?c=down");
				}else{
					$this->ACT_layer_msg("操作失败！",8,"index.php?c=down");
				}
			}
		}

		if($_GET['up']){
			$this->get_com(3);
			$nid=$this->obj->DB_update_all("company_job","`lastupdate`='".time()."'","`uid`='".$this->uid."' and `id`='".(int)$_GET['up']."'");
			if($nid){
				$this->obj->DB_update_all("company","`lastupdate`='".time()."'","`uid`='".$this->uid."'");
				$job=$this->obj->DB_select_once("company_job","`id`='".(int)$_GET['up']."'","name");
				$this->obj->member_log("刷新职位《".$job['name']."》",1,4);
				$this->layer_msg('刷新职位成功！',9,0,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('刷新失败！',8,0,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['gotimeid']){
			$_POST['day']=intval($_POST['day']);
			if($_POST['day']<1){
 				$this->ACT_layer_msg("请正确填写延期天数！",8);
			}else{
				$posttime=(int)$_POST['day']*86400;
				$ids=@explode(",",$_POST['gotimeid']);
				if(is_array($ids)){
					foreach($ids as $value){
						$where=array();$data=array();
						$row=$this->obj->DB_select_once("company_job","`id`='".(int)$value."' and `uid`='".$this->uid."'","`state`,`edate`");
						$where['id']=(int)$value;
						$where['uid']=$this->uid;
						if($row['state']==2 || $row['edate']<time()){
							$time=time()+$posttime;
							$data['edate']=$time;
							$data['state']=1;
							$id=$this->obj->update_once("company_job",$data,$where);
						}else{
							$time=$row['edate']+$posttime;
							$id=$this->obj->update_once("company_job",array("edate"=>$time),$where);
						}
					}
				}
				if($id){
					$this->obj->member_log("职位延期");
					$this->ACT_layer_msg("延期成功！",9,$_SERVER['HTTP_REFERER']);
				}else{
					$this->ACT_layer_msg("延期失败！",8,$_SERVER['HTTP_REFERER']);
				}
			}
		}
		if($_POST['status'] && ($_POST['id']|| is_array($_POST['id']))){
			if(is_array($_POST['id'])){
				$id=pylode(",",$_POST['id']);
			}else if($_POST['id']){
				$id=(int)$_POST['id'];
			}
			$where="`uid`='".$this->uid."' and `id` in (".$id.")";
			if($_POST['status']==2){
				$_POST['status']=0;
			}
			$nid=$this->obj->update_once("company_job",array("status"=>(int)$_POST['status']),$where);
			if($nid){
				$this->obj->member_log("修改职位发布状态");
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		if($_GET['del'] || is_array($_POST['checkboxid'])){
			if(is_array($_POST['checkboxid'])){
				$layer_type=1;
				$delid=pylode(",",$_POST['checkboxid']);
			}else if($_GET['del']){
				$layer_type=0;
				$delid=(int)$_GET['del'];
			}
			$nid=$this->obj->DB_delete_all("company_job","`uid`='".$this->uid."' and `id` in (".$delid.")"," ");
			$this->obj->DB_delete_all("company_job_link","`uid`='".$this->uid."' and `jobid` in (".$delid.")"," ");
			if($nid){
				$newest=$this->obj->DB_select_once("company_job","`uid`='".$this->uid."' order by lastupdate DESC","`lastupdate`");

				$this->obj->DB_delete_all("userid_job","`com_id`='".$this->uid."' and `job_id` in (".$delid.")"," ");
				$this->obj->DB_delete_all("look_job","`com_id`='".$this->uid."' and `jobid` in (".$delid.")"," ");
				$this->obj->DB_delete_all("fav_job","`job_id` in (".$delid.")"," ");
				$this->obj->DB_delete_all("user_entrust_record","`jobid` in (".$delid.") and `comid`='".$this->uid."'","");
                $this->obj->DB_delete_all("report","`usertype`=1 and `type`=0 and `eid` in (".$delid.")","");
   				$this->obj->update_once("company",array("jobtime"=>$newest['lastupdate']),array("uid"=>$this->uid));
				$this->obj->member_log("删除职位",1,3);
				$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	function add_invoice_record($post,$order_id,$oid){
		if($post['linkway']=='1'){
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`address`");
			$link=",`link_man`='".$company['linkman']."',`link_moblie`='".$company['linktel']."',`address`='".$company['address']."'";
		}else{
			$post=$this->post_trim($post);
			if($post['link_man']==''||$post['link_moblie']==''||$post['address']==''){
				$this->ACT_layer_msg("联系人、联系电话、寄送地址均不能为空！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$link=",`link_man`='".$post['link_man']."',`link_moblie`='".$post['link_moblie']."',`address`='".$post['address']."'";
			}
		}
		$record=$this->obj->DB_select_once("invoice_record","`order_id`='".$order_id."' and `uid`='".$this->uid."'","id");
		if($record['id']){
			$this->obj->DB_update_all("invoice_record","`title`='".trim($post['invoice_title'])."',`status`='0',`addtime`='".time()."'".$link,"`id`='".$record['id']."'");
		}else{
			$iid=$this->obj->DB_insert_once("invoice_record","`oid`='".$oid."',`order_id`='".$order_id."',`uid`='".$this->uid."',`did`='".$this->userdid."',`title`='".trim($post['invoice_title'])."',`status`='0',`addtime`='".time()."'".$link);
			if($iid==false||$iid==''){$this->ACT_layer_msg("发票信息添加失败！",8,$_SERVER['HTTP_REFERER']);}
		}
	}
	
	function wnameup($namekey,$wname,$type){
		$wanmeinfo = $this->obj->DB_select_all("company_statis","`$namekey`='$wname' AND `uid`<>'".$this->uid."'");
		if(is_array($wanmeinfo)&&!empty($wanmeinfo))
		{
			$this->ACT_layer_msg("该帐号已经被绑定，如有人恶意绑定请想管理员申诉！",8,"index.php?c=Web&type=".$type);
		}else{
			$this->obj->update_once("company_statis",array($namekey=>$wname),array("uid"=>$this->uid));
		}
	}
	
	function logout_action(){
		$this->logout();
	}
	
	function HandleError($message){
		echo $message;
	}
	
	function CreateFirstName($file_extension ){
		$num=date('mdHis').rand(1,100);
		$fileName=$num.".".$file_extension;
		return $fileName;
	}
	
	function CreateNextName($file_extension,$file_dir){
		$fileName_arr = scandir($file_dir,1);
		$fileName=$fileName_arr[0];
		$aa=floatval($fileName);
		$num=0;
		$num=(1+$aa);
		if(empty($aa)){
			$num = date('mdHis').rand(1,100);
		}
		return $num.".".$file_extension;
	}
	
	function createdatefilename($file_extension){
		date_default_timezone_set('PRC');
		return date('mdHis').rand(1,100).".".$file_extension;
	}
	
	function create_folders($dir){
    return is_dir($dir) or ($this->create_folders(dirname($dir)) and mkdir($dir, 0777));
  }
  
	function part(){
		if($_POST['gotimeid']){
			$_POST['day']=intval($_POST['day']);
			if($_POST['day']<1){
 				$this->ACT_layer_msg("请正确填写延期天数！",8);
			}else{
				$posttime=(int)$_POST['day']*86400;
				$ids=@explode(",",$_POST['gotimeid']);
				if(is_array($ids)){
					foreach($ids as $value){
						$where=array();$data=array();
						$row=$this->obj->DB_select_once("partjob","`id`='".(int)$value."' and `uid`='".$this->uid."'","`state`,`deadline`,`edate`");
						if ($row['deadline']){
							$time=$row['deadline']+$posttime;
						}else{
							$time='';							
						}
						if ($row['edate']){
						    $edate=$row['edate']+$posttime;
						}else{
						    $edate='';
						}
						$where['id']=(int)$value;
						$where['uid']=$this->uid;
						if($row['state']==2 && $time>time()){
							$data['deadline']=$time;
							$data['state']=1;
							$data['edate']=$edate;
							$id=$this->obj->update_once("partjob",$data,$where);
						}else{
							$id=$this->obj->update_once("partjob",array("deadline"=>$time,'edate'=>$edate),$where);
						}
					}
				}
				if($id)
				{
					$this->obj->member_log("兼职职位延期");
					$this->ACT_layer_msg("兼职延期成功！",9,$_SERVER['HTTP_REFERER']);
				}else{
					$this->ACT_layer_msg("兼职延期失败！",8,$_SERVER['HTTP_REFERER']);
				}
			}
		}
		if($_GET['up']){
			$this->get_com(9);
			$nid=$this->obj->DB_update_all("partjob","`lastupdate`='".time()."'","`uid`='".$this->uid."' and `id`='".(int)$_GET['up']."'");
			if($nid){
				$part=$this->obj->DB_select_once("partjob","`id`='".(int)$_GET['up']."'","name");
				$this->obj->member_log("刷新兼职《".$part['name']."》",1,4);
				$this->layer_msg('刷新兼职职位成功！',9,0,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('刷新失败！',8,0,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['recid']){
			$id=(int)$_POST['recid'];
			$_POST['day']=intval($_POST['day']);
			if($_POST['day']<1){
				$this->ACT_layer_msg("请正确填写推荐天数！",8,$_SERVER['HTTP_REFERER']);
			}
			$reow=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","integral");
			$part=$this->obj->DB_select_once("partjob","`id`='".$id."' and `uid`='".$this->uid."'","name,rec_time");
			if($part['rec_time']<time())
			{
				$time=time()+$_POST['day']*86400;
			}else{
				$time=$part['rec_time']+$_POST['day']*86400;
			}
			$integral=$this->config['com_recpartjob']*$_POST['day'];
			if($reow['integral']<$integral && $this->config['com_recpartjob_type']=="2")
			{
				$this->ACT_layer_msg("您的".$this->config['integral_pricename']."不足，请充值！",8,"index.php?c=pay");
			}else{
				if($this->config['com_recpartjob_type']=="1")
				{
					$auto=true;
				}else{
					$auto=false;
				}
				$this->company_invtal($this->uid,$integral,$auto,"推荐兼职职位",true,2,'integral',12);
			}
			$data['rec']=1;
			$data['rec_time']=$time;
			$where['id']=$id;
			$where['uid']=$this->uid;
			$this->obj->update_once("partjob",$data,$where);
			$this->obj->member_log("发布推荐兼职职位《".$part['name']."》",1,1);
 			$this->ACT_layer_msg("推荐兼职成功！",9,$_SERVER['HTTP_REFERER']);
		}
	}

	/*
	 * 职位解析
	 */
	function jobs_parse($jobs){
        include  PLUS_PATH."/city.cache.php";
        $cache_array = $this->obj->cacheget();
        $arr_rows = "";
        foreach ($jobs as $key=>$list){
            $list['name_n'] = "<font color='red'>".mb_substr($list['name'],0,80,"GBK")."</font>";
            $list['com_n'] = $list['com_name'];
            $list['hy_n'] = $cache_array['industry_name'][$list['hy']];
            $list['pr_n'] = $cache_array['comclass_name'][$list[pr]];
            $list['mun_n'] = $cache_array['comclass_name'][$list[mun]];
            if($list[minsalary]&&$list[maxsalary]){
                $list[job_salary] =$list[minsalary]."-".$list[maxsalary];
            }elseif($list[minsalary]){
                $list[job_salary] =$list[minsalary]."以上";
            }else{
                $list[job_salary] ="面议";
            }
            $list['job_city_one'] = $city_name[$list[provinceid]];
            $list['job_city_two'] = $city_name[$list[cityid]];
            $list['job_url'] = Url("job",array("c"=>"comapply","id"=>$list[id]),"1");
            $list['com_url'] = Url("company",array("c"=>"show","id"=>$list[uid]));
//            str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$city_name[$value[provinceid]])
            $arr_rows[] = $list;
        }

        return $arr_rows;
    }

    /*
     * 项目推荐解析
     */
    function obj_parse($obj){
        $arr_obj = "";
        foreach ($obj as $list){
            $this->obj->DB_select_num("userid_job","job_id=".$list['job_id']." and identity=3","");
        }
    }

    /*
     * 简历分页列表
     */
    function resume_page($where='',$pageurl='',$limit=20,$field='*',$rowsname='rows'){
        $rows=array();
        $page=$_GET['page']<1?1:$_GET['page'];

        $ststrsql=($page-1)*$limit;
        $num=$this->obj->DB_select_num("pt_resume",$where);
        if($num>$limit){
            $pages=ceil($num/$limit);
            $pagenav=Page($page,$num,$limit,$pageurl,$notpl=false,null);
        }
        $rows=$this->obj->DB_select_all("pt_resume",$where.' limit '.$ststrsql.','.$limit,$field);
        return array('total'=>$num,'pagenav'=>$pagenav,$rowsname=>$rows);
    }


    /*
     * 推荐信息分页列表 目前只针对猎头部分
     */
    function recommend_page($where='',$pageurl='',$limit=10,$field='*',$rowsname='rows'){
        $rows=array();
        $page=$_GET['page']<1?1:$_GET['page'];

        $ststrsql=($page-1)*$limit;
        $num=$this->obj->DB_select_num("userid_job",$where);
        if($num>$limit){
            $pages=ceil($num/$limit);
            $pagenav=Page($page,$num,$limit,$pageurl,$notpl=false,null);
        }
        $rows=$this->obj->DB_select_all("userid_job",$where.' limit '.$ststrsql.','.$limit,$field);
        $arr_row = "";
        foreach ($rows as $list){
            $list['resume_name'] = $this->obj->DB_select_once("pt_resume","id=".$list['resume_id'],"name");
            $list['resume_name'] = $list['resume_name']['name'];
            $list['recommend_count'] = $this->obj->DB_select_num("userid_job","uid=".$this->uid." and job_id=".$list['job_id']);
            $list['resume_count'] = $this->obj->DB_select_num("down_resume","uid=".$this->uid." and job_id=".$list['job_id']);
            $arr_row[] = $list;
        }

        return array('total'=>$num,'pagenav'=>$pagenav,$rowsname=>$arr_row);
    }


    function error_msg($content){
        echo $content;exit();
    }

    function success_msg($content){
        echo $content;exit();
    }

    /*
     * 根据职位id获取相关信息
     */
    function job_more($job_id){
        $job = $this->obj->DB_select_once("company_job"," id=".$job_id,"uid,name,com_name");
        return $job;

    }


    function _format_date($time){
        $t=time()-$time;
        $f=array(
            '31536000'=>'年',
            '2592000'=>'个月',
            '604800'=>'星期',
            '86400'=>'天',
            '3600'=>'小时',
            '60'=>'分钟',
            '1'=>'秒'
        );
        foreach ($f as $k=>$v)    {
            if (0 !=$c=floor($t/(int)$k)) {
                return $c.$v.'前';
            }
        }
    }


    /*
     * 猎头排行
     * datekind 时间类型 month表示月，week表示周，day表示日
     */
    function lietou_rank($datekind="month"){
        if($datekind=="month"){
            $begintime=mktime(0,0,0,date('m'),1,date('Y'));
            $endtime=mktime(23,59,59,date('m'),date('t'),date('Y'));
        }elseif ($datekind=="week"){
            $begintime=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
            $endtime=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
        }elseif ($datekind=="day"){
            $begintime=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endtime=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        }

        $where = " datetime>".$begintime." and datetime<".$endtime." and identity=3  group by uid having recommend_result=1";
        $this->obj->DB_select_all("userid_job",$where);
        $lietou  = $this->obj->DB_select_all("lietou","1 order by download_num desc limit 0,10");
        foreach ($lietou as $list){

        }
    }
}
?>