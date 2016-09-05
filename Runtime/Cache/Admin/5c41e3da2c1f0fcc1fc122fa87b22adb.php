<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>一品农夫</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    
    <style>
        thead{background:#F3F3F3;}
        tfoot{background:#F8F8F8;}
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
        <h3>订单</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">交易订单</a>
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
                    <div class="operate-body">
                        <div class="pull-right search-form form-inline">
                            <form action="" onsubmit="return false">

                                下单时间：
                                <input type="text" name="start_time" value="<?php echo I('start_time');?>" class="laydate-icon form-control search-input" id="start" placeholder="开始时间"/>
                                <input type="text" name="end_time" value="<?php echo I('end_time');?>" class="form-control search-input laydate-icon" id="end" placeholder="结束时间"/>

                                <select name="kw" class="form-control">
                                    <option value="1" <?php if(I('kw') == 1): ?>selected="true"<?php endif; ?>>支付单号</option>
                                    <option value="2" <?php if(I('kw') == 2): ?>selected="true"<?php endif; ?>>用户名</option>
                                </select>
                                <input type="text" name="vw" class="form-control search-input" value="<?php echo I('vw');?>">
                                <a href="javascript:;" class="btn btn-info" id="search">搜索</a>
                                <a href="<?php echo U();?>" class="btn btn-primary" id="search">重置</a>
                            </form>
                        </div>
                    </div>
                    <section id="unseen">

                            <?php if(!empty($lists)): if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td colspan="7">
                                                    <?php if($row['is_platform'] == 0): if($row['is_platform'] == 0): ?>真实用户
                                                            <?php else: ?>
                                                            平台用户<?php endif; endif; ?>
                                                    &nbsp;
                                                    支付单号：<?php echo ($row["payment_sn"]); ?>&nbsp;
                                                    用户：<?php echo ($row["user_name"]); ?>&nbsp;
                                                    下单时间：<?php echo (date("Y-m-d H:i:s", $row["add_time"])); ?>
                                                    <b class="text-warning pull-right">共计：<?php echo ($row["pay_total"]); ?></b>
                                                </td>
                                            </tr>   
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td width="120">订单号</td>
                                            <td>方案名称</td>
                                            <td>存储方式/费用(元)</td>
                                            <td>购买面积(平米)</td>
                                            <td>方案单价(元/平米)</td>
                                            <td>收益状态</td>
                                            <td class="text-right">支付总额(元)</td>
                                        </tr>
                                        <?php if(!empty($row["extend_sell_order"])): if(is_array($row["extend_sell_order"])): foreach($row["extend_sell_order"] as $key=>$list): ?><tr>
                                                    <td><?php echo ($list["order_sn"]); ?></td>
                                                    <td><?php echo ($list["plan_name"]); ?></td>
                                                    <td><?php echo ($list["storage_name"]); ?> ( <?php echo ($list["storage_price"]); ?> )</td>
                                                    <td><?php echo ($list["order_area"]); ?></td>
                                                    <td><?php echo ($list["order_price"]); ?></td>
                                                    <td>
                                                        <?php if($list['status'] == 2): ?><a href="#">已收益</a>
                                                            <?php else: ?>
                                                            <a href="<?php echo U('income',array('id'=>$list['order_id']));?>" class="text-muted">未收益</a><?php endif; ?>
                                                    </td>
                                                    <td class="text-right"><?php echo ($list["pay_total"]); ?></td>
                                                </tr><?php endforeach; endif; endif; ?>
                                        </tbody>
                                        
                                    </table><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                                <div class="alert alert-info text-center">
                                    暂无数据
                                </div><?php endif; ?>
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
        var start = {
            elem: '#start',
            format: 'YYYY-MM-DD',
            //min: '2015-10-10', //设定最小日期为当前日期
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
            //min: '2015-10-10',
            max: laydate.now(), //最大日期
            istoday: true,
            choose: function(datas){
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate.skin('molv');
        laydate(start);
        laydate(end);

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
    </script>


</body>
</html>