<?php

namespace Api\Controller;

/**
 * 充值管理接口
 * 注：返回码说明：41201开始
 * Class ChargeController
 * @package Api\Controller
 */
class ChargeController extends ApiController
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
     * 添加充值
     */
    public function add()
    {
        if (!isset($this->data['amount']) || empty($this->data['amount'])) {
            $this->apiReturn(41201, '充值金额不能为空');
        }

        $amount = abs(floatval($this->data['amount']));
        if ($amount <= 0) {
            $this->apiReturn(41202, '充值金额不能小于0元');
        }

        $Model = D('UserCharge');
        $data = array();
        $data['charge_sn'] = $Model->makeSn();
        $data['user_id'] = $this->uid;
        $data['user_name'] = M('User')->where(array('uid' => $this->uid))->getField('user_name');
        $data['charge_amount'] = $amount;
        $data['add_time'] = NOW_TIME;
        //TODO 充值状态（有待修改）
        $data['payment_state'] = 1;
        $account = D('UserAccount');

        //充值成功之后修改账户资金
        try {
            if ($Model->add($data)) {
                //获取用户的详细信息
                $user_info = M('User')->field('user_name')->where(array('uid'=>$this->uid))->find();
                //修改用户的资金账户
                $data_log = array();
                $data_log['affect_money'] = $amount;
                $data_log['withdraw_freeze'] = 0;
                $data_log['order_sn'] = $data['charge_sn'];
                $data_log['uid'] = $this->uid;
                $data_log['user_name'] = $user_info['user_name'];
                $result = $account->changeAccount('recharge', $data_log);
                if (!$result) {
                    throw new \Exception($account->getError(), $account->getCode());
                }
                $this->apiReturn(0, '添加成功，请支付');
            } else {
                $this->apiReturn(-1, '系统繁忙，请稍候重试');
            }
        } catch (\Exception $e) {
            $account->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }

    }

    /**
     * 充值列表
     */
    public function lists()
    {
        $condition = array();
        // TODO 查询条件

        $condition['uid'] = $this->uid;

        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }

        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = M('UserCharge')->where($condition)->count();

        // 查询字段
        $fields = 'id,charge_sn,charge_amount,payment_state,add_time,trade_sn,payment_time';
        $lists = M('UserCharge')->field($fields)->where($condition)->order('id DESC')->limit($limit)->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 删除充值记录
     * 注：只能删除没有充值的记录
     */
    public function del()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(41211, '请选择要删除的充值记录');
        }

        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['id'] = $this->data['id'];
        $condition['payment_state'] = 0;

        $res = M('UserCharge')->where($condition)->save(array('payment_state' => -1));
        if (false === $res) {
            $this->apiReturn(-1, '系统错误，请稍候重试');
        }
        $this->apiReturn(0, '删除成功');
    }
}