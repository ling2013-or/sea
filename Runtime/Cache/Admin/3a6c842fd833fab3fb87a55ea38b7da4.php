<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="shortcut icon" href="#" type="image/png">
    <title>后台登录</title>
    <link href="/statics/Admin/css/style.css" rel="stylesheet">
    <link href="/statics/Admin/css/style-responsive.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/statics/Admin/js/html5shiv.js"></script>
    <script src="/statics/Admin/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">

<div class="container">

    <form class="form-signin" action="#" onsubmit="return false" id="login">
        <div class="form-signin-heading text-center">
            <h1 class="sign-title">管理员登录</h1>
        </div>
        <div class="login-wrap">
            <input type="text" name="uname" id="uname" class="form-control" placeholder="用户名" autofocus>
            <input type="password" name="upwd" id="upwd"  class="form-control" placeholder="密码">
            <div class="col-md-6">
            	<div class="row">
           	 		<input type="text" name="vcode" id="vcode" class="form-control" placeholder="验证码">
            	</div>
            </div>
            <div class="col-md-6">
            	<img src="<?php echo U('vcode');?>" id="vcodeImg" width="100%" height="100%" class="img-rounded" alt="">
            </div>
            <?php if(($config["ADMIN_SMS_AUTH"]) == "1"): ?><div class="row col-md-8">
                    <input type="password" name="scode" id="scode"   class="form-control" placeholder="短信验证码">
                </div>
                <div class="col-md-4 "id="getCode" ><span class="btn btn-primary getCode" >获取验证码</span></div><?php endif; ?>

            <button class="btn btn-lg btn-login btn-block" id="submit-btn" type="submit">
                登 录
            </button>

            <!--<label class="checkbox">-->
                <!--<span class="pull-right">-->
                    <!--<a data-toggle="modal" href="#myModal"> 忘记密码?</a>-->
                <!--</span>-->
            <!--</label>-->
	
        </div>
    </form>

        <!-- Modal -->
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">密码找回</h4>
                    </div>
                    <div class="modal-body">
                        <p>请输入您的安全邮箱</p>
                        <input type="text" name="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">

                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                        <button class="btn btn-primary" type="button">提交</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

    </form>

