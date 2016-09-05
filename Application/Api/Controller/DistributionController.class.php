<?php

namespace Api\Controller;

/**
 * 会员（库存）配送管理
 * Class DistributionController
 * @package Api\Controller
 */
class DistributionController extends ApiController
{
    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->uid = $this->isLogin();
    }

    /**
     * （库存）配送列表
     * @code 42101~42103
     */
    public function lists()
    {
        $condition = array();
        // TODO 查询条件
        $condition['user_id'] = $this->uid;
        $condition['is_delete'] = 0;

        $Model = D('DistributionOrder');

        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }

        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = $Model->where($condition)->count();

        $lists = $Model->getOrderList($condition, true, 'order_id DESC', $limit);

        if (empty($limit)) {
            $this->apiReturn(42101, '暂无配送订单');
        }

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists,
        );
        $this->apiReturn(0, '获取列表成功', $data);
    }

    /**
     * 确认申请配送库存列表
     * @code 42104~42109
     */
    public function apply()
    {
        // 检查购买购物车中的参数
        if (!isset($this->data['cart_ids']) || empty(trim($this->data['card_ids'], '\t\n,'))) {
            $this->apiReturn(42104, '请选择要该买购物车中的商品');
        }
        $cart_ids = trim($this->data['card_ids'], '\t\n,');

        // 获取用户默认地址
        $address_field = 'id,consignee,area_info,address,phone,area_id';
        $address_condition = array();
        $address_condition['uid'] = $this->uid;
        $address_condition['is_default'] = 1;
        $address = M('UserAddress')->field($address_field)->where($address_condition)->find();

        // 获取用户可用资金
        $balance = M('UserAccount')->where(array('uid' => $this->uid))->getField('account_balance');

        // 获取用户购物车详情
        $CartModel = D('DistributionCart');

        // 获取购物车列表
        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['cart_id'] = array('IN', $cart_ids);

        $cart_list = $CartModel->listCart($condition);

        // 获取商品最新的在售信息
        $cart_list = $CartModel->getStorageCartList($cart_list);

        if (empty($cart_list)) {
            $this->apiReturn(42105, '请选择要配送的农作物');
        }

        /* 计算商品总重量开始 */
        $weight = 0;
        foreach ($cart_list as $info) {
            $weight += $info['crop_weight'];
        }

        $shipping_fee = D('Transport')->calcShippingFee($weight, $address['area_id']);

        $data = array();
        $data['address'] = $address;        // 默认收获地址
        $data['balance'] = $balance;        // 账户可用余额
        $data['weight'] = $weight;      // 总重量
        $data['shipping_fee'] = $shipping_fee;      // 运费
        $data['crop_list'] = $cart_list;   // 确认购买商品列表
        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 申请（库存）配送
     * @code 42111~42115
     */
    public function confim()
    {
        // 检查购买购物车中的参数
        if (!isset($this->data['cart_ids']) || empty(trim($this->data['card_ids'], '\t\n,'))) {
            $this->apiReturn(42111, '请选择要该买购物车中的商品');
        }
        $cart_ids = trim($this->data['card_ids'], '\t\n,');

        // 用户配送地址
        if (!isset($this->data['address_id']) || empty(trim($this->data['address_id'], '\t\n,'))) {
            $this->apiReturn(42112, '请选择收货地址');
        }
        $address_id = intval($this->data['address_id']);

        $orderModel = D('DistributionOrder');
        try {
            $orderModel->startTrans();

            $order_id = $orderModel->createOrder($cart_ids, $this->uid, $address_id, $this->from);
            if (!$order_id) {
                throw new \Exception($orderModel->getError(), $orderModel->getCode());
            }

            $res = $orderModel->orderSuccess($order_id, $this->uid);
            if (!$res) {
                throw new \Exception($orderModel->getError(), $orderModel->getCode());
            }

            $orderModel->commit();
            $this->apiReturn(0, '申请配送成功');
        } catch (\Exception $e) {
            $orderModel->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * （库存）配送详情
     * @code 42116~42119
     */
    public function detail()
    {
        if (!isset($this->data['order_id']) || empty($this->data['order_id'])) {
            $this->apiReturn(42116, '请选择要查看的配送订单');
        }

        $condition = array();
        $condition['order_id'] = $this->data['order_id'];
        $condition['user_id'] = $this->uid;
        $condition['is_delete'] = 0;

        $Model = D('DistributionOrder');

        $order_info = $Model->getOrderInfo($condition, array('order_common', 'order_crop'));
        if (empty($order_info)) {
            $this->apiReturn(42117, '配送订单不存在');
        }

        $this->apiReturn(0, '成功', $order_info);

    }

    /**
     * 取消（库存）配送
     * @code 42121~42125
     * 注：只有未配送的订单支持取消
     */
    public function cancel()
    {
        if (!isset($this->data['order_id']) || empty($this->data['order_id'])) {
            $this->apiReturn(42121, '请选择要取消的配送订单');
        }

        $orderModel = D('DistributionOrder');
        try {
            $orderModel->startTrans();

            $res = $orderModel->orderCancel($this->data['order_id'], $this->uid);
            if (!$res) {
                throw new \Exception($orderModel->getError(), $orderModel->getCode());
            }

            $orderModel->commit();
            $this->apiReturn(0, '取消配送订单成功');
        } catch (\Exception $e) {
            $orderModel->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 删除（库存）配送
     * @code 42126~42139
     * 注：只有已完成，或者已取消订单支持删除
     */
    public function del()
    {
        if (!isset($this->data['order_id']) || empty($this->data['order_id'])) {
            $this->apiReturn(42126, '请选择要删除的配送订单');
        }

        $condition = array();
        $condition['order_id'] = $this->data['order_id'];
        $condition['user_id'] = $this->uid;
        $condition['is_delete'] = 0;
        $condition['order_status'] = array('IN', '0,4');
        $res = M('DistributionOrder')->where($condition)->save(array('is_delete' => 1));
        if (!$res) {
            $this->apiReturn(42127, '删除配送订单失败');
        }
        $this->apiReturn(0, '删除配送订单成功');
    }

    /**
     * 确认收货
     * @code 42131~42139
     */
    public function success()
    {
        if (!isset($this->data['order_id']) || empty($this->data['order_id'])) {
            $this->apiReturn(42131, '请选择要确认收货的配送订单');
        }

        $orderModel = D('DistributionOrder');
        try {
            $orderModel->startTrans();

            $res = $orderModel->confirmReceipt($this->data['order_id'], $this->uid);
            if (!$res) {
                throw new \Exception($orderModel->getError(), $orderModel->getCode());
            }

            $orderModel->commit();
            $this->apiReturn(0, '确认收货成功');
        } catch (\Exception $e) {
            $orderModel->rollback();
            $this->apiReturn(42132, $e->getMessage());
        }
    }

    /**
     * （库存）配送购物车列表
     * @code 42141~42149
     */
    public function cartlist()
    {
        $cartModel = D('DistributionCart');

        // 获取购物车列表
        $cart_list = $cartModel->listCart(array('uid' => $this->uid));

        // 获取最新库存信息
        $cart_list = $cartModel->getStorageCartList($cart_list, $this->uid);

        $this->apiReturn(0, '成功', $cart_list);
    }

    /**
     * 添加（库存）配送购物车
     * @code 42151~42159
     */
    public function cartadd()
    {
        // 农作物（种子ID）
        if (!isset($this->data['seed_id']) || empty($this->data['seed_id'])) {
            $this->apiReturn(42151, '请选择要添加的农作物');
        }

        // 购买重量
        if (!isset($this->data['weight']) || (floatval($this->data['weight']) <= 0)) {
            $this->apiReturn(42152, '请选择要配送的重量');
        }

        $weight = floatval($this->data['weight']);

        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['seed_id'] = $this->data['seed_id'];

        $storage_info = D('UserStorage')->getStorageInfo(array('id' => $this->data['seed_id']));
        if (!$storage_info) {
            $this->apiReturn(42154, '库存农作物不存在');
        }

        if ($storage_info['available_weight'] <= 0) {
            $this->apiReturn(42155, '暂无可用库存');
        }

        // 检测是否为重复添加
        $Model = D('DistributionCart');

        $condition = array();
        $condition['goods_id'] = $this->data['cart_id'];
        $condition['uid'] = $this->uid;
        $cart_info = $Model->getCartInfo($condition);
        if ($cart_info) {
            $this->data['cart_id'] = $cart_info['cart_id'];
            $this->cartedit();
        } else {

            if ($storage_info['available_weight'] < $weight) {
                $this->apiReturn(42156, '库存不足');
            }

            // 获取种子详情
            $seed_image = M('Seed')->where(array('seed_id' => $this->data['seed_id']))->getField('seed_img');

            $add_data = array();
            $add_data['user_id'] = $this->uid;
            $add_data['seed_id'] = $storage_info['seed_id'];
            $add_data['crop_name'] = $storage_info['seed_name'];
            $add_data['crop_image'] = $seed_image;
            $add_data['crop_weight'] = $weight;
            $add_data['add_time'] = NOW_TIME;

            $res = $Model->addCart($add_data, $weight);
            if (!$res) {
                $this->apiReturn(-1, '系统错误，请稍候重试');
            }

            $data = array();
            $data['quantity'] = $Model->cart_all_num;
            $data['weight'] = $Model->cart_all_weight;
            $this->apiReturn(0, '添加购物车成功', $data);
        }
    }

    /**
     * 更改（库存）配送购物车
     * @code 42161~42169
     */
    public function cartedit()
    {
        if (!isset($this->data['cart_id']) || empty($this->data['cart_id'])) {
            $this->apiReturn(42161, '请选择要编辑的农作物');
        }

        if (!isset($this->data['weight']) || (floatval($this->data['weight']) <= 0)) {
            $this->apiReturn(42162, '请选择要配送的重量');
        }

        $weight = floatval(abs($this->data['weight']));

        // 修改数量
        $condition = array();
        $condition['cart_id'] = $this->data['cart_id'];
        $condition['user_id'] = $this->uid;

        $Model = M('DistributionCart');
        $cart_info = $Model->getCartInfo($condition);

        if (!$cart_info) {
            $this->apiReturn(42163, '数据不存在');
        }

        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['seed_id'] = $cart_info['seed_id'];
        $storage_info = D('UserStorage')->getStorageInfo($condition);
        if (!$storage_info) {
            // 删除此购物车信息
            $Model->delCart(array('cart_id' => $this->data['cart_id']));
            $data = array();
            $data['quantity'] = $Model->cart_all_num;
            $data['weight'] = $Model->cart_all_weight;
            $this->apiReturn(42164, '不存在此农作物库存', $data);
        }

        if ($storage_info['available_weight'] <= 0) {
            // 删除此购物车信息
            $Model->delCart(array('cart_id' => $this->data['cart_id']));
            $data = array();
            $data['quantity'] = $Model->cart_all_num;
            $data['weight'] = $Model->cart_all_weight;
            $this->apiReturn(42165, '已无库存', $data);
        }

        // 检查库存
        if ($storage_info['available_weight'] < $weight) {
            $Model->editCart(array('crop_weight' => $storage_info['available_weight']), array('cart_id' => $this->data['cart_id'], 'user_id' => $this->uid));

            $data = array();
            $data['quantity'] = $Model->cart_all_num;
            $data['weight'] = $Model->cart_all_weight;
            $this->apiReturn(42166, '库存不足', $data);
        }

        $data = array();
        $data['crop_weight'] = $weight;
        $res = $Model->editCart($data, array('cart_id' => $this->data['cart_id']));
        if (false === $res) {
            $this->apiReturn(-1, '系统错误，请稍候重试');
        }

        $data = array();
        $data['quantity'] = $Model->cart_all_num;
        $data['weight'] = $Model->cart_all_weight;
        $this->apiReturn(0, '修改重量成功', $data);
    }

    /**
     * 删除（库存）配送购物车
     * @code 42171~42179
     * 注：删除多个农作物使用 ','分割 例：1,2,3
     */
    public function cartdel()
    {
        if (!isset($this->data['cart_ids']) || empty(trim($this->data['cart_ids'], ','))) {
            $this->apiReturn(42171, '请选择要删除的农作物');
        }
        $condition = array();
        $condition['cart_id'] = array('IN', trim($this->data['cart_ids'], ','));
        $condition['user_id'] = $this->uid;

        $res = D('DistributionCart')->delCart($condition);
        if (false === $res) {
            $this->apiReturn(42172, '删除失败');
        }
        $this->apiReturn(0, '删除成功');
    }
}