<?php

namespace Common\Model;

use Think\Model;

/**
 * 农产品购物车管理模型
 * Class GoodsCartModel
 * @package Common\Model
 */
class GoodsCartModel extends Model
{

    /**
     * 购物车商品总金额
     * @var float
     */
    public $cart_all_price = 0;

    /**
     * 购物车商品总数
     * @var int
     */
    public $cart_goods_num = 0;

    /**
     * 检测购物车内的商品是否存在
     * @param  array $condition 查询条件
     * @return mixed
     */
    public function checkCart($condition = array())
    {
        return $this->where($condition)->find();
    }

    /**
     * 取得单条购物车信息
     * @param   array $condition 查询条件
     * @param   bool|string $field 查询字段
     * @return   mixed
     */
    public function getCartInfo($condition = array(), $field = true)
    {
        return $this->field($field)->where($condition)->find();
    }

    /**
     * 将商品添加到购物车
     * @param   array $data 商品数据信息
     * @param   float $weight 购物重量
     * @return  mixed
     */
    public function addCart($data = array(), $weight = null)
    {
        $res = $this->_addCartDb($data, $weight);
        // 更改购物车商品数和总金额
        $this->getCartNum(array('uid' => $data['uid']));
        return $res;
    }

    /**
     * 添加数据库购物车
     * @access private
     * @param array $goods_info 商品数据信息
     * @param float $weight 购物数量
     * @return bool|mixed
     */
    private function _addCartDb($goods_info, $weight)
    {
        // 验证购物车商品是否已经存在
        $condition['goods_id'] = $goods_info['goods_id'];
        $condition['uid'] = $goods_info['uid'];

        $check_cart = $this->checkCart($condition);
        if (!empty($check_cart)) return true;

        $data = array();
        $data['uid'] = $goods_info['uid'];
        $data['farm_id'] = $goods_info['farm_id'];
        $data['goods_id'] = $goods_info['goods_id'];
        $data['seed_id'] = $goods_info['seed_id'];
        $data['goods_name'] = $goods_info['goods_name'];
        $data['goods_price'] = $goods_info['goods_price'];
        $data['goods_weight'] = $weight;
        $data['add_time'] = NOW_TIME;
        return $this->add($data);
    }

    /**
     * 计算购物车商品数和总金额
     * @param   array $condition 计算条件
     * @return  int
     */
    public function getCartNum($condition = array())
    {
        // 获取购物车列表
        $this->listCart($condition);
        return $this->cart_goods_num;
    }

    /**
     * 更新购物车
     * @param  array $data 商品信息
     * @param   array $condition 修改约束条件
     * @return  bool|int
     */
    public function editCart($data, $condition)
    {
        $res = $this->where($condition)->save($data);
        if (false !== $res) {
            $this->getCartNum(array('uid' => $condition['uid']));
        }
        return $res;
    }

    /**
     * 购物车列表(不存在分页)
     * @param   array $condition 查询条件
     * @param   bool|string $field 查询字段
     * @return array
     */
    public function listCart($condition = array(), $field = true)
    {
        $cart_list = $this->field($field)->where($condition)->select();
        $cart_list = is_array($cart_list) ? $cart_list : array();

        // 统计购物车商品数和总金额
        $this->cart_goods_num = count($cart_list);
        $cart_all_price = 0;
        if (!empty($cart_list)) {
            foreach ($cart_list as $val) {
                $cart_all_price += $val['goods_price'] * $val['goods_weight'];
            }
        }

        $this->cart_all_price = format_money($cart_all_price);
        return $cart_list;
    }

    /**
     * 删除购物车商品
     * @param   array $condition 删除条件
     * @return  bool|int
     */
    public function delCart($condition)
    {
        $res = $this->where($condition)->delete();
        if (false !== $res) {
            $this->getCartNum(array('uid' => $condition['uid']));
        }
        return $res;
    }

    /**
     * 清空购物车
     * @param   array $condition 清空条件
     * @return  bool|int
     */
    public function clearCart($condition)
    {
        return $this->where($condition)->delete();
    }

    /**
     * 获取商品最新在售信息
     * @param array $cart_list 购物车列表
     * @return array
     */
    public function getOnlineCartList($cart_list)
    {
        if (!is_array($cart_list) || empty($cart_list)) {
            return $cart_list;
        }

        // 验证商品有效性
        $goods_id_array = array();
        foreach ($cart_list as $cart_info) {
            $goods_id_array[] = $cart_info['goods_id'];
        }

        $GoodsModel = D('Goods');
        $goods_online_list = $GoodsModel->getGoodsOnlineList(array('id' => array('IN', $goods_id_array)));
        $goods_online_array = array();
        foreach ($goods_online_list as $goods) {
            $goods_online_array[$goods['id']] = $goods;
        }

        foreach ((array)$cart_list as $key => $cart_info) {
            $cart_list[$key]['state'] = true;
            $cart_list[$key]['storage_state'] = true;
            if (in_array($cart_info['goods_id'], array_keys($goods_online_array))) {
                $goods_online_info = $goods_online_array[$cart_info['goods_id']];
                $cart_list[$key]['farm_id'] = $goods_online_info['farm_id'];
                $cart_list[$key]['goods_name'] = $goods_online_info['goods_name'];
                $cart_list[$key]['goods_image'] = $goods_online_info['goods_image'];
                $cart_list[$key]['goods_price'] = $goods_online_info['goods_price'];
                $cart_list[$key]['transport_id'] = $goods_online_info['transport_id'];
                $cart_list[$key]['goods_stock'] = $goods_online_info['goods_stock'];
                if ($cart_info['goods_weight'] > $goods_online_info['goods_stock']) {
                    $cart_list[$key]['storage_state'] = false;
                }
            } else {
                //如果商品下架
                $cart_list[$key]['state'] = false;
                $cart_list[$key]['storage_state'] = false;
            }
        }
        return $cart_list;

    }

    /**
     * 从购物车中取得有效的商品
     * @param  array $cart_list 购物车列表
     * @return array
     */
    public function getGoodsList($cart_list)
    {
        if (empty($cart_list) || !is_array($cart_list)) return $cart_list;
        $goods_list = array();
        $i = 0;
        foreach ($cart_list as $key => $cart) {
            if (!$cart['state'] || !$cart['storage_state']) continue;

            $goods_list[$i]['goods_stock'] = $cart['goods_stock'];
            $goods_list[$i]['farm_id'] = $cart['farm_id'];
            $goods_list[$i]['goods_id'] = $cart['goods_id'];
            $goods_list[$i]['store_id'] = $cart['store_id'];
            $goods_list[$i]['seed_id'] = $cart['seed_id'];
            $goods_list[$i]['goods_name'] = $cart['goods_name'];
            $goods_list[$i]['goods_price'] = $cart['goods_price'];
            $goods_list[$i]['goods_image'] = $cart['goods_image'];
            $goods_list[$i]['transport_id'] = $cart['transport_id'];
            $i++;
        }
        return $goods_list;
    }

    /**
     * 商品金额计算（分别对每个商品小计，每个农场小计，金额汇总）
     * @param array $cart_list 以店铺D分组的购物车商品信息
     * @return array
     */
    public function calcCartList($cart_list)
    {
        if (empty($cart_list) || !is_array($cart_list)) return array($cart_list, 0);
        // 商品总额
        $goods_total = 0;

        foreach ($cart_list as &$cart) {
            $cart['goods_total'] = format_money($cart['goods_price'] * $cart['goods_weight']);
            $goods_total += $cart['goods_total'];
        }
        return array($cart_list, $goods_total);
    }


}