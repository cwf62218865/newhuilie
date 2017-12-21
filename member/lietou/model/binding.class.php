<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2017 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ����������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class binding_controller extends company{
	function index_action(){
		$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'");
		$this->yunset("member",$member);
		if(($member['qqid']!=""||$member['wxid']!=""||$member['unionid']!=""||$member['sinaid']!="") && $member['restname']=="0"){
			$this->yunset("setname",1);
		}
		$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("company",$company);
		$cert=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
		if ($cert['check']){
		    $cert['check']=str_replace('./','../', $cert['check']);
		}
		$this->yunset("cert",$cert);
		$this->public_action();
		$this->get_user();
		$this->yunset("js_def",2);
		$this->lt_tpl("binding");
	}
	function save_action(){
		if($_POST['moblie']){
			$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `check`='".$_POST['moblie']."' and `type`='2'");
			if(!empty($row)){
				if($row['check2']!=$_POST['code']){
					echo 3;die;
				}
				
				$this->obj->DB_update_all("member","`moblie`=''","`moblie`='".$row['check']."'");
				$this->obj->DB_update_all("resume","`moblie_status`='0',`telphone`=''","`telphone`='".$row['check']."'");
				$this->obj->DB_update_all("company","`moblie_status`='0',`linktel`=''","`linktel`='".$row['check']."'");
				$this->obj->DB_update_all("member","`moblie`='".$row['check']."'","`uid`='".$this->uid."'");
				$this->obj->DB_update_all("company","`linktel`='".$row['check']."',`moblie_status`='1'","`uid`='".$this->uid."'");
				$this->obj->DB_update_all("company_cert","`status`='1'","`uid`='".$this->uid."' and `check2`='".$_POST['code']."'");
				$this->obj->member_log("�ֻ���");
				$pay=$this->obj->DB_select_once("company_pay","`pay_remark`='�ֻ���' and `com_id`='".$this->uid."'");
				if(empty($pay)){
					$this->get_integral_action($this->uid,"integral_mobliecert","�ֻ���");
				}
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
	    if($_POST['upfile']){
		    $row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
	        if($this->config['com_cert_status']=="1"){
				$sql['status']=0;
			}else{
				$sql['status']=1;
			}
			if ($_POST['upfile']=='info'){
			    $company_name=iconv('utf-8','gbk',$_POST['company_name']);
			}else{
			    $company_name=$_POST['company_name'];
			}
			$this->obj->DB_update_all("company","`name`='".$company_name."',`yyzz_status`='".$sql['status']."'","`uid`='".$this->uid."'");
			$sql['step']=1;
			$sql['check']=str_replace('../','./', $_POST['com_cert']);
			$sql['check2']="0";
			$sql['ctime']=mktime();
			if(is_array($row)){
				$where['uid']=$this->uid;
				$where['type']='3';
				$id=$this->obj->update_once("company_cert",$sql,$where);
				$this->obj->member_log("����Ӫҵִ��");
			}else{
				$sql['uid']=$this->uid;
				$sql['did']=$this->userdid;
				$sql['type']=3;
				$id=$this->obj->insert_into("company_cert",$sql);
				$this->obj->member_log("�ϴ�Ӫҵִ��");
				if($this->config['com_cert_status']!="1"){
					$this->get_integral_action($this->uid,"integral_comcert","��֤Ӫҵִ��");
				}
			}
		    if($id){
		        $this->ACT_layer_msg("�ϴ��ɹ���",9,1);
		    }else{
		        $this->ACT_layer_msg("�ϴ�ʧ�ܣ�",9,1);
		    }
		}
		if ($_POST['uppic']){
		    $row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
		    if(is_uploaded_file($_FILES['pic']['tmp_name'])){
		        $upload=$this->upload_pic("../data/upload/cert/",false,$this->config['com_uppic']);
		        $pictures=$upload->picture($_FILES['pic']);
		        $this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
		        $pic = str_replace("../data/upload/cert","./data/upload/cert",$pictures);
		        $id=$this->obj->DB_update_all('company_cert',"`check`='".$pic."'","`uid`='".$this->uid."' and type=3");
		
		        if($id){
		            unlink_pic(".".$row['check']);
		            $data['url']=$pictures;
		        }else{
		            $data['msg']=iconv('gbk','utf-8','�ϴ�ʧ��');
		        }
		    }else{
		        $data['msg']=iconv('gbk','utf-8','���ϴ�Ӫҵִ��');
		    }
		    echo json_encode($data);
		}
	}
	
	function del_action(){
		if($_GET['type']=="moblie"){
			$this->obj->DB_update_all("company","`moblie_status`='0'","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="email"){
			$this->obj->DB_update_all("company","`email_status`='0'","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="qqid"){
			$this->obj->DB_update_all("member","`qqid`=''","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="sinaid"){
			$this->obj->DB_update_all("member","`sinaid`=''","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="wxid"){
			$this->obj->DB_update_all("member","`wxid`='',`wxopenid`='',`unionid`=''","`uid`='".$this->uid."'");
		}
		$this->layer_msg("����󶨳ɹ���",9,0,$_SERVER['HTTP_REFERER']);
	}
}
?>