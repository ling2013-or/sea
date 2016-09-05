<?php
namespace Admin\Controller;

/**
 * 充值管理
 * Class ChargeController
 * @package Admin\Controller
 */
class ChargeController extends AdminController
{

    /**
     * 充值列表
     */
    public function index()
    {
        $map = array();

        /* 时间段查询 */
        if(isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if($start_unixtime) {
                $map['add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if(isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $map['add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        // 用户名查询
        if(isset($_GET['username']) && !empty($_GET['username'])) {
            $map['user_name'] = array('like', '%' . (string)I('username') . '%');
        }

        if(isset($_GET['state']) && $_GET['state'] != '') {
            $map['payment_state'] = $_GET['state'];
        }

        $total = M('UserCharge')->where($map)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('UserCharge')->field(true)->where($map)->limit($limit)->order('id DESC')->select();
        $this->meta_title = '充值管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 充值详情
     */
    public function info()
    {
        $id = I('id', 0, 'intval');
        if(empty($id)) {
            $this->error('请选择要查看的充值记录');
        }

        $info = M('UserCharge')->field(true)->where(array('id'=>$id))->find();
        if(!$info) {
            $this->error('充值记录不存在');
        }
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '充值详情';
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 更改充值状态
     */
    public function edit()
    {
        $id = I('id', 0, 'intval');
        if(empty($id)) {
            $this->error('请选择要编辑的充值记录');
        }

        $Model = M('UserCharge');
        $map = array();
        $map['id'] = $id;
        $map['payment_state'] = 0;
        $info = $Model->field(true)->where($map)->find();
        if(!$info) {
            $this->error('充值记录不存在');
        }

        if(IS_POST) {
            $paytime = I('paytime');
            if(!$paytime) {
                $this->error('支付时间不能为空');
            }

            //获取支付方式信息
            $code = I('payment');
            if(!$code || $code == 'balance') {
                $this->error('系统不支持此支付类型');
            }
            $payment = M('Payment')->field(true)->where(array('code'=>$code))->find();
            if(!$payment) {
                $this->error('系统不支持此支付类型');
            }

            $trade_sn = I('trade_sn');
            if(!$trade_sn) {
                $this->error('第三方支付平台交易号不能为空');
            }

            try {
                $Model->startTrans();
                // 更改充值状态
                $data = array(
                    'payment_state' => 1,
                    'payment_time'  => strtotime($paytime),
                    'payment_name'  => $payment['name'],
                    'trade_sn'      => $trade_sn,
                    'admin'         => session('admin.admin_name'),
                );
                $state = $Model->where(array('id'=>$id))->save($data);
                if(!$state) {
                    throw new \Exception('修改支付状态失败');
                }
                $user_name = M('User')->where(array('uid'=>$info['user_id']))->getField('user_name');
                $data = array(
                    'uid'           => $info['user_id'],
                    'user_name'     => $user_name,
                    'affect_money'  => $info['charge_amount'],
                    '$trade_sn'     => $trade_sn,
                );
                $UserAccountModel = D('UserAccount');
                $res = $UserAccountModel->changeAccount('recharge', $data);
                if(!$res) {
                    throw new \Exception($UserAccountModel->getError());
                }

                $Model->commit();
                $this->success('修改支付状态成功', Cookie('__forward__'));

            } catch(\Exception $e) {
                $Model->rollback();
                $this->error($e->getMessage());
            }
        } else {
            // 获取支付充值接口
            $payment = M('Payment')->field(true)->select();
            foreach($payment as $key => $val) {
                if($val['code'] == 'balance') {
                    unset($payment[$key]);
                }
            }

            $this->meta_title = '编辑充值';
            $this->assign('info', $info);
            $this->assign('payment', $payment);
            $this->display();
        }
    }
}