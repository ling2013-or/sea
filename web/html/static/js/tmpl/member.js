/**
 * shop
 * @param e
 * @returns {*}
 */
$(function () {
    if (getQueryString("token") != "") {
        var a = getQueryString("key");
        var e = getQueryString("username");
        addCookie("key", a);
        addCookie("username", e)
    } else {
        var a = getCookie("key");
        var account = getCookie('user_name');
    }
    //TODO 暂时的  a的值
    var a = true;
    var token = getCookie('token');
    //获取订单统计

    $.ajax({
        type: "post",
        url: ApiUrl + '/Order/count.html',
        data: {token: token},
        dataType: "json",
        success: function (a) {
            if (a.status) {
                var nick_name = getCookie('nick_name');
                var user_name = getCookie('user_name');
                var e = '<div class="member-info">' + '<div class="user-avatar"> <a href="userinfo.html"><img id="user_avatar" src="' + getCookie('user_avatar') + '"/></a> </div>' + '<div class="user-name"> <span>' + (nick_name ? nick_name : user_name) + '</sup></span> </div>' + "</div>";
                $(".member-top").html(e);
                var e = '<li><a href="../goodsorder/list.html">' + (a.data.obligation > 0 ? "<em></em>" : '') + '<i class="cc-01"></i><p>待付款</p></a></li>' + '<li><a href="../goodsorder/list.html?data-state=state_receive"> ' + (a.data.received > 0 ? "<em></em>" : '') + '<i class="cc-02"></i><p>待收货</p></a></li>' + '<li><a href="../goodsorder/list.html?data-state=state_shipped">' + (a.data.shipped > 0 ? "<em></em>" : '') + '<i class="cc-03"></i><p>待发货</p></a></li>' + '<li><a href="../goodsorder/list.html?data-state=state_noeval">' + (a.data.comment > 0 ? "<em></em>" : '') + '<i class="cc-04"></i><p>待评价</p></a></li>' + '<li><a href="../goodsorder/list.html?data-state=state_cancel">' + '<i class="cc-05"></i><p>已取消</p></a></li>';
                $("#order_ul").html(e);
                var e = '<li><a href="../account/mycash.html "><i class="cc-06"></i><p>余额</p></a></li>' + '<li><a href="../account/mycash.html?data-state=state_total"><i class="cc-07"></i><p>总额</p></a></li>' + '<li><a href="../account/mycash.html?data-state=state_ty"><i class="cc-08"></i><p>投入</p></a></li>' + '<li><a href="../account/mycash.html?data-state=state_exp"><i class="cc-09"></i><p>消费额</p></a></li>' + '<li><a href="../account/mycash.html?data-state=state_forzen"><i class="cc-10"></i><p>冻结额</p></a></li>';
                $("#asset_ul").html(e);
                //判断用户是否设置账户名
                if (account.length == 0) {
                    var con = $("#description_info").html();
                    $.sDialog({
                        content: "请设置账户名",
                        "width": 100,
                        "height": 100,
                        "cancelBtn": false,
                        "lock": true,
                        okFn: function () {
                            window.location.href = farm + '/user/userinfo.html';
                        }
                    });
                }
            } else {
                console.log(444555);

                var i = '<div class="member-info">' + '<a href="'+farm+'/user/login.html" class="default-avatar" style="display:block;"></a>' + '<a href='+farm+'"/user/login.html" class="to-login">点击登录</a>' + "</div>" ;
                $(".member-top").html(i);
                var i = '<li><a href=' + farm + '"/user/login.html"><i class="cc-01"></i><p>待付款</p></a></li>' + '<li><a href=' + farm + '"/user/login.html"><i class="cc-02"></i><p>待收货</p></a></li>' + '<li><a href=' + farm + '"/user/login.html"><i class="cc-03"></i><p>待自提</p></a></li>' + '<li><a href=' + farm + '"/user/login.html"><i class="cc-04"></i><p>待评价</p></a></li>' + '<li><a href=' + farm + '"/user/login.html"><i class="cc-05"></i><p>退款/退货</p></a></li>';
                $("#order_ul").html(i);
                return false
            }
            return false
        }

    });


    $.scrollTransparent()
});