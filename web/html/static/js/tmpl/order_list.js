var page = pagesize;
var curpage = 1;
var hasMore = true;
var start = 0;
var end = 0;
//var footer = false;
var reset = true;
//var orderKey = "";
$(function () {
    var e = getCookie("token");
    if (!e) {
        window.location.href = farm + "/user/login.html"
    }
    if (getQueryString("data-state") != "") {
        $("#filtrate_ul").find("li").has('a[data-state="' + getQueryString("data-state") + '"]').addClass("selected").siblings().removeClass("selected")
    }
    $("#search_btn").click(function () {
        reset = true;
        if($('#beginDate').html()){
            start = transdate($('#beginDate').html());
        }
        if($('#endDate').html()){
            //当前最后一小时的时间戳
            end = transdate($('#endDate').html())+86400;
        }
        t()
    });
    $("#fixed_nav").waypoint(function () {
        $("#fixed_nav").toggleClass("fixed")
    }, {offset: "50"});
    //获取种子订单列表
    function t() {
        if (reset) {
            curpage = 1;
            hasMore = true
        }
        $(".loading").remove();
        if (!hasMore) {
            return false
        }
        hasMore = false;
        //状态
        var t = $("#filtrate_ul").find(".selected").find("a").attr("data-state")?$("#filtrate_ul").find(".selected").find("a").attr("data-state"):null;
        //搜索条件
        var time = start+','+end;
        $.ajax({
            type: "post",
            url: ApiUrl + "/Sellorder/lists.html",
            data: {token:e,page:curpage,page_size:pagesize,sn:time},
            dataType: "json",
            success: function (e) {
                if(e.status){
                    if (e.data.list.length <= 0) {
                        $("#footer").addClass("posa")
                    } else {
                        $("#footer").removeClass("posa")
                    }

                    var t = e;
                    t.farm = farm;
                    t.server = server;
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

                    template.helper("p2f", function (e) {
                        return (parseFloat(e) || 0).toFixed(2)
                    });

                    template.helper("ccc", function (o) {
                        var n = 0;
                        for(var i in o){
                            n++;
                        }
                        return n;
                    });
                    template.helper("parseInt", function (e) {
                        return parseInt(e)
                    });
                    template.helper("eval", function (e) {
                        return eval(e)
                    });
                    template.helper("parsefloat", function (e) {
                        return parseFloat(e)
                    });
                    var r = template.render("order-list-tmpl", t);
                    if(e.data.page * pagesize < e.data.count){
                        hasMore = true;
                        curpage++;

                    }
                    if (reset) {
                        reset = false;
                        $("#order-list").html(r)
                    } else {
                        $("#order-list").append(r)
                    }
                }else{
                    $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                    var t = e;
                    t.farm = farm;
                    t.server = server;
                    var r = template.render("order-list-tmpl", t);
                    $("#order-list").html(r)
                    if(e.code == 40001){
                        //清空cookie
                        delCookie();
                        window.location.href = farm + "/user/login.html";
                    }
                }


            }
        })
    }

    //取消订单
    $("#order-list").on("click", ".cancel-order", r);
    //删除订单
    $("#order-list").on("click", ".delete-order", o);
    //确认收货
    $("#order-list").on("click", ".sure-order", n);
    //评价订单
    $("#order-list").on("click", ".evaluation-order", l);
    //追加评论
    $("#order-list").on("click", ".evaluation-again-order", d);
    //查看物流
    $("#order-list").on("click", ".viewdelivery-order", c);
    //订单支付
    $("#order-list").on("click", ".check-payment", function () {
        var e = $(this).attr("data-paySn");
        toPay(e, "member_buy", "pay");
        return false
    });
    function r() {
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "确定取消订单？", okFn: function () {
                a(e)
            }
        })
    }

    function a(r) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_cancel",
            data: {order_id: r, key: e},
            dataType: "json",
            success: function (e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({skin: "red", content: e.datas.error, okBtn: false, cancelBtn: false})
                }
            }
        })
    }

    function o() {
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "是否移除订单？<h6>电脑端订单回收站可找回订单！</h6>", okFn: function () {
                i(e)
            }
        })
    }

    function i(r) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_delete",
            data: {order_id: r, key: e},
            dataType: "json",
            success: function (e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({skin: "red", content: e.datas.error, okBtn: false, cancelBtn: false})
                }
            }
        })
    }

    function n() {
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "确定收到了货物吗？", okFn: function () {
                s(e)
            }
        })
    }

    function s(r) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_receive",
            data: {order_id: r, key: e},
            dataType: "json",
            success: function (e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({skin: "red", content: e.datas.error, okBtn: false, cancelBtn: false})
                }
            }
        })
    }

    function l() {
        var e = $(this).attr("order_id");
        //location.href = WapSiteUrl + "/tmpl/member/member_evaluation.html?order_id=" + e
    }

    function d() {
        var e = $(this).attr("order_id");
        //location.href = WapSiteUrl + "/tmpl/member/member_evaluation_again.html?order_id=" + e
    }

    function c() {
        var e = $(this).attr("order_id");
        //location.href = WapSiteUrl + "/tmpl/member/order_delivery.html?order_id=" + e
    }
    //分类查询
    $("#filtrate_ul").find("a").click(function () {
        $("#filtrate_ul").find("li").removeClass("selected");
        $(this).parent().addClass("selected").siblings().removeClass("selected");
        reset = true;
        window.scrollTo(0, 0);
        t()
    });
    t();
    //分页选项
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            t()
        }
    })
});
function get_footer() {
    if (!footer) {
        footer = true;
        //$.ajax({url: WapSiteUrl + "/js/tmpl/footer.js", dataType: "script"})
    }
}