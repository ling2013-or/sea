<?php
/**
 * 验证码检查
 * @param string $code 验证码
 * @param string $id 生成验证码时传入的ID
 * @return bool
 */
function verifycheck($code, $id = "")
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param bool $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 调用系统的API接口（静态）方法
 * 实例：pi('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param string $name 格式 [模块名]/接口名/方法名
 * @param array $var array|string  $vars 参数
 * @return mixed
 */
function api($name, $var = array())
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $class = array_pop($array);
    $module = $array ? array_pop($array) : 'Common';
    $callback = $module . '\\Api\\' . $class . 'Api::' . $method;
    if (is_string($var)) {
        parse_str($var, $var);
    }
    return call_user_func_array($callback, $var);
}

/**
 * 格式化价钱
 * TODO 按照条件可做后续修改
 * @param   float $money 钱币
 * @return  float
 */
function format_money($money = 0.00)
{
    return number_format($money, 2, '.', '');
}

/**
 * 格式化重量
 * @param   float $weight 重量
 * @return  float
 */
function format_weight($weight = 0.00)
{
    return number_format($weight, 2, '.', '');
}

/**
 * 身份证校验处理
 * @param  string $id_card 身份证号码
 * @return bool
 */
function validation_filter_id_card($id_card)
{
    if (strlen($id_card) == 18) {
        return idcard_checksum18($id_card);
    } elseif ((strlen($id_card) == 15)) {
        $id_card = idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    } else {
        return false;
    }
}

/**
 * 计算身份证校验码，根据国家标准GB 11643-1999
 * @param   int $idcard_base 身份证去除左后以为的数值
 * @return bool
 */
function idcard_verify_number($idcard_base)
{
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += (int)substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

/**
 * 将15位身份证升级到18位
 * @param   string $idcard 身份证号码
 * @return bool|string
 */
function idcard_15to18($idcard)
{
    if (strlen($idcard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
        } else {
            $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcard_verify_number($idcard);
    return $idcard;
}

/**
 * 18位身份证校验码有效性检查
 * @param string $idcard 身份证号码
 * @return bool
 */
function idcard_checksum18($idcard)
{
    if (strlen($idcard) != 18) {
        return false;
    }
    $idcard_base = substr($idcard, 0, 17);
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return false;
    } else {
        return true;
    }
}

/**
 * 产生一个指定长度的随机字符串,并返回给用户
 * @param int $len 产生字符串的长度
 * @return string 随机字符串
 */
function gen_random_string($len = 6)
{
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    // 将数组打乱
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 发送短信
 *  // TODO 更合理的解决方案
 * @param   string $mobile 手机号码
 * @param   string $content 短信内容
 * @param   int $type 短信类型   （如：1-短信验证码）
 * @param   int $admin 是否后台发送   （如：0-会员，1-后台）
 * @return  array
 */
function sendsms($mobile, $content, $type='', $admin = 0)
{
    // 获取短信接口配置
    $config = S('SMS_CONFIG');
    if (!$config) {
        $config = M('SmsConfig')->field('code,name,config')->where(array('is_default' => 1))->find();
        if ($config) {
            if ($config['config'] != '') {
                $config['config'] = unserialize($config['config']);
            }
            S('SMS_CONFIG', $config);
        }
    }
    if (!$config) {
        $result = array('status' => false, 'code' => 17, 'msg' => '获取接口信息失败');
    }

    if ($config['code'] == 'huyi') {
        $result = sms_huyi($mobile, $content);
    }
    //记录操作日志
    smsLog($mobile, $content, $config['code'], $result['status'], $admin);
    return $result;
}

/**
 * 互亿无线发送短信
 * @param   string $mobile 手机号码
 * @param   string $content 短信内容
 * @return  array
 */
function sms_huyi($mobile, $content)
{
    $sms_config = S('SMS_CONFIG');
    $config = $sms_config['config'];
    if (!(isset($config['huyi_account']) && !empty($config['huyi_account']) && isset($config['huyi_password']) && !empty($config['huyi_password']))) {
        return array('status' => false, 'msg' => '接口参数错误');
    }

    $url = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit';
    $param = array(
        'account' => $config['huyi_account'],
        'password' => md5($config['huyi_password']),
        'mobile' => $mobile,
        'content' => $content,
    );
    $param = http_build_query($param, '', '&', PHP_QUERY_RFC3986);

    $Curl = new \Common\Library\Curl\Curl();
    $Curl->post($url, $param);
    $res = xml_to_array($Curl->rawResponse);
    $res['status'] = $res['code'] == 2 ? true : false;

    return $res;
}

/**
 * XML转数组
 * @param   string $xml 待转化的XML
 * @return  array
 */
function xml_to_array($xml)
{
    $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $json = json_encode($obj);
    return json_decode($json, true);
}

/**
 * 获取用户资金变更类型
 * @param   string $type 类型
 * @return  string
 */
function get_pd_status($type)
{
    return str_replace(
        array(
            'distribut_pay',
            'distribut_freeze',
            'distribut_cancel',
            'distribut_comb_pay',
            'pay_seller',
            'order_pay',
            'order_freeze',
            'order_cancel',
            'order_comb_pay',
            'recharge',
            'cash_apply',
            'cash_pay',
            'cash_del',
            'refund',
        ),
        array(
            '库存配送下单支付',
            '库存配送下单冻结预存款',
            '库存配送取消订单解冻预存款',
            '库存配送下单支付被冻结的预存款',
            '支付卖家会员金钱',
            '下单支付预存款',
            '下单冻结预存款',
            '取消订单解冻预存款',
            '下单支付被冻结的预存款',
            '充值',
            '申请提现冻结预存款',
            '提现成功',
            '取消提现申请，解冻预存款',
            '退款',
        ),
        $type);
}

/**
 * 记录短信日志
 * @param   string $mobile 手机号码
 * @param   string $content 短信内容
 * @param   string $platform 短信平台
 * @param   string $status 短信发送状态
 * @param   string $type 短信发送地址（0 前台会员   1后台）
 * @return  null
 */
function smsLog($mobile, $content, $platform, $status, $type=0)
{
    //记录短信发送日志
    $code = '';
    if ($platform == 'huyi') {
        switch ($status) {
            case 001:
                $code = 1;
                break;
            default:
                $code = 0;
                break;
        }
    }

    $log = M('SmsLog');
    $data = array(
        'mobile' => $mobile,
        'content' => $content,
        'add_time' => NOW_TIME,
        'status' => $code,
        'type' => $type,
        'platform' => $platform,
        'code' => $status
    );
    //插入数据库
    $log->data($data)->add();


}

/**
 * 生成指定长度的验证码
 * @param int $length 所需要生成验证码的长度
 * @return int
 */
function code($length = 6) {
    return rand(pow(10,($length-1)), pow(10,$length)-1);
}