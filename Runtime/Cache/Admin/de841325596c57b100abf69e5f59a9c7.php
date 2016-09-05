<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>一品农夫</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="/statics/Admin/js/uploadify/uploadify.css">
    <style>
        #img_show span, #img_show_multi span {
            position: relative;
            display: inline-block;
        }

        #img_show img, #img_show_multi img {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        #img_show a, #img_show_multi a {
            position: absolute;
            top: 5px;
            right: 5px;
            margin-right: 5px;
        }
    </style>


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
        <h3>商城</h3>
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo U('index');?>">产品管理</a>
            </li>
            <li class="active">添加产品</li>
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

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="name">商品名称 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="name" id="name"
                                       placeholder="新增商品名称">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">商品状态 <span class="check-tips"></span></label>
                                <label><input type="radio" name="status" value="0" checked> 未上架</label>&nbsp;
                                <label><input type="radio" name="status" value="1"> 已上架</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="price">商品单价 <span class="check-tips">（单位：元/份）</span></label>
                                <input type="text" class="form-control" name="price" id="price"
                                       placeholder="价格">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="mark_price">商品市场价格 <span class="check-tips">（单位：元）</span></label>
                                <input type="text" class="form-control" name="mark_price" id="mark_price"
                                       placeholder="价格">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="sales">销量 <span class="check-tips">（单位：份）</span></label>
                                <input type="text" class="form-control" name="sales" id="sales"
                                       placeholder="销量">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="title">商品简介</label>
                                <textarea class="form-control" name="title" rows="7" id="title"
                                          placeholder="商品描述"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        商品封面图片<span class="small">（仅需要上传一张图片）</span>
                                    </div>
                                    <div class="panel-footer" id="img_show"></div>
                                    <div class="panel-footer">
                                        <p class="small text-warning">
                                            1、图片格式为jpeg、jpg、gif、png
                                            <br>
                                            2、图片大小不要超过 5M
                                        </p>
                                        <input type="file" id="upload_file">
                                        <input type="button" class="btn btn-primary btn-sm" id="upload_btn"
                                               value=" 上传图片 ">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        商品展示图片<span class="small">（可以上传多张图片）</span>
                                    </div>
                                    <div class="panel-footer" id="img_show_multi"></div>
                                    <div class="panel-footer">
                                        <p class="small text-warning">
                                            1、图片格式为jpeg、jpg、gif、png
                                            <br>
                                            2、图片大小不要超过 5M
                                        </p>
                                        <input type="file" id="upload_file_multi">
                                        <input type="button" class="btn btn-primary btn-sm" id="upload_btn_multi"
                                               value=" 上传图片 ">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        商品详情
                                    </div>
                                    <div class="panel-footer">
                                        <script id="ueditor" style="width:100%;height:400px;"
                                                type="text/plain"></script>
                                    </div>

                                </div>
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
            2015 &copy; 一品农夫(河北)有限公司
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
        $('title', parent.document).html('<?php echo ($meta_title); ?>' + ' | 一品农夫');
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


    <script type="text/javascript" src="/statics/Admin/js/uploadify/jquery.uploadify.js"></script>
    <script type="text/javascript" charset="utf-8" src="/statics/Admin/js/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/statics/Admin/js/ueditor/ueditor.all.min.js"></script>

    <script type="text/javascript">
        //导航高亮
        highlight_subnav("<?php echo U('Adminuser/index');?>");

        //ueditor编辑器
        var ue = UE.getEditor('ueditor', {
            textarea: 'description'
        });

        //uploadify配置
        //单图上传
        $("#upload_file").uploadify({
            'auto': false,
            'swf': '/statics/Admin/js/uploadify/uploadify.swf',
            'uploader': "<?php echo U('uploadImg',array('path'=>'goods'));?>",
            'buttonText': '选择图片',
            'fileObjName': 'Filedata',
            'fileSizeLimit': 5120,
            'fileTypeExts': '*.jpg;*.png;*.jpeg;*.gif',
            'fileTypeDesc': '请您选择图片',
            //'formData':{'uid':id},
            'width': 68,
            'height': 28,
            'multi': false,
            'queueSizeLimit': 1,
            'itemTemplate': false,
            'onUploadSuccess': function (file, data) {
                var res = $.parseJSON(data);
                if (res.status) {
                    $('#seed_demo').remove();
                    var cont = '<span><img src="' + res.info + '" style="width:160px;height:160px;" class="img-thumbnail" alt="商品封面图片">';
                    cont += '<input type="hidden" name="picture" value="' + res.info + '">';
                    cont += '<a href="javascript:void(0)" class="btn btn-xs btn-primary"> 删除 </a></span>';
                    $('#img_show').append(cont);
                    $('#img_show a').click(function () {
                        $(this).parent().remove();
                    })
                } else {
                    layer.msg(res.info);
                }
            }
        });
        $('#upload_btn').click(function () {
            $('#upload_file').uploadify('upload', '*');
        });

        //多图上传
        $("#upload_file_multi").uploadify({
            'auto': false,
            'swf': '/statics/Admin/js/uploadify/uploadify.swf',
            'uploader': "<?php echo U('uploadImg',array('path'=>'goods'));?>",
            'buttonText': '选择图片',
            'fileObjName': 'Filedata',
            'fileSizeLimit': 5120,
            'fileTypeExts': '*.jpg;*.png;*.jpeg;*.gif',
            'fileTypeDesc': '请您选择图片',
            //'formData':{'uid':id},
            'width': 68,
            'height': 28,
            'multi': true,
            //'queueSizeLimit':1,
            'itemTemplate': false,
            'onUploadSuccess': function (file, data) {
                var res = $.parseJSON(data);
                if (res.status) {
                    $('#seed_demo').remove();
                    var cont = '<span><img src="' + res.info + '" style="width:160px;height:160px;" class="img-thumbnail" alt="商品展示图片">';
                    cont += '<input type="hidden" name="picture_more[]" value="' + res.info + '">';
                    cont += '<a href="javascript:void(0)" class="btn btn-xs btn-primary"> 删除 </a></span>';
                    $('#img_show_multi').append(cont);
                    $('#img_show_multi a').click(function () {
                        $(this).parent().remove();
                    })
                } else {
                    layer.msg(res.info);
                }
            }
        });
        $('#upload_btn_multi').click(function () {
            $('#upload_file_multi').uploadify('upload', '*');
        });
    </script>


</body>
</html>