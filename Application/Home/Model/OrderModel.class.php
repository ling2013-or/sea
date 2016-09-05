<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/3
 * Time: 20:22
 */

namespace Home\Model;


use Think\Model;

class OrderModel extends Model
{

    /**
     * 取消订单
     * 0：未支付 1：已取消  2：已支付 3：养殖中  :4：待评论(养殖完成)
     *  5：已完成  6：退款中 7：已退款
     */
    const ORDER_CANCEL = 1;

    /**
     * 生成订单（待支付）
     */
    const ORDER_CREATE = 0;

    /**
     * 订单已支付
     */
    const ORDER_PAY = 2;

    /**
     * 养殖中
     */
    const ORDER_BREED = 3;

    /**
     * 待评价
     */
    const ORDER_EVALUATE = 4;

    /**
     * 订单完成
     */
    const ORDER_SUCCESS = 5;

    /**
     * 退款中
     */
    const ORDER_REFUND_CONFIRM = 6;

    /**
     * 确认收货（已到达目的地，等待确认）
     */
    const ORDER_REFUND = 7;
    /**
     * 删除订单（用户购买已完成）
     */
    const ORDER_DEL = -1;
    /**
     * 错误码
     * @var int
     */
    protected $code = 0;

    /**
     * 计算购买商品所需费用
     * @param  string $cart_ids 购物车ID（格式：1,2,3,4,5）
     * @param  int $uid 当前用户UID
     */
    public function calcBuyList()
    {
        $cart = session('cart');
        $goods_type = $cart['goods_type'];
        $price = $cart['total'];
        $return = $cart;
        return [$goods_type, $price, $return];

    }


    /**
     * 生成订单
     * @param  string $cart_ids 购物车ID（格式：1,2,3,4,5）
     * @param  int $uid 用户ID
     * @param  int $address_id 送货地址ID， 0-表示不配送，大于0-表述送货且是送货地址ID
     * @param  string $from 订单来源（Android, IOS, WeChat, AliPay）
     * @return bool|int
     */
    public function createOrder($address_id)
    {
        $uid = session('id');
        // 获取用户基本资料
        $user_info = M('User')->field(true)->where(array('uid' => $uid))->find();
        //获取用户的收货地址
        //查看当前用户的收货地址是否存在

        if ($address_id > 0) {
            $address_info = M('UserAddress')->where(array('id' => $address_id, 'uid' => $uid))->find();
            if (!$address_info) {
                $this->code = 51211;
                $this->error = '请选择收货地址';
                return false;
            }
        }else{
            $this->code = 51211;
            $this->error = '请选择收货地址';
            return false;
        }

        list($order_type, $goods_total, $goods_info) = $this->calcBuyList();
        if (empty($goods_info)) {
            $this->code = 51212;
            $this->error = '请选择要购买的商品';
            return false;
        }


        /* 向数据表Order表中添加数据开始 */
        $order = array();
        // 订单号
        $order['order_sn'] = $this->makeOrderSn($uid);
        // 商品总额
        $order['order_price'] = $goods_total;
        // 订单状态（已下单，未支付）
        $order['order_status'] = self::ORDER_CREATE;
        //订单产品类型（1：套餐 0：产品）
        $order['type'] = $order_type;

        // 购买用户ID
        $order['uid'] = $uid;

        // 下单时间
        $order['add_time'] = NOW_TIME;
        //收货人省份ID
        $order['province_id'] = session('cart.receive.province_id');
        //收货人城市ID
        $order['city_id'] = $address_info['city_id'];
        //收货人地址ID
        $order['address'] = $address_info['area_info'];
        //收货手机号码
        $order['reciver_tel'] = $address_info['phone'];
        //收货人手机号码
        $order['reciver_name'] = $address_info['consignee'];
        /* 创建订单 */
        $order_id = $this->add($order);
        if (!$order_id) {
            $this->code = 51214;
            $this->error = '创建订单失败';
            return false;
        }
        /* 向数据表Order表中添加数据结束 */

        /* 向数据表Order Common表中添加数据开始 */
        $order_common = array();
        $order_common['order_id'] = $order_id;  // 订单号

        /* 向数据表Order Goods表中添加数据开始 */
        $order_goods = array();
        //判断是产品还是套餐
        if ($order_type == 0) {
            //产品
            $order_goods[] = array(
                'order_id' => $order_id,    // 订单号
                'goods_id' => $goods_info['goods_id'],
                'goods_price' => $goods_info['goods_price'],
                'goods_name' => $goods_info['goods_name'],
                'goods_cover' => $goods_info['goods_cover'],
                'goods_type' => $goods_info['goods_type'],
                'goods_num' => $goods_info['goods_num'],
                'zone_id' => $goods_info['zone_id'],
            );
        } else {
            //套餐
            foreach ($goods_info['extend'] as $goods) {
                $order_goods[] = array(
                    'order_id' => $order_id,    // 订单号
                    'goods_id' => $goods['goods_id'],// 产品ID
                    'goods_price' => $goods['goods_price'], // 产品价格
                    'goods_name' => $goods['goods_name'],// 产品名称
                    'goods_cover' => $goods['goods_cover'],// 商品图片
                    'goods_type' => $goods['goods_type'],
                    'goods_num' => $goods['goods_num'],// 产品数量
                    'zone_id' => $goods['zone_id'], // 分区ID
                    'camera_id' => $goods['video_id'], // 视频编号
                );

            }
        }
        $res = M('OrderGoods')->addAll($order_goods);
        if (!$res) {
            $this->code = 51217;
            $this->error = '添加订单详情失败';
            return false;
        }
        /* 向数据表Order Goods表中添加数据结束 */
        /* 冻结账户资金结束 */
        return $order_id;
    }

