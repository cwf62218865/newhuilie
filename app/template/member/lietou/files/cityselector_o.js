/*
	Author: CaiHao
	Function: 城市单(多)选择器
*/
document.write('<script src="/js/dic/citydata.js"><\/script>')
jQuery.fn.extend({
  loadCitySelector:function(){
    this.each(function(index){
      
		var oBox = $(this), nMax = $(oBox).attr("maxSelect"), sLang = $(oBox).attr("lang"), sJoinSign=", ", sShowHot = $(oBox).attr("showhot"), enClass="", getCityID, delCityID, getCityUpID, getCityName, aChked=[], sChkedShow, aChkedShow=[], aChkedVal=[];
		var sHideTip="", aInitValLen, sNoShowHot="", sTempInitVal, sTempInitName, aInitName, aInitVal, fnShowSubDelay, fnShowSub, oCurActiveSubLeave, oCurActiveSubEnter;
		if(typeof(sLang)=="undefined" || sLang!="en") {
			var loadingTxt="加载中..", plsSelTxt="请选择", maxSelTxt="最多可选择 ", maxUnitTxt=" 项", okTxt="确定", hadSelTxt="已选择", hotCityTxt="热门城市", allCityTxt="全国城市", maxSelNumTxt="最多可选择"+nMax+"项", errTxt="非常抱歉，加载数据失败，请刷新或稍后再尝试！";
			}
		else {
			var loadingTxt="Loading..", plsSelTxt="Please Choose", maxSelTxt="Select up to ", maxUnitTxt="", okTxt="Confirm", hadSelTxt="City Chosen", hotCityTxt="Hot Cities", allCityTxt="All Cities", maxSelNumTxt="Select up to"+nMax;
			var enClass=" jCitySelectorPopEn";
		}
		if ( typeof(nMax)=="undefined" ) nMax=1;
		if ( nMax==1 ) { sHideTip=" style=\"display:none\"" }
		if ( sShowHot=="0" ) { var sNoShowHot="style=\"display:none\"" } //是否显示热门城市
		var html="<div class=\"jCitySelectorPop"+enClass+"\" style=\"position:absolute; top:0;\"><div class=\"jCitySelectorPopIn\" style=\"background:#fff; position:absolute; top:0;\"><div class=\"jCitySelectorHD layout\"><span class=\"jCitySelectorMaxTip\""+sHideTip+">"+maxSelTxt+"<em class=\"jCitySelectorMaxNum\">"+nMax+"</em>"+maxUnitTxt+"</span><a "+sHideTip+" class=\"jCitySelectorClose\" href=\"javascript:;\">"+okTxt+"</a></div><div class=\"jCitySelectorItem jCitySelectorHad\"><div class=\"jCitySelectorTit\">"+hadSelTxt+"</div><div class=\"jCitySelectorHadList layout\"></div></div><div class=\"jCitySelectorItem jCitySelectorHot\" "+ sNoShowHot +"><div class=\"jCitySelectorTit\">"+hotCityTxt+"</div><div class=\"jCitySelectorHotList layout\"></div></div><div class=\"jCitySelectorItem jCitySelectorAll\"><div class=\"jCitySelectorTit\">"+allCityTxt+"</div><ul class=\"jCitySelectorAllList layout\"></ul></div></div></div><a class=\"jCitySelectorBtn\" style=\"height:30px;padding:3px 12px;font-size:12px;\" hidefocus=\"true\" href=\"javascript:;\"><span>"+plsSelTxt+"</span></a>"; 
		$(oBox).append(html);
		var oBtn = $(oBox).find(".jCitySelectorBtn:first"), //请选择按钮
			oBtnTxt = $(oBtn).children("span"),
			oPop = $(oBox).find(".jCitySelectorPop:first"), //弹窗
			oItem = $(oBox).find(".jCitySelectorListItem"),
			oAll = $(oBox).find(".jCitySelectorAllList:first"),
			oHot = $(oBox).find(".jCitySelectorHotList:first"),
			chktype=(nMax==1?"radio":"checkbox"),
			oClose = $(oBox).find(".jCitySelectorClose:first"), //确认按钮
			oHad = $(oBox).find(".jCitySelectorHadList:first"),
			sVal = $(oBox).find(".jCitySelectorVal:first").val(), //初始ID
			oVal = $(oBox).find(".jCitySelectorVal:first"),
			sName = $(oBox).find(".jCitySelectorName:first").val(), //初始文字
			oName = $(oBox).find(".jCitySelectorName:first");
		
		if (sVal!="") { //如果有初始值
			aInitName = sName.split(","); aInitVal = sVal.split(",");
			aInitValLen = aInitVal.length;
			for( var m=aInitValLen-1;m>=0;m-- ){
				sTempInitVal = aInitVal[m];
	      			sTempInitName = aInitName[m];
	      			if( sTempInitVal=="" || sTempInitVal=="0" || sTempInitVal==" " ) { aInitVal.splice(m,1) }
	      			if( sTempInitName=="" || sTempInitName=="0" || sTempInitName==" " ) { aInitName.splice(m,1) }
			}
			if( aInitVal.length<=0 ){ oVal.val(""); oName.val(""); }
			else { sInitName = sName.split(","); sInitName = aInitName.join(sJoinSign); $(oBtnTxt).text(sInitName); } //按钮上显示初始文字
		}
      
		function fnInitChked(){ //如果存ID的表单内有值
	      	if ($(oHad).children("a").length>0 ) { $(oHad).children("a").trigger("click"); }
	      	if (oVal.val()!="") {
	      		aInitName = oName.val().split(","); aInitVal = oVal.val().split(",");
	      		for( var m=aInitVal.length-1;m>=0;m-- ){
	      			sTempInitVal = aInitVal[m];
	      			sTempInitName = aInitName[m];
	      			if( sTempInitVal=="" || sTempInitVal=="0" || sTempInitVal==" " ) { aInitVal.splice(m,1) }
	      			if( sTempInitName=="" || sTempInitName=="0" || sTempInitName==" " ) { aInitName.splice(m,1) }
	      		}
			    sInitName = sName.split(","); sInitName = aInitName.join(sJoinSign);
			  	$(oBtnTxt).text(sInitName); //按钮上显示初始文字
	      	  	for (var j=0;j<aInitVal.length;j++){
	      	  		getCityID=aInitVal[j]; getCityName=aInitName[j];
	      	  		//添加城市
	      	  		$(oPop).find(":input[value="+getCityID+"]").prop("checked",true).data("chked",1); //查找选中的ID并勾选
		      		aChked.push([getCityID,getCityName]); 
		      	  	$(oHad).append("<a href=\"javascript:;\" cityID=\""+getCityID+"\">"+getCityName+"</a>");
		      	  	$(oHad).children("[cityID="+getCityID+"]").click(function(){ //已选中的城市绑定事件
			      	    delCityID=$(this).attr("cityID");
			      	    $(oPop).find(":input[value="+delCityID+"]").prop("checked",false).data("chked",0);
			      	    for (var i=0;i<aChked.length;i++){
			      	  	  if (aChked[i][0]==delCityID) {aChked.splice(i,1)};
			      	    }
			      	    //清除值
			      	    oVal.val("");oName.val("");oBtnTxt.text("请选择");
			      	    $(this)[0].style.background = "";
			      	    $(this).remove();
			      	})
		      	}
			}
		}
      
      
      var winScrollTop, winScrollLeft, winWidth, winHeight;
      var getWinSize = function () { //获取可见区域宽度和高度
      	winWidth = $(window).width(); winHeight = $(window).height() ;
      };
      getWinSize();
      $(window).resize(function(){ getWinSize();})
      
      var rePopPos = function(btn, pop, btnOnClass){ //重置弹层相对触发按钮的位置
      	var btnTop = $(btn).offset().top, //对象离文档顶部的距离
       		btnLeft = $(btn).offset().left,
       		btnHeight = $(btn).outerHeight(),
      		btnWidth = $(btn).outerWidth(),
      		popHeight = $(pop).outerHeight(),
      		popWidth = $(pop).outerWidth(),
      		winScrollTop = $(window).scrollTop(),
      		winScrollLeft = $(window).scrollLeft();
      	//alert( winHeight - btnTop - winScrollTop +" " + popHeight + "******" + ( btnTop - winScrollTop ) + " " + popHeight )
       	//alert("可见区域高度:"+ winHeight +"\n按钮离文档顶部高度:" + btnTop + "\n已滚动高度:"+ winScrollTop +"\n弹层高度:" + popHeight)
       	if ( winHeight - ( btnTop - winScrollTop ) < popHeight && btnTop - winScrollTop > popHeight ) { //如果可见部分的下面不足以容纳弹层，且上面高度要大于弹层高度，则让弹层显示在上方
       		$(pop).css( "top",-(popHeight-1) + "px");
       		if(btnOnClass) $(btn).addClass( btnOnClass );
       	} else {
       		$(pop).css( "top", (btnHeight-1)+"px"); //将弹层top样式还原
       		if(btnOnClass) $(btn).removeClass( btnOnClass );
       	}
       	
       	if ( winWidth - ( btnLeft - winScrollLeft ) < popWidth && btnLeft - winScrollLeft > popWidth ) { //如果可见部分的右边不足以容纳弹层，且左边宽度要大于弹层宽度，则让弹层显示在左边
       		$(pop).css( "left",-( popWidth - btnWidth ) + "px");
       	} else {
       		$(pop).css( "left", "0"); //将弹层left样式还原
		}
      }
      
      //加载城市
//      $.ajax({
//          url: "/js/selector/citydata.json",
//		  contentType:"application/json;charset=utf-8;",
//		  dataType:"json",
//		  //error:function(){alert(errTxt)},
//		  success:function(data){
      	 var data = _allcitydata;
		  var directhtml = data.directcity, all = data.allcity, direct = data.directcity, directhtml = "", 
		      allhtml = "", subhtml = "", zindexMax = "9999999", isHasSub = "", oCurHasSub;
			var fnLoadEvent = function(){
				var oHasSub = $(oBox).find(".jCitySelectorHasSub"),
					//oHasSubOn = $(oBox).find(".jCitySelectorOn"),
					oNoSub = $(oBox).find(".jCitySelectorNoSub"),
					oChkInps = $(oBox).find(":input");
		        $(oHasSub).click(function(){ //一级列表点击后显示下级，并添加样式
		        	oCurHasSubBtn = $(this);
		        	oCurHasSub = $(this).next();
					$(this).toggleClass("jCitySelectorOn");
					rePopPos(oCurHasSubBtn, oCurHasSub, "");
					$(oCurHasSub).toggle();
					if ( $(oCurHasSub).is(":visible") ) {
							$(oCurHasSub).parent().css("zIndex",zindexMax).mouseleave(function(){ //移开下级列表时隐藏
							$(oCurHasSub).hide().prev().removeClass("jCitySelectorOn").parent().css("zIndex","");
						})
					}
					})
		      	
		      	if ( nMax>1 ) { //如果多选
		      	$(oNoSub).click(function(){
		      	  var oChkInp= $(":input",this);
		      	  getCityID = $(this).attr("cityID");
		      	  getCityName = $(this).text();
		      	  
		      	  if ( $(oChkInp).data("chked")!=1  ) { //如果未选中，点击后...
			      	  if (aChked.length >= nMax) {$(oChkInp).prop("checked",false);alert(maxSelNumTxt);return false;}
			      	  
			      	  $(oPop).find(":input[value="+getCityID+"]").prop("checked",true).data("chked",1); //查找选中的ID并勾选
			      	  
			      	  //添加城市
			      	  aChked.push([getCityID,getCityName]); 
			      	  $(oHad).append("<a href=\"javascript:;\" cityID=\""+getCityID+"\">"+getCityName+"</a>");
			      	  $(oHad).children("[cityID="+getCityID+"]").click(function(){ //已选中的城市绑定事件
			      	    //删除城市
			      	    delCityID=$(this).attr("cityID");
			      	    $(oPop).find(":input[value="+delCityID+"]").prop("checked",false).data("chked",0);
			      	    for (var i=0;i<aChked.length;i++){
			      	  	  if (aChked[i][0]==delCityID) {aChked.splice(i,1)};
			      	    }
			      	    $(this)[0].style.background = "";
			      	    $(this).remove();
			      	  })
			      	  	
			      	  if($(this).hasClass("jCitySelectorItemTop")) {
			      	     $(this).nextAll().children(":checked").parent().each(function(){ $(this).trigger("click") });
			      	  } 
			      	  else if($(this).parent().hasClass("jCitySelectorListSub")) {
			      	    var citytop=$(this).siblings(":first").children(":input");
			      	    if ( citytop.prop("checked")=="checked" ) {
			      	       citytop.parent().trigger("click")
			      	     }
			      	   }
		      	    
		      	  } else { //否则如果已选中
		      	  	$(oPop).find(":input[value="+getCityID+"]").prop("checked",false).data("chked",0);
		      	  	for (var i=0;i<aChked.length;i++){
		      	  	  if (aChked[i][0]==getCityID) {aChked.splice(i,1)};
		      	  	}
		      	  	$(oHad).find("a[cityID="+getCityID+"]").remove();
		      	  }
		      	})
		      	
		      	} else { //如果是单选
		      		$(oNoSub).click(function(){
			      	  var oChkInp= $(":input",this);
			      	  getCityID = $(this).attr("cityID");
			      	  getCityName = $(this).text();
			      	  
			      	  if ( $(oChkInp).data("chked")!=1  ) { //如果未选中，点击后...
				      	  //先删除已选中的城市
				      	  if ($(oHad).children().length>0){
				      	  	aChked.pop();
				      	  	$(oHad).children().trigger("click");
				      	  }
				      	  
				      	  //添加城市
				      	  aChked.push([getCityID,getCityName]);
				      	  $(oPop).find(":input[value="+getCityID+"]").prop("checked",true).data("chked",1); //查找选中的ID并勾选
				      	  $(oHad).append("<a href=\"javascript:;\" cityID=\""+getCityID+"\">"+getCityName+"</a>");
				      	  
				      	  $(oHad).children("[cityID="+getCityID+"]").click(function(){ //已选中的城市绑定事件
				      	    //删除城市
				      	    delCityID=$(this).attr("cityID");
				      	    $(oPop).find(":input[value="+delCityID+"]").prop("checked",false).data("chked",0);
				      	    aChked.shift();
				      	    $(this)[0].style.background = "";
				      	    $(this).remove();
				      	  })
			      	  	}
			      	  
			      	  $(oClose).trigger('click');
		      	    })
		      	    //单选时的绑定事件结束
		      	}
		      	
			}
			
			if(typeof(sLang)=="undefined" || sLang!="en") {
				//加载中文城市数据
				(function loadCityAll(){
					for(var i=0;i<all.length;i++){
						var sub = all[i].subcity, subhtmlItem="";						
						if (sub.length>0) {isHasSub = " jCitySelectorHasSub";} else {isHasSub=" jCitySelectorNoSub"}
						for(var j=0;j<sub.length;j++){
							subhtmlItem=subhtmlItem+"<a class=\"jCitySelectorListSubItem jCitySelectorNoSub\" cityID=\""+sub[j].cityCode+"\" href=\"javascript:;\" title=\""+sub[j].cityName+"\"><input type=\""+ chktype +"\" value=\""+sub[j].cityCode+"\"><span>"+sub[j].cityName+"</span></a>";
						}
						subhtml="<div class=\"jCitySelectorListSub\"><a class=\"jCitySelectorListSubItem jCitySelectorNoSub jCitySelectorItemTop\" cityID=\""+all[i].value+"\" href=\"javascript:;\" title=\""+all[i].name+"\"><input type=\""+ chktype +"\" value=\""+all[i].value+"\"><span>"+all[i].name+"</span></a>"+subhtmlItem+"</div>";
						allhtml=allhtml+"<li><a href=\"javascript:;\" class=\"jCitySelectorListItem jCitySelectorHasSub\" value=\""+all[i].value+"\" title=\""+all[i].name+"\" >"+all[i].name+"</a>"+subhtml+"</li>";
					}
					$(oAll).html(allhtml);
				})();
				
				//加载热门城市数据
				(function loadCityDirect(){
					for(var i=0;i<direct.length;i++){
						directhtml=directhtml+"<a class=\"jCitySelectorListItem jCitySelectorNoSub\" cityID=\""+direct[i].value+"\" href=\"javascript:;\" title=\""+direct[i].name+"\"><input type=\""+chktype+"\" value=\""+direct[i].value+"\"><span>"+direct[i].name+"</span></a>";
					}
					oHot.html(directhtml);
					fnLoadEvent();
				})();
			}
			else {
				//加载英文数据
				(function loadCityAll(){
					for(var i=0;i<all.length;i++){
						var sub = all[i].subcity, subhtmlItem="";						
						if (sub.length>0) {isHasSub = " jCitySelectorHasSub";} else {isHasSub=" jCitySelectorNoSub"}
						for(var j=0;j<sub.length;j++){
							subhtmlItem=subhtmlItem+"<a class=\"jCitySelectorListSubItem jCitySelectorNoSub\" cityID=\""+sub[j].cityCode+"\" href=\"javascript:;\" title=\""+sub[j].cityEnName+"\"><input type=\""+ chktype +"\" value=\""+sub[j].cityCode+"\"><span>"+sub[j].cityEnName+"</span></a>";
						}
							subhtml="<div class=\"jCitySelectorListSub\"><a class=\"jCitySelectorListSubItem jCitySelectorNoSub jCitySelectorItemTop\" cityID=\""+all[i].value+"\" href=\"javascript:;\" title=\""+all[i].enName+"\"><input type=\""+ chktype +"\" value=\""+all[i].value+"\"><span>"+all[i].enName+"</span></a>"+subhtmlItem+"</div>";
						allhtml=allhtml+"<li><a href=\"javascript:;\" class=\"jCitySelectorListItem jCitySelectorHasSub\" value=\""+all[i].value+"\" title=\""+all[i].enName+"\">"+all[i].enName+"</a>"+subhtml+"</li>";
					}
					$(oAll).html(allhtml);
				})();
				
				//加载热门城市数据
				(function loadCityDirect(){
					for(var i=0;i<direct.length;i++){
						directhtml=directhtml+"<a class=\"jCitySelectorListItem jCitySelectorNoSub\" cityID=\""+direct[i].value+"\" href=\"javascript:;\" title=\""+direct[i].enName+"\"><input type=\""+chktype+"\" value=\""+direct[i].value+"\"><span>"+direct[i].enName+"</span></a>";
					}
					oHot.html(directhtml);
					fnLoadEvent();
				})();
			}
//		}
//      })
      
      $(oBtn).click(function(){ //点击'请选择'按钮时
        if ($(oPop).is(":hidden")) {
        	//upMeTop(oBox);
        	$(document).trigger("click");
        	$(this).addClass("jCitySelectorBtnOn");
        	$(oPop).show();
			rePopPos(oBtn, oPop, "jCitySelectorBtnOnTop")
        	fnInitChked();
        } else {
            $(this).removeClass("jCitySelectorBtnOn");
        	$(document).trigger("click");
        }
      })
      
      $(oClose).click(function(){ //点击确定时关闭层并获取值，显示相应文字
      	aChkedVal=[]; aChkedShow=[];
        for (var i=0;i<aChked.length;i++) {
          aChkedVal.push(aChked[i][0]);
          aChkedShow.push(aChked[i][1]);
        }
        oVal.val(aChkedVal.join());
        oName.val(aChkedShow.join());
        sChkedShow=aChkedShow.join(sJoinSign);
        if (sChkedShow!="" ) { $(oBtnTxt).text(sChkedShow).parent("a").attr("title",sChkedShow); }
        else {$(oBtnTxt).text(plsSelTxt).parent("a").attr("title","")} //如果没有选择城市，则显示“请选择”字样
        $(oPop).hide();
        $(oBtn).removeClass("jCitySelectorBtnOn jCitySelectorBtnOnTop");
      })
      
	  $(oBox).click(function(event){ event.stopPropagation(); }) //点击盒子时停止冒泡
      $(document).click(function(){
      	$(oPop).hide();
      	$(oBtn).removeClass("jCitySelectorBtnOn jCitySelectorBtnOnTop"); 
      })
    })
  }
})


