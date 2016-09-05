$(function () {
    var e = getCookie("token");
    var key = false;
    //获取用户的手机号码
    var m = getCookie('user_phone');
    if(typeof (m) == 'undefined'){
        //用户未登录
        $.sDialog({
            skin: "red",
            content: "设置您的手机号码!",
            okBtn: true,
            cancelBtn: true,
            okFn: function () {
                window.location.href = farm+"/user/login.html";
            },
            cancelFn:function(){
                window.location.href = farm+"/user/setting.html"
            }
        })
    }
    if (!checkLogin(e)) {
        //用户未登录
        $.sDialog({
            skin: "red",
            content: "请登录!",
            okBtn: true,
            cancelBtn: true,
            okFn: function () {
                window.location.href = farm+"/user/login.html";
            },
            cancelFn:function(){
                window.location.href = farm+"/user/setting.html"
            }
        })
    }
    //将手机号码输入到头部
    $("#mobile").html(m);
    //发送给当前用户绑定的手机号码


    //确认修改密码
    $("#updateCode").click(function () {
        if (!$(this).parent().hasClass("ok")) {
            return false
        }
        var mine = $(this);
        var old = $.trim($("#old").val());
        var password = $.trim($("#password").val());
        var repeat = $.trim($("#password1").val());
        if((typeof password == 'null') || (typeof repeat == 'null') || (typeof old == 'null')){
            $.sDialog({skin:"block",content:"密码不能为空",okBtn:false,cancelBtn:false});
            return false;
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/user/edit.html",
            data: {token: e,password:password,password_repeat:repeat,old:old},
            dataType: "json",
            beforeSend:function(){
                mine.parent().removeClass("ok")
            },
            success: function (e) {
                if (e.status) {
                    $.sDialog({skin: "block", content: "手机验证成功，正在跳转", okBtn: false, cancelBtn: false});
                    setTimeout(
                       window.location.href = farm+"/user/setting.html", 6e3);
                } else {
                    $.sDialog({skin: "block", content: e.msg, okBtn: false, cancelBtn: false});
                }
            }
        })

    })
});