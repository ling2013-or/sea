<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/2
 * Time: 14:24
 */

namespace Home\Model;

use Think\Model;

class GoodsModel extends Model
{
    /**
     * 获取产品详情，以及分区ID
     * @param $id
     * @return mixed
     */
    public function goodsInfo($id, $num = 1)
    {
        $map = array();
        $map['id'] = $id;
        $map['status'] = 1;
        $map['stock'] = array('gt', 0);
        $info = $this->field('*')->where($map)->find();
        if(!$info){
            $this->error = '产品库存不足，请重新选择';
            return false;
        }
        $info['zone_id'] = '';
        if ($info['goods_type'] == 0) {
            $info['zone_id'] = $this->zone($id, $num);
        }
        $info['extend'] = '';
        if ($info['goods_type'] == 1) {
            $info['extend'] = $this->extend_goods($info['gids'], $num);
            if(!$info['extend']){
                return false;
            }
        }
        return $info;
    }

    /**
     *
     * 获取产品对应的分区ID
     * @param $id
     * @return mixed
     */
    public function zone($id, $num = 0)
    {
        $map = array();
        $map['goods_id'] = $id;
        $map['status'] = 0;
        $map['real_stock'] = array('gt', $num);
        $result = M('GoodsZone')->field('*')->where($map)->order('star ASC')->find();
        if(!$result){
            $this->error = '产品库存不足，请下次再来';
            return false;
        }
        return $result['id'];
    }

    protected function extend_goods($ids, $num)
    {
        $map = array();
        $map['id'] = array('in', explode(',', $ids));
        $map['goods_type'] = 0;
        $field = 'id as goods_id,price as goods_price,picture as goods_cover,goods_type';
        $list = $this->field($field)->where($map)->select();
        if(!$list){
            $this->error = '套餐下产品不存在，请重新选择';
            return false;
        }
        foreach ($list as $key => $val) {
            $zone = $this->zone($val['goods_id'], $num);
            if(!$zone){
                return false;
            }
            $list[$key]['zone_id'] = $this->zone($val['goods_id'], $num);
            $list[$key]['total'] = intval(abs($num)) * $val['goods_price'];
            $list[$key]['goods_num'] = intval(abs($num));
        }
        return $list;
    }
} 