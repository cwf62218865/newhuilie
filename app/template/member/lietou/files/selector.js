// 把我置顶
var maxIndex=999999;
function upMeTop(o) {$(o).css("zIndex",++maxIndex);}

// 切换修改
function toggleModBox(sPrefix,sIndex){
	var oDefBox=$("."+sPrefix+"Def"); //默认显示的内容
	var oAddBox=$("."+sPrefix+"Add"); //添加层
	var aModBox=$("."+sPrefix+"Mod"); //修改层
	var oBtn=$("."+sPrefix+"Btn"); //添加按钮
	if(!sIndex) {
		oDefBox.slideToggle("fast");
		oAddBox.slideToggle("fast");
		oBtn.toggle();
	}
	else {
		oDefBox.slideToggle("fast");
		oBtn.toggle();
		aModBox.eq(sIndex).slideToggle("fast");
	}
}

function toggleAddBox(obj){
	$("#"+obj).slideToggle("fast")
}

function disSalaryInp(obj){ //屏蔽输入框
	var obj = $("#"+obj);
	 
	//if ( obj.prop("disabled") ) {
	if (!$("#salaryNegotiable").is(":checked")) {	 
		obj.removeAttr("ignore");
		obj.prop("disabled", false);
	}
	else {
		obj.attr("ignore","ignore");
		obj.removeClass("Validform_error");
		obj.prop("disabled", true);
		obj.val("");
	}
}

function toggleMultiple(obj,me) { //切换多选
	var obj=$("#"+obj), me=$(me);
	var initTxt=me.attr("init");
	if (obj.is(":visible")) { 
		me.text("已选择");
		obj.hide();
	}
	else {
		me.text("确定选择");
		obj.show();
	}
	
}

jQuery.fn.extend({
	//固定层
	fixIt:function(){
		try{
			var objfix = $(this), objpos = $(this).offset().top;
			if ( $.browser.msie && $.browser.version=="6.0" ) {
				$(window).scroll(function(){
					if ( $(window).scrollTop() > objpos ) {
						$(objfix).css({"marginTop":($(this).scrollTop()-objpos)+"px"});
					} else {
						$(objfix).css({"marginTop":"0"});
					}
				})
			} else {
				$(window).scroll(function(){
					if ( ( $("html").scrollTop() || $("body").scrollTop() ) > objpos ) { 
						$(objfix).css({"position":"fixed", "top":"0px"}) 
					}
					else {
						$(objfix).css({"position":"", "top":""})
					}
				})
			}
		}catch(e){
			
		} 
	}
})



