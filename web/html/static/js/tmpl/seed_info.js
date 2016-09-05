$(function () {
    var o = getQueryString("goods_id");
    $.ajax({
        url: ApiUrl + "/Seed/info.html",
        data: {id: o},
        type: "post",
        success: function (e) {
            $(".fixed-tab-pannel").html(e.data.seed_descript)
        }
    });
    //ApiUrl
    $("#goodsDetail").click(function () {
        window.location.href = farm + "/seed/detail.html?goods_id=" + o
    });
    $("#goodsBody").click(function () {
        window.location.href = farm + "/seed/info.html?goods_id=" + o
    });
    $("#goodsEvaluation").click(function () {
        window.location.href = farm + "/seed/eval.html?goods_id=" + o
    })
});