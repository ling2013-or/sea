$(function () {
    var first = false;
    var token = getCookie("token");
    var m = getCookie("user_phone");
    if (!token) {
        window.location.href = ApiUrl + "/user/login.html";
        return
    }

    //验证手机号码是否正常
    if((typeof m == "undefined") || (m == null)){
        //跳到设置用户信息界面
        window.location.href = farm+"/user/setting.html";
        return
    }
    console.log(m);
    $("#mobile").html(m);
    //获取图形验证码
    //loadSeccode();
    //$("#refreshcode").bind("click", function () {
    //    loadSeccode()
    //});

    $("#send").click(function () {
        $.ajax({
            type: "post",
            url: ApiUrl + "/Sms/send.html",
            data: {token: token, mine:true},
            dataType: "json",
            success: function (e) {
                if (e.status) {
                    $("#send").hide();
                    $("#auth_code").removeAttr("readonly");
                    $(".code-countdown").show().find("em").html(60);
                    //TODO 验证码先放入输入框中（仅测试时用）
                    document.getElementById('auth_code').value = e.data.code;
                    var a = setInterval(function () {
                        var e = $(".code-countdown").find("em");
                        var t = parseInt(e.html() - 1);
                        if (t == 0) {
                            $("#send").show();
                            $(".code-countdown").hide();
                            clearInterval(a);
                            $("#captcha").val("")
                        } else {
                            e.html(t)
                        }
                    }, 1e3)
                } else {
                    $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                }
            }
        })
    });
    $("#nextform").click(function () {
        if (!$(this).parent().hasClass("ok")) {
            return false
        }
        var a = $.trim($("#auth_code").val());
        if (a) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/User/forget.html",
                data: {token: token, code: a,mine:true},
                dataType: "json",
                success: function (e) {
                    if(e.status){
                        $('#check_mobile').attr('style',"display:none");
                        $('#pwd').attr('style',"display:block");
                        first = true;
                    }else {
                        $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                    }
                }
            })
        }
    });

    $("#update").click(function () {
        if (!$(this).parent().hasClass("ok")) {
            return false
        }
        var a = $.trim($("#auth_code").val());
        if (a) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/Member/pay_pass.html",
                data: {token: token, auth_code: a},
                dataType: "json",
                success: function (e) {
                    if(e.status){
                        $.sDialog({skin: "block", content: '修改'+e.msg, okBtn: false, cancelBtn: false});
                        setTimeout(
                            window.location.href = farm+'/user/setting.html'
                            , 1e3);
                    }else {
                        $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                    }
                }
            })
        }
    });
    $("#payupdate").click(function () {
        if (!$(this).parent().hasClass("ok")) {
            return false
        }
        var a = $.trim($("#auth_code").val());
        if (a) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/Member/pay_pass.html",
                data: {key: e, pay_pass_old: a,pay_pass_new:b,pay_pass_repeat:c},
                dataType: "json",
                success: function (e) {
                    if(e.status){
                        $.sDialog({skin: "block", content: '修改'+e.msg, okBtn: false, cancelBtn: false});
                        setTimeout(
                            window.location.href = farm+'/user/setting.html'
                            , 1e3);
                    }else {
                        $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                    }
                }
            })
        }
    })

});