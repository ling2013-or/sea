<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="zh-CN"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="zh-CN"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="zh-CN"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="zh-CN"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>一品农夫</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/statics/Home/css/bootstrap.min.css">
    <link rel="stylesheet" href="/statics/Home/css/leaflet.css" />
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/statics/Home/css/leaflet.ie.css" />
    <![endif]-->
    <link rel="stylesheet" href="/statics/Home/css/main.css">
    <link rel="stylesheet" type="text/css" href="/statics/Home/css/jquery-picZoomer.css">
    <script src="/statics/Home/js/modernizr.min.js"></script>
    
    <link href="/statics/Home/css/xcConfirm.css" media="all" rel="stylesheet" type="text/css"/>
    <style>
        body {
            background-color: #e7e6e6
        }

        #navWrp {
            border-bottom: 1px solid #d29d3e;
            padding: 20px 0 10px 0;
        }

        #navWrp .container {
            padding: 0 100px
        }

        .piclist {
            margin-top: 15px;
            padding: 0
        }

        .piclist li {
            display: inline-block;
            padding: 0;
            cursor: pointer;
        }

        .piclist li img {
            width: 100%;
            height: auto;
            padding-right: 15px
        }

        .picZoomer {
            padding-right: 15px
        }

        #main {
            background: url(/statics/Home/img/loginBg.jpg) no-repeat top center;
            background-size: cover;
        }

        #loginWrp {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            width: 70%;
            background-color: rgba(255, 255, 255, 0.75);
            margin: 80px auto;
            padding: 30px 10px;
        }

        .form-control {
            height: 50px;
            border: none;
            padding: 0 0 0 10px;
        }

        .loginNote {
            border-left: 1px dashed #ccc;
            margin-left: 30px;
        }

        .loginNote ul {
            padding: 0;
            margin-left: 10px;
            margin-bottom: 0;
        }

        .loginNote li p {
            color: #999;
            margin-bottom: 20px;
        }

        .inputWrp {
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
        }

        .inputWrp span {
            height: 30px;
            line-height: 30px;
            border-right: 1px solid #ccc;
            margin: 10px 0;
            padding: 0;
            text-align: center;
        }
    </style>

    <!--[if lt IE 9]>
    <script src="/statics/Home/js/html5shiv.js"></script>
    <script src="/statics/Home/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">您的浏览器版本过低，请升级下浏览器版本。</p>
<![endif]-->
<div id="main">
    <!-- Navigation & Logo-->
    <div class="mainmenu-wrapper">
        <div class="menuextras">
            <div class="container clearfix">
                <div class="pull-left">嗨，欢迎来到一品农夫！</div>
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
                <a href="<?php echo U('Index/index');?>" style="display:block;text-decoration: none;">
                    <img class="img-circle" src="/statics/Home/img/logo.png" style="width: 60px;">
                    <img src="/statics/Home/img/logoTitle.png" alt="一品农夫销售平台">
                </a>
            </div>
        </div>
    </div>
    <!---main-->
    
    <!---main-->
    <div class="clearfix">
        <div id="loginWrp">
            <div class="formWrp col-md-5">
                <h3 style="font-size:20px;font-weight:bold;text-align:center;margin-bottom:20px">用户登陆</h3>

                <form class="form-horizontal" id="loginFrom">
                    <div class="form-group">
                        <div class="clearfix inputWrp">
                            <span class="col-md-2">头像</span>

                            <div class="col-md-10" style="padding:0">
                                <input type="text" class="form-control" id="userName" name="username" placeholder="用户名"
                                       len="4,20" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="clearfix inputWrp">
                            <span class="col-md-2">密码</span>

                            <div class="col-md-10" style="padding:0">
                                <input type="password" class="form-control" id="inputPassword3" name="password"
                                       placeholder="密码" required len="6,20" lenInfo="长度必须为6~20">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox pull-right">
                            <label>
                                <input type="checkbox">记住密码
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12" style="padding:0">
                            <button type="submit" class="btn btn-primary" id="loginAct" style="width:100%;height:50px">
                                登陆
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="loginNote col-md-7">
                <ul>
                    <li class="clearfix">
                        <h4>如果您还不是会员，请点击“马上注册”。</h4>

                        <p>请创建并登录您的账户，享受更多的服务和了解更多的信息。</p>
                        <button class="btn btn-primary pull-right">马上注册</button>
                    </li>
                    <li class="clearfix">
                        <h4>如果您还忘记了密码，请点击“忘记密码”。</h4>

                        <p>您可以通过点击“忘记密码”按钮，进入相应的页面进行密码找回工作。</p>
                        <button class="btn btn-primary pull-right" id="forget">忘记密码</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
<!-- Footer -->
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
<script src="/statics/Home/js/jquery-1.9.1.min.js"></script>
<script src="/statics/Home/js/bootstrap.min.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.5.1/leaflet.js"></script>
<script src="/statics/Home/js/main-menu.js"></script>
<script>
    //更改title内容
    $('title', parent.document).html('<?php echo ($meta_title); ?>' + ' | 一品农夫');
</script>

    <script src="/statics/Home/js/validate.js"></script>
    <script src="/statics/Home/js/xcConfirm.js"></script>
    <script>
        $(function () {
            $('#loginFrom').formCheck('#loginAct', function (res) {
                if (res) {
                    if ($("#loginAct").hasClass('btn-disabled')) {
                        return;
                    }
                    $('#loginAct').addClass('btn-disabled').html('登录中...');
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('Public/login');?>",
                        data: $('#loginFrom').serialize(),
                        dataType: "json",
                        success: function (data) {

                            if (data.status) {
                                windowInfo(data.info, 'success', '', 1);
                                setTimeout(window.location.href = data.url, 3);
                            } else {
                                windowInfo(data.info, 'info', '', 1);
                                $('#loginAct').removeClass('btn-disabled').html('登录');
                            }
                        },
                        complete: function (data) {
                            $('#loginAct').removeClass('btn-disabled').html('登录');
                        }
                    });

                }
            });
        });
    </script>

</body>
</html>