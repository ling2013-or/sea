<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>农场管理平台</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="/statics/Admin/js/uploadify/uploadify.css">


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/statics/Admin/js/html5shiv.js"></script>
    <script src="/statics/Admin/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="sticky-header">

<section>
    <!-- main content start-->
    <div class="main-content" style="margin-left:0;">

        <!-- page heading start-->
        
    <div class="page-heading">
        <h3>系统</h3>
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo U('Adminuser/index');?>">系统管理员</a>
            </li>
            <li class="active">编辑管理员</li>
        </ul>
    </div>

        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper" style="padding:0 15px">
            
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <form role="form" action="<?php echo U();?>" class="form">
                        <input type="hidden" name="admin_id" value="<?php echo ($info["admin_id"]); ?>">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="name">管理员名称 <span class="check-tips">（不可以修改）</span></label>
                                <input type="text" class="form-control" readonly id="name" value="<?php echo ($info["admin_name"]); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">管理员状态<span class="check-tips">（是否启用管理员帐号）</span></label>
                                <label><input type="radio" name="status" value="1" <?php if($info["status"] == 1): ?>checked="true"<?php endif; ?>> 启用 </label>
                                &nbsp;
                                <label><input type="radio" name="status" value="0" <?php if($info["status"] == 0): ?>checked="true"<?php endif; ?>> 禁用 </label>
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">管理员头像<span class="check-tips"></span></label>
                                <div class="col-md-6">
                                    <img src="/statics/Admin/images/default.jpg" style="width:160px;height:160px;" class="img-thumbnail" id="img_show" alt="用户头像">
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <input type="file" id="upload_file">
                                        <input type="button" class="btn btn-primary btn-sm" id="upload_btn" value=" 上传图片 ">
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="group">所属分组 <span class="check-tips">（管理员所属的用户组）</span></label>
                                <select class="form-control" id="group" name="group_id">
                                    <?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><option value="<?php echo ($list["group_id"]); ?>" <?php if($list['group_id'] == $info['group_id']): ?>selected="selected"<?php endif; ?>><?php echo ($list["group_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="true_name">姓名 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="true_name" id="true_name" value="<?php echo ($info["true_name"]); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="phone">手机号</label>
                                <input type="text" class="form-control" name="phone" id="phone" value="<?php echo ($info["phone"]); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="email">邮箱</label>
                                <input type="text" class="form-control" name="email" id="email" value="<?php echo ($info["email"]); ?>">
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="old_pwd">原密码 <span class="check-tips">（修改密码,请先输入原密码）</span></label>
                                <input type="password" class="form-control" name="old_pwd" id="old_pwd">
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="new_pwd">新密码 <span class="check-tips">（请输入至少6位的密码）</span></label>
                                <input type="password" class="form-control" name="new_pwd" id="new_pwd">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="v_pwd">重复密码</label>
                                <input type="password" class="form-control" name="v_pwd" id="v_pwd">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary ajax-post" target-form="form">确 定</button>
                        <button class="btn a-back">返 回</button>
                    </form>
                </div>
            </section>
        </div>
    </div>

        </div>
        <!--body wrapper end-->

        <!--footer section start-->
        <footer class="text-center">
            2015 &copy; 智慧云联科技（北京）有限公司
        </footer>
        <!--footer section end-->
    </div>
    <!-- main content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="/statics/Admin/js/jquery-1.10.2.min.js"></script>
<script src="/statics/Admin/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/statics/Admin/js/jquery-migrate-1.2.1.min.js"></script>
<script src="/statics/Admin/js/bootstrap.min.js"></script>
<script src="/statics/Admin/js/modernizr.min.js"></script>
<script src="/statics/Admin/js/jquery.nicescroll.js"></script>
<script src="/statics/assets/layer/layer.js" type="text/javascript"></script>
<!--common scripts for all pages-->
<script src="/statics/Admin/js/scripts.js"></script>
<script src="/statics/Admin/js/common.js"></script>
<script>

    +function () {
        //更改title内容
        $('title', parent.document).html('<?php echo ($meta_title); ?>' + ' | 农场管理平台');
        //更改iframe高度
        var $body = $('body');
        var $main_body = $('#main-body', parent.document);

        if ($body.height() < $main_body.height()) {
            $body.height($main_body.height());
        }

//        $('#main-body',parent.document).height($('html').height());
    }();

    function highlight_subnav(url) {

    }

    //窗口重置时改变
    $(window).resize(function () {
        var $body = $('body');
        var $main_body = $('#main-body', parent.document);

        if ($body.height() < $main_body.height()) {
            $body.height($main_body.height());
        }
    });
    $("html").niceScroll({
        styler: "fb",
        cursorcolor: "#65cea7",
        cursorwidth: '6',
        cursorborderradius: '0px',
        background: '#424f63',
        spacebarenabled: false,
        cursorborder: '0',
        zindex: '1000'
    });
</script>


    <!-- <script type="text/javascript" src="/statics/Admin/js/uploadify/jquery.uploadify.js"></script> -->
    <script type="text/javascript">
        //导航高亮
        highlight_subnav("<?php echo U('Adminuser/index');?>");

        /*$(document).ready(function() {
            var id = <?php echo ($info["admin_id"]); ?>;
            $("#upload_file").uploadify({
                'auto':false,
                'swf': '/statics/Admin/js/uploadify/uploadify.swf', 
                'uploader': "<?php echo U('Adminuser/uploadImg');?>", 
                'buttonText':'选择图片',
                'fileObjName':'Filedata',
                'fileSizeLimit':5120,
                'fileTypeExts':'*.jpg;*.png;*.jpeg;*.gif',
                'fileTypeDesc':'请您选择图片格式的文件',
                'formData':{'uid':id},
                'width':68,
                'height':28,
                'multi':false,
                'queueSizeLimit':1,
                'itemTemplate':false,
                'onUploadSuccess':function(file, data, response){
                    var res = $.parseJSON(data);
                    console.log(res);
                    if (res.status) {
                        $('#upload_url').val(res.info);
                        $('#img_show').attr('src',res.info);
                    } else {
                        alert(res.info);
                    }
                    
                }
            });
            $('#upload_btn').click(function(){
                $('#upload_file').uploadify('upload','*');
            });
        });*/
    </script>


</body>
</html>