
var _table;

$(function(){

	//��ӹ�������
	$(".addWorkExp").click(function(){
		addWork();
	});
	
	//��ӽ�������
	$(".addEduExp").click(function(){
		addEdu();
	});
	
	
	//�����Ŀ����
	$(".addProExp").click(function(){
		addPro();
	});
	
	$(document).on("click", ".removeExp", function () {
		_table = $(this).parents("table");
		$("#ensureModal").modal("show");
	});
	
	$(document).on("click", "#comfirmDelBtn", function () {
		var tbcss = $(_table).attr("class");
		tbcss =  $.trim(tbcss.replace("dash ",""));
		var i = $("table."+tbcss).size();
		$(_table).remove();
		$("#ensureModal").modal("hide");
		$($("table."+tbcss)[0]).removeClass("dash");
		if(i==2){
//			$("table."+tbcss).removeClass("dash").find(".removeExp").remove();
			$("table."+tbcss).removeClass("dash");
		}
	});
	
	$(document).on("click", ".tip", function () {
		$(this).parent("div").find("input[name=endDate]").val("");
	});
	
	$("#resName,#cityId,#mobile,#email,#curMoney,#moneyMonthes,#hopeMoney,#workYear").blur(function(){
		var thisval = $(this).val()
		
		if($(this).attr("id")=="resName"){
			if(thisval!=""){
				$("#tips").text("");
			}else{
				$("#tips").text("����д������");
			}
			return;
		}
		if($(this).attr("id")=="cityId"){
			if(thisval!=""){
				$("#tips").text("");
			}else{
				$("#tips").text("��ѡ�����ڵأ�");
			}
			return;
		}

		if($(this).attr("id")=="email"){
			if(validEmail(thisval)){
				$("#tips").text("");
			}else{
				$("#tips").text("�����ʽ����ȷ��");
			}
			return;
		}
		if($(this).attr("id")=="curMoney"){
			if(validNum(thisval)){
				$("#tips").text("");
				var moneyMonthes = $.trim($("#moneyMonthes").val());
				moneyMonthes = moneyMonthes==""?0:moneyMonthes;
				if(validNum(moneyMonthes)){
					$("#moneyspan").text(moneyMonthes*thisval);
				}
			}else{
				$("#tips").text("��н��д���֣�");
				$("#moneyspan").text(0);
			}
			return;
		}
		if($(this).attr("id")=="moneyMonthes"){
			if(validNum(thisval)){
				$("#tips").text("");
				var curMoney = $.trim($("#curMoney").val());
				curMoney = curMoney==""?0:curMoney;
				if(validNum(curMoney)){
					$("#moneyspan").text(curMoney*thisval);
				}
			}else{
				$("#tips").text("��н������д���֣�");
				$("#moneyspan").text(0);
			}
			return;
		}
		
		if($(this).attr("id")=="workYear"){
			if(validNum(thisval)){
				$("#tips").text("");
			}else{
				$("#tips").text("������������д���֣�");
			}
			return;
		}
		
		if($(this).attr("id")=="hopeMoney"){
			if(validNum(thisval)){
				$("#tips").text("");
			}else{
				$("#tips").text("��������д���֣�");
			}
			return;
		}
			
	});
	$("#saveLtCandBtn").click(function(){
		
		var flag = false;
		var ff = valideResumeForm();
		if(!ff){
			return false;
		}
		var cand = {};
		var workList= new Array();
		var proList= new Array();
		var eduList= new Array();
		
		var works = $(".workexp");
		var pros = $(".proexp");
		var edus = $(".eduexp");
		
		//������Ϣ
		cand.name=$("#resName").val();
		cand.engName=$("#engName").val();
		cand.sex=$("input[name=sex]:checked").val();
		cand.cityId=$("#cityId").val();
		cand.birthDayStr=$("#birthDay").val();
		cand.mobile=$("#mobile").val();
		cand.email=$("#email").val();
		cand.product=$("#product").val();
		cand.jobState=$("#jobState").val();
		cand.workYear=$("#workYear").val();
		cand.degree=$("#degree").val();
		
		//��������
		$.each(works,function(i,v){
			
			var _startDate = $(v).find("input[name=startDate]").val();
			var _endDate = $(v).find("input[name=endDate]").val();
			var _now = $(v).find("input[name=now]").prop("checked");
			var _companyName = $(v).find("input[name=companyName]").val();
			var _posName = $(v).find("input[name=posName]").val();
			var _workDes = $(v).find("[name=workDes]").val();
			var _companyDes = $(v).find("[name=companyDes]").val();
			if(_startDate!=''){
				if(!_now&&(_endDate==''||new Date(_startDate)>new Date(_endDate))){
					alert("��ѡ������������ʱ����߹�ѡ����ѡ�"); flag =true; return false;
				}
			}
			
			if(_now){
				_endDate='';
			}
			if(_companyDes==''){
				alert("����д��˾���ܣ�");flag =true; return false;
			}
			if((_startDate!=null&&_startDate!='')||(_endDate!=null&&_endDate!='')||(_companyName!=null&&_companyName!='')
					||(_posName!=null&&_posName!='')||(_workDes!=null&&_workDes!='')||(_companyDes!=null&&_companyDes!='')){
				workList.push({
					'startDateStr':_startDate,
					'endDateStr':_endDate,
					'companyName':_companyName,
					'posName':_posName,
					'workDes':_workDes,
					'companyDes':_companyDes
					
				});
			}
		});
		if(flag){
			return false;
		}
		cand.workExp=workList;
		
		//��Ŀ����
		$.each(pros,function(i,v){
			
			var _startDate = $(v).find("input[name=startDate]").val();
			var _endDate = $(v).find("input[name=endDate]").val();
			var _now = $(v).find("input[name=now]").prop("checked");
			var _proName = $(v).find("input[name=proName]").val();
			var _proDes = $(v).find("[name=proDes]").val();
			var _projectOffice = $(v).find("[name=projectOffice]").val();
			var _subCompany = $(v).find("[name=subCompany]").val();
			var _projectRole = $(v).find("[name=projectRole]").val();
			var _projectPerfromnance = $(v).find("[name=projectPerfromnance]").val();
			if(_startDate!=''){
				if(!_now&&(_endDate==''||new Date(_startDate)>new Date(_endDate))){
					alert("��ѡ����Ŀ��������ʱ����߹�ѡ����ѡ�"); flag =true;return false;
				}
			}
			
			if(_now){
				_endDate='';
			}
			
			if((_startDate!=null&&_startDate!='')||(_endDate!=null&&_endDate!='')||(_proName!=null&&_proName!='')
					||(_proDes!=null)){
				proList.push({
					'startDateStr':_startDate,
					'endDateStr':_endDate,
					'proName':_proName,
					'proDes':_proDes,
					'projectOffice':_projectOffice,
					'subCompany':_subCompany,
					'projectRole':_projectRole,
					'projectPerfromnance':_projectPerfromnance
					
				});
			}
		});
		if(flag){
			return false;
		}
		cand.proExp=proList;
		
		
		//��������
		$.each(edus,function(i,v){
			
			var _startDate = $(v).find("input[name=startDate]").val();
			var _endDate = $(v).find("input[name=endDate]").val();
			var _now = $(v).find("input[name=now]").prop("checked");
			var _schoolName = $(v).find("input[name=schoolName]").val();
			var _majorName = $(v).find("input[name=majorName]").val();
			var _degree = $(v).find("select[name=degree]").val();
			if(_startDate!=''){
				if(!_now&&(_endDate==''||new Date(_startDate)>new Date(_endDate))){
					alert("��ѡ�������������ʱ����߹�ѡĿǰ�ڶ�ѡ�"); flag =true;return false;
				}
			}
			
			if(_now){
				_endDate='';
			}
			
			if((_startDate!=null&&_startDate!='')||(_endDate!=null&&_endDate!='')||(_schoolName!=null&&_schoolName!='')||(_majorName!=null&&_majorName!='')||(_degree!=null&&_degree!='')){
				eduList.push({
					'startDateStr':_startDate,
					'endDateStr':_endDate,
					'schoolName':_schoolName,
					'majorName':_majorName,
					'degree':_degree
					
				});
			}
		});
		
		if(flag){
			return false;
		}
		cand.eduExps=eduList;
		
		
		//��ְ����
		var tends = {};
		tends.hopeCallings=$("#hopeCallings").val();
		tends.hopeIndustry=$("#hopeIndustry").val();
		tends.hopeCitys=$("#locations").val();
		tends.curMoney=$("#curMoney").val();
		tends.moneyMonthes=$("#moneyMonthes").val();
		tends.hopeMoney=$("#hopeMoney").val();
		tends.otherMoney=$("#otherMoney").val();
		
		cand.intent=tends;
		//������Ϣ
		var ext = {};
		ext.selfEvaluation=$("#selfEvaluation").val();
		ext.extraInfo=$("#extraInfo").val();
		ext.attachmentsPaths=$("#attachmentsPaths").val();
		ext.fileNames=$("#fileNames").val();
		
		cand.extra=ext;
		cand.hunterId = $("#hunterId").val();
		cand.synType = $("input[name=synType]:checked").val();
		$('#saveLtCandBtn').attr('disabled',"true");
		$.ajax({
		  url:"/member/index.php?c=resume&act=add",
		  type:"post",
		  data:cand,
		  dataType:"json",
		  success:function(data){
			if(data==1)
		  		$("#ensureModal2").modal("show");
			else{
				$.globalMessenger().post({
	                message: "��������ʧ�ܣ�",
	                hideAfter: 2,
	                type: 'error',//error,success
	                showCloseButton: false,
	                singleton:false
	            });
			}
			$('#saveLtCandBtn').removeAttr("disabled");
		   },error:function(data){
			   $.globalMessenger().post({
	                message: "��������ʧ�ܣ�",
	                hideAfter: 2,
	                type: 'error',//error,success
	                showCloseButton: false,
	                singleton:false
	            });
			   $('#saveLtCandBtn').removeAttr("disabled");
		  }
		});
	});
	
	$(".analyse-btn").click(function(){
		var c = $("#htmlContent").val();
		if($.trim(c)==""){
			alert("������������ݣ�");return false;
		}
		$("#data-form")[0].reset();
		$("#hopeCallings").val("");
		$.post("/center/hunter/analysisOnline",{resumeContent:c},function(data){
			//$("#htmlContent").val(data.htmlContent);
			$.globalMessenger().post({
                message: "�����ɹ���",
                hideAfter: 2,
                type: 'success',//error,success
                showCloseButton: false,
                singleton:false
            });
			
			cleanLeftData();
			
			if (data.sex == 0) $("input[name='sex'][value=0]").prop("checked", true);
			if (data.sex == 1) $("input[name='sex'][value=1]").prop("checked", true);
			$("#mobile").val(data.mobile);
			$("#email").val(data.email);
			$("#resName").val(data.name);
			$("#workYear").val(data.workYear);
			$("#degree").val(data.degree)
			$("#birthDay").val(data.birthDayStr);
			$("#selfEvaluation").val(data.extra.selfEvaluation);
			
			var worksdata = data.workExp;
			var edudata = data.eduExps;
			var prodata = data.proExp;
			if(worksdata){
				$.each(worksdata,function(i,v){
					addWork(v);
				});
			}
			
			if(edudata){
				$.each(edudata,function(i,v){
					addEdu(v);
				});
			}
			
			if(prodata){
				$.each(prodata,function(i,v){
					addPro(v);
				});
			}
			
		},"json")
		
	});
	
	$("#conAdd").click(function(){
		$("#ensureModal2").modal("hide");
		window.location.reload();
	});
	
	$("#toreumebase").click(function(){
		$("#ensureModal2").modal("hide");
		window.location="/center/hunter/resume/resumeWaitRec";
	});
	
});


