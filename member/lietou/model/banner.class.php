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
class banner_controller extends company{
	function index_action(){
		$banner=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
		$this->yunset("banner",$banner); 
		$this->public_action();
		$this->yunset("js_def",2);
		$this->lt_tpl("banner");
	}
	
	function save_action(){
	    if($_POST['save']){
			$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'",'integral');
			if($statis['integral']<$this->config['integral_banner']&&$this->config['integral_banner_type']!=1){
				$this->ACT_layer_msg($this->config['integral_pricename']."���㣬���ȳ�ֵ��",8,"index.php?c=banner");
			}else{
			    if(is_uploaded_file($_FILES['pic']['tmp_name'])){
			        $upload=$this->upload_pic("../data/upload/company/",false,$this->config['com_uppic']);
			        $pictures=$upload->picture($_FILES['pic']);
			        $this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
			        $data['uid']=$this->uid;
    		        $data['pic']=str_replace("../data/upload/company/","./data/upload/company/",$pictures);
    		        $this->obj->insert_into("banner",$data);
    		        $this->obj->member_log("�ϴ���ҵ���");
    		        $this->get_integral_action($this->uid,"integral_banner","�ϴ���ҵ���");
    		        $this->ACT_layer_msg("���óɹ���",9,"index.php?c=banner");
			    }
			}
		}
        if($_POST['update']){
            if(is_uploaded_file($_FILES['pic']['tmp_name'])){
                $upload=$this->upload_pic("../data/upload/company/",false,$this->config['com_uppic']);
                $pictures=$upload->picture($_FILES['pic']);
                $this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
                $row=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
                if(is_array($row)){
                    unlink_pic('.'.$row['pic']);
                }
                $this->obj->update_once("banner",array("pic"=>str_replace("../data/upload/company/","./data/upload/company/",$pictures)),array("uid"=>$this->uid));
                $this->obj->member_log("�޸���ҵ���");
                $this->ACT_layer_msg("�޸ĳɹ���",9,"index.php?c=banner");
            }	
        }
	}
}
?>