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
class login_controller extends common{
	function index_action(){
		$this->get_moblie();
	
		if(preg_match("/^[a-zA-Z0-9_-]+$/",$_GET['wxid'])){
			$wxid = $_GET['wxid'];
			setcookie("wxid",$_GET['wxid'],time() + 86400, "/");
		}elseif($_COOKIE['wxid']){
			$wxid = $_COOKIE['wxid'];
		}
		if(preg_match("/^[a-zA-Z0-9_-]+$/",$_GET['unionid'])){
			setcookie("unionid",$_GET['unionid'],time() + 86400, "/");
		}
		if(preg_match("/^[a-zA-Z0-9_-]+$/",$_GET['wxloginid'])){
			setcookie("wxloginid",$_GET['wxloginid'],time() + 86400, "/");
		}
		if($wxid){
			if($wxid == $_COOKIE['wxid']){
				$this->yunset("wxid",$wxid);
				$this->yunset("wxnickname",$_COOKIE['wxnickname']);
				$this->yunset("wxpic",$_COOKIE['wxpic']);
			}else{
				$wxM = $this->MODEL('weixin');
				$wxinfo = $wxM->getWxUser($wxid);
				if($wxinfo['nickname']){
					$this->yunset("wxid",$wxid);
					setcookie("wxnickname",iconv('utf-8','gbk',$wxinfo['nickname']),time() + 86400, "/");
					$this->yunset("wxnickname",iconv('utf-8','gbk',$wxinfo['nickname']));
					setcookie("wxpic",$wxinfo['headimgurl'],time() + 86400, "/");
					$this->yunset("wxpic",$wxinfo['headimgurl']);
				}
			}
		}
		if($this->uid || $this->username){
			if((int)$_GET['bind']=='1'){
				$this->unset_cookie();
				$data['msg']='重新绑定您的求职账户！';
			}elseif($_GET['wxid']){
				$this->unset_cookie();
			}else{
				$this->wapheader('member/index.php');
			}
		}
			
		$checkurl=$_COOKIE['checkurl'];
		unset($checkurl);
    $this->yunset("checkurl",$checkurl);
	
		if($_POST['submit']){
      $UserinfoM=$this->MODEL('userinfo');
			if($_POST['wxid']){
				$wxparse = '&wxid='.$_POST['wxid'];
			}
			$username = yun_iconv("utf-8","gbk",$_POST['username']);
			$moblie = yun_iconv("utf-8","gbk",$_POST['moblie']);
			if(!$_POST['act_login']){
				if(strstr($this->config['code_web'],'前台登录')){
				session_start();
				if(!gtauthcode($this->config,'',$this->config['code_kind'])){
					if ($this->config['code_kind']=='3'){
							$this->layer_msg('请点击按钮进行验证！',9,0,'',2);
					}else{
							$this->layer_msg('验证码错误！',10,0,'',2);
					}
				  }
				}
			}
			if ($this->config['sy_msg_isopen'] && $this->config['sy_msg_login'] && $_POST['act_login']) {
				if(!$this->CheckMoblie($moblie)){
					$this->layer_msg('手机号码不正确!');
				}
				if(!is_array($this->FetchMoblie($moblie))){
					$this->layer_msg('手机号码不存在!');
				}
			}else {
			 	if(!$this->CheckRegUser($username) && !$this->CheckRegEmail($username) && ($username!="")){
					$this->layer_msg('用户名包含特殊字符或为空!');
				}
			}
			
				if($this->config['sy_uc_type']=="uc_center" && !$_POST['act_login']){
					$ucinfo = $this->uc_open();
					if(strtolower($ucinfo['UC_CHARSET']) =='utf8' || strtolower($ucinfo['UC_DBCHARSET'])=='utf8'){
						$uname = iconv('gbk','utf-8',$username);
					}else{
						$uname = $username;
					}
					list($uid, $uname, $password, $email) = uc_user_login($uname, $_POST['password']);
					if($uid=='-1'){
						$user = $UserinfoM->GetMemberOne(array("username"=>$username),array("field"=>"username,email,uid,password,salt"));
						$pass = md5(md5($_POST['password']).$user['salt']);
						if($pass==$user['password']){
							$uid = $user['uid'];
							$uid = uc_user_register($user['username'],$_POST['password'],$user['email']);
							$uc_user_register_msg_arr = array( 
										 '-1' => '当前用户名不合法',
							       '-2' => '当前用户名包含论坛禁止的词语',
							       '-4' => '当前用户 Email 格式不符合论坛规则！',
							       '-5' => '当前用户 Email 论坛不允许注册！',
							       '-6' => '当前用户 Email 与论坛其他用户重复！'
							      );
							if(array_key_exists($uid,$uc_user_register_msg_arr)){
								$this->layer_msg($uc_user_register_msg_arr[$uid],9,0,'',2);
							}
							list($uid, $username, $password, $email) = uc_user_login($uname, $_POST['password']);
						}else{
							$this->layer_msg('账户或密码错误！',9,0,'',2);
						}
					}else if($uid > 0) {
						$ucsynlogin=uc_user_synlogin($uid);
						$msg =  '登录成功！';
						$user = $UserinfoM->GetMemberOne(array("username"=>$username),array("field"=>"`uid`,`usertype`,`email_status`,`status`"));
						if(!empty($user)){
							if (session_id() == ""){session_start();}
							$pass = md5(md5($_POST['password']).$user['salt']);

							if($pass!=$user['password']){
								$this->layer_msg('账户密码错误！',9,0,'',2);
							}
							if($_SESSION['qq']['openid']){
								$UserinfoM->UpdateMember(array("qqid"=>$_SESSION['qq']['openid']),array("uid"=>$user['uid']));
								unset($_SESSION['qq']);
							}
							if($_SESSION['wx']['openid']){
								$udate = array('wxid'=>$_SESSION['wx']['openid']);

								if($_SESSION['wx']['unionid']){
									$udate['unionid']  = $_SESSION['wx']['unionid'];
								}
								$UserinfoM->UpdateMember($udate,array("uid"=>$user['uid']));
								unset($_SESSION['wx']);
							}
							if($_SESSION['sina']['openid']){

								$UserinfoM->UpdateMember(array("sinaid"=>$_SESSION['sina']['openid']),array("uid"=>$user['uid']));
								unset($_SESSION['sina']);
							}
							if($_COOKIE['wxid']){
								$UserinfoM->UpdateMember(array('wxid'=>'','unionid'=>'','wxopenid'=>''),array('wxid'=>$_COOKIE['wxid']));
								$UserinfoM->UpdateMember(array('wxid'=>$_COOKIE['wxid'],'unionid'=>$_COOKIE['unionid'],'wxbindtime'=>time()),array('uid'=>$user['uid']));
								
								if($_COOKIE['wxloginid']){
									$UserinfoM->UpdateWxlogin(array('wxid'=>$_COOKIE['wxid'],'unionid'=>$_COOKIE['unionid'],'status'=>'1'),array('wxloginid'=>$_COOKIE['wxloginid']));
								}

								setcookie("unionid",'',time() - 86400, "/");
								setcookie("wxloginid",'',time() - 86400, "/");
							}
							if(!$user['usertype']){
								$this->unset_cookie();
								$this->addcookie("username",$username,time()+3600);
								$this->addcookie("password",$_POST['password'],time()+3600);
								$this->layer_msg($ucsynlogin,9,0,Url("login",array("c"=>"utype"),"1"),2,3);
							}
							if($user['status']=="2"){
								$this->layer_msg('您的账号已被锁定!',9,0,'',2);
							}
							if($user['usertype']=="2" && $this->config['com_status']!="1" && $user['status']!="1"){
								$this->layer_msg('您还没有通过审核',9,0,'',2);
							}
							
							
							if($this->config['user_status']=="1"){
								if($user['usertype']=='1'){
									$Resume=$this->MODEL("resume");
									$info=$Resume->SelectResumeOne(array("uid"=>$user['uid']),"`name`,`email_status`,`birthday`");
									if($info['email_status']=="1"){
										$this->layer_msg('您的账户还未激活,请查阅邮箱激活邮件!',9,0,'',2);
									}
								}
							}
							if($_POST['loginname']){
								setcookie("loginname",$username,time()+8640000);
							}
							$this->autoupjob($user['uid'],$user['usertype']);
							if($_COOKIE['wxid']){
							 
							 $this->sendredpack(array('type'=>'2','openid'=>$_COOKIE['wxid']));
							 setcookie("wxid",'',time() - 86400, "/");
							}
						}else{
							
							$this->unset_cookie();
							$this->addcookie("username",$username,time()+3600);
							$this->addcookie("password",$_POST['password'],time()+3600);
							$this->layer_msg('论坛用户请先激活您的身份!'.$ucsynlogin,9,0,Url("wap",array("c"=>"login",'a'=>'utype')),2);
						}
						$this->layer_msg($msg.$ucsynlogin,9,0,Url("wap",array("c"=>"login",'a'=>'utype')),2);

					} elseif($uid == -2) {
						$msg =  '密码错误';
					} elseif($uid == -3)  {
						$msg = '论坛安全提问错误!';
					}else{
						$msg = '登录失败!';
					}
					$this->layer_msg($msg.$ucsynlogin,9,0,Url("wap",array("c"=>"login",'a'=>'utype')),2);
				}else{
				
					
					if ($this->config['sy_msg_isopen'] && $this->config['sy_msg_login'] && $_POST['act_login']) {
						
						$userinfo = $UserinfoM->GetMemberOne(array('moblie'=> $moblie),array('field'=>"uid,username,usertype,password,uid,salt,status,did,login_date"));
						if(!is_array($userinfo)){
							$data['msg']='用户不存在！';
							$this->layer_msg($data['msg'],9,0,'',2);
						}
						$cert_validity = 1800;	
						$cert_arr = $this->obj->DB_select_once("company_cert","`uid`='".$userinfo['uid']."' and type='2'  ORDER BY id desc");
						if (is_array($cert_arr)) {
							if((time()-$cert_arr['ctime']) <= $cert_validity){
							 	$res = $_POST['dynamiccode'] == $cert_arr['check2'] ? true : false;
							}else {
							 	$this->layer_msg('验证码验证超时，请重新点击发送验证码！'); 
							}
						}else {
						 	$this->layer_msg('验证码发送不成功，请重新点击发送验证码！'.$moblie); 
						}
					}else{
						$userinfo = $UserinfoM->GetMemberOne(array('username'=>$username),array('field'=>"username,usertype,password,uid,salt,status,did,login_date"));
						if(!is_array($userinfo)){
							$data['msg']='用户不存在！';
							$this->layer_msg($data['msg'],9,0,'',2);
						}
						$res = md5(md5($_POST['password']).$userinfo['salt']) == $userinfo['password'] ? true : false;
					}
					if($res){
						
						if($userinfo['usertype']=='1'){
							$Resume=$this->MODEL("resume");
							$info=$Resume->SelectResumeOne(array("uid"=>$userinfo['uid']),"`email_status`");
							if($this->config['user_status']=="1" &&$info['email_status']!="1"){
								$data['msg']='您的账户还未激活，请先激活！';
								$this->layer_msg($data['msg'],9,0,'',2);
							}
						}
						
						$ip = fun_ip_get();
						$UserinfoM->UpdateMember(array("login_ip"=>$ip,"login_date"=>time(),"`login_hits`=`login_hits`+1"),array("uid"=>$userinfo['uid']));

						if($_SESSION['qq']['openid']){
								$UserinfoM->UpdateMember(array("qqid"=>$_SESSION['qq']['openid']),array("uid"=>$userinfo['uid']));
								unset($_SESSION['qq']);
						}
						if($_SESSION['wx']['openid']){
							$udate = array('wxid'=>$_SESSION['wx']['openid']);
							if($_SESSION['wx']['unionid']){
								$udate['unionid']  = $_SESSION['wx']['unionid'];
							}
							$UserinfoM->UpdateMember($udate,array("uid"=>$userinfo['uid']));
							unset($_SESSION['wx']);
						}
						if($_SESSION['sina']['openid']){

							$UserinfoM->UpdateMember(array("sinaid"=>$_SESSION['sina']['openid']),array("uid"=>$userinfo['uid']));
							unset($_SESSION['sina']);
						}
						
						if($_COOKIE['wxid']){
							$UserinfoM->UpdateMember(array('wxid'=>'','unionid'=>'','wxopenid'=>''),array('wxid'=>$_COOKIE['wxid']));
							$UserinfoM->UpdateMember(array('wxid'=>$_COOKIE['wxid'],'unionid'=>$_COOKIE['unionid'],'wxbindtime'=>time()),array('uid'=>$userinfo['uid']));
							
							if($_COOKIE['wxloginid']){
								$UserinfoM->UpdateWxlogin(array('wxid'=>$_COOKIE['wxid'],'unionid'=>$_COOKIE['unionid'],'status'=>'1'),array('wxloginid'=>$_COOKIE['wxloginid']));
							}
							setcookie("unionid",'',time() - 86400, "/");
							setcookie("wxloginid",'',time() - 86400, "/");
						}
						
						$logdate=date("Ymd",$userinfo['login_date']);
						$nowdate=date("Ymd",time());
						if($logdate!=$nowdate){
							$this->get_integral_action($userinfo['uid'],"integral_login","会员登录");
						}
						$this->autoupjob($userinfo['uid'],$userinfo['usertype']);
						$this->add_cookie($userinfo['uid'],$userinfo['username'],$userinfo['salt'],$userinfo['email'],$userinfo['password'],$userinfo['usertype'],1,$userinfo['did']);
						if($_COOKIE['wxid']){
							
							 $this->sendredpack(array('type'=>'2','openid'=>$_COOKIE['wxid']));
							 setcookie("wxid",'',time() - 86400, "/");
							 $this->layer_msg('绑定成功，请按左上方返回进入微信客户端',9,0,Url('wap').'member/',2);
						}else if($_POST['job']){
							$this->layer_msg('',9,0,Url('wap',array('c'=>'job','a'=>'view','id'=>intval($_POST['job']))),2);
						}else if($_POST['checkurl']){
							$this->layer_msg('',9,0,$_POST['checkurl'],2);
						}else{ 
							$this->layer_msg('',9,0,Url('wap').'member/',2);
						}
					}else {
					 	$data['msg']='密码不正确！';
					}
				}
			
      $this->layer_msg($data['msg'],9,0,'',2);
		}
		if($_GET['usertype']=="2"){
			$this->yunset("headertitle","会员登录");
		}else{
			$this->yunset("headertitle","会员登录");
		}
		$this->seo("login");
		$this->yuntpl(array('wap/login'));
	}
	
