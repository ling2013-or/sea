<?php
namespace Home\Controller;

use Common\Library\Pay\Pay;
use Common\Library\Pay\Pay\Param;
use Think\Controller;

class PayController extends Controller
{
    public function notify()
    {
        $config = array(
            // 收款账号邮箱
            'email' => 'wz@scbin.com',
            // 加密key，开通支付宝账户后给予
            'key' => 'rnwn0oocydzzizpbcaut9ey8rxc4lkrw',
            // 合作者ID，支付宝有该配置，开通易宝账户后给予
            'partner' => '2088311603328755'
        );
        $pay = new Pay('alipay', $config);

        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }

        dump($notify);
        dump($pay->verifyNotify($notify));die;
        //验证
        if ($pay->verifyNotify($notify)) {
            //获取订单信息
            $info = $pay->getInfo();
            dump($info);die;
            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0 && $payinfo['callback']) {
                    session("pay_verify", true);
                    $check = R($payinfo['callback'], array('money' => $payinfo['money'], 'param' => unserialize($payinfo['param'])));
                    if ($check !== false) {
                        M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1));
                    }
                }
                if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {
                    $pay->notifySuccess();
                }
            } else {
                $this->error("支付失败！");
            }
        } else {
            E("Access Denied");
        }
    }
}