function valideResumeForm(){
	var tip = $("#tips");
	var name = $("#resName").val();
	var sex = $("input[name=sex]:checked").val();
	var mobile = $("#mobile").val();
	var email = $("#email").val();
	var curMoney = $("#curMoney").val();
	var moneyMonthes = $("#moneyMonthes").val();
	var workYear = $("#workYear").val();
	var hopeMoney = $("#hopeMoney").val();
	var hopeMoney = $("#companyName").val();
	var hopeMoney = $("#posName").val();
	
	msg="";
	var cityId = $("#cityId").val();
	if(name==""){
		msg="����д������";
		tip.text(msg);
		return false;
	}
	
	if(sex==""){
		msg="��ѡ���Ա�";
		tip.text(msg);
		return false;
	}
	
	if(!validMobile(mobile)){
		msg="�ֻ������ʽ����ȷ��";
		tip.text(msg);
		return false;
	}

	
	if(!validEmail(email)){
		msg="�����ʽ����ȷ��";
		tip.text(msg);
		return false;
	}
	
	if(!validNum(curMoney)){
		msg="��н����д���֣�";
		tip.text(msg);
		return false;
	}
	
	if(!validNum(moneyMonthes)){
		msg="��н����������д���֣�";
		tip.text(msg);
		return false;
	}
	
	if(!validNum(workYear)){
		msg="������������д���֣�";
		tip.text(msg);
		return false;
	}
	if(!validNum(hopeMoney)){
		msg="��������д���֣�";
		tip.text(msg);
		return false;
	}
	
	tip.text(msg);
	return true;
	
}


