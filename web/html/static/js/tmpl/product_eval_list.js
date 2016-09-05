var goods_id = getQueryString("goods_id");
var type = 0;
$(function () {
    var o = new ncScrollLoad;
    o.loadInit({
        url: ApiUrl + "/Goods/commentlist.html",
        data: {goods_id: goods_id,type:type},
        tmplid: "product_ecaluation_script",
        containerobj: $("#product_evaluation_html"),
        iIntervalId: true,
        callback: function () {
            callback()
        }
    });

    $("#goodsDetail").click(function () {
        window.location.href = farm + "/goods/detail.html?goods_id=" + goods_id
    });
    $("#goodsBody").click(function () {
        window.location.href = farm + "/goods/info.html?goods_id=" + goods_id
    });

    $(".nctouch-tag-nav").find("a").click(function () {
        var i = $(this).attr("data-state");
        type = i;
        o.loadInit({
            url: ApiUrl + "/Goods/commentlist.html",
            data: {goods_id: goods_id,type:type},
            tmplid: "product_ecaluation_script",
            containerobj: $("#product_evaluation_html"),
            iIntervalId: true,
            callback: function () {
                callback()
            }
        });
        $(this).parent().addClass("selected").siblings().removeClass("selected")
    })
});
function callback() {
    $(".goods_geval").on("click", "a", function () {
        var o = $(this).parents(".goods_geval");
        o.find(".nctouch-bigimg-layout").removeClass("hide");
        var i = o.find(".pic-box");
        o.find(".close").click(function () {
            o.find(".nctouch-bigimg-layout").addClass("hide")
        });
        if (i.find("li").length < 2) {
            return
        }
        Swipe(i[0], {
            speed: 400,
            auto: 3e3,
            continuous: false,
            disableScroll: false,
            stopPropagation: false,
            callback: function (o, i) {
                $(i).parents(".nctouch-bigimg-layout").find("div").last().find("li").eq(o).addClass("cur").siblings().removeClass("cur")
            },
            transitionEnd: function (o, i) {
            }
        })
    })
}