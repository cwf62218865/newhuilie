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
class resumeshare_controller extends resume_controller{
	function index_action(){
		if(!isset($this->uid) || !$this->uid){
			header('Location: '.Url('login'));die;
		}

		if($_POST){
			
			if($_POST['femail']=="" || $_POST['authcode']==""){
				echo "请完整填写信息！";die;
			}
			session_start();
			if(md5(strtolower($_POST['authcode']))!=$_SESSION['authcode'] || empty($_SESSION['authcode'])){
				echo "验证码不正确！";die;
			}
			unset($_SESSION['authcode']);
		
			if($this->config['sy_email_set']!="1"){
				echo "网站邮件服务器不可用!";die;
			}
			if($this->CheckRegEmail(trim($_POST['femail']))==false){echo "邮箱格式错误！";die;}

			
			if(isset($this->config['sy_recommend_day_num']) 
				&& $this->config['sy_recommend_day_num'] > 0){
				$num = $this->obj->DB_select_num('recommend', "`uid`={$this->uid}");
				if($num >= $this->config['sy_recommend_day_num']){
					echo "每天最多推荐{$this->config['sy_recommend_day_num']}次职位/简历！";
					exit;
				}
			}

			
			if(isset($this->config['sy_recommend_interval']) && $this->config['sy_recommend_interval'] > 0){
				$row = $this->obj->DB_select_once('recommend', "`uid`={$this->uid} order by addtime desc");

				if(isset($row['addtime'])
					&& (time() - $row['addtime']) < $this->config['sy_recommend_interval']){
					$needTime = $this->config['sy_recommend_interval'] - (time() - $row['addtime']);
					echo "推荐职位、简历间隔不得少于".$this->config['sy_recommend_interval']. "秒，请{$needTime}秒后再推荐";
					exit;
				}
			}

			$contents=file_get_contents($this->config[sy_weburl]."/resume/index.php?c=sendresume&id=".$_POST['id']);

			
		    $nickname = '';
		    if($this->usertype == 1){
		    	$row = $this->obj->DB_select_once('resume', "`uid`={$this->uid}");
		    	if($row['name']){
		    		$nickname = iconv("gbk", "utf-8", $row['name']);
		    	}
		    }
		    else if($this->usertype == 2){
		    	$row = $this->obj->DB_select_once('company', "`uid`={$this->uid}");
		    	if($row['name']){
		    		$nickname = iconv("gbk", "utf-8", $row['name']);
		    	}
		    }
		    
			
			$myemail = $this->stringfilter($nickname);


			$emailData['to'] = $_POST['femail'];
			$emailData['subject'] = "您的好友".$myemail."向您推荐了简历！";
			$emailData['content'] = $contents;

			$emailData['uid'] = '';
			$emailData['name'] = $_POST['femail'];
			$emailData['cuid'] = $this->uid;
			$emailData['cname'] = $myemail;

			$sendid = $this->sendemail($emailData);	

			if($sendid){
				echo 1;
			}else{
				echo "邮件发送错误 原因：1邮箱不可用 2网站关闭邮件服务";die;
			}
		
			$recommend = array(
				'uid' => $this->uid,
				'rec_type' => 2,
				'rec_id' => (int)$_POST['id'],
				'email' => $_POST['femail'],
				'addtime' => time()
			);
			$result = $this->obj->insert_into('recommend', $recommend);

			die;
		}
		if((int)$_GET['id']){
			$M=$this->MODEL('resume');
			$id=(int)$_GET['id'];
			$user=$M->resume_select($id);
			include(CONFIG_PATH."db.data.php");
		    unset($arr_data['sex'][3]);
		    $this->yunset("arr_data",$arr_data);
			$user['sex']=$arr_data['sex'][$user['sex']];
			$JobM=$this->MODEL('job');
			$time=strtotime("-14 day");
			$allnum=$JobM->GetUserJobNum(array("uid"=>$user['uid'],"eid"=>$user['id'],"`datetime`>'".$time."'"));
			$replynum=$JobM->GetUserJobNum(array("uid"=>$user['uid'],"eid"=>$user['id'],"`datetime`>'".$time."' and `is_browse`>'1'"));
			$pre=round(($replynum/$allnum)*100);
			$this->yunset("pre",$pre);
			$this->yunset('Info',$user);
			$data['resume_username']=$user['username_n'];
			$data['resume_city']=$user['city_one'].",".$user['city_two'];
			$data['resume_job']=$user['hy'];
			$this->data=$data;
		}
		$this->seo("resume_share");
		$this->yuntpl(array('resume/resume_share'));
    }
}
?>