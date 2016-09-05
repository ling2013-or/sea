<?php
namespace Admin\Model;

use Think\Model;

/**
 * 订单管理模型
 * Class OrderModel
 * @package Admin\Model
 */
class OrderModel extends Model
{

    /**
     * 获取订单详情
     * @param array $condition 查询条件
     * @param array $extend 查询扩展内容
     * @param string $fields 查询字段
     * @return array|mixed
     */
    public function getOrderInfo($condition = array(), $extend = array(), $fields = '*')
    {
        $order_info = $this->field($fields)->where($condition)->find();
        if (empty($order_info)) {
            return array();
        }
        $order_info['state_txt'] = get_order_status($order_info['order_status']);

        // 返回订单扩展表信息
        if (in_array('order_common', $extend)) {
            $order_info['extend_order_common'] = $this->getOrderCommonInfo(array('order_id' => $order_info['order_id']));
            $order_info['extend_order_common']['reciver_info'] = @unserialize($order_info['extend_order_common']['reciver_info']);
            $order_info['extend_order_common']['invoice_info'] = @unserialize($order_info['extend_order_common']['invoice_info']);
        }

        // 返回买家信息
        if (in_array('member', $extend)) {
            $order_info['extend_user'] = M('User')->field(true)->where(array('uid' => $order_info['uid']))->find();
        }

        // 返回商品信息
        if (in_array('order_goods', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_id' => $order_info['order_id']));
            foreach ($order_goods_list as $value) {
                $order_info['extend_order_goods'][] = $value;
            }
        }
        return $order_info;
    }

    /**
     * 获取订单列表
     * @param array $condition 查询条件
     * @param string $field 查询字段
     * @param string $order 排序
     * @param string $limit 查询限制条数
     * @param array $extend 查询扩展 array('order_common','order_goods', 'member')
     * @return array
     */
    public function getOrderList($condition = array(), $field = '*', $order = 'order_id desc', $limit = '', $extend = array())
    {
        $list = $this->field($field)->where($condition)->order($order)->limit($limit)->select();
        if (!$list) return array();
        $order_list = array();
        foreach ($list as $order) {
            $order['state_txt'] = get_order_status($order['order_status']);
            $order['payment_name'] = get_order_payment_name($order['payment_code']);
            if (!empty($extend)) $order_list[$order['order_id']] = $order;
        }
        if (empty($order_list)) $order_list = $list;

        // 返回会员信息
        if (in_array('member', $extend)) {
            $user_id_array = array();
            foreach ($order_list as $value) {
                if (!in_array($value['uid'], $user_id_array)) {
                    $user_id_array[] = $value['uid'];
                }
            }
            $user_fields = 'uid,user_name,user_phone,user_email';   // TODO 待增加
            $user_list = M('User')->where(array('uid' => array('in', $user_id_array)))->getField($user_fields);
            foreach ($order_list as $order_id => $order) {
                $order_list[$order_id]['extend_user'] = $user_list[$order['uid']];
            }
        }


        // 返回商品信息
        if (in_array('order_goods', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_id' => array('in', array_keys($order_list))));
            foreach ($order_goods_list as $value) {
//                $value['goods_image_url'] = $value['goods_cover'];              // TODO 图片获取
                $order_list[$value['order_id']]['extend_order_goods'][] = $value;
            }
        }

        // 返回产品视频信息
        if (in_array('order_camera', $extend)) {
            //获取视频列表
            $order_goods_list = $this->getOrderCameraList(array('order_id' => array('in', array_keys($order_list))));
            foreach ($order_goods_list as $value) {
                $order_list[$value['order_id']]['extend_order_camera'][] = $value;
            }
        }

        return $order_list;
    }

    /**
     * 获取订单日志操作表
     * @param array $condition
     * @return mixed
     */
    public function getOrderLogList($condition = array())
    {
        return M('OrderLog')->where($condition)->select();
    }

    /**
     * 获取订单扩展表详情（单条数据）
     * @param   array $condition 查询条件
     * @param   bool|string $fields 查询字段
     * @return  mixed
     */
    public function getOrderCommonInfo($condition = array(), $fields = true)
    {
        return M('OrderCommon')->field($fields)->where($condition)->find();
    }

    /**
     * 获取支付单列表
     * @param   array $condition 查询条件
     * @param   bool|string $fields 查询字段
     * @param   string $limit 限制条件
     * @param   string $order 排序
     * @param   bool|false $key 是否使用字段信息作为索引（此字段结合 查询字段 使用，查询字段第一个字段为索引字段）
     * @return  mixed
     */
    public function getOrderPayList($condition = array(), $fields = true, $limit = '', $order = 'id desc', $key = false)
    {
        if ($key) {
            return M('OrderPay')->where($condition)->limit($limit)->order($order)->getField($fields);
        } else {
            return M('OrderPay')->field($fields)->where($condition)->order($order)->limit($limit)->select();
        }
    }

    /**
     * 获取订单支付表详情（单条数据）
     * @param   array $condition 查询条件
     * @param   bool|string $fields 查询字段
     * @return  mixed
     */
    public function getOrderPayInfo($condition = array(), $fields = true)
    {
        return M('OrderPay')->field($fields)->where($condition)->find();
    }

    /**
     * 获取订单商品表列表
     * @param   array $condition 查询条件
     * @param   string $fields 查询字段
     * @param   null $limit 查询限制
     * @param   string $order 排序
     * @param   null $group GROUP查询条件
     * @param   bool|false $key 是否使用字段信息作为索引（此字段结合 查询字段 使用，查询字段第一个字段为索引字段）
     * @return  mixed
     */
    public function getOrderGoodsList($condition = array(), $fields = '*', $limit = null, $order = 'id desc', $group = null, $key = false)
    {
        if ($key) {
            $return = M('OrderGoods')->where($condition)->limit($limit)->order($order)->group($group)->getField($fields);
            //获取分区信息
            if($return['zone_id']) {
                $zcondition = array('id' => $return['zone_id']);
                $return['zone_title'] = $this->getZoneList($zcondition,'title');
            }
        } else {
            $return = M('OrderGoods')->field($fields)->where($condition)->limit($limit)->order($order)->group($group)->select();
            //获取分区信息
            foreach($return as $s=>$v){
                if($v['zone_id']){
                    $zcondition =array('id'=>$return[$s]['zone_id']);
                    $return[$s]['zone_title'] = $this->getZoneList($zcondition);
                }
            }
        }
        //获取产品分区 goods_zone
        return $return;
    }

    /**
     * 获取分区信息
     * @param array $condition
     * @param string $field
     * @param null $limit
     * @param string $order
     * @param null $group
     * @param bool $key
     * @return mixed
     */
    public function getZoneList($condition = array(),$field='title',$limit = null,$order = 'id desc',$group = null,$key=true){
        if ($key) {
            $return = M('GoodsZone')->where($condition)->limit($limit)->order($order)->group($group)->getField($field);
        } else {
            $return = M('GoodsZone')->field($field)->where($condition)->limit($limit)->order($order)->group($group)->select();
        }
        return $return;
    }

    /**
     * 获取订单视频列表
     * @param   array $condition 查询条件
     * @param   string $fields 查询字段
     * @param   null $limit 查询限制
     * @param   string $order 排序
     * @param   null $group GROUP查询条件
     * @param   bool|false $key 是否使用字段信息作为索引（此字段结合 查询字段 使用，查询字段第一个字段为索引字段）
     * @return  mixed
     */
    public function getOrderCameraList($condition = array(), $fields = '*', $limit = null, $order = 'id desc', $group = null, $key = false)
    {
        if ($key) {
            return M('OrderCamera')->where($condition)->limit($limit)->order($order)->group($group)->getField($fields);
        } else {
            return M('OrderCamera')->field($fields)->where($condition)->limit($limit)->order($order)->group($group)->select();
        }
    }

    /**
     * 返回订单允许操作的状态
     * @param $operate
     * @param $order_info
     * @return bool
     */
    public function getOrderOperateState($operate, $order_info)
    {
        if (!is_array($order_info) || empty($order_info)) return false;

        switch ($operate) {

            //已取消
            case 'cancel':
                $state = ($order_info['order_status'] == 0);
                break;

            //已支付
            case 'pay_yes':
                $state = in_array($order_info['order_status'],array(0,1));
                break;
            //养殖中
            case 'breed':
                $state = ($order_info['order_status'] == 2);
                break;

            //待评论
            case 'rate':
                $state = ($order_info['order_status'] == 4);
                break;

            //已完成
            case 'over':
                $state = in_array($order_info['order_status'],array(5,6,7));
                break;
            //分享
            case 'breed_over':
                $state = $order_info['order_status'] == 3;
                break;
            //分享
            case 'share':
                $state = $order_info['order_status'] == 9;
                break;

            default:
                $state = false;
                break;
        }
        return $state;
    }

    /**
     * 添加订单日志
     * @param   array $data 日志内容
     * @return  mixed
     */
    public function addOrderLog($data)
    {
        $data['operate_role'] = str_replace(array('buyer', 'seller', 'system'), array('买家', '商家', '系统'), $data['operate_role']);
        $data['operate_time'] = NOW_TIME;
        return M('OrderLog')->add($data);
    }



}