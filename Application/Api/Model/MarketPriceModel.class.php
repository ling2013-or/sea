<?php

namespace Api\Model;

use Think\Model;

/**
 * 农作物市场价格模型
 * Class MarketPriceModel
 * @package Api\Model
 */
class MarketPriceModel extends Model
{

    /**
     * 获取农作物当天的市场价格
     * @param int $seed_id 种子（作物）ID
     * @param int $user_id 用户ID
     * @return float
     */
    public function getDayCropPrice($seed_id, $user_id)
    {
        $condition = array();
        $condition['seed_id'] = $seed_id;

        // 市场指导价格（必须存在）
        $price = $this->where($condition)->order('id DESC')->getField('price');

        // 获取当前市场上正在销售且库存存在的农作物的最小价格
        $condition = array();
        $condition['goods_stock'] = array('GT', 0);
        $condition['seed_id'] = $seed_id;
        $condition['store_id'] = array('NEQ', $user_id);    // TODO 排除自己出售的农作物价格
        $condition['goods_status'] = 1;
        $goods_price = M('Goods')->where($condition)->min('goods_price');

        // 价格按照最低价格处理
        return min($price, $goods_price);
    }
}