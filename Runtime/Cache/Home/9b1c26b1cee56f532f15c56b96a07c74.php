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
    
<style>
body{
    background-color:#e7e6e6
}
#navWrp{
    background: rgb(245,245,245); /* Old browsers */
    background: -moz-linear-gradient(top, rgb(255,255,255) 0%, rgb(255,255,255) 50%, rgb(255,255,255) 51%, rgb(245,245,245) 51%, rgb(245,245,245) 100%); /* FF3.6-15 */
    background: -webkit-linear-gradient(top, rgb(255,255,255) 0%,rgb(255,255,255) 50%,rgb(255,255,255) 51%,rgb(245,245,245) 51%,rgb(245,245,245) 100%); /* Chrome10-25,Safari5.1-6 */
    background: linear-gradient(to bottom, rgb(255,255,255) 0%,rgb(255,255,255) 50%,rgb(255,255,255) 51%,rgb(245,245,245) 51%,rgb(245,245,245) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#efefef',GradientType=0 ); /* IE6-9 */
    height:6.5em;
    line-height:6.5em
}
.carousel-inner .item img{
    width:100%
}
#competitive{
    padding:0 15px 0 0;
    border-top:1px solid #ddd;
    border-left:1px solid #ddd;
    border-right:1px solid #ddd;
    border-bottom: 2px solid #d29d3e;
    background-color:#f2f2f2
}
#competitive h3{
    width:140px;
    height:60px;
    text-align:center;
    font-size:1.5em;
    font-weight:bold;
    line-height:60px;
    margin-top:15px;
    background-color:#d29d3e;
    color:white
}
.panelList{
    margin-left:10px;
    position:relative;
    border-top-right-radius: 0;

}
.panelList:before{
    content: '';
    position: absolute;
    top: -1px;
    left: -10px;
    width: 10px;
    height: 10px;
    background: linear-gradient(45deg,rgba(0,0,0,0) 7px,#838383 7px);
    background: -moz-linear-gradient(45deg,rgba(0,0,0,0) 7px,#838383 7px);
    background: -o-linear-gradient(45deg,rgba(0,0,0,0) 7px,#838383 7px);
    background: -webkit-linear-gradient(45deg,rgba(0,0,0,0) 7px,#838383 7px);
    display: block;
    z-index: 15;
}
#competitive a{
    font-size: .8em;
    padding-right: 6px;
    color:#999
}
#competitive a:hover{
    color:#4f8db3;
    text-decoration: none;
}
::-moz-placeholder { color: #999;font-size:.9em }
::-webkit-input-placeholder { color: #999;font-size:.9em  }
:-ms-input-placeholder { color: #999;font-size:.9em  }

#competitive .input-group-btn>.btn,#competitive .input-group-btn>.btn:hover,#competitive .input-group-btn>.btn:active,#competitive .input-group-btn>.btn:focus{
    color:white;
    margin-left:-1px;
    background-color:#55acef;
}
#competitive .input-group-btn>.btn:hover, .input-group-btn>.btn:focus, .input-group-btn>.btn:active{z-index:0}
#search{
    padding-left: 40px;
    color: #999;
    border: 4px solid #55acef;
    border-radius: 0;
    background-color: #f2f2f2;
    background: url(/statics/Home/img/search.png) no-repeat 5px center;
}
.sImgLabel{
    display: block;
    width: 40px;
    text-align: center;
    height: 90px;
    font-size: 1.5em;
    background-color: #ffd301;
    line-height: 36px;
    padding-top: 9px;
    color: #333;
}
.payAct,.payAct:hover,.payAct:active,.payAct:focus{
    background-color: #c40000;
    color: white;
    margin-top: 6px;
    font-size: 1.8rem;
    outline: none;
}
.list-inline{margin:0}
#feature{
    margin:20px auto;
}
#feature li{
    position:relative;
    margin: 0 10px;
}
#feature li div.circleImg{
    width: 150px;
    height: 150px;
    border-radius: 150px;
    border: 1px solid #d29d3e;
    text-align: center;
}
#feature li img{
    width: 95%;
    border-radius: 95%;
    margin: 2.5%;
}
#feature li div.titleWrp{
    position: absolute;
    width: 30px;
    border: 1px solid #d29d3e;
    color: white;
    padding: 3px 0;
    bottom: -32px;
    left: 50%;
    transform: translate(-50%)
}
#feature li div.titleWrp span{
    display: block;
    width: 24px;
    margin: 0 auto;
    padding: 5px 0;
    background-color: #d29d3e;
}
#seaSlug{
    position: relative;
    padding-top: 130px;
    background: url(/statics/Home/img/indexBnr.jpg) no-repeat;
    width: 100%;
    background-size: contain;
    margin-top: 50px;
    text-align:center
}
#seaSlug div.imgDiv{
    width: 200px;
    overflow: hidden;
    padding: 4px;
    background-color: #fff;
    margin-bottom: 10px;
}
.more{
    background-color: #d29d3e;
    font-size: 18px;
    padding: 10px 40px;
    color: #fff;
    border-color: #d29d3e;
}
.more:hover,.more:active,.more:focus{color:white}
.yellow{color:#d29d3e}
.white{color:#fff}
.introduce{
    margin-top: 50px;
    margin-bottom: 80px;
}
.introduce p{
    text-align: left;
    line-height: 24px;
}
.introduce h3{
    width: 400px;
    font-size: 2.5em;
    height: 80px;
    margin: 15px auto;
    background: url(/statics/Home/img/indexH3Bg.png) no-repeat center bottom;
}
#cmpInfo{
    background: url(/statics/Home/img/cmpInfoBk.png) no-repeat top center;
    background-size: cover;
    padding: 20px 0;
}
#cmpInfo h3{
    background:url(/statics/Home/img/whiteH3Bg.png) no-repeat center bottom;
}
#cmpInfo .container{
    margin:40px auto;
}
#cmpInfo .more{
    background-color: white;
    border-color: white;
    color: #d29d3e;
}
#cmpInfo ul li{
    width:335px;
    overflow:hidden;
    margin:0 15px;
}
#cmpInfo ul li p{
    color:white;
    margin-top:20px;
    line-height:30px
}
</style>

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
                    <a href="<?php echo U('Index/index');?>">
                        <img src="/statics/Home/img/logo.png" alt="一品农夫" style="width: 60px;">
                        <img src="/statics/Home/img/logoTitle.png" alt="一品农夫销售平台">
                    </a>
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
            
    <div id="myCarousel" class="carousel slide">
        <!-- Indicators -->
        <ol class="carousel-indicators">

            <?php if(is_array($imgs)): $cc = 0; $__LIST__ = $imgs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$car): $mod = ($cc % 2 );++$cc;?><li data-target="#myCarouse<?php echo ($cc); ?>" data-slide-to="<?php echo ($cc-1); ?>" <?php if($cc == 1): ?>class="active"><?php endif; ?></li><?php endforeach; endif; else: echo "" ;endif; ?>

        </ol>
        <div class="carousel-inner">
            <?php if(is_array($imgs)): $cc = 0; $__LIST__ = $imgs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$car): $mod = ($cc % 2 );++$cc;?><div class="item <?php if($cc == 1): ?>active<?php endif; ?>">
                    <img src="<?php echo ($car); ?>">
                </div><?php endforeach; endif; else: echo "" ;endif; ?>

        </div>

    </div>
    <!---主体内容--->
    <div id="main" style="background-color:#fff;padding:20px 0 50px 0">
        <div class="container" style="text-align:center">
            <ul class="list-inline" id="feature">
                <li>
                    <div class="circleImg"><img src="/statics/Home/img/product.jpg" alt=""></div>
                    <div class="titleWrp"><span>健康</span></div>
                </li>
                <li>
                    <div class="circleImg"><img src="/statics/Home/img/product.jpg" alt=""></div>
                    <div class="titleWrp"><span>养生</span></div>
                </li>
                <li>
                    <div class="circleImg"><img src="/statics/Home/img/product.jpg" alt=""></div>
                    <div class="titleWrp"><span>美味</span></div>
                </li>
            </ul>
        </div>
        <div id="seaSlug">
            <ul class="list-inline">
                <li>
                    <div class="imgDiv"><img src="/statics/Home/img/product.jpg" alt="" style="width:100%"></div>
                    <h3 class="yellow">特级海参</h3>
                    <p class="yellow" style="font-size:10px;margin-top:10px">延续衰老，消除疲劳，提高免疫力</p>
                    <a class="btn btn-default yellow" role="button">立即购买 》</a>
                </li>
                <li>
                    <div class="imgDiv"><img src="/statics/Home/img/product.jpg" alt="" style="width:100%"></div>
                    <h3 class="yellow">特级海参</h3>
                    <p class="yellow" style="font-size:10px;margin-top:10px">延续衰老，消除疲劳，提高免疫力</p>
                    <a class="btn btn-default yellow" role="button">立即购买 》</a>
                </li>
            </ul>
        </div>
        <div class="container introduce center">
            <h3 class="yellow">海参</h3>
            <p>
                海参，属海参纲(Holothuroidea)，是生活在海边至8000米的海洋棘皮动物，距今已有六亿多年的历史，海参以海底藻类和浮游生物为食。 海参全身长满肉刺，广布于世界各海洋中。我国南海沿岸种类较多，约有二十余种海参可供食用，海参同人参、燕窝、鱼翅齐名，是世界八大珍品之一。海参不仅是珍贵的食品，也是名贵的药材。据《本草纲目拾遗》中记载:海参，味甘咸，补肾，益精髓，摄小便，壮阳疗痿，其性温补，足敌人参，故名海参。海参具有提高记忆力、延缓性腺衰老，防止动脉硬化以及抗肿瘤等作用。随着海参价值知识的普及，海参逐渐进入百姓餐桌。生活环境决定海参品质。
            </p>
            <button class="btn more">查看更多</button>
        </div>
        <div id="cmpInfo" class="introduce center">
            <div class="container">
                <h3 class="white">公司简介</h3>
                <ul class="list-inline clearfix">
                    <li>
                        <img src="/statics/Home/img/infoLiBk.jpg" alt="">
                        <p>生活在海边至8000米的海洋棘皮动物，距今已有六亿多年的历史，海参以海底藻类和浮游</p>
                    </li>
                    <li>
                        <img src="/statics/Home/img/infoLiBk.jpg" alt="">
                        <p>生活在海边至8000米的海洋棘皮动物，距今已有六亿多年的历史，海参以海底藻类和浮游</p>
                    </li>
                    <li>
                        <img src="/statics/Home/img/infoLiBk.jpg" alt="">
                        <p>生活在海边至8000米的海洋棘皮动物，距今已有六亿多年的历史，海参以海底藻类和浮游</p>
                    </li>
                </ul>
                <button class="btn more">查看更多</button>
            </div>
        </div>
        <img src="/statics/Home/img/activityBg.jpg" alt="" style="width:100%">
        <div class="container introduce center" style="margin-bottom: 0">
            <h3 class="yellow">活动介绍</h3>
            <p>
                海参，属海参纲(Holothuroidea)，是生活在海边至8000米的海洋棘皮动物，距今已有六亿多年的历史，海参以海底藻类和浮游生物为食。 海参全身长满肉刺，广布于世界各海洋中。我国南海沿岸种类较多，约有二十余种海参可供食用，海参同人参、燕窝、鱼翅齐名，是世界八大珍品之一。海参不仅是珍贵的食品，也是名贵的药材。据《本草纲目拾遗》中记载:海参，味甘咸，补肾，益精髓，摄小便，壮阳疗痿，其性温补，足敌人参，故名海参。海参具有提高记忆力、延缓性腺衰老，防止动脉硬化以及抗肿瘤等作用。随着海参价值知识的普及，海参逐渐进入百姓餐桌。生活环境决定海参品质。
            </p>
            <button class="btn more">查看更多</button>
        </div>
    </div>

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
        $(document).ready(function(){
            $('.carousel').carousel({
                interval: 2000
            });
            // 导航高亮
        });
    </script>

</body>
</html>