$(function () {
    var token = getCookie("token");
    if (token === null) {
        window.location.href = WapSiteUrl + "/user/login.html";
        return
    }
    $("#feedbackbtn").click(function () {
        var a = $("#feedback").val();
        console.log(a);
        if (a == "") {
            $.sDialog({skin: "red", content: "请填写反馈内容", okBtn: false, cancelBtn: false});
            return false
        }
        $.ajax({
            url: ApiUrl + "/Feedback/index.html",
            type: "post",
            dataType: "json",
            data: {token: token, content: a},
            success: function (e) {
                if (checkLogin(token)) {
                    if (e.status) {
                        $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                        setTimeout(function () {
                            window.location.href = farm + "/user/setting.html"
                        }, 2e3)
                    } else {
                        $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                        return false
                    }
                }else{
                    $.sDialog({skin: "red", content: '您还未登录，请登录！', okBtn: false, cancelBtn: false});
                    return false
                }
            }
        })
    })
});