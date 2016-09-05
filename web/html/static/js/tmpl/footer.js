$(function () {
    var a = getCookie("token");
    var e = '<div class="nctouch-footer-wrap posr">' + '<div class="nav-text">';
    if (a) {
        e += '<a href="' + farm + '/goods/list.html">我的商城</a>' + '<a id="logoutbtn" href="javascript:void(0);">退出</a>' + '<a href="' + farm + '/user/back.html">反馈</a>'
    } else {
        e += '<a href="' + farm + '/user/login.html">登录</a>' + '<a href="' + farm + '/user/login.html?data-type=register">注册</a>' + '<a href="' + farm + '/user/login.html">反馈</a>'
    }
    // e += '<a href="javascript:void(0);" class="gotop">返回顶部</a>' + "</div>" + '<div class="nav-pic">' + '<a href="' + farm + '/index.php?act=mb_app" class="app"><span><i></i></span><p>客户端</p></a>' + '<a href="javascript:void(0);" class="touch"><span><i></i></span><p>触屏版</p></a>' + '<a href="' + farm + '" class="pc"><span><i></i></span><p>电脑版</p></a>' + "</div>" + '<div class="copyright">' + 'Copyright&nbsp;&copy;&nbsp;2007-2015 网城天创<a href="javascript:void(0);">ShopNC.net</a>版权所有' + "</div>";
    e += '';
    $("#footer").html(e);
    $("#logoutbtn").click(function () {
        delCookie('farm_name');
        delCookie('nick_name');
        delCookie('token');
        delCookie('user_email');
        delCookie('user_name');
        delCookie('user_phone');
        setTimeout(
            window.location.href = farm+'/user/login.html'
            , 3e3);
    })
});