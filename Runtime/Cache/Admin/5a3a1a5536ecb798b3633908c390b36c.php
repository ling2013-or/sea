<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>一品农夫</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="/statics/Admin/js/uploadify/uploadify.css">
    <style>
        #img_show span, #img_show_multi span {
            position: relative;
            display: inline-block;
        }

        #img_show img, #img_show_multi img {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        #img_show a, #img_show_multi a {
            position: absolute;
            top: 5px;
            right: 5px;
            margin-right: 5px;
        }
    </style>


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
        <h3>分区管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo U('index');?>">分区列表</a>
            </li>
            <li class="active">分区详情</li>
        </ul>
    </div>

        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper" style="padding:0 15px">
            
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <form role="form" action="<?php echo U();?>" class="form">
                        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="title">分区标题 <span class="check-tips"></span></label>
                                <input type="text" class="form-control" name="title" id="title" value="<?php echo ($info["title"]); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="total_stock">分区容量<span
                                        class="check-tips">（单位：份）</span></label>
                                <input type="number" class="form-control" name="total_stock" id="total_stock"
                                       value="<?php echo ($info["total_stock"]); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="seed">商品 </label>
                                <select class="form-control" id="seed" name="goods_id">
                                    <?php if(!empty($goodsList)): if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><option <?php if($row['id'] == $info['goods_id']): ?>selected<?php endif; ?> value="<?php echo ($row["id"]); ?>"><?php echo ($row["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <?php else: ?>
                                        <option value="0"> - 暂无可选 -</option><?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary ajax-post" target-form="form">确 定</button>
                        <button class="btn a-back">返 回</button>
                    </form>
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


    <script type="text/javascript" src="/statics/Admin/js/uploadify/jquery.uploadify.js"></script>
    <script type="text/javascript" charset="utf-8" src="/statics/Admin/js/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/statics/Admin/js/ueditor/ueditor.all.min.js"></script>

    <script type="text/javascript">
        //导航高亮
        highlight_subnav("<?php echo U('Goodszone/index');?>");
        //ueditor编辑器
        var ue = UE.getEditor('ueditor', {
            textarea:'content'
        });
        //时间选择窗
        var start = {
            elem: '#plan_start',
            format: 'YYYY-MM-DD',
            istoday: false,
            choose: function(datas){
            }
        };


    </script>


</body>
</html>