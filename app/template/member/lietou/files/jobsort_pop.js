function refreshPosSelectState(inputId){
	//var jobIds = $("#"+inputId).val();
	//var txt="";
    //
	////����
	//$("#selectItems").children().remove();
	//$("div.selectdiv").find("a").removeClass("act");
	////û��ѡ��ְλ
	//if(jobIds&&jobIds!=""){
	//	var jobs = jobIds.split(",");
	//	$.each(jobs,function(i,v){
	//		txt += ((i==0?"":",")+ $("a[jobId='"+v+"']").text());
	//		$("a[jobId='"+v+"']").addClass("act");
	//		if($("a[jobId='"+v+"']").text() && $("a[jobId='"+v+"']").text()!=""){
	//			$("#selectItems").append('<li jobsId="'+v+'"><a href="#" class="label alert label-success selectlabel" '
	//				+'role="alert"><span class="innertext">'+$("a[jobId='"+v+"']").text()+'&nbsp;</span>'
	//				+'<span class="glyphicon glyphicon-remove"></span></a></li>');
	//		}
	//	});
	//}
		
	
}


function queryIndustry(mark,obj){
	//��ǰѡ�еĶ����˵�����ҵ
	var curMark = $("ul.jCallingSelectorItem").find("a.jCallingSelectorHadItem3").parent("li").attr("item");
	if(curMark!=mark){
		$("ul.secondmenu").hide();
		
		$("ul.jPosSelectorItem").hide();
		$("ul.jPosSelectorItem[callingmark="+mark+"]").show();	
		$(obj).parents("ul.jCallingSelectorItem").find("a").removeClass("jCallingSelectorHadItem3");
		$(obj).addClass("jCallingSelectorHadItem3");
		$("ul.jPosSelectorItem[callingmark="+mark+"]").children("li:first").find("a").trigger("click");
	}
	
}


function querySubJob(jobId,obj){
	var jobMark = $(obj).parent().attr("jobId");
	//$("div.selectdiv").hide();
	$("ul.secondmenu").hide();
	$("ul.secondmenu[supJobId='"+jobMark+"']").show();
	
	//�ӱ߿�
	$(obj).parents("ul.jPosSelectorItem").find("a").removeClass("jCallingSelectorHadItem3");
	$(obj).addClass("jCallingSelectorHadItem3");
}

function queryJob(jobId,obj){
	var jobIdstr = $(obj).attr("jobId");
	var curcss = $(obj).attr("class");
	//�Ƿ��Ѿ�ѡ��
	if(curcss=="act"){
		//�Ѿ�ѡ����,ȡ��ѡ��(ȡ����ʽ,ȡ��ѡ���ǩ,�޸���ѡ���ֵ)
		$(obj).removeClass("act");
		$("#selectItems").find("li[jobsId='"+jobIdstr+"']").remove();
		
	}else{
		//δѡ��״̬,�ж��Ƿ񳬹�����
		var cursize = $("#selectItems").find("li").size();
		
		if(cursize>=5){
			alert("����ְλ���ѡ������");return false;
		}else{
			$("#selectItems").append('<li jobsid="'+jobIdstr+'"><a href="#" class="label alert label-success selectlabel" role="alert"><span class="innertext">'+$("a[jobId='"+jobIdstr+"']").text()+'&nbsp;</span><span aria-hidden="true">&times;</span></a></li>');
			$("a[jobId='"+jobIdstr+"']").addClass("act");
		}		
	}
	
}

$(function(){
	refreshPosSelectState("hopeCallings");
	$("ul.jCallingSelectorItem").children("li[item=carjob]").children("a").trigger("click");	
	$("ul.jPosSelectorItem[callingmark=carjob]").children("li:first").find("a").trigger("click");
	
	$('#popPosDiv').on('shown.bs.modal', function () {
	  	refreshPosSelectState("hopeCallings");
	})
	
	$(document).on("click","a.selectlabel",function(){				
		var jobMark = $(this).parent().attr("jobsid");
		$(this).parent().remove();
		$("div.selectdiv").find("a[jobId="+jobMark+"]").trigger("click");
	});
	
	$("#comfirmSelBtn").click(function(){
		var selects = $("#selectItems").find("li");
		var txt="";
		var vals="";
		if(selects&&selects.size()>0){
			$.each(selects,function(i,v){
				txt += ((i==0?"":",")+ $(v).find(".innertext").text());
				vals += ((i==0?"":",")+ $(v).attr("jobsid").substring(5,9));
			});
		}
		$("#hopeCallings").val(vals);	
		$("#hopeCallingsText").val(txt);
	});
	
});