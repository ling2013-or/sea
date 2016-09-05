<?php
namespace Api\Model;

use Think\Model;

/**
 * 库存操作
 * Class UserStorageModel
 * @package Api\Model
 */
class UserStorageModel extends Model
{

    /**
     * 错误码
     * @var int
     */
    protected $code = 0;

    /**
     * 获取用户库存总览
     *
     * @param int $user_id 用户ID
     * @param int $seed_id 种子ID
     * @return mixed
     */
    public function getStorageSummary($user_id, $seed_id)
    {
        $map['user_id'] = $user_id;
        $map['seed_id'] = $seed_id;
        $res = $this->field(true)->where($map)->find();
        return $res;
    }

    /**
     * 获取用户库存总览详情
     *
     * @param int $user_id 用户ID
     * @param int $summary_id 库存总览ID
     * @return mixed
     */
    public function getStorage($user_id, $summary_id)
    {
        $map['user_id'] = $user_id;
        $map['summary_id'] = $summary_id;
        $res = $this->field(true)->where($map)->select();
        return $res;
    }

    /**
     * 库存变化操作：农作物出售更改库存
     * TODO 没有校验库存合法性，调用此方法之前已经校验
     * @param  string $change_type 库存变更类型
     * @param  int $uid 会员ID
     * @param  int $storage_id 库存ID
     * @param  float $weight 库存变更重量（正数）
     * @return bool
     */
    public function sellChangeStorage($change_type, $uid, $storage_id, $weight)
    {
        // 获取库存详情
        $condition = array();
        $condition['storage_id'] = $storage_id;
        $condition['user_id'] = $uid;
        $info = $this->where($condition)->find();
        if (!$info) {
            $this->code = 51321;
            $this->error = '库存信息不存在';
            return false;
        }

        $data_storage = array();        // 库存变化
        $data_storage['update_time'] = NOW_TIME;

        $data_summary = array();        // 总库存变化
        $data_summary['update_time'] = NOW_TIME;

        $data_log = array();            // 变化日志详情
        $data_log['user_id'] = $uid;
        $data_log['plan_id'] = $info['plan_id'];
        $data_log['seed_name'] = $info['seed_name'];
        $data_log['type'] = $change_type;
        switch ($change_type) {
            case 'sell_direct':     // 直接出售扣除（无冻结），注：暂时用不到
                $data_log['storage_available'] = $info['available_weight'] - $weight;
                $data_log['storage_change'] = -$weight;
                $data_log['storage_freeze'] = $info['freeze_weight'];
                $data_log['description'] = '出售农作物成功：' . $info['seed_name'] . '，扣除库存';

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_storage['available_weight'] = array('exp', 'available_weight - ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_summary['available_weight'] = array('exp', 'available_weight - ' . $weight);
                break;
            case 'sell_freeze':     // 出售冻结
                $data_log['storage_available'] = $info['available_weight'] - $weight;
                $data_log['storage_change'] = -$weight;
                $data_log['storage_freeze'] = $info['freeze_weight'] + $weight;
                $data_log['description'] = '出售农作物：' . $info['seed_name'] . '，冻结库存';

                // 库存变化
                $data_storage['available_weight'] = array('exp', 'available_weight - ' . $weight);
                $data_storage['freeze_weight'] = array('exp', 'freeze_weight + ' . $weight);

                //库存总览变化
                $data_summary['available_weight'] = array('exp', 'available_weight - ' . $weight);
                $data_summary['freeze_weight'] = array('exp', 'freeze_weight + ' . $weight);
                break;
            case 'sell_unfreeze':   // 取消出售，库存解冻
                $data_log['storage_available'] = $info['available_weight'] + $weight;
                $data_log['storage_change'] = $weight;
                $data_log['storage_freeze'] = $info['freeze_weight'] - $weight;
                $data_log['description'] = '取消出售农作物：' . $info['seed_name'] . '，解冻库存';

                // 库存变化
                $data_storage['available_weight'] = array('exp', 'available_weight + ' . $weight);
                $data_storage['freeze_weight'] = array('exp', 'freeze_weight - ' . $weight);

                //库存总览变化
                $data_summary['available_weight'] = array('exp', 'available_weight + ' . $weight);
                $data_summary['freeze_weight'] = array('exp', 'freeze_weight - ' . $weight);
                break;
            case 'sell_pay':        // 扣除冻结
                $data_log['storage_available'] = $info['available_weight'];
                $data_log['storage_change'] = -$weight;
                $data_log['storage_freeze'] = $info['freeze_weight'] - $weight;
                $data_log['description'] = '出售农作物成功：' . $info['seed_name'] . '，扣除解冻库存';

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_storage['freeze_weight'] = array('exp', 'freeze_weight - ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_summary['freeze_weight'] = array('exp', 'freeze_weight - ' . $weight);
                break;
            default:
                $this->code = 51322;
                $this->error = '操作库存类型不存在';
                return false;
                break;
        }

        // 更新库存
        if (false === $this->where(array('storage_id' => $storage_id))->save($data_storage)) {
            $this->code = 51323;
            $this->error = '更新库存失败';
            return false;
        }

        if (false === M('UserStorageSummary')->where(array('summary_id' => $info['summary_id']))->save($data_summary)) {
            $this->code = 51324;
            $this->error = '更新库存失败';
            return false;
        }
        return $this->changeLog($data_log);
    }

    /**
     * 库存变化操作：农作物库存配送冻结库存，只适用于添加配送，不适用取消配送订单
     * TODO 没有校验库存合法性，调用此方法之前已经校验
     * @param  int $uid 会员ID
     * @param  int $seed_id 农作物（种子）ID
     * @param  float $weight 库存变更重量（正数）
     * @param  string $order_sn 配送订单号
     * @return bool|string
     */
    public function pickChangeStorage($uid, $seed_id, $weight, $order_sn)
    {
        $summaryModel = M('UserStorageSummary');
        // 获取库存详情
        $condition = array();
        $condition['seed_id'] = $seed_id;
        $condition['user_id'] = $uid;
        $summary_info = $summaryModel->field(true)->where($condition)->find();
        if (!$summary_info) {
            $this->code = 51331;
            $this->error = '库存信息不存在';
        }

        // 获取按批次库存存储
        $lists = $this->field(true)->where(array('summary_id' => $summary_info['summary_id']))->order('plan_id ASC')->select();
        if (!$lists) {
            $this->code = 51333;
            $this->error = '库存信息不存在';
            return false;
        }

        // 根据提货重量冻结合理的库存批次，并冻结库存
        $stock = $weight;
        $stock_array = array();
        foreach ($lists as $val) {
            $stock -= $val['available_weight'];
            $plan_weight = $stock > 0 ? $val['available_weight'] : $stock;
            $stock_array[$val['storage_id']] = array(
                'weight' => $plan_weight,
                'plan_id' => $val['plan_id'],
            );

            // 冻结库存
            $data_storage = array();        // 库存变化
            $data_storage['update_time'] = NOW_TIME;
            $data_storage['available_weight'] = array('exp', 'available_weight - ' . $plan_weight);
            $data_storage['freeze_weight'] = array('exp', 'freeze_weight + ' . $plan_weight);
            if (false === $this->where(array('storage_id' => $val['storage_id']))->save($data_storage)) {
                $this->code = 51334;
                $this->error = '更新库存失败';
                return false;
            }

            // 记录日志
            $data_log = array();            // 变化日志详情
            $data_log['user_id'] = $uid;
            $data_log['plan_id'] = $val['plan_id'];
            $data_log['seed_name'] = $summary_info['seed_name'];
            $data_log['type'] = 'pick_freeze';
            $data_log['storage_available'] = $val['available_weight'] - $plan_weight;
            $data_log['storage_change'] = -$plan_weight;
            $data_log['storage_freeze'] = $val['freeze_weight'] + $plan_weight;
            $data_log['description'] = '库存农作物配送：' . $summary_info['seed_name'] . '，冻结库存，订单号：' . $order_sn;
            if (!$this->changeLog($data_log)) {
                return false;
            }
            if ($stock <= 0) break;
        }

        $data_summary = array();        // 总库存变化
        $data_summary['update_time'] = NOW_TIME;
        $data_summary['available_weight'] = array('exp', 'available_weight - ' . $weight);
        $data_summary['freeze_weight'] = array('exp', 'freeze_weight + ' . $weight);
        if (false === $summaryModel->where(array('summary_id' => $summary_info['summary_id']))->save($data_summary)) {
            $this->code = 51335;
            $this->error = '更新库存失败';
            return false;
        }
        return $stock_array;
    }

    /**
     * 库存变化操作：农作物库存配送更改库存，取消配送订单，完成配送订单
     * TODO 没有校验库存合法性，调用此方法之前已经校验
     * @param  string $change_type 库存变更类型
     * @param  int $uid 会员ID
     * @param  int $summary_id 会员总库存ID
     * @param  float $weight 库存变更重量（正数）
     * @param  array $stock_array 批次库存变更重量
     * @param  string $order_sn 配送订单号
     * @return bool
     */
    public function psChangeStorage($change_type, $uid, $summary_id, $weight, $stock_array, $order_sn)
    {
        $summaryModel = M('UserStorageSummary');
        // 获取库存详情
        $condition = array();
        $condition['summary_id'] = $summary_id;
        $condition['user_id'] = $uid;
        $summary_info = $summaryModel->field(true)->where($condition)->find();
        if (!$summary_info) {
            $this->code = 51341;
            $this->error = '库存信息不存在';
        }

        switch ($change_type) {
            case 'pick_unfreeze':
                foreach ($stock_array as $storage_id => $info) {
                    $detail = $this->where(array('storage_id' => $storage_id))->find();
                    // 冻结库存
                    $data_storage = array();        // 库存变化
                    $data_storage['update_time'] = NOW_TIME;
                    $data_storage['available_weight'] = array('exp', 'available_weight + ' . $info['weight']);
                    $data_storage['freeze_weight'] = array('exp', 'freeze_weight - ' . $info['weight']);
                    if (false === $this->where(array('storage_id' => $storage_id))->save($data_storage)) {
                        $this->code = 51343;
                        $this->error = '更新库存失败';
                        return false;
                    }

                    // 记录日志
                    $data_log = array();            // 变化日志详情
                    $data_log['user_id'] = $uid;
                    $data_log['plan_id'] = $info['plan_id'];
                    $data_log['seed_name'] = $summary_info['seed_name'];
                    $data_log['type'] = $change_type;
                    $data_log['storage_available'] = $detail['available_weight'] + $info['weight'];
                    $data_log['storage_change'] = $info['weight'];
                    $data_log['storage_freeze'] = $detail['freeze_weight'] - $info['weight'];
                    $data_log['description'] = '取消库存农作物配送：' . $summary_info['seed_name'] . '，解冻库存，订单号：' . $order_sn;
                    if (!$this->changeLog($data_log)) {
                        return false;
                    }
                }

                $data_summary = array();        // 总库存变化
                $data_summary['update_time'] = NOW_TIME;
                $data_summary['available_weight'] = array('exp', 'available_weight + ' . $weight);
                $data_summary['freeze_weight'] = array('exp', 'freeze_weight - ' . $weight);
                if (false === $summaryModel->where(array('summary_id' => $summary_id))->save($data_summary)) {
                    $this->code = 51344;
                    $this->error = '更新库存失败';
                    return false;
                }
                break;
            case 'pick_pay':
                foreach ($stock_array as $storage_id => $info) {
                    $detail = $this->where(array('storage_id' => $storage_id))->find();
                    // 冻结库存
                    $data_storage = array();        // 库存变化
                    $data_storage['update_time'] = NOW_TIME;
                    $data_storage['total_weight'] = array('exp', 'total_weight - ' . $info['weight']);
                    $data_storage['freeze_weight'] = array('exp', 'freeze_weight - ' . $info['weight']);
                    if (false === $this->where(array('storage_id' => $storage_id))->save($data_storage)) {
                        $this->code = 51343;
                        $this->error = '更新库存失败';
                        return false;
                    }

                    // 记录日志
                    $data_log = array();            // 变化日志详情
                    $data_log['user_id'] = $uid;
                    $data_log['plan_id'] = $info['plan_id'];
                    $data_log['seed_name'] = $summary_info['seed_name'];
                    $data_log['type'] = $change_type;
                    $data_log['storage_available'] = $detail['available_weight'];
                    $data_log['storage_change'] = -$info['weight'];
                    $data_log['storage_freeze'] = $detail['freeze_weight'] - $info['weight'];
                    $data_log['description'] = '确认收货农作物：' . $summary_info['seed_name'] . '，扣除冻结库存，订单号：' . $order_sn;
                    if (!$this->changeLog($data_log)) {
                        return false;
                    }
                }

                $data_summary = array();        // 总库存变化
                $data_summary['update_time'] = NOW_TIME;
                $data_summary['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_summary['freeze_weight'] = array('exp', 'freeze_weight - ' . $weight);
                if (false === $summaryModel->where(array('summary_id' => $summary_id))->save($data_summary)) {
                    $this->code = 51344;
                    $this->error = '更新库存失败';
                    return false;
                }
                break;
            default:
                $this->code = 51342;
                $this->error = '操作库存类型不存在';
                return false;
                break;
        }
        return true;
    }

    /**
     * 库存变化操作：添加库存（单农作物添加处理）
     * TODO 兑换，交换添加
     * @param  string $change_type 库存变更类型
     * @param  int $uid 会员ID
     * @param  int $seed_id 种子（农作物）ID
     * @param  int $plan_id 销售方案ID
     * @param  float $weight 库存变更重量（正数）
     * @param  string $order_sn 商品订单号
     * @return bool
     */
    public function changeStorage($change_type, $uid, $seed_id, $plan_id, $weight, $order_sn = '')
    {
        // 检查此类农作物是否存在库存，如没有，则添加库存
        $condition = array();
        $condition['user_id'] = $uid;
        $condition['seed_id'] = $seed_id;
        $summaryModel = M('UserStorageSummary');
        $summary_info = $summaryModel->field(true)->where($condition)->find();
        if (!$summary_info) {
            $summary_info = array();
            $summary_info['user_id'] = $uid;
            $summary_info['seed_id'] = $seed_id['seed_id'];
            $summary_info['seed_name'] = M('Seed')->where(array('seed_id' => $seed_id))->getField('seed_name');
            $summary_info['total_weight'] = 0;
            $summary_info['available_weight'] = 0;
            $summary_info['freeze_weight'] = 0;
            $summary_info['update_time'] = NOW_TIME;
            $summary_info['summary_id'] = M('UserStorageSummary')->add($summary_info);
            if (!$summary_info['summary_id']) {
                $this->code = 51351;
                $this->error = '库存信息创建失败';
                return false;
            }
        }

        $condition = array();
        $condition['user_id'] = $uid;
        $condition['seed_id'] = $seed_id;
        $condition['summary_id'] = $summary_info['summary_id'];
        $storage_info = $this->field(true)->where($condition)->find();
        if (!$storage_info) {
            $storage_info = array();
            $storage_info['summary_id'] = $summary_info['summary_id'];
            $storage_info['plan_id'] = $plan_id;
            $storage_info['seed_id'] = $seed_id;
            $storage_info['seed_name'] = $summary_info['seed_name'];
            $storage_info['user_id'] = $uid;
            $storage_info['total_weight'] = 0;
            $storage_info['freeze_weight'] = 0;
            $storage_info['available_weight'] = 0;
            $storage_info['add_time'] = $storage_info['update_time'] = NOW_TIME;
            $storage_info['storage_id'] = $this->add($storage_info);
            if (!$storage_info['storage_id']) {
                $this->code = 51352;
                $this->error = '库存信息创建失败';
                return false;
            }
        }

        $data_storage = array();        // 库存变化
        $data_storage['update_time'] = NOW_TIME;

        $data_summary = array();        // 总库存变化
        $data_summary['update_time'] = NOW_TIME;

        $data_log = array();            // 变化日志详情
        $data_log['user_id'] = $uid;
        $data_log['plan_id'] = $plan_id;
        $data_log['seed_name'] = $summary_info['seed_name'];
        $data_log['type'] = $change_type;

        switch ($change_type) {
            case 'buy_add':     // 会员购买添加
                $data_log['storage_available'] = $storage_info['available_weight'] + $weight;
                $data_log['storage_change'] = $weight;
                $data_log['storage_freeze'] = $storage_info['freeze_weight'];
                $data_log['description'] = '会员购买农作物：' . $summary_info['seed_name'] . '，增加库存，订单编号：' . $order_sn;

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight + ' . $weight);
                $data_storage['available_weight'] = array('exp', 'available_weight + ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight + ' . $weight);
                $data_summary['available_weight'] = array('exp', 'available_weight + ' . $weight);
                break;
            case 'give_reduce':     // 赠送者库存减少
                $data_log['storage_available'] = $storage_info['available_weight'] - $weight;
                $data_log['storage_change'] = $weight;
                $data_log['storage_freeze'] = $storage_info['freeze_weight'];
                $data_log['description'] = '赠送农作物：' . $summary_info['seed_name'] . '，减少库存';

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_storage['available_weight'] = array('exp', 'available_weight - ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_summary['available_weight'] = array('exp', 'available_weight - ' . $weight);
                break;
            case 'give_add':        // 接受赠送者库存增加
                $data_log['storage_available'] = $storage_info['available_weight'] + $weight;
                $data_log['storage_change'] = $weight;
                $data_log['storage_freeze'] = $storage_info['freeze_weight'];
                $data_log['description'] = '接收会员赠送：' . $summary_info['seed_name'] . '，增加库存';

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight + ' . $weight);
                $data_storage['available_weight'] = array('exp', 'available_weight + ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight + ' . $weight);
                $data_summary['available_weight'] = array('exp', 'available_weight + ' . $weight);
                break;
            case 'store_to_money':  // 库存转金币
                $data_log['storage_available'] = $storage_info['available_weight'] - $weight;
                $data_log['storage_change'] = $weight;
                $data_log['storage_freeze'] = $storage_info['freeze_weight'];
                $data_log['description'] = '库存农作物：' . $summary_info['seed_name'] . '，转金币';

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_storage['available_weight'] = array('exp', 'available_weight - ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight - ' . $weight);
                $data_summary['available_weight'] = array('exp', 'available_weight - ' . $weight);
                break;
            case 'income_add':  //收益添加
                $data_log['storage_available'] = $storage_info['available_weight'] + $weight;
                $data_log['storage_change'] = $weight;
                $data_log['storage_freeze'] = $storage_info['freeze_weight'];
                $data_log['description'] = '收益农作物：' . $summary_info['seed_name'] . '，增加';

                // 库存变化
                $data_storage['total_weight'] = array('exp', 'total_weight + ' . $weight);
                $data_storage['available_weight'] = array('exp', 'available_weight + ' . $weight);

                //库存总览变化
                $data_summary['total_weight'] = array('exp', 'total_weight + ' . $weight);
                $data_summary['available_weight'] = array('exp', 'available_weight + ' . $weight);
                break;
            default:
                $this->code = 51353;
                $this->error = '操作库存类型不存在';
                return false;
                break;
        }

        // 更新库存
        if (false === $this->where(array('storage_id' => $storage_info['storage_id']))->save($data_storage)) {
            $this->code = 51354;
            $this->error = '更新库存失败';
            return false;
        }

        if (false === $summaryModel->where(array('summary_id' => $summary_info['summary_id']))->save($data_summary)) {
            $this->code = 51355;
            $this->error = '更新库存失败';
            return false;
        }
        return $this->changeLog($data_log);
    }


    /**
     * 库存操作
     *
     * 注意：未开启事务，调用时请放到事务中执行
     * 说明：
     *      1、增加库存[add] (期货转现货[add_by_crop],好友赠送[add_by_give],交换获取[add_by_exchange],用户购买[add_by_buy])
     *      2、减少可用库存[use] (赠送[use_by_give])
     *      3、增加冻结库存[freeze] (出售冻结[freeze_by_sell],配送冻结[freeze_by_send],交换冻结[freeze_by_exchange])
     *      4、减少冻结库存[use_freeze] (出售[use_freeze_by_sell],配送[use_freeze_by_send],交换[use_freeze_by_exchange])
     *      5、释放冻结库存[free] (出售解冻[free_by_sell],配送解冻[free_by_send],交换解冻[free_by_exchange])
     * @param string $operation 必须参数 ,库存操作选项
     * @param array $data 操作和描述 array('user_id','change','storage_id|'plan_id',['sn'],['user])
     *          user_id   必须参数，用户ID
     *          change    必须参数，保留两位小数且大于零的浮点数
     *          storage_id 库存ID，除了创建库存，storage_id 和 plan_id 操作相同
     *          plan_id    方案ID，创建库存时必须使用 plan_id
     *          sn         产生操作的订单编号
     *          user       如果是赠送，赠送的用户账户
     * @return bool
     */
    public function changeStorage_old($operation, $data)
    {
        //获取数据
        $user_id = $data['user_id'];//用户ID 必须存在
        $change = $data['change'];//改变量 必须存在
        $plan_id = isset($data['plan_id']) ? $data['plan_id'] : 0; //方案ID
        $storage_id = isset($data['storage_id']) ? $data['storage_id'] : 0; //库存ID
        $sn = isset($data['sn']) ? $data['sn'] : ''; //额外数据 订单数据
        $user = isset($data['user']) ? $data['user'] : ''; //额外数据 用户名称

        //查询当前方案的库存信息
        $map['user_id'] = $user_id;
        if ($plan_id == 0) {
            $map['storage_id'] = $storage_id;
        } else {
            $map['plan_id'] = $plan_id;
        }
        $storage = $this->field(true)->where($map)->find();
        //通过storage_id 获取 plan_id
        if ($plan_id == 0) {
            if (!$storage) {
                $this->code = 51301;
                $this->error = '不存在此库存信息';
                return false;
            } else {
                $plan_id = $storage['plan_id'];
            }
        }

        //查询方案信息
        $seed = M('PlanSell')->alias('plan')
            ->join('__SEED__ seed ON plan.seed_id=seed.seed_id', 'LEFT')
            ->field('plan.plan_name,seed.seed_id,seed.seed_name')
            ->find($plan_id);
        if (!$seed) {
            $this->code = 51302;
            $this->error = '存储的方案已不存在';
            return false;
        }
        if (empty($seed['seed_id'])) {
            $this->code = 51303;
            $this->error = '存储的种子已不存在';
            return false;
        }

        //查询库存总览信息
        $maps['user_id'] = $user_id;
        $maps['seed_id'] = $seed['seed_id'];
        $summary = M('UserStorageSummary')->field(true)->where($maps)->find();

        //不存在库存总览时创建库存总览
        if (!$summary) {
            $summary['user_id'] = $user_id;
            $summary['seed_id'] = $seed['seed_id'];
            $summary['seed_name'] = $seed['seed_name'];
            $summary['total_weight'] = 0;
            $summary['available_weight'] = 0;
            $summary['freeze_weight'] = 0;
            $summary['update_time'] = NOW_TIME;
            $summary['summary_id'] = M('UserStorageSummary')->add($summary);
        }
        if (!$summary['summary_id']) {
            $this->code = 51304;
            $this->error = '获取库存总览失败';
            return false;
        }

        //操作和日志信息
        switch ($operation) {
            case 'add_by_crop':
                $operate = 'add';
                $log = '收益入库,订单为' . $sn;
                break;
            case 'add_by_buy':
                $operate = 'add';
                $log = '购买入库,订单为' . $sn;
                break;
            case 'add_by_give':
                $operate = 'add';
                $log = '受赠于' . $user;
                break;
            case 'add_by_exchange':
                $operate = 'add';
                $log = '交换入库,订单为' . $sn;
                break;
            case 'use_by_give':
                $operate = 'use';
                $log = '赠送给' . $user;
                break;
            case 'freeze_by_sell':
                $operate = 'freeze';
                $log = '出售冻结,订单为' . $sn;
                break;
            case 'freeze_by_send':
                $operate = 'freeze';
                $log = '邮递冻结,订单为' . $sn;
                break;
            case 'freeze_by_exchange':
                $operate = 'freeze';
                $log = '交换冻结,订单为' . $sn;
                break;
            case 'use_freeze_by_sell':
                $operate = 'use_freeze';
                $log = '出售,订单为' . $sn;
                break;
            case 'use_freeze_by_send':
                $operate = 'use_freeze';
                $log = '邮递,订单为' . $sn;
                break;
            case 'use_freeze_by_exchange':
                $operate = 'use_freeze';
                $log = '交换,订单为' . $sn;
                break;
            case 'free_by_sell':
                $operate = 'free';
                $log = '取消出售,订单为' . $sn;
                break;
            case 'free_by_send':
                $operate = 'free';
                $log = '取消邮递,订单为' . $sn;
                break;
            case 'free_by_exchange':
                $operate = 'free';
                $log = '取消交换,订单为' . $sn;
                break;
            default:
                $this->code = 51305;
                $this->error = '未指定合法的操作';
                return false;
                break;
        }

        //库存操作
        switch ($operate) {
            case 'add':
                if ($storage) {
                    //如果已经存在则更新库存
                    $storage_data['storage_id'] = $storage['storage_id'];
                    $storage_data['available_weight'] = $storage['available_weight'] + $change;
                    $storage_data['total_weight'] = $storage['total_weight'] + $change;
                    $storage_data['update_time'] = NOW_TIME;
                    $res = $this->save($storage_data);
                    $storage_data['freeze_weight'] = $storage['freeze_weight'];
                } else {
                    //如果不存在则添加到库存
                    $storage_data['summary_id'] = $summary['summary_id'];
                    $storage_data['plan_id'] = $plan_id;
                    $storage_data['seed_id'] = $seed['seed_id'];
                    $storage_data['user_id'] = $user_id;
                    $storage_data['total_weight'] = $change;
                    $storage_data['freeze_weight'] = 0;
                    $storage_data['available_weight'] = $change;
                    $storage_data['add_time'] = $storage_data['update_time'] = NOW_TIME;
                    $res = $this->add($storage_data);
                }
                if (!$res) {
                    $this->code = 51306;
                    $this->error = '添加库存失败';
                    return false;
                }

                //库存总览变化
                $summary_data['available_weight'] = $summary['available_weight'] + $change;
                $summary_data['total_weight'] = $summary['total_weight'] + $change;

                //日志信息
                $log_data['storage_available'] = $storage_data['available_weight'];
                $log_data['storage_change'] = $change;
                $log_data['storage_freeze'] = $storage_data['freeze_weight'];
                break;
            case 'use':
                if (!$storage) {
                    $this->code = 51301;
                    $this->error = '不存在可用库存';
                    return false;
                }

                //库存变化
                if ($storage['available_weight'] < $change) {
                    $this->code = 51307;
                    $this->error = '使用量大于可用库存';
                    return false;
                }
                $storage_data['storage_id'] = $storage['storage_id'];
                $storage_data['available_weight'] = $storage['available_weight'] - $change;
                $storage_data['total_weight'] = $storage['total_weight'] - $change;
                $storage_data['update_time'] = NOW_TIME;
                $res = $this->save($storage_data);
                if (!$res) {
                    $this->code = 51308;
                    $this->error = '更新库存失败';
                    return false;
                }

                //库存总览变化
                $summary_data['available_weight'] = $summary['available_weight'] - $change;
                $summary_data['total_weight'] = $summary['total_weight'] - $change;

                //日志信息
                $log_data['storage_available'] = $storage_data['available_weight'];
                $log_data['storage_change'] = -$change;
                $log_data['storage_freeze'] = $storage['freeze_weight'];
                break;
            case 'freeze':
                if (!$storage) {
                    $this->code = 51301;
                    $this->error = '不存在可用库存';
                    return false;
                }
                if ($storage['available_weight'] < $change) {
                    $this->code = 51309;
                    $this->error = '冻结量大于可用库存';
                    return false;
                }
                $storage_data['storage_id'] = $storage['storage_id'];
                $storage_data['available_weight'] = $storage['available_weight'] - $change;
                $storage_data['freeze_weight'] = $storage['freeze_weight'] + $change;
                $storage_data['update_time'] = NOW_TIME;
                $res = $this->save($storage_data);
                if (!$res) {
                    $this->code = 51308;
                    $this->error = '更新库存失败';
                    return false;
                }

                //库存总览变化
                $summary_data['available_weight'] = $summary['available_weight'] - $change;
                $summary_data['freeze_weight'] = $summary['freeze_weight'] + $change;

                //日志信息
                $log_data['storage_available'] = $storage_data['available_weight'];
                $log_data['storage_change'] = 0;
                $log_data['storage_freeze'] = $storage_data['freeze_weight'];
                break;
            case 'free':
                if (!$storage) {
                    $this->code = 51301;
                    $this->error = '不存在可用库存';
                    return false;
                }
                if ($storage['freeze_weight'] < $change) {
                    $this->code = 51310;
                    $this->error = '释放量大于冻结库存';
                    return false;
                }
                $storage_data['storage_id'] = $storage['storage_id'];
                $storage_data['available_weight'] = $storage['available_weight'] + $change;
                $storage_data['freeze_weight'] = $storage['freeze_weight'] - $change;
                $storage_data['update_time'] = NOW_TIME;
                $res = $this->save($storage_data);
                if (!$res) {
                    $this->code = 51308;
                    $this->error = '更新库存失败';
                    return false;
                }

                //库存总览变化
                $summary_data['available_weight'] = $summary['available_weight'] + $change;
                $summary_data['freeze_weight'] = $summary['freeze_weight'] - $change;

                //日志信息
                $log_data['storage_available'] = $storage_data['available_weight'];
                $log_data['storage_change'] = 0;
                $log_data['storage_freeze'] = $storage_data['freeze_weight'];
                break;
            case 'use_freeze':
                if (!$storage) {
                    $this->code = 51301;
                    $this->error = '不存在可用库存';
                    return false;
                }
                if ($storage['freeze_weight'] < $change) {
                    $this->code = 51311;
                    $this->error = '使用量大于冻结库存';
                    return false;
                }
                $storage_data['storage_id'] = $storage['storage_id'];
                $storage_data['total_weight'] = $storage['total_weight'] - $change;
                $storage_data['freeze_weight'] = $storage['freeze_weight'] - $change;
                $storage_data['update_time'] = NOW_TIME;
                $res = $this->save($storage_data);
                if (!$res) {
                    $this->code = 51308;
                    $this->error = '更新库存失败';
                    return false;
                }

                //库存总览变化
                $summary_data['total_weight'] = $summary['total_weight'] - $change;
                $summary_data['freeze_weight'] = $summary['freeze_weight'] - $change;

                //日志信息
                $log_data['storage_available'] = $storage['available_weight'];
                $log_data['storage_change'] = -$change;
                $log_data['storage_freeze'] = $storage_data['freeze_weight'];
                break;
            default:
                return false;
                break;
        }

        //库存总览操作
        $summary_data['summary_id'] = $summary['summary_id'];
        $summary_data['update_time'] = NOW_TIME;
        $result = M('UserStorageSummary')->save($summary_data);
        if ($result === false) {
            $this->code = 51312;
            $this->error = '更新库存总览失败';
            return false;
        }

        //日志操作
        $log_data['user_id'] = $user_id;
        $log_data['plan_id'] = $plan_id;
        $log_data['seed_name'] = $seed['seed_name'];
        $log_data['type'] = $operation;
        $log_data['description'] = $log;
        $log_data['operate_time'] = NOW_TIME;
        $log_data['operate_ip'] = get_client_ip();
        $res = M('UserStorageLog')->add($log_data);
        if ($res) {
            return true;
        } else {
            $this->code = 51313;
            $this->error = '添加库存日志失败';
            return false;
        }
    }

    /**
     * 库存变化日志
     * @access private
     * @param  array $data
     * @return bool
     */
    private function changeLog($data)
    {
        $data['operate_time'] = NOW_TIME;
        $data['operate_ip'] = get_client_ip();
        $res = M('UserStorageLog')->add($data);
        if (!$res) {
            $this->code = 51381;
            $this->error = '添加库存变化日志失败';
            return false;
        }
        return true;
    }

    /**
     * 返回模型错误状态码
     * @return int 错误码
     */
    public function getCode()
    {
        return $this->code;
    }
}