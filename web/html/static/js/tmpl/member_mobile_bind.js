//手机验证
$(function () {
    //第一步
    var first = false;
    var key = false;
    var token = getCookie("token");
    var m = getCookie("user_phone");
    if (!token) {
        window.location.href = farm + "/user/login.html";
        return
    }
    console.log(m);
    //验证手机号码是否正常
    if((typeof m == "undefined") || (m == null) || (m == '')){
        //跳到设置用户信息界面
        //window.location.href = farm+"/user/setting.html";
        $("#first").attr('style','display:none');
        $("#second").attr('style','display:block');
        first = 'styles';
    }
    $("#phone").html(m);
    //手机规则校验
    $("#mobile").on("blur", function () {
        if ($(this).val() != "" && !/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test($(this).val())) {
            $(this).val(/\d+/.exec($(this).val()))
        }
    });
    //发送给当前用户绑定的手机号码
    $("#msend").click(function () {
        $.ajax({
            type: "post",
            url: ApiUrl + "/Sms/send.html",
            data: {token: token, mobile: m,style:1},
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
                            $("#codeimage").attr("src", ApiUrl + "/index.php?act=seccode&op=makecode&k=" + $("#codekey").val() + "&t=" + Math.random())
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
    //发送给指定用户
    $("#send").click(function () {
        //查看第一步是否验证成功
        if(!first){
            return false;
        }
        var styles = 3;
        if(first == 'styles'){
            styles = 4;
        }
        var a = $.trim($("#mobile").val());
        if(!a){
            $.sDialog({skin: "block", content: "手机号不能为空", okBtn: false, cancelBtn: false});
            return false;
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/Sms/send.html",
            data: {token: token, mobile: a,style:styles,key:key},
            dataType: "json",
            success: function (e) {
                if (e.status) {
                    $("#send").hide();
                    $("#auth_code").removeAttr("readonly");
                    $("#code-countdown2").show().find("em").html(60);
                    //TODO 验证码先放入输入框中（仅测试时用）
                    document.getElementById('auth_code').value = e.data.code;
                    var a = setInterval(function () {
                        var e = $("#code-countdown2").find("em");
                        var t = parseInt(e.html() - 1);
                        if (t == 0) {
                            $("#send").show();
                            $("#code-countdown2").hide();
                            clearInterval(a);
                            //$("#codeimage").attr("src", ApiUrl + "/index.php?act=seccode&op=makecode&k=" + $("#codekey").val() + "&t=" + Math.random());
                            $("#captcha").val("")
                        } else {
                            e.html(t);
                        }
                    }, 3e3)
                } else {
                    $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                    return false;
                }
            }
        })
    });
    //下一步
    $("#nextform").click(function () {
        if (!$(this).parent().hasClass("ok")) {
            return false;
        }
        var a = $.trim($("#check_code").val());
        if (a) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/User/forget.html",
                data: {token: token, mobile: m,code:a,mine:true},
                dataType: "json",
                success: function (e) {
                    if (e.status) {
                        setTimeout(function() {
                            $('#first').attr('style', 'display:none');
                            $('#second').attr('style', 'display:block');
                        }, 1e3);
                        first = true;
                    } else {
                        $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                    }
                }
            })
        }
    });

    $("#update").click(function () {
        if (!$(this).parent().hasClass("ok") || !first) {
            return false
        }
        var s = $.trim($("#auth_code").val());
        var mobile = $.trim($("#mobile").val());
        if (s && mobile) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/User/forget.html",
                data: {token: token, mobile: mobile,code:s},
                dataType: "json",
                success: function (e) {
                    if (e.status) {
                        key = e.data.key;

                        //console.log(e);return false;
                        //修改用户的绑定手机
                        $.ajax({
                            type: "post",
                            url: ApiUrl + "/Member/user_phone.html",
                            data: {token: token, user_phone_new: mobile,key:key},
                            dataType: "json",
                            success: function (e) {
                                if (e.status) {
                                    $.sDialog({skin: "block", content: '修改'+e.msg, okBtn: false, cancelBtn: false});
                                    setTimeout(
                                       window.location.href = farm+'/user/setting.html'
                                        , 6e3);
                                    addCookie('user_phone',mobile)

                                } else {
                                    $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                                }
                            }
                        })
                    } else {
                        $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                        return false;
                    }
                }
            })
        }else{
            return false;
        }
    })
});