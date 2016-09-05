<?php

namespace Common\Library\Pay\Pay\Driver;

use Common\Library\Pay\Pay\Driver;
use Common\Library\Pay\Pay\Param;

/**
 * 手机支付接口
 * Class Aliwappay
 * @package Common\Library\Pay\Pay\Driver
 */
class Aliwappay extends Driver
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

    public function buildRequestForm(Param $param)
    {
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "alipay.wap.create.direct.pay.by.user",
            "partner" => trim($this->config['partner']),
            "seller_id" => trim($this->config['partner']),
            "payment_type" => '1',
            "notify_url" => $this->config['notify_url'],
            "return_url" => $this->config['return_url'],
            "out_trade_no" => $param->getOrderNo(),
            "subject" => $param->getTitle(),
            "total_fee" => $param->getFee(),
            "show_url" => '',
            "body" => $param->getBody(),
            "it_b_pay" => '',
            "extern_token" => '',
            "_input_charset" => 'utf-8'
        );

        $parameter = $this->paramFilter($parameter);

        ksort($parameter);
        reset($parameter);

        $arg = '';
        while (list ($key, $val) = each($parameter)) {
            $arg .= $key . '=' . $val . '&';
        }

        $mySign = $this->rsaSign(substr($arg, 0, -1), $this->config['private_key_path']);

        $parameter['sign'] = $mySign;
        $parameter['sign_type'] = 'RSA';

        return $this->buildForm($parameter, $this->gatewayUrl, 'get');
    }

    /**
     * 除去数组中的空值和签名参数
     * @param array $param 签名参数组
     * @return array 去掉空值与签名参数后的新签名参数组
     */
    public function paramFilter($param)
    {
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") continue;
            else    $param_filter[$key] = $param[$key];
        }
        return $param_filter;
    }

    /**
     * RSA签名
     * @param array $data 待签名数据
     * @param string $private_key_path 商户私钥文件路径
     * @return string 签名结果
     */
    public function rsaSign($data, $private_key_path) {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * RSA验签
     * @param array $data 待签名数据
     * @param string $ali_public_key_path 支付宝的公钥文件路径
     * @param string $sign 要校对的的签名结果
     * @return bool 验证结果
     */
    public function rsaVerify($data, $ali_public_key_path, $sign)  {
        $pubKey = file_get_contents($ali_public_key_path);
        $res = openssl_get_publickey($pubKey);
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    /**
     * RSA解密
     * @param string $content 需要解密的内容，密文
     * @param string $private_key_path 商户私钥文件路径
     * @return string 解密后内容，明文
     */
    public function rsaDecrypt($content, $private_key_path) {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        //用base64将内容还原成二进制
        $content = base64_decode($content);
        //把需要解密的内容，按128位拆开解密
        $result  = '';
        for($i = 0; $i < strlen($content)/128; $i++  ) {
            $data = substr($content, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
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

        return $this->rsaVerify(substr($arg, 0, -1), $this->config['ali_public_key_path'], $sign);
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
        $responseTxt = 'true';
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