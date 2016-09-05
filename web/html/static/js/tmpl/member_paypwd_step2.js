$(function () {
    var token = getCookie("token");
    var pwd = true;
    var fs = true;
    var key = null;
    if (!token) {
        window.location.href = ApiUrl + "/user/login.html";
        return
    }
    sss();

    function sss() {
        //检测当前支付密码是否设置
        $.ajax({
            type: "post",
            url: ApiUrl + "/Account/check_user.html",
            data: {token: token},
            dataType: "json",
            success: function (e) {

                if (e.status) {
                    if (e.data.pay_pass) {
                        if (pwd) {
                            var i = template.render("pay_pass_old", e);
                        } else {
                            var i = template.render("pay_pass_mobile", e);
                        }
                    } else {
                        var i = template.render("pay_pass_set", e);
                    }
                    $("#pay").html(i);

                }else{
                    if(e.code == 40001){
                        $.sDialog({skin: "block", content: '登陆失效，请重新登录', okBtn: true, cancelBtn: false,okFn:function(){
                            setTimeout(window.location.href = farm+'/user/login.html',3e3)
                        }});
                    }
                }

                $("#mobile").click(function () {
                    fs = false;
                    type = $('#mobile').attr('data-type');
                    if (type == 'mobile' && pwd == true) {
                        $('#mobile').attr('data-type', 'pwd');
                        $('#mobile').html('密码验证');
                        pwd = false;
                        type = null;
                        e.mobile = getCookie('user_phone');
                        var i = template.render("pay_pass_mobile", e);
                        $("#pay").html(i);
                        a();

                    } else if (type == 'pwd' && pwd == false) {
                        $('#mobile').attr('data-type', 'mobile');
                        $('#mobile').html('手机验证');
                        pwd = true;
                        type = null;
                        var i = template.render("pay_pass_old", e);
                        $("#pay").html(i);
                    }
                });
                //发送给当前用户绑定的手机号码
                function a() {

                    //发送短信验证码
                    $("#msend").click(function () {
                        $.ajax({
                            type: "post",
                            url: ApiUrl + "/Sms/send.html",
                            data: {token: token,style: 1},
                            dataType: "json",
                            success: function (e) {
                                if (e.status) {
                                    key = e.data.key;
                                    $("#msend").hide();
                                    $("#code-countdown1").show().find("em").html(60);
                                    //TODO 验证码先放入输入框中（仅测试时用）
                                    document.getElementById('check_code').value = e.data.code;
                                    var a = setInterval(function () {
                                        var e = $("#code-countdown1").find("em");
                                        var t = parseInt(e.html() - 1);
                                        if (t == 0) {
                                            $("#msend").show();
                                            $("#code-countdown1").hide();
                                            clearInterval(a);
                                            //$("#codeimage").attr("src", ApiUrl + "/index.php?act=seccode&op=makecode&k=" + $("#codekey").val() + "&t=" + Math.random())
                                        } else {
                                            e.html(t)
                                        }
                                    }, 1e3)//表示1秒钟一次
                                } else {
                                    $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                                }
                            }
                        });

                    });

                    //提交修改的密码
                    $("#nextform").click(function () {
                        if (!$(this).parent().hasClass("ok")) {
                            return false
                        }

                        var p = $.trim($("#check_code").val());
                        var n = $.trim($("#new_password").val());
                        var r = $.trim($("#repeat_password").val());
                        if(p.length == 0 || n.length == 0 || r.length == 0){
                            $.sDialog({skin: "block", content: '验证码、新密码不能为空！', okBtn: false, cancelBtn: false});
                            return false;
                        }
                        $.ajax({
                            type: "post",
                            url: ApiUrl + "/Member/pay_pass.html",
                            data: {type:2,token: token, code: p, pay_pass_new: n, pay_pass_repeat: r,pay_pass_key:key},
                            dataType: "json",
                            success: function (s) {
                                if (s.status) {
                                    $.sDialog({skin: "block", content: "支付密码设置成功", okBtn: false, cancelBtn: false});
                                    setTimeout("location.href = farm+'/user/setting.html'", 3e3);
                                    key = null;
                                } else {
                                    $.sDialog({skin: "block", content: s.msg, okBtn: false, cancelBtn: false});
                                }
                            }
                        })
                    });
                }

                $("#nextform").click(function () {
                    if (!$(this).parent().hasClass("ok")) {
                        return false
                    }

                    var type = 3;
                    var p = $.trim($("#password").val());
                    if(p.length == 0 ){
                        type = 1;
                    }
                    var n = $.trim($("#new_password").val());
                    var r = $.trim($("#repeat_password").val());
                    $.ajax({
                        type: "post",
                        url: ApiUrl + "/Member/pay_pass.html",
                        data: {type:type,token: token, pay_pass_old: p, pay_pass_new: n, pay_pass_repeat: r},
                        dataType: "json",
                        success: function (e) {
                            if (e.status) {
                                $.sDialog({skin: "block", content: "支付密码设置成功", okBtn: false, cancelBtn: false});
                                setTimeout("location.href = farm+'/user/setting.html'", 3e3)
                            } else {
                                $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                            }
                        }
                    })
                });

            },
            error: function (f) {
                $.sDialog({skin: "block", content: '网络不给力，稍后请重试。', okBtn: false, cancelBtn: false})
            }
        });
    }


});