<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>农场管理平台</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="/statics/Admin/js/ios-switch/switchery.css" />


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
        <h3>支付方式管理</h3>
        <ul class="breadcrumb">
            <li>
                <a href="#">支付方式</a>
            </li>
            <li class="active">编辑（<?php echo ($payment["name"]); ?>）</li>
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
                        <?php if($payment['code'] == 'chinabank'): ?><!-- 网银在线支付 -->
                            <input type="hidden" name="config_name" value="chinabank_account,chinabank_key" />
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">网银在线商户号</label>
                                    <?php if(isset($payment['config']['chinabank_account'])): ?><input type="text" class="form-control" name="chinabank_account" value="<?php echo ($payment["config"]["chinabank_account"]); ?>" placeholder="网银在线商户号" />
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="chinabank_account" value="" placeholder="网银在线商户号" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">网银在线密钥</label>
                                    <?php if(isset($payment['config']['chinabank_account'])): ?><input type="text" class="form-control" name="chinabank_key" value="<?php echo ($payment["config"]["chinabank_account"]); ?>" placeholder="网银在线密钥" />
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="chinabank_key" value="" placeholder="网银在线密钥" /><?php endif; ?>
                                </div>
                            </div>
                        <?php elseif($payment['code'] == 'tenpay'): ?>
                            <!-- 财付通 -->
                            <input type="hidden" name="config_name" value="tenpay_account,tenpay_key" />
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">财付通商户号</label>
                                    <?php if(isset($payment['config']['tenpay_account'])): ?><input type="text" class="form-control" name="tenpay_account" value="<?php echo ($payment["config"]["tenpay_account"]); ?>" placeholder="财付通商户号" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="tenpay_account" value="" placeholder="财付通商户号" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">财付通密钥</label>
                                    <?php if(isset($payment['config']['tenpay_key'])): ?><input type="text" class="form-control" name="tenpay_key" value="<?php echo ($payment["config"]["tenpay_key"]); ?>" placeholder="财付通密钥" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="tenpay_key" value="" placeholder="财付通密钥" /><?php endif; ?>
                                </div>
                            </div>
                        <?php elseif($payment['code'] == 'alipay'): ?>
                            <!-- 支付宝 -->
                            <input type="hidden" name="config_name" value="alipay_service,alipay_account,alipay_key,alipay_partner" />
                            <input type="hidden" name="alipay_service" value="create_direct_pay_by_user" />
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">支付宝账号</label>
                                    <?php if(isset($payment['config']['alipay_account'])): ?><input type="text" class="form-control" name="alipay_account" value="<?php echo ($payment["config"]["alipay_account"]); ?>" placeholder="支付宝账号" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="alipay_account" value="" placeholder="支付宝账号" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">交易安全校验码（key）</label>
                                    <?php if(isset($payment['config']['alipay_key'])): ?><input type="text" class="form-control" name="alipay_key" value="<?php echo ($payment["config"]["alipay_key"]); ?>" placeholder="交易安全校验码（key）" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="alipay_key" value="" placeholder="交易安全校验码（key）" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">合作者身份（partner ID）</label>
                                    <?php if(isset($payment['config']['alipay_partner'])): ?><input type="text" class="form-control" name="alipay_partner" value="<?php echo ($payment["config"]["alipay_partner"]); ?>" placeholder="合作者身份（partner ID）" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="alipay_partner" value="" placeholder="合作者身份（partner ID）" /><?php endif; ?>
                                </div>
                            </div>
                            <?php elseif($payment['code'] == 'wxpay'): ?>
                            <!-- 支付宝 -->
                            <input type="hidden" name="config_name" value="appid,appsecret,encodingaeskey,token,mch_id,key" />
                            <input type="hidden" name="alipay_service" value="create_direct_pay_by_user" />
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">APPID（公众账号ID）</label>
                                    <?php if(isset($payment['config']['appid'])): ?><input type="text" class="form-control" name="appid" value="<?php echo ($payment["config"]["appid"]); ?>" placeholder="应用ID" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="appid" value="" placeholder="应用ID" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">APPSECRET（公众号上的应用密钥）</label>
                                    <?php if(isset($payment['config']['appsecret'])): ?><input type="text" class="form-control" name="appsecret" value="<?php echo ($payment["config"]["appsecret"]); ?>" placeholder="应用密钥）" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="appsecret" value="" placeholder="应用密钥" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">EncodingAesKey（ 公众平台上，开发者设置的EncodingAESKey）</label>
                                    <?php if(isset($payment['config']['encodingaeskey'])): ?><input type="text" class="form-control" name="encodingaeskey" value="<?php echo ($payment["config"]["encodingaeskey"]); ?>" placeholder="消息加解密密钥" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="encodingaeskey" value="" placeholder="消息加解密密钥" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">TOKEN（公众平台上，开发者设置的Token）</label>
                                    <?php if(isset($payment['config']['token'])): ?><input type="text" class="form-control" name="token" value="<?php echo ($payment["config"]["token"]); ?>" placeholder="令牌" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="token" value="" placeholder="令牌" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">MCH_ID（微信支付分配的商户号）</label>
                                    <?php if(isset($payment['config']['mch_id'])): ?><input type="text" class="form-control" name="mch_id" value="<?php echo ($payment["config"]["token"]); ?>" placeholder="商户号码" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="mch_id" value="" placeholder="商户号码" /><?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label class="item-label">key（微信支付分配的商户密钥）</label>
                                    <?php if(isset($payment['config']['key'])): ?><input type="text" class="form-control" name="key" value="<?php echo ($payment["config"]["key"]); ?>" placeholder="商户号码" />
                                        <?php else: ?>
                                        <input type="text" class="form-control" name="key" value="" placeholder="商户密钥" /><?php endif; ?>
                                </div>
                            </div><?php endif; ?>
                        <div class="row slide-toggle">
                            <div class="form-group col-lg-4">
                                <label class="item-label">启用</label>
                                <input type="checkbox" class="js-switch-blue" value="1" name="status" <?php if($payment["status"] == 1): ?>checked<?php endif; ?>/>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?php echo ($payment["id"]); ?>">
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


    <script src="/statics/Admin/js/ios-switch/switchery.js" ></script>
    <script>
        var blue = document.querySelector('.js-switch-blue');
        var switchery = new Switchery(blue, { color: '#41b7f1' });
        highlight_subnav("<?php echo U('index');?>");
    </script>


</body>
</html>