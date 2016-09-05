$(function () {
    var e = getCookie("token");
    if (!e) {
        window.location.href = farm + "/user/login.html";
        return
    }
    //检测用户是否设置过用户名，以及支付密码


    $("#logoutbtn2").click(function () {
        //window.console.log(123456);
        delCookie('farm_name');
        delCookie('nick_name');
        delCookie('token');
        delCookie('user_email');
        delCookie('user_name');
        delCookie('user_phone');
        $.sDialog({skin: "block", content: '退出成功！', okBtn: false, cancelBtn: false});
        setTimeout(
            window.location.href = farm+'/user/login.html'
            , 3e3);
    })

});