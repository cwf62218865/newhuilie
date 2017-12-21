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
function wxOrder($data=array()){
	
	
	require_once APP_PATH."api/wxpay/lib/WxPay.Api.php";
	require_once APP_PATH."api/wxpay/WxPay.NativePay.php";

	$notify = new NativePay();
	
	$input = new WxPayUnifiedOrder();
	$input->SetBody($data['body']);
	$input->SetOut_trade_no($data['id']);
	$input->SetTotal_fee($data['total_fee']*100);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetNotify_url($data['url']."/api/wxpay/notify.php");
	$input->SetTrade_type("NATIVE");
	$input->SetProduct_id($data['id']);
	$result = $notify->GetPayUrl($input);
	$url = $result["code_url"];
	return $url;
}


function wxWapOrder($data=array()){
	
	require_once APP_PATH."api/wxpay/lib/WxPay.Api.php";
	require_once APP_PATH."api/wxpay/WxPay.JsApiPay.php";

	$tools = new JsApiPay();

	$openId = $tools->GetOpenid();
	
	if(!$openId){
		return false;
	}
	$input = new WxPayUnifiedOrder();


	$input = new WxPayUnifiedOrder();
	$input->SetBody($data['body']);
	$input->SetOut_trade_no($data['id']);
	$input->SetTotal_fee($data['total_fee']*100);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetNotify_url($data['url']."/api/wxpay/notify.php");
	$input->SetTrade_type("JSAPI");
	$input->SetOpenid($openId);
	$order = WxPayApi::unifiedOrder($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	
	return $jsApiParameters;
}
function wxWapOrderMweb($data=array()){
	
	require_once APP_PATH."api/wxpay/lib/WxPay.Api.php";
	require_once APP_PATH."api/wxpay/WxPay.JsApiPay.php";

	$tools = new JsApiPay();

	
	$input = new WxPayUnifiedOrder();


	$input = new WxPayUnifiedOrder();
	$input->SetBody($data['body']);
	$input->SetOut_trade_no($data['id']);
	$input->SetTotal_fee($data['total_fee']*100);
	$input->SetTime_start(date("YmdHis"));
	$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetNotify_url($data['url']."/api/wxpay/notify.php");
	$input->SetTrade_type("MWEB");
	
	$order = WxPayApi::unifiedOrder($input);
	
	
	return $order;
}

?>