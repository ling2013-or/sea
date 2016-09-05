$(function () {
    var token = getCookie("token");
    checkLogin(token);
    v();
    //获取订单详情
    function v(t) {
        var a = getQueryString('order_id');
        $.ajax({
            type: "post",
            url: ApiUrl + "/Order/detail",
            data: {token: token, order_id: a},
            dataType: "json",
            success: function (r) {
                //checkLogin(r.login);
                if (r.status) {
                    r.farm = farm;
                    r.ApiUrl = ApiUrl;
                    template.helper("ccc", function (o) {
                        var n = 0;
                        for (var i in o) {
                            n++;
                        }
                        return n;
                    });
                    template.helper("$getLocalTime", function (e) {
                        if(e == '' || e == 'undefined' || e == null){
                            return 0;
                        }
                        var t = new Date(parseInt(e) * 1e3);
                        var r = "";
                        r += t.getFullYear() + "年";
                        r += t.getMonth() + 1 + "月";
                        r += t.getDate() + "日 ";
                        r += t.getHours() + ":";
                        r += t.getMinutes();
                        return r
                    });
                    $("#order-info-container").html(template.render("order-info-tmpl", r));
                    $(".cancel-order").click(e);
                    $(".sure-order").click(o);
                    $(".evaluation-order").click(d);
                    $(".evaluation-again-order").click(a);
                    $(".all_refund_order").click(n);
                    $(".goods-refund").click(c);
                    $(".goods-return").click(_);
                    $(".viewdelivery-order").click(l);
                    if (r.deliver_info) {
                        $("#delivery_content").html(r);
                        $("#delivery_time").html(r)
                    }
                }else{
                    if(r.code == 40001){
                        z = '登录超时！';
                        $.sDialog({skin: "red", content: z, okBtn: true, cancelBtn: true,okFn: function () {
                            window.location.href = farm+"/user/login.html";
                        }});
                    }else{
                        z = r.msg;
                        $.sDialog({skin: "red", content: z, okBtn: false, cancelBtn: false});

                    }
                }
            }

        });
    }

    function e() {
        var r = $(this).attr("order_id");
        $.sDialog({
            content: "确定取消订单？", okFn: function () {
                t(r)
            }
        })
    }

    function t(e) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_cancel",
            data: {order_id: e, key: r},
            dataType: "json",
            success: function (r) {
                if (r.datas && r.datas == 1) {
                    window.location.reload()
                }
            }
        })
    }

    function o() {
        var r = $(this).attr("order_id");
        $.sDialog({
            content: "确定收到了货物吗？", okFn: function () {
                i(r)
            }
        })
    }

    function i(e) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_receive",
            data: {order_id: e, key: r},
            dataType: "json",
            success: function (r) {
                if (r.datas && r.datas == 1) {
                    window.location.reload()
                }
            }
        })
    }

    function d() {
        var r = $(this).attr("order_id");
        location.href = WapSiteUrl + "/tmpl/member/member_evaluation.html?order_id=" + r
    }

    function a() {
        var r = $(this).attr("order_id");
        location.href = WapSiteUrl + "/tmpl/member/member_evaluation_again.html?order_id=" + r
    }

    function n() {
        var r = $(this).attr("order_id");
        location.href = WapSiteUrl + "/tmpl/member/refund_all.html?order_id=" + r
    }

    function l() {
        var r = $(this).attr("order_id");
        location.href = WapSiteUrl + "/tmpl/member/order_delivery.html?order_id=" + r
    }

    function c() {
        var r = $(this).attr("order_id");
        var e = $(this).attr("order_goods_id");
        location.href = WapSiteUrl + "/tmpl/member/refund.html?order_id=" + r + "&order_goods_id=" + e
    }

    function _() {
        var r = $(this).attr("order_id");
        var e = $(this).attr("order_goods_id");
        location.href = WapSiteUrl + "/tmpl/member/return.html?order_id=" + r + "&order_goods_id=" + e
    }
});