var page = pagesize;
var curpage = 1;
var hasmore = true;
var footer = false;
var keyword = decodeURIComponent(getQueryString("keyword"));
var gc_id = getQueryString("gc_id");
var b_id = getQueryString("b_id");
var key = getQueryString("key");
var order = getQueryString("order");
var area_id = getQueryString("area_id");
var price_from = getQueryString("price_from");
var price_to = getQueryString("price_to");
var own_shop = getQueryString("own_shop");
var gift = getQueryString("gift");
var groupbuy = getQueryString("groupbuy");
var xianshi = getQueryString("xianshi");
var virtual = getQueryString("virtual");
var ci = getQueryString("ci");
var myDate = new Date;
var searchTimes = myDate.getTime();
var type = getQueryString("type");//console.log('type:'+type);
$(function () {
    $.animationLeft({valve: "#search_adv", wrapper: ".nctouch-full-mask", scroll: "#list-items-scroll"});
    $("#header").on("click", ".header-inp", function () {
        location.href = farm + "/common/search.html?keyword=" + keyword
    });
    if (keyword != "") {
        $("#keyword").html(keyword)
    }
    $("#show_style").click(function () {
        if ($("#product_list").hasClass("grid")) {
            $(this).find("span").removeClass("browse-grid").addClass("browse-list");
            $("#product_list").removeClass("grid").addClass("list")
        } else {
            $(this).find("span").addClass("browse-grid").removeClass("browse-list");
            $("#product_list").addClass("grid").removeClass("list")
        }
    });
    $("#sort_default").click(function () {
        if ($("#sort_inner").hasClass("hide")) {
            $("#sort_inner").removeClass("hide")
        } else {
            $("#sort_inner").addClass("hide")
        }
    });
    $("#nav_ul").find("a").click(function () {
        $(this).addClass("current").parent().siblings().find("a").removeClass("current");
        if (!$("#sort_inner").hasClass("hide") && $(this).parent().index() > 0) {
            $("#sort_inner").addClass("hide")
        }
    });
    $("#sort_inner").find("a").click(function () {
        $("#sort_inner").addClass("hide").find("a").removeClass("cur");
        var e = $(this).addClass("cur").text();
        $("#sort_default").html(e + "<i></i>")
    });
    // 删除我出售的农作物
    $("#product_list").on("click", ".goods-store a", function () {
        var goods_id = Zepto(this).attr("goods_id");//console.log(goods_id);return false;
        var myself = Zepto(this);
        if(confirm( "确定删除？" ))
        {
            $.ajax({
                type: "post",
                data: {token:getCookie('token'),id:goods_id},
                dataType: "json",
                url: ApiUrl+"/sale/del.html",
                beforeSend: function () {
                    myself.html("正在删除...");
                },
                success: function (data) 
                { //console.log(data);
                    if(data.status==true){
                        myself.closest('li').remove();
                    }else{
                        if(data.code == 40001){
                            window.location.href = farm+"/user/login.html";
                        }
                    }
                },
                error: function (data) {
                    console.info("error: " + data.msg);
                }
            });
        }
        
    }).on("click", ".sotre-creidt-layout", function () {
        $(this).hide()
    });

    // 上架、下架
    /*$("#product_list").on("click", ".goods_status", function (e) {
        var goods_id = Zepto(this).attr("goods_id");//console.log(goods_id);return false;
        var myself = Zepto(this);
        var statusText = myself.html();
        if(confirm( statusText=='已上架'?"确定下架？":'确定上架？' ))
        {
            $.ajax({
                type: "post",
                data: {token:getCookie('token'),id:goods_id},
                dataType: "json",
                url: ApiUrl+"/sale/del.html",
                beforeSend: function () {
                    myself.html("正在删除...");
                },
                success: function (data) 
                { //console.log(data);
                    if(data.status==true){
                        myself.closest('li').remove();
                    }else{
                        if(data.code == 40001){
                            window.location.href = farm+"/user/login.html";
                        }
                    }
                },
                error: function (data) {
                    console.info("error: " + data.msg);
                }
            });
            
        }
        e.stopPropagation();
        
    })*/

    get_list();
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            get_list()
        }
    });

    // search_adv()
});
function get_list() {
    $(".loading").remove();
    if (!hasmore) {
        return false
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    if (gc_id != "") {
        param.gc_id = gc_id
    } else if (keyword != "") {
        param.keyword = keyword
    } else if (b_id != "") {
        param.b_id = b_id
    }
    if (key != "") {
        param.key = key
    }
    if (order != "") {
        param.order = order
    }

    // 请求我出售的农作物列表
    var data = {token:getCookie('token')};
    if(type)
    {
        data.type = type;
    }
    $.ajax({
        type: "post",
        data: data,
        dataType: "json",
        url: ApiUrl+"/sale/lists.html",
        beforeSend: function () {
            // 禁用按钮防止重复提交
            //mui.toast('正在加载请稍等');
        },
        success: function (data) {
            if(data.status){

            }else{
                if(data.code == 40001){
                    window.location.href = farm+"/user/login.html";
                }
            }
            window.console.info(data);
            if (data.code == 0) {

                remarkStatus(type); // 根据type标记筛选文字
                
                e = data;
                e.farm = farm;
                e.server = server;
                // console.log(e);
                template.helper("eval",function(s) {
                    vals =  eval(s);
                    return vals[0];

                });
                var r = template.render("home_body", e);
                $("#product_list .goods-secrch-list").append(r);

            }else{
                //window.location.reload();
            }
        },
        complete: function () {
            $(".loading").remove();
        },
        error: function (data) {
            console.info("error: " + data.responseText);
        }
    });
    
}

function remarkStatus(type)
{
    switch(type)
    {
        case 'all':
        $("#sort_default").html("全部农作物<i></i>");
        $("#sort_inner").find('a').attr('class','');
        $("#allStatus").attr('class','cur');
        break;

        case 'up':
        $("#sort_default").html("已上架农作物<i></i>");
        $("#sort_inner").find('a').attr('class','');
        $("#upStatus").attr('class','cur');
        break;

        case 'down':
        $("#sort_default").html("未上架农作物<i></i>");
        $("#sort_inner").find('a').attr('class','');
        $("#downStatus").attr('class','cur');
        break;

        default:
        $("#sort_default").html("筛选农作物<i></i>");
        $("#sort_inner").find('a').attr('class','');
        $("#allStatus").attr('class','cur');
        break;
    }
}

function search_adv() {
    $.getJSON(ApiUrl + "/index.php?act=index&op=search_adv", function (e) {
        var r = e.datas;
        $("#list-items-scroll").html(template.render("search_items", r));
        if (area_id) {
            $("#area_id").val(area_id)
        }
        if (price_from) {
            $("#price_from").val(price_from)
        }
        if (price_to) {
            $("#price_to").val(price_to)
        }
        if (own_shop) {
            $("#own_shop").addClass("current")
        }
        if (gift) {
            $("#gift").addClass("current")
        }
        if (groupbuy) {
            $("#groupbuy").addClass("current")
        }
        if (xianshi) {
            $("#xianshi").addClass("current")
        }
        if (virtual) {
            $("#virtual").addClass("current")
        }
        if (ci) {
            var i = ci.split("_");
            for (var t in i) {
                $('a[name="ci"]').each(function () {
                    if ($(this).attr("value") == i[t]) {
                        $(this).addClass("current")
                    }
                })
            }
        }
        $("#search_submit").click(function () {
            var e = "?keyword=" + keyword, r = "";
            e += "&area_id=" + $("#area_id").val();
            if ($("#price_from").val() != "") {
                e += "&price_from=" + $("#price_from").val()
            }
            if ($("#price_to").val() != "") {
                e += "&price_to=" + $("#price_to").val()
            }
            if ($("#own_shop")[0].className == "current") {
                e += "&own_shop=1"
            }
            if ($("#gift")[0].className == "current") {
                e += "&gift=1"
            }
            if ($("#groupbuy")[0].className == "current") {
                e += "&groupbuy=1"
            }
            if ($("#xianshi")[0].className == "current") {
                e += "&xianshi=1"
            }
            if ($("#virtual")[0].className == "current") {
                e += "&virtual=1"
            }
            $('a[name="ci"]').each(function () {
                if ($(this)[0].className == "current") {
                    r += $(this).attr("value") + "_"
                }
            });
            if (r != "") {
                e += "&ci=" + r
            }
            window.location.href = farm + "/Product/product_list.html" + e
        });
        $('a[nctype="items"]').click(function () {
            var e = new Date;
            if (e.getTime() - searchTimes > 300) {
                $(this).toggleClass("current");
                searchTimes = e.getTime()
            }
        });
        $('input[nctype="price"]').on("blur", function () {
            if ($(this).val() != "" && !/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test($(this).val())) {
                $(this).val("")
            }
        });
        $("#reset").click(function () {
            $('a[nctype="items"]').removeClass("current");
            $('input[nctype="price"]').val("");
            $("#area_id").val("")
        })
    })
}
function init_get_list(e, r) {
    order = e;
    key = r;
    curpage = 1;
    hasmore = true;
    $("#product_list .goods-secrch-list").html("");
    $("#footer").removeClass("posa");
    get_list()
}