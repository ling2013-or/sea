$(function () {
    var token = getCookie("token");
    if (!token) {
        window.location.href = farm + "/user/login.html";
        return
    }
    //获取账户的详细信息
    $.ajax({
        'url':ApiUrl + '/Account/detail.html',
        data:{'token': token},
        type:'post',
        dataType:'json',
        success:function (e) {
            if(e.status){
                $("#balance").html(e.data.account_balance + " 元");
                $("#amount").html(e.data.account_amount + " 元");
                $("#investment").html(e.data.investment_amount + " 元");
                $("#consume").html(e.data.consume_amount + " 元");
                $("#charge").html(e.data.charge_amount + " 元")
                $("#freeze").html(e.data.freeze_amount + " 元")
            }else{
                window.location.href = farm + "/user/login.html";
                return
            }

        }
    });

});