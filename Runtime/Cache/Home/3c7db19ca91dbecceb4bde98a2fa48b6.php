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
<link rel="stylesheet" type="text/css" href="/statics/Home/css/jquery-picZoomer.css">
<style>
    body {
        background-color: #e7e6e6
    }

    #navWrp {
        background: #fff;
        height: 8em;
        line-height: 8em
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

    #orderDetail {
        background-color: #fff;
        border-top: 1px solid #d29d3e;
        padding-top: 30px
    }

    #orderDetail h3.productTitle {
        height: 2em;
        font-size: 1.6em;
        margin-top: 10px;
        font-weight: 700;
    }

    .picZoomer-pic-wp {
        max-height: 350px;
        border: 1px solid #efefef;
        padding: 5px;
        text-align: center;
    }

    #detailWrp h3 {
        height: 3em;
        line-height: 3em;
        color: #1d1d1d;
        font-size: 1.5em;
    }

    .form-horizontal .control-label {
        text-align: left
    }

    .productInfo {
        height: 90px;
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
        font-size: 1.4em;
        background: rgb(255, 240, 232); /* Old browsers */
        background: -moz-linear-gradient(-65deg, rgba(255, 240, 232, 1) -1%, rgba(255, 240, 232, 1) 50%, rgba(255, 255, 255, 1) 51%, rgba(254, 87, 16, 1) 51%, rgba(254, 87, 16, 1) 100%); /* FF3.6-15 */
        background: -webkit-linear-gradient(-65deg, rgba(255, 240, 232, 1) -1%, rgba(255, 240, 232, 1) 50%, rgba(255, 255, 255, 1) 51%, rgba(254, 87, 16, 1) 51%, rgba(254, 87, 16, 1) 100%); /* Chrome10-25,Safari5.1-6 */
        background: linear-gradient(115deg, rgba(255, 240, 232, 1) -1%, rgba(255, 240, 232, 1) 50%, rgba(255, 255, 255, 1) 51%, rgba(254, 87, 16, 1) 51%, rgba(254, 87, 16, 1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff0e8', endColorstr='#fe5710', GradientType=1); /* IE6-9 fallback on horizontal gradient */
    }

    #price span {
        color: #fe5710
    }

    #express span {
        display: block;
        color: white;
        text-align: center;
    }

    .orange {
        color: #fe835e
    }

    label {
        font-weight: normal
    }

    .padWrp span {
        padding-right: 1.1em
    }

    #buyAct {
        font-size: 1.5em;
        padding: .5em 0;
        background-color: #c40000;
        color: white;
        margin-top: 1em
    }

    .glyphicon-ok-circle:before {
        vertical-align: middle
    }

    .nav-pills {
        display: flex;
        justify-content: center;
        margin-top: 2em;
        margin-bottom: 2em;
    }

    .nav-pills > li {
        float: none
    }

    .nav-pills li {
        width: 30%;
        text-align: center;
        background-color: #858585;
        color: white;
        cursor: pointer;
    }

    /*.nav-pills li:focus,.nav-pills li:active{*/
    /*background-color: #858585;*/
    /*}*/
    .nav > li > a {
        padding: 0;
        height: 2.4em;
        line-height: 2.4em;
        font-size: 1.2em;
    }

    .nav li.col-md-4 {
        padding: 0
    }

    .nav > li > a, .nav > li > a:hover, .nav > li > a:active {
        color: white
    }

    .nav-pills > li > a, .nav-pills > li > a:hover, .nav-pills > li > a:active {
        border-radius: 0
    }

    .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus, .nav-pills > li > a:hover {
        background-color: #3c3c3c
    }

    .table-striped > tbody > tr:nth-child(odd) > td, .table-striped > tbody > tr:nth-child(odd) > th {
        background-color: #fff
    }

    .table-striped > tbody > tr:nth-child(even) > td, .table-striped > tbody > tr:nth-child(even) > th {
        background-color: #f9f9f9
    }

    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
        border-top: none
    }

    #prodcutShow {
        border-left: 7px solid #b88939;
        color: #b88939;
        padding-left: 10px;
        height: 20px;
        line-height: 20px;
    }

    .cmtLi {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cmtLi .avatarDt {
        width: 80%;
        text-align: center;
    }

    .avatarImg {
        width: 80px;
        height: 80px;
        border-radius: 80px;
        background-color: #b88939;
        padding: 2px;
        margin: 10px auto;
    }

    .cmtTitle {
        height: 40px;
        line-height: 40px;
        border-bottom: 1px dashed #aaa;
    }

    .cmtAct span {
        border-bottom: 1px solid #535b60;
        margin-left: 10px;
        cursor: pointer
    }

    .titleWrp {
        position: relative;
        clear: both;
    }

    #planWrp .planWrp:nth-child(even) .descTitle {
        left: -webkit-calc(50% - 280px);
        text-align: left;
        padding-left: 20px;
    }

    #planWrp .planWrp:nth-child(even) .desc {
        float: left;
        width: 40%;
    }

    #planWrp .planWrp:nth-child(odd) .desc {
        float: right;
        width: 40%;
    }

    .circle {
        width: 150px;
        height: 150px;
        border-radius: 150px;
        padding: 10px 0;
        box-shadow: 0 0 50px #aaa;
        text-align: center;
        margin: 0 auto;
    }

    .circle span {
        display: inline-block;
        width: 130px;
        height: 130px;
        border-radius: 130px;
        background-color: #9d3b57;
        line-height: 130px;
        color: white;
        font-size: 80px;
    }

    .descTitle {
        position: absolute;
        top: 45px;
        left: -webkit-calc(50% + 100px);
        height: 60px;
        line-height: 20px;
        background: url(/statics/Home/img/titleBg.png) no-repeat center 0;
        background-size: contain;
        width: 200px;
        text-align: right;
        padding-right: 20px;
        font-size: 16px;
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
    
    <div id="orderDetail">
        <div class="container" style="padding:0 100px">
            <h3 class="productTitle"><?php echo ($info["name"]); ?></h3>

            <div class="clearfix" style="padding:0">
                <div class="col-md-6" style="padding:0">
                    <div class="picZoomer">
                        <img src="<?php echo ($info["picture"]); ?>" alt="">
                    </div>
                    <ul class="piclist clearfix">

                        <?php if(is_array($info["picture_more"])): $i = 0; $__LIST__ = $info["picture_more"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$more): $mod = ($i % 2 );++$i;?><li class="col-md-4"><img src="<?php echo ($more); ?>" alt=""></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
                <div class="col-md-5" style="float:right" id="detailWrp">
                    <div class="productInfo">
                        <p id="price">￥<span><?php echo ($info["price"]); ?></span></p>

                        <p id="express">
                            <span>快递</span>
                            <span style="padding-top:.5em;font-size:1.2em;">秦皇岛</span>
                        </p>
                    </div>
                    <h3>
                        <?php echo ($info["name"]); ?>
                    </h3>

                    <form class="form-horizontal" id="shopAddFrom" style="font-size: 1.1em;color: #777;">
                        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>"/>
                        <input type="hidden" name="type" value="<?php echo ($info["goods_type"]); ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">供应商：</label>

                            <div class="col-sm-6" style="padding-top:7px;">
                                <span class="orange" style="text-decoration:underline">河北擎远生物科技</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="variety" class="col-sm-3 control-label">数　量：</label>

                            <div class="col-sm-9">
                                <input type="number" class="form-control col-sm-3" id="variety" name="variety"
                                       value="1" regC="integer2" required regcInfo="请输入正整数"/>
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 padWrp">
                                <span>销　量：<b class="orange"><?php echo ($info["sales"]); ?></b></span>
                                <span>浏览次数：<b class="orange">
                                    <?php if($info["browse"] > 0): echo ($info["browse"]); ?>
                                        <?php else: ?>
                                        0<?php endif; ?>
                                </b></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 padWrp">
                                <span>服务承诺</span>
                                <span>正品保证</span>
                                <span>极速退款</span>
                                <span>七天无理由退换</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-10">
                                <button class="btn btn-default col-sm-6" id="buyAct">
                                    确定下单
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="nav nav-pills" id="navLi" role="tablist">
                    <li role="presentation" class="active"><a>产品详情</a></li>
                    <li role="presentation"><a>产品评价</a></li>
                    <li role="presentation"><a>养殖计划</a></li>
                </ul>
                <div class="tab-content" style="margin-top:15px">
                    <div role="tabpanel" class="tab-pane active" id="detail">
                        <div style="text-align: center;" class="embed-responsive embed-responsive-4by3">
                            <iframe src="/statics/Home/img/testvedio.mp4" allowfullscreen="" class="embed-responsive-item"
                                    frameborder="0" style="width:100%"></iframe>
                        </div>
                        <?php echo ($info["description"]); ?>

                    </div>
                    <div role="tabpanel" class="tab-pane " id="comment">
                        <?php if(is_array($info['statistics']['lists'])): $i = 0; $__LIST__ = $info['statistics']['lists'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$static): $mod = ($i % 2 );++$i;?><div class="cmtLi">
                                <div class="avatarDt">
                                    <div class="avatarImg"></div>
                                    <p><?php echo ($static["user_name"]); ?></p>

                                    <p>订单号：<span><?php echo ($static["order_sn"]); ?></span></p>
                                </div>
                                <div class="cmtWrp">
                                    <div class="cmtTitle clearfix">
                                        <div class="time pull-left"><?php echo (date('Y-m-d', $static["comment_time"])); ?></div>
                                        <!--<div class="cmtAct pull-right">-->
                                        <!--<span>点赞</span>-->
                                        <!--<span>回复</span>-->
                                        <!--</div>-->
                                    </div>
                                    <p style="margin-top:10px">
                                        <?php echo ($static["comment"]); ?>
                                    </p>
                                </div>
                            </div><?php endforeach; endif; else: echo "" ;endif; ?>


                    </div>
                    <div role="tabpanel" class="tab-pane " id="plan">

                        <div id="planWrp">
                            <?php if(is_array($info['plans'])): $cc = 0; $__LIST__ = $info['plans'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$plan): $mod = ($cc % 2 );++$cc;?><div class="planWrp">
                                    <div class="oneTitle titleWrp">
                                        <div class="circle">
                                            <span><?php echo ($cc); ?></span>
                                        </div>
                                        <div class="descTitle">
                                            <?php echo ($plan["title"]); ?>
                                        </div>
                                    </div>
                                    <div class="desc">
                                        <?php echo ($plan["content"]); ?>
                                    </div>
                                </div><?php endforeach; endif; else: echo "" ;endif; ?>

                        </div>
                    </div>
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

    <script type="text/javascript" src="/statics/Home/js/jquery.picZoomer.js"></script>
    <script src="/statics/Home/js/validate.js"></script>
    <script src="/statics/Home/js/xcConfirm.js"></script>
    <script type="text/javascript">
        $(function () {
            $('.picZoomer').picZoomer();
            //切换图片
            $('.piclist li').on('click', function (event) {
                var $pic = $(this).find('img');
                $('.picZoomer-pic').attr('src', $pic.attr('src'));
            });
            //tab切换
            $("#navLi li").click(function () {
                $(this).addClass("active").siblings().removeClass("active");
                $(".tab-content>div").eq($(this).index()).show().siblings().hide();
            });
//            shopAddFrom
            $('#shopAddFrom').formCheck('#buyAct', function (res) {
                if (res) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('shop/add');?>",
                        data: $('#shopAddFrom').serialize(),
                        dataType: "json",
                        success: function (data) {
                            if (data.status) {
//                                    windowInfo('', 'success', '', 1);
                                setTimeout(window.location.href = data.url, 3);
                            } else {
                                windowInfo(data.info, 'info', '', 1);
                                if(data.url != 'undefined'){
                                    setTimeout(window.location.href = data.url, 5);
                                }
                            }
                            $vcodeImg.attr("src", verifyimg + '&random=' + Math.random());

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