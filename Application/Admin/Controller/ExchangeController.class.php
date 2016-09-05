<?php
namespace Admin\Controller;

class ExchangeController extends AdminController
{
    /**
     * 交换列表
     */
    public function index()
    {
        // TODO 查询条件
        $map = array();

        $total = M('ObjectExchange')
            ->alias('object')
            ->join('__USER__ AS initiator ON initiator.uid = object.initiator_id', 'LEFT')
            ->join('__USER__ AS receive ON receive.uid = object.receive_id', 'LEFT')
            ->where($map)
            ->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('ObjectExchange')
            ->alias('object')
            ->join('__USER__ AS initiator ON initiator.uid = object.initiator_id', 'LEFT')
            ->join('__USER__ AS receive ON receive.uid = object.receive_id', 'LEFT')
            ->field('object.*,initiator.user_name AS initiator_name,receive.user_name AS receive_name')
            ->where($map)
            ->limit($limit)->order('id desc')->select();
        $this->meta_title = '物物交换管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        trace($lists);
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 获取交换详情
     */
    public function detail()
    {
        $id = I('id', 0, 'intval');
        if(empty($id)) {
            $this->error('请选择要查看的交易');
        }

        $info = M('ObjectExchange')
            ->alias('object')
            ->join('__USER__ AS initiator ON initiator.uid = object.initiator_id', 'LEFT')
            ->join('__USER__ AS receive ON receive.uid = object.receive_id', 'LEFT')
            ->field('object.*,initiator.user_name AS initiator_name,receive.user_name AS receive_name')
            ->where(array('object.id'=>$id))
            ->find();
        if(!$info) {
            $this->error('请选择要查看的交易');
        }

        // 处理显示
        $info['initiator_status_txt'] = $info['initiator_status'] == 1 ? '已同意' : '待处理';
        $info['receive_status_txt'] = $info['receive_status'] == 1 ? '已同意' : '待处理';
        $info['status_txt'] = get_object_exchange_status($info['status']);

        $lists = M('ObjectExchangeData')
            ->alias('object')
            ->join('__USER__ AS user ON user.uid = object.user_id', 'LEFT')
            ->join('__PLAN_SELL__ AS plan ON plan.plan_id = object.plan_id', 'LEFT')
            ->field('user.user_name,object.*,plan.plan_sn,plan.plan_name')
            ->where(array('object.exchange_id'=>$id))
            ->select();

        $initiator = $receive = array();
        if($lists) {
            foreach($lists as $val) {
                if($val['is_initiator'] == 1) {
                    $initiator[] = $val;
                } else {
                    $receive[] = $val;
                }
            }
        }

        $this->meta_title = '交易详情';

        $this->assign('info', $info);
        $this->assign('initiator', $initiator);
        $this->assign('receive', $receive);
        $this->display();
    }
}