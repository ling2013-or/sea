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
        <h3>预期收获</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">预期收获管理</a>
            </li>
            <li class="active">预期列表</li>
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
                        <div class="pull-right search-form form-inline">
                            <form action="" onsubmit="return false">
                                <input type="text" name="username" class="form-control search-input" value="<?php echo I('username');?>" placeholder="请输入用户名">
                                <a href="javascript:;" class="btn btn-info" id="search">搜索</a>
                            </form>
                        </div>
                    </div>
                    <section id="unseen">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <th>销售方案批次</th>
                                <th>用户名</th>
                                <th>农场名称</th>
                                <th>作物名称</th>
                                <th>销售方案名称</th>
                                <th>种植时间</th>
                                <th>收获时间</th>
                                <th>种植面积</th>
                                <th>预期产量</th>
                                <th>实际产量</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["plan_sn"]); ?></td>
                                        <td><?php echo ($vo["user_name"]); ?></td>
                                        <td><?php echo ($vo["farm_name"]); ?></td>
                                        <td><?php echo ($vo["seed_name"]); ?></td>
                                        <td><?php echo ($vo["plan_name"]); ?></td>
                                        <td><?php echo (date('Y-m-d', $vo["plan_start"])); ?></td>
                                        <td><?php echo (date('Y-m-d', $vo["plan_end"])); ?></td>
                                        <td><?php echo ($vo["plant_area"]); ?></td>
                                        <td><?php echo ($vo["expect_yield"]); ?></td>
                                        <td><?php echo ($vo["real_yield"]); ?></td>
                                        <td>
                                            <?php if($vo["status"] == 0 && $vo['plan_end'] <= $today): ?><a href="javascript:doComple$te(<?php echo ($vo["id"]); ?>)">收益</a>
                                                <?php else: ?>
                                                --<?php endif; ?>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">暂无数据</td>
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


    <script>
        highlight_subnav("<?php echo U('index');?>");
        $("#search").click(function(){
            var url = "<?php echo U('index');?>";
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

        function doComplete(id)
        {
            layer.use('extend/layer.ext.js', function(){
                layer.prompt({
                    title: '实际收益(单位:千克)',
                    formType: 0
                }, function(cont){
                    var cont = parseFloat(cont);
                    layer.confirm('总收益为 <span class="label label-danger">'+cont+' 千克</span>', {
                        btn: ['确定','返回']
                    }, function(){
                        $.ajax({
                            url:'<?php echo U("Expect/doComplete");?>',
                            data:{id:id,real:cont},
                            type:'post',
                            dataType:'json',
                            success:function(res){
                                if (res.status==0) {
                                    layer.msg(res.info);  
                                } else {
                                    layer.msg('操作成功！');
                                    //window.location.reload();  
                                }
                            },
                            error:function(){
                                layer.msg('提交失败');
                                //window.location.reload();
                            }
                        })
                    }, function(){
                        doComplete(id,exp); 
                    }); 
                });
            });
        }
    </script>


</body>
</html>