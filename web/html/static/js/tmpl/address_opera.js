$(function () {
    var a = getCookie("key");
    var going = getQueryString("going");
    if(going)
    {
        going = '../'+going.replace('_','/')+'.html';//console.log('going:'+going);//获取将要跳转到的页面
    }
    
    $.sValid.init({
        rules: {true_name: "required", mob_phone: "required", area_info: "required", address: "required"},
        messages: {true_name: "姓名必填！", mob_phone: "手机号必填！", area_info: "地区必填！", address: "街道必填！"},
        callback: function (a, e, r) {
            if (a.length > 0) {
                var i = "";
                $.map(e, function (a, e) {
                    i += "<p>" + a + "</p>"
                });
                errorTipsShow(i)
            } else {
                errorTipsHide()
            }
        }
    });
    $("#header-nav").click(function () {
        $(".btn").click()
    });
    $(".btn").click(function () {
        if ($.sValid()) {
            var e = $("#true_name").val();
            var r = $("#mob_phone").val();
            var i = $("#address").val();
            var d = $("#area_info").attr("data-areaid2");
            var t = $("#area_info").attr("data-areaid");
            var p = $("#area_info").attr("data-areaid1");
            var n = $("#area_info").val();
            var o = $("#is_default").attr("checked") ? 1 : 0;
            $.ajax({
                type: "post",
                url: ApiUrl + "/address/add.html",
                data: {
                    key: a,
                    consignee: e,
                    phone: r,
                    province_id: p,
                    city_id: d,
                    area_id: t,
                    address: i,
                    area_info: n,
                    is_default: o,
                    token:getCookie("token")
                },
                dataType: "json",
                success: function (a) {
                    if (a.status==true) {
                        addCookie("going", going); // 写入cookie
                        location.href = going?going:(server+"/html/view/address/list.html");// 成功后跳转到地址列表/going
                    } else {
                        alert(a.msg);
                    }
                }
            })
        }
    });
    $("#area_info").on("click", function () {
        $.areaSelected({
            success: function (a) {
                // console.log(a);
                $("#area_info").val(a.area_info).attr({
                    "data-areaid": a.area_id,
                    "data-areaid1": a.area_id_1,
                    "data-areaid2": a.area_id_2 == 0 ? a.area_id_1 : a.area_id_2
                })
            }
        })
    })
});