	function sendmsg_action(){
		if(!$this->config['sy_msg_isopen'] || !$this->config['sy_msg_login']){
			$this->layer_msg('网站未开启短信验证登录服务!');
		}else{
			$moblie=$_POST['moblie'];
			$res = $this->send_autocode($moblie);
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
	function loginlock_action(){
		$this->seo("login"); 
		$this->yuntpl(array('wap/loginlock'));
	}
	function autoupjob($uid,$usertype){
		if($usertype=='2'){
			$Job=$this->Model("job");
			$Job->UpdateComjob(array("lastupdate"=>time()),array("`uid`='".$uid."' AND `autotype`='2' AND `autotime`>'".time()."'"));
		}
	}
	function utype_action()
	{
		if($this->uid)
		{
			header("Location:".$this->config['sy_weburl']."/member");
		}
		$this->seo("login");
		$this->yun_tpl(array('utype'));
	}

	function setutype_action(){


		if($_COOKIE['username'] && $_COOKIE['password'] && ($this->CheckRegUser($_COOKIE['username']) OR $this->CheckRegEmail($_COOKIE['username'])))
		{
			$Member=$this->MODEL("userinfo");
			$user = $Member->GetMemberOne(array("username"=>$_COOKIE['username']),array("field"=>"uid,username,password,salt,usertype,did"));
		
			$pass = md5(md5($_COOKIE['password']).$user['salt']);
			$userid = $user['uid'];

			if(!$user['usertype'])
			{
				if($pass==$user['password'] && $user['password']!='')
				{
					$usertype = (int)$_GET['usertype'];
					if($usertype=='1')
					{
						$table = "member_statis";
						$table2 = "resume";
						$data1=array("uid"=>$userid);
						$data2['uid']=$userid;

					}elseif($usertype=='2')
					{

						$table = "company_statis";
						$table2 = "company";
						$data1=$Member->FetchRatingInfo(array("uid"=>$userid));
						$data2['uid']=$userid;
						$data1['did']=$user['did'];

					}
					$Member->UpdateMember(array("usertype"=>$usertype),array("uid"=>$userid));
					$Member->InsertReg($table,$data1);
					$Member->InsertReg($table2,$data2);
					$this->add_cookie($userid,$user['username'],$user['salt'],$user['email'],$user['password'],$usertype,1,$user['did']);
					header("Location:".$this->config['sy_weburl'].'/member');
				}else{
					$this->unset_cookie();
					echo "激活失败";
				}
			}else{
				$this->unset_cookie();
				echo "激活失败";
			}


		}else{
			header("Location:".Url('index'));
		}


	}
}
?>