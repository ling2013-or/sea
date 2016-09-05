<?php
namespace Admin\Controller;

/**
 * 订单管理
 * Class SellorderController
 * @package Admin\Controller
 */
class SellorderController extends AdminController
{
    /**
     * 订单列表
     *
     * 后台可见所有订单(包括删除)
     */
    public function index()
    {
        //查询条件
        $map = array();

        //查询条件
        if (isset($_GET['kw']) && isset($_GET['vw']) && $_GET['vw'] !== '') {
            switch (I('kw')) {
                case 1:
                    //查询方案名称对应的ID
                    $map['summary.payment_sn'] = I('vw');
                    break;
                case 2:
                    //查询作物名称对应的ID
                    $map['user.user_name'] = array('like','%'. I('vw') .'%');
                    break;
                default:
                    break;
            }
        }

        /* 时间段查询 */
        if (isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if ($start_unixtime) {
                $map['summary.add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if (isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $map['summary.add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('SellOrderSummary')->alias('summary')
            ->join('__USER__ user ON user.uid=summary.user_id', 'LEFT')
            ->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //查询总单
        $res = M('SellOrderSummary')->alias('summary')
            ->join('__USER__ user ON user.uid=summary.user_id', 'LEFT')
            ->field('summary.*,user.user_name,user.is_platform')
            ->where($map)->limit($limit)->order('summary.payment_id DESC')->select();

        if ($res) {
            //获取所有订单的汇总ID
            $ids = array();
            foreach ($res as $v) {
                $ids[] = $v['payment_id'];
            }

            //查询所有分单
            $condition['orders.payment_id'] = array('IN', $ids);
            $order_all = M('SellOrder')->alias('orders')
                ->field('storage.storage_name,plan.plan_name,orders.order_id,orders.payment_id,orders.storage_id,orders.plan_id,
				orders.order_area,orders.plan_price,orders.order_price,orders.storage_price,orders.order_sn,orders.pay_total,orders.status')
                ->join('__PLAN_STORAGE__ storage ON orders.storage_id=storage.storage_id', 'LEFT')
                ->join('__PLAN_SELL__ plan ON orders.plan_id=plan.plan_id', 'LEFT')
                ->where($condition)->select();

            //获取订单列表
            foreach ($res as $key => $val) {
                foreach ($order_all as $v) {
                    if ($val['payment_id'] == $v['payment_id']) {
                        $res[$key]['extend_sell_order'][] = $v;
                    } else {
                        continue;
                    }
                }
            }
        }

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '农作物订单列表';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $res);
        $this->display();
    }

    /**
     * 用户收益列表
     */
    public function income()
    {
        //查询条件
        $map = array();

        $id = I('id', 0, 'intval');
        if ($id) {
            $map['order_id'] = $id;
        }

        //查询条件
        if (isset($_GET['kw']) && isset($_GET['vw']) && $_GET['vw'] !== '') {
            switch (I('kw')) {
                case 1:
                    //查询方案名称对应的ID
                    $map['s.order_sn'] = I('vw');
                    break;
                case 2:
                    //查询作物名称对应的ID
                    $map['u.user_name'] = array('like','%'. I('vw') .'%');
                    break;
                default:
                    break;
            }
        }

        /* 时间段查询 */
        if (isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if ($start_unixtime) {
                $map['s.add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if (isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $map['s.add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('SellOrder')->alias('s')
            ->join('__PLAN_SELL__ plan ON plan.plan_id=s.plan_id', 'LEFT')
            ->join("__USER__ u ON s.user_id=u.uid", 'LEFT')
            ->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('SellOrder')->alias('s')
            ->field('s.*,u.is_platform,u.uid uid,u.user_name uname,plan.plan_name,s.order_area * s.order_income_set AS order_income_should')
            ->join('__PLAN_SELL__ plan ON plan.plan_id=s.plan_id', 'LEFT')
            ->join('__USER__ u ON s.user_id=u.uid', 'LEFT')
            ->where($map)->limit($limit)->order('s.add_time')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '销售方案列表';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 设置方案收益
     */
    public function docomplete()
    {
        $id = I('id', 0, 'intval');
        $income = I('income', 0, 'floatval');
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        //更新income_real
        $data['order_id'] = $id;
        $data['order_income_set'] = $income;
        if (M('SellOrder')->save($data)) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }
}