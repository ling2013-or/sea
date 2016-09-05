<?php

namespace Api\Model;

use Think\Model;

/**
 * 申请配送的购物车模型
 * Class DistributionCart
 * @package Api\Model
 */
class DistributionCartModel extends Model
{

    /**
     * 申请配送的总重量
     * @var int
     */
    public $cart_all_weight = 0;

    /**
     * 申请配送的农作物种类
     * @var int
     */
    public $cart_all_num = 0;

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
        // 验证购物车商品是否已经存在
        $condition['stock_id'] = $data['stock_id'];
        $condition['user_id'] = $data['user_id'];
        $check_cart = $this->checkCart($condition);

        if (!empty($check_cart)) return true;

        $cart = array();
        $cart['user_id'] = $data['user_id'];
        $cart['stock_id'] = $data['stock_id'];
        $cart['crop_name'] = $data['crop_name'];
        $cart['crop_image'] = $data['crop_image'];
        $cart['crop_weight'] = $weight;
        $cart['add_time'] = NOW_TIME;
        $res = $this->add($cart);
        if ($res) {
            // 更改购物车商品数和总金额
            $this->getCartNum(array('user_id' => $data['user_id']));
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
        $this->cart_all_num = count($cart_list);
        $cart_all_weight = 0;
        if (!empty($cart_list)) {
            foreach ($cart_list as $val) {
                $cart_all_weight += $val['crop_weight'];
            }
        }

        $this->cart_all_weight = format_weight($cart_all_weight);
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
            $this->getCartNum(array('user_id' => $condition['user_id']));
        }
        return $res;
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
        return $this->cart_all_num;
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
            $this->getCartNum(array('user_id' => $condition['user_id']));
        }
        return $res;
    }

    /**
     * 检测数据有效性
     * @param   array $cart_list 购物车数据
     * @param   int $user_id 用户ID
     * @return  array
     */
    public function getStorageCartList($cart_list, $user_id)
    {
        if (!is_array($cart_list) || empty($cart_list)) {
            return $cart_list;
        }

        // 验证库存农作物有效性
        $storage_id_array = array();
        foreach ($cart_list as $cart_info) {
            $storage_id_array[] = $cart_info['seed_id'];
        }

        $condition = array();
        $condition['user_id'] = $user_id;
        $condition['seed_id'] = array('IN', $storage_id_array);

        $storage_list = M('UserStorage')->where($condition)->select();
        $storage_array = array();
        foreach ($storage_list as $storage) {
            $storage_array[$storage['seed_id']] = $storage;
        }

        foreach ((array)$cart_list as $key => $cart_info) {
            $cart_list[$key]['storage_state'] = true;
            if (in_array($cart_info['seed_id'], array_keys($storage_array))) {
                $storage_info = $storage_array[$cart_info['seed_id']];

                if ($cart_info['crop_weight'] > $storage_info['available_weight']) {
                    $cart_list[$key]['storage_state'] = false;
                }
            } else {
                //库存不足
                $cart_list[$key]['storage_state'] = false;
            }
        }
        return $cart_list;
    }
}