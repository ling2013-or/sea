$(function () {
    template.helper("isEmpty", function (t) {
        for (var a in t) {
            return false
        }
        return true
    });
    template.helper("decodeURIComponent", function (t) {
        return decodeURIComponent(t)
    });
    var t = getCookie("token");
    if (!t) {
         window.location.href = farm+"/user/login.html";
    } else {
        //购物车列表
        function p() {
            $.ajax({
                url: ApiUrl + "/Sellcart/lists.html",
                type: "post",
                dataType: "json",
                data: {token: t},
                success: function (t) {
                    if (checkLogin(getCookie('token'))) {
                        if (t.status) {
                            var a = t.data;
                            a.ApiUrl = ApiUrl;
                            a.farm = farm;
                            a.check_out = true;
                            var e = template.render("cart-list", a);
                            if (a.length == 0) {
                                get_footer()
                            }
                            $("#cart-list-wp").html(e);
                            $(".goods-del").click(function () {
                                var t = $(this).attr("cart_id");
                                var mine = $(this).parents('.nctouch-cart-container')[0];
                                $.sDialog({
                                    skin: "red",
                                    content: "确认删除吗？",
                                    okBtn: true,
                                    cancelBtn: true,
                                    okFn: function () {
                                        f(t,mine);
                                    }
                                })
                            });
                            $(".minus").click(h);
                            $(".add").click(_);
                            $(".buynum").blur(m);

                            $(".nctouch-voucher-list").on("click", ".btn", function () {
                                getFreeVoucher($(this).attr("data-tid"))
                            });
                            $(".store-activity").click(function () {
                                $(this).css("height", "auto")
                            })
                        } else {
                            if(t.code == 40001){
                                z = '登录超时！';
                                $.sDialog({skin: "red", content: z, okBtn: true, cancelBtn: true,okFn: function () {
                                    window.location.href = farm+"/user/login.html";
                                }});
                            }else{
                                z = t.msg;
                                $.sDialog({skin: "red", content: z, okBtn: false, cancelBtn: false});
                                //购物车为空
                                var a = t.data;
                                a.farm = farm;
                                var e = template.render("cart-list", a);
                                $("#cart-list-wp").html(e);
                            }

                        }
                    }
                }
            })
        }
        //购物车列表
        p();
        function f(a,s) {
            var mine = s;
            $.ajax({
                url: ApiUrl + "/Sellcart/del.html",
                type: "post",
                dataType: "json",
                data: {token: t,id:a},
                success: function (t) {
                    if(t.status){
                        if(t.status){
                            mine.remove();
                            $('#money').html(t.data.total_money);
                            if(t.data.total_num == 0){
                                //删除全部
                                $('.nctouch-cart-bottom').remove();
                                var inner = '<div class="nctouch-norecord cart"><div class="norecord-ico"><i></i></div><dl><dt>您的购物车还是空的</dt><dd>去挑一些中意的商品吧</dd></dl> <a href="'+farm+'/seed/list.html" class="btn">随便逛逛</a> </div>';
                                $('#notice').html(inner);
                            }
                        }else{

                            $.sDialog({skin: "red", content: t.msg, okBtn: false, cancelBtn: false});
                        }

                    }else{
                        if(t.code == 40001){
                            z = '登录超时！';
                            $.sDialog({skin: "red", content: z, okBtn: true, cancelBtn: true,okFn: function () {
                                window.location.href = farm+"/user/login.html";
                            }});
                        }else{
                            z = t.msg;
                            $.sDialog({skin: "red", content: z, okBtn: false, cancelBtn: false});
                        }


                    }
                }
            });

        }

        function h() {
            var t = this;
            g(t, "minus")
        }

        function _() {
            var t = this;
            g(t, "add")
        }

        function g(a, e) {
            var o = $(a).parents(".cart-litemw-cnt");
            //cart_id
            var r = o.attr("cart_id");
            var i = o.find(".buy-num");
            var n = o.find(".goods-price");
            var c = parseFloat(i.val());
            var s = null;
            if (e == "add") {
                s = parseFloat(c + 1)
            } else if(e == 'change'){
                if (c > 0) {
                    s = parseFloat(c)
                } else {
                    return false
                }
            }else if(e == 'minus') {
                if (c > 1) {
                    s = parseFloat(c - 1)
                } else {
                    return false
                }
            }
            //页面闪烁
            $(".pre-loading").removeClass("hide");
            $.ajax({
                url: ApiUrl + "/Sellcart/edit.html",
                type: "post",
                data: {token: t, cart_id: r, area: s},
                dataType: "json",
                success: function (t) {
                    $(".pre-loading").addClass("hide");
                    if (checkLogin(getCookie('token'))) {
                        if (t.status) {
                            num = t.data.num;
                            i.val(num);
                            $("#money").html(t.data.total_money);

                        } else {
                            $.sDialog({skin: "red", content: t.msg, okBtn: false, cancelBtn: false})
                            num = t.data.num;
                            i.val(num);
                            $("#money").html(t.data.total_money);
                        }

                    }
                }
            })
        }

        //购物车结算
        $("#cart-list-wp").on("click", ".check-out > a", function () {
            if (!$(this).parent().hasClass("ok")) {
                return false
            }
            //获取购物车ID信息
            var t = [];
            $(".cart-litemw-cnt").each(function () {
                if ($(this).find('input[name="cart_id"]').prop("checked")) {
                    var a = $(this).find('input[name="cart_id"]').val();
                    t.push(a)
                }
            });

            //结算购物车(生成一个订单) TODO 接口修改为核对购物车信息
            $.ajax({
                url: ApiUrl + "/Sellcart/confirm.html",
                type: "post",
                data: {token: getCookie('token'), cart_list: t},
                dataType: "json",
                success: function (t) {
                    if(t.status){
                        window.location.href = farm+'/seedorder/step.html';
                    }else{
                        //结算失败
                        var msg = t.msg;
                        $.sDialog({skin: "red", content: msg, okBtn: false, cancelBtn: false});
                    }
                }
            });

        });
        $.sValid.init({
            rules: {buynum: "number"}, messages: {buynum: "请输入正确的数字"}, callback: function (t, a, e) {
                if (t.length > 0) {
                    var o = "";
                    $.map(a, function (t, a) {
                        o += "<p>" + t + "</p>"
                    });
                    $.sDialog({skin: "red", content: o, okBtn: false, cancelBtn: false})
                }
            }
        });
        //校验数据类型（整形）
        function m() {
            $.sValid();
            var t = this;
            g(t, "change")
        }
    }
    $("#cart-list-wp").on("click", ".store_checkbox", function () {
        $(this).parents(".nctouch-cart-container").find('input[name="cart_id"]').prop("checked", $(this).prop("checked"));
        calculateTotalPrice()
    });
    $("#cart-list-wp").on("click", ".all_checkbox", function () {
        $("#cart-list-wp").find('input[type="checkbox"]').prop("checked", $(this).prop("checked"));
        calculateTotalPrice()
    });
    $("#cart-list-wp").on("click", 'input[name="cart_id"]', function () {
        calculateTotalPrice()
    })
});
function calculateTotalPrice() {
    var t = parseFloat("0.00");
    $(".cart-litemw-cnt").each(function () {
        if ($(this).find('input[name="cart_id"]').prop("checked")) {
            t += parseFloat($(this).find(".goods-price").find("em").html()) * parseInt($(this).find(".value-box").find("input").val())
        }
    });
    $(".total-money").find("em").html(t.toFixed(2));
    check_button();
    return true
}
function getGoods(t, a) {
    var e = {};
    $.ajax({
        type: "get",
        url: ApiUrl + "/index.php?act=goods&op=goods_detail&goods_id=" + t,
        dataType: "json",
        async: false,
        success: function (o) {
            if (o.datas.error) {
                return false
            }
            var r = o.datas.goods_image.split(",");
            e.cart_id = t;
            e.store_id = o.datas.store_info.store_id;
            e.store_name = o.datas.store_info.store_name;
            e.goods_id = t;
            e.goods_name = o.datas.goods_info.goods_name;
            e.goods_price = o.datas.goods_info.goods_price;
            e.goods_num = a;
            e.goods_image_url = r[0];
            e.goods_sum = (parseInt(a) * parseFloat(o.datas.goods_info.goods_price)).toFixed(2)
        }
    });
    return e
}
function get_footer() {
    footer = true;
    $.ajax({url: WapSiteUrl + "/js/tmpl/footer.js", dataType: "script"})
}
function check_button() {
    var t = false;
    $('input[name="cart_id"]').each(function () {
        if ($(this).prop("checked")) {
            t = true
        }
    });
    if (t) {
        $(".check-out").addClass("ok")
    } else {
        $(".check-out").removeClass("ok")
    }
}