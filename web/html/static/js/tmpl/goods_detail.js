var goods_id = getQueryString("goods_id");
var map_list = [];
var map_index_id = "";
var store_id;
$(function () {
    var e = getCookie("token");
    var t = function (e, t) {
        e = parseFloat(e) || 0;
        if (e < 1) {
            return ""
        }
        var o = new Date;
        o.setTime(e * 1e3);
        var a = "" + o.getFullYear() + "-" + (1 + o.getMonth()) + "-" + o.getDate();
        if (t) {
            a += " " + o.getHours() + ":" + o.getMinutes() + ":" + o.getSeconds()
        }
        return a
    };
    var o = function (e, t) {
        e = parseInt(e) || 0;
        t = parseInt(t) || 0;
        var o = 0;
        if (e > 0) {
            o = e
        }
        if (t > 0 && o > 0 && t < o) {
            o = t
        }
        return o
    };
    template.helper("isEmpty", function (e) {
        for (var t in e) {
            return false
        }
        return true
    });
    function a() {
        var e = $("#mySwipe")[0];
        window.mySwipe = Swipe(e, {
            continuous: false, stopPropagation: true, callback: function (e, t) {
                $(".goods-detail-turn").find("li").eq(e).addClass("cur").siblings().removeClass("cur")
            }
        })
    }

    r(goods_id);
    function i(e, t) {
        $(e).addClass("current").siblings().removeClass("current");
        var o = $(".spec").find("a.current");
        var a = [];
        $.each(o, function (e, t) {
            a.push(parseInt($(t).attr("specs_value_id")) || 0)
        });
        var i = a.sort(function (e, t) {
            return e - t
        }).join("|");
        goods_id = t.spec_list[i];
        //r(goods_id)
    }

    function s(e, t) {
        var o = e.length;
        while (o--) {
            if (e[o] === t) {
                return true
            }
        }
        return false
    }

    function evaluate(){
        console.log(goods_id);
        $.ajax({
            url: ApiUrl + "/Evaluate/info.html",
            type: "post",
            data: {goods_id: goods_id, token: e},
            dataType: "json",
            beforeSend: function () {
                //alert(r);
            },
            success: function (e) {

                window.console.log(e);

            }
        });
    }

    $.sValid.init({
        rules: {buynum: "digits"}, messages: {buynum: "请输入正确的数字"}, callback: function (e, t, o) {
            if (e.length > 0) {
                var a = "";
                $.map(t, function (e, t) {
                    a += "<p>" + e + "</p>"
                });
                $.sDialog({skin: "red", content: a, okBtn: false, cancelBtn: false})
            }
        }
    });
    function n() {
        $.sValid()
    }


    function r() {
        $.ajax({
            url: ApiUrl + "/Goods/detail",
            type: "post",
            data: {goods_id: goods_id, token: e},
            dataType: "json",
            beforeSend: function () {
                //alert(r);
            },
            success: function (e) {
                var l = e.data[0];
                if (e.status) {
                    l.time = parseInt(Date.parse(new Date()) / 1000);//js获取到的时间是以毫秒为单位的。
                    l.farm = farm;
                    l.server = server;
                    l.statistics = e.data.statistics;
                    var _ = template.render("product_detail", l);
                    $("#product_detail_html").html(_);

                    var _ = template.render("product_detail_sepc", l);
                    $("#product_detail_spec_html").html(_);
                    //展示图片滑动
                    a();
                    //$(".pddcp-arrow").click(function () {
                    //    $(this).parents(".pddcp-one-wp").toggleClass("current")
                    //});
                    var p = {};
                    p["spec_list"] = l.storage;
                    $(".spec a").click(function () {
                        var e = this;
                        i(e, p)
                    });
                    //购物车减少
                    $(".minus").click(function () {
                        var e = $(".buy-num").val();
                        if (e > 1) {
                            $(".buy-num").val(parseInt(e - 1))
                        }
                    });
                    //购物车增加
                    $(".add").click(function () {
                        var e = parseInt($(".buy-num").val());
                        if (e < l.goods_stock) {
                            $(".buy-num").val(parseInt(e + 1))
                        }
                    });
                    //商品收藏 TODO 待定
                    $(".pd-collect").click(function () {
                        if ($(this).hasClass("favorate")) {
                            if (dropFavoriteGoods(r))$(this).removeClass("favorate")
                        } else {
                            if (favoriteGoods(r))$(this).addClass("favorate")
                        }
                    });

                    $("#add-cart").click(function () {
                        var e = getCookie("token");
                        if(!checkLogin(e)){
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
                                    window.location.href = farm+"/goods/list.html";


                                }
                            });
                            return false;
                        }
                        //获取此次购买的产品ID
                        var p = parseInt(l.id);
                        var z = null;
                        //购买重量
                        var t = parseFloat($(".buy-num").val());
                        if (!t) {
                            z = '请选择购买面积';
                            $.sDialog({skin: "red", content: z, okBtn: false, cancelBtn: false});
                        }
                        $.ajax({
                            url: ApiUrl + "/Goodscart/add.html",
                            data: {token: e, goods_id: p, weight: t},
                            type: "post",
                            dataType: 'json',
                            success: function (e) {
                               if(e.status){
                                   //图片移动到购物车上
                                   show_tip();
                                   //购物车产品类型个数
                                   var num = e.data.quantity;
                                   $("#cart_count,#cart_count1").html("<sup>" + num + "</sup>");
                               }else{
                                   $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                               }
                            }
                        });


                    });

                    $("#buy-now").click(function () {
                        //生成一个订单，并且发起付款
                        //检测是否登录
                        var e = getCookie("token");
                        if(!checkLogin(e)){
                            $.sDialog({skin: "red", content: '请登录！', okBtn: false, cancelBtn: false});
                            setTimeout(window.location.href = farm+'/user/login.html',600)
                            return false;
                        }
                        //获取此次购买的产品ID
                        var p = parseInt(l.id);
                        var z = null;
                        //购买重量
                        var t = parseFloat($(".buy-num").val());
                        if (!t) {
                            z = '请选择购买重量';
                            $.sDialog({skin: "red", content: z, okBtn: false, cancelBtn: false});
                        }
                        //直接支付
                        $.ajax({
                            url: ApiUrl + "/Goodscart/add.html",
                            data: {token: e, goods_id: p, weight: t},
                            type: "post",
                            dataType: 'json',
                            success: function (y) {
                                //判断订单生成结果，直接发起支付
                                if(y.status){
                                    //console.log(y);
                                    //确认订单
                                    addCookie('cart_ids',y.data.cart_id);
                                    window.location.href = farm+'/goodsorder/step.html';
                                    var msg = '添加成功！';
                                    $.sDialog({skin: "red", content: msg, okBtn: false, cancelBtn: false});
                                    return false;
                                }else{

                                    var v = '暂支付暂不支持，请先加入购物车...';
                                    $.sDialog({skin: "red", content: v, okBtn: false, cancelBtn: false});
                                    //window.location.href = farm+'/cart/list.html';
                                }

                            }
                        });
                    });


                } else {
                    $.sDialog({
                        content: l.error + "！<br>请返回上一页继续操作…",
                        okBtn: false,
                        cancelBtnText: "返回",
                        cancelFn: function () {
                            history.back()
                        }
                    })
                }
                $("#buynum").blur(n);
                $.animationUp({
                    valve: ".animation-up,#goods_spec_selected",
                    wrapper: "#product_detail_spec_html",
                    scroll: "#product_roll",
                    start: function () {
                        $(".goods-detail-foot").addClass("hide").removeClass("block")
                    },
                    close: function () {
                        $(".goods-detail-foot").removeClass("hide").addClass("block")
                    }
                });
                $.animationUp({valve: "#getVoucher", wrapper: "#voucher_html", scroll: "#voucher_roll"});
                $("#voucher_html").on("click", ".btn", function () {
                    getFreeVoucher($(this).attr("data-tid"))
                });
                $(".kefu").click(function () {
                    window.location.href = ApiUrl + "/tmpl/member/chat_info.html?goods_id=" + r + "&t_id=" + e.datas.store_info.member_id
                })
            }
        })
    }

    $.scrollTransparent();
    $("#product_detail_html").on("click", "#get_area_selected", function () {
        $.areaSelected({
            success: function (e) {
                $("#get_area_selected_name").html(e.area_info);
                var t = e.area_id_2 == 0 ? e.area_id_1 : e.area_id_2;
                $.getJSON(ApiUrl + "/index.php?act=goods&op=calc", {goods_id: goods_id, area_id: t}, function (e) {
                    $("#get_area_selected_whether").html(e.datas.if_store_cn);
                    $("#get_area_selected_content").html(e.datas.content);
                    if (!e.datas.if_store) {
                        $(".buy-handle").addClass("no-buy")
                    } else {
                        $(".buy-handle").removeClass("no-buy")
                    }
                })
            }
        })
    });
    $("body").on("click", "#goodsBody,#goodsBody1", function () {
        window.location.href = farm + "/goods/info.html?goods_id=" + goods_id
    });
    //商品评论详情
    $("body").on("click", "#goodsEvaluation,#goodsEvaluation1", function () {
        window.location.href = farm + "/goods/eval.html?goods_id=" + goods_id;
    });
    $("#list-address-scroll").on("click", "dl > a", map);
    $("#map_all").on("click", map)
});
function show_tip() {
    var e = $(".goods-pic > img").clone().css({"z-index": "999", height: "3rem", width: "3rem"});
    e.fly({
        start: {
            left: $(".goods-pic > img").offset().left,
            top: $(".goods-pic > img").offset().top - $(window).scrollTop()
        },
        end: {
            left: $("#cart_count1").offset().left + 40,
            top: $("#cart_count1").offset().top - $(window).scrollTop(),
            width: 0,
            height: 0
        },
        onEnd: function () {
            e.remove()
        }
    })
}
function virtual() {
    $("#get_area_selected").parents(".goods-detail-item").remove();
    $.getJSON(ApiUrl + "/index.php?act=goods&op=store_o2o_addr", {store_id: store_id}, function (e) {
        if (!e.datas.error) {
            if (e.datas.addr_list.length > 0) {
                $("#list-address-ul").html(template.render("list-address-script", e.datas));
                map_list = e.datas.addr_list;
                var t = "";
                t += '<dl index_id="0">';
                t += "<dt>" + map_list[0].name_info + "</dt>";
                t += "<dd>" + map_list[0].address_info + "</dd>";
                t += "</dl>";
                t += '<p><a href="tel:' + map_list[0].phone_info + '"></a></p>';
                $("#goods-detail-o2o").html(t);
                $("#goods-detail-o2o").on("click", "dl", map);
                if (map_list.length > 1) {
                    $("#store_addr_list").html("查看全部" + map_list.length + "家分店地址")
                } else {
                    $("#store_addr_list").html("查看商家地址")
                }
                $("#map_all > em").html(map_list.length)
            } else {
                $(".goods-detail-o2o").hide()
            }
        }
    });
    $.animationLeft({valve: "#store_addr_list", wrapper: "#list-address-wrapper", scroll: "#list-address-scroll"})
}
function map() {
    $("#map-wrappers").removeClass("hide").removeClass("right").addClass("left");
    $("#map-wrappers").on("click", ".header-l > a", function () {
        $("#map-wrappers").addClass("right").removeClass("left")
    });
    $("#baidu_map").css("width", document.body.clientWidth);
    $("#baidu_map").css("height", document.body.clientHeight);
    map_index_id = $(this).attr("index_id");
    if (typeof map_index_id != "string") {
        map_index_id = ""
    }
    if (typeof map_js_flag == "undefined") {
        $.ajax({url: ApiUrl + "/js/map.js", dataType: "script", async: false})
    }
    if (typeof BMap == "object") {
        baidu_init()
    } else {
        load_script()
    }
}