</div>
<!-- Placed js at the end of the document so the pages load faster -->
<script src="/statics/Admin/js/jquery-1.10.2.min.js"></script>
<script src="/statics/Admin/js/bootstrap.min.js"></script>
<script src="/statics/Admin/js/modernizr.min.js"></script>
<script src="/statics/assets/layer/layer.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		var $vcodeImg = $("#vcodeImg");
		var verifyimg = $vcodeImg.attr("src");
		$vcodeImg.click(function(){
			if( verifyimg.indexOf('?')>0){
				$vcodeImg.attr("src", verifyimg+'&random='+Math.random());
			}else{
				$vcodeImg.attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
			}
		});

        $('#submit-btn').on('click', function () {
            if (!$(this).hasClass('btn-disabled')) {
                sendMessage();
            }
        });


        function sendMessage(){
            //过滤空数据
            var uname = $('#uname').val();
            var upwd = $('#upwd').val();
            var vcode = $('#vcode').val();
            if(!uname){layer.tips('请输入用户名', '#uname',{tips:1});return false;}
            if(!upwd){layer.tips('请输入密码', '#upwd',{tips:1});return false;}
            if(!vcode){layer.tips('请输入验证码', '#vcode',{tips:1});return false;}

            var data = {};
            data.uname = uname;
            data.upwd = upwd;
            data.vcode = vcode;

            <?php if(($sms) == "1"): ?>var scode = $('#scode').val();
                if(!scode){layer.tips('请输入短信验证码', '#scode',{tips:1});return false;}
                data.scode = scode;<?php endif; ?>


            $('#submit-btn').addClass('btn-disabled').html('登录中...');

            //后台校验
            $.ajax({
                url:'<?php echo U("Public/login");?>',
                data:data,
                type:'post',
                datatype:'json',
                success:function(res){

                    //清除等待样式
                    if (res.status==0){
                        //输出提示
                        if (res.info == 1 ) {
                            layer.tips('验证码错误！', '#vcode',{tips:[1,'#A32']});
                        } else if (res.info == 2) {
                            layer.tips('用户名不存在！', '#uname',{tips:[1,'#A32']});
                        } else if (res.info == 3) {
                            layer.tips('密码错误！', '#upwd',{tips:[1,'#A32']});
                        }else if (res.info == 5) {
                            layer.tips('短信验证码错误！', '#scode',{tips:[1,'#A32']});
                        }else {
                            layer.alert('您没有登录权限！', {icon: 1,skin: 'layer-ext-moon'})
                        }
                        $('#submit-btn').removeClass('btn-disabled').html('登录');
                        $vcodeImg.click();
                    } else {
                        window.location.href = res.url;
                    }

                },
                error:function(){
                    layer.closeAll('loading');
                    layer.alert('网络连接失败,请重新尝试。', {
                        skin: 'layui-layer-molv' //样式类名
                        ,closeBtn: 0
                    });
                }
            })
        }

        <?php if(($sms) == "1"): ?>//发送短信验证码
        $('#getCode').click(function(){
            if(!$(this).children('span').hasClass('disabled')){
                getcode();
            }

        });

        function getcode(){
            //验证用户名和密码以及验证码
            //检测session中的code是否存在

            //过滤空数据
            var smsAuth = "<?php echo ($config["ADMIN_SMS_AUTH"]); ?>";
            var uname = $('#uname').val();
            var upwd = $('#upwd').val();
            var vcode = $('#vcode').val();
            var scode = $('#scode').val();

            if(!uname){layer.tips('请输入用户名', '#uname',{tips:1});return false;}
            if(!upwd){layer.tips('请输入密码', '#upwd',{tips:1});return false;}
            if(!vcode){layer.tips('请输入验证码', '#vcode',{tips:1});return false;}
            //判断短信验证是否打开
            if(smsAuth){
                //等待样式和连接失败提示
                layer.load();
                setTimeout(function(){
                    layer.closeAll('loading');
                }, 10000);


                //后台校验
                $.ajax({
                    url:'<?php echo U("Public/checkUser");?>',
                    data:{'uname':uname,'upwd':upwd,'vcode':vcode},
                    type:'post',
                    datatype:'json',
                    success:function(res){
                        console.log(res);
                        //清除等待样式
                        layer.closeAll('loading');
                        if (res.status==0){
                            //输出提示
                            if (res.info == 1 ) {
                                layer.tips('验证码错误！', '#vcode',{tips:[1,'#A32']});
                            } else if (res.info == 2) {
                                layer.tips('用户名不存在！', '#uname',{tips:[1,'#A32']});
                            } else if (res.info == 3) {
                                layer.tips('密码错误！', '#upwd',{tips:[1,'#A32']});
                            }else if (res.info == 5) {
                                layer.tips('短信验证码错误！', '#scode',{tips:[1,'#A32']});
                            }else if (res.info == 6) {
                                layer.tips('1分钟之内请勿重复获取', '#scode',{tips:[1,'#6bc5a4']});
                            } else {
                                layer.alert('您没有登录权限！', {icon: 1,skin: 'layer-ext-moon'})
                            }

                        } else {
                            sendpass();
                        }

                    },
                    error:function(){
                        layer.closeAll('loading');
                        layer.alert('网络连接失败,请重新尝试。', {
                            skin: 'layui-layer-molv' //样式类名
                            ,closeBtn: 0
                        });
                    }
                })
            } else {
                layer.alert('短信验证 已关闭！', {
                    skin: 'layui-layer-molv' //样式类名
                    ,closeBtn: 0
                });
            }

        }

        var sec = 60;
        function sendpass(){
            if (sec == 0) {
                $(".getCode").html('获取验证码').removeClass('disabled');
                sec = 60;
            } else {
                $(".getCode").html(sec+'秒后重新发送').addClass('disabled');
                sec--;
                setTimeout(function() {
                    sendpass();
                },1000)
            }

        }<?php endif; ?>
	});
</script>
</body>
</html>