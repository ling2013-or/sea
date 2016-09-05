
(function($) {
    /**
     * 生成一个表单项的提示信息，生成的id规则:err+表单项name
     * 只支持单一对象,样式名称：label-error
     * @param string msg 必须
     */
    $.fn.genError = function(msg){
        var $obj = $(this);
        var suffix = '';
        var spanClass =  'label-error';
        try{
            var form = $obj[0].form;
            suffix = $(form).attr('suffix');
            suffix = suffix?'_'+suffix:'';
        }catch(ex){}

        var errid = 'err' + $obj.attr('name')+suffix;
        var errobj = document.getElementById(errid);
        errobj = errobj ? $(errobj) : $('<span>',{'id':errid}).insertAfter($obj);
        errobj.removeClass().addClass(spanClass);
        if(msg=='')
        {
            errobj.html(msg).hide();
            $obj.removeClass('input-error');
        }else{
            errobj.html(msg).show();
            $obj.addClass('input-error');
        }
    };
    /**
     * 表单验证
     * 属性：required=bool; compare=id; len=min,max; func=函数名,regc=正则,不用加前后辍,
     * func($obj)：自定义验证函数，$obj当前对象，返回bool
     * regc常量有email,url,ip,date,alpha,alpha_dash,alpha_numeric,digit,numeric,username,password
     * 属性名后面加Info为验证失败的提示信息，如requiredInfo="必填"
     * @param string|function trigger 可选项 触发验证的选择器字符串或者是参数callback
     * @param function callback 可选项验证之后的回调函数
     * @return {*}
     */
    $.fn.formCheck = function(trigger,callback){
        var eventName = 'submit';
        var regConst = {
            'email':/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
            'url':/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
            'ip':/^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
            'date':/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/,
            // 检查一个字符串是否只有英文字母组成
            'alpha':/^[a-zA-Z]$/,
            // 检查一个字符串是否包含英文字母，数字，下划线和破折号只。
            'alpha_dash':/^[a-zA-Z_-]$/,
            // 检测是否是只包含[A-Za-z0-9]
            'alpha_numeric':/^[A-Za-z0-9]$/,
            // 检查一个字符串是否由数字（没有点或破折号） 。
            'digit':/^[0-9]$/,
            // 数，包括正，负，和浮点十进制。
            'numeric':/^[\-\+]?(([0-9]+)([\.,]([0-9]+))?|([\.,]([0-9]+))?)$/,
            //中英文、数字、“_”或减号
            'username':/^[0-9a-zA-Z\u4e00-\u9fa5_-]*$/,
            //半角字符（字符、数字、符号）
            'password':/^([\w\~\!\@\#\$\%\^\&\*\(\)\+\`\-\=\[\]\\{\}\|\;\'\:\"\,\.\/\<\>\?]+){6,20}$/,
            'password2':/^(\w){6,20}$/,
            'mobile':/^(0|86|17951)?(13[0-9]|15[012356789]|17[0135678]|18[0-9]|14[57])[0-9]{8}$/,
            'organizationNo':/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]$/,
            'areacode':/^(0[0-9]{2,3})$/,
            'telphone':/^(\d{8})$/,
            'postcode':/^[1-9]\d{5}(?!\d)$/,
            'integer':/^\d+$/,
            'integer2':/^[1-9]\d*|0$/,
            'integer3':/^[1-9]\d*$/,
            'integer4':/^\d*\.?\d{0,2}$/,
            'integer5':/^[1-9]*[1-9][0-9]*$/,
            //'format':/^[^\<\>]$/
            'personID':/^[1-9]{1}[0-9]{14}$|^[1-9]{1}[0-9]{16}([0-9]|[xX])$/

        };
        return this.each(function(){
            var $form = $(this);
            $form.attr('novalidate','novalidate');
            var $trigger = $form;
            if(typeof(trigger)=='string'){
                $trigger = $form.find(trigger);
                $trigger = $trigger.size()>0 ? $trigger : $(trigger);	// $form.find(trigger);
                eventName = 'click';
            }else{
                callback = trigger;
            }


            var err = {};
            $form.find('input,textarea,select').focus(function(){
                var $this = $(this);
                var val = $.trim($this.val());
               // $this.genError('');
            }).blur(function(){
                    var $this = $(this);
                    var req = $this.attr('required');
                    var compare = $this.attr('compare');
                    var len = $this.attr('len');
                    var regc = $this.attr('regc');
                    var func = $this.attr('func');
                    var regexp = regConst[regc] || $this.attr('reg');
                    var errid = 'err' + this.name;

                    if(!req && !regexp && !compare && !len && !func){
                        err[errid] = null;
                        $this.genError('');
                        return true;
                    }
                    if(typeof(regexp)=='string' && regexp!=''){
                        regexp = new RegExp("^"+regexp+"$",'ig');
                    }
                    var val = $.trim($this.val());
                    var vlen = val.length;
                    len =  len ? len.split(',') : len;

                    var reqInfo = $this.attr('requiredInfo') || '不能为空';
                    var regInfo = $this.attr('regcInfo') || '输入项格式错误';
                    var compareInfo = $this.attr('compareInfo') || '两次输入不一致';
                    var lenInfo = len ? ($this.attr('lenInfo') || '长度必须在'+len[0]+'-'+len[1]+'之间'):'';
                    var funcInfo = $this.attr('funcInfo') || '你输入的信息错误';

                    if(req && val=='')
                    {
                        err[errid] = $this;
                        $this.genError(reqInfo);
                        return true;
                    }else
                    {
                        err[errid] = null;
                        $this.genError('');
                    }
                    if(len && (vlen > 0 && vlen < len[0] || vlen >len[1]))
                    {
                        err[errid] = $this;
                        $this.genError(lenInfo);
                        return true;
                    }else
                    {
                        err[errid] = null;
                        $this.genError('');
                    }
                    if(regexp && val!='')
                    {
                        if(!regexp.test(val)){
                            err[errid] = $this;
                            $this.genError(regInfo);
                            return true;
                        }else
                        {
                            err[errid] = null;
                            $this.genError('');
                        }
                    }
                    if(compare && compare!='' && $('#'+compare).val()!=val){
                        err[errid] = $this;
                        $this.genError(compareInfo);
                        return true;
                    }else{
                        err[errid] = null;
                        $this.genError('');
                    }
                    if(func && func!=''){
                        eval('var cf ='+func);
                        if(!cf($this))
                        {
                            err[errid] = $this;
                            $this.genError(funcInfo);
                            return true;
                        }else{
                            err[errid] = null;
                            $this.genError('');
                        }
                    }
                });
            $trigger.bind(eventName,function(){
                $form.find('input,textarea,select').blur();
                var res = true;
                for(var r in err){
                    if(!err[r])continue;
                    res = false;
                    err[r].focus();
                    break;
                }
                if(callback){
                    callback(res,$(this));
                    return false;
                }
                else return res;
            });
        });
    }
})(jQuery);