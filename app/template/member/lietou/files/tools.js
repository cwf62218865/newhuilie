//获取时间段的欢迎语句
var getWelcomeTime = function(){
	var hour = new Date().getHours() ;
	if(hour < 6){return"凌晨好！";} 
	else if (hour < 9){return"早上好！";} 
	else if (hour < 11){return"上午好！";} 
	else if (hour < 13){return"中午好！";} 
	else if (hour < 18){return"下午好！";} 
	//else if (hour < 19){return"傍晚好！";} 
	else if (hour < 22){return "晚上好！";} 
	else {return"夜深了！";} 
};

/** 
 * 格式化金钱数字1,234,567
 * @param str
 * @returns
 */
function formatCurrencyTenThou(str) {
	if(str==null||str==undefined){
		return 0;
	}
	if(isNaN(str)){
		return 0;
	}else{
		str = str+'';
		str = str.indexOf(".") == -1 ? str : str.substr(0,  str.indexOf("."));
		var str=str.split('').reverse().join('').replace(/(\d{3})/g,'$1,').replace(/\,$/,'').split('').reverse().join('');
		return str;
	}
}


//扩展date格式化
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


/*//日期格式化为2015-04-04 09:18:20
function formateDate(time) {
	if(time !=null){
		var date = new Date();
		date.setTime(time);
		var str = date.Format("yyyy-MM-dd hh:mm:ss");
		return str;
	}
	else{
		return "";
	}
}*/
//日期格式化为2015-04-04
function formateDate1(time) {
	var date = new Date();
	date.setTime(time);
	var str = "";
	str = date.getFullYear() + "-" + (date.getDate() < 10 ? "0"+date.getDate():date.getDate())+"-"+
	(date.getDay() < 10 ? "0"+date.getDay():date.getDay());
	return str;
}

//日期格式化为2015-04-04 09:18:20
function formateDate(time,format) {
	if(time != null){
		var date = new Date();
		date.setTime(time);
		var str = date.Format(format);
		return str;
	}
	else{
		return "";
	}
}

function getDateStr(time,format) {
	if(time == "" || time == null) {
		return "";
	}
	
	var date = new Date();
	var todayStr = date.Format("yyyy-MM-dd");
	date.setDate(date.getDate()-1);//昨天
	var yesStr = date.Format("yyyy-MM-dd");
	date.setDate(date.getDate()+2);//明天
	var tomStr = date.Format("yyyy-MM-dd");
	
	var temp = new Date();
	temp.setTime(time);
	var tempStr = temp.Format("yyyy-MM-dd");
	if(tempStr == todayStr) {
		return "今天&nbsp;"+temp.Format("hh:mm");
	}if(tempStr == yesStr) {
		return "昨天&nbsp;"+temp.Format("hh:mm");
	}if(tempStr == tomStr) {
		return "明天&nbsp;"+temp.Format("hh:mm");
	}else {
		return temp.Format(format);
	}
}


/**数字转换为中文大写*/
function numberToChinese(num) {	
	if(!/^\d*(\.\d*)?$/.test(num)) 
	{ 
	alert("你输入的不是数字，请重新输入!"); 
	return false; 
	} 
	var AA = new Array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"); 
	var BB = new Array("","拾","佰","仟","万","亿","点",""); 
	var a = (""+ num).replace(/(^0*)/g, "").split("."), k = 0, re = ""; 
	for(var i=a[0].length-1; i>=0; i--) 
	{ 
	switch(k) 
	{ 
	case 0 : 
	re = BB[7] + re; 
	break; 
	case 4 : 
	if(!new RegExp("0{4}\\d{"+ (a[0].length-i-1) +"}$").test(a[0])) 
	re = BB[4] + re; 
	break; 
	case 8 : 
	re = BB[5] + re; 
	BB[7] = BB[5]; 
	k = 0; 
	break; 
	} 
	if(k%4 == 2 && a[0].charAt(i)=="0" && a[0].charAt(i+2) != "0") re = AA[0] + re; 
	if(a[0].charAt(i) != 0) re = AA[a[0].charAt(i)] + BB[k%4] + re; 
	k++; 
	} 
	if(a.length>1) { 
	re += BB[6]; 
	for(var i=0; i<a[1].length; i++) re += AA[a[1].charAt(i)]; 
	} 
	return re;
}

/**邮箱验证，多个邮箱逗号分隔**/
function isEmail(email) {
	if(email != "") {
		 var emailReg = /^((([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6}\,))*(([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})))$/;
		 if(!emailReg.test(email)){
			 return false;
		 }
		 return true;
	}
}

var now = new Date(); //当前日期 
var nowDayOfWeek = now.getDay(); //今天本周的第几天 
var nowDay = now.getDate(); //当前日 
var nowMonth = now.getMonth(); //当前月 
var nowYear = now.getYear(); //当前年 
nowYear += (nowYear < 2000) ? 1900 : 0; //

var lastMonthDate = new Date(); //上月日期 
lastMonthDate.setDate(1); 
lastMonthDate.setMonth(lastMonthDate.getMonth()-1); 
var lastYear = lastMonthDate.getYear(); 
var lastMonth = lastMonthDate.getMonth();


// 获得本周的开端日期
function getWeekStartDate() { 
	var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek); 
	return weekStartDate.Format("yyyy-MM-dd"); 
}

// 获得本周的停止日期
function getWeekEndDate() { 
	var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek)); 
	return weekEndDate.Format("yyyy-MM-dd"); 
}

// 获得本月的开端日期
function getMonthStartDate(){ 
	var monthStartDate = new Date(nowYear, nowMonth, 1); 
	return monthStartDate.Format("yyyy-MM-dd"); 
}

// 获得本月的停止日期
function getMonthEndDate(){ 
	var monthEndDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth)); 
	return monthEndDate.Format("yyyy-MM-dd"); 
}

// 获得上月开端时候
function getLastMonthStartDate(){ 
	var lastMonthStartDate = new Date(nowYear, lastMonth, 1); 
	return lastMonthStartDate.Format("yyyy-MM-dd"); 
}

// 获得上月停止时候
function getLastMonthEndDate(){ 
	var lastMonthEndDate = new Date(nowYear, lastMonth, getMonthDays(lastMonth)); 
	return lastMonthEndDate.Format("yyyy-MM-dd"); 
}

//获得某月的天数 
function getMonthDays(myMonth){ 
	var monthStartDate = new Date(nowYear, myMonth, 1); 
	var monthEndDate = new Date(nowYear, myMonth + 1, 1); 
	var days = (monthEndDate - monthStartDate)/(1000 * 60 * 60 * 24); 
	return days; 
}


//修复小数点
function tofixedNum(v,n){
	if(isNaN(v)){
		return 0;
	}
	n=n?n:2;
	return parseFloat(v.toFixed(n));
	
}

function isEmpty(str) {
	if(str == "" || str == null) {
		return true;
	}else {
		return false;
	}
}

function getStr(str,defaultStr) {
	if(str == "" || str == null) {
		return defaultStr;
	}else {
		return str;
	}
}

/**
 * 限制输入字数
 * @param obj
 * @param maxLen
 * @param tipId
 * @returns {Boolean}
 */
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
		//alert("超过字数限制，多出的字将被截断！");
	} else{
		$("#"+tipId).text(actLength);
	}
	
}

function toString(str){
	if(str==null)
		return '';
	return str;
}