function validMobile(val){
	val = $.trim(val);
	if(!val||val==""){
		return true;
	}
	
	if(/^13[0-9]{9}$|14[0-9]{9}$|15[0-9]{9}$|18[0-9]{9}$|17[0-9]{9}$/.test(val)){
		return true;		
	}else{
		return false;
	}
	
}

function validNum(val){
	val = $.trim(val);
	if(!val||val==""){
		return true;
	}
	
	if(/^\d+$/.test(val)){
		return true;		
	}else{
		return false;
	}
	
}

function validEmail(val){
	val = $.trim(val);
	if(!val||val==""){
		return true;
	}
	
	if(/^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(val)){
		return true;		
	}else{
		return false;
	}
	
}

function wordsDeal(obj,maxLen,tipId) {
	var baseText = $(obj).val();
	
	var AllLength = $(obj).val().length;
	var actLength = $(obj).val().replace(/(^\s*)|(\s*$)/g,"").length;
	var spacLenth = AllLength - actLength;
	
	if (actLength >= maxLen) {
		var num = $(obj).val().substr(0, maxLen);
		$(obj).val(num);
		$("#"+tipId).text(num.replace(/(^\s*)|(\s*$)/g,"").length);
		return false;
		//alert("�����������ƣ�������ֽ����ضϣ�");
	} else{
		$("#"+tipId).text(actLength);
	}
	
}