$(function(){
	//表格掠过时变色
	$(".jSubHasHover").children().hover(function(){
		$(this).addClass("jOver");
	},function(){ $(this).removeClass("jOver") })

	//文本框掠过颜色
	$(":text,select,textarea").hover(function(){
		$(this).toggleClass("jInpOver")
	})

	//全选checkbox
	var sCurjChkAllStat;
	$(".jChkAll").click(function(){ 
		sCurjChkAllStat = $(this).attr("checked") ? true : false;
		$(this).parents(".jChkAllBox").find(".jChkAllItem").attr("checked",sCurjChkAllStat);
	})

	//输入框的默认文字
	var oInitjInpTip, oInitjInpTipLabel;
	$(".jInpTipInp").bind("keydown paste", function(){
		oInitjInpTipLabel = $(this).parents(".jInpTipBox").find(".jInpTipLabel");
		$(oInitjInpTipLabel).hide();
	})
	$(".jInpTipInp").blur(function(){
		if( $(this).val()=="" ) { $(oInitjInpTipLabel).show(); }
	})
	$(".jInpTipLabel").click(function(){
		$(this).parents(".jInpTipBox").find(".jInpTipInp").focus();
	})

	//删除简历
	if($("#resumeDelete")[0]){
		$("#resumeDelete").openDOMWindow({
			width:500,
			height:220,
			windowHTTPType:'post',
			windowSource:'ajax'
		})
	}
	
	/*页脚固定在页面最底部
	var footerH = $("#footer").height(), footerTop = $("#footer").offset().top, windowH = $(window).height();
	if ( windowH-footerH-footerTop > 0) { $("#footer").addClass("footer_in_bot") }*/

	//层展开折叠
	$(".jFold").click(function(){
		$(this).toggleClass("ico_fold ico_unfold").parents(".jFoldBox").find(".jFoldContent").slideToggle("fast");
	})

	if ( $(".jVform")[0] ){
		//表单验证
		$(".jVform").Validform({
			tiptype:3, showAllError:true, tipSweep:true,
			datatype:{
				"date": /^\d{4}(\-)\d{1,2}(\1\d{1,2})?$/,
				"nationality":function(gets,obj,curform,datatype){
					if($("#hanNationality").attr("checked")) {
						return true;
					}
					else{
						if(datatype["*"].test(gets) && gets!=obj.attr("tip")) {return true}
						return false;
					}
					return false;
				},
				"seltip": function(gets,obj,curform,datatype){
					if (!datatype["*"].test(gets)) {
						$(obj).parent().parent().css("paddingBottom","28px")
						return false;
					}
					else {
						return true;
					}
				},
				"isSaveSearcher": function(gets,obj,curform,datatype){ //保密状态的半公开设置
					if( $("#saveFlag").attr("checked") ) {
						if(datatype["*"].test(gets) && gets!=obj.attr("tip") ) {return true} else {return false}
					}
					else {return true}
				},
				// "hopeSalary":function(gets,obj,curform,datatype){
				// 	if ( !$("#salaryNegotiable").attr("checked") ) {
				// 		if ( datatype["n1-7"].test(gets) ) {
				// 			return true;
				// 		}
				// 	}
				// 	return false;
				// },
				"studNum": function(gets){
					if ( parseInt( $('#maleNum').val() )+parseInt( $('#femaleNum').val() )==parseInt(  $('#studNum').val() ) )  { 
						return true;
					}
					return false;
				},
				"addJobFind":function(gets,obj,curform,datatype) { //求职意向-寻求职位的最少填写1项
					if( datatype["*"].test( $(obj).val() ) || datatype["*"].test( $(obj).next().val() ) ||  datatype["*"].test( $(obj).next().prev().val() ) ){
						return true
					}
					return false
					
				},
				"cusername":function(gets){
					var reg1=/^[_a-zA-Z]{1}([_a-zA-Z0-9]){5,19}$/;
					if ( reg1.test(gets) ) {
						return true;
					}
					return false;
				},
				"cpassword":function(gets){
					var reg1=/^([_a-zA-Z0-9]){5,16}$/;
					if ( reg1.test(gets) ) {
						return true;
					}
					return false;
				},
				"cpassword1":function(gets){
					if( gets==$("#password").val() ){
						return true;
					}
					return false;
				},
				"idcard":function(gets,obj,curform,datatype){			
					var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];// 加权因子;
					var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];// 身份证验证位值，10代表X;
				
					if (gets.length == 15) {   
						return isValidityBrithBy15IdCard(gets);   
					}else if (gets.length == 18){   
						var a_idCard = gets.split("");// 得到身份证数组   
						if (isValidityBrithBy18IdCard(gets)&&isTrueValidateCodeBy18IdCard(a_idCard)) {   
							return true;   
						}   
						return false;
					}
					return false;
					
					function isTrueValidateCodeBy18IdCard(a_idCard) {   
						var sum = 0; // 声明加权求和变量   
						if (a_idCard[17].toLowerCase() == 'x') {   
							a_idCard[17] = 10;// 将最后位为x的验证码替换为10方便后续操作   
						}   
						for ( var i = 0; i < 17; i++) {   
							sum += Wi[i] * a_idCard[i];// 加权求和   
						}   
						valCodePosition = sum % 11;// 得到验证码所位置   
						if (a_idCard[17] == ValideCode[valCodePosition]) {   
							return true;   
						}
						return false;   
					}
					
					function isValidityBrithBy18IdCard(idCard18){   
						var year = idCard18.substring(6,10);   
						var month = idCard18.substring(10,12);   
						var day = idCard18.substring(12,14);   
						var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
						// 这里用getFullYear()获取年份，避免千年虫问题   
						if(temp_date.getFullYear()!=parseFloat(year) || temp_date.getMonth()!=parseFloat(month)-1 || temp_date.getDate()!=parseFloat(day)){   
							return false;   
						}
						return true;   
					}
					
					function isValidityBrithBy15IdCard(idCard15){   
						var year =  idCard15.substring(6,8);   
						var month = idCard15.substring(8,10);   
						var day = idCard15.substring(10,12);
						var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
						// 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法   
						if(temp_date.getYear()!=parseFloat(year) || temp_date.getMonth()!=parseFloat(month)-1 || temp_date.getDate()!=parseFloat(day)){   
							return false;   
						}
						return true;
					}
				}

			}
		});
		$.Tipmsg.r="";//表单验证正确时显示空
	}
	//input的label效果
	$(".jChkRd").bind("focus click",function(){
		$(this).prevAll("input").first().attr("checked",true);
	})
	
	
});


/*! Copyright (c) 2010 Brandon Aaron (http://brandonaaron.net)
  * Licensed under the MIT License (LICENSE.txt).
  *
  * Version 2.1.2
  */
 /*
 (function($){
 
 $.fn.bgiframe = ($.browser.msie && /msie 6\.0/i.test(navigator.userAgent) ? function(s) {
     s = $.extend({
         top     : 'auto', // auto == .currentStyle.borderTopWidth
         left    : 'auto', // auto == .currentStyle.borderLeftWidth
         width   : 'auto', // auto == offsetWidth
         height  : 'auto', // auto == offsetHeight
         opacity : true,
         src     : 'javascript:;'
     }, s);
     var html = '<iframe class="bgiframe"frameborder="0"tabindex="-1"src="'+s.src+'"'+
                    'style="display:block;overflow:hidden;position:absolute;z-index:-1;'+
                        (s.opacity !== false?'filter:Alpha(Opacity=\'0\');':'')+
                        'top:'+(s.top=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderTopWidth)||0)*-1)+\'px\')':prop(s.top))+';'+
                        'left:'+(s.left=='auto'?'expression(((parseInt(this.parentNode.currentStyle.borderLeftWidth)||0)*-1)+\'px\')':prop(s.left))+';'+
                        'width:'+(s.width=='auto'?'expression(this.parentNode.offsetWidth+\'px\')':prop(s.width))+';'+
                        'height:'+(s.height=='auto'?'expression(this.parentNode.offsetHeight+\'px\')':prop(s.height))+';'+
                 '"/>';
     return this.each(function() {
         if ( $(this).children('iframe.bgiframe').length === 0 )
             this.insertBefore( document.createElement(html), this.firstChild );
     });
 } : function() { return this; });
 
 // old alias
 $.fn.bgIframe = $.fn.bgiframe;
 
 function prop(n) {
     return n && n.constructor === Number ? n + 'px' : n;
 }
 
 })(jQuery);*/
