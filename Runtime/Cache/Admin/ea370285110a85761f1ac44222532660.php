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
        <h3>系统</h3>
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo U('Group/index');?>">系统用户组</a>
            </li>
            <li class="active">编辑用户组</li>
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
                        <input type="hidden" name="group_id" value="<?php echo ($info["group_id"]); ?>">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label" for="name">用户组名称 <span class="check-tips">（用于显示的用户组名称）</span></label>
                                <input type="text" class="form-control" name="group_name" id="name" value="<?php echo ($info["group_name"]); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="item-label">用户组状态<span class="check-tips">（是否启用管理组）</span></label>
                                <label><input type="radio" name="status" value="1" <?php if($info["status"] == 1): ?>checked="true"<?php endif; ?>> 启用 </label>
                                &nbsp;
                                <label><input type="radio" name="status" value="0" <?php if($info["status"] == 0): ?>checked="true"<?php endif; ?>> 禁用 </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-5">
                                <label class="item-label" for="auth">用户组权限 <span class="check-tips"></span></label>
                                <div class="col-md-12" id="checkControl" onselectstart="return false">
                                    <label><input type="checkbox" id="checkAll"> 全选</label> 
                                    <label><input type="checkbox" id="revCheckAll"> 反选</label> 
                                    <label><input type="checkbox" id="cancelAll"> 取消</label> 
                                </div>
                                <hr>
                                <div id="moduleList" onselectstart="return false">
                                    <h5 class="col-md-12">菜单权限</h5>
                                    <?php if(is_array($moduleList)): $i = 0; $__LIST__ = $moduleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i; if($list['is_menu'] == 1): ?><label class="col-md-4"><input type="checkbox" name="auth[]" <?php if(in_array(($list["module_id"]), is_array($groupAuth)?$groupAuth:explode(',',$groupAuth))): ?>checked="true"<?php endif; ?> value="<?php echo ($list["module_id"]); ?>"> <?php echo ($list["module_name"]); ?></label><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                    <hr>
                                    <h5 class="col-md-12">非菜单权限</h5>
                                    <?php if(is_array($moduleList)): $i = 0; $__LIST__ = $moduleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i; if($list['is_menu'] == 0): ?><label class="col-md-4"><input type="checkbox" name="auth[]" <?php if(in_array(($list["module_id"]), is_array($groupAuth)?$groupAuth:explode(',',$groupAuth))): ?>checked="true"<?php endif; ?> value="<?php echo ($list["module_id"]); ?>"> <?php echo ($list["module_name"]); ?></label><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
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


    <script type="text/javascript">
        //导航高亮
        highlight_subnav("<?php echo U('Group/index');?>");
        $(function(){
            //全选
            $('#checkAll').click(function(){
                if($(this).is(':checked')){
                    $('#revCheckAll').removeAttr('checked');
                    $('#cancelAll').removeAttr('checked');
                    $('#moduleList input').attr('checked','true');
                }else{
                    return false;
                } 
            });

            //反选
            $('#revCheckAll').click(function(){
                if($(this).is(':checked')){
                    $('#checkAll').removeAttr('checked');
                    $('#cancelAll').removeAttr('checked');
                    $('#moduleList input').each(function(){
                        if($(this).is(':checked')){
                            $(this).removeAttr('checked');
                        }else{
                            $(this).attr('checked','true');
                        }
                    });
                }else{
                    return false;
                } 
            });

            //取消
            $('#cancelAll').click(function(){
                $('#checkAll').removeAttr('checked');
                $('#revCheckAll').removeAttr('checked');
                $('#moduleList input').removeAttr('checked');
            })
        })
    </script>


</body>
</html>