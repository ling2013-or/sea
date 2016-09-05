<?php

namespace Api\Model;

use Think\Model;

/**
 * （库存）订单详情
 * Class DistributionOrder
 * @package Api\Model
 */
class DistributionOrderModel extends Model
{

    /**
     * 取消订单
     */
    const ORDER_CANCEL = 0;

    /**
     * 生成订单
     */
    const ORDER_CREATE = 1;

    /**
     * 订单已支付
     */
    const ORDER_PAY = 2;

    /**
     * 已发货
     */
    const ORDER_SEND = 3;

    /**
     * 订单完成
     */
    const ORDER_SUCCESS = 4;

    /**
     * 错误码
     * @var int
     */
    protected $code = 0;

    /**
     * 获取（库存）订单详情
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

        // 返回订单扩展表信息
        if (in_array('order_common', $extend)) {
            $order_info['extend_order_common'] = $this->getOrderCommonInfo(array('order_id' => $order_info['order_id']));
            $order_info['extend_order_common']['reciver_info'] = @unserialize($order_info['extend_order_common']['reciver_info']);
            $order_info['extend_order_common']['invoice_info'] = @unserialize($order_info['extend_order_common']['invoice_info']);
        }

        // 返回买家信息
        if (in_array('member', $extend)) {
            $order_info['extend_user'] = M('User')->field(true)->where(array('uid' => $order_info['user_id']))->find();
        }

        // 返回商品信息
        if (in_array('order_crop', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_id' => $order_info['order_id']));
            foreach ($order_goods_list as $value) {
                $order_info['extend_order_crop'][] = $value;
            }
        }
        return $order_info;
    }

    /**
     * 获取（库存）订单列表
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
            if (!empty($extend)) $order_list[$order['order_id']] = $order;
        }
        if (empty($order_list)) $order_list = $list;

        // 返回订单扩展信息
        if (in_array('order_common', $extend)) {
            $order_common_list = $this->getOrderCommonList(array('order_id' => array('in', array_keys($order_list))));
            foreach ($order_common_list as $value) {
                $order_list[$value['order_id']]['extend_order_common'] = $value;
                $order_list[$value['order_id']]['extend_order_common']['reciver_info'] = @unserialize($value['reciver_info']);
            }
        }

        // 返回会员信息
        if (in_array('member', $extend)) {
            $user_id_array = array();
            foreach ($order_list as $value) {
                if (!in_array($value['user_id'], $user_id_array)) {
                    $user_id_array[] = $value['user_id'];
                }
            }
            $user_fields = 'uid,user_name,nick_name,farm_name,user_phone,user_email';   // TODO 待增加
            $user_list = M('User')->where(array('uid' => array('in', $user_id_array)))->getField($user_fields);
            foreach ($order_list as $order_id => $order) {
                $order_list[$order_id]['extend_user'] = $user_list[$order['buyer_id']];
            }
        }

        // 返回商品信息
        if (in_array('order_crop', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderCropList(array('order_id' => array('in', array_keys($order_list))));
            foreach ($order_goods_list as $value) {
                $value['seed_image_url'] = $value['seed_image'];              // TODO 图片获取
                $order_list[$value['order_id']]['extend_order_goods'][] = $value;
            }
        }

        return $order_list;
    }

    /**
     * TODO 获取（库存）订单日志操作表
     * @param array $condition
     * @return mixed
     */
    public function getOrderLogList($condition = array())
    {
        return M('DistributionLog')->where($condition)->select();
    }

    /**
     * 获取（库存）订单扩展表列表
     * @param   array $condition 查询条件
     * @param   bool|string $fields 查询字段
     * @param   string $limit 限制条件
     * @return  mixed
     */
    public function getOrderCommonList($condition = array(), $fields = true, $limit = null)
    {
        return M('DistributionCommon')->field($fields)->where($condition)->limit($limit)->select();
    }

    /**
     * 获取（库存）订单扩展表详情（单条数据）
     * @param   array $condition 查询条件
     * @param   bool|string $fields 查询字段
     * @return  mixed
     */
    public function getOrderCommonInfo($condition = array(), $fields = true)
    {
        return M('DistributionCommon')->field($fields)->where($condition)->find();
    }

