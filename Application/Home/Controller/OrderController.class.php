<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/24
 * Time: 10:55
 */

namespace Home\Controller;


class OrderController extends HomeController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->isLogin();
    }

    public function address()
    {
        //获取当前的支付方式
//        $pays = $this->payList();
        //获取当前用户的收货地址
        $map = array();
        $map['uid'] = session('id');
        $map['status'] = 0;
        if (IS_POST && I('more') == 1) {
            $address = M('UserAddress')->field('*')->where($map)->order('is_default DESC')->select();
        } else {
            $address = M('UserAddress')->field('*')->where($map)->order('is_default DESC')->limit(5)->select();
        }
        $this->meta_title = "订单补全";
        $this->assign('goods', session('cart'));
        $this->assign('address', $address);
//        $this->assign('payList', $pays);
        $this->display();
    }

    /**
     * 获取支付方式信息列表
     * @return mixed
     */
    protected function payList()
    {
        $return = S('PAY_CONFIG_DATA');
        if (!$return) {
            $condition = array();
            $condition['status'] = 1;
            $lists = M('Payment')->field('id,code,name')->where($condition)->select();
            S('PAY_CONFIG_DATA', $lists);
            $return = $lists;
        }
        return $return;
    }

    /**
     * 创建订单
     */
    public function orderCreate()
    {
        if (IS_AJAX) {
            //获取当前用户的收货地址ID
            $post = I('post.');
//            $post = I('get.');
            $address_id = $post['address_id'];
            //创建订单信息
            $model = D('Order');
            try {
                $model->startTrans();
                $order = $model->createOrder($address_id);
                if (!$order) {
                    throw new \Exception($model->getError());
                }
                $model->commit();
                $this->redirect('order/pay', array('order_id' => $order));
            } catch (\Exception $e) {
                $model->rollback();
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * 发起支付信息
     */
    public function pay()
    {
        $data = I('get.');
        $order = '';
        //获取支付方式列表$payList
        $payList = $this->payList();
        //获取订单信息
        if (isset($data['order_id']) && !empty($data['order_id'])) {
            $map = array();
            $map['order_id'] = $data['order_id'];
            $map['order_sn'] = $data['order_id'];
            $map['_logic'] = 'or';
            $order = D('Order')->getOrderInfo($map);
        }
        $this->assign('order', $order);
        $this->assign('payList', $payList);
        $this->display();

    }
} 