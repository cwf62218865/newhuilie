<?php
/* *
 * $Author ��PHPYUN�����Ŷ�
 *
 * ����: http://www.phpyun.com
 *
 * ��Ȩ���� 2009-2016 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
 *
 * ����������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class pay_controller extends company
{
	function index_action()
	{
		$this->public_action();
		$statis=$this->company_satic();
		if($_GET['type']=='vip'){
			$rows=$this->obj->DB_select_all("company_rating","`service_price`<>'' and `display`='1' and `category`=1 order by sort desc","name,service_price,id");
			$this->yunset("rows",$rows);
		}
		$this->yunset("statis",$statis);
		$this->yunset("js_def",4);
		$this->lt_tpl('pay');
	}
	function dingdan_action(){
		if($_POST['price'] || $_POST['money_int'] || $_POST['comvip'] || $_POST['comservice']){
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`name`,`hy`");
			if($company['name']==''||$company['hy']==''){
						$this->ACT_layer_msg("�������ƻ������ϣ�",8,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['comvip']){
				$comvip=(int)$_POST['comvip'];
				$ratinginfo =  $this->obj->DB_select_once("company_rating","`id`='".$comvip."'");
				if($ratinginfo['time_start']<time() && $ratinginfo['time_end']>time()){
					$price = $ratinginfo['yh_price'];
				}else{
					$price = $ratinginfo['service_price'];
				}
				if($ratinginfo['category']==1 || $ratinginfo['category']==2){
					$data['type']='1';
				}else{
					$data['type']='5';
				}
			}elseif($_POST['comservice']){
				$id=(int)$_POST['comservice'];
				$detailinfo =  $this->obj->DB_select_once("company_service_detail","`id`='".$id."'");
				$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
				if ($statis){
					$rating=$statis['rating'];
					$discount=$this->obj->DB_select_once("company_rating"," `id`='".$rating."' ");
					if($discount){
						$dis=$discount['service_discount'];
						if ($dis !=0 && $dis !=100){
							$price = $detailinfo['service_price'] * $dis *0.01;
						}else {
							$price = $detailinfo['service_price'];
						}
					}
				}
				$data['type']='5';
			}elseif($_POST['price_int']){
				$integral=intval($_POST['price_int']);
				if($this->config['integral_min_recharge']&&$integral<$this->config['integral_min_recharge']){
					$integral=$this->config['integral_min_recharge'];
				}
				$price = $integral/$this->config['integral_proportion'];
				$data['type']='2';
			}elseif($_POST['money_int']){
				$pay=intval($_POST['money_int']);
				if($this->config['money_min_recharge'] && $pay <$this->config['money_min_recharge']){
					$pay=$this->config['money_min_recharge'];
				}
				$price = $pay;
				$data['type']='4';
			}else {
				$this->ACT_layer_msg("��������ȷ������ȷ��д��",8,$_SERVER['HTTP_REFERER']);
			}
			if($data['type']=='2'&&$integral<1){
				$this->ACT_layer_msg("����ȷ��д����������",8,$_SERVER['HTTP_REFERER']);
			}
			$dingdan=mktime().rand(10000,99999);
			$data['order_id']=$dingdan;
			$data['order_price']=$price;
			$data['order_time']=mktime();
			$data['order_state']="1";
			$data['order_remark']=trim($_POST['remark']);
			$data['uid']=$this->uid;
			$data['did']=$this->userdid;
			$data['rating']=$_POST['comvip']?$_POST['comvip']:$_POST['comservice'];
			$data['integral']=$integral;
			$id=$this->obj->insert_into("company_order",$data);
			if($id){
				$this->obj->member_log("�µ��ɹ�,����ID".$dingdan);
				$this->ACT_layer_msg("�µ��ɹ����븶�",9,$this->config['sy_weburl']."/member/index.php?c=payment&id=".$id);
			}else{
				$this->ACT_layer_msg("�ύʧ�ܣ��������ύ������",8,$_SERVER['HTTP_REFERER']);
			}
		}else{

			$this->ACT_layer_msg("�ύʧ�ܣ�����ȷ�ύ������",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>