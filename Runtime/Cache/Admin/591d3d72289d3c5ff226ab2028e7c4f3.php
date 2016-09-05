<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>农场管理平台</title>
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
        <h3>物流</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">物流管理</a>
            </li>
            <li class="active">物流模板列表</li>
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
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th class="col-sm-2">运送方式</th>
                                    <th class="col-sm-4">运送到</th>
                                    <th class="col-sm-1">首重（kg）</th>
                                    <th class="col-sm-1">运费（元）</th>
                                    <th class="col-sm-1">续重（kg）</th>
                                    <th class="col-sm-1">运费（元）</th>
                                </tr>
                            </thead>
                        </table>
                        <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><table class="table table-bordered table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th colspan="4" class="col-sm-6"><?php echo ($vo["title"]); ?><span class="pull-right"><?php echo (date('Y-m-d H:i:s', $vo["update_time"])); ?></span></th>
                                    <th colspan="2" class="text-center"><a href="<?php echo U('edit', array('id'=>$vo['id']));?>">修改</a></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($extend[$vo['id']]['data'])): if(is_array($extend[$vo['id']]['data'])): $i = 0; $__LIST__ = $extend[$vo['id']]['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                                            <td class="col-sm-2">&nbsp;</td>
                                            <td class="col-sm-4"><?php echo ($list["area_name"]); ?></td>
                                            <td class="col-sm-1"><?php echo ($list["first_weight"]); ?></td>
                                            <td class="col-sm-1"><?php echo ($list["first_price"]); ?></td>
                                            <td class="col-sm-1"><?php echo ($list["next_weight"]); ?></td>
                                            <td class="col-sm-1"><?php echo ($list["next_price"]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </tr><?php endif; ?>
                                </tbody>
                            </table><?php endforeach; endif; else: echo "" ;endif; ?>
                    </section>
                </div>
            </section>
        </div>
    </div>

        </div>
        <!--body wrapper end-->

        <!--footer section start-->
        <footer class="text-center">
            2015 &copy; 智慧云联科技（北京）有限公司
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
        $('title', parent.document).html('<?php echo ($meta_title); ?>' + ' | 农场管理平台');
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




</body>
</html>