$(function () {
    var e = getCookie("token");
    if (!e) {
        location.href = farm +"/user/login.html"
    }
    function s() {
        $.ajax({
            type: "post",
            url: ApiUrl + "/address/lists.html",
            data: {token: e},
            dataType: "json",
            success: function (e) {
                console.log(e);
                /*if (e.datas.address_list == null) {
                    return false
                }*/
                if (!e.data) {
                    e.data=[];
                }
                /*var s = e.datas;*/
                var s = e;
                var t = template.render("saddress_list", s); // 执行js模板(js标签id, )
                $("#address_list").empty();
                $("#address_list").append(t);
                $(".deladdress").click(function () {
                    var e = $(this).attr("address_id");
                    $.sDialog({
                        skin: "block", content: "确认删除吗？", okBtn: true, cancelBtn: true, okFn: function () {
                            a(e)
                        }
                    })
                })
            }
        })
    }

    s();
    function a(a) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/address/del",
            data: {id: a, token: getCookie("token")},
            dataType: "json",
            success: function (e) {
                // checkLogin(e.login);
                if (e.status==true) {
                    // console.log(e);
                    window.location.reload();
                }
            }
        })
    }

    $("#address_list").on('click','.is_default',function(){
        // console.log(123);
        var addressId = $(this).attr("address_id"); //console.log(addressId);
        var myself = $(this);
        
        $.ajax({
            type: "post",
            url: ApiUrl + "/address/def",
            data: {id: addressId, token: getCookie("token"), is_default:1},
            dataType: "json",
            success: function (e) {
                // checkLogin(e.login);
                if (e.status==true) {
                    // console.log(e);
                    // window.location.reload();
                    $(".is_default").prop('checked',false);
                    $(".is_default").prop('disabled',false);
                    myself.prop('checked',true); // 使自己选中
                    myself.prop('disabled',true);// 使自己不能点击
                    mui.toast("设置默认地址成功！");
                }
                else if(e.code=='40001')
                {
                    window.location.href = farm+"/user/login.html";
                }
            }
        })
        
        /*else
        {
            $(this).prop('checked',false);
        }*/
    });
});