    /**
     * 获取（库存）订单商品表列表
     * @param   array $condition 查询条件
     * @param   string $fields 查询字段
     * @param   null $limit 查询限制
     * @param   string $order 排序
     * @param   null $group GROUP查询条件
     * @param   bool|false $key 是否使用字段信息作为索引（此字段结合 查询字段 使用，查询字段第一个字段为索引字段）
     * @return  mixed
     */
    public function getOrderCropList($condition = array(), $fields = '*', $limit = null, $order = 'id desc', $group = null, $key = false)
    {
        if ($key) {
            return M('DistributionCrop')->where($condition)->limit($limit)->order($order)->group($group)->getField($fields);
        } else {
            return M('DistributionCrop')->field($fields)->where($condition)->limit($limit)->order($order)->group($group)->select();
        }
    }

    /**
     * 生成订单
     * @param  string $cart_ids 购物车ID（格式：1,2,3,4,5）
     * @param  int $uid 用户ID
     * @param  int $address_id 送货地址ID
     * @param  string $from 订单来源（Android, IOS, WeChat, AliPay）
     * @return bool|int
     */
    public function createOrder($cart_ids, $uid, $address_id, $from = 'app')
    {
        // 获取用户基本资料
        $user_info = M('User')->field(true)->where(array('uid' => $uid))->find();

        $CartModel = D('DistributionCart');

        // 获取购物车列表
        $condition = array();
        $condition['user_id'] = $uid;
        $condition['cart_id'] = array('IN', $cart_ids);

        $cart_list = $CartModel->listCart($condition);

        // 获取商品最新的在售信息
        $cart_list = $CartModel->getStorageCartList($cart_list);

        if (empty($cart_list)) {
            $this->code = 51010;
            $this->error = '请选择要配送的农作物';
            return false;
        }

        /* 计算商品总重量开始 */
        $weight = 0;
        foreach ($cart_list as $info) {
            $weight += $info['crop_weight'];
        }

        /* 计算商品总重量结束 */
        $address_info = M('UserAddress')->where(array('id' => $address_id, 'uid' => $uid))->find();
        if (!$address_info) {
            $this->code = 51011;
            $this->error = '请选择收货地址';
            return false;
        }

        // 运费
        $shipping_fee = D('Transport')->calcShippingFee($weight, $address_info['area_id']);

        // 检测用户资金是够足够
        $userAccountModel = D('UserAccount');
        $account_balance = $userAccountModel->where(array('uid' => $uid))->getField('account_balance');
        if ($account_balance < $shipping_fee) {
            $this->code = 51012;
            $this->error = '账户资金不足';
            return false;
        }

        /* 向数据表Order表中添加数据开始 */
        $order = array();
        // 订单号
        $order['order_sn'] = $this->makeOrderSn($uid);
        // 配送总重量
        $order['crop_weight'] = $weight;
        // 订单状态（已下单，未支付）
        $order['order_status'] = self::ORDER_CREATE;
        // 订单来源地址
        $order['order_from'] = $from;
        // 购买用户ID
        $order['user_id'] = $uid;
        // 购买用户的用户名
        $order['user_name'] = $user_info['user_name'];
        // 用户属性（平台，注册用户）
        $order['is_platform'] = $user_info['is_platform'];
        // 运费
        $order['shipping_fee'] = $shipping_fee;
        // 下单时间
        $order['add_time'] = NOW_TIME;
        /* 创建订单 */
        $order_id = $this->add($order);
        if (!$order_id) {
            $this->code = 51013;
            $this->error = '创建订单失败';
            return false;
        }
        /* 向数据表Order表中添加数据结束 */

        /* 冻结用户帐户资金开始 */
        if ($shipping_fee > 0) {
            $data = array();
            $data['uid'] = $uid;
            $data['user_name'] = $user_info['user_name'];
            $data['affect_money'] = $shipping_fee;
            $data['order_sn'] = $order['order_sn'];
            $res = $userAccountModel->changeAccount('distribut_freeze', $data);
            if (!$res) {
                $this->code = 51014;
                $this->error = $userAccountModel->getError();
                return false;
            }
        }
        /* 冻结用户帐户资金结束 */

        /* 向数据表Order Common表中添加数据开始 */
        $order_common = array();
        $order_common['order_id'] = $order_id;  // 订单号

        //收货人信息
        $reciver_info = array();
        $reciver_info['address'] = $address_info['area_info'] . '&nbsp;' . $address_info['address'];
        $reciver_info['phone'] = $address_info['phone'];

        $order_common['daddress_id'] = $address_id;   // 收货人地址主键ID
        $order_common['reciver_name'] = $address_info['consignee'];
        $order_common['reciver_info'] = serialize($reciver_info);
        $order_common['reciver_province_id'] = $address_info['provice_id'];

        $res = M('DistributionCommon')->add($order_common);
        if (!$res) {
            $this->code = 51015;
            $this->error = '创建订单信息失败';
            return false;
        }
        /* 向数据表Order Common表中添加数据结束 */

        /* 向数据表Order Crop表中添加数据开始 */
        $order_crop = array();
        $storageModel = D('UserStorage');
        foreach ($cart_list as $crop) {
            if (!$crop['storage_state']) {
                $this->code = 51016;
                $this->error = '库存不足，请重新选择';
                return false;
            }

            /* 冻结会员库存开始 */
            $res = $storageModel->pickChangeStorage($uid, $crop['seed_id'], $crop['crop_weight'], $order['order_sn']);
            if (!$res) {
                $this->code = 51019;
                $this->error = $storageModel->getError();
                return false;
            }
            /* 冻结会员库存结束 */

            $order_crop[] = array(
                'order_id' => $order_id,    // 订单号
                'seed_id' => $crop['seed_id'], // 农作物（种子）ID
                'crop_name' => $crop['crop_name'], // 农作物名称
                'crop_image' => $crop['crop_image'],     // 农作物图片
                'crop_weight' => $crop['crop_weight'], // 农作物重量
                'plan_data' => serialize($res),
            );
        }
        $res = M('DistributionCrop')->addAll($order_crop);
        if (!$res) {
            $this->code = 51017;
            $this->error = '添加订单详情失败';
            return false;
        }
        /* 向数据表Order Crop表中添加数据结束 */

        /* 删除购物车商品开始 */
        $cartMap = array();
        $cartMap['cart_id'] = array('IN', $cart_ids);
        $cartMap['user_id'] = $uid;
        $res = $CartModel->where($cartMap)->delete();
        if (false === $res) {
            $this->code = 51018;
            $this->error = '清空购物车失败';
            return false;
        }
        /* 删除购物车商品结束 */

        return $order_id;
    }

