<script type="text/javascript" src="/js/uploadossjs/plupload.full.min.js"></script>
<script type="text/javascript" src="/js/md5.js"></script>


<script type="text/javascript">
$(document).ready(function (){

//文件后缀
function get_suffix(filename) {
    var pos = filename.lastIndexOf('.')
    var suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}

for (var i = $('.selectbtnbody').length - 1; i >= 0; i--) {
	var idname = 'selectbtnbody'+i;
    var btnidname = 'selectbtn'+i;
	var option = {
		runtimes : 'html5,flash,silverlight,html4',
		browse_button : btnidname, 
	    multi_selection: false,
		container: idname,
		flash_swf_url : '/uploadossjs/Moxie.swf',
		silverlight_xap_url : '/uploadossjs/Moxie.xap',
	    url : 'http://oss.aliyuncs.com',

	    filters: {
	        mime_types : [ //只允许上传图片和zip,rar文件
	        	{ title : "Image files", extensions : "jpg,gif,png,bmp" }, 
	        ],
	        max_file_size : '10mb', //最大只能上传10mb的文件
	        prevent_duplicates : true //不允许选取重复文件
	    },

		init: {
			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					var filename = file.name;
					if (filename != '') {
				        var newname = '{{$ossconfig["dir"]}}'+hex_md5(filename)+get_suffix(filename);
				        var new_multipart_params = {
					        'key' : newname,
					        'policy': '{{$ossconfig["policy"]}}',
					        'OSSAccessKeyId':'{{$ossconfig["accessid"]}}', 
					        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
					        //'callback' : callbackbody,
					        'signature':'{{$ossconfig["signature"]}}',
					    };

					    up.setOption({
					        'url': '{{$ossconfig["host"]}}',
					        'multipart_params': new_multipart_params
					    });

					    up.start();
				    }
				});
			},

			BeforeUpload: function(up, file) {

	        },

			UploadProgress: function(up, file) {
				sendError('已上传'+file.percent+'%');
			},

			FileUploaded: function(up, file, info) {
	            if (info.status == 200){
	            	var imgurl = '{{$ossconfig["host"].'/'.$ossconfig["dir"]}}'+hex_md5(file.name)+get_suffix(file.name) ;
	                var op = up.getOption();
					$('#'+op.container).find('.uploadimg').attr('src',imgurl);
					$('#'+op.container).find('.uploadinput').val(imgurl);
	            }else{
	            	sendError('上传失败');
	            }
			},

			Error: function(up, err) {
				//sendError(err.code);
				//sendError(err.response);
			}
		}
	};

	uploader = 'uploader'+i; 
    uploader =  new plupload.Uploader(option);
    uploader.init();
} 


});

</script>