    /**
     * 下单支付成功，处理资金流向
     * @param int $order_id 订单ID
     * @param int $uid 用户ID
     * @param bool $is_send 是否发货
     * @return bool
     */
    public function orderSuccess($order_id, $uid, $is_send = false)
    {
        // 检测订单是否存在
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $uid;
        $condition['order_status'] = self::ORDER_CREATE;
        $condition['is_delete'] = 0;
        $order_info = $this->where($condition)->find();
        if (empty($order_info['buyer_name'])) {
            $this->code = 51230;
            $this->error = '用户名不能为空';
        }
        if (!$order_info) {
            $this->code = 51221;
            $this->error = '订单信息不存在';
            return false;
        }

        // 获取订单详情
        $order_goods_list = M('OrderGoods')
            ->alias('o')
            ->join('__GOODS__ AS goods ON o.goods_id = goods.id', 'LEFT')
            ->field('o.*,goods.store_id')
            ->where(array('o.order_id' => $order_id))
            ->select();
        if (!$order_goods_list) {
            $this->code = 51222;
            $this->error = '订单商品不存在';
            return false;
        }
        // 账户资金变动
        $userModel = M('User');
        $userAccountModel = D('UserAccount');
        $userStorageModel = D('UserStorage');
        // 卖家资金变动以及卖家库存变动
        // TODO 平台账户资金变动以及平台库存变动
        foreach ($order_goods_list as $goods) {
            if ($goods['store_id'] <= 0) continue;
            $data = array();
            $user_name = $userModel->where(array('uid' => $goods['store_id']))->getField('user_name');
            $data['uid'] = $goods['store_id'];
            $data['user_name'] = $user_name;
            $data['affect_money'] = $goods['pay_amount'];
            $data['seed_name'] = $goods['goods_name'];
            $res = $userAccountModel->changeAccount('pay_seller', $data);
            if (!$res) {
                $this->code = 51223;
                $this->error = $userAccountModel->getError();
                return false;
            }
            // 库存变动
            $res = $userStorageModel->sellChangeStorage('sell_pay', $goods['store_id'], $goods['storage_id'], $goods['goods_weight']);
            if (!$res) {
                $this->code = 51224;
                $this->error = $userAccountModel->getError();
                return false;
            }
        }

        // 买家资金变动以及买家库存变动
        $data = array();
        $data['uid'] = $uid;
        $data['user_name'] = $order_info['buyer_name'];
        $data['affect_money'] = $order_info['order_amount'];
        $data['order_sn'] = $order_info['order_sn'];
        $res = $userAccountModel->changeAccount('order_comb_pay', $data);
        if (!$res) {
            $this->code = 51225;
            $this->error = $userAccountModel->getError();
            return false;
        }

        if (!$is_send) {
            // 直接修改库存， 更改订单状态
            // TODO 库存变动
            foreach ($order_goods_list as $goods) {
                // 库存变动
                $res = $userStorageModel->changeStorage('buy_add', $uid, $goods['seed_id'], $goods['plan_id'], $goods['goods_weight'], $order_info['order_sn']);
                if (!$res) {
                    $this->code = 51226;
                    $this->error = $userAccountModel->getError();
                    return false;
                }
            }

            // 修改订单状态（不需要配送，然后订单状态改为待评价）
            $status = $this->where(array('order_id' => $order_id))->save(array('order_status' => self::ORDER_EVALUATE));
            if (!$status) {
                $this->code = 51227;
                $this->error = '修改订单状态失败';
                return false;
            }
        } else {
            // 修改订单状态(订单需要配送，代发货状态)
            $status = $this->where(array('order_id' => $order_id))->save(array('order_status' => self::ORDER_PAY));
            if (!$status) {
                $this->code = 51228;
                $this->error = '修改订单状态失败';
                return false;
            }
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
    public function getOrderList($condition = array(), $field = '*', $order = 'order_id DESC', $limit = '', $extend = array())
    {
        $list = $this->field($field)->where($condition)->order($order)->limit($limit)->select();
        if (!$list) return array();
        $order_list = array();

        foreach ($list as $orders) {
            if (!empty($extend)) $order_list[$orders['order_id']] = $orders;
        }
        if (empty($order_list)) $order_list = $list;

        // 返回商品信息
        if (in_array('order_goods', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_id' => array('in', array_keys($order_list))));
            //获取订单以评论产品信息
            $order_goods_comment = $this->getOrderGoodsComment(array('order_id' => array('in', array_keys($order_list))));
            foreach ($order_goods_list as $value) {
                //判断订单中的产品是否已经评价 1-已评价  0-未评价
                if (in_array($value['order_id']['goods_id'], $order_goods_comment[$value['order_id']])) {
                    $value['is_comment'] = 1;
                } else {
                    $value['is_comment'] = 0;
                }
                $order_list[$value['order_id']]['extend_order_goods'][] = $value;
            }
        }
        return $order_list;
    }

    /**
     * 获取订单中以评论商品的数组并返回
     */
    public function getOrderGoodsComment($condition = array(), $field = true)
    {
        $result = M('GoodsComment')->field($field)->where($condition)->select();
        $value = array();
        if ($result) {
            foreach ($result as $val) {
                if ($val) {
                    $value[$val['order_id']][$val['goods_id']] = $val['goods_id'];
                }
            }
        }
        return $value;
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
            return M('OrderGoods')->where($condition)->limit($limit)->order($order)->group($group)->getField($fields);
        } else {
            return M('OrderGoods')->field($fields)->where($condition)->limit($limit)->order($order)->group($group)->select();
        }
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
     * 获取订单扩展表列表
     * @param   array $condition 查询条件
     * @param   bool|string $fields 查询字段
     * @param   string $limit 限制条件
     * @return  mixed
     */
    public function getOrderCommonList($condition = array(), $fields = true, $limit = null)
    {
        return M('OrderCommon')->field($fields)->where($condition)->group('order_id')->limit($limit)->select();
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
     * 返回模型错误状态码
     * @access public
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * 修改订单状态
     *
     * @param $id 订单ID
     * @param int $status 订单状态，默认为订单已完成
     * @return array
     */
    public function updateOrder($id, $status = 'success')
    {
        if (empty($id)) {
            return array('status' => false, 'msg' => '请选择您需要修改的订单！');
        }
        $data = array();
        $return = array();
        switch ($status) {
            case 'success':
                $data['order_status'] = self::ORDER_SUCCESS;
                break;
            //发送到达，等待用户确认收货
            case 'arrive':
                $data['order_status'] = self::ORDER_CONFIRM;
                break;
            //付款完成（无需发货），或者确认收货之后，用户待评论
            case 'comment':
                $data['order_status'] = self::ORDER_EVALUATE;
                break;
            //商户已发货
            case 'send':
                $data['order_status'] = self::ORDER_SEND;
                break;
            case 'delete':
                $data['is_delete'] = self::ORDER_DEL;
                break;
            default :
                return false;
        }

        $condition = array();
        $condition['order_id'] = $id;
        $result = $this->where($condition)->save($data);
        if (false === $result) {
            $return['status'] = false;
            $return['msg'] = '订单修改失败';
            return $return;
        }
        $return['status'] = true;
        $return['msg'] = '成功';
        return $return;
    }

} 