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
class index_controller extends ask_controller{
	function index_action(){
		$M=$this->MODEL('ask');
 		$hotuser=$M->GetHotUser(array("`add_time`>'".strtotime("-30 day")."'"),array('groupby'=>"uid","orderby"=>'num',"desc"=>'desc',"limit"=>10,"field"=>"uid,count(id) as num,sum(support) as support,nickname,pic"));
		foreach($hotuser as $v){
			$uids[]=$v['uid'];
		}
		$userinfo=$this->obj->DB_select_all('member','`uid` in('.implode(',',$uids).')','`uid`,`username`');
		$resume=$this->obj->DB_select_all('resume','`uid` in('.implode(',',$uids).')','`uid`,`photo`');
		$company=$this->obj->DB_select_all('company','`uid` in('.implode(',',$uids).')','`uid`,`logo`');
		foreach($hotuser as $k=>$v){
			if(!$v['nickname']){
				foreach($userinfo as $va){
					if($va['uid']==$v['uid']){
						$hotuser[$k]['nickname']=$va['username'];
					}
				}
			}
			if($v['pic']&&file_exists(str_replace("./",APP_PATH,$v['pic']))){
				$pic=str_replace("./",$this->config['sy_weburl']."/",$v['pic']);
			}else{
				foreach($resume as $va){
					if($va['uid']==$v['uid']){
						if($va['photo']&&file_exists(str_replace("./",APP_PATH,$va['photo']))){
							$pic=str_replace("./",$this->config['sy_weburl']."/",$va['photo']);
						}
					}
				}
				foreach($company as $va){
					if($va['uid']==$v['uid']){
						if($va['logo']&&file_exists(str_replace("./",APP_PATH,$va['logo']))){
							$pic=str_replace("./",$this->config['sy_weburl']."/",$va['logo']);
						}
					}
				}
			}
			if($pic){
				$hotuser[$k]['pic']=$pic;
			}else{
				$hotuser[$k]['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
			}
		}
		$this->atnask($M);
		$this->hotclass();
		$this->seo('ask_index');
		$this->yunset("hotuser",$hotuser);
		$this->ask_tpl('index');
	}

	function getcomment_action(){
		$M=$this->MODEL('ask');
		$aid=(int)$_POST['aid'];
		$comment=$M->GetCommentList(array('aid'=>$aid));
		if(is_array($comment)){
			foreach($comment as $k=>$v){
				if($v['pic']!=""&&file_exists(str_replace($this->config['sy_weburl'],APP_PATH,".".$v['pic']))){
					$comment[$k]['pic']=str_replace("./",$this->config['sy_weburl'].'/',$v['pic']);
				}else{
					$comment[$k]['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
				}
				$comment[$k]['errorpic']=$this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
				$comment[$k]['nickname']=urlencode($v['nickname']);
				$comment[$k]['content']=urlencode($v['content']);
				$comment[$k]['date']=date("Y-m-d H:i",$v['add_time']);
				if($v['uid']==$this->uid){
					$comment[$k]['myself']='1';
				}
			}
			$comment_json = $this->JSON($comment);
			echo urldecode($comment_json);die;
		}
	}
	
	function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
	{
	  static $recursive_counter = 0;
	  if (++$recursive_counter > 1000) {
	    die('possible deep recursion attack');
	  }
	  foreach ($array as $key => $value) {
	    if (is_array($value)) {
	      $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
	    } else {
	      $array[$key] = $function($value);
	    }
	  
	    if ($apply_to_keys_also && is_string($key)) {
	      $new_key = $function($key);
	      if ($new_key != $key) {
	        $array[$new_key] = $array[$key];
	        unset($array[$key]);
	      }
	    }
	  }
	  $recursive_counter--;
	}

	
	function JSON($array) {
	    $this->arrayRecursive($array, 'urlencode', true);
	    $json = json_encode($array);
	    return urldecode($json);
	}
	function qrepost_action(){
		$M=$this->MODEL('ask');
		$this->is_login();
		$eid=(int)$_POST['eid'];
		$reason=$_POST['reason'];
		$is_set=$M->GetReportOne(array('type'=>1,'r_type'=>1,'eid'=>$eid),array('field'=>'`p_uid`'));
		if(empty($is_set)){
            $question=$M->GetQuestionOne(array('id'=>$eid),array('field'=>'`uid`')); 
            $UserInfoM=$this->MODEL('userinfo');
            $Userinfo=$UserInfoM->GetMemberOne(array('uid'=>$question['uid']),array('field'=>'`username`'));
            $my_nickname=$UserInfoM->GetMemberOne(array('uid'=>$this->uid),array('field'=>'`username`'));
			$data['did']=$this->userdid;
			$data['p_uid']=$this->uid;
			$data['c_uid']=$question['uid'];
			$data['eid']=(int)$_POST['eid'];
			$data['usertype']=$this->usertype;
			$data['inputtime']=time();
			$data['username']=$my_nickname['username'];
			$data['r_name']=$Userinfo['username'];
			$data['r_reason']=$_POST['reason'];
			$data['type']=1;
			$data['r_type']=1;
			$new_id=$M->AddReport($data);
			if($new_id){
				$M->member_log('举报问答问题');
				echo '1';
			}else{
				echo '0';
			}
		}else{
			if($is_set['p_uid']==$this->uid){
				echo '2';
			}else{
				echo '3';
			}
		}
	}
	function forcomment_action(){
		$M=$this->MODEL('ask');
		$this->is_login();
		$MemberM=$this->MODEL("userinfo");
		if($this->config['integral_answerpl_type']=="1"){
			$auto=true;
		}else{
			$statis=$MemberM->GetUserstatisOne(array("uid"=>$this->uid),array("field"=>"`integral`","usertype"=>$this->usertype));
			if($statis['integral']<$this->config['integral_answerpl']){
				echo "您的".$this->config['integral_pricename']."不足，请先充值后再评论问答！";die;
			}
			$auto=false;
		}
		$data['aid']=(int)$_POST['aid'];
		$data['qid']=(int)$_POST['qid'];
		$data['content']=str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
		$data['content']=iconv("utf-8","gbk",$data['content']);
		$data['uid']=$this->uid;
		$data['add_time']=time();
		$new_id=$M->AddAnswerReview($data);
		if($new_id){ 
			$result=$this->max_time('评论问答');		
			if($result==true||$auto==false){  	 			 
				$this->company_invtal($this->uid,$this->config['integral_answerpl'],$auto,"评论问答",true,2,'integral');
			}
			$M->member_log("评论问答");
			$num=$M->GetCommentNum(array('aid'=>(int)$_POST['aid']));
			$M->UpdateAnswer(array('comment'=>$num),array('id'=>(int)$_POST['aid']));
			echo '1';
		}else{
			echo '0';
		}
	}
	function forsupport_action(){
		$M=$this->MODEL('ask');
		$cookid=explode(',', $_COOKIE['support']);
		if(in_array($_POST['aid'],$cookid)){
			echo '2';die;
		}else{
			$id=$M->UpdateAnswer(array("`support`=`support`+1"),array('id'=>$_POST['aid']));
			if($id){
				$M->member_log("给问题回答点赞");
				$sendid=array();
				$sendid[] =$_POST['aid'];
				if($_COOKIE['support']){
					$support=$_COOKIE['support'].','.pylode(',',$sendid);
				}else{
					$support=pylode(',',$sendid);
				}
				if($this->config['sy_web_site']=="1"){
					if($this->config['sy_onedomain']!=""){
						$weburl=get_domain($this->config['sy_onedomain']);
					}elseif($this->config['sy_indexdomain']!=""){
						$weburl=get_domain($this->config['sy_indexdomain']);
					}else{
						$weburl=get_domain($this->config['sy_weburl']);
					}
					SetCookie("support",$support,time() + 86400,"/",$weburl);
				}else{
					SetCookie("support",$support,time() + 86400,"/");
				}
				echo '1';
			}else{
				echo '0';die;
			}
		}
	}
	
	function answer_action(){
		$M=$this->MODEL('ask');
		if($this->uid==''||$this->username==''){$this->ACT_layer_msg( "请先登录！",8,$_SERVER['HTTP_REFERER']);}
		$id=(int)$_POST['id'];
		if($_POST['content']&&$id){
			$MemberM=$this->MODEL("userinfo");
			if($this->config['integral_answer_type']=="1"){
				$auto=true;
			}else{
				$statis=$MemberM->GetUserstatisOne(array("uid"=>$this->uid),array("field"=>"`integral`","usertype"=>$this->usertype));
				if($statis['integral']<$this->config['integral_answer']){
					$this->ACT_layer_msg("您的！".$this->config['integral_pricename']."不足，请先充值后再回答问题！",8,$_SERVER['HTTP_REFERER']);
				}
				$auto=false;
			}
			$info=$M->GetQuestionOne(array('id'=>$id),array('field'=>'`id`,`uid`,`title`,`content`'));
			$content = html_entity_decode($_POST["content"],ENT_QUOTES,"GB2312");
			$content = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),$content);
			
			$data=array();
			if($this->usertype&&$this->uid){
				$minfo=$MemberM->GetUserinfoOne(array("uid"=>$this->uid),array("usertype"=>$this->usertype));
				if($this->usertype==2||$this->usertype==4){
					$data['nickname']=$minfo['name'];
					$data['pic']=$minfo['logo'];
				}elseif($this->usertype==1){
					$data['nickname']=$minfo['name'];
					$data['pic']=$minfo['photo'];
				}elseif($this->usertype==3){
					$data['nickname']=$minfo['realname'];
					$data['pic']=$minfo['photo'];
				}
			}
			$data['qid']=$id;
			$data['content']=trim(strip_tags($content));
			$data['uid']=$this->uid;
			$data['comment']=0;
			$data['support']=0;
			$data['oppose']=0;
			$data['add_time']=time();
			$id=$M->insert_into("answer",$data);
			if($id){
				$result=$this->max_time('回答问题');		
				if($result==true||$auto==false){
					$this->company_invtal($this->uid,$this->config['integral_answer'],$auto,"回答问题",true,2,'integral');
				}
				$M->UpdateQuestion(array("`answer_num`=`answer_num`+1","lastupdate"=>time()),array('id'=>$info['id']));
				$state_content = "回答了问答《<a href=\"".Url('ask',array("c"=>"content","id"=>$id))."\" target=\"_blank\">".$info['title']."</a>》。";
				$this->addstate($state_content,2);
				$M->member_log("回答了问答《".$info['title']."》");
				$this->ACT_layer_msg( "回答成功！", 9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->ACT_layer_msg( "回答失败！", 8);
			}
		}else{
			$this->ACT_layer_msg("非法操作！", 8);
		}
	}
	function addquestion_action(){
		if($this->uid==''||$this->username==''){header('Location:'.Url("login"));}
		$this->seo('ask_add_question');
		$this->ask_tpl('addquestion');
	}
	function qclass_action(){
		$CacheM=$this->MODEL('cache');
        $info=$CacheM->GetCache(array('ask'));
		$rows=array();
		$id=(int)$_POST['id'];
		foreach($info['ask_type'][$id] as $v){
			$rows[$v]=urlencode($info['ask_name'][$v]);
		}
		$rows = json_encode($rows);
		echo urldecode($rows);die;
	}
	function save_action(){
		$cid=(int)$_POST['cid'];
		if($_POST['submit']&&$cid){
			$M=$this->MODEL('ask');
			$MemberM=$this->MODEL("userinfo");
			if($this->uid==''){
				$this->ACT_layer_msg( "请先登录！", 8);
			}
			if(trim($_POST['title'])==""){
				$this->ACT_layer_msg( "标题不能为空！", 8);
			}
			if(strpos($this->config['code_web'],'职场提问')!==false){
			    session_start();
			    
				if($this->config['code_kind']==3){
					
					if(!gtauthcode($this->config)){
						$this->ACT_layer_msg("请点击按钮进行验证！",8);
					}
			    }else{
			        if(md5(strtolower($_POST['authcode']))!=$_SESSION['authcode'] || empty($_SESSION['authcode'])){
			            $this->ACT_layer_msg("验证码错误！",8);
			            unset($_SESSION['authcode']);
			        }
					 unset($_SESSION['authcode']);
			    }
			}

			
			if(isset($this->config['sy_day_ask_num']) && $this->config['sy_day_ask_num'] > 0) {
				$dayAskNum = $MemberM->GetDayAskedNum(array("uid"=>$this->uid));
				if($dayAskNum >= $this->config['sy_day_ask_num']){
					$this->ACT_layer_msg("您今天已发布".$dayAskNum."个问答了，请明日再发！",8,$_SERVER['HTTP_REFERER']);
				}
			}

			if($this->config['integral_question_type']=="1"){
				$auto=true;
			}else{
				$statis=$MemberM->GetUserstatisOne(array("uid"=>$this->uid),array("field"=>"`integral`","usertype"=>$this->usertype));
				if($statis['integral']<$this->config['integral_question']){
					$this->ACT_layer_msg("您的！".$this->config['integral_pricename']."不足，请先充值后再发布问题！",8,$_SERVER['HTTP_REFERER']);
				}
				$auto=false;
			} 

			if($this->usertype&&$this->uid){
				$minfo=$MemberM->GetUserinfoOne(array("uid"=>$this->uid),array("usertype"=>$this->usertype));
				if($this->usertype==2||$this->usertype==4){
					$data['nickname']=$minfo['name'];
					$data['pic']=$minfo['logo'];
				}elseif($this->usertype==1){
					$data['nickname']=$minfo['name'];
					$data['pic']=$minfo['photo'];
				}elseif($this->usertype==3){
					$data['nickname']=$minfo['realname'];
					$data['pic']=$minfo['photo'];
				}
			}

			$data['title']=$_POST['title'];
			$data['cid']=$cid;
			$data['content'] = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),html_entity_decode($_POST["content"],ENT_QUOTES,"GB2312"));
			$data['content']=strip_tags(trim($data['content']),"<p> <br> <b>");
			$data['uid']=$this->uid;
			$data['add_time']=time();

		
			$data['state'] = $this->config['ask_check'] == 1 ? 0 : 1;

		
			if($data['state'] != 0 && $this->usertype == 2){
				$member = $this->obj->DB_select_once("member", "`uid`='{$this->uid}'", "`status`");
				if($member['status'] != 1){
					$data['state'] = 0;
				}
			}

			$nid=$M->SaveQuestion($data);
		}
 		if($nid){
			$result=$this->max_time('发布问题');		
			if($result==true||$auto==false){
				$this->company_invtal($this->uid,$this->config['integral_question'],$auto,"发布问题",true,2,'integral');
			}
			$FriendM=$this->MODEL('friend');
			$sql['uid']=$this->uid;
			$sql['content']="发布了问答《<a href=\"".Url("ask",array("c"=>"content","id"=>$nid))."\" target=\"_blank\">".$_POST['title']."</a>》。";
			$sql['ctime']=time();
			$sql['type']='2';
			$FriendM->InsertFriendState($sql);
			$M->member_log("发布了问答《".$_POST['title']."》");
			unset($_SESSION['authcode']);

			if($data['state'] == 0){
				$this->ACT_layer_msg( "已发布，正在审核中！",9,Url("ask",array("c"=>"index")));
			}
 			else{
 				$this->ACT_layer_msg( "发布成功！",9,Url("ask",array("c"=>"index")));
 			}
		}else{
			$this->ACT_layer_msg( "提问失败！", 8);
		}
	}

	function hotweek_action(){
		$M=$this->MODEL('ask');
		$recom=$M->GetQuestionList(array('is_recom'=>'1'),array('field'=>'uid,nickname,pic','limit'=>'8','orderby'=>'add_time'));
		foreach($recom as $k=>$v){
			if($v['pic']){
				$recom[$k]['pic']=$this->config['sy_weburl'].'/'.$v['pic'];
			}else{
				$recom[$k]['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
			}
		}
		$this->yunset('recom',$recom);
		$this->atnask($M);
		$this->yunset("navtype",'hotweek');
		$this->seo('ask_hot_week');
		$this->ask_tpl('hotweek');
	}

	function savecode_action(){
	    if(strpos($this->config['code_web'],'职场提问')!==false){
	        session_start();
	        if ($this->config['code_kind']==1){
	            if(md5(strtolower($_POST['authcode']))!=$_SESSION['authcode'] || empty($_SESSION['authcode'])){
	                echo 1;die;
	                unset($_SESSION['authcode']);
	            }
	        }elseif ($this->config['code_kind']==3){
	             if (md5(strtolower($_POST['unlock']))!=$_SESSION['unlock'] || empty($_SESSION['unlock'])){
			            unset($_SESSION['unlock']);
			            echo 3;die;
			     }
	        }
	    }		
	}
}
?>