function addEdu(d){
	var thisobj = $(".addEduExp").parents("table");
	var sz = $("table.eduexp").size();
	
//	if(sz==1){
//		$("table.eduexp").find("div.form-inline").append('<a class="removeExp"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>');
//	}
	
	var table = 
		'<table class="'+(sz==0?"":"dash")+' eduexp" width="100%" border="0" cellspacing="0" cellpadding="0">'
		+'<tr>'
		+'<th class="optional">ѧϰʱ��</th>'
		+'<td colspan="3">'
		+'<div class="form-inline">'
		+'<div class="input-group input-group-sm date form_date ym" data-date-format="yyyy-mm">'
   
		+'<input class="form-control" size="16" type="text"  value="'+(d?d.startDateStr:"")+'" name="startDate"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'
		+'</div> - <div class="input-group input-group-sm date form_date ym" data-date-format="yyyy-mm">'
   
		+'<input class="form-control" size="16" type="text"  value="'+(d?d.endDateStr:"")+'" name="endDate"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'
		+'</div>'

		+'<span class="tip">'
		+' <input type="checkbox" name="now" '+(d?(d.endDateStr==""?"checked":""):"")+'><span class="black">Ŀǰ�ڶ�</span> <span class="gray">�������ڲ�ѡ����ʾ����</span>'
		+'</span>'
		+'<a class="removeExp"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">ѧУ</th>'
		+'<td colspan="3" class="input-group-sm"><input type="text" value="'+(d?d.schoolName:"")+'" name="schoolName" class="form-control indst" placeholder="ѧУ����"></td>'
   
		+'</tr>'
		+'<tr>'
		+'<th class="optional">רҵ</th>'
		+'<td class="input-group-sm"><input type="text" value="'+(d?d.majorName:"")+'" name="majorName" class="form-control adr" placeholder="רҵ"></td>'
		+'<th class="optional">ѧ��</th>'
		+'<td class="input-group-sm">'
    	
		+'<select class="form-control adr" name="degree">'
		+'<option value="">��ѡ��</option>'
		+'<option value="1" '+(d?(d.degree==1?"selected":""):"")+'>��ר</option>'
		+'<option value="2" '+(d?(d.degree==2?"selected":""):"")+'>����</option>'
		+'<option value="3" '+(d?(d.degree==3?"selected":""):"")+'>˶ʿ</option>'
		+'<option value="4" '+(d?(d.degree==4?"selected":""):"")+'>MBA</option>'
		+'<option value="5" '+(d?(d.degree==4?"selected":""):"")+'>EMBA</option>'
		+'<option value="6" '+(d?(d.degree==4?"selected":""):"")+'>��ʿ</option>'
		+'<option value="7" '+(d?(d.degree==4?"selected":""):"")+'>��ʿ��</option>'
		+'</select>'
    
		+'</td>'
		+'</tr>'
		
		+'<tr>'
		+'<th class="empty"></th><td clospan="4"">  </td>'
		+'</tr>'
		+'</table>' ;
	thisobj.before(table);
	
	$('.form_date.ym').datetimepicker( {
		language : 'zh-CN',
		weekStart : 1,
		todayBtn : 1,
		autoclose : 1,
		todayHighlight : 1,
		startView : 3,
		minView : 3,
		forceParse : 0,
		format : 'yyyy-mm',
		pickerPosition : "bottom-left"
	}).on('show', function(ev) {
		$('.form_date').datetimepicker('update')
	});
}

