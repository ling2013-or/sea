$(document).ready(function() {
    $("#upload_file").uploadify({
        'auto':false,
        'swf': './uploadify.swf', 
        'uploader': '/upload.php', 
        'buttonText':'选择图片',
        'fileObjName':'Filedata',
        'fileSizeLimit':5120,
        'fileTypeExts':'*.jpg;*.png;*.jpeg;*.gif',
        'fileTypeDesc':'请您选择图片格式',
        'formData':{'uid':1},
        'width':80,
        'height':30,
        'multi':false,
        'buttonClass':'btn btn-default btn-sm',
        'queueSizeLimit':1
    });
    $('#upload_btn').addClass('btn btn-primary btn-sm').click(function(){
        $('#upload_file').uploadify('upload','*');
    });
});