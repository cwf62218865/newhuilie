
var swfUpload1;
$(function(){
	swfUpload1 = new SWFUpload(
			{
				upload_url : "/hunter/uploadWorks",
				flash_url : "/js/swf/swfupload.swf",
				file_size_limit : "10 MB",
				file_upload_limit:"5",
				file_queue_limit:"5",
				post_params:{type:6,curId:$("#hunterId").val()},
				file_types : "*.txt;*.html;*.htm;*.doc;*.docx;*.mht;*.pdf;*.JPG;*.jpeg;*.gif;*.png;*.bmp",
				file_types_description : "Web Doc Files",
				file_post_name : "file",
				button_width : "96",
				button_height : "26",
				flash_color : "#FFFFFF",
				button_image_url : "/img/hunter/upload_btn3.jpg",
				//button_image_url : "/img/hunter/upload_btn_blank.png",
				button_placeholder_id : "worksBtn",
				button_text : "",  
				//button_text_style : ".redText { color:#57afe1; }",  				
				button_text_left_padding : 0,
				button_text_top_padding : 0,
				button_disabled : false,
				button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,
				button_cursor : SWFUpload.CURSOR.HAND,
				upload_success_handler : uploadSuccess1,
				upload_error_handler : uploadError1,
				file_queue_error_handler : fileQueueError1,
				upload_progress_handler : uploadProgress1,
				file_dialog_complete_handler : dialogComplete1
                
                
			});
	
	$(document).on("click",".glyphicon-remove",function(){
		$(this).parents("div.progressName").remove();
		updatePathVal();
		var stats = swfUpload1.getStats();
		stats.successful_uploads--;
		swfUpload1.setStats(stats);
	});
});
	

function dialogComplete1() {
	upload1();
}
function uploadSuccess1(object, data, response) {
	data = $.parseJSON(eval(data));
	if (data.flag == "1") {
		$.globalMessenger().post({
            message: "上传成功！",
            hideAfter: 2,
            type: 'success',//error,success
            showCloseButton: false,
            singleton:false
        });
		$("#"+object.id).find("div.progressName").attr("filePath",data.potoPath);
		updatePathVal();
		//$("#worksBtn").val(data.potoPath);
	}else if(data.flag == "0"){
		alert("上传失败!");
	}
}
function uploadError1(object, code, message) {
	alert("上传文件失败：" + message+"erroCode:"+code);
}
function uploadProgress1(file, bytesComplete, bytesTotal) {
	var percent = Math.ceil((bytesComplete / bytesTotal) * 100);
	var progress = new FileProgress(file,  "aaa");
	progress.setProgress(percent);
}
function upload1() {
	if (swfUpload1.getStats().files_queued > 0) {
		$("#headProgress").children().remove();
		swfUpload1.startUpload();
	}
}

function fileQueueError1(object, code, message) {
	if (code=='-110'){ alert("文件上传失败！文件不能超过  "+swfUpload1.getInitParams().file_size_limit);};
	if (code=='-100'){ alert("文件上传失败！文件数量不能超过  "+swfUpload1.getInitParams().file_queue_limit)};
}


function updatePathVal(){
	var paths = "";
	var names = "";
	var o = $(".progressName");
	$.each(o,function(i,v){
		paths += $(v).attr("filePath")+"|"
		names += $.trim($(v).text())+"|"
		
	});
	$("#attachmentsPaths").val(paths);
	$("#fileNames").val(names);
}
