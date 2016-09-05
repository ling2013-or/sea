<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>一品农夫</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    

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
        <h3>会员</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">会员管理</a>
            </li>
            <li class="active">编辑会员</li>
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
                                <label class="item-label">账号 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="user_name" value="<?php echo ($info["user_name"]); ?>" placeholder="账号">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">用户手机号 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="user_phone" value="<?php echo ($info["user_phone"]); ?>" placeholder="用户手机号">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">昵称 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="nick_name" value="<?php echo ($info["nick_name"]); ?>" placeholder="昵称">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">身份证号 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="id_card" value="<?php echo ($info["id_card"]); ?>" placeholder="身份证号">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">用户密码 <span class="check-tips">不填表示不修改</span></label>
                                <input type="text" class="form-control" name="user_pass" placeholder="用户密码">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">用户邮箱 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="user_email" value="<?php echo ($info["user_email"]); ?>" placeholder="用户邮箱">
                            </div>
                        </div>

                        <input type="hidden" name="uid" value="<?php echo ($info["uid"]); ?>">
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


    <script type="text/javascript">
        //导航高亮
        highlight_subnav("<?php echo U('index');?>");
    </script>


</body>
</html>