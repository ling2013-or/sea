<?php

namespace Common\Model;

use Think\Model;

/**
 * 商品管理模型
 * Class GoodsModel
 * @package Common\Model
 */
class GoodsModel extends Model
{
    /**
     * 商品已删除
     */
    const GOODS_IS_DEL = -1;

    /**
     * 商品未上架
     */
    const GOODS_NOT_SELL = 0;

    /**
     * 商品销售中
     */
    const GOODS_IS_SELL = 1;

    /**
     * 获取单条销售中商品SKU信息
     * @param   array $condition 查询条件
     * @param   bool|string $field 查询字段
     * @return bool|array
     */
    public function getGoodsOnlineInfo($condition, $field = true)
    {
        $condition['goods_status'] = self::GOODS_IS_SELL;
        return $this->field($field)->where($condition)->find();
    }

    /**
     * 获取商品列表
     * @param   array $condition 查询条件
     * @param   bool|string $field 查询字段
     * @param   string $group 分组
     * @param   string $order 排序
     * @param   string $limit 限制条件（用户分页）
     * @return  array
     */
    public function getGoodsOnlineList($condition, $field = true, $group = '', $order = '', $limit = '')
    {
        $condition['goods_status'] = self::GOODS_IS_SELL;
        return $this->getGoodsList($condition, $field, $group, $order, $limit);//刘健健 1.19 16:46修改
    }

    /**
     * 获取单条商品的信息
     * @param   array $condition 查询条件
     * @param   bool|string $field 查询字段
     * @return bool|array
     */
    public function getGoodsInfo($condition, $field = true)
    {
        return $this->field($field)->where($condition)->find();
    }

    /**
     * 获取单条商品信息详情
     * @param   int $goods_id 商品ID
     * @param   bool|string $field 查询字段
     * @return  mixed
     */
    public function getGoodsDetail($goods_id, $field = true)
    {
        if ($goods_id <= 0) {
            return false;
        }

    }

    /**
     * 获取商品列表
     * @param   array $condition 查询条件
     * @param   bool|string $field 查询字段
     * @param   string $group 分组
     * @param   string $order 排序
     * @param   string $limit 限制条件（用户分页）
     * @return  array
     */
    public function getGoodsList($condition, $field = true, $group = '', $order = '', $limit = '')
    {
        return $this->field($field)->where($condition)->group($group)->order($order)->limit($limit)->select();
    }

    /**
     * 获取商品列表总数
     * @param   array $condition 查询条件
     * @param   string $group 分组
     * @return  int
     */
    public function getGoodsTotal($condition, $group = '')
    {
        return $this->where($condition)->group($group)->count();
    }

    /**
     * 添加商品
     */
    public function addGoods($uid, $seed_id, $goods_name = '')
    {
        $data['plan_id'] = 1;
        $data['store_id'] = 1;
        $data['seed_id'] = 1;
        $data['goods_name'] = 1;
        $data['goods_weight'] = 1;
        $data['goods_price'] = 1;
        $data['goods_description'] = 1;
        $data['goods_body'] = 1;
        $data['goods_image'] = 1;
        $data['goods_image_more'] = 1;
        $data['goods_click'] = 1;
        $data['goods_stock'] = 1;
        $data['goods_commend'] = 1;
        $data['goods_add_time'] = 1;
        $data['goods_status'] = 1;
        $data['farm_id'] = 1;
        $data['goods_star'] = 1;
        $data['comment_num'] = 1;
        $data['sale_num'] = 1;
        $data['transport_id'] = 1;
        $data['service'] = 1;
    }
}