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
        <h3>配置管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">配置管理</a>
            </li>
            <li class="active">编辑配置</li>
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
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="name">配置标识 <span class="check-tips">（用于C函数调用，只能使用英文且不能重复）</span></label>
                                <input type="text" class="form-control" name="name" id="name" value="<?php echo ($info["name"]); ?>" placeholder="配置标识">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="title">配置标题 <span class="check-tips">（用于后台显示的配置标题）</span></label>
                                <input type="text" class="form-control" name="title" value="<?php echo ($info["title"]); ?>" id="title" placeholder="配置标题">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-2">
                                <label class="item-label" for="sort">排序<span class="check-tips">（用于分组显示的顺序）</span></label>
                                <input type="number" class="form-control" name="sort" value="<?php echo ($info["sort"]); ?>" id="sort" placeholder="排序">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="type">配置类型<span class="check-tips">（系统会根据不同类型解析配置值）</span></label>
                                <select class="form-control" id="type" name="type">
                                    <?php if(is_array(C("CONFIG_TYPE_LIST"))): $i = 0; $__LIST__ = C("CONFIG_TYPE_LIST");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($type); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="group">配置分组<span class="check-tips">（配置分组 用于批量设置 不分组则不会显示在系统设置中）</span></label>
                                <select class="form-control" id="group" name="group">
                                    <option value="0">不分组</option>
                                    <?php if(is_array(C("CONFIG_GROUP_LIST"))): $i = 0; $__LIST__ = C("CONFIG_GROUP_LIST");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($group); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="value">配置值<span class="check-tips">（配置值）</span></label>
                                <textarea class="form-control" name="value" id="value" rows="6"><?php echo ($info["value"]); ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="extra">配置项<span class="check-tips">（如果是枚举型 需要配置该项）</span></label>
                                <textarea class="form-control" name="extra" id="extra" rows="6"><?php echo ($info["extra"]); ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="remark">说明<span class="check-tips">（配置详细说明）</span></label>
                                <textarea class="form-control" name="remark" id="remark" rows="6"><?php echo ($info["remark"]); ?></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
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


    <script type="text/javascript">
        window.setValue("type", "<?php echo ((isset($info["type"]) && ($info["type"] !== ""))?($info["type"]):0); ?>");
        window.setValue("group", "<?php echo ((isset($info["group"]) && ($info["group"] !== ""))?($info["group"]):0); ?>");
        //导航高亮
        highlight_subnav("<?php echo U('Config/index');?>");
    </script>


</body>
</html>