<?php

namespace Api\Controller;

/**
 * 农作物购物车管理
 * 注：返回码说明：43101开始
 * Class GoodscartController
 * @package Api\Controller
 */
class GoodscartController extends ApiController
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
     * 购物车列表
     */
    public function lists()
    {
        $cartModel = D('GoodsCart');
        $condition = array();
        if(isset($this->data['ids']) && !empty($this->data['ids'])){
            $cart_ids = explode(',',trim($this->data['ids']));
            $condition['id'] = array('IN',$cart_ids);
        }
        $condition['uid'] = $this->uid;
        // 获取购物车列表
        $cart_list = $cartModel->listCart($condition);

        // 获取商品最新在售信息
        $cart_list = $cartModel->getOnlineCartList($cart_list);

        // 购物车商品以 会员/平台 ID分组显示，并计算商品小计，其他通过js计算
        $store_cart_list = array();
        foreach ($cart_list as $cart) {
            $cart['goods_total'] = format_money($cart['goods_price'] * $cart['goods_weight']);
            $store_cart_list[$cart['store_id']][] = $cart;
        }
        $this->apiReturn(0, '成功', $store_cart_list);
    }

    /**
     * 添加农作物到购物车
     */
    public function add()
    {
        /**
         * 农产品ID
         * 校验农产品是否合法（下架）
         */
        if (!isset($this->data['goods_id']) || empty($this->data['goods_id'])) {
            $this->apiReturn(43101, '请选择要添加的商品');
        }

        // 购买重量
        if (!isset($this->data['weight']) || (floatval($this->data['weight']) <= 0)) {
            $this->apiReturn(43102, '请选择要购买的数量');
        }

        $weight = floatval($this->data['weight']);

        $goods_info = D('Goods')->getGoodsOnlineInfo(array('id' => $this->data['goods_id']));
        if (!$goods_info) {
            $this->apiReturn(43103, '商品不存在');
        }

        if ($goods_info['store_id'] == $this->uid) {
            $this->apiReturn(43104, '不能购买自己的商品');
        }

        if ($goods_info['goods_stock'] <= 0) {
            $this->apiReturn(43105, '商品已卖完');
        }

        // 检测是否为重复添加
        $Model = D('GoodsCart');

        $condition = array();
        $condition['goods_id'] = $this->data['goods_id'];
        $condition['uid'] = $this->uid;
        $cart_info = $Model->getCartInfo($condition);
        if ($cart_info) {
            $this->data['cart_id'] = $cart_info['id'];
            $this->weight();
        } else {

            if ($goods_info['goods_stock'] < $weight) {
                $this->apiReturn(43106, '库存不足');
            }

            $add_data = array();
            $add_data['uid'] = $this->uid;
            $add_data['farm_id'] = $goods_info['farm_id'];
            $add_data['store_id'] = $goods_info['store_id'];
            $add_data['goods_id'] = $goods_info['id'];
            $add_data['seed_id'] = $goods_info['seed_id'];
            $add_data['goods_price'] = $goods_info['goods_price'];
            $add_data['goods_name'] = $goods_info['goods_name'];

            $res = $Model->addCart($add_data, $weight);
            if (!$res) {
                $this->apiReturn(-1, '系统错误，请稍候重试');
            }

            $data = array();
            $data['quantity'] = $Model->cart_goods_num;
            $data['amount'] = $Model->cart_all_price;
            $data['cart_id'] = $res;
            $this->apiReturn(0, '添加购物车成功', $data);
        }
    }

    /**
     * 更新指定农作物重量
     */
    public function weight()
    {
        if (!isset($this->data['cart_id']) || empty($this->data['cart_id'])) {
            $this->apiReturn(43111, '请选择要修改的数据');
        }

        if (!isset($this->data['weight']) || (floatval($this->data['weight']) <= 0)) {
            $this->apiReturn(43112, '请选择要购买的数量');
        }

        $weight = floatval(abs($this->data['weight']));

        // 修改数量
        $condition = array();
        $condition['id'] = $this->data['cart_id'];
        $condition['uid'] = $this->uid;

        $Model = D('GoodsCart');
        $cart_info = $Model->getCartInfo($condition);
        if (!$cart_info) {
            $this->apiReturn(43113, '数据不存在');
        }

        $goods_info = D('Goods')->getGoodsOnlineInfo(array('id' => $cart_info['goods_id']));
        if (!$goods_info) {
            // 删除此购物车信息
            $Model->delCart(array('id' => $this->data['cart_id'], 'uid' => $this->uid));
            $data = array();
            $data['quantity'] = $Model->cart_goods_num;
            $data['amount'] = $Model->cart_all_price;
            $this->apiReturn(43114, '商品已下架', $data);
        }

        if ($goods_info['goods_stock'] <= 0) {
            // 删除此购物车信息
            $Model->delCart(array('id' => $this->data['cart_id'], 'uid' => $this->uid));
            $data = array();
            $data['quantity'] = $Model->cart_goods_num;
            $data['amount'] = $Model->cart_all_price;
            $this->apiReturn(43114, '商品已卖完', $data);
        }

        // 检查库存
        if ($goods_info['goods_stock'] < $weight) {
            $Model->editCart(array('goods_weight' => $goods_info['goods_stock']), array('id' => $this->data['cart_id'], 'uid' => $this->uid));

            $data = array();
            $data['quantity'] = $Model->cart_goods_num;
            $data['amount'] = $Model->cart_all_price;
            $data['goods_weight'] = $goods_info['goods_stock'];
            $data['goods_price'] = $cart_info['goods_price'];
            $this->apiReturn(43115, '库存不足', $data);
        }

        $data = array();
        $data['goods_weight'] = $weight;
        $data['goods_price'] = $cart_info['goods_price'];

        $res = $Model->editCart($data, $condition);
        if (false === $res) {
            $this->apiReturn(-1, '系统错误，请稍候重试');
        }

        $data = array();
        $data['quantity'] = $Model->cart_goods_num;
        $data['amount'] = $Model->cart_all_price;
        $data['goods_weight'] = $weight;
        $data['goods_price'] = $cart_info['goods_price'];
        $data['cart_id'] = $this->data['cart_id'];
        $this->apiReturn(0, '修改数量成功', $data);
    }

    /**
     * 删除指定农作物
     */
    public function del()
    {
        if (!isset($this->data['cart_ids']) || empty($this->data['cart_ids'])) {
            $this->apiReturn(43121, '请选择要删除的数据');
        }
        $Model = D('GoodsCart');
        // TODO 不做当前用户不存在的购物车处理， 如果删除不存在的数据也返回成功
        $res = $Model->delCart(array('id' => array('IN', trim($this->data['cart_ids'], ',')), 'uid' => $this->uid));
        if (false === $res) {
            $this->apiReturn(-1, '系统错误，请稍候重试');
        }
        $data = array();
        $data['quantity'] = $Model->cart_goods_num;
        $data['amount'] = $Model->cart_all_price;

        $this->apiReturn(0, '删除成功', $data);
    }
}