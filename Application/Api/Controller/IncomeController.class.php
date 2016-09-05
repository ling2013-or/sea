<?php
namespace Api\Controller;

/**
 * 销售方案订单收益管理
 */
class IncomeController extends ApiController
{
    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->uid = $this->isLogin();
    }

    /**
     * 订单列表
     */
    public function lists()
    {

        //查询条件
        $map['orders.user_id'] = $this->uid;
        $map['orders.status'] = array('neq',-1);

        /**
         * 按时间查询
         */
        if(isset($this->data['sn']) && !empty($this->data['sn']) && ($this->data['sn'] != '0,0')){
            if(false === strpos($this->data['sn'],',')){
                $this->apiReturn(45123,'非法操作');//查询条件不符合
            }
            $times = explode(',',$this->data['sn']);
            $time = array();
            $time[] = array('egt',$times[0]);
            $time[] = array('lt',$times[1]);
            $map['orders.add_time'] = $time;
        }


        //状态搜索
        if(isset($this->data['status']) && !empty($this->data['status'])){
            switch($this->data['status']){
                //待收益
                case 'state_new':
                    $map['orders.status'] = 1;
                    break;
                case 'state_receive':
                    $map['orders.status'] = 2;
                    break;
                default:
                    $this->apiReturn(-1,'非法请求');
            }
        }



        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
        $count = M('SellOrder')->alias('orders')->where($map)->count();

        //查询所有分单
        $res = M('SellOrder')->alias('orders')
            ->field('seed.seed_img,seed.seed_id,seed.seed_name,storage.storage_name,plan.plan_name,orders.order_id,orders.payment_id,orders.storage_id,orders.plan_id,
			orders.order_area,orders.add_time,orders.plan_price,orders.order_sn,orders.order_price,orders.storage_price,orders.pay_total,
			orders.plan_income,orders.order_income_final,orders.status')
            ->join('__SEED__ seed ON orders.seed_id = seed.seed_id','LEFT')
            ->join('__PLAN_STORAGE__ storage ON orders.storage_id=storage.storage_id','LEFT')
            ->join('__PLAN_SELL__ plan ON orders.plan_id=plan.plan_id','LEFT')
            ->where($map)->limit($limit)->select();
        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $res ? $res : '',
        );
        $this->apiReturn(0,'成功',$data);
    }

    /**
     * 删除订单
     *
     * id 单条订单ID
     */
    public function del()
    {
        try {
            $m = M('SellOrder');
            $m->startTrans();
            $map['status'] = 2;
            $map['order_id'] = intval($this->data['id']);
            $map['user_id'] = $this->uid; //订单所属用户
            $data['status'] = -1;

            //查询订单
            $order = M('SellOrder')->field('order_id,order_sn')->where($map)->select();
            if (!$order) {
                throw new \Exception('暂无订单信息',46501);
            }

            //删除订单
            $result = $m->where($map)->save($data);
            if ($result === false) {
                throw new \Exception('订单删除失败',46502);
            }

            //添加订单日志
            $order_log['operate_rule'] = 1;
            $order_log['operate_user'] = $this->uid;
            $order_log['operate_time'] = NOW_TIME;
            $order_log['operate_descript'] = '删除已完成的订单';
            $order_log['operate_status'] = 0;
            $order_log['order_id'] = $order['order_id'];
            $order_log['order_sn'] = $order['order_sn'];

            if (M('SellOrderLog')->add($order_log)) {
                $m->commit();
                $this->apiReturn(0,'成功');
            } else {
                throw new \Exception("订单日志添加失败",46503);//添加订单日志失败
            }
        } catch(\Exception $e) {
            $m->rollBack();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 查看种子订单收益
     *
     * id 订单ID
     */
    public function details()
    {
        //获取订单ID
        $order_id = intval($this->data['id']);
        if (!$order_id) {
            $this->apiReturn(46506,'订单ID不能为空');
        }


        //获取订单信息
        $res = D('SellOrder')->getOrderDetails($order_id, $this->uid);
        if ($res) {
            $this->apiReturn(0,'成功',$res);
        } else {
            $this->apiReturn(46501,'暂无订单信息');
        }
    }

    /**
     * 确认收益
     * id 订单ID
     */
    public function income(){

        //获取订单号
        $order_id = intval($this->data['id']);
        if (!$order_id) {
            $this->apiReturn(46506,'订单ID不能为空');
        }

        try {
            //获取订单
            $d = D('SellOrder');
            $d->startTrans();
            $res = $d->getOrderDetails($order_id, $this->uid);
            if (!$res) {
                throw new \Exception('暂无订单信息',46501);
            }

            //判断订单状态
            if ($res['order']['status'] == 2) {
                throw new \Exception('订单已经完成收益',46507);
            }

            //修改订单状态和实际收益
            $state = $res['state'];
            $income = $res['list'][$state]['income'];
            $data['order_id'] = $order_id;
            $data['order_income_final'] = $income;
            $data['order_income_period'] = $res['state'];
            $data['status'] = 2;
            $data['update_time'] = NOW_TIME;
            if ($d->save($data) === false) {
                throw new \Exception('系统繁忙，请稍候重试',-1);
            }

            //添加订单日志
            $log['order_id'] = $res['order']['order_id'];
            $log['order_sn'] = $res['order']['order_sn'];
            $log['operate_rule'] = 1;
            $log['operate_user'] = $this->uid;
            $log['operate_time'] = NOW_TIME;
            $log['operate_descript'] = '收益' . $res['order']['seed_name'] . $income . '千克';
            $log['operate_status'] = 2;
            if (M('SellOrderLog')->add($log) === false) {
                throw new \Exception('系统繁忙，请稍候重试',-1);
            }

            //添加库存
            $storage = D('UserStorage');
            $result = $storage->changeStorage('income_add', $this->uid, $res['order']['seed_id'], $res['order']['plan_id'], $income, $res['order']['order_sn']);
            if ($result) {
                $d->commit();
                $data['income'] = $income;
                $this->apiReturn(0, '成功', $data);
            } else {
                throw new \Exception($storage->getError(), $storage->getCode());
            }

        } catch (\Exception $e) {
            $d->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }
}