<?php

namespace Api\Controller;

/**
 * 用户商品订单管理
 * Class OrderController
 * @package Api\Controller
 */
class OrderController extends ApiController
{
    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

//        $this->uid = $this->isLogin();
    }

    /**
     * 订单列表
     */
    public function lists()
    {
        $condition = array();
//        $condition['uid'] = $this->uid;
        $condition['is_delete'] = 0;
        /**
         * 订单查询类型
         * all 默认/所有订单
         * cancel 查询所有已取消的订单
         */
        if (isset($this->data['type']) && !empty($this->data['type'])) {
            switch ($this->data['type']) {
                //取消
                case 'state_cancel':
                    $condition['order_status'] = ``;
                    break;
                //待付款
                case 'state_pay':
                    $condition['order_status'] = 0;
                    break;
                //待养殖
                case 'state_shipped':
                    $condition['order_status'] = 2;
                    break;
                //养殖中
                case 'state_receive':
                    $condition['order_status'] = 3;
                    break;
                //待评价
                case 'state_noeval':
                    $condition['order_status'] = 4;
                    break;
                default:
                    break;
            }
        }

        /**
         * 按订单ID查询
         */
        if (isset($this->data['search']) && !empty($this->data['search']) && ($this->data['search'] != '0,0')) {
            if (false === strpos($this->data['search'], ',')) {
                $this->apiReturn(45123, '非法操作');//查询条件不符合
            }
            $times = explode(',', $this->data['search']);
            $map = array();
            $map[] = array('egt', $times[0]);
            $map[] = array('elt', $times[1]);

            $condition['add_time'] = $map;
            unset($where);
        }

        $Model = D('Order');
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }

        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        $count = $Model->where($condition)->count();
        $field = 'order_id,order_sn,order_status,add_time';
        $lists = $Model->getOrderList($condition, $field, 'order_id DESC', $limit, array('order_goods'));

        $data = array(
            'where' => $condition,
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );
        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $this->data['order_id'] = 1;
        if (!isset($this->data['order_id']) || empty($this->data['order_id'])) {
            $this->apiReturn(45121, '请选择要查看的订单');
        }

        $condition = array();
        $condition['order_id'] = $this->data['order_id'];
