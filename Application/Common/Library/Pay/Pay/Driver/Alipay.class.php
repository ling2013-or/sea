<?php

namespace Common\Library\Pay\Pay\Driver;

use Common\Library\Pay\Pay\Driver;
use Common\Library\Pay\Pay\Param;

/**
 * 支付宝即时到帐
 * Class Alipay
 * @package Common\Library\Pay\Pay
 */
class Alipay extends Driver
{

    /**
     * 支付宝网关地址
     * @var string
     */
    protected $gatewayUrl = 'https://mapi.alipay.com/gateway.do';

    /**
     * 支付宝通知地址
     * @var string
     */
    protected $notifyUrl = 'http://notify.alipay.com/trade/notify_query.do';

    /**
     * 支付宝配置参数
     * @var array
     */
    protected $config = array('email' => '', 'key' => '', 'partner' => '');

    /**
     * 检测参数是否合法
     * @throws \Exception
     */
    public function checkConfig()
    {
        if (!$this->config['email'] || !$this->config['key'] || !$this->config['partner']) {
            throw new \Exception('支付宝设置有误！');
        }
        return true;
    }

    /**
     * 生成请求表单
     * @param \Common\Library\Pay\Pay\Param $param
     * @return string
     */
    public function buildRequestForm(Param $param)
    {
        $params = array(
            'service' => 'create_direct_pay_by_user',
            'payment_type' => '1',
            '_input_charset' => 'utf-8',
            'seller_email' => $this->config['email'],
            'partner' => $this->config['partner'],
            'notify_url' => $this->config['notify_url'],
            'return_url' => $this->config['return_url'],
            'out_trade_no' => $param->getOrderNo(),
            'subject' => $param->getTitle(),
            'body' => $param->getBody(),
            'total_fee' => $param->getFee()
        );

        ksort($params);
        reset($params);

        $arg = '';
        while (list ($key, $val) = each($params)) {
            $arg .= $key . '=' . $val . '&';
        }

        $params['sign_type'] = 'MD5';

        $params['sign'] = md5(substr($arg, 0, -1) . $this->config['key']);

        return $this->buildForm($params, $this->gatewayUrl, 'get');
    }

    /**
     * 获取返回时的签名验证结果
     * @param array $param 参数
     * @param string $sign 签名结果
     * @return bool
     */
    protected function getSignVerify($param, $sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        ksort($param_filter);
        reset($param_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $arg = "";
        while (list ($key, $val) = each($param_filter)) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, -1);

        $arg = $arg . $this->config['key'];
        $mySign = md5($arg);

        if ($mySign == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @param array $notify
     * @return bool
     */
    public function verifyNotify($notify)
    {
        //生成签名结果
        $isSign = $this->getSignVerify($notify, $notify["sign"]);
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'false';
        if (!empty($notify["notify_id"])) {
            $responseTxt = $this->getResponse($notify["notify_id"]);
        }

        if (preg_match("/true$/i", $responseTxt) && $isSign) {
            $this->setInfo($notify);
            return true;
        } else {
            return false;
        }
    }

    protected function setInfo($notify)
    {
        $info = array();
        //支付状态
        $info['status'] = ($notify['trade_status'] == 'TRADE_FINISHED' || $notify['trade_status'] == 'TRADE_SUCCESS') ? true : false;
        $info['money'] = $notify['total_fee'];
        $info['out_trade_no'] = $notify['out_trade_no'];
        $this->info = $info;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param string $notify_id 通知校验ID
     * @return mixed   invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     *                  true 返回正确信息
     *                  false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse($notify_id)
    {
        $partner = $this->config['partner'];
        $verify_url = $this->notifyUrl . '?partner=' . $partner . '&notify_id=' . $notify_id;
        return $this->http($verify_url);
    }
}