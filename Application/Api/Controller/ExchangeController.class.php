<?php

namespace Api\Controller;

/**
 * 交换管理
 * Class ExchangeController
 * @package Api\Controller
 */
class ExchangeController extends ApiController
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
     * 查看交换列表 (默认查看发起人身份)
     *
     *type 列表类型 0用户作为发起人 1用户作为接收人
     */
    public function lists()
    {
        $m = M('ObjectExchange');
        $map['status'] = array('neq',-1);

        //作为发起人 或 接收人
        if ($this->data['type']) {
            $map['receive_id'] = $this->uid;
        } else {
            $map['initiator_id'] = $this->uid;
        }

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = $m->where($map)->count();
        $lists = $m->where($map)->limit($limit)->order('add_time DESC')->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        //返回数据
        if ($lists) {
            $this->apiReturn(0,'成功',$data);
        } else {
            $this->apiReturn(49001,'暂无交易信息');
        }
    }

    /**
     * 确认交换
     *
     * id 交换单ID
     * plan_id 如果是接收人还要传入接收人使用方案的ID
     */
    public function agree()
    {
        //获取订单
        $id = $this->data['id'];
        if (empty($id)) {
            $this->apiReturn(49002,'交易ID为空');
        }

        try {
            $m = M('ObjectExchange');
            $m->startTrans();

            //查找相应的交易记录
            $map_a['initiator_id'] = $this->uid;
            $map_a['receive_id'] = $this->uid;
            $map_a['_logic'] = 'or';
            $map[] = $map_a;
            $map['id'] = $data['id'] = $id;
            $map['status'] = 0;
            $res = $m->where($map)->find();
            if (!$res) {
                throw new \Exception('查询不到交易信息',49002);
            }

            //判断交易是否已经过期
            if ($res['expire_time'] <= NOW_TIME) {
                throw new \Exception('交易已经过期',49009);
            }

            //交易完成状态
            $exchange_complete = false;
            $change = true;

            //发起人请求确认
            if ($res['initiator_id'] == $this->uid && $res['initiator_status'] == 0) {
                $change = false;
                $data['initiator_status'] = 1;
                if ($res['receive_status'] == 1) {
                    $exchange_complete = true;
                    $data['status'] = 1;
                }
            }

            //接收人请求确认
            if ($res['receive_id'] == $this->uid && $res['receive_status'] == 0) {
                $change = false;
                $data['receive_status'] = 1;
                if ($res['initiator_status'] == 1) {
                    $exchange_complete = true;
                    $data['status'] = 1;
                }
            }

            //是否已经产生操作
            if ($change) {
                throw new \Exception('无效操作',49005);
            }

            $data['update_time'] = NOW_TIME;
            if ($m->save($data) !== false) {

                //交易完成
                if ($exchange_complete) {

                    //发起人拿出的库存
                    $initiator_map['exchange_id'] = $res['id'];
                    $initiator_map['user_id'] = $res['initiator_id'];
                    $initiator_info = M('ObjectExchangeData')->where($initiator_map)->find();

                    //发起人减少库存 同时验证库存
                    //TODO

                    //接收人增加库存
                    //TODO

                    //接收人拿出的库存
                    $receive_map['exchange_id'] = $res['id'];
                    $receive_map['user_id'] = $res['receive_id'];
                    $receive_info = M('ObjectExchangeData')->where($receive_map)->find();
                    if (empty($receive_info['plan_id'])) {
                        if (empty($this->data['plan_id'])) {
                            throw new \Exception('请先指定库存',49003);
                        } else {
                            $receive_info['plan_id'] = $this->data['plan_id'];
                        }
                    }

                    //接收人减少库存  同时验证库存
                    //TODO

                    //发起人增加库存
                    //TODO

                }

                //成功执行
                $this->commit();
                $this->apiReturn(0,'成功');
            } else {
                throw new \Exception('确认失败',49004);
            }
        } catch(\Exception $e) {
            $this->rollBack();
            $this->apiReturn($e->getCode() , $e->getMessage());
        }

    }

    /**
     * 取消交换
     *
     * 说明：只有交换单 status 为0时 才可以取消交换 修改 status 为2
     * id 交换单ID
     */
    public function cancel()
    {
        //获取订单ID
        $id = $this->data['id'];
        if (empty($id)) {
            $this->apiReturn(49002,'交易ID为空');
        }

        try {
            //查看交换记录
            $m = M('ObjectExchange');
            $m->startTrans();
            $map_a['initiator_id'] = $this->uid;
            $map_a['receive_id'] = $this->uid;
            $map_a['_logic'] = 'or';
            $map[] = $map_a;
            $map['id'] = $id;
            $map['status'] = 0;
            $res = $m->where($map)->find();
            if (!$res) {
                throw new \Exception('不存在可撤销的交易单',92031);
            }

            //修改交换记录状态
            $data['status'] = 2;
            $data['update_time'] = NOW_TIME;
            $data['id'] = $id;
            if ($m->save($data) === false) {
                throw new \Exception('操作失败',-1);
            }

            //删除交换详情
            $default_data['status'] = -1;
            $default_data['exchange_id'] = $id;
            if ($m->save($default_data) === false) {
                throw new \Exception('操作失败',-1);
            } else {
                $m->commit();
                $this->apiReturn(0,'成功');
            }
        } catch(\Exception $e) {
            $m->rollback();
            $this->apiReturn($e->getCode() , $e->getMessage());
        }

    }

    /**
     * 删除交换单
     *
     * 说明：交换单 status 为任何状态时都可以删除 修改 status 为-1
     * id 交换单ID
     */
    public function del()
    {
        //获取订单ID
        $id = $this->data['id'];
        if (empty($id)) {
            $this->apiReturn(49002,'交易ID为空');
        }

        try {
            //查看交换记录
            $m = M('ObjectExchange');
            $m->startTrans();
            $map_a['initiator_id'] = $this->uid;
            $map_a['receive_id'] = $this->uid;
            $map_a['_logic'] = 'or';
            $map[] = $map_a;
            $map['id'] = $id;
            $res = $m->where($map)->find();
            if (!$res) {
                throw new \Exception('不存在可删除的交易单',92031);
            }

            //修改交换记录状态
            $data['status'] = -1;
            $data['update_time'] = NOW_TIME;
            $data['id'] = $id;
            if ($m->save($data) === false) {
                throw new \Exception('操作失败',-1);
            }

            //删除交换详情
            $default_data['status'] = -1;
            $default_data['exchange_id'] = $id;
            if ($m->save($default_data) === false) {
                throw new \Exception('操作失败',-1);
            } else {
                $m->commit();
                $this->apiReturn(0,'成功');
            }
        } catch(\Exception $e) {
            $m->rollback();
            $this->apiReturn($e->getCode() , $e->getMessage());
        }
    }

    /**
     * 发布交换请求
     *
     * title                交易标题
     * receive_id           接收人id
     * expire_time          过期时间
     * initiator_plan_id    交换方案ID
     * initiator_weight     发起方拿出的重量
     * receive_seed_id      要交换的种子ID
     * receive_weight       要交换的重量
     */
    public function send()
    {
        try {
            $m = M('ObjectExchange');
            $m->startTrans();

            //获取被交易者名称
            $receive_name = $this->getUserName($this->data['receive_id']);
            if (!$receive_name) {
                throw new \Exception('接收用户不存在',49006);
            }

            //获取过期时间 默认1天
            $time = NOW_TIME;
            if (isset($this->data['expire_time'])) {
                $expire_time = $this->data['expire_time'];
            } else {
                $expire_time = $time + 86400; //1天
            }

            //生成交易单信息
            $data['order_sn'] = $this->makeSn($this->uid);
            $data['title'] = isset($this->data['title'])?$this->data['title']:'';
            $data['initiator_id'] = $this->uid;
            $data['initiator_name'] = $this->user_name;
            $data['receive_id'] = $this->data['receive_id'];
            $data['receive_name'] = $receive_name;
            $data['expire_time'] = $expire_time;
            $data['add_time'] = $time;
            $data['update_time'] = $time;
            $exchange_id = $m->add($data);
            if (!$exchange_id) {
                throw new \Exception('添加交易单失败',49007);
            }

            //生成发起者详情信息
            $initiator_data['exchange_id'] = $exchange_id;
            $initiator_data['user_id'] = $this->uid;
            $initiator_plan = $this->getStorageInfo($this->uid,$this->data['initiator_plan_id']);

            if (!$initiator_plan) {
                throw new \Exception('无可用库存',49008);
            }

            if ($initiator_plan['available_weight'] < $this->data['initiator_weight']) {
                throw new \Exception('可用库存不足',49008);
            }

            $initiator_data['plan_id'] = $initiator_plan['plan_id'];
            $initiator_data['seed_id'] = $initiator_plan['seed_id'];
            $initiator_data['seed_name'] = $initiator_plan['seed_name'];
            $initiator_data['weight'] = $this->data['initiator_weight'];
            $initiator_data['is_initiator'] = 1;

            //被交易者信息
            $receive_data['exchange_id'] = $exchange_id;
            $receive_data['user_id'] = $this->data['receive_id'];
            $receive_data['seed_id'] = $this->data['receive_seed_id'];
            $receive_data['seed_name'] = $receive_name;
            $receive_data['weight'] = $this->data['receive_weight'];
            $receive_data['is_initiator'] = 0;

            $detail_data[] = $initiator_data;
            $detail_data[] = $receive_data;

            $res = M('ObjectExchangeData')->addAll($detail_data);
            if ($res) {
                $this->commit();
                $this->apiReturn(0,'成功');
            } else {
                throw new \Exception('添加交易详情失败',49009);
            }
        } catch(\Exception $e) {
            $this->rollBack();
            $this->apiReturn($e->getCode() , $e->getMessage());
        }
    }

    /**
     * 获取用户名
     * @param int $uid 用户ID
     * @return mixed
     */
    private function getUserName($uid)
    {
        $map['uid'] = $uid;
        $map['status'] = 0;
        $user = M('User')->field('user_name')->where($map)->find();
        if ($user) {
            return $user['user_name'];
        } else {
            return false;
        }
    }

    /**
     * 库存验证
     * @param int $plan_id 方案ID
     * @return mixed
     */
    private function getStorageInfo($user_id,$plan_id)
    {
        $map['user_id'] = $user_id;
        $map['plan_id'] = $plan_id;
        $res = M('UserStorage')->field(true)->where($map)->find();
        return $res;
    }

    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $uid取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param int $uid 用户ID
     * @return string
     */
    private function makeSn($uid)
    {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        return $type . sprintf('%010d', time() - 946656000) . sprintf('%03d', (float)microtime() * 1000) . sprintf('%03d', (int)$uid % 1000) . sprintf('%02d', $num);
    }
}



