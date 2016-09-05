var page = pagesize;
var curpage = 1;
var hasMore = true;
//var footer = false;
var reset = true;
var start = 0;
var end = 0;
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
        var t = $("#filtrate_ul").find(".selected").find("a").attr("data-state");
        var time = start+','+end;
        //console.log(time);
        $.ajax({
            type: "post",
            url: ApiUrl + "/Order/lists.html",
            data: {token:e,type: t,search:time,page:curpage,page_size:pagesize},
            dataType: "json",
            success: function (e) {
                if(e.code == 40001){
                    //清空cookie
                    delCookie();
                    window.location.href = farm + "/user/login.html";
                }

                if (e.data.list.length <= 0) {
                    $("#footer").addClass("posa")
                } else {
                    $("#footer").removeClass("posa")
                }
                var t = e;
                t.farm = farm;
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
            }
        })
    }

    //取消订单
    //$("#order-list").on("click", ".cancel-order", r);
    //删除订单
    $("#order-list").on("click", ".delete-order", o);
    //确认收货
    $("#order-list").on("click", ".sure-order", n);
    //评价订单
    $("#order-list").on("click", ".evaluation-order", l);
    //追加评论
    //$("#order-list").on("click", ".evaluation-again-order", d);
    //查看物流
    $("#order-list").on("click", ".viewdelivery-order", c);
    //订单支付
    $("#order-list").on("click", ".check-payment", function () {
        //var e = $(this).attr("data-paySn");
        //toPay(e, "member_buy", "pay");
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
            url: ApiUrl + "/order/status.html",
            data: {id: r, token: getCookie('token')},
            dataType: "json",
            success: function (e) {
                $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false})
                if (e.status) {
                    reset = true;
                    window.scrollTo(0, 0);
                    t()
                }
            }
        })
    }

    function o() {
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "是否移除订单？<h6>订单移除后不能找回！</h6>", okFn: function () {
                i(e)
            }
        })
    }

    function i(r) {
        var token = getCookie('token');
        var status = 'delete';
        $.ajax({
            type: "post",
            url: ApiUrl + "/Order/status.html",
            data: {id: r, token: token,status:status},
            dataType: "json",
            success: function (e) {
                $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false})
                if (e.status) {
                    reset = true;
                    window.scrollTo(0, 0);
                    t()
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
            url: ApiUrl + "/Order/status.html",
            data: {token:e,id:r,status:'state_receipt'},
            dataType: "json",
            success: function (e) {
                $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                if (e.status) {
                    reset = true;

                    t()
                }
            }
        })
    }

    //订单评价
    function l() {
        var e = $(this).attr("order_id");
        var g = $(this).attr("goods_id");
        location.href = farm + "/goodsorder/addeval.html?order_id=" + e;
    }

    //查看物流
    function c() {
        var e = $(this).attr("order_id");
        //location.href = WapSiteUrl + "/tmpl/member/order_delivery.html?order_id=" + e
    }

    $("#filtrate_ul").find("a").click(function () {
        $("#filtrate_ul").find("li").removeClass("selected");
        $(this).parent().addClass("selected").siblings().removeClass("selected");
        reset = true;
        window.scrollTo(0, 0);
        t()
    });
    t();
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            t()
        }
    })
});
function get_footer() {
    if (!footer) {
        footer = true;
        $.ajax({url: server + "/html/static/js/tmpl/footer.js", dataType: "script"})
    }
}