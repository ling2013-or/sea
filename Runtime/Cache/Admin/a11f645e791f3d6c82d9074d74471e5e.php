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
        #img_show span,#img_show_multi span{
            position: relative;
            display: inline-block;
        }

        #img_show img,#img_show_multi img{
            margin-right: 5px;
            margin-bottom: 5px;
        }

        #img_show a,#img_show_multi a{
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
        <h3>轮播图管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">轮播图</a>
            </li>
            <li class="active">轮播图列表</li>
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
                            <a href="<?php echo U('add');?>" class="btn btn-info">添加</a>
                        </div>
                        <div class="pull-right search-form form-inline">
                            <form action="" onsubmit="return false">
                                <input type="text" name="name" class="form-control search-input" value="<?php echo I('name');?>" placeholder="请输入计划标题">
                                <a href="javascript:;" class="btn btn-info" id="search">搜索</a>
                            </form>
                        </div>
                    </div>
                    <section id="unseen">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>标题</th>
                                    <th>数量</th>
                                    <th>添加时间</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($row["id"]); ?></td>
                                    <td><?php echo ($row["title"]); ?></td>
                                    <td><?php echo ($row["num"]); ?></td>
                                    <td><?php echo (date('Y-m-d',$row["add_time"])); ?></td>
                                    <td>
                                        <?php switch($row['status']): case "0": ?><span class="label label-primary">未发布</span>
                                                <a title="禁用" class="confirm ajax-get" href="<?php echo U('dopush',array('id'=>$row['id'],'status'=>1));?>">发布</a><?php break;?>
                                            <?php case "1": ?><span class="label label-primary">已发布</span>
                                                <a title="启用" class="confirm ajax-get" href="<?php echo U('dopush',array('id'=>$row['id'],'status'=>0));?>">下架</a><?php break;?>
                                        <?php default: ?>
                                            --<?php endswitch;?>
                                    </td>
                                    <td>
                                        <?php if($row["status"] == 0): ?><a title="编辑" href="<?php echo U('edit?id='.$row['id']);?>">编辑</a><?php endif; ?>
                                        <a class="confirm ajax-get" title="删除" href="<?php echo U('del?id='.$row['id']);?>">删除</a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">暂无数据</td>
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


    <script>
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



        //批量收获
        function doComplete(id,exp)
        {
            layer.use('extend/layer.ext.js', function(){
                layer.prompt({
                    title: '设置实际收益的单位产量',
                    formType: 0
                }, function(cont){
                    var cont = parseFloat(cont);
                    layer.confirm('用户将获得 <span class="label label-danger">'+cont+' 千克/平米</span> 的收益', {
                        btn: ['确定','返回']
                    }, function(){
                        $.ajax({
                            url:'<?php echo U("Sellplan/docomplete");?>',
                            data:{id:id,income:cont},
                            type:'post',
                            dataType:'json',
                            success:function(res){
                                if (res.status==0) {
                                    layer.msg(res.info);
                                } else {
                                    layer.msg('操作成功！');
                                    setTimeout(function(){
                                        window.location.reload();
                                    },800);
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