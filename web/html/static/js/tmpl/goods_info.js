$(function () {
    var o = getQueryString("goods_id");
    $.ajax({
        url: ApiUrl + "/Goods/detail.html",
        data: {goods_id: o},
        type: "post",
        success: function (e) {
            console.log(e);


            $(".fixed-tab-pannel").html(e.data[0].goods_body)
        }
    });
    //ApiUrl
    $("#goodsDetail").click(function () {
        window.location.href = farm + "/goods/detail.html?goods_id=" + o
    });
    $("#goodsBody").click(function () {
        window.location.href = farm + "/goods/info.html?goods_id=" + o
    });
    $("#goodsEvaluation").click(function () {
        window.location.href = farm + "/goods/eval.html?goods_id=" + o
    })
});