<?php
namespace Admin\Controller;

/**
 * 商品订单管理
 * Class OrderController
 * @package Admin\Controller
 */
class OrderController extends AdminController
{
    /**
     * 订单列表
     */
    public function index()
    {
        $map = array();

        $Model = D('Order');
        $order_sn = I('order_sn', '', 'trim');
        if ($order_sn) {
            $map['order_sn'] = $order_sn;
        }

        $is_platform = I('is_platform');
        if (in_array($is_platform, array('real', 'platform'))) {
            $map['is_platform'] = $is_platform == 'real' ? 0 : 1;
        }

        $buyer_name = I('buyer_name', '', 'trim');
        if ($buyer_name) {
            $map['buyer_name'] = $buyer_name;
        }

        $allow_state_array = array('state_new', 'state_pay', 'state_send', 'state_success', 'state_cancel');
        if (isset($_GET['state_type']) && in_array($_GET['state_type'], $allow_state_array)) {
            $map['order_status'] = str_replace($allow_state_array, array(0, 2, 3, 4, 1), $_GET['state_type']);
        } else {
            $_GET['state_type'] = 'state_order';
        }

        /* 时间段查询 */
        if (isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if ($start_unixtime) {
                $map['add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if (isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $map['add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        $total = $Model->where($map)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $lists = $Model->getOrderList($map, true, 'order_id DESC', $limit, array('order_goods', 'order_camera', 'member'));
        //页面中显示操作 (设为已支付、取消订单、设为待评价、订单已完成)
        foreach ($lists as $key => $order_info) {

            //显示取消订单 1 0
            $lists[$key]['if_cancel'] = $Model->getOrderOperateState('cancel', $order_info);

            //显示已支付 2 3 4 5 6 7
            $lists[$key]['if_pay'] = $Model->getOrderOperateState('pay_yes', $order_info);

            //显示养殖中 3
            $lists[$key]['if_breed'] = $Model->getOrderOperateState('breed', $order_info);

            //显示待评价 4
            $lists[$key]['if_rate'] = $Model->getOrderOperateState('rate', $order_info);

            //显示已完成 5 6 7
            $lists[$key]['if_over'] = $Model->getOrderOperateState('over', $order_info);

            //养殖完成
            $lists[$key]['if_breed_over'] = $Model->getOrderOperateState('breed_over', $order_info);

            //TODO 退款中

        }
//        dump($lists);die;
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '订单列表';
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->assign('state_type', $_GET['state_type']);
        $this->display();
    }

    /**
     * 平台获取订单详情
     */
    public function detail()
    {
        $order_id = I('order_id', 0, 'intval');
        if (empty($order_id)) {
            $this->error('请选择要查看的订单详情');
        }

        $Model = D('Order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $Model->getOrderInfo($condition, array('order_camera', 'order_goods', 'member'));
        if (empty($order_info)) {
            $this->error('订单不存在');
        }

        // 订单处理历史
        $log_list = $Model->getOrderLogList(array('order_id' => $order_id));

        // TODO 退款退货信息

        // TODO 退款信息
        $this->meta_title = '订单详情';
        //允许转移分区操作的订单状态
        $order = '2,3';
        $this->assign('order', $order);
        $this->assign('info', $order_info);
        $this->assign('log', $log_list);

        $this->display();
    }

    /**
     * 打印发货单
     */
    public function oprint()
    {
        $order_id = I('order_id', 0, 'intval');
        if (empty($order_id)) {
            $this->error('请选择要打印的订单');
        }
        $Model = D('Order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $Model->getOrderInfo($condition, array('order_common', 'order_goods'));
        if (empty($order_info)) {
            $this->error('订单不存在');
        }

        // TODO 卖家信息

        // 订单商品
        $goods_new_list = array();
        $goods_all_num = 0;
        $goods_total_price = 0;
        if (!empty($order_info['extend_order_goods'])) {
            $i = 1;
            foreach ($order_info['extend_order_goods'] as $k => $v) {
                $v['goods_name'] = msubstr($v['goods_name'], 0, 100);
                $goods_all_num += $v['goods_num'];
                $v['goods_all_price'] = format_money($v['goods_num'] * $v['goods_price']);
                $goods_total_price += $v['goods_all_price'];
                $goods_new_list[ceil($i / 4)][$i] = $v;
                $i++;
            }
        }
        //优惠金额
        $promotion_amount = $goods_total_price - $order_info['goods_amount'];

        $this->assign('order_info', $order_info);
        $this->assign('promotion_amount', $promotion_amount);
        $this->assign('goods_all_num', $goods_all_num);
        $this->assign('goods_total_price', format_money($goods_total_price));
        $this->assign('goods_list', $goods_new_list);

        $this->display();
    }

    /**
     * 取消订单
     */
    public function cancel()
    {
        $order_id = I('order_id', 0, 'intval');
        if (empty($order_id)) {
            $this->error('请选择要取消的订单');
        }
        $Model = D('Order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $Model->getOrderInfo($condition);

        if (IS_POST) {
            $state_info = I('state_info', '', 'trim,htmlspecialchars');
            $state_info1 = I('state_info1', '', 'trim,htmlspecialchars');
            if (empty($state_info) && empty($state_info1)) {
                $this->error('请选择取消订单原因');
            }

            $Model->startTrans();   // 开启事务
            try {
                // 获取订单操作状态
                $if_allow = $Model->getOrderOperateState('cancel', $order_info);
                if (!$if_allow) {
                    throw new \Exception('无效的请求');
                }
                $goods_list = $Model->getOrderGoodsList(array('order_id' => $order_id));
                $GoodsModel = M('Goods');
                if (is_array($goods_list) and !empty($goods_list)) {
                    foreach ($goods_list as $goods) {
                        $data = array();
                        $data['stock'] = array('exp', 'stock + ' . $goods['goods_num']);
                        $data['real_sales'] = array('exp', 'real_sales - ' . $goods['goods_num']);
                        $update = $GoodsModel->where(array('id' => $goods['id']))->save($data);
                        if (false === $update) {
                            throw new \Exception('修改订单状态失败');
                        }
                    }
                }

                //更新订单信息
                $data = array('order_status' => 1);
                $update = $Model->where(array('order_id' => $order_id))->save($data);
                if (!$update) {
                    throw new \Exception('修改订单状态失败');
                }

                //记录订单日志
                $data = array();
                $data['order_id'] = $order_id;
                $data['operate_role'] = 'seller';
                $data['operate_user'] = session('admin.admin_name');
                $data['operate_msg'] = '取消订单';
                $extend_msg = $state_info1 != '' ? $state_info1 : $state_info;
                if ($extend_msg) {
                    $data['operate_msg'] .= ' ( ' . $extend_msg . ' )';
                }
                $data['order_state'] = 1;
                $log = $Model->addOrderLog($data);
                if (!$log) {
                    throw new \Exception('添加操作日志失败');
                }
                $Model->commit();
                $this->success('取消订单成功', Cookie('__forward__'));
            } catch (\Exception $e) {
                $Model->rollback();
                $this->error($e->getMessage());
            }
        } else {
            $this->meta_title = '取消订单';
            $this->assign('order_info', $order_info);
            $this->display();
        }
    }

    /**
     * 确认订单支付
     */
    public function order_pay()
    {
        $order_id = I('order_id', 0, 'intval');
        if (empty($order_id)) {
            $this->error('请选择要修改的订单');
        }
        $Model = D('Order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $Model->getOrderInfo($condition);
//        dump($order_info);die;
        if (IS_POST) {
            $state_info = I('state_info', '', 'trim,htmlspecialchars');
            $state_info1 = I('state_info1', '', 'trim,htmlspecialchars');
            if (empty($state_info) && empty($state_info1)) {
                $this->error('请选择修改订单原因');
            }

            $Model->startTrans();   // 开启事务
            try {
                // 获取订单操作状态
                $if_allow = $Model->getOrderOperateState('pay_yes', $order_info);
                if (!$if_allow) {
                    throw new \Exception('无效的请求');
                }

                //更新订单信息
                $data = array('order_status' => 2, 'pay_time' => NOW_TIME, 'pay_money' => $order_info['order_price'], 'pay_type' => 2);
                $update = $Model->where(array('order_id' => $order_id))->save($data);
                if (!$update) {
                    throw new \Exception('修改订单状态失败');
                }

                //记录订单日志
                $data = array();
                $data['order_id'] = $order_id;
                $data['operate_role'] = 'seller';
                $data['operate_user'] = session('admin.admin_name');
                $data['operate_msg'] = '将订单状态修改为已支付';
                $extend_msg = $state_info1 != '' ? $state_info1 : $state_info;
                if ($extend_msg) {
                    $data['operate_msg'] .= ' ( ' . $extend_msg . ' )';
                }
                $data['order_state'] = 2;

                $log = $Model->addOrderLog($data);
                if (!$log) {
                    throw new \Exception('添加操作日志失败');
                }
                $Model->commit();
                $this->success('修改订单成功', Cookie('__forward__'));
            } catch (\Exception $e) {
                $Model->rollback();
                $this->error($e->getMessage());
            }
        } else {
            $this->meta_title = '修改订单状态';
            $this->assign('order_info', $order_info);
            $this->display();
        }
    }

    /**
     * 订单养殖处理逻辑
     */
    public function breed($id = null)
    {
        //订单号码
        if (empty($id)) {
            $id = I('order_id');
        }
        if (empty($id)) $this->error('请选择您要操作的订单');
        $where['order_id'] = $id;
        //实例化order model类
        $model = D('Order');
        //修改订单状态
        $data = array();
        $data['order_status'] = 3;
        $data['breed_time'] = NOW_TIME;
        $result = $model->where($where)->save($data);
        if ($result) {
            $this->success('修改成功');
        } else {
            $this->error('更新失败');
        }
    }

    /**
     * 订单养殖处理逻辑
     */
    public function over_will($id = null)
    {
        //订单号码
        if (empty($id)) {
            $id = I('order_id', '', 'trim');
        }
        if (empty($id)) $this->error('请选择您要操作的订单');
        $where['order_id'] = $id;
        //实例化order model类
        $model = D('Order');
        //修改订单状态
        $data = array();
        $data['order_status'] = 4;
        $data['breed_time'] = NOW_TIME;
        $result = $model->where($where)->save($data);
        if ($result) {
            $this->success('修改成功');
        } else {
            $this->error('更新失败');
        }
    }

    /**
     * 编辑收货地址
     * 数据表 table __ORDER_COMMON__
     */
    public function editAddress()
    {
        if (IS_POST) {
            //订单编号
            $order_id = I('post.order_id');
            //收货人名称
            $name = I('reciver_name');
            //收货人的手机号码
            $phone = I('post.phone');
            //收货地址
            $address = I('post.address');
            $data['reciver_info'] = serialize(array('address' => $address, 'phone' => $phone));
            $data['reciver_name'] = $name;
            //修改数据库信息
            $common = M('OrderCommon');
            //判断条件
            $where['order_id'] = $order_id;
            $result = $common->where($where)->save($data);
            if ($result !== false) {
                //组织收货地址
                $msg = $name . '　' . $phone . '　' . $address;
                $this->success($msg);
            } else {
                $this->error($common->getError());
            }
        }
    }

    /**
     * 订单中选择发货地址
     */
    public function editDaddress()
    {
        if (IS_POST) {
            //订单号码
            $order_id = I('post.order_id');
            //发货地址单号
            $daddress_id = I('post.daddress_id');
            $where['order_id'] = array('eq', $order_id);
            $map['id'] = array('eq', $order_id);
            $data['daddress_id'] = $daddress_id;
            //更新订单公用表中的发货地址ID
            $com = M('OrderCommon');
            $result = $com->where($where)->save($data);
            if ($result !== false) {
                //获取当前选中的发货地址
                $data = M('Daddress')->field('seller_name,telphone,address')->where($map)->find();

                $message = $data['seller_name'] . '　' . $data['telphone'] . '　' . $data['address'];
                $this->success($message);
            } else {
                $this->error($com->getError());
            }
        } else {
            $this->error('非法请求！', U('send'));
        }
    }


    /**
     *  修改叮当中的物流信息（roderCommon  order）
     */
    public function editLogistics()
    {
        if (IS_POST) {
            //订单编号
            $order_id = I('post.order_id');
            //shipping_express_id 配送的物流公司ID
            $shipping_express_id = I('shipping_express_id');
            //物流单号
            $shipping_code = I('shipping_code');
            //物流单号
            $where['order_id'] = $order_id;
            $data['shipping_express_id'] = $shipping_express_id;
            $data['shipping_time'] = NOW_TIME;

            //修改数据库信息
            $common = M('OrderCommon');
            //判断条件
            $where['order_id'] = array('eq', $order_id);
            $result = $common->where($where)->save($data);
            //修改订单表中的物流单号信息
            $value['shipping_code'] = $shipping_code;
            //设置订单为已发货状态
            $value['order_state'] = 3;
            $order = M('order');
            $res = $order->where($where)->save($value);
            if (($result !== false) && ($res !== false)) {
                //组织收货地址
                $msg = '编辑成功';
                $this->success($msg, U('index'));
            } else {
                $this->error($common->getError());
            }
        }
    }

    //转移分区
    public function transfer()
    {
        //获取当前产品的id，订单状态
        $order_id = I('oid', '', 'intval');
        $gid = I('gid', '', 'intval');
        $goods_id = I('goods_id', '', 'intval');
        $zone_id = I('zone_id', '', 'intval');
        if (empty($order_id) || empty($gid) || empty($gid)) $this->error('请选择您要专一的产品');
            //检测订单状态是否是已付款，或者养殖中
            $status = M('Order')->where(array('id' => $order_id))->getField('order_status');
        if (!in_array($status, array(2, 3))) {
            $this->error('不满足转移条件', U('index'));
        }
        //检测当前订单下产品信息
        $map['id'] = $gid;
        $map['goods_id'] = $goods_id;
        $map['zone_id'] = $zone_id;
        $info = M('OrderGoods')->field('*')->where($map)->find();
        if(!$info){
            $this->error('数据丢失，请重新选择',U('index'));
        }



    }

}