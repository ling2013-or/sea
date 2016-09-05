/**
 * Created by Administrator on 2015/12/24.
 */
var address_id = null;
var is_send = false;
var ifcart = getQueryString("ifcart");
if (ifcart == 1) {
    var cart_id = getQueryString("cart_id")
} else {
    var cart_id = getQueryString("goods_id") + "|" + getQueryString("buynum")
}
var pay_name = "online";
var invoice_id = 0;
var address_id, vat_hash, offpay_hash, offpay_hash_batch, voucher, pd_pay, password, fcode = "", rcb_pay, rpt, payment_code;
var message = {};
var freight_hash, city_id, area_id;
var area_info;
var goods_id = getQueryString('ids');
var token = getCookie('token');

$(function () {
    //获取收货地址列表
    $("#list-address-valve").click(function () {
        selectedId = $(this).attr('address_id');//alert(selectedId);
        $.ajax({
            type: "post",
            url: ApiUrl + "/Address/lists.html",
            data: {token: token},
            dataType: "json",
            async: false,
            success: function (e) {
                checkLogin(e.login);

                var a = e;
                a.address_id = address_id;
                a.selectedId = selectedId;// (LY)标记哪个地址被选中
                var i = template.render("list-address-add-list-script", a);
                $("#list-address-add-list-ul").html(i)
            }
        })
    });
    $.animationLeft({valve: "#list-address-valve", wrapper: "#list-address-wrapper", scroll: "#list-address-scroll"});
    $("#list-address-add-list-ul").on("click", "li", function () {
        // alert(123);
        $(this).addClass("selected").siblings().removeClass("selected");
        eval("address_info = " + $(this).attr("data-param"));//console.log(address_info);
        _init(address_info.address_id);//return false;
        $("#list-address-wrapper").find(".header-l > a").click();// 返回
    });

    $("#new-address-wrapper").on("click", "#varea_info", function () {
        $.areaSelected({
            success: function (e) {
                // console.log(e);
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
        var ids = getCookie('cart_ids');
        $.ajax({
            type: "post",
            url: ApiUrl + "/Order/confim.html",
            dataType: "json",
            //data: {token: token, cart_id: cart_id, ifcart: ifcart, address_id: e},
            data: {token: token, cart_ids: ids, address_id:h},
            success: function (e) {/*console.log(e);*/
                if (e.status) {
                    //收货地址
                    var address = e.data.address;
                    //产品信息
                    var goods = e.goods_list;
                    //用户的收货地址
                    if(address == "" || address == undefined || address == null){
                        $.sDialog({
                            autoTime: "500",
                            skin: "red",
                            content: "请添加您的收货地址！",
                            okBtn: false,
                            cancelBtn: false
                        });
                    }else{
                        $('#true_name').html(address.consignee);
                        $('#mob_phone').html(address.phone);
                        $('#address').html(address.area_info + address.address);
                        $("#ToBuyStep2").parent().addClass("ok");
                        address_id = address.id;
                        $('#list-address-valve').attr('address_id', address.id);//添加address_id属性，确认订单所需
                    }

                    cart_id = e.data.cart_ids;


                    var a = e.data;
                    var values = e.data;
                    var total = 0;
                    //获取购物车中总金额
                    total = e.data.goods_total;


                    e.total_money = total;
                    $("#totalPrice").html(total);
                    e.farm = farm;
                    template.helper('parsefloat', function (s) {
                        return parseFloat(s);
                    });
                    var i = template.render("goods_list", e);
                    $("#deposit").html(i);


                    $("#container-fcode").find(".submit").click(function () {
                        fcode = $("#fcode").val();
                        if (fcode == "") {
                            $.sDialog({skin: "red", content: "请填写F码", okBtn: false, cancelBtn: false});
                            return false
                        }
                        $.ajax({
                            type: "post",
                            url: ApiUrl + "/Address/add.html",
                            dataType: "json",
                            data: {key: key, goods_id: goods_id, fcode: fcode},
                            success: function (e) {
                                if (e.datas.error) {
                                    $.sDialog({skin: "red", content: e.datas.error, okBtn: false, cancelBtn: false});
                                    return false
                                }
                                $.sDialog({
                                    autoTime: "500",
                                    skin: "green",
                                    content: "验证成功",
                                    okBtn: false,
                                    cancelBtn: false
                                });
                                $("#container-fcode").addClass("hide")
                            }
                        })
                    });
                } else {
                    $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                    setTimeout(history.go(-1),5000)
                    return false
                }
            }
        })
    };


    var _init2 = function (h) {
        var a = 0;
        $.ajax({
            type: "post",
            url: ApiUrl + "/Goodscart/lists.html",
            dataType: "json",
            //data: {token: token, cart_id: cart_id, ifcart: ifcart, address_id: e},
            data: {token: token},
            success: function (e) {
                //获取用户的默认收货地址
                var add_id = null;
                var is_default = true;
                if (typeof (h) != 'undefined') {
                    add_id = h;
                    is_default = null;
                }
                //获取购物车ID
                var ids = [];
                for (var q in e.data) {
                    for (var r in e.data[q]) {
                        ids.push(e.data[q][r].id);
                    }
                }
                cart_id = ids.join(',');

                $.ajax({
                    type: "post",
                    url: ApiUrl + "/Address/lists.html",
                    dataType: "json",
                    //data: {token: token, cart_id: cart_id, ifcart: ifcart, address_id: e},
                    data: {token: token, default: is_default, id: add_id},
                    success: function (v) {
                        e.address = v.data[0];
                        if ($.isEmptyObject(v.data[0])) {
                            $.sDialog({
                                skin: "block", content: "请添加地址", okFn: function () {
                                    $("#new-address-valve").click()
                                }, cancelFn: function () {
                                    history.go(-1)
                                }
                            });
                            return false
                        }
                        if (v.status) {
                            if (typeof(v.data) != "undefined") {
                                var address = v.data[0];
                                $('#true_name').html(address.consignee);
                                $('#mob_phone').html(address.phone);
                                $('#address').html(address.area_info + address.address);
                                $("#ToBuyStep2").parent().addClass("ok");
                                address_id = address.id;
                                $('#list-address-valve').attr('address_id', address_id);//添加address_id属性，确认订单所需
                            } else {
                                e.address = false;
                            }
                        } else {

                            e.address = false;
                        }
                    }
                });

                var a = e.data;
                var values = e.data;
                var total = 0;
                //获取购物车中总金额
                for (var prop in values) {
                    for (var s in values[prop]) {
                        total += parseFloat(values[prop][s].goods_total);
                    }
                }
                e.total_money = total;
                $("#totalPrice").html(total);
                e.farm = farm;
                var i = template.render("goods_list", e);
                $("#deposit").html(i);

                $("#container-fcode").find(".submit").click(function () {
                    fcode = $("#fcode").val();
                    if (fcode == "") {
                        $.sDialog({skin: "red", content: "请填写F码", okBtn: false, cancelBtn: false});
                        return false
                    }
                    $.ajax({
                        type: "post",
                        url: ApiUrl + "/Address/add.html",
                        dataType: "json",
                        data: {key: key, goods_id: goods_id, fcode: fcode},
                        success: function (e) {
                            if (!e.status) {
                                $.sDialog({skin: "red", content: e.datas.error, okBtn: false, cancelBtn: false});
                                return false
                            }
                            $.sDialog({
                                autoTime: "500",
                                skin: "green",
                                content: "验证成功",
                                okBtn: false,
                                cancelBtn: false
                            });
                            $("#container-fcode").addClass("hide")
                        }
                    })
                });


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
            e.consignee = $("#vtrue_name").val();
            e.phone = $("#vmob_phone").val();
            e.address = $("#vaddress").val();
            e.city_id = city_id;
            e.area_id = area_id;
            e.area_info = $("#varea_info").val();
            e.is_default = 0;

            $.ajax({
                type: "post",
                url: ApiUrl + "/Address/add.html",
                data: e,
                dataType: "json",
                success: function (a) {
                    if (!a.data.error) {
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
        var e = "";
        message = $("#storeMessage0").val();
        for (var a in message) {
            e += a + "|" + message[a] + ","
        }
        //获取是否配送
        if($('#useRPT').parent().hasClass('checked')){
            address_id = null;
        }
        //console.log();

        /*输入支付密码*/
        var btnArray = ['取消', '确定'];
        mui.prompt('请输入支付密码：', 'password', '', btnArray, function(p) {
            if (p.index == 1 && p.value.length > 5 && p.value.length < 16) {
                //获取支付密码
                pay_pass = p.value;
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/Order/submit.html",
                    data: {
                        token: token,
                        cart_ids: cart_id,
                        address_id: address_id,
                        pay_pass: pay_pass
                    },
                    dataType: "json",
                    success: function (e) {
                        checkLogin(token);
                        if (e.status) {
                            delCookie('cart_ids');
                            $.sDialog({skin: "red", content: e.msg, okBtn: true, cancelBtn: false,okFn:function(){
                                window.location.href = farm+'/goodsorder/list.html';
                            }});
                            //setTimeout();
                        } else {
                            $.sDialog({
                                skin: "block", content: e.msg, okFn: function () {
                                    if (e.code == 40001) {
                                        window.location.href = farm + "/user/login.html";
                                    } else if (e.code == 51213) {
                                        //资金不足充值
                                        window.location.href = farm + "/user/member.html";
                                    }else if (e.code == 45121) {
                                        console.log(e.code)
                                        //资金不足充值
                                        window.location.href = farm + "/account/paypwd.html";
                                    }


                                }, cancelFn: function () {
                                    setTimeout(window.location.href = farm + "/goodscart/list.html",5000)
                                }
                            });
                            return false
                        }

                    }
                })
            } else {
                if(p.index == 1 && (p.value.length < 6 || p.value.length > 15)){
                    mui.toast ('请检查您的密码长度！');
                }
                // 什么都不做
            }
        });

        
    })
    
    var going = getCookie('going');
    if(going && going.indexOf('goodsorder/step')) //如果是通过添加地址之后才跳转到此页面
    {
        $("#list-address-valve").click();
        addCookie('going','');// 重新设置为空
    }
});