$(function(){
	$(".jCitySelector").loadCitySelector();
	//$(".jCitySelectorPop").bgiframe();
})




/*----------old version-----------------------------------------


function getCitySelector(obtn){
	var chooseLang,chkPopMain, chkPopChkedVal, chkPopChkedBtn, chkPopChkedMax, chkPopChkedHadChked, curval, curtxt, curtxtTmp=new Array();
	 if(!window.lang) {lang="cn"};
	 
	chkPopMain=obtn.parents("[citychkpop='_main']"); //正在操作的父元素
	chkPopChkedVal=chkPopMain.find("[citychkpop='_val']"); //已选择的表单的值
	chkPopChkedBd=chkPopMain.find("[citychkpop='_bd']");
	chkPopChkedHadChked=chkPopMain.find("[citychkpop='_chked']"); //已选择的父元素
	chkPopChkedBtn=chkPopMain.find("[citychkpop='_btn']"); //弹出按钮
	chkPopChkedItem=chkPopMain.find("[citychkpop='_item']");
	chkPopChkedItemList=chkPopMain.find("[citychkpop='_itemList']");
	chkPopChkedDirectList=chkPopMain.find("[citydirectchkpop='_directList']");
	chkPopChkedClose=chkPopMain.find("[citychkpop='_close']");
	chkPopChkedMax=chkPopChkedBtn.attr("chkpopmax");
	chkPopMain.find("[citychkpop='_max']").text(chkPopChkedMax); //显示最大值
	$("[citychkpop='_btn']").not(obtn).next().hide();
	chkPopChkedBd.toggle();
	var docW=$(window).width();
	var chkPopChkedBdMagin=(chkPopChkedBd.offset().left+chkPopChkedBd.width())-docW;
	if (chkPopChkedBdMagin>0) chkPopChkedBd.css("marginLeft",-(chkPopChkedBdMagin+10));
	chkPopMain.click(function(evt){evt.stopPropagation();})
	$(document).click(function(){chkPopChkedBd.hide();})
	$(chkPopChkedClose).click(function(){chkPopChkedBd.hide();})
	
	if(lang=="cn") {
	 	chooseLang="请选择";
	 	overMaxTipLang="最多只能选"+chkPopChkedMax+"项";
	 } else if(lang=="en") {
	 	chooseLang="Please Choose";
	 	overMaxTipLang="Select up to "+chkPopChkedMax;
	 }
	
	//选项限制
	function notSelectedMax(checkboxObj){
		//alert(chkPopChkedHadChked.children().length +" + "+ chkPopChkedMax)
		if ( chkPopChkedHadChked.children().length >= chkPopChkedMax){
			if(chkPopChkedMax>1){
				checkboxObj.attr("checked",false);
				alert(overMaxTipLang);
				return false;
			}
			else if(chkPopChkedMax==1) {
				delChked(chkPopChkedHadChked.find("input:checked").not(checkboxObj));
				return true;
			}
		}
		else {
			return true;
		}
	}
	var chktype=chkPopChkedMax==1?"radio":"checkbox";
	//显示已选择项
	function addChked(o){ //o为checkbox对象
		curval=o.attr("value");
		curtxt=o.siblings("label").text();
		chkPopChkedVal.val(curval+","+chkPopChkedVal.val()); //输出选中值
		if (chkPopChkedBtn.text()==chooseLang){chkPopChkedBtn.text("");curtxtTmp=[];}
		else {curtxtTmp=chkPopChkedBtn.text().split(",");}
		curtxtTmp.push(curtxt);
		chkPopChkedBtn.text(curtxtTmp.join()); //显示选中文字
		chkPopMain.find("[value='"+curval+"']").attr("checked",true);
		chkPopChkedHadChked.html(chkPopChkedHadChked.html()+'<li><input type="checkbox" value="'+curval+'" checked><label>'+o.next().text()+'</label></li>');
		chkPopChkedHadChked.find("input").click(function(){delChked($(this));})
		chkPopChkedHadChked.find("li").click(function(){$(this).children("input").trigger("click")})
		
	}
	//删除已选项
	function delChked(o){
		curval=o.attr("value");
		curtxt=o.siblings("label").text();
		chkPopChkedVal.val(chkPopChkedVal.val().replace(curval+",",""));
		curtxtTmp=(chkPopChkedBtn.text()).split(",");
		for (var jj=0;jj<=curtxtTmp.length;jj++) {if (curtxtTmp[jj]==curtxt) {curtxtTmp.splice(jj,1);}}
		chkPopChkedBtn.text(curtxtTmp.join());
		if (chkPopChkedBtn.text()=="") chkPopChkedBtn.text(chooseLang);
		chkPopMain.find("[value='"+o.attr("value")+"']").attr("checked",false);
		chkPopChkedHadChked.find("[value='"+o.attr("value")+"']").parent().remove();
	}
	//var lang="en";

	if(chkPopChkedItemList.children().length==0){
	$.ajax({
			url: "scripts/citydata.json",
			contentType:"application/json", 
			dataType:"json",
			success:function(data){
			var directhtml=data.directcity, all = data.allcity, direct = data.directcity, directhtml="", allhtml="", subhtml="", zindexMax="9999999", isHasSub="";
			
			if(lang!="en") {
				//加载中文城市数据
				(function loadCityAll(){
					for(var i=0;i<all.length;i++){
						var sub = all[i].subcity, subhtmlItem="";
						if (sub.length>0) {isHasSub = " chkPopHasSub";} else {isHasSub=""}
						for(var j=0;j<sub.length;j++){
							subhtmlItem=subhtmlItem+"<li><input name=\"\" type=\""+chktype+"\" value=\""+sub[j].cityCode+"\"><label for=\""+sub[j].cityCode+"\">"+sub[j].cityName+"</label></li>";
						}
							subhtml="<ul class=\"city_chkpop_item_list_sub_item\" citychkpop=\"_sub\" style=\"display:none;\">"+subhtmlItem+"</ul>";
						allhtml=allhtml+"<li class=\"city_chkpop_item_list_sub"+isHasSub+"\" style=\"z-index:"+(zindexMax-i)+"\" ><input name=\"\" type=\""+chktype+"\" value=\""+all[i].value+"\"><label for=\"\">"+all[i].name+"</label>"+subhtml+"</li>";
					}
					chkPopChkedItemList.html(allhtml);
				})();
				
				
				//加载热门城市数据
				(function loadCityDirect(){
					for(var i=0;i<direct.length;i++){
						directhtml=directhtml+"<li class=\"city_chkpop_item_list_sub\" ><input name=\"\" type=\""+chktype+"\" value=\""+direct[i].value+"\"><label for=\"\">"+direct[i].name+"</label></li>";
					}
					chkPopChkedDirectList.html(directhtml);
				})();
			}
				
			else {
				//加载英文数据
				(function loadCityAll(){
					for(var i=0;i<all.length;i++){
						var sub = all[i].subcity, subhtmlItem="";
						if (sub.length>0) isHasSub = " chkPopHasSub";
						for(var j=0;j<sub.length;j++){
							subhtmlItem=subhtmlItem+"<li><input name=\"\" type=\""+chktype+"\" value=\""+sub[j].cityCode+"\"><label for=\""+sub[j].cityCode+"\">"+sub[j].cityEnName+"</label></li>";
						}
							subhtml="<ul class=\"city_chkpop_item_list_sub_item\" citychkpop=\"_sub\" style=\"display:none;\">"+subhtmlItem+"</ul>";
						allhtml=allhtml+"<li class=\"city_chkpop_item_list_sub"+isHasSub+"\" style=\"z-index:"+(zindexMax-i)+"\" ><input name=\"\" type=\""+chktype+"\" value=\""+all[i].value+"\"><label for=\"\">"+all[i].enName+"</label>"+subhtml+"</li>";
					}
					chkPopChkedItemList.html(allhtml);
				})();
				
				(function loadCityDirect(){
					for(var i=0;i<direct.length;i++){
						directhtml=directhtml+"<li class=\"city_chkpop_item_list_sub\" ><input name=\"\" type=\""+chktype+"\" value=\""+direct[i].value+"\"><label for=\"\">"+direct[i].enName+"</label></li>";
					}
					chkPopChkedDirectList.html(directhtml);
				})();
			}
			
			
			//默认显示已选择城市
			var chkPopChkedValArr=chkPopChkedVal.attr("initval").split(",");
			if (chkPopChkedVal.attr("initval")!=0) {
				chkPopChkedBtn.text(chooseLang);
				for (i=0; i<chkPopChkedValArr.length; i++){
					addChked(chkPopChkedItem.find("input[value='"+chkPopChkedValArr[i]+"']"));
				}
			}
			
			//一级分类的点击和移开事件
			chkPopChkedItemList.find("li").click(function(event){
				if($(this).hasClass("chkPopHasSub")) { //如果有下级分类
					$(this).toggleClass("on");
					$(this).children("[citychkpop='_sub']").toggle();
				}
				else{ //如果没有下级分类
					event.stopPropagation();
					if($(this).children("input").attr("checked")=="checked"){
						$(this).children("input").attr("checked",false).triggerHandler("click");
					}
					else{
						$(this).children("input").attr("checked",true).triggerHandler("click");
					}
				}
			})
			.mouseleave(function(){
				$(this).removeClass("on").children("[citychkpop='_sub']").hide();	
			})
			
			//checkbox的点击事件
			chkPopChkedItemList.find("input").unbind("click").click(function(event){
				event.stopPropagation();
				
				//event.preventDefault();
				if(chkPopChkedMax==1) {$(this).attr("checked")==true?$(this).attr("checked",false):$(this).attr("checked",true);chkPopChkedBd.hide();}
				if (this.checked && notSelectedMax($(this))) { //如果是选中且不超过最大值
					if ($(this).parent().hasClass("chkPopHasSub")) { //如果有子节点
						$(this).siblings("[citychkpop='_sub']").find("input:checked").each(function(){
							delChked($(this));
						})
					}
					else{
						if ($(this).parent().parent().attr("citychkpop")=="_sub") {$(this).parent().parent().hide();delChked($(this).parents("[citychkpop='_sub']").siblings("input:checked"));}	//如果存在父节点
					}
					addChked($(this));
				} 
				else {
					delChked($(this));
				}
			})
		}
	})
	}
	
}

*/