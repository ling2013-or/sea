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
    
    <link href="/statics/Home/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="/statics/Home/css/xcConfirm.css" media="all" rel="stylesheet" type="text/css"/>
    <style>
        body {
            background-color: #e7e6e6
        }

        #main {
            background: url(/statics/Home/img/loginBg.jpg) no-repeat top center;
            background-size: cover;
            color: #606060
        }

        #navWrp {
            background: #fff;
            height: 8em;
            line-height: 8em
        }

        #navWrp .container {
            padding: 0 100px
        }

        .file-preview-frame {
            float: none;
            margin: 0 auto
        }

        .gray {
            color: #c7c7c7
        }

        .red {
            color: #e11414
        }

        #perWrp {
            width: 80%;
            margin: 0 auto;
            background-color: #f6f6f6;
            border: 1px solid #dfdfdf;
            border-radius: 8px;
        }

        .avartar {
            position: relative;
            background-color: white;
            padding: 15px;
            margin: 15px;
        }

        aside .title {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            background-color: rgba(0, 0, 0, .5);
            height: 30px;
            line-height: 30px;
            color: white;
            cursor: pointer;
        }

        aside h3 {
            text-align: left;
            font-weight: bold;
            margin-bottom: 15px;
            margin-left: 15px
        }

        .list-group-item {
            padding: 10px 0;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
        }

        .list-group-item:hover, #perList .active {
            background-color: #e8e8e8
        }

        .list-group-item span {
            padding-left: 15px;
            display: block;
        }

        .navLi {
            border-bottom: 1px solid #dfdfdf;
        }

        .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
            color: #b88939;
            background: none;
            cursor: pointer;
        }

        .nav > li > a {
            color: #606060;
            cursor: pointer;
        }

        .personUnit {
            min-height: 550px;
            background-color: white;
            border-bottom: 1px solid #dfdfdf;
        }

        .personUnit .liCnt {
            padding: 40px 15px
        }

        .absCenter {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translate(-50%, 0)
        }

        .table-bordered thead {
            background-color: #dbdbdb
        }

        .table-bordered th {
            font-weight: normal
        }

        .handle span {
            color: #20a3dd;
            padding: 0 5px;
        }

        .modify {
            border-right: 1px solid #20a3dd;
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
    
<div id="main">
<div style="background-color:rgba(255,255,255,.8);padding-bottom: 100px;">
<div class="clearfix" id="perWrp">
<aside class="col-md-3" style="border-right: 1px solid #dfdfdf;padding:0;min-height: 600px;">
    <div class="avartar">
        <div style="position:relative">
            <img src="/statics/Home/img/avatar.jpg" alt="头像" style="width:100%">
            <span class="title">编辑头像</span>
        </div>
    </div>
    <h3>帐户管理</h3>
    <ul class="list-group" id="perList">
        <li class="list-group-item active"><span>个人信息</span></li>
        <li class="list-group-item"><span>收货地址</span></li>
        <li class="list-group-item"><span>评价信息</span></li>
        <li class="list-group-item"><span>修改密码</span></li>
        <li class="list-group-item"><span>养殖规划</span></li>
        <li class="list-group-item"><span>会员中心</span></li>
    </ul>
</aside>
<section class="col-md-9" id="perSection" style="padding:0;min-height:600px;position:relative">
<div class="personUnit active" id="personInfo">
    <ul class="nav nav-pills navLi" role="tablist">
        <li role="presentation" class="active"><a>基本信息</a></li>
        <li role="presentation"><a>头像照片</a></li>
    </ul>
    <div class="liCnt" id="base">
        <div class="personWrp">
            <dl class="dl-horizontal">
                <dt>用户名：</dt>
                <dd><?php echo ($info["user_name"]); ?></dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>姓名：</dt>
                <dd><?php echo ($info["real_name"]); ?></dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>邮箱：</dt>
                <dd><?php echo ($info["user_email"]); ?></dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>手机号：</dt>
                <dd><?php echo ($info["user_phone"]); ?></dd>
            </dl>

            <dl class="dl-horizontal">
                <dt>地区：</dt>
                <dd><?php echo ($info["address"]); ?></dd>
            </dl>
        </div>
        <div class="personWrp" style="display: none">
            <form enctype="multipart/form-data">
                <input id="file-0" class="file" type="file" data-min-file-count="1">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-default">Reset</button>
            </form>
            <p class="gray">仅支持JPG、GIF、PNG图片文件，且文件小于5M</p>
        </div>
    </div>
    <button class="btn btn-primary absCenter">修改</button>
</div>
<div class="personUnit" id="personAddr" style="display:none">
    <ul class="nav nav-pills navLi" role="tablist">
        <li role="presentation" class="active"><a>新增收货地址</a></li>
    </ul>
    <div class="liCnt">
        <form action="" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">详细地址：</label>

                <div class="col-sm-7">
                    <textarea name="" id="address" rows="3" class="form-control"></textarea>
                </div>
                <p class="gray form-control-static">请输入详细的收货地址</p>
            </div>
            <div class="form-group">
                <label for="postalcode" class="col-sm-2 control-label">邮政编码：</label>

                <div class="col-sm-4">
                    <input type="text" class="form-control" id="postalcode" placeholder="">
                </div>
                <p class="gray form-control-static">请输入您的邮政编码</p>
            </div>
            <div class="form-group">
                <label for="receiptName" class="col-sm-2 control-label">收货姓名：</label>

                <div class="col-sm-4">
                    <input type="text" class="form-control" id="receiptName" placeholder="">
                </div>
                <p class="gray form-control-static">请输入您的收货姓名</p>
            </div>
            <div class="form-group">
                <label for="inputMobile" class="col-sm-2 control-label">手机号：</label>

                <div class="col-sm-4">
                    <input type="text" class="form-control" id="inputMobile" placeholder="">
                </div>
                <p class="gray form-control-static">请输入您的手机号</p>
            </div>
            <div class="form-group">
                <button class="btn btn-primary col-sm-offset-2 col-sm-2" style="margin-top: 10px;">保存</button>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
            <th>姓名</th>
            <th>地址</th>
            <th>电话</th>
            <th>默认</th>
            <th>操作</th>
            </thead>
            <tbody>
            <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td><?php echo ($list["consignee"]); ?></td>
                    <td><?php echo ($list["address"]); ?></td>
                    <td><?php echo ($list["phone"]); ?></td>
                    <td><?php echo (get_default_msg($list["is_default"])); ?></td>
                    <td class="handle"><span class="modify" aid="<?php echo ($list["id"]); ?>">修改</span><span class="del" aid="<?php echo ($list["id"]); ?>">删除</span>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="personUnit" id="cmtDetail" style="display:none">
    <ul class="nav nav-pills navLi" role="tablist">
        <li role="presentation" class="active"><a>评价信息</a></li>
    </ul>
    <div class="liCnt">
        <table class="table table-bordered">
            <thead>
            <th>商家</th>
            <th>商品</th>
            <th>评分</th>
            <th>评价</th>
            <th>回复</th>
            <th>操作</th>
            </thead>
            <tbody>
            <tr>
                <td>占三</td>
                <td>有机嗨森</td>
                <td>10分</td>
                <td>生态美味</td>
                <td>万分感谢</td>
                <td class="handle"><span class="del">删除</span></td>
            </tr>
            <tr>
                <td>占三</td>
                <td>有机嗨森</td>
                <td>10分</td>
                <td>生态美味</td>
                <td>万分感谢</td>
                <td class="handle"><span class="del">删除</span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="personUnit" id="modifyPwd" style="display:none">
    <ul class="nav nav-pills navLi" role="tablist">
        <li role="presentation" class="active"><a>修改密码</a></li>
    </ul>
    <div class="liCnt">
        <form class="form-horizontal" id="updatePwd">
            <div class="form-group">
                <label for="oldPwd" class="col-sm-2 control-label">旧密码：</label>

                <div class="col-sm-4">
                    <input type="text" class="form-control" id="oldPwd" name="old" placeholder="" required len="6,20"
                           lenInfo="长度必须为6~20">
                </div>
                <p class="gray form-control-static">请输入旧密码</p>
            </div>
            <div class="form-group">
                <label for="newPwd" class="col-sm-2 control-label">新密码：</label>

                <div class="col-sm-4">
                    <input type="text" class="form-control" id="newPwd" name="password" placeholder="" required
                           len="6,20" lenInfo="长度必须为6~20">
                </div>
                <p class="gray form-control-static">请输入新密码</p>
            </div>
            <div class="form-group">
                <label for="surePwd" class="col-sm-2 control-label">确认密码：</label>

                <div class="col-sm-4">
                    <input type="text" class="form-control" id="surePwd" name="password_repeat" placeholder="" required
                           len="6,20" lenInfo="长度必须为6~20" compare="newPwd">
                </div>
                <p class="gray form-control-static">请确认新密码</p>
            </div>
            <div class="form-group">
                <div style="width: 200px;margin-top: 10px;position: absolute;bottom: 6px;left: 50%;transform: translate(-50%,0);">
                    <button class="btn btn-primary" id="updateAct">确认修改</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="personUnit" id="plan" style="display:none">
    <ul class="nav nav-pills navLi" role="tablist">
        <li role="presentation" class="active"><a>养殖规划</a></li>
    </ul>
    <div class="liCnt">
        <img src="/statics/Home/img/slider1.jpg" alt="" style="width:100%">
    </div>
</div>
<div class="personUnit" id="privilege" style="display:none">
    <ul class="nav nav-pills navLi" role="tablist">
        <li role="presentation" class="active"><a>会员中心</a></li>
    </ul>
    <div class="liCnt">
        <dl class="dl-horizontal">
            <dt>会员等级：</dt>
            <dd>黄金会员</dd>
        </dl>
        <dl class="dl-horizontal">
            <dt>会员积分：</dt>
            <dd><span class="red">8888</span></dd>
        </dl>
    </div>
</div>
</section>
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

    <script src="/statics/Home/js/fileinput.js" type="text/javascript"></script>
    <script src="/statics/Home/js/fileinput_locale_zh.js" type="text/javascript"></script>
    <script src="/statics/Home/js/validate.js"></script>
    <script src="/statics/Home/js/xcConfirm.js"></script>
    <script>
        $(function () {
            //tab切换
            $(".navLi li").click(function () {
                $(this).addClass("active").siblings().removeClass("active");
                $("#base>.personWrp").eq($(this).index()).show().siblings().hide();
            });

            //菜单切换
            $('#perList li').click(function () {
                $(this).addClass("active").siblings().removeClass("active");
                $("#perSection>.personUnit").eq($(this).index()).show().siblings().hide();
            });


            $("#file-0").fileinput({
                uploadUrl: '#',
                language: 'zh', //设置语言
                allowedFileExtensions: ['jpg', 'png', 'gif'],
                overwriteInitial: true,
                maxFileSize: 5000,
                maxFilesNum: 1,
                showUpload: false, //是否显示上传按钮
                showCaption: false,//是否显示标题
            });

            var $vcodeImg = $("#vcodeImg");
            var verifyimg = $vcodeImg.attr("src");
            $vcodeImg.click(function () {
                if (verifyimg.indexOf('?') > 0) {
                    $vcodeImg.attr("src", verifyimg + '&random=' + Math.random());
                } else {
                    $vcodeImg.attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
                }
            });
            $('#updatePwd').formCheck('#updateAct', function (res) {
                if (res) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('Public/updatePwd');?>",
                        data: $('#updatePwd').serialize(),
                        dataType: "json",
                        success: function (data) {
                            if (data.status) {
                                windowInfo(data.info, 'success', '', 1);
                                if(data.url != 'undefined'){
                                    setTimeout(window.location.href = data.url, 3);
                                }
                            } else {
                                windowInfo(data.info, 'info', '', 1);
                            }

                        },
                        complete: function (data) {

                        }
                    });


                }
            });
        });

    </script>

</body>
</html>