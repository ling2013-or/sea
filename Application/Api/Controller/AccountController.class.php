<?php

namespace Api\Controller;

/**
 * 会员资金账户管理
 * Class AccountController
 * @package Api\Controller
 */
class AccountController extends ApiController
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
     * 账户资金详情
     */
    public function detail()
    {
        $condition = array();
        $condition['uid'] = $this->uid;
        $fields = 'account_balance,account_amount,investment_amount,consume_amount,charge_amount,freeze_amount';
        $info = M('UserAccount')->field($fields)->where($condition)->find();
        $this->apiReturn(0, '成功', $info);
    }

    /**
     * 账户资金变动记录
     */
    public function changelist()
    {
        // TODO 时间查询条件
        $condition = array();
        $condition['uid'] = $this->uid;

        /**
         * order_pay下单支付预存款,
         * order_freeze下单冻结预存款,
         * order_cancel取消订单解冻预存款,
         * order_comb_pay下单支付被冻结的预存款,
         * recharge充值,cash_apply申请提现冻结预存款,
         * cash_pay提现成功,
         * cash_del取消提现申请，
         * 解冻预存款,refund退款
         */
        $type_array = array('order_pay', 'order_freeze', 'order_cancel', 'order_comb_pay', 'recharge', 'cash_apply',
            'cash_pay', 'cash_del', 'refund');
        if(isset($this->data['type']) && in_array($this->data['type'], $type_array)) {
            $condition['type'] = $this->data['type'];
        }

        //计算分页
        if(isset($this->data['page']) && intval($this->data['page']) > 0){
            $this->page = intval($this->data['page']);
        }

        if(isset($this->data['page_size']) && intval($this->data['page_size']) > 0){
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = M('UserAccountChange')->where($condition)->count();

        // 查询字段
        $fields = 'type,affect_money,available_money,freeze_money,description,add_time';
        $lists = M('UserAccountChange')->field($fields)->where($condition)->order('id DESC')->limit($limit)->select();

        $data = array(
            'page'  => $this->page,
            'count' => $count,
            'list'  => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 充值明细
     */
    public function chargelist()
    {
        // TODO 时间查询
        $condition = array();

        $condition['user_id'] = $this->uid;
        //计算分页
        if(isset($this->data['page']) && intval($this->data['page']) > 0){
            $this->page = intval($this->data['page']);
        }

        if(isset($this->data['page_size']) && intval($this->data['page_size']) > 0){
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = M('UserCharge')->where($condition)->count();

        // 查询字段
        $fields = 'charge_sn,charge_amount,payment_name,add_time,payment_time,payment_state';
        $lists = M('UserCharge')->field($fields)->where($condition)->order('id DESC')->limit($limit)->select();

        $data = array(
            'page'  => $this->page,
            'count' => $count,
            'list'  => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }


    /**
     * 检测用户的账户名称、支付密码是否设置
     */
    public function check_user(){
        //用户所需要检测的选项(只有两个  user_name,paypassword)
        $uid = $this->uid;
        //获取用户名称
        $model = M("User");
        $user_info = $model->field('pay_pass,user_name')->where(array('uid'=>$uid))->find();
        if(false === $user_info){
            $this->apiReturn(-1,'查询失败');
        }
        $data = array('pay_pass'=>false,'user_name'=>false);
        if(!empty($user_info['pay_pass'])){
            $data['pay_pass'] = true;
        }

        if(!empty($user_info['user_name'])){
            $data['user_name'] = true;
        }
        $this->apiReturn(0,'成功',$data);
    }

}