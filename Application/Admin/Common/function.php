<?php
/**
 * 后台公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    if (session('admin.admin_id')) {
        return session('admin.admin_id');
    } else {
        return 0;
    }
}

/**
 * 获取配置的分组
 * @param   int $group 配置分组
 * @return  string
 */
function get_config_group($group = 0)
{
    $list = C('CONFIG_GROUP_LIST');
    return $group ? $list[$group] : '';
}

/**
 * 获取配置的类型
 * @param   int $type 配置类型
 * @return  string
 */
function get_config_type($type = 0)
{
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取提现状态的文字信息
 * @param null|int $status 提现状态
 * @return bool|string              文字描述，false表示为获取到
 */
function get_withdraw_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case -1:
            return '已删除';
            break;
        case 0:
            return '待审中';
            break;
        case 1:
            return '处理中';
            break;
        case 2:
            return '提现成功';
            break;
        case 3:
            return '提现失败';
            break;
        default:
            return false;
            break;
    }
}

/**
 * 权限管理中的菜单状态
 * @param null|int $state 菜单状态
 * @return  string 返回字符串是或否
 */
function get_auth_state($state = 0)
{
    return $state ? '<span style="color:#0A0">是</span>' : '<span style="color:#C00">否</span>';
}

/**
 * 分析枚举类型配置值 格式 a:名称1,b:名称2
 * @param $string
 * @return array
 */
function parse_config_attr($string)
{
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 获取status字段的说明
 * @param bool|int $status 状态值
 * @return string 返回字符串 -1删除 0禁用 1正常
 */
function get_common_status($status = false)
{
    if ($status === false) return '未知';
    switch ($status) {
        case 0:
            return '<span style="color:#C00">禁用</span>';
            break;
        case 1:
            return '<span style="color:#0A0">正常</span>';
            break;
        default:
            return '未知';
            break;
    }
}

/**
 * 获取支付方式状态的文字信息
 * @param null|int $status 状态
 * @return bool|string
 */
function get_payment_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case 0:
            return '关闭中';
            break;
        case 1:
            return '开启中';
            break;
        default:
            return false;
            break;
    }
}

/**
 * 获取支付方式名称
 * @param null|int $status 状态
 * @return bool|string
 */
function get_pay_type($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case 0:
            return '---';
            break;
        case 1:
            return '支付宝';
            break;
        case 2:
            return '微信';
            break;
        case 3:
            return '线下支付';
            break;
        default:
            return '---';
            break;
    }
}

/**
 * 获取会员状态的文字信息
 * @param   null|int $status 状态 -1-删除，0-正常，1-锁定，2-冻结
 * @return  bool|string
 */
function get_user_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case -1:
            return '已删除';
            break;
        case 0:
            return '正常';
            break;
        case 1:
            return '已锁定';
            break;
        case 2:
            return '已冻结';
            break;
        default:
            return false;
            break;
    }
}

/**
 * 获取摄像头状态的文字信息
 * @param   null|int $status 状态
 * @return  bool|string
 */
function get_camera_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case -1:
            return '已删除';
            break;
        case 0:
            return '正常';
            break;
        case 1:
            return '已禁用';
            break;
        default:
            return false;
            break;
    }


}

/**
 * 转换社区状态
 * @param null $status 社区状态
 * @return string
 */
function get_article_status($status = null)
{
    switch ($status) {
        case 0:
            return '禁用';
            break;
        case 1:
            return '开启';
            break;
        case -1:
            return '删除';
            break;
        default :
            return '未知';
    }
}

/**
 * 转换社区状态
 * @param null $status 社区状态
 * @return string
 */
function get_comment_status($status = null)
{
    switch ($status) {
        case 1:
            return '开启';
            break;
        case -1:
            return '删除';
            break;
        case 0:
            return '禁止';
            break;
        case 2:
            return '待审核';
            break;
        default :
            return '未知';
    }
}

/**
 * 转换消息状态
 * @param null $status 社区状态
 * @return string
 */
function get_news_status($status = null)
{
    switch ($status) {
        case 2:
            return '未读';
            break;
        case 1:
            return '已读';
            break;
        case -1:
            return '删除';
            break;
        default :
            return '未知';
    }
}


/**
 * 获取反馈状态的文字信息
 * @param   null|int $status 状态
 * @return  bool|string
 */
function get_feedback_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case 0:
            return '未处理';
            break;
        case 1:
            return '处理中';
            break;
        case 2:
            return '已处理';
            break;
        default:
            return false;
            break;
    }
}


/**
 * @param 消息模板的状态
 * @param null $status
 * @return bool|string
 */
function get_sms_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case 0:
            return '禁用';
            break;
        case 1:
            return '启用';
            break;
        default:
            return false;
            break;
    }
}

/**
 * 获取会员物物交换状态的文字信息
 * @param   null|int $status 状态
 * @return  bool|string
 */
function get_object_exchange_status($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case -1:
            return '已删除';
            break;
        case 0:
            return '待交换';
            break;
        case 1:
            return '交换成功';
            break;
        case 2:
            return '已撤销';
            break;
        default:
            return false;
            break;
    }
}

/**
 * @param 消息模板的操作
 * @param null $status
 * @return  bool|string
 */
function get_sms_operator($status = null)
{
    if ($status === null) return false;
    switch ($status) {
        case 0:
            return '启用';
            break;
        case 1:
            return '禁用';
            break;
        default:
            return false;
            break;
    }
}


