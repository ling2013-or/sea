<?php
namespace Admin\Controller;

/**
 * 会员提现管理
 * Class WithdrawController
 * @package Admin\Controller
 */
class WithdrawController extends AdminController
{
    /**
     * 会员提现列表
     */
    public function index()
    {
        $where = array();

        // 用户名搜索条件
        if(isset($_GET['username']) && !empty($_GET['username'])) {
            $where['user.user_name'] = array('like', '%' . (string)I('username') . '%');
        }

        // 提现状态搜索条件
        $status_arr = array('0', '1', '2', '3');
        $status = I('status');
        if(in_array($status, $status_arr)) {
            $where['withdraw.status'] = $status;
        } else {
            $where['withdraw.status'] = array('IN', $status_arr);
        }

        // 申请提现开始时间搜索条件
        if(isset($_GET['time-start']) && !empty($_GET['time-start'])) {
            $where['withdraw.add_time'][] = array('EGT', strtotime(I('time-start')));
        }

        // 申请提现结束时间搜索条件
        if(isset($_GET['time-end']) && !empty($_GET['time-end'])) {
            $where['withdraw.add_time'][] = array('ELT', 86400 + strtotime(I('time-end')));
        }

        // TODO 提现金额搜索条件

        // 审核人员I搜索条件
        if(isset($_GET['audit']) && !empty($_GET['audit'])) {
            $where['withdraw.audit_user'] = array('like', '%' . (string)I('audit') . '%');
        }

        $total = M('UserWithdraw')
            ->alias('withdraw')
            ->join('__USER__ AS user ON user.uid = withdraw.uid', 'LEFT')
            ->where($where)
            ->count();

        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('UserWithdraw')
            ->alias('withdraw')
            ->join('__USER__ AS user ON user.uid = withdraw.uid', 'LEFT')
            ->field('withdraw.*,user.user_name,user.nick_name,user.real_name')
            ->where($where)
            ->limit($limit)
            ->order('id DESC')
            ->select();
        $this->meta_title = '提现管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 提现状态处理
     * TODO
     */
    public function edit()
    {
        $id = I('id', 0, 'intval');
        if(empty($id)) {
            $this->error('请选择要处理的提现');
        }
        if(IS_POST) {

        } else {
            $withdraw = M('UserWithdraw')->field('status')->where(array('id'=>$id))->find();
            if(!$withdraw) {
                $this->error('提现信息不存在');
            }
            if($withdraw['status'] !=0 && $withdraw['status'] != 1) {
                $this->error('操作非法');
            }
        }
    }
}