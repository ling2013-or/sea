$(function () {
    var token = getCookie("token");
    if (!token) {
        window.location.href = farm + "/user/login.html";
        return
    }
    //加载验证码
    //loadSeccode();
    $("#refreshcode").bind("click", function () {
        loadSeccode()
    });
    template.helper("$getLocalTime", function (e) {
        var t = new Date(parseInt(e) * 1e3);
        var r = "";
        r += t.getFullYear() + "年";
        r += t.getMonth() + 1 + "月";
        r += t.getDate() + "日 ";
        r += t.getHours() + ":";
        r += t.getMinutes();
        return r
    });
    $.sValid.init({
        rules: {rc_sn: "required", captcha: "required"},
        messages: {rc_sn: "请输入平台充值卡号", captcha: "请填写验证码"},
        callback: function (e, r, a) {
            if (e.length > 0) {
                var c = "";
                $.map(r, function (e, r) {
                    c += "<p>" + e + "</p>"
                });
                errorTipsShow(c)
            } else {
                errorTipsHide()
            }
        }
    });
    $("#saveform").click(function () {
        if (!$(this).parent().hasClass("ok")) {
            return false
        }
        if ($.sValid()) {
            var r = $.trim($("#rc_sn").val());
            //获取验证码
            //var a = $.trim($("#captcha").val());
            //var c = $.trim($("#codekey").val());
            $.ajax({
                type: "post",
                url: ApiUrl + "/Charge/add.html",
                data: {token: token, amount: r},
                dataType: "json",
                success: function (e) {
                    if (e.status) {
                        console.log(e);
                        $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false})
                        //location.href = farm + "/account/recharge_list.html"
                    } else {
                        loadSeccode();
                        errorTipsShow("<p>" + e.data.error + "</p>")
                    }
                }
            })
        }
    })
});