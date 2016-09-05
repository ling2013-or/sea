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
        <h3>订单管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo U('index');?>">订单列表</a>
            </li>
            <li class="active">订单详情</li>
        </ul>
    </div>

        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper" style="padding:0 15px">
            
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <section id="unseen">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>订单状态：<?php echo ($info["state_txt"]); ?></td>
                                    <td>订单编号：<?php echo ($info["order_sn"]); ?></td>
                                    <?php if($info["order_status"] > 1): ?><td>实付金额：<?php echo ($info["pay_money"]); ?></td><?php endif; ?>
                                    <td>下单时间：<?php echo (date('Y-m-d H:i:s', $info["add_time"])); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>数量</th>
                                <th>价格</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($info["extend_order_goods"])): $i = 0; $__LIST__ = $info["extend_order_goods"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><tr>
                                <td><?php echo ($goods["goods_name"]); ?></td>
                                <td><?php echo ($goods["goods_num"]); ?></td>
                                <td><?php echo ($goods["goods_price"]); ?></td>

                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4">

                                    订单总价：<i class="fa fa-rmb"></i><?php echo ($info["order_price"]); ?>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                        <table class="table">
                            <?php if(!empty($info["pay_type"])): ?><tr>
                                    <td>支付方式：<?php echo (get_pay_type($info["pay_type"])); ?></td>
                                </tr><?php endif; ?>
                                <tr>
                                    <td>下单时间：<?php echo (date('Y-m-d H:i:s', $info["add_time"])); ?></td>
                                </tr>
                            <?php if(!empty($info["pay_time"])): ?><tr>
                                    <td>付款时间：<?php echo (date('Y-m-d H:i:s', $info["pay_time"])); ?></td>
                                </tr><?php endif; ?>
                            <?php if(!empty($info["over_time"])): ?><tr>
                                    <td>完成时间：<?php echo (date('Y-m-d H:i:s', $info["over_time"])); ?></td>
                                </tr><?php endif; ?>
                            </tr>
                        </table>

                        <!-- 物流信息 -->
                        <h3>物流信息</h3>
                        <table class="table">

                            <?php if(!empty($info["order_message"])): ?><tr>
                                    <td>买家留言：<?php echo ($info["order_message"]); ?></td>
                                </tr><?php endif; ?>
                            <tr>
                                <td class="col-lg-2">收货人：
                                    <?php echo ($info["reciver_name"]); ?>&nbsp;
                                    <?php if(isset($info['reciver_tel'])): echo ($info["reciver_tel"]); ?>&nbsp;<?php endif; ?>
                                    <br />
                                    <?php if(isset($info['address'])): echo ($info["address"]); ?>&nbsp;<?php endif; ?>
                                </td>
                            </tr>
                        </table>
                        <!-- 订单操作日志 -->
                        <?php if(!empty($log)): ?><h3>操作历史</h3>
                            <table>
                                <tbody>
                                <?php if(is_array($log)): $i = 0; $__LIST__ = $log;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["operate_rule"]); ?> <?php echo ($vo["operate_user"]); ?> &emsp; <?php echo (date('Y-m-d H:i:s', $vo["operate_time"])); ?> &emsp;  <?php echo ($vo["description"]); ?></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table><?php endif; ?>
                    </section>
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
        highlight_subnav("<?php echo U('index');?>");
    </script>


</body>
</html>