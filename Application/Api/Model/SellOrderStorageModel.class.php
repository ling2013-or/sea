<?php
namespace Api\Model;

use Think\Model;

/**
 * 库存操作
 * Class SellStorageModel
 * @package Api\Model
 */
class SellOrderStorageModel extends Model
{
    /**
     * 收益计算
     *
     * @param int $user_id 用户ID
     * @param int $storage_id 临时库存ID
     * @return bool
     */
    public function afterCrop($user_id,$storage_id)
    {
        //查询库存信息
        $map['id'] = $storage_id;
        $map['status'] = 1;
        $map['user_id'] = $user_id;
        $info = M('SellOrderStorage')->field(true)->where($map)->find();
        if (!$info) {
            return false;
        }

        //防止负库存
        if ($info['total'] < 0) {
            $info['total'] = 0;
        }

        //获取收益结束时间
        $now_day = date('Y-m-d',NOW_TIME);
        $now = strtotime($now_day) + 86399;

        //逾期时间(秒)
        $over = $info['end'] - $now;

        if ($over <= 0) {
            //未逾期时全部返回
            return $info;
        } else {
            //逾期时间(天)
            $days = ceil($over / 86400);
            $discount = 1 - $info['discount_over'] * $days;
            $weight = $discount * $info['total'];
            if ($weight > 0) {
                $info['total'] = $weight;
                return $info;
            } else {
                return 0;
            }
        }
    }

    /**
     * 折损计算
     *
     * @param int $user_id 用户ID
     * @param int $order_id 订单ID
     * @return mixed
     */
    public function beforeCrop($user_id,$order_id)
    {
        //查看订单是否已经收益
        $m = M('SellOrder');
        $map['user_id'] = $user_id;
        $map['order_id'] = $order_id;
        $storage = $this->where($map)->find();
        if ($storage) {
            return false; //订单已经收益
        }

        //查询订单所有期限和折扣
        $maps['orders.user_id'] = $user_id;
        $maps['orders.order_id'] = $order_id;
        $res = $m->alias('orders')
            ->join('__PLAN_SELL__ plan ON orders.plan_id=plan.plan_id')
            ->join('__PLAN_STORAGE__ storage ON orders.storage_id=storage.storage_id')
            ->join('__PLAN_DISCOUNT__ discount ON orders.discount_id=discount.discount_id','LEFT')
            ->join('__SEED__ seed ON seed.seed_id=orders.seed_id','LEFT')
            ->field('storage.storage_time,plan.plan_start,discount.seeding,discount.nursery,discount.grow,discount.maturity,discount.reap,discount.storage,discount.over,seed.period_seeding seed_seeding,seed.period_nursery seed_nursery,seed.period_grow seed_grow,seed.period_maturity seed_maturity,seed.period_reap seed_reap')
            ->where($map)->find();
        if (!$res) {
            return false; //不存在订单
        }

        $today = NOW_TIME;
        $info = array();

        //未开始
        if ($today < $res['plan_start']) {
            return false; //还未开始
        }

        //播种期
        $seeding = $res['plan_start'] + $res['seed_seeding']*86400;
        if ($res['plan_start'] <= $today && $today < $seeding) {
            $info['stage'] = 'seeding';
            $info['discount'] = $res['seeding'];
        }

        //育苗期
        $nursery = $seeding + $res['seed_nursery']*86400;
        if ($seeding <= $today && $today < $nursery) {
            $info['stage'] = 'nursery';
            $info['discount'] = $res['nursery'];
        }

        //成长期
        $grow = $nursery + $res['seed_grow']*86400;
        if ($nursery <= $today && $today < $grow) {
            $info['stage'] = 'grow';
            $info['discount'] = $res['grow'];
        }

        //成熟期
        $maturity = $grow + $res['seed_maturity']*86400;
        if ($grow <= $today && $today < $maturity) {
            $info['stage'] = 'maturity';
            $info['discount'] = $res['maturity'];
        }

        //收获期
        $reap = $maturity + $res['seed_reap']*86400;
        if ($maturity <= $today && $today < $reap) {
            $info['stage'] = 'reap';
            $info['discount'] = $res['reap'];
        }

        //存储
        $storage = $reap + $res['storage_time']*86400;
        if ($reap <= $today && $today < $storage) {
            $info['stage'] = 'nursery';
            $info['discount'] = $res['storage'];
        }

        //过期
        if ($storage <= $today) {
            $info['stage'] = 'over';
            $info['discount'] = $res['over'];
        }

        $total = $info['discount'] * $res['order_income'];
        $info['weight'] = $total;
        return $info;

    }
}