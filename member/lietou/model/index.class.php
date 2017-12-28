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
class index_controller extends lietou{
	function index_action(){

		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);

        $service_com_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"DISTINCT com_id");
        $service_job_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"DISTINCT job_id");
        $service_resume_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"resume_id");
        $down_resume_count =$this->obj->DB_select_num("down_resume","uid=".$this->uid,"resume_id");
        $down_resume_odds = intval($down_resume_count/$down_resume_count);

        $recommend_jobs = $this->obj->DB_select_all("userid_job","uid=".$this->uid." limit 0,5");
        $this->obj->DB_select_all("down_resume","type=2 ");
		$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`login_date`,`status`");

 		$this->yunset("service_com_count",$service_com_count);
 		$this->yunset("service_job_count",$service_job_count);
 		$this->yunset("service_resume_count",$service_resume_count);
 		$this->yunset("down_resume_odds",$down_resume_odds);
 		$this->yunset("member",$member);
		$this->yunset("uid",$this->uid);
		$this->public_action();
		$this->yunset("js_def",1);
		$this->lt_tpl('index');
	}

    function recent_action(){

        include(CONFIG_PATH."db.data.php");
        $this->yunset("arr_data",$arr_data);

        $service_com_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"DISTINCT com_id");
        $service_job_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"DISTINCT job_id");
        $service_resume_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"resume_id");
        $down_resume_count =$this->obj->DB_select_num("down_resume","uid=".$this->uid,"resume_id");
        $down_resume_odds = intval($down_resume_count/$down_resume_count);

        $recommend_jobs = $this->obj->DB_select_all("userid_job","uid=".$this->uid." limit 0,5");
        $this->obj->DB_select_all("down_resume","type=2 ");
        $member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`login_date`,`status`");

        $this->yunset("service_com_count",$service_com_count);
        $this->yunset("service_job_count",$service_job_count);
        $this->yunset("service_resume_count",$service_resume_count);
        $this->yunset("down_resume_odds",$down_resume_odds);
        $this->yunset("member",$member);
        $this->yunset("uid",$this->uid);
        $this->public_action();
        $this->yunset("js_def",1);
        $this->lt_tpl('recentrecommend');
    }


    function newjobs_action(){

        include(CONFIG_PATH."db.data.php");
        $this->yunset("arr_data",$arr_data);

        $service_com_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"DISTINCT com_id");
        $service_job_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"DISTINCT job_id");
        $service_resume_count = $this->obj->DB_select_num("userid_job","uid=".$this->uid,"resume_id");
        $down_resume_count =$this->obj->DB_select_num("down_resume","uid=".$this->uid,"resume_id");
        $down_resume_odds = intval($down_resume_count/$down_resume_count);

        $recommend_jobs = $this->obj->DB_select_all("userid_job","uid=".$this->uid." limit 0,5");
        $this->obj->DB_select_all("down_resume","type=2 ");
        $member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`login_date`,`status`");

        $this->yunset("service_com_count",$service_com_count);
        $this->yunset("service_job_count",$service_job_count);
        $this->yunset("service_resume_count",$service_resume_count);
        $this->yunset("down_resume_odds",$down_resume_odds);
        $this->yunset("member",$member);
        $this->yunset("uid",$this->uid);
        $this->public_action();
        $this->yunset("js_def",1);
        $this->lt_tpl('newjobs');
    }
}
?>