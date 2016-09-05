<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="zh-CN"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang="zh-CN"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="zh-CN"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="zh-CN"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>一品农夫</title>
    <meta name="description" content="一品农夫">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/statics/Home/css/bootstrap.min.css">
    <link rel="stylesheet" href="/statics/Home/css/leaflet.css"/>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/statics/Home/css/leaflet.ie.css"/>
    <![endif]-->
    <link rel="stylesheet" href="/statics/Home/css/main.css">
    <script src="/statics/Home/js/modernizr.min.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/statics/Home/js/html5shiv.js"></script>
    <script src="/statics/Home/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="sticky-header">
    <!-- main content start-->
    <div class="main-content">
        <!--[if lt IE 7]>
        <p class="chromeframe">您的浏览器版本过低，请升级下浏览器版本。</p>
        <![endif]-->
        <!-- Navigation & Logo-->
        <div class="mainmenu-wrapper">
            <div class="menuextras">
                <div class="container clearfix">
                    <div class="pull-left" style="padding-left:200px">嗨，欢迎来到一品农夫！</div>
                    <div class="pull-right">
                        <?php if($_SESSION['farm_admin']['id']!= 0): ?><a href="<?php echo U('User/userCenter');?>">欢迎<?php echo (session('user_name')); ?></a>
                            <?php else: ?>
                            <a href="<?php echo U('Public/login');?>">请登录</a>
                            <a href="<?php echo U('Public/register');?>">免费注册</a><?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="navWrp">
                <div class="container">
                    <a href="<?php echo U('Index/index');?>"><img src="/statics/Home/img/logo.png" alt="一品农夫"></a>
                    <nav id="mainmenu" class="mainmenu pull-right" style="margin-right:5em">
                        <ul>
                            <li class="active">
                                <a href="<?php echo U('Index/index');?>">首页</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Goods/index');?>">单品介绍</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Goods/index',array('goods_type'=>'package'));?>">组合介绍</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Other/privacy');?>">会员福利</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Other/map');?>">来访地图</a>
                            </li>
                            <li>
                                <a href="<?php echo U('Other/connect');?>">联系我们</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- page heading start-->
        


        <!-- page heading end-->
        <!--body wrapper start-->
        <div class="wrapper">
            


        </div>
        <!--body wrapper end-->
        <!--footer section start-->
        <footer>
            <div class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-footer">
                            <h3>友情链接</h3>
                        </div>
                        <div class="col-footer">
                            <ul class="no-list-style footer-navigate-section">
                                <li><a href="page-blog-posts.html">友情链接</a></li>
                                <li><a href="page-portfolio-3-columns-2.html">友情链接</a></li>
                                <li><a href="page-products-3-columns.html">友情链接</a></li>
                            </ul>
                        </div>
                        <div class="col-footer">
                            <ul class="no-list-style footer-navigate-section">
                                <li><a href="page-blog-posts.html">友情链接</a></li>
                                <li><a href="page-portfolio-3-columns-2.html">友情链接</a></li>
                                <li><a href="page-products-3-columns.html">友情链接</a></li>
                            </ul>
                        </div>
                        <div class="col-footer">
                            <ul class="no-list-style footer-navigate-section">
                                <li><a href="page-blog-posts.html">友情链接</a></li>
                                <li><a href="page-portfolio-3-columns-2.html">友情链接</a></li>
                                <li><a href="page-products-3-columns.html">友情链接</a></li>
                            </ul>
                        </div>
                        <div class="col-footer">
                            <ul class="no-list-style footer-navigate-section">
                                <li><a href="page-blog-posts.html">友情链接</a></li>
                                <li><a href="page-portfolio-3-columns-2.html">友情链接</a></li>
                                <li><a href="page-products-3-columns.html">友情链接</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-copyright">
                    <div class="container">
                        &copy; 2016 一品农夫. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
        <!--footer section end-->
    </div>
    <!-- main content end-->
<!--common scripts for all pages-->
<script src="/statics/Home/js/jquery-1.9.1.min.js"></script>
<script src="/statics/Home/js/bootstrap.min.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.5.1/leaflet.js"></script>
<script src="/statics/Home/js/main-menu.js"></script>
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

    }();

    function highlight_subnav(url) {
        console.log(url);
    }

</script>

    <script>
        var str = window.location.href;
        var s = str.split('?')[1];
        console.log(s);
        if(s != '/Public/login.html'){
            fas();
        }
    </script>

</body>
</html>