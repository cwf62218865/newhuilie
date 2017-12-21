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

include(dirname(dirname(dirname(__FILE__)))."/data/api/wxpay/wxpay_data.php");

define("APIKEY",$wxpaydata['sy_wxpaykey']);
define("PATHCERT",$wxpaydata['sy_wxpem_cert']); 
define("PATHKEY",$wxpaydata['sy_wxpem_key']);
define("PATHCA",$wxpaydata['sy_wxpem_ca']);
define("APPID",$wxpaydata['sy_wxpayappid']);
define('MCHID', $wxpaydata['sy_wxpaymchid']);

class ApiWxHb{
	
	const APIKEY   = APIKEY;
	const PATHCERT = PATHCERT;
	const PATHKEY  = PATHKEY;
	const PATHCA   = PATHCA;
	const APPID    = APPID;
	const MCHID    = MCHID;

	var $para;

	function set_para($key,$value){
		$this->para[$key] = $value;
	}
	
	function create_noncestr( $length = 24 ) {  
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$str ="";  
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);   
		}  
		return $str;  
	}



	function check_sign_para(){
		
		if(!$this->para["nonce_str"]   || 
			!$this->para["mch_billno"] || 
			!$this->para["mch_id"]     || 
			!$this->para["wxappid"]    || 
			!$this->para["send_name"]  ||
			!$this->para["re_openid"]  || 
			!$this->para["total_amount"] || 
			!$this->para["total_num"]  || 
			!$this->para["wishing"]    || 
			!$this->para["client_ip"]  || 
			!$this->para["act_name"]
			)
		{
			return false;
		}
		return true;

	}

	function create_sign(){
		if($this->check_sign_para() == false) {
			return 'FAIL';die;
		}
		ksort($this->para);
		$tempsign = "";
		foreach ($this->para as $k => $v){
			if (null != $v && "null" != $v && "sign" != $k) {
				$tempsign .= $k . "=" . $v . "&";
			}
		}
		$tempsign = substr($tempsign, 0, strlen($tempsign)-1);
		$tempsign .="&key=". APIKEY;

		return strtoupper(md5($tempsign));
	}

	function create_xml(){
		$return = $this->set_para('sign', $this->create_sign());
		if($return == 'FAIL'){
			return;
		}else{
			return $this->ArrayToXml($this->para);
		}
		
	}

	function ArrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.= "</xml>";
        return $xml; 
    }

	function curl_post_ssl($url, $vars, $second=30)
	{
		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		
		
		curl_setopt($ch,CURLOPT_SSLCERT,PATHCERT);
 		curl_setopt($ch,CURLOPT_SSLKEY,PATHKEY);
 		curl_setopt($ch,CURLOPT_CAINFO,PATHCA);
	 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	 
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
		
			curl_close($ch);
			return false;
		}
	}
	
	function sendHb($data){
		
		$this->set_para("nonce_str", $this->create_noncestr());
		$this->set_para("mch_billno", $data['mch_billno']);
		$this->set_para("mch_id", MCHID);
		$this->set_para("wxappid", APPID);
		$this->set_para("send_name", iconv("gbk","utf-8",$data['send_name']));
		$this->set_para("re_openid", $data['openid']);
		$this->set_para("total_amount", $data['total_amount']);
		$this->set_para("total_num", 1);
		$this->set_para("wishing", iconv("gbk","utf-8",$data['wishing']));
		$this->set_para("client_ip", $data['client_ip']);
		$this->set_para("act_name", iconv("gbk","utf-8",$data['act_name']));
		
		
		$this->set_para("remark", iconv("gbk","utf-8",$data['remark']));

		$postxml = $this->create_xml(); 
		if(!$postxml){
			$responseObj = array('return_code'=>'FAIL','return_msg'=>iconv('gbk','utf-8','签名参数错误！'));
		}else{
			$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
			$response = $this->curl_post_ssl($url, $postxml);
			$responseObj = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
			$responseObj = (array)$responseObj;  
		}
		
		return $responseObj;
	}

	function sendPay($data){


		$this->set_para("mchid", MCHID);
		$this->set_para("mch_appid", APPID);
		$this->set_para("nonce_str", $this->create_noncestr());
		$this->set_para("partner_trade_no", $data['partner_trade_no']);
		
		$this->set_para("openid", $data['openid']);
		$this->set_para("amount", $data['amount']);
		$this->set_para("desc", iconv("gbk","utf-8",$data['desc']));
		$this->set_para("spbill_create_ip", $data['spbill_create_ip']); 

		$postxml = $this->create_xml();
		
		if(!$postxml){
			$responseObj = array('return_code'=>'FAIL','return_msg'=>iconv('gbk','utf-8','签名参数错误！'));
		}else{
			$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
			$response = $this->curl_post_ssl($url, $postxml);
			$responseObj = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
			$responseObj = (array)$responseObj;  

		}
		
		return $responseObj;
	}


}

?>