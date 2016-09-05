<?php
namespace Admin\Logic;

use Think\Model;

/**
 * 提现逻辑层处理
 * Class WithdrawLogic
 * @package Admin\Logic
 */
class WithdrawLogic extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('audit_remark', 'require', '标识不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 构造函数，用于这是Logic层的表前缀
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix = C('DB_PREFIX') . 'user_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }

    /**
     * 处理提现
     * @param int $id 申请提现ID
     * @param string $remark 审核意见
     * @return  bool
     */
    public function audit($id, $remark = '')
    {
        $withdraw = $this->_withdrawStatus($id);
        if(!isset($withdraw['status'])) {
            $this->error('该提现不存在');
            return false;
        }
        $_status = $withdraw['status'];

        if($_status != 0) {
            $this->error('非法请求');
            return false;
        }

        // 更改状态
        if(false == $this->create()) {
            return false;
        }
        $data = array(
            'audit_id'      => UID,
            'audit_user'    => session('admin.admin_name'),     // TODO
            'audit_remark'  => $remark,
            'add_time'      => NOW_TIME,
            'status'        => 1,
        );
        $res = $this->where(array('id'=>$id))->save($data);
        if($res === false) {
            $this->error('审核失败');
            return false;
        } else {
            $this->_auditLog($id, $remark);
            return true;
        }
    }

    /**
     * 提现失败
     * @param int $id 申请提现ID
     * @param string $remark 处理意见
     * @return  bool
     */
    public function failure($id, $remark = '')
    {
        $withdraw = $this->_withdrawStatus($id);
        if(!isset($withdraw['status'])) {
            $this->error('该提现不存在');
            return false;
        }
        $_status = $withdraw['status'];
        if($_status != 1 && $_status != 0) {
            $this->error('非法操纵');
            return false;
        }

        // 更改状态
        if(false == $this->create()) {
            return false;
        }

        //启用事务处理
        $this->startTrans();
        // 修改提现状态
        $data = array(
            'audit_id'      => UID,
            'audit_user'    => session('admin.admin_name'),     // TODO
            'audit_remark'  => $remark,
            'add_time'      => NOW_TIME,
            'status'        => 3,
        );
        $money = $withdraw['withdraw_money'] + $withdraw['withdraw_fee'];
        $account_data = array(
            'withdraw_freeze'   => array('exp', 'withdraw_freeze - ' . $money),
            'account_amount'   => array('exp', 'account_amount + ' . $money),
            'account_balance'   => array('exp', 'account_balance + ' . $money),
        );
        // 获取当前账户信息
        $account = M('UserAccount')->field(true)->where(array('uid'=>$withdraw['uid']))->find();
        $account_log = array(
            'uid'           => $account['uid'],
            'type'          => 10,              // TODO 类型待定
            'affect_money'  => $money,
            'account_money' => $account['account_amount'] + $money,
            'freeze_money'  => $account['withdraw_freeze'] - $money,
            'add_time'      => NOW_TIME,
            'add_ip'        => get_client_ip(),
            'title'         => '用户提现',
            'description'   => '提现失败，账户解冻提现金额'       // TODO     具体内容
        );
        try {
            $res = $this->where(array('id'=>$id))->save($data);
            // 扣除冻结金额
            $account = M('UserAccount')->where(array('uid'=>$withdraw['uid']))->save($account_data);
            // 增加用户资金变动记录
            $log = M('UserAccountChange')->add($account_log);

            if($res !== false && $account !== false && $log !== false) {
                $this->commit();
                $this->_auditLog($id, $remark);
                return true;
            } else {
                $this->rollback();
                $this->error('修改失败，请重试');
                return false;
            }

        }catch (\Exception $e) {
            $this->rollback();
            $this->error($e->getMessage());
            return false;
        }
    }

    /**
     * 提现成功
     * @param int $id 申请提现ID
     * @param string $remark 处理意见
     * @return  bool
     */
    public function success($id, $remark = '')
    {
        $withdraw = $this->_withdrawStatus($id);
        if(!isset($withdraw['status'])) {
            $this->error('该提现不存在');
            return false;
        }
        $_status = $withdraw['status'];
        if($_status != 1) {
            $this->error('非法操纵');
            return false;
        }

        // 更改状态
        if(false == $this->create()) {
            return false;
        }

        //启用事务处理
        $this->startTrans();
        // 修改提现状态
        $data = array(
            'audit_id'      => UID,
            'audit_user'    => session('admin.admin_name'),     // TODO
            'audit_remark'  => $remark,
            'add_time'      => NOW_TIME,
            'status'        => 2,
        );
        $money = $withdraw['withdraw_money'] + $withdraw['withdraw_fee'];
        $account_data = array(
            'withdraw_freeze'   => array('exp', 'withdraw_freeze - ' . $money),
            'withdraw_amount'   => array('exp', 'withdraw_freeze + ' . $money),
        );
        // 获取当前账户信息
        $account = M('UserAccount')->field(true)->where(array('uid'=>$withdraw['uid']))->find();
        $account_log = array(
            'uid'           => $account['uid'],
            'type'          => 10,              // TODO 类型待定
            'affect_money'  => -$money,
            'account_money' => $account['account_amount'],
            'freeze_money'  => $account['withdraw_freeze'] - $money,
            'add_time'      => NOW_TIME,
            'add_ip'        => get_client_ip(),
            'title'         => '用户提现',
            'description'   => '提现成功'       // TODO     具体内容
        );
        try {
            $res = $this->where(array('id'=>$id))->save($data);
            // 扣除冻结金额
            $account = M('UserAccount')->where(array('uid'=>$withdraw['uid']))->save($account_data);
            // 增加用户资金变动记录
            $log = M('UserAccountChange')->add($account_log);

            if($res !== false && $account !== false && $log !== false) {
                $this->commit();
                $this->_auditLog($id, $remark);
                return true;
            } else {
                $this->rollback();
                $this->error('修改失败，请重试');
                return false;
            }

        }catch (\Exception $e) {
            $this->rollback();
            $this->error($e->getMessage());
            return false;
        }
    }

    /**
     * 获取当前提现状态
     * @param int $id 提现ID
     * @return mixed
     */
    private function _withdrawStatus($id)
    {
        $status = $this->where(array('id' => $id))->select();
        return $status;
    }

    /**
     * 提现审核日志
     * @param int       $id       提现ID
     * @param string    $remark   审核意见
     */
    private function _auditLog($id, $remark)
    {
        $data = array(
            'withdraw_id'   => $id,
            'withdraw_statuw'   => 1,
            'audit_id'      => UID,
            'audit_user'    => session('admin.admin_name'),     // TODO
            'remark'        => $remark,
            'add_ip'        => get_client_ip(),
            'add_time'      => NOW_TIME,
        );
        M('UserWithdrawAuditLog')->add($data);
    }
}