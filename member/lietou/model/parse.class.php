<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2017 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class parse_controller extends lietou
{
    //电话
    public  $template_tel = array("固话","座机", "电话", "电话号码", "手机", "手机号", "手机号码", "tel", "phone", "mobile", "telephone", "宿舍电话");
    //邮箱
    public $template_mail =  array("邮件", "邮箱", "邮箱号", "邮箱号码", "邮箱地址", "电子邮件", "电子邮箱", "e-mail", "email", "mail", "mailbox");
    //公司
    public $template_companyname = array("公司", "公司名称", "企业名称", "单位名称");
    //学历
    public $template_education = array("最高学历", "教育程度", "学历", "学历层次", "学历等级");
    //学校
    public $template_school = array("毕业院校", "毕业学校", "学校", "学院", "教育经历");
    //出生日期
    public $template_birthday = array("生日", "年龄", "出生年月", "出生日期");
    //所在地
    public $template_location = array("现居住地", "现居住城市", "所在地", "居住地", "地址", "目前所在地");
    //期望行业
    public $template_trade  =   array("行业", "期望从事职业", "求职意向", "职业发展意向", "期望从事行业", "期望行业");
    //学历
    public $template_xueli = array("初中", "高中", "中技", "中专", "大专", "本科", "硕士", "博士", "博后");
    //期望工作地
    public $template_workadress = array("工作地点", "期望工作地区", "工作地区", "目标地点", "期望地点");
    //求职状态
    public $template_current  = array("目前状态", "目前状况", "目前职业概况", "求职状态", "目前求职状态");
    //当前职位
    public $template_work = array("职位", "目前职业", "当前职位", "所任职位");
    //期望职位
    public $template_work_cn = array("期望职业", "期望职位", "目标职能", "期望从事行业");

	function index_action()
	{

        $content = shell_exec('/usr/local/bin/antiword -m UTF-8 '.$_SERVER['DOCUMENT_ROOT']."/test/test.doc");
        $content = mb_convert_encoding($content, "gbk", "utf-8");
        echo $content;exit();
        if($_FILES){
            $file = $_FILES['file'];
            $upload_path_name = "../resume_file/".time().".doc";
            $complete_path = time().".doc";
            if(move_uploaded_file($file['tmp_name'],$upload_path_name)){
                $content = shell_exec('/usr/local/bin/antiword -m UTF-8 '.$_SERVER['DOCUMENT_ROOT']."/resume_file/".$complete_path);
                echo $content;exit();
                $content = mb_convert_encoding($content, "gbk", "utf-8");
                echo $content;exit();
            }else{
                echo 2;
            }
            exit();
        }
	}

    //所有特殊字符转换
    function replaceAll($str,$tostr="")
    {
        // $str = "!@#$%^&*（中'文：；﹑?中'文中'文().,<>|[]'\"";
        if (!$tostr )
        {
            $tostr = "&%@";
        }
//中文标点
//    $char = "。、！？：；﹑?＂…‘’“”〝〞∕?‖—　〈〉﹞﹝「」??〖〗】【??』『〕〔》《﹐?﹕︰﹔！?？?﹖﹌﹏﹋＇?ˊˋ―﹫︳︴?＿￣﹢﹦﹤‐??﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";

        $pattern = array(
            "/[[:punct:]]/i", //英文标点符号
//        '/['.$char.']/u', //中文标点符号
            '/[ ]{2,}/'
        );
        $str = preg_replace($pattern, '&%@', $str);
        return $str;


    }

    //去掉空格
    function myTrim($str)
    {

        $search = array(" ","?","????","\n","\r","\t", "\s", "&gt; ","　　");
        $replace = array("","","","","","", "", "", "");
        return trim(str_replace($search, $replace, $str));
    }

    function isChineseName($name){
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
            return true;
        } else {
            return false;
        }
    }

    function isTel($tel){
        if(strlen($tel) == "11" && preg_match("/^1[34578]\d{9}$/",$tel))
        {
            return true;
        }else{
            return false;
        }
    }

    //检索关键字
    function strpos_key($arrs,$str){
        foreach ($arrs as $value){
//        if(strpos($value,$str) !==false){
//            return true;
//        }
//
            if($value=$str){
                return true;
            }
        }
    }


    function search_key($arr,$str){
        foreach ($arr as $hr){
            if($hr = $str){
                return true;
            }
        }
    }

}
?>