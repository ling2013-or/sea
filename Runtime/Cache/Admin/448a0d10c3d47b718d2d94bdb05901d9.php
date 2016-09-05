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
        <h3>农场管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">农场管理</a>
            </li>
            <li class="active">农场列表</li>
        </ul>
    </div>

        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper" style="padding:0 15px">
            
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="operate-body">
                        <div class="pull-left">
                            <a href="<?php echo U('farmadd');?>" class="btn btn-info">添加农场</a>
                            <a href="<?php echo U('type');?>" class="btn btn-info">分类管理</a>
                        </div>
                        <div class="pull-right search-form form-inline">
                            <form action="" onsubmit="return false">
                                <input type="text" name="mobile" class="form-control search-input" value="<?php echo I('mobile');?>" placeholder="请输入手机号码">
                                <input type="text" name="farm_name" class="form-control search-input" value="<?php echo I('farm_name');?>" placeholder="请输入农场名称">
                                <a href="javascript:;" class="btn btn-info" id="search">搜索</a>
                            </form>
                        </div>
                    </div>
                    <section id="unseen">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>农场编号</th>
                                    <th>农场名称</th>
                                    <th>总面积（平米）</th>
                                    <th>农场位置</th>
                                    <th>农场管理员</th>
                                    <th>手机</th>
                                    <th>邮箱</th>
									<th>添加时间</th>
									<th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($list["farm_sn"]); ?></td>
                                    <td><?php echo ($list["farm_name"]); ?></td>
                                    <td><?php echo ($list["area_total"]); ?></td>
                                    <td><?php echo ($list["province"]); ?> <?php echo ($list["city"]); ?> <?php echo ($list["area"]); ?></td>
                                    <td><?php echo ($list["owner_name"]); ?></td>
                                    <td><?php echo ($list["owner_mobile"]); ?></td>
                                    <td><?php echo ($list["owner_email"]); ?></td>
                                    <td><?php echo (date('Y-m-d H:i:s', $list["add_time"])); ?></td>
                                    <td>
                                        <a title="编辑" href="<?php echo U('farmedit', array('farm_id'=>$list['farm_id']));?>">编辑</a>
                                        <a title="农场详情" href="<?php echo U('block', array('farm_id'=>$list['farm_id'], 'farm_name'=>$list['farm_name']));?>">农场分类详情</a>
                                        <a title="物流" href="<?php echo U('express', array('farm_id'=>$list['farm_id'], 'farm_name'=>$list['farm_name']));?>">物流</a>
                                        <a class="confirm ajax-get" title="删除" href="<?php echo U('farmdel', array('farm_id'=>$list['farm_id']));?>">删除</a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">暂无数据</td>
                                </tr><?php endif; ?>
                            </tbody>
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


    <script type="text/javascript">
		// 导航高亮
		highlight_subnav('<?php echo U("Farm/farm");?>');

		// 搜索
        $("#search").click(function(){
            var url = "<?php echo U();?>";
            var query  = $('form').serialize();
            query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
            query = query.replace(/^&/g,'');
            if( url.indexOf('?')>0 ){
                url += '&' + query;
            }else{
                url += '?' + query;
            }
            window.location.href = url;
        });
        //回车搜索
        $(".search-input").keyup(function(e){
            if(e.keyCode === 13){
                $("#search").click();
                return false;
            }
        });
    </script>


</body>
</html>