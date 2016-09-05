/**
 * Created by Administrator on 2015/12/24.
 */
var address_id = null;
var cart_id = null;
var pay_name = "online";
var invoice_id = 0;
var offpay_hash, offpay_hash_batch, voucher, pd_pay, password, fcode = "", rcb_pay, rpt, payment_code;
var message = {};
var address_id = [];
var freight_hash, city_id, area_id;
var area_info;
var goods_id;
var token = getCookie('token');
$(function () {
    //获取收货地址列表
    $("#list-address-valve").click(function () {
        $.ajax({
            type: "post",
            url: ApiUrl + "/Address/lists.html",
            data: {token: token},
            dataType: "json",
            async: false,
            success: function (e) {
                var a = e;
                a.address_id = address_id;
                var i = template.render("list-address-add-list-script", a);
                $("#list-address-add-list-ul").html(i)
            }
        })
    });
    $.animationLeft({valve: "#list-address-valve", wrapper: "#list-address-wrapper", scroll: "#list-address-scroll"});
    $("#list-address-add-list-ul").on("click", "li", function () {
        $(this).addClass("selected").siblings().removeClass("selected");
        eval("address_info = " + $(this).attr("data-param"));
        _init(address_info.address_id);
        $("#list-address-wrapper").find(".header-l > a").click();
    });
    $.animationLeft({valve: "#new-address-valve", wrapper: "#new-address-wrapper", scroll: ""});
    $.animationLeft({valve: "#select-payment-valve", wrapper: "#select-payment-wrapper", scroll: ""});
    $("#new-address-wrapper").on("click", "#varea_info", function () {
        $.areaSelected({
            success: function (e) {
                console.log(e);
                city_id = e.area_id_2 == 0 ? e.area_id_1 : e.area_id_2;
                area_id = e.area_id;
                area_info = e.area_info;
                $("#varea_info").val(e.area_info)
            }
        })
    });
    $.animationLeft({valve: "#invoice-valve", wrapper: "#invoice-wrapper", scroll: ""});
    template.helper("isEmpty", function (e) {
        var a = true;
        $.each(e, function (e, i) {
            a = false;
            return false
        });
        return a
    });
    template.helper("pf", function (e) {
        return parseFloat(e) || 0
    });
    template.helper("p2f", function (e) {
        return (parseFloat(e) || 0).toFixed(2)
    });
    var _init = function (h) {
        var a = 0;
        $.ajax({
            type: "post",
            url: ApiUrl + "/Sellcart/lists.html",
            dataType: "json",
            data: {token: token},
            success: function (e) {
                if (e.status) {
                    //获取购物车ID
                    var ids = [];
                    for (var q in e.data.list) {
                        ids.push(e.data.list[q].cart_id);
                    }
                    cart_id = ids;
                    var a = e.data;

                    e.farm = farm;
                    var i = template.render("goods_list", e);
                    $("#deposit").html(i);

                    $("#ToBuyStep2").parent().addClass("ok");
                    $("#totalPrice,#onlineTotal").html(parseFloat(e.data.total_money))
                } else {
                    $.sDialog({
                        skin: "block", content: e.msg, okFn: function () {
                            if (e.code == 40001) {
                                window.location.href = farm + "/user/login.html";
                            } else if (e.code == 46401) {
                                //资金不足充值
                                window.location.href = farm + "/seed/list.html";
                            }
                        }, cancelFn: function () {
                            history.go(-1)
                        }
                    });
                }
            }
        })
    };
    rcb_pay = 0;
    pd_pay = 0;
    _init();
    var insertHtmlAddress = function (a) {
        address_id = e.address_id;
        $("#true_name").html(a.true_name);
        $("#mob_phone").html(e.mob_phone);
        $("#address").html(e.area_info + e.address);
        area_id = e.area_id;
        city_id = e.city_id;
        if (a.content) {
            for (var i in a.content) {
                $("#storeFreight" + i).html(parseFloat(a.content[i]).toFixed(2))
            }
        }
        offpay_hash = a.offpay_hash;
        offpay_hash_batch = a.offpay_hash_batch;
        if (a.allow_offpay == 1) {
            $("#payment-offline").show()
        }
        if (!$.isEmptyObject(a.no_send_tpl_ids)) {
            $("#ToBuyStep2").parent().removeClass("ok");
            for (var t = 0; t < a.no_send_tpl_ids.length; t++) {
                $(".transportId" + a.no_send_tpl_ids[t]).show()
            }
        } else {
            $("#ToBuyStep2").parent().addClass("ok")
        }
    };
    $("#payment-online").click(function () {
        pay_name = "online";
        $("#select-payment-wrapper").find(".header-l > a").click();
        $("#select-payment-valve").find(".current-con").html("在线支付");
        $(this).addClass("sel").siblings().removeClass("sel")
    });
    $("#payment-offline").click(function () {
        pay_name = "offline";
        $("#select-payment-wrapper").find(".header-l > a").click();
        $("#select-payment-valve").find(".current-con").html("货到付款");
        $(this).addClass("sel").siblings().removeClass("sel")
    });
    $.sValid.init({
        rules: {
            vtrue_name: "required",
            vmob_phone: "required",
            varea_info: "required",
            vaddress: "required"
        },
        messages: {vtrue_name: "姓名必填！", vmob_phone: "手机号必填！", varea_info: "地区必填！", vaddress: "街道必填！"},
        callback: function (e, a, i) {
            if (e.length > 0) {
                var t = "";
                $.map(a, function (e, a) {
                    t += "<p>" + e + "</p>"
                });
                errorTipsShow(t)
            } else {
                errorTipsHide()
            }
        }
    });
    $("#add_address_form").find(".btn").click(function () {
        if ($.sValid()) {
            var e = {};
            e.key = key;
            e.true_name = $("#vtrue_name").val();
            e.mob_phone = $("#vmob_phone").val();
            e.address = $("#vaddress").val();
            e.city_id = city_id;
            e.area_id = area_id;
            e.area_info = $("#varea_info").val();
            e.is_default = 0;
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?act=member_address&op=address_add",
                data: e,
                dataType: "json",
                success: function (a) {
                    if (!a.datas.error) {
                        e.address_id = a.datas.address_id;
                        _init(e.address_id);
                        $("#new-address-wrapper,#list-address-wrapper").find(".header-l > a").click()
                    }
                }
            })
        }
    });
    $("#invoice-noneed").click(function () {
        $(this).addClass("sel").siblings().removeClass("sel");
        $("#invoice_add,#invoice-list").hide();
        invoice_id = 0
    });

    $('input[name="inv_title_select"]').click(function () {
        if ($(this).val() == "person") {
            $("#inv-title-li").hide()
        } else {
            $("#inv-title-li").show()
        }
    });
    $("#invoice-div").on("click", "#invoiceNew", function () {
        invoice_id = 0;
        $("#invoice_add,#invoice-list").show()
    });
    $("#invoice-list").on("click", "label", function () {
        invoice_id = $(this).find("input").val()
    });
    $("#invoice-div").find(".btn-l").click(function () {
        if ($("#invoice-need").hasClass("sel")) {
            if (invoice_id == 0) {
                var e = {};
                e.key = key;
                e.inv_title_select = $('input[name="inv_title_select"]:checked').val();
                e.inv_title = $("input[name=inv_title]").val();
                e.inv_content = $("select[name=inv_content]").val();
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/index.php?act=member_invoice&op=invoice_add",
                    data: e,
                    dataType: "json",
                    success: function (e) {
                        if (e.datas.inv_id > 0) {
                            invoice_id = e.datas.inv_id
                        }
                    }
                });
                $("#invContent").html(e.inv_title + " " + e.inv_content)
            } else {
                $("#invContent").html($("#inv_" + invoice_id).html())
            }
        } else {
            $("#invContent").html("不需要发票")
        }
        $("#invoice-wrapper").find(".header-l > a").click()
    });
    $("#ToBuyStep2").click(function () {
        //判断是否可以提交订单
        if (!$(this).parent().hasClass('ok')) {
            return false;
        }
        var e = "";
        message = $("#storeMessage0").val();
        for (var a in message) {
            e += a + "|" + message[a] + ","
        }

        /*输入支付密码*/
        var btnArray = ['取消', '确定'];
        mui.prompt('请输入支付密码：', 'password', '', btnArray, function (p) {
            if (p.index == 1 && p.value.length > 5 && p.value.length < 16) {
                pay_pass = p.value;
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/Sellcart/submit.html",
                    data: {
                        token: token,
                        cart_list: cart_id,
                        pay_pass: pay_pass
                    },
                    dataType: "json",
                    success: function (e) {
                        checkLogin(token);
                        if (e.status) {
                            $.sDialog({
                                skin: "red", content: e.msg, okBtn: true, cancelBtn: false, okFn: function () {
                                    setTimeout(window.location.href = farm + "/seedorder/list.html", 3e5);
                                }
                            });

                        } else {
                            $.sDialog({
                                skin: "block", content: e.msg, okFn: function () {
                                    if (e.code == 40001) {
                                        window.location.href = farm + "/user/login.html";
                                    } else if (e.code == 46502) {
                                        //资金不足充值
                                        window.location.href = farm + "/account/mycash.html";
                                    }
                                }, cancelFn: function () {
                                    history.go(-1)
                                }
                            });
                            return false
                        }

                    }
                })
            } else {
                if (p.index == 1 && (p.value.length < 6 || p.value.length > 15)) {
                    mui.toast('请检查您的密码长度！');
                }
            }
        })
    })
});
