$(function () {
    var e = getCookie("token");
    // e = true;
    if (!e) {
        location.href = farm+"/user/login.html"
    }
    template.helper("isEmpty", function (e) {
        for (var t in e) {
            return false
        }
        return true
    });

    // 请求消息列表
    $.ajax({
        type: "post",
        url: ApiUrl + "/message/lists.html",
        data: {token: e, recent: 1},
        dataType: "json",
        success: function (t) {
            // console.log(t);//return false;
            if(t.code==40001)
            {
                location.href = farm+"/user/login.html";return false;
            }
            if(t.code==46501)
            {
                t.data = {list:[]};
            }
            var a = t.data;
            $("#messageList").html(template.render("messageListScript", a));

            // 删除消息
            $(".msg-list-del").click(function () {
                var t = $(this).attr("t_id");
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/message/del.html",
                    data: {token: e, id: t},
                    dataType: "json",
                    success: function (e) {
                        if (e.status == true) {
                            location.reload()
                        } else {
                            $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                            //return false
                        }
                    }
                })
            })

            // 标记为已读
            $(".msg-list-browse").click(function () {
                var t = $(this).attr("t_id");
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/message/isread.html",
                    data: {token: e, id: t},
                    dataType: "json",
                    success: function (e) {
                        if (e.status == true) {
                            location.reload()
                        } else {
                            $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                            //return false
                        }
                    }
                })
            })
        }
    })
});