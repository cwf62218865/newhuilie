function refreshPosSelectState(inputId){
	//var jobIds = $("#"+inputId).val();
	//var txt="";
    //
	////清理
	//$("#selectItems").children().remove();
	//$("div.selectdiv").find("a").removeClass("act");
	////没有选择职位
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
	//当前选中的二级菜单的行业
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
	
	//加边框
	$(obj).parents("ul.jPosSelectorItem").find("a").removeClass("jCallingSelectorHadItem3");
	$(obj).addClass("jCallingSelectorHadItem3");
}

function queryJob(jobId,obj){
	var jobIdstr = $(obj).attr("jobId");
	var curcss = $(obj).attr("class");
	//是否已经选择
	if(curcss=="act"){
		//已经选择了,取消选择(取消样式,取消选择标签,修改已选择的值)
		$(obj).removeClass("act");
		$("#selectItems").find("li[jobsId='"+jobIdstr+"']").remove();
		
	}else{
		//未选择状态,判断是否超过上限
		var cursize = $("#selectItems").find("li").size();
		
		if(cursize>=5){
			alert("期望职位最多选择五个项！");return false;
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