<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2017 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class parse_controller extends lietou
{
    //�绰
    public  $template_tel = array("�̻�","����", "�绰", "�绰����", "�ֻ�", "�ֻ���", "�ֻ�����", "tel", "phone", "mobile", "telephone", "����绰");
    //����
    public $template_mail =  array("�ʼ�", "����", "�����", "�������", "�����ַ", "�����ʼ�", "��������", "e-mail", "email", "mail", "mailbox");
    //��˾
    public $template_companyname = array("��˾", "��˾����", "��ҵ����", "��λ����");
    //ѧ��
    public $template_education = array("���ѧ��", "�����̶�", "ѧ��", "ѧ�����", "ѧ���ȼ�");
    //ѧУ
    public $template_school = array("��ҵԺУ", "��ҵѧУ", "ѧУ", "ѧԺ", "��������");
    //��������
    public $template_birthday = array("����", "����", "��������", "��������");
    //���ڵ�
    public $template_location = array("�־�ס��", "�־�ס����", "���ڵ�", "��ס��", "��ַ", "Ŀǰ���ڵ�");
    //������ҵ
    public $template_trade  =   array("��ҵ", "��������ְҵ", "��ְ����", "ְҵ��չ����", "����������ҵ", "������ҵ");
    //ѧ��
    public $template_xueli = array("����", "����", "�м�", "��ר", "��ר", "����", "˶ʿ", "��ʿ", "����");
    //����������
    public $template_workadress = array("�����ص�", "������������", "��������", "Ŀ��ص�", "�����ص�");
    //��ְ״̬
    public $template_current  = array("Ŀǰ״̬", "Ŀǰ״��", "Ŀǰְҵ�ſ�", "��ְ״̬", "Ŀǰ��ְ״̬");
    //��ǰְλ
    public $template_work = array("ְλ", "Ŀǰְҵ", "��ǰְλ", "����ְλ");
    //����ְλ
    public $template_work_cn = array("����ְҵ", "����ְλ", "Ŀ��ְ��", "����������ҵ");

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

    //���������ַ�ת��
    function replaceAll($str,$tostr="")
    {
        // $str = "!@#$%^&*����'�ģ����p?��'����'��().,<>|[]'\"";
        if (!$tostr )
        {
            $tostr = "&%@";
        }
//���ı��
//    $char = "�������������p?�����������������M?�����������{�z����??��������??�������������o?�s�U�r��?��?�t�k�n�j��?�@�A�D������?�ߣ��������\??�|���}���~���l�h�m�i������������硥��������";

        $pattern = array(
            "/[[:punct:]]/i", //Ӣ�ı�����
//        '/['.$char.']/u', //���ı�����
            '/[ ]{2,}/'
        );
        $str = preg_replace($pattern, '&%@', $str);
        return $str;


    }

    //ȥ���ո�
    function myTrim($str)
    {

        $search = array(" ","?","????","\n","\r","\t", "\s", "&gt; ","����");
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

    //�����ؼ���
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