function addPro(d){
	var thisobj = $(".addProExp").parents("table");
	var sz = $("table.proexp").size();
	
//	if(sz==1){
//		$("table.proexp").find("div.form-inline").append('<a class="removeExp"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>');
//	}
	
	var table = 
		'<table  class="'+(sz==0?"":"dash")+' proexp" width="100%" border="0" cellspacing="0" cellpadding="0">'
		+'<tr>'
		+'<th class="optional">��ְʱ��</th>'
		+'<td colspan="3">'
		+'<div class="form-inline">'
		+'<div class="input-group input-group-sm date form_date ym" data-date-format="yyyy-mm">'
   
		+'<input class="form-control" size="16" type="text"  value="'+(d?d.startDateStr:"")+'" name="startDate"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'
		+'</div> - <div class="input-group input-group-sm date form_date ym" data-date-format="yyyy-mm">'
   
		+'<input class="form-control" size="16" type="text"  value="'+(d?d.endDateStr:"")+'" name="endDate"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'
		+'</div>'

		+'<span class="tip">'
		+' <input type="checkbox" name="now" '+(d?(d.endDateStr==""?"checked":""):"")+'><span class="black">����</span> <span class="gray">�������ڲ�ѡ����ʾ����</span>'
		+'</span>'
		+'<a class="removeExp"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��Ŀ����</th>'
		+'<td colspan="3" class="input-group-sm"><input type="text" class="form-control indst" value="'+(d?d.proName:"")+'" name="proName" placeholder="��Ŀ����"></td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��Ŀְ��</th>'
		+'<td colspan="3" class="input-group-sm"><input type="text" class="form-control indst" value="'+(d?d.projectOffice:"")+'" name="projectOffice" placeholder="��Ŀְλ"></td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">���ڹ�˾</th>'
		+'<td colspan="3" class="input-group-sm"><input type="text" class="form-control indst" value="'+(d?d.subCompany:"")+'" name="subCompany" placeholder="���ڹ�˾"></td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��Ŀ����</th>'
		+'<td colspan="3">'
		+'<div class="indst">'
		+'<textarea class="form-control" rows="3" name="proDes" >'+(d?d.proDes:"")+'</textarea>'
		+'<p class="remainWordTip">0 / 1000</p>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��Ŀְ��</th>'
		+'<td colspan="3">'
		+'<div class="indst">'
		+'<textarea class="form-control" rows="3" name="projectRole" >'+(d?d.projectRole:"")+'</textarea>'
		+'<p class="remainWordTip">0 / 500</p>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��Ŀҵ��</th>'
		+'<td colspan="3">'
		+'<div class="indst">'
		+'<textarea class="form-control" rows="3" name="projectPerfromnance" >'+(d?d.proPerTip:"")+'</textarea>'
		+'<p class="remainWordTip">0 / 500</p>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'</table>' ;
	thisobj.before(table);
	
	$('.form_date.ym').datetimepicker( {
		language : 'zh-CN',
		weekStart : 1,
		todayBtn : 1,
		autoclose : 1,
		todayHighlight : 1,
		startView : 3,
		minView : 3,
		forceParse : 0,
		format : 'yyyy-mm',
		pickerPosition : "bottom-left"
	}).on('show', function(ev) {
		$('.form_date').datetimepicker('update')
	});
}


