<?php

namespace Api\Model;

use Think\Model;

/**
 * 提取收益
 * @package Api\Model
 */
class SellOrderModel extends Model
{
    /**
     * 订单详情
     *
     * 说明:
     *      如果用户已经收益则直接返回订单信息 order
     *      如果用户未收益则返回收益对照表list 当前所在期 state 和 订单信息 order
     *      单位收益(千克/平米)的加载顺序 方案预期收益 -> 方案统一设置的收益 -> 订单设置的收益
     *      只要设置收益即使还没到存储期用户也可以看到收益 NOTICE!
     *
     * @param int $user_id 用户ID
     * @param int $order_id 订单ID
     * @return mixed
     */
    public function getOrderDetails($order_id, $user_id)
    {
        //获取指定用户 指定ID 的订单信息
        $m = M('SellOrder');
        $map['orders.status']   = array('IN',array(1,2)); //1未收益 2已收益
        $map['orders.user_id']  = $user_id;
        $map['orders.order_id'] = $order_id;
        $res = $m->alias('orders')
            ->field('orders.*,psell.plan_start,psell.plan_name,psell.plan_price,
            seed.period_seeding,seed.period_nursery,seed.period_grow,seed.period_maturity,seed.period_reap,seed.seed_img,seed.seed_id,
            pstorage.storage_name,pstorage.storage_time,pdiscount.seeding,pdiscount.nursery,pdiscount.grow,pdiscount.maturity,pdiscount.reap,
            pdiscount.storage,pdiscount.over,psell.income_real,seed.seed_name')
            ->join('__SEED__ seed ON orders.seed_id = seed.seed_id','LEFT')
            ->join('__PLAN_SELL__ psell ON orders.plan_id = psell.plan_id','LEFT')
            ->join('__PLAN_DISCOUNT__ pdiscount ON orders.discount_id = pdiscount.discount_id','LEFT')
            ->join('__PLAN_STORAGE__ pstorage ON orders.storage_id = pstorage.storage_id','LEFT')
            ->where($map)
            ->find();

        if (!$res) {
            return false;
        }

        //过滤要显示的订单信息
        $info  = array();
        $order = array();
        $order['order_id']     = $res['order_id'];
        $order['order_sn']     = $res['order_sn'];
        $order['plan_id']      = $res['plan_id'];
        $order['plan_name']    = $res['plan_name'];
        $order['seed_id']      = $res['seed_id'];
        $order['seed_name']    = $res['seed_name'];
        $order['seed_img']     = $res['seed_img'];
        $order['storage_id']   = $res['storage_id'];
        $order['storage_name'] = $res['storage_name'];
        $order['storage_price'] = $res['storage_price'];
        $order['order_area']    = $res['order_area'];
        $order['plan_income']   = $res['plan_income'];
        $order['order_income']  = $res['order_income'];
        $order['plan_price']  = $res['plan_price'];
        $order['order_price'] = $res['order_price'];
        $order['pay_total']   = $res['pay_total'];
        $order['order_income_final']  = $res['order_income_final'];
        $order['status']     = $res['status'];
        $order['add_time']   = $res['add_time'];
        $order['update_time']   = $res['update_time'];
        $order['plan_start'] = $res['plan_start'];
        $order['order_income_period'] = $res['order_income_period'];
        $info['order'] = $order;

        //计算当前所属期
        $today = NOW_TIME;
        $list  = array();

        //未开始
        if ($today < $res['plan_start']) {
            $info['state']    = null;
        }

        //播种期
        $tmp = array();
        $tmp['name']     = '播种期';
        $tmp['discount'] = $res['seeding'] . ' %';
        $tmp['income']   = round($res['order_income'] * $res['seeding'] / 100, 2);
        $tmp['day']      = $res['period_seeding'];
        $list['seeding'] = $tmp;
        $seeding = $res['plan_start'] + $res['period_seeding']*86400;
        if ($res['plan_start'] <= $today && $today < $seeding) {
            $info['state'] = 'seeding';
        }

        //育苗期
        $tmp = array();
        $tmp['name']     = '育苗期';
        $tmp['discount'] = $res['nursery'] . ' %';
        $tmp['income']   = round($res['order_income'] * $res['nursery'] / 100, 2);
        $tmp['day']      = $res['period_nursery'];
        $list['nursery'] = $tmp;
        $nursery = $seeding + $res['period_nursery']*86400;
        if ($seeding <= $today && $today < $nursery) {
            $info['state'] = 'nursery';
        }

        //成长期
        $tmp = array();
        $tmp['name']     = '成长期';
        $tmp['discount'] = $res['grow'] . ' %';
        $tmp['income']   = round($res['order_income'] * $res['grow'] / 100, 2);
        $tmp['day']      = $res['period_grow'];
        $list['grow']    = $tmp;
        $grow = $nursery + $res['period_grow']*86400;
        if ($nursery <= $today && $today < $grow) {
            $info['state'] = 'grow';
        }

        //成熟期
        $tmp = array();
        $tmp['name']      = '成熟期';
        $tmp['discount']  = $res['maturity'] . ' %';
        $tmp['income']    = round($res['order_income'] * $res['maturity'] / 100, 2);
        $tmp['day']       = $res['period_maturity'];
        $list['maturity'] = $tmp;
        $maturity = $grow + $res['period_maturity']*86400;
        if ($grow <= $today && $today < $maturity) {
            $info['state'] = 'maturity';
        }

        //收获期
        $tmp = array();
        $tmp['name']     = '收获期';
        $tmp['discount'] = $res['reap'] . ' %';
        $tmp['income']   = round($res['order_income'] * $res['reap'] / 100, 2);
        $tmp['day']      = $res['period_reap'];
        $list['reap']   = $tmp;
        $reap = $maturity + $res['period_reap']*86400;
        if ($maturity <= $today && $today < $reap) {
            $info['state'] = 'reap';
        }

        //获取单位收益
        $income = $res['plan_income'];
        if ($res['income_real'] != 0) {
            $income = $res['income_real'];
        }
        if ($res['order_income_set'] != 0) {
            $income = $res['order_income_set'];
        }

        //存储
        $tmp = array();
        $tmp['name']     = '存储期';
        $tmp['discount'] = '无';
        $tmp['income']   = round($income * $res['order_area'], 2);
        $tmp['day']      = $res['storage_time'];
        $list['storage'] = $tmp;
        $storage = $reap + $res['storage_time']*86400;
        if ($reap <= $today && $today < $storage) {
            $info['state'] = 'storage';
        }

        //过期
        $tmp = array();
        $tmp['name']    = '过期';
        $tmp['discount'] = $res['over'] . ' %';
        $tmp['income']   = round($income * $res['order_area'] * $res['over'] / 100, 2);
        $list['over'] = $tmp;
        if ($storage <= $today) {
            $info['state'] = 'over';
        }

        //如果已经完成收益的订单直接返回订单
        if ($res['status'] == 2) {
            $info['state'] = 'complete';
        }

        $info['list'] = $list;
        return $info;
    }
}