    /**
     * 下单支付成功，处理资金流向
     * @param int $order_id 订单ID
     * @param int $user_id 用户ID
     * @return bool
     */
    public function orderSuccess($order_id, $user_id)
    {
        // 检测订单是否存在
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['user_id'] = $user_id;
        $condition['order_status'] = self::ORDER_CREATE;
        $condition['is_delete'] = 0;
        $order_info = $this->where($condition)->find();
        if (!$order_info) {
            $this->code = 51021;
            $this->error = '订单信息不存在';
            return false;
        }

        $res = $this->where($order_info['order_id'])->save(array('order_status' => self::ORDER_PAY));
        if (false === $res) {
            $this->code = -1;
            $this->error = '订单状态修改失败';
            return false;
        }

        if ($order_info['shipping_fee'] > 0) {
            // 账户资金变动
            $userAccountModel = D('UserAccount');

            // 买家资金变动以及买家库存变动
            $data = array();
            $data['uid'] = $user_id;
            $data['user_name'] = $order_info['user_name'];
            $data['affect_money'] = $order_info['shipping_fee'];
            $data['order_sn'] = $order_info['order_sn'];
            $res = $userAccountModel->changeAccount('distribut_finish', $data);
            if (!$res) {
                $this->code = 51022;
                $this->error = $userAccountModel->getError();
            }
        }
        return true;
    }