function addWork(d){
	var thisobj = $(".addWorkExp").parents("table");
	var sz = $("table.workexp").size();
	
//	if(sz==1){
//		$("table.workexp").find("div.form-inline").append('<a class="removeExp"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>');
//	}
	
	var table = 
		'<table class="'+(sz==0?"":"dash")+' workexp" width="100%" border="0" cellspacing="0" cellpadding="0">'
		+'<tr>'
		+'<th class="optional">��ְʱ��</th>'
		+'<td colspan="3">'
		+'<div class="form-inline">'
		+'<div class="input-group input-group-sm date form_date ym" data-date-format="yyyy-mm">'
		+'<input class="form-control" size="16" type="text"  value="'+(d?d.startDateStr:"")+'" name="startDate"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'
		+'</div> - '
		+'<div class="input-group input-group-sm date form_date ym" data-date-format="yyyy-mm">'
		+'<input class="form-control" size="16" type="text"  value="'+(d?d.endDateStr:"")+'" name="endDate"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'
		+'</div>'

		+'<span class="tip">'
		+' <input type="checkbox" name="now" '+(d?(d.endDateStr==""?"checked":""):"")+'><span class="black">����</span> <span class="gray">�������ڲ�ѡ����ʾ����</span>'
		+'</span>'
		+'<a class="removeExp"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��˾����</th>'
		+'<td class="input-group-sm"><input type="text" value="'+(d?d.companyName:"")+'" class="form-control adr" placeholder="��˾����" name="companyName"></td>'
		+'<th class="optional">ְλ����</th>'
		+'<td class="input-group-sm"><input type="text" value="'+(d?d.posName:"")+'" class="form-control adr" placeholder="ְλ����" name="posName"></td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��˾����</th>'
		+'<td colspan="3">'
		+'<div class="indst">'
		+'<textarea class="form-control" rows="3" name="companyDes">'+(d?d.companyDes:"")+'</textarea>'
		+'<p class="remainWordTip">0 / 500</p>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'<tr>'
		+'<th class="optional">��������</th>'
		+'<td colspan="3">'
		+'<div class="indst">'
		+'<textarea class="form-control" rows="3" name="workDes">'+(d?d.workDes:"")+'</textarea>'
		+'<p class="remainWordTip">0 / 5000</p>'
		+'</div>'
		+'</td>'
		+'</tr>'
		+'</table>' ;
	
	thisobj.before(table);
	

}
$('.form_date.ym').datetimepicker( {
	language : 'zh-CN',
	weekStart : 1,
	todayBtn : 0,
	autoclose : 1,
	todayHighlight : 1,
	startView : 3,
	minView : 3,
	forceParse : 0,
	format : 'yyyy-mm',
	pickerPosition : "bottom-left"
}).on('show', function(ev) {
	$('.form_date').datetimepicker('update')
});

function cleanLeftData(){
	$("#mobile").val("");
	$("#email").val("");
	$("#resName").val("");
	$("#workYear").val("");
	$("#degree").val("");
	$("#birthDay").val("");
	$("#selfEvaluation").val("");
	
	$(".workexp,.eduexp,.proexp").remove();
}

