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
	#main h3{
		font-size: 30px;
		font-weight: bold;
	}
	#main dl{
		margin:20px 0 60px 0
	}
	#main dl:last-child{
		margin-bottom:0
	}
	#main dt{margin-bottom:20px}
	#main span{
		font-weight: normal;
		font-size: 12px;
		margin-top: 5px;
	}
	#main p{
		margin-top: 20px;
		line-height: 30px;
		font-size: 16px;
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
            
	<!---主体内容--->
	<div id="main" style="background-color:#fff;padding-top:20px">
		<dl class="clearfix">
			<dt class="container">
				<h3>景区</h3>
				<span>Scenic spot</span>
			</dt>
			<dd>
				<img src="/statics/Home/img/scenic.jpg" alt="" style="width:100%">
				<p class="container">
					秦皇岛，简称秦，又称港城，河北省地级市，世界级汽车轮毂制造基地和中国最大铝制品生产加工基地，北方最大粮油加工基地，中国首批沿海开放城市，中国海滨城市，东北亚重要的对外贸易口岸，地处环渤海经济圈中心地带，是东北与华北两大经济区的结合部。秦皇岛港是世界第一大能源输出港，有国民经济“晴雨表”之称。[1]
					秦皇岛是国家历史文化名城，因秦始皇求仙驻跸而得名，两千余载的岁月长河，留下了夷齐让国、秦皇求仙、魏武挥鞭等历史典故。秦皇岛曾协办北京亚运会和北京奥运会，是中国唯一协办过奥运会和亚运会的地级市。[2]
					秦皇岛是低碳试点城市；国家园林城市；中国优秀旅游城市；中国综合交通枢纽城市；第一批国家智慧城市试点；2012中国特色魅力城市；全国双拥模范城市；十大最佳休闲城市之一；全国十佳生态文明城市；全国十佳绿色生态旅游城市；全国首批无障碍设施建设示范创建城市；中国最具幸福感城市。
				</p>
			</dd>
		</dl>
		<dl class="clearfix">
			<dt class="container">
			<h3>住宿</h3>
			<span>Get accommodation</span>
			</dt>
			<dd>
				<img src="/statics/Home/img/hotel.jpg" alt="" style="width:100%">
				<p class="container">
					秦皇岛，简称秦，又称港城，河北省地级市，世界级汽车轮毂制造基地和中国最大铝制品生产加工基地，北方最大粮油加工基地，中国首批沿海开放城市，中国海滨城市，东北亚重要的对外贸易口岸，地处环渤海经济圈中心地带，是东北与华北两大经济区的结合部。秦皇岛港是世界第一大能源输出港，有国民经济“晴雨表”之称。[1]
					秦皇岛是国家历史文化名城，因秦始皇求仙驻跸而得名，两千余载的岁月长河，留下了夷齐让国、秦皇求仙、魏武挥鞭等历史典故。秦皇岛曾协办北京亚运会和北京奥运会，是中国唯一协办过奥运会和亚运会的地级市。[2]
					秦皇岛是低碳试点城市；国家园林城市；中国优秀旅游城市；中国综合交通枢纽城市；第一批国家智慧城市试点；2012中国特色魅力城市；全国双拥模范城市；十大最佳休闲城市之一；全国十佳生态文明城市；全国十佳绿色生态旅游城市；全国首批无障碍设施建设示范创建城市；中国最具幸福感城市。
				</p>
			</dd>
		</dl>
		<dl class="clearfix">
			<dt class="container">
			<h3>美食</h3>
			<span>Delicious food</span>
			</dt>
			<dd>
				<img src="/statics/Home/img/delicacy.jpg" alt="" style="width:100%">
				<p class="container" style="padding-bottom:60px">
					秦皇岛，简称秦，又称港城，河北省地级市，世界级汽车轮毂制造基地和中国最大铝制品生产加工基地，北方最大粮油加工基地，中国首批沿海开放城市，中国海滨城市，东北亚重要的对外贸易口岸，地处环渤海经济圈中心地带，是东北与华北两大经济区的结合部。秦皇岛港是世界第一大能源输出港，有国民经济“晴雨表”之称。[1]
					秦皇岛是国家历史文化名城，因秦始皇求仙驻跸而得名，两千余载的岁月长河，留下了夷齐让国、秦皇求仙、魏武挥鞭等历史典故。秦皇岛曾协办北京亚运会和北京奥运会，是中国唯一协办过奥运会和亚运会的地级市。[2]
					秦皇岛是低碳试点城市；国家园林城市；中国优秀旅游城市；中国综合交通枢纽城市；第一批国家智慧城市试点；2012中国特色魅力城市；全国双拥模范城市；十大最佳休闲城市之一；全国十佳生态文明城市；全国十佳绿色生态旅游城市；全国首批无障碍设施建设示范创建城市；中国最具幸福感城市。
				</p>
			</dd>
		</dl>
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

</body>
</html>