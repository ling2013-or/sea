<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>一品农夫</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" href="/statics/Admin/css/order.css">


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
        <h3>订单管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">订单管理</a>
            </li>
            <li class="active">订单列表</li>
        </ul>
    </div>

        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper" style="padding:0 15px">
            
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="tab-body">
                        <ul class="tab-nav nav">
                            <li
                            <?php if(($state_type) == "state_order"): ?>class="current"<?php endif; ?>
                            ><a href="<?php echo U();?>">所有订单</a></li>
                            <li
                            <?php if(($state_type) == "state_new"): ?>class="current"<?php endif; ?>
                            ><a href="<?php echo U('', array('state_type'=>'state_new'));?>">待付款</a></li>
                            <li
                            <?php if(($state_type) == "state_pay"): ?>class="current"<?php endif; ?>
                            ><a href="<?php echo U('', array('state_type'=>'state_pay'));?>">已付款</a></li>
                            <li
                            <?php if(($state_type) == "state_send"): ?>class="current"<?php endif; ?>
                            ><a href="<?php echo U('', array('state_type'=>'state_send'));?>">待完成养殖</a></li>
                            <li
                            <?php if(($state_type) == "state_success"): ?>class="current"<?php endif; ?>
                            ><a href="<?php echo U('', array('state_type'=>'state_success'));?>">待评论</a></li>
                            <li
                            <?php if(($state_type) == "state_cancel"): ?>class="current"<?php endif; ?>
                            ><a href="<?php echo U('', array('state_type'=>'state_cancel'));?>">已取消</a></li>
                        </ul>
                    </div>
                    <div class="operate-body">
                        <div class="pull-right search-form form-inline">
                            <form action="" onsubmit="return false">
                                下单时间：<input type="text" name="start_time" value="<?php echo I('start_time');?>" class="laydate-icon form-control search-input" id="start" placeholder="开始时间"/>
                                <input type="text" name="end_time" value="<?php echo I('end_time');?>" class="form-control search-input laydate-icon" id="end" placeholder="结束时间"/>
                                <input type="text" name="buyer_name" class="form-control search-input" value="<?php echo I('buyer_name');?>" placeholder="请输入会员名">
                                <input type="text" name="order_sn" class="form-control search-input" value="<?php echo I('order_sn');?>" placeholder="请输入订单号">

                                <a href="javascript:;" class="btn btn-info" id="search">搜索</a>
                            </form>
                        </div>
                    </div>
                    <section id="unseen">
                        <table class="table order-table-style order">
                            <thead>
                            <tr>
                                <th class="w10"></th>
                                <th colspan="2">商品详情</th>
                                <th class="w70">单价</th>
                                <th class="w50">数量</th>
                                <th class="w110">买家</th>
                                <th class="w110">订单总价</th>
                                <th class="w110">状态与操作</th>
                            </tr>
                            </thead>
                            <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tbody>
                                    <tr>
                                        <td colspan="20" class="sep-row"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="20">
                                    <span class="fl ml10">订单编号：
                                        <span class="goods-num"><em><?php echo ($vo["order_sn"]); ?></em><?php if(($vo["order_from"]) == "2"): ?><i
                                                class="fa fa-mobile" style="font-size: 18px;"></i><?php endif; ?></span>
                                    </span>
                                            <span class="fl ml20">下单时间：<em class="goods-time"><?php echo (date('Y-m-d
                                                H:i:s', $vo["add_time"])); ?></em></span>
                                    <span class="fr mr5">
                                        <a href="<?php echo U('detail', array('order_id'=>$vo['order_id']));?>"  class="btn btn-xs">查看订单</a>

                                    </span>
                                        </th>
                                    </tr>
                                    <?php if(is_array($vo["extend_order_goods"])): $i = 0; $__LIST__ = $vo["extend_order_goods"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><tr>
                                            <td class="bdl"></td>
                                            <td class="w50">
                                                <div class="pic-thumb">
                                                    <a href="javascript:;" target="_blank" class="img-thumbnail">
                                                        <img src="<?php echo ($goods["goods_cover"]); ?>">
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="tl">
                                                <dl class="goods-name">
                                                    <dt>
                                                        <a target="_blank" href="javascript:;"><?php echo ($goods["goods_name"]); ?></a>
                                                    </dt>
                                                    <dd></dd>
                                                </dl>
                                            </td>
                                            <td><i class="fa fa-rmb"></i><?php echo ($goods["goods_price"]); ?></td>
                                            <td><?php echo ($goods["goods_num"]); ?></td>
                                            <?php if(((count($vo['extend_order_goods']) > 1) and ($key == 0)) or (count($vo['extend_order_goods']) == 1)): ?><td class="bdl" rowspan="<?php echo count($vo['extend_order_goods']);?>">
                                                    <div class="buyer"><?php echo ($vo["extend_user"]["user_name"]); ?>
                                                        <p>(账号名)</p>
                                                        <div class="buyer-info" style="display: none;">
                                                            <div class="con">
                                                                <h3><i></i><span>联系信息</span></h3>
                                                                <dl>
                                                                    <dt>姓名：</dt>
                                                                    <dd><?php echo ($vo["extend_user"]["user_name"]); ?></dd>
                                                                </dl>
                                                                <dl>
                                                                    <dt>电话：</dt>
                                                                    <dd><?php echo ($vo["extend_user"]["user_phone"]); ?></dd>
                                                                </dl>
                                                                <dl>
                                                                    <dt>地址：</dt>
                                                                    <dd><?php echo ($vo["address"]); ?></dd>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="bdl" rowspan="<?php echo count($vo['extend_order_goods']);?>">
                                                    <p class="order-order-amount"><i class="fa fa-rmb"></i><?php echo ($vo["order_amount"]); ?>
                                                    </p>

                                                    <p class="goods-pay"><?php echo ($vo["payment_name"]); ?></p>

                                                    <p class="goods-freight"></p>
                                                </td>
                                                <td class="bdl bdr" rowspan="<?php echo count($vo['extend_order_goods']);?>">
                                                    <p>
                                                        <?php echo ($vo["state_txt"]); ?>
                                                        <?php if(!empty($vo["evaluation_time"])): ?><br/>已评价<?php endif; ?>
                                                    </p>
                                                    <?php if($vo["if_cancel"] == true): ?><!-- 取消订单 -->
                                                        <p>
                                                            <a href="<?php echo U('cancel', array('order_sn'=>$vo['order_sn'], 'order_id'=>$vo['order_id']));?>"
                                                               class="btn btn-danger btn-xs order-cancel">取消订单</a></p><?php endif; ?>

                                                    <?php if($vo["if_pay"] == true): ?><!-- 设为已付款 -->
                                                        <p>
                                                            <a href="<?php echo U('order_pay', array('state_type'=>'modify_price', 'order_sn'=>$vo['order_sn'], 'order_id'=>$vo['order_id']));?>"
                                                               class="btn btn-warning btn-xs">确认支付</a></p><?php endif; ?>

                                                    <?php if($vo["if_breed"] == true): ?><!-- 养殖中 -->
                                                        <p><a href="<?php echo U('breed', array('order_id'=>$vo['order_id']));?>"
                                                              class="btn btn-success btn-xs confirm ajax-get">开始养殖</a></p><?php endif; ?>

                                                    <?php if($vo["if_breed_over"] == true): ?><!-- 养殖完成 -->
                                                        <p><a href="<?php echo U('over_will', array('order_id'=>$vo['order_id']));?>"
                                                              class="btn btn-success btn-xs confirm ajax-get">养殖完成</a></p><?php endif; ?>

                                                    <?php if($vo["if_lock"] == true): ?><!-- 锁定 -->
                                                        退款退货中<?php endif; ?>
                                                </td><?php endif; ?>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </tbody><?php endforeach; endif; else: echo "" ;endif; ?>
                                <?php else: ?>
                                <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">暂无数据</td>
                                </tr>
                                </tbody><?php endif; ?>
                        </table>
                    </section>
                    <div class="page">
                        <?php echo ($page); ?>
                    </div>
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


    <script src="/statics/assets/laydate/laydate.js"></script>
    <script>
        window.setValue("is_platform", "<?php echo I('is_platform');?>");
        var start = {
            elem: '#start',
            format: 'YYYY-MM-DD',
            min: '2015-10-10', //设定最小日期为当前日期
            max: laydate.now(), //最大日期
            istoday: false,
            choose: function(datas){
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas; //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY-MM-DD',
            min: '2015-10-10',
            max: laydate.now(), //最大日期
            istoday: true,
            choose: function(datas){
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate.skin('molv');
        laydate(start);
        laydate(end);
        var tips;
        $('.buyer').mouseover(function () {
            var content = $(this).find('.buyer-info').html();
            tips = layer.tips(content, this, {
                time: 0
            });
        }).mouseout(function () {
            layer.close(tips);
        });
        highlight_subnav("<?php echo U('index');?>");
        $("#search").click(function () {
            var url = "<?php echo U('index');?>";
            var query = $('form').serialize();
            query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            query = query.replace(/^&/g, '');
            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        });
        //回车搜索
        $(".search-input").keyup(function (e) {
            if (e.keyCode === 13) {
                $("#search").click();
                return false;
            }
        });
    </script>


</body>
</html>