function checkinfo(){
	var linkphone='';
	var name=$.trim($("#name").val());
	var hy=$.trim($("#hy").val());
	var pr=$.trim($("#pr").val());
	var cityid=$.trim($("#cityid").val());
	var address=$.trim($("#address").val());
	var linkman=$.trim($("#linkman").val());
	var mun=$.trim($("#mun").val());
	var phone=$.trim($("#phone").val());
	var phonetwo=$.trim($("#phonetwo").val());
	var phonethree=$.trim($("#phonethree").val());
	var linktel=$.trim($("#linktel").val());
	var linkmail=$.trim($("#linkmail").val()); 
	if(linkmail==""){
		ifemail = true;
	}else{
		ifemail = check_email(linkmail);
	}
	iflinktel = isjsMobile(linktel);
	var linkqq=$.trim($("#linkqq").val()); 
	var content=UE.getEditor('content').hasContents();
	if(phonetwo){
		if(phone==''){
			layermsg("请填写区号！");return false;
		}
		linkphone=phone+'-'+phonetwo; 
		if(phonethree){
			linkphone=linkphone+'-'+phonethree;
		}
	}
	if(name==''){layermsg("请输入企业名称！");return false;}
	if(hy==''){layermsg("请选择企业行业！");return false;}
	if(pr==''){layermsg("请选择企业性质！");return false;}
	if(cityid==''){layermsg("请选择所在地！");return false;}
	if(mun==''){layermsg("请选择企业规模！");return false;}
	if(address==''){layermsg("请填写公司地址！");return false;}
	if(linkman==''){layermsg("请填写联系人！");return false;}
	if(linkphone==''&&linktel==''){layermsg("联系电话和联系手机必填一项！");return false;}
	if(iflinktel==false && linktel!=''){layermsg('请填写正确手机号码！');return false}
	if(ifemail==false){layermsg("请填写正确格式电子邮箱！");return false;}
	if(content==''||content==false){layermsg("请填写企业简介！");return false;}
	if(linkqq&&(linkqq.length<6||linkqq.length>12)){
		layermsg("只能输入6-12位QQ号！");return false;
	}
}
function Checkltjobadd(){
	if($.trim($("#job_name").val())==""){
		layermsg("请输入职位名称");return false;
	}
	if($.trim($("#jobtwo").val())==""){
		layermsg("请选择职位分类");return false;
	}
	if($.trim($("#cityid").val())==""){
		layermsg("请选择工作地点");return false;
	}
	var job_desc=UE.getEditor('job_desc').hasContents();
	if(job_desc==""){
		layermsg("请输入职位描述");return false;
	}
	var eligible=UE.getEditor('eligible').hasContents();
	if(eligible==""){
		layermsg("请输入任职资格");return false;
	}
	if($.trim($("#edate").val())==""){
		layermsg("请填写截止日期");return false;
	}
	var today=$("#today").val();
	var st=toDate(today).getTime()/1000;
	if($("#edate").val()!=''){
		var edate=$("#edate").val();
	}else{
		var edate='';
	}
	edate=toDate(edate).getTime()/1000;
	if(edate<st){
		layermsg('截止日期必须大于今天日期！',2,8);return false
	}
	if($.trim($("#department").val())==""){
		layermsg("请输入所属部门");return false;
	}
	if($.trim($("#report").val())==""){
		layermsg("请输入汇报对象");return false;
	}
	var min = $.trim($("#minsalary").val());
	var max = $.trim($("#maxsalary").val());
	
	if(min==""||min=="0"){
		layermsg("请填写职位年薪");return false;
	}
	if(max && parseInt(max) <= parseInt(min)){
		layermsg("最高年薪必须大于最低年薪");return false;
	}
	var constitute =[];
	$('input[name="constitute[]"]:checked').each(function(){
		constitute.push($(this).val());
	});
	if(constitute.length==0){
		layermsg("请选择薪资构成！",2);return false;
	}
	var welfare =[];
	$('input[name="welfare[]"]:checked').each(function(){
		welfare.push($(this).val());
	});
	if(welfare.length==0){
		layermsg("请选择福利待遇！",2);return false;
	}
	if($.trim($("#age").val())<1){
		layermsg("请选择年龄要求");return false;
	}
	if($.trim($("#sex").val())<1){
		layermsg("请选择性别要求");return false;
	}	
	if($.trim($("#exp").val())<1){
		layermsg("请选择工作经验");return false;
	}
	
	if($.trim($("#edu").val())<1){
		layermsg("请选择学历要求");return false;
	}
	var language =[];
	$('input[name="language[]"]:checked').each(function(){
		language.push($(this).val());
	});
	if(language.length==0){
		layermsg("请选择语言要求！",2);return false;
	}
}
function check_email(strEmail) {
	 var emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
	 if (emailReg.test(strEmail))
	 return true;
	 else
	 return false;
}
function checkjob(id,type){
	if(id>0){
		$.post(wapurl+"?c=ajax&a=wap_job",{id:id,type:type},function(data){
			if(type==1){
				$("#job1_son").html(data);
			}else{
				$("#job_post").html(data);
			}
		})
	}else{
		if(type==1){
			$("#job1_son").html('<option value="">请选择</option>');
		}
	}
	$("#job_post").html('<option value="">请选择</option>');
}
function checkcity(id,type){
	if(id>0){
		$.post(wapurl+"?c=ajax&a=wap_city",{id:id,type:type},function(data){
			if(type==1){
				$("#cityid").html(data);
				$("#cityshowth").hide();
			}else{
				if(data){
					$("#cityshowth").attr('style','width:31%;');
					$("#three_cityid").html(data);
				}
				
			}
		})
	}else{
		if(type==1){
			$("#cityid").html('<option value="">请选择</option>');
			$("#cityshowth").hide();
		}
	}
	$("#three_cityid").html('<option value="">请选择</option>');
}
function checkfrom() {
	if($.trim($("#name").val())==""){
		layermsg("招聘名称不能为空！");return false;
	}else if($.trim($("#job1_son").val())==""){
		layermsg("请选择职位类别！");return false;
	}else if($.trim($("#cityid").val())==""){
		layermsg("请选择工作地点！");return false;
	}else if($.trim($("#days").val())<1){
		layermsg("请正确填写招聘天数！");return false;
	}
	var minsalary=$.trim($("#minsalary").val());
	var maxsalary=$.trim($("#maxsalary").val());
	if($("#salary_type").attr("checked")!='checked'){
	if(minsalary==""||minsalary=="0"){
		layermsg("请填写工资！");return false;
	}
	if(maxsalary){
		if(parseInt(maxsalary)<=parseInt(minsalary)){
			layermsg('最高工资必须大于最低工资！');return false;
		}
	}
	}
	var description=UE.getEditor('description').hasContents();  
	if(description==""||description==false){
		layermsg("职位描述不能为空！");return false;
	} 
	var islink=$("input[name='islink']").val();
	if(islink==2){
		var link_man=$.trim($("input[name='link_man']").val());
		var link_moblie=$.trim($("input[name='link_moblie']").val()); 
		if(link_man==''||link_moblie==''){
			layermsg('联系人及联系电话均不能为空！');return false;
		}
		if(link_moblie&& (isjsMobile(link_moblie)==false && !isjsTell(link_moblie))){
			layermsg('联系电话格式错误！');return false;
		}
	} 
	var isemail=$("input[name='isemail']").val();
	if(isemail=='2'){
		var email=$.trim($("input[name='email']").val());
		if(email==''){
			layermsg('请输入邮箱！');return false;
		}else if(check_email(email)==false){
			layermsg('新邮箱格式错误！');return false;
		} 
	}
}
function check_email(strEmail) {
	 var emailReg = /^([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	 if (emailReg.test(strEmail))
	 return true;
	 else
	 return false;
 }