//        $condition['uid'] = $this->uid;
        $condition['uid'] = 2;
        $condition['is_delete'] = 0;

        $Model = D('Order');

        $field = 'order_id,order_sn,order_status,add_time';
        $order_info = $Model->getOrderInfo($condition, array('order_goods'), $field);
        if (empty($order_info)) {
            $this->apiReturn(45122, '订单不存在');
        }

        $this->apiReturn(0, '成功', $order_info);
    }


    /**
     * 去结算
     */
    public function confim()
    {
        // 检查购买购物车中的参数
        if (!isset($this->data['cart_ids']) || empty($this->data['cart_ids'])) {
            $this->apiReturn(45101, '请选择要该买购物车中的商品');
        }
        $cart_ids = trim($this->data['cart_ids'], '\t\n,');

        // 获取用户默认地址
        $address_field = 'id,consignee,area_info,address,phone';
        $address_condition = array();
        if ($addressid = $this->data['address_id']) // (LY) 2016年1月25日13:20:42
        {
            $address_condition['id'] = $addressid;
        } else {
            $address_condition['uid'] = $this->uid;
            $address_condition['is_default'] = 1;
        }

        $address = M('UserAddress')->field($address_field)->where($address_condition)->find();

        // 获取用户可用资金
        $balance = M('UserAccount')->where(array('uid' => $this->uid))->getField('account_balance');

        // 获取用户购物车详情
        $orderModel = D('Order');

        list($cart_list, $goods_total) = $orderModel->calcBuyList($cart_ids, $this->uid);
        if (empty($cart_list)) {
            $this->apiReturn(45101, '请选择要该买购物车中的商品');
        }

        // TODO 运费处理

        $data = array();
        $data['address'] = $address;        // 默认收获地址
        $data['balance'] = $balance;        // 账户可用余额
        $data['goods_total'] = $goods_total; // 商品总价格
        $data['shipping_fee'] = 0;      // 运费 TODO
        $data['goods_list'] = $cart_list;   // 确认购买商品列表
        $data['cart_ids'] = $this->data['cart_ids'];   // 确认购买商品列表
        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 用户提交订单
     * 前面已经处理完成账户资金的管理：如充值，现货转金币，期货转现货转金币
     */
    public function submit()
    {
        //产品id
        if(!isset($this->data['goods_id']) || empty($this->data['goods_id'])){
            $this->apiReturn(45140,'请选择需要购买的产品');
        }

        //产品id
        if(!isset($this->data['address_id']) || empty($this->data['address_id'])){
            $this->apiReturn(45141,'请选择收货地址');
        }
        // 用户配送地址
        $address_id = isset($this->data['address_id']) && !empty($this->data['address_id']) ? intval($this->data['address_id']) : 0;

        $orderModel = D('Order');
        try {

            $orderModel->startTrans();
            $order_id = $orderModel->createOrder($this->data['goods_id'], $this->uid, $address_id, $this->from);
            if (!$order_id) {
                throw new \Exception($orderModel->getError(), $orderModel->getCode());
            }

            $orderModel->commit();
            $this->apiReturn(0, '下单成功');
        } catch (\Exception $e) {
            $orderModel->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /** todo 文档
     * 验证支付密码是否正确
     *
     * @param $pwd 密码
     * @return array 返回验证结构
     */
    private function check_pay_pass($pwd)
    {

        $model = D('User');
        $user_info = $model->field('pay_pass,pay_encrypt')->where(array('uid' => $this->uid))->find();
        if (false === $user_info) {
            return array('status' => false, 'msg' => '数据查询失败，请稍后！', 'code' => 45120);
        }
        if (empty($user_info['pay_pass'])) {
            return array('status' => false, 'msg' => '尊敬的用户，您还未设置支付密码，请设置！', 'code' => 45121);
        }
        //TODO 加密验证
        if ($model->hashPassword($pwd, $user_info['pay_encrypt']) == $user_info['pay_pass']) {
            $value = array('status' => true, 'code' => 0);

        } else {
            $value = array('status' => false, 'msg' => '请输入正确的支付密码！', 'code' => 45122);
        }
        return $value;

    }

    /**
     * 订单信息统计
     */
    public function count()
    {
        $condition = array();
        $this->uid = 1;
        $condition['buyer_id'] = $this->uid;
        $condition['is_delete'] = 0;
        //统计代付款、代收货、待自提、退款/退货  订单状态：0-已取消未付款，1-默认未付款，2-已付款，3-已发货，4-已完成
        $Model = D('Order');
        //统计待付款订单数量
        $condition['order_status'] = null;
        $condition['order_status'] = 1;
        $obligation = $Model->where($condition)->count();

        //统计待发货
        $condition['order_status'] = null;
        $condition['order_status'] = 2;
        $shipped = $Model->where($condition)->count();
        //统计待收货订单数量
        $condition['order_status'] = null;
        $condition['order_status'] = 3;
        $received = $Model->where($condition)->count();

        //待评论（不需要配送且支付完成，需要配送，且配送完成的）
        //是否需要配送 0f 1s is_shipping
        //配送且配送完成 且未评论
        unset($condition);
        $condition['t1.buyer_id'] = 1;
        $condition['t1.is_delete'] = 0;
        $condition['t1.is_shipping'] = 1;
        $condition['t1.order_status'] = 4;
        $condition['t2.evalseller_state'] = 0;
        $num1 = $Model->alias('t1')->field('t1.order_id')->join('__ORDER_COMMON__ t2 ON t1.order_id = t2.order_id', 'LEFT')->where($condition)->group('t1.order_id')->select();
        //不需要配配送且支付完成 且未评论
        unset($condition);
        $condition['t1.buyer_id'] = 1;
        $condition['t1.is_delete'] = 0;
        $condition['t1.order_status'] = 2;
        $condition['t1.is_shipping'] = 0;
        $condition['t2.evalseller_state'] = 0;
        $num2 = $Model->alias('t1')->field('t1.order_id')->join('__ORDER_COMMON__ t2 ON t1.order_id = t2.order_id', 'LEFT')->where($condition)->group('t1.order_id')->select();
        $comment = count($num1) + count($num2);
//        unset($condition);
//        $map[] = array('t1.is_shipping' => 1, 'order_status' => 4);
//        $map[] = array('t1.is_shipping' => 0, 'order_status' => 2);
//        $map['_logic'] = 'or';
//        $condition['_complex'] = $map;
//        $condition['t2.evalseller_state'] = 0;
//        $result = $Model->alias('t1')->field('t1.order_id')->join('__ORDER_COMMON__ t2 ON t1.order_id = t2.order_id', 'LEFT')->where($condition)->group('t1.order_id')->select();
//        var_dump($result);die;
        $data = array(
            'comment' => $comment,//评论
            'received' => $received,//待收货
            'obligation' => $obligation,//待付款
            'shipped' => $shipped,//待发货
        );

        $this->apiReturn(0, '成功', $data);
    }

    //TODO 订单物流管理


    /**
     * 标记订单状态
     *  state_receipt 确认收货
     */
    public function status()
    {
        $data = array();
        //验证这个用户是否购买了当前的订单
        if (!isset($this->data['id']) && empty($this->data['id'])) {
            $this->apiReturn(45124, '请选择您要修改的订单！');
        }

        //验证这个用户是否购买了当前的订单
        if (!isset($this->data['status']) && empty($this->data['status'])) {
            $this->apiReturn(45125, '请确认您要修改的状态！');
        }

        switch ($this->data['status']) {
            case 'state_receipt':
                $data['order_status'] = 'comment';
                break;
            case 'delete':
                $data['order_status'] = 'delete';
                break;
            case 'cancel':
                $data['order_status'] = 'cancel';
                break;
            default :
                $this->apiReturn(45125, '请确认您要修改的状态！');
                break;

        }

        $order_id = intval(trim($this->data['id']));
        //检测当前用户是否是当前用户所有的订单
        $model = D('Order');
        $condition = array();
        $condition['buyer_id'] = $this->uid;
        $condition['order_id'] = $order_id;
        $info = $model->getOrderInfo($condition, '', 'order_sn');
        if (empty($info)) {
            $this->apiReturn(45126, '无效订单！');
        }
        //修改订单的状态(用户收货完成)
        $result = $model->updateOrder($order_id, $data['order_status']);
        if (!$result['status']) {
            $this->apiReturn(45127, '操作失败，请重新修改！');
        }
        $this->apiReturn(0, '操作成功！');
    }

}