/**
 * @param null $status 手机短信日志状态判断
 * @return bool|string 如果没有参数返回false,
 * @return bool|string 0 短信发送失败
 * @return bool|string 1 短信发送成功
 */
function get_sms_log($status = null)
{
    if ($status === null) {
        return false;
    }

    switch ($status) {
        case 0:
            return '<span style="color: red;">失败</span>';
            break;
        case 1:
            return '<span style="color: green;">成功</span>';
            break;
        default:
            return false;
            break;
    }
}

/**
 * @param null $type 短信日志中标识该短信是通过前台还是后台
 * @return bool|string 0 后台
 * @return bool|string 1前台
 * @return bool|string false 未知
 */
function get_sms_type($type = null)
{
    if ($type === null) {
        return false;
    }

    switch ($type) {
        case 0:
            return '后台';
            break;
        case 1:
            return '前台';
            break;
        default:
            return false;
            break;
    }
}

/**
 * 获取订单状态的文字信息
 * @param   int $state 订单状态
 * @return bool|string
 */
function get_order_status($state)
{
    switch ($state) {
        case 0:
            $order_state = '<span style="color:#999">待付款</span>';
            break;
        case 1:
            $order_state = '<span style="color:#36C">已取消</span>';
            break;
        case 2:
            $order_state = '<span style="color:#F30">已付款</span>';
            break;
        case 3:
            $order_state = '<span style="color:#F30">养殖中</span>';
            break;
        case 4:
            $order_state = '<span style="color:#F30">待评论</span>';
            break;
        case 5:
            $order_state = '<span style="color:#999">已完成</span>';
            break;
        case 6:
            $order_state = '<span style="color:#999">退款中</span>';
            break;
        case 7:
            $order_state = '<span style="color:#999">已退款</span>';
            break;
        default:
            $order_state = false;
            break;
    }
    return $order_state;
}

/**
 * 根据支付编码获取支付方式
 * @param   string $code 支付方式编码
 * @return  string
 */
function get_order_payment_name($code)
{
    $payment = S('PAYMENT');
    if (!$payment) {
        $payment = M('Payment')->getField('code,name');
        S('PAYMENT', $payment);
    }
    return isset($payment[$code]) ? $payment[$code] : '';
}


/**
 * 获取后台管理员日志状态
 * @param string $status
 * @return string
 */
function get_log_status($status = null)
{
    switch ($status) {
        case 0:
            return '失败';
            break;
        case 1:
            return '正常';
            break;

        default:
            return '未知';
            break;
    }
}

/**
 * 获取用户注册类型 0-真实用户，1-平台用户
 * @param   int    $is_platform     用户类型
 * @return  string
 */
function get_user_platform_type($is_platform)
{
    return str_replace(array(0, 1), array('真实用户', '平台用户'), $is_platform);
}

/**
 * 获取农场发货地址是否是默认状态
 * @param null $status 农场发货地址状态
 * @return string 返回状态值对应的描述
 */
function get_default($status = null)
{
    if($status == 1){
        return '是';
    }else{
        return '否';
    }
}

/**
 * 根据地区ID查询地区名称
 * @param $id 所需要查询的ID
 * @param string $field ID对应的字段
 * @return mixed 返回地区命长
 */
function get_area_name($id , $field = 'area_id'){
    $area = M('Area');
    $where[$field] = array('eq',$id);
    $result = $area->field('*')->where($where)->find();
    return $result['area_name'];
}

/**
 * 获取快递公司的状态
 * @param null $status 表示快递公司的状态
 * @return bool|string 返回状态值对应的描述
 */
function get_express_status($status = null){
    if($status === null){
        return false;
    }
    if($status == 1){
        return '正常';
    }else{
        return '禁用';
    }
}

/**
 * 获取养殖方案状态
 * @param null $status
 * @return string
 */
function get_plan_status($status = null){
    $array = array(0=>'正常',-1=>'删除');
    if(isset($array[$status])){
        $return = $array[$status];
    }else{
        $return = '暂无';
    }
    return $return;

}

/**
 * 获取收货地址是否为默认地址
 * @param null $code
 * @return string
 */
function get_address_default($code=null){
    if(empty($code) && $code == null) return '';
    $array = [0=>'否',1=>'是'];
    if(isset($array[$code])){
        $return = $array[$code];
    }else{
        $return = '未知';
    }
    return $return;
}

/**
 * 获取收货地址状态
 * @param null $code
 * @return string
 */
function get_address_status($code=null){
    if(empty($code) && $code == null) return '';
    $array = [0=>'正常'];
    if(isset($array[$code])){
        $return = $array[$code];
    }else{
        $return = '未知';
    }
    return $return;
}

/**
 * 获取订单类型 type 0:产品 1：套餐
 * @param $type @类型编号
 * @return string
 */
function getOrderType($type){
    switch($type){
        case 0:
            $return = '商品';
            break;
        case 1:
            $return = '套餐';
            break;
        default:
            $return = '';
            break;
    }
    return $return;
}

/**
 * 获取产品类型
 * @param $type
 * @return string
 */
function get_goods_type($type){
    $list = [0=>'产品',1=>'套餐'];
    $return = isset($list[$type])?$list[$type]:'';
    return $return;
}


