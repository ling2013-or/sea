<?php

namespace Common\Library\Pay\Pay\Driver;

use Common\Library\Pay\Pay\Driver;
use Common\Library\Pay\Pay\Param;

class Tenpay extends Driver
{
    /**
     * 财付通网关地址
     * @var string
     */
    protected $gatewayUrl = 'https://gw.tenpay.com/gateway/pay.htm';

    /**
     * 财付通通知地址
     * @var string
     */
    protected $notifyUrl = 'https://gw.tenpay.com/gateway/simpleverifynotifyid.xml';

    /**
     * 财付通配置参数
     * @var array
     */
    protected $config = array('key' => '', 'partner' => '');

    /**
     * 检测参数是否合法
     * @throws \Exception
     */
    public function checkConfig()
    {
        if (!$this->config['key'] || !$this->config['partner']) {
            throw new \Exception('财付通设置有误！');
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
        $paramer = array(
            'input_charset' => "UTF-8",
            'body' => $param->getBody(),
            'subject' => $param->getTitle(),
            'return_url' => $this->config['return_url'],
            'notify_url' => $this->config['notify_url'],
            'partner' => $this->config['partner'],
            'out_trade_no' => $param->getOrderNo(),
            'total_fee' => $param->getFee() * 100,
            'spbill_create_ip' => get_client_ip()
        );

        $paramer['sign'] = $this->createSign($paramer);

        return $this->buildForm($paramer, $this->gatewayUrl);
    }

    /**
     * 创建签名
     * @param array $params 参数
     * @return string
     */
    protected function createSign($params)
    {
        ksort($params);
        reset($params);

        $arg = '';
        foreach ($params as $key => $value) {
            $arg .= "{$key}={$value}&";
        }
        return strtoupper(md5($arg . 'key=' . $this->config['key']));
    }

    /**
     * 获取返回时的签名验证结果
     * @param array $param 通知返回来的参数数组
     * @param string $sign 返回的签名结果
     * @return bool 签名验证结果
     */
    protected function getSignVerify($param, $sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $val == "") {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }

        $mySign = $this->createSign($param_filter);

        if ($mySign == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 针对notify_url验证消息是否是财付通发出的合法消息
     * @param array $notify
     * @return bool
     */
    public function verifyNotify($notify)
    {
        //生成签名结果
        $isSign = $this->getSignVerify($notify, $notify["sign"]);
        $response = false;
        if (!empty($notify["notify_id"])) {
            $response = $this->getResponse($notify["notify_id"]);
        }
        if ($response && $isSign) {
            $this->setInfo($notify);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 订单详情
     * @param array $notify 订单详情
     */
    protected function setInfo($notify)
    {
        $info = array();
        //支付状态
        $info['status'] = $notify['trade_state'] == 0 ? true : false;
        $info['money'] = $notify['total_fee'] / 100;
        $info['out_trade_no'] = $notify['out_trade_no'];
        $this->info = $info;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param string $notify_id 通知校验ID
     * @return int 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    protected function getResponse($notify_id)
    {
        $partner = $this->config['partner'];
        $params = array(
            'input_charset' => 'UTF-8',
            'partner' => $partner,
            'notify_id' => $notify_id
        );
        $sign = $this->createSign($params);
        $verify_url = $this->notifyUrl . '?input_charset=UTF-8&sign=' . $sign . '&partner=' . $partner . '&notify_id=' . $notify_id;
        $responseTxt = $this->http($verify_url);

        $responseTxt = simplexml_load_string($responseTxt);
        return (int)$responseTxt->retcode == 0;
    }
}