    /**
     * 取消未发货的订单
     * @param int $order_id 订单ID
     * @param int $user_id 用户ID
     * @return bool
     */
    public function orderCancel($order_id, $user_id)
    {
        // 检测订单是否存在
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['user_id'] = $user_id;
        $condition['is_delete'] = 0;
        $order_info = $this->where($condition)->find();
        if (!$order_info) {
            $this->code = 51031;
            $this->error = '订单信息不存在';
            return false;
        }

        $status = $order_info['order_status'];

        if ($status == self::ORDER_CANCEL) {
            $this->code = 51032;
            $this->error = '订单信息已取消';
            return false;
        }

        if ($status != self::ORDER_PAY && $status != self::ORDER_CREATE) {
            $this->code = 51033;
            $this->error = '配送订单禁止取消';
            return false;
        }

        if ($order_info['shipping_fee'] > 0) {
            // 账户资金变动
            $userAccountModel = D('UserAccount');

            $data = array();
            $data['uid'] = $user_id;
            $data['user_name'] = $order_info['user_name'];
            $data['affect_money'] = $order_info['shipping_fee'];
            $data['order_sn'] = $order_info['order_sn'];

            if ($status == self::ORDER_CREATE) {
                // 解冻资金账户
                $res = $userAccountModel->changeAccount('distribut_cancel', $data);
                if (!$res) {
                    $this->code = 51034;
                    $this->error = $userAccountModel->getError();
                }
            }

            if ($status == self::ORDER_CREATE) {
                // 退还资金账户
                $res = $userAccountModel->changeAccount('distribut_refund', $data);
                if (!$res) {
                    $this->code = 51035;
                    $this->error = $userAccountModel->getError();
                }
            }
        }

        // 库存解冻
        $crop_list = $this->getOrderCropList(array('order_id' => $order_id));
        $storageModel = D('UserStorage');
        foreach ($crop_list as $crop) {
            /* 冻结会员库存开始 */
            $res = $storageModel->psChangeStorage('pick_unfreeze', $user_id, $crop['summary_id'], $crop['crop_weight'], unserialize($crop['plan_data']), $order_info['order_sn']);
            if (!$res) {
                $this->code = 51036;
                $this->error = $storageModel->getError();
                return false;
            }
            /* 冻结会员库存结束 */
        }

        // 修改订单状态
        $res = $this->where(array('order_id'=>$order_id))->save(array('order_status'=>self::ORDER_CANCEL));
        if(false === $res) {
            $this->code = 51037;
            $this->error = '修改订单状态失败';
            return false;
        }

        return true;
    }

    /**
     * 确认收货
     * @param int $order_id 订单ID
     * @param int $user_id 用户ID
     * @return bool
     */
    public function confirmReceipt($order_id, $user_id)
    {
        // 检测订单是否存在
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['user_id'] = $user_id;
        $condition['is_delete'] = 0;
        $order_info = $this->where($condition)->find();
        if (!$order_info) {
            $this->code = 51041;
            $this->error = '订单信息不存在';
            return false;
        }

        $status = $order_info['order_status'];

        if ($status == self::ORDER_CANCEL) {
            $this->code = 51042;
            $this->error = '订单信息已取消';
            return false;
        }

        if($status == self::ORDER_SUCCESS) {
            return true;
        }

        if ($status != self::ORDER_PAY) {
            $this->code = 51043;
            $this->error = '尚未支付订单';
            return false;
        }

        // 库存解冻扣除
        $crop_list = $this->getOrderCropList(array('order_id' => $order_id));
        $storageModel = D('UserStorage');
        foreach ($crop_list as $crop) {
            /* 冻结会员库存开始 */
            $res = $storageModel->psChangeStorage('pick_pay', $user_id, $crop['summary_id'], $crop['crop_weight'], unserialize($crop['plan_data']), $order_info['order_sn']);
            if (!$res) {
                $this->code = 51044;
                $this->error = $storageModel->getError();
                return false;
            }
            /* 冻结会员库存结束 */
        }

        // 修改订单状态
        $res = $this->where(array('order_id'=>$order_id))->save(array('order_status'=>self::ORDER_SUCCESS));
        if(false === $res) {
            $this->code = 51037;
            $this->error = '确认收货失败';
            return false;
        }

        return true;
    }

    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $uid取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param int $uid 用户ID
     * @return string
     */
    public function makeOrderSn($uid)
    {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        return (date('y', time()) % 9 + 1) . sprintf('%013d', $uid) . sprintf('%02d', $num);
    }

    /**
     * TODO 添加订单日志
     * @param   array $data 日志内容
     * @return  mixed
     */
    public function addOrderLog($data)
    {
        $data['operate_role'] = str_replace(array('buyer', 'seller', 'system'), array('买家', '商家', '系统'), $data['operate_role']);
        $data['operate_time'] = NOW_TIME;
        return M('DistributionLog')->add($data);
    }

    /**
     * 返回模型错误状态码
     * @access public
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
}