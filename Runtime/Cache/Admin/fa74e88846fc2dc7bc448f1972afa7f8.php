<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>农场管理平台</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    <style type="text/css">
        body{padding:20px 10px;background: #f3f3f3;}
        .panel-body{height:320px;overflow-y: auto;}
        .list-group li{cursor: default;}
    </style>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/statics/Admin/js/html5shiv.js"></script>
    <script src="/statics/Admin/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="sticky-header">
<div class="col-xs-6">
    <div class="panel">
        <header class="panel-heading">当前组管理员</header>
        <div class="panel-body">
            <ul class="list-group"  id="group_admin_list" role="form">
                <?php if(is_array($group_admin_list)): foreach($group_admin_list as $key=>$row): ?><li class="list-group-item text-primary" data="<?php echo ($row["admin_id"]); ?>"><i class="fa fa-user"></i> <?php echo ($row["admin_name"]); ?></li><?php endforeach; endif; ?>
            </ul>
        </div>
    </div>
</div>
<div class="col-xs-6">
    <div class="panel">
        <header class="panel-heading">所有网站管理员</header>
        <div class="panel-body">
            <ul class="list-group"  id="admin_list" role="list">
                <?php if(is_array($admin_list)): foreach($admin_list as $key=>$row): ?><li class="list-group-item " data="<?php echo ($row["admin_id"]); ?>">
                        <i class="fa fa-user"></i> <?php echo ($row["admin_name"]); ?>
                        <span class="pull-right small"><?php if($row['group_name'] == ''): ?>无分组<?php else: echo ($row['group_name']); endif; ?> <i class="fa fa-group"></i></span>
                    </li><?php endforeach; endif; ?>
            </ul>
        </div>
    </div>
</div>
<div class="col-xs-12 text-center">
    <button class="btn btn-success" id="btn">确 定</button>
    &nbsp;&nbsp;
    <button class="btn btn-info" onclick="window.location.reload();">重 置</button>
    <p class="help-block small">点击管理员名称即可左右切换</p>
</div>
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
<script type="text/javascript">

    //左右切换
    $('.list-group li').click(function(){
        if ($(this).closest('ul').attr('role') == 'list') {
            $(this).appendTo('#group_admin_list');  
        } else {
            $(this).appendTo('#admin_list');
        }
    });

    //确认提交
    $('#btn').click(function(){
        var group_admin_list = new Array();
        var list = $('#group_admin_list li');
        for (var i=0;i<list.length;i++) {
            group_admin_list[group_admin_list.length] = $(list[i]).attr('data');
        }
        $.post('<?php echo U("admin");?>',{id:<?php echo ($id); ?>,admin:group_admin_list},function(res){
            if (res.status == 0) {
              layer.msg(res.info, {icon: 5});
            } else {
              layer.msg(res.info, {icon: 6});
              setTimeout(function(){
                window.location.reload();
              },800);
            }
        });
    });
</script>
</body>
</html>