<?php

namespace Api\Model;

use Think\Model;

/**
 * 运费模板模型
 * Class TransportModel
 * @package Api\Model
 */
class TransportModel extends Model
{

    /**
     * 取得扩展信息列表
     * @param array $condition 查询条件
     * @param string $order 排序条件
     * @return mixed
     */
    public function getExtendList($condition = array(), $order = 'is_default')
    {
        return M('TransportCommon')->where($condition)->order($order)->select();
    }

    /**
     * 计算某地区某运费模板ID下的商品总运费，
     * 如果运费模板不存在则按免运费处理
     * @param int $transport_id 运费模板ID
     * @param float $weight 购买重量
     * @param int $area_id 区域ID（地区）
     * @return bool|float
     */
    public function calcTransport($transport_id, $weight, $area_id)
    {
        if (empty($transport_id) || empty($weight) || empty($area_id)) {
            return 0;
        }
        $condition = array();
        $condition['transport_id'] = $transport_id;
        $extend_list = $this->getExtendList($condition);

        return $this->calcUnit($area_id, $weight, $extend_list);
    }

    /**
     * TODO 此方法暂时只考虑一种配送方式，不存在运费模板
     * @param float $weight 购买重量
     * @param int $area_id 区域ID（地区）
     * @return float
     */
    public function calcShippingFee($weight, $area_id)
    {
        if (empty($weight) || empty($area_id)) {
            return 0;
        }

        $extend_list = $this->getExtendList();

        return $this->calcUnit($area_id, $weight, $extend_list);
    }

    /**
     * 计算某个运单的运费
     * @param int $area_id 区域ID(配送地区)
     * @param float $weight 货物重量
     * @param array $extend 运费模板内容
     * @return float
     */
    private function calcUnit($area_id, $weight, $extend)
    {
        if (empty($extend) || !is_array($extend)) return 0;

        foreach ($extend as $val) {
            if (strpos($val['area_id'], ',' . $area_id . ',') !== false) {
                if ($weight <= $val['first_weight']) {
                    // 首重范围内
                    $calc_total = $val['first_price'];
                } else {
                    // 超出首重范围，需要按照增量计算续重
                    $calc_total = sprintf('%.2f', $val['first_price'] + ceil(($weight - $val['first_weight']) / $val['next_weight']) * $val['next_price']);
                }
                break;
            }

            if ($val['is_default'] == 1) {
                if ($weight <= $val['first_weight']) {
                    // 首重范围内
                    $calc_default_total = $val['first_price'];
                } else {
                    // 超出首重范围，需要按照增量计算续重
                    $calc_default_total = sprintf('%.2f', $val['first_price'] + ceil(($weight - $val['first_weight']) / $val['next_weight']) * $val['next_price']);
                }
            }
        }

        //如果运费模板中没有指定该地区，取默认运费
        if (!isset($calc_total) && isset($calc_default_total)) {
            $calc_total = $calc_default_total;
        }

        return $calc_total;
    }
}