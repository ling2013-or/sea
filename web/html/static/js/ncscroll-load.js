function ncScrollLoad() {
    var page,curpage,hasmore,footer,isloading;

    ncScrollLoad.prototype.loadInit = function(options) {
        var defaults = {
                data:{},
                callback :function(){},
                resulthandle:''
            }
        var options = $.extend({}, defaults, options);
        if (options.iIntervalId) {
            page = options.page>0?options.page : pagesize;
            curpage = 1;
            hasmore = true;
            footer = false;
            isloading = false;
        }
        ncScrollLoad.prototype.getList(options);
        $(window).scroll(function(){
            if (isloading) {//防止scroll重复执行
                return false;
            }
            if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
                isloading = true;
                options.iIntervalId = false;
                ncScrollLoad.prototype.getList(options);
            }
        });
    }

    ncScrollLoad.prototype.getList = function(options){
        if (!hasmore) {
            $('.loading').remove();
            //ncScrollLoad.prototype.getLoadEnding();
            return false;
        }
        param = {};
        //参数
        if(options.getparam){
            param = options.getparam;
        }
        //初始化时延时分页为1
        if(options.iIntervalId){
            param.curpage = 1;
        }
        param.page = page;
        param.curpage = curpage;
        $.ajax({
            url:options.url,
            data:options.data,
            type:'post',
            dataType:'json',
            success:function(result){
            checkLogin(getCookie('token'));
            $('.loading').remove();
            curpage++;
            var data = result;
            //处理返回数据
            if(options.resulthandle){
                eval('data = '+options.resulthandle+'(data);');
            }

            if (!$.isEmptyObject(options.data)) {
                data = $.extend({}, options.data, data);
            }

            template.helper("$getLocalTime", function (e) {
                var t = new Date(parseInt(e) * 1e3);
                var r = "";
                r += t.getFullYear() + "年";
                r += t.getMonth() + 1 + "月";
                r += t.getDate() + "日 ";
                r += t.getHours() + ":";
                r += t.getMinutes();
                return r
            });
            template.helper("parseInt", function (e) {
                if(e){
                    return parseInt(e);
                }
                return false;
            });
            template.helper("eval", function (e) {
                if(e){
                    return eval(e);
                }
                return false;
            });
            template.helper("typeof", function (e) {
                if(e){
                    return typeof(e);
                }
                return false;
            });
            template.helper("str_length", function (e) {
                if(e){
                    if(e.length > 20){
                        return e.substring(0,20)+'...';
                    }else{
                        return e;
                    }

                }
                return false;
            });
            data.server = server;
            var html = template.render(options.tmplid, data);
            if(options.iIntervalId === false){
                $(options.containerobj).append(html);
            }else{
                $(options.containerobj).html(html);
            }
            hasmore = false;
            if(data.count > page * pagesize){
                hasmore = true;
            }
            if (!hasmore) {
                $('.loading').remove();
                //加载底部
                if ($('#footer').length > 0) {
                    //ncScrollLoad.prototype.getLoadEnding();
                    if (result.page_total == 0) {
                        $('#footer').addClass('posa');
                    }else{
                        $('#footer').removeClass('posa');
                    }
                }
            }
            if (options.callback) {
                options.callback.call('callback');
            }
            isloading = false;
        }});
    }

    //ncScrollLoad.prototype.getLoadEnding = function() {
    //    if (!footer) {
    //        footer = true;
    //        $.ajax({
    //            url: server+'/html/js/tmpl/footer.js',
    //            dataType: "script"
    //        });
    //    }
    //}
}