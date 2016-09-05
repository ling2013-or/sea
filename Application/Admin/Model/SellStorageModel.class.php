<?php
namespace Admin\Model;

use Think\Model;

/**
 * 库存操作
 * 
 * Class SellStorageModel
 * @package Admin\Model
 */
class SellStorageModel extends Model
{
    /**
     * 库存变更
     * 
     * 说明：
     * 1、每个用户每方案仅对应一条库存信息
     * 2、可能增加库存的操作包括收益、购买、交换
     * 3、可能减少库存的操作包括出售、交换、赠送、使用
     * 4、待操作的库存将变为冻结库存，总库存=自由库存+冻结库存
     * 5、冻结库存使用时如果使用量大于冻结库存量，是可以直接使用自由库存
     * @param int $user 用户ID
     * @param int $plan 方案ID
     * @param int $total 变化量,正数表示添加,负数表示使用
     * @param string $info 库存日志的操作说明
     * @param bool $operation 冻结操作,默认false,为true时表示对冻结库存的操作
     * @return bool
     */
    public function changeStorage($user=0,$plan=0,$total=0,$info='',$operation=false)
    {
        try {
            //开启事务
            $this->startTrans();

            //验证方案是否存在
            //TODO
            
            //验证用户是否存在
            //TODO
            
            //查询当前库存是否存在
            $map['status'] = 1;
            $map['user_id'] = $user;
            $map['plan_id'] = $plan;
            $storage = $this->where($map)->find(); 
            if ($storage) {
                //总库存(自由+冻结)
                $stock = $storage['stock'];
                
                //冻结库存
                $freeze = $storage['freeze'];
                
                //修改库存
                if ($operation) {
                    //冻结操作
                    //使用量大于总库存
                    if ($stock + $total < 0) throw new \Exception('总库存不足');
                    
                    //要冻结库存大于自由库存
                    if ($stock - $freeze < $total) throw new \Exception('可用库存不足');
                    
                    //总库存和冻结库存变化
                    $data['freeze'] = $freeze + $total > 0 ? $freeze + $total : 0;
                    if ($total<0) {
                        $stock += $total;
                        $data['stock'] = $stock;
                    } else {
                        $total = 0;
                    }
                    
                } else {
                    //使用量大于自由库存
                    if ($stock - $freeze + $total < 0) throw new \Exception('可用库存不足');
                    
                    //总库存变化
                    $stock += $total;
                    $data['stock'] = $stock;
                    
                }

                //执行修改
                $data['id'] = $storage['id'];
                $res = $this->save($data);
                if (!$res) throw new \Exception('库存修改失败');
            } else {
                //无库存时不可进行冻结操作
                if ($operation) throw new \Exception('暂无可用库存');

                //创建库存时初始量不能小于0
                if ($total<0) throw new \Exception('初始库存量不能为负');

                //添加库存
                $planInfo = M('PlanSell')->field('seed_id,plan_start,plan_end')->find($plan);
                $data['plan_id'] = $plan;
                $data['user_id'] = $user;
                $data['seed_id'] = $planInfo['seed_id'];
                $data['initialize'] = $data['stock'] = $total;
                $data['start_time'] = $planInfo['plan_start'];
                $data['end_time'] = $planInfo['plan_end'];
                $data['add_time'] = $data['update_time'] = NOW_TIME;
                $sid = $this->add($data);
                if (!$sid) throw new \Exception('库存添加失败');

                //当前总库存
                $stock = $total;
            }

            //添加库存日志
            //操作者 ??
            $log['user'] = session('admin_id') ? 0 : $user;
            $log['descript'] = $info;
            $log['storage_id'] = isset($sid)?$sid:$storage['id'];
            $log['storage_change'] = $total; 
            $log['storage_stock'] = $stock;
            $log['operate_time'] = NOW_TIME;
            $res = M('StorageLog')->add($log);
            if ($res) {
                $this->commit();
                return true;
            } else {
                throw new \Exception('日志添加失败');
            }
        } catch(\Exception $e){
            $this->rollback();
            echo $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 查看用户库存信息
     * @param int $user 用户ID
     * @param int $plan 方案ID
     * @return fixed array or false
     */
    public function countStorage($user=0,$plan=0)
    {
        $map['storage.status'] = 1;
        $map['storage.user_id'] = $user;
        $map['storage.plan_id'] = $plan;
        $res = $this->alias('storage')
        ->join('__USER__ user ON user.uid=storage.user_id','LEFT')
        ->join('__PLAN_SELL__ plan ON plan.plan_id=storage.plan_id','LEFT')
        ->field('storage.*,user.user_name,plan.plan_name')
        ->where($map)->find();
        if ($res) {
            $res['free'] = $res['stock'] - $res['freeze'];
            return $res;    
        } else {
            return false;
        }
    }
}