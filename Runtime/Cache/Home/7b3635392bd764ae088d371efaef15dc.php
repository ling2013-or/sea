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
    
    <link href="/statics/Home/css/xcConfirm.css" media="all" rel="stylesheet" type="text/css"/>
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
	#otherList::before{
		display:none;
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
		<a class="carousel-control left" href="#myCarousel" data-slide="prev"></a>
		<a class="carousel-control right" href="#myCarousel" data-slide="next"></a>
	</div>
		<!---主体内容--->
		<div id="main" style="background-color:#fff;padding-top:20px">
		<div class="container" id="contentWrp">
			<div class="clearfix" id="competitive">
				<h3 class="pull-left">精品单品</h3>
				<div class="pull-right" style="width:500px;margin-top:10px">
				<div class="input-group">
				<input type="text" class="form-control" id="search" placeholder="输入商品名称">
				<span class="input-group-btn">
				<button class="btn btn-default" type="button">搜索</button>
				</span>
				</div>
				<p><a href="">有机海参</a><a href="">有机基围虾</a><a href="">虾皮</a></p>
				</div>
			</div>
			<div class="panel panel-default panelList">
				<div class="panel-body" id="productWrp">
                    <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="row service-wrapper-row productList">
						<div class="col-sm-12">
						<div class="col-sm-4">
							<div class="service-image">
								<img src="<?php echo ($list['picture']); ?>" alt="Color Schemes">
								<span class="imgLabel">直播</span>
							</div>
						</div>
						<div class="col-sm-6 detail">
							<h3><?php echo ($list["name"]); ?></h3>
							<p class="price"><span class="orginPrice"><del>市场价：￥<?php echo ($list["mark_price"]); ?></del></span><span class="curPrice">售价：<span>￥<?php echo ($list["price"]); ?></span></span></p>
							<p class="desc"><?php echo ($list["title"]); ?></p></div>
						<div class="col-sm-2 btnActWrp">
							<button class="btn btn-default buyAct" goods_id="<?php echo ($list['id']); ?>" goods_type="<?php echo ($list['goods_type']); ?>" style="border:2px solid #c40000;color:#c40000;line-height:44px">我要购买</button>
							<a href="<?php echo U('Goods/detail',array('gid'=>$list['id']));?>" class="btn btn-default" style="background:#c40000;color:white" role="button">查看详情</a>
							<a class="btn btn-default" style="background:#aaa;color:white" role="button">直接退款</a>
						</div>
						</div>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>

			</div>

		</div>
		</div>
	<!--购买弹窗--->
	<div class="modal fade" id="buyModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document" style="z-index: 9999;">
			<div class="modal-content">
				<div class="modal-header" style="border:none">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				</div>
				<div class="modal-body">
					<div class="row clearfix">
						<div class="col-md-4">
							<img src="/statics/Home/img/product.jpg" alt="有机海参 有机基围虾 精品包装" style="width:100%;height:auto">
						</div>
						<ul class="col-md-8">
							<li class="clearfix"><h5 class="pull-left">有机海参 有机基围虾 精品包装</h5><div class="sprice pull-right"><del>市场价：￥9999</del></div></li>
							<li class="clearfix"><div class="pull-right"><span class="cprice">售价：￥66666</span></div></li>
							<li class="clearfix"><h5 class="pull-left">运费</h5><div class="pull-right"><span class="cprice">￥50</span></div></li>
							<li class="clearfix"><h5 class="pull-left">实付款（含运费）</h5><div class="pull-right"><span class="cprice">￥9999</span></div></li>
							<li class="clearfix">
								<button type="submit" class="btn btn-default payAct pull-right">
									<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
									确定支付
								</button>
							</li>
						</ul>
					</div>
					<div class="row clearfix" style="margin-top:30px">
						<div class="col-md-5" style="height: 150px;overflow: hidden;">
							<img src="/statics/Home/img/product.jpg" alt="有机海参 有机基围虾 精品包装" style="width:100%;height:100%;border-radius: 10px;">
						</div>
						<div class="col-md-5" style="margin-top:15px;max-height:120px;overflow:hidden">
							会员卡会员卡会员卡会员卡会员卡会员卡
							会员卡会员卡会员卡会员卡会员卡会员卡
							会员卡会员卡会员卡会员卡会员卡会员卡
							会员卡会员卡会员卡会员卡会员卡会员卡
						</div>
						<div class="col-md-2" style="width:30px;margin-top:15px">
							<span class="sImgLabel">直播</span>
						</div>
					</div>
				</div>
			</div>
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

    <script src="/statics/Home/js/validate.js"></script>
    <script src="/statics/Home/js/xcConfirm.js"></script>
	<script>
		$(document).ready(function(){
			$('.carousel').carousel({
				interval: 2000
			});
			// 导航高亮
			highlight_subnav("<?php echo U('Goods/index');?>");
			//我要购买
			$('.buyAct').on('click',function(){
                var variety = 1;
                var goods_id = $(this).attr('goods_id');
                var goods_type = $(this).attr('goods_type');
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('shop/add');?>",
                    data: {id:goods_id,goods_type:goods_type,variety:variety},
                    dataType: "json",
                    success: function (data) {
                        if (data.status) {
                            setTimeout(window.location.href = data.url, 3);
                        } else {
                            windowInfo(data.info, 'info', '', 1);
                            if(data.url != 'undefined'){
                                setTimeout(window.location.href = data.url, 5);
                            }

                        }


                    },
                    complete: function (data) {

                    }
                });
//                $('#buyModel').modal('show');
			});
		});
	</script>

</body>
</html>