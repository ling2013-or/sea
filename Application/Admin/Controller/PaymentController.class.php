<?php
namespace Admin\Controller;

/**
 * 支付方式管理
 * tips: 此处为系统支持的几种方式
 * Class PaymentController
 * @package Admin\Controller
 */
class PaymentController extends AdminController
{
    /**
     * 支付方式列表
     */
    public function index()
    {
        $lists = M('Payment')->field(true)->select();
        $this->meta_title = '支付方式管理';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 编辑支付方式
     */
    public function edit()
    {
        if(IS_POST) {
            $id = I('id', 0, 'intval');
            $data = array();
            $data['status'] = I('status', 0, 'intval');

            $config_array = explode(',', I('config_name'));
            if(is_array($config_array) && !empty($config_array)) {
                $config_info = array();
                foreach($config_array as $val) {
                    $config_info[$val] = I($val, '', 'trim');
                }
                $data['config'] = serialize($config_info);
            }
            $res = M('Payment')->where(array('id'=>$id))->save($data);
            if(false === $res) {
                $this->error('修改支付方式失败');
            } else {
                $this->success('修改支付方式成功', Cookie('__forward__'));
            }
        } else {
            $id = I('id', 0, 'intval');
            $payment = M('Payment')->where(array('id'=>$id))->find();
            if(!$payment) {
                $this->error('此支付方式不存在');
            }

            if($payment['config'] != '') {
                $payment['config'] = unserialize($payment['config']);
            }
            $this->meta_title = '支付方式管理';
            $this->assign('payment', $payment);
            $this->display();
        }
    }
}