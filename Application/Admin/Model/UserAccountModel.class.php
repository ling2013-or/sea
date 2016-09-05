<?php
namespace Admin\Model;

use Think\Model;

class UserAccountModel extends Model
{
    /**
     * 用户资金变化   （结合事务处理使用）
     * @param   string  $change_type    变更类型
     * @param   array   $data           变动数据 array('uid'=> '', 'user_name', 'affect_money'=> '', 'order_sn'=> '')四个参数缺一不可
     * @return bool
     */
    public function changeAccount($change_type, $data = array())
    {
        $data_log = array();
        $data_pd = array();
        $data_log['uid'] = $data['uid'];
        $data_log['user_name'] = $data['user_name'];
        $data_log['add_time'] = NOW_TIME;
        $data_log['add_ip'] = get_client_ip();
        $data_log['type'] = $change_type;
        // 获取当前账户资金状态
        $account = $this->field(true)->where(array('uid'=>$data['uid']))->find();
        if(!$account) {
            $this->error = '账户未找到';
            return false;
        }

        switch($change_type) {
            case 'order_pay':       // 下单支付预存款
                $data_log['affect_money'] = -$data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] - $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'];
                $data_log['description'] = '下单，支付预存款，订单号: '.$data['order_sn'];

                $data_pd['account_amount'] = array('exp', 'account_amount - ' . $data['affect_money']);
                $data_pd['account_balance'] = array('exp', 'account_balance - ' . $data['affect_money']);
                break;
            case 'order_freeze':    // 下单冻结预存款
                $data_log['affect_money'] = -$data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] - $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'] + $data['affect_money'];
                $data_log['description'] = '下单，冻结预存款，订单号: '.$data['order_sn'];

                $data_pd['account_balance'] = array('exp', 'account_balance - ' . $data['affect_money']);
                $data_pd['withdraw_freeze'] = array('exp','withdraw_freeze  + ' . $data['affect_money']);
                break;
            case 'order_cancel':    // 取消订单解冻预存款
                $data_log['affect_money'] = $data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] + $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'] - $data['affect_money'];
                $data_log['description'] = '取消订单，解冻预存款，订单号: '.$data['order_sn'];

                $data_pd['account_balance'] = array('exp', 'account_balance + ' . $data['affect_money']);
                $data_pd['withdraw_freeze'] = array('exp','withdraw_freeze  - ' . $data['affect_money']);
                break;
            case 'order_comb_pay':  // 下单支付被冻结的预存款
                $data_log['affect_money'] = -$data['affect_money'];
                $data_log['available_money'] = $account['account_balance'];
                $data_log['freeze_money'] = $account['withdraw_freeze'] - $data['affect_money'];
                $data_log['description'] = '下单，支付被冻结的预存款，订单号: '.$data['order_sn'];

                $data_pd['account_amount'] = array('exp', 'account_amount - ' . $data['affect_money']);
                $data_pd['withdraw_freeze'] = array('exp','withdraw_freeze  - ' . $data['affect_money']);
                break;
            case 'recharge':        // 充值
                $data_log['affect_money'] = $data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] + $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'];
                $data_log['description'] = '充值，充值单号: '.$data['order_sn'];

                $data_pd['account_amount'] = array('exp', 'account_amount + ' . $data['affect_money']);
                $data_pd['account_balance'] = array('exp', 'account_balance + ' . $data['affect_money']);
                $data_pd['charge_amount'] = array('exp','charge_amount  + ' . $data['affect_money']);
                $data_pd['investment_amount'] = array('exp','investment_amount  + ' . $data['affect_money']);
                break;
            case 'refund':          // 退款
                $data_log['affect_money'] = $data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] + $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'];
                $data_log['description'] = '确认退款，订单号: '.$data['order_sn'];

                $data_pd['account_amount'] = array('exp', 'account_amount + ' . $data['affect_money']);
                $data_pd['account_balance'] = array('exp', 'account_balance + ' . $data['affect_money']);
                break;
            case 'cash_apply':      // 申请提现冻结预存款
                $data_log['affect_money'] = -$data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] - $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'] + $data['affect_money'];
                $data_log['description'] = '申请提现，冻结预存款，提现单号: '.$data['order_sn'];

                $data_pd['account_balance'] = array('exp', 'account_balance - ' . $data['affect_money']);
                $data_pd['withdraw_freeze'] = array('exp', 'withdraw_freeze + ' . $data['affect_money']);
                break;
            case 'cash_pay':        // 提现成功
                $data_log['affect_money'] = -$data['affect_money'];
                $data_log['available_money'] = $account['account_balance'];
                $data_log['freeze_money'] = $account['withdraw_freeze'] - $data['affect_money'];
                $data_log['description'] = '提现成功，提现单号: '.$data['order_sn'];

                $data_pd['account_amount'] = array('exp', 'account_amount - ' . $data['affect_money']);
                $data_pd['withdraw_freeze'] = array('exp', 'account_balance - ' . $data['affect_money']);
                break;
            case 'cash_del':        // 取消提现申请，解冻预存款
                $data_log['affect_money'] = $data['affect_money'];
                $data_log['available_money'] = $account['account_balance'] + $data['affect_money'];
                $data_log['freeze_money'] = $account['withdraw_freeze'] - $data['affect_money'];
                $data_log['description'] = '取消提现申请，解冻预存款，提现单号: '.$data['order_sn'];

                $data_pd['account_balance'] = array('exp', 'account_balance + ' . $data['affect_money']);
                $data_pd['withdraw_freeze'] = array('exp', 'withdraw_freeze - ' . $data['affect_money']);
                break;
            default:
                $this->error = '参数错误';
                return false;
                break;
        }

        $update = $this->where(array('uid'=>$data['uid']))->save($data_pd);
        if (!$update) {
            $this->error = '操作失败';
            return false;
        }
        $insert = M('UserAccountChange')->add($data_log);
        if (!$insert) {
            $this->error = '操作失败';
            return false;
        }
        return true;

    }
}