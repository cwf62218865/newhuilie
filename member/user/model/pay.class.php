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
class pay_controller extends user{
	function index_action(){
		$this->public_action();
		$this->user_tpl('pay');
	}
	function dingdan_action(){
		$integral=intval($_POST['integral']);
		$pay=intval($_POST['pay']);
		if($_POST['submit']&& ($integral || $pay)){
			if($integral){
				if($this->config['integral_min_recharge']&&$integral<$this->config['integral_min_recharge']){
					$integral=$this->config['integral_min_recharge'];
				}
				$price = $integral/$this->config['integral_proportion'];
				$data['type']=2;
			}elseif ($pay){
				if($this->config['money_min_recharge']&&$pay<$this->config['money_min_recharge']){
					$pay=$this->config['money_min_recharge'];
				}
				$price = $pay;
				$data['type']=4;
			}
			$dingdan=mktime().rand(10000,99999);
			$data['order_id']=$dingdan;
			$data['order_price']=$price;
			$data['order_time']=mktime();
			$data['order_state']="1";
			$data['integral']=$integral;
			$data['order_remark']=$_POST['remark'];
			$data['uid']=$this->uid;
			$data['did']=$this->userdid;
			$id=$this->obj->insert_into("company_order",$data);
			if($id){
				$this->obj->member_log("�µ��ɹ� ����ID".$dingdan);
				$this->ACT_layer_msg("�µ��ɹ����븶�",9,"index.php?c=payment&id=".$id);
			}else{
				$this->ACT_layer_msg("�ύʧ�ܣ��������ύ������",8,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->ACT_layer_msg("��������ȷ������ȷ��д��",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>