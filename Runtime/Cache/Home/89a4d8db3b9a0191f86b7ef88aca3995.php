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
            width: 70%;
            background-color: rgba(255, 255, 255, 0.75);
            margin: 80px auto;
            padding: 30px 10px;
        }

        .red {
            color: red
        }

        label.control-label {
            color: #565656;
            padding-right: 0;
            font-weight: normal;
        }

        .gray {
            color: #9c9c9c
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
                <a href="index.html" style="display:block;text-decoration: none;">
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
            <div class="formWrp">
                <h3 style="font-size:20px;font-weight:bold;text-align:left;margin-bottom:20px">用户注册</h3>

                <form class="form-horizontal" id="registerForm">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">用户名：</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="支持中文，数字，字母，4-20位" len="4,20" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputMobile" class="col-sm-4 control-label">手机号：</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="inputMobile" name="mobile"
                                   placeholder="请输入11位手机号" regC="mobile" required regcInfo="请输入合法手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputCheckcode" class="col-sm-4 control-label">验证码：</label>

                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="inputCheckcode" name="code"
                                   placeholder="" required>
                        </div>
                        <div class="col-sm-2">
                            <img src="<?php echo U('Public/vcode');?>" id="vcodeImg"  class="form-control-static">
                        </div>
                        <!--<div class="col-sm-2 form-control-static">看不清，<span style="color:#2990d8;cursor: pointer">换一张？</span></div>-->
                    </div>
                    <!--<div class="form-group">-->
                    <!--<label for="inputMescode" class="col-sm-4 control-label">校验码：</label>-->
                    <!--<div class="col-sm-2">-->
                    <!--<input type="text" class="form-control" id="inputMescode" name="inputMescode" placeholder="" required>-->
                    <!--</div>-->
                    <!--<div class="col-sm-4">-->
                    <!--<button class="btn btn-default">获取短信验证码</button>-->
                    <!--</div>-->
                    <!--</div>-->
                    <div class="form-group">
                        <label for="password" class="col-sm-4 control-label">密码：</label>

                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="password" name="password" placeholder="6~20位数字"
                                   required len="6,20" lenInfo="长度必须为6~20">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="surePassword" class="col-sm-4 control-label">确认密码：</label>

                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="surePassword" name="surePassword"
                                   placeholder="输入与上次相同的密码" required len="6,20" lenInfo="长度必须为6~20" compare="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rule" class="col-sm-4 col-sm-offset-4 control-label" style="text-align:center">
                            <input type="checkbox" id="rule" name="rule" style="margin-right:6px">我已看过并同意 <span
                                style="color:#2990d8">《网络服务协议》</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4" style="margin-top: 20px;">
                            <button class="btn btn-warning col-sm-5" style="height: 50px;font-size: 18px;"
                                    id="registerAct">立即注册
                            </button>
                        </div>
                    </div>

                </form>
            </div>
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
            var $vcodeImg = $("#vcodeImg");
            var verifyimg = $vcodeImg.attr("src");
            $vcodeImg.click(function(){
                if( verifyimg.indexOf('?')>0){
                    $vcodeImg.attr("src", verifyimg+'&random='+Math.random());
                }else{
                    $vcodeImg.attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
                }
            });
            $('#registerForm').formCheck('#registerAct', function (res) {
                if (res) {
                    if (!$('#rule').is(":checked")) {
//                        console.log(123);
                        windowInfo('请先同意网络服务协议', 'info', '', 1);
                        return;
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo U('Public/register');?>",
                            data: $('#registerForm').serialize(),
                            dataType: "json",
                            success: function (data) {
                                if(data.status){
                                    windowInfo('注册成功', 'success', '', 1);
                                    setTimeout(window.location.href = data.url,3);
                                }else{
                                    windowInfo(data.info, 'info', '', 1);
                                }
                                $vcodeImg.attr("src", verifyimg+'&random='+Math.random());

                            },
                            complete:function(data){

                            }
                        });

                    }
                }
            });
        });
    </script>

</body>
</html>