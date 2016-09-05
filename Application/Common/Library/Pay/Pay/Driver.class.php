<?php

namespace Common\Library\Pay\Pay;

use Common\Library\Pay\Pay\Param;

/**
 * 支付驱动接口
 * Class Driver
 * @package Common\Library\Pay\Pay
 */
abstract class Driver
{

    /**
     * 配置参数
     * @var array
     */
    protected $config;

    /**
     * 订单详情
     * @var array
     */
    protected $info;

    /**
     * 构造方法，配置支付信息
     * @param array $config 支付配置
     */
    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 验证通过后获取订单信息
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * 创建HTML表单
     * @param array $params 表单参数
     * @param string $gateway 表单提交地址
     * @param string $method 表单提交方式
     * @param string $charset 表单编码
     * @return string
     */
    public function buildForm($params, $gateway, $method = 'post', $charset = 'utf-8')
    {
        header('Content-type:text/html;charset= ' . $charset);

        $html = '<form id="paysubmit" name="paysubmit" action="' . $gateway . '" method="' . $method . '">';

        foreach ($params as $name => $value) {
            $html .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />' . PHP_EOL;
        }

        $html = $html . '</form>Loading......';

        $html = $html . "<script>document.forms['paysubmit'].submit();</script>";

        return $html;
    }

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param string $method 请求方法GET/POST
     * @param array $header 配置请求头部信息
     * @param bool $multi 判断是否传输文件
     * @throws \Exception
     * @return array 响应数据
     */
    public function http($url, $params = array(), $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header,
        );

        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            throw new \Exception('请求发生错误：' . $error);
        }
        return $data;
    }

    /**
     * 抽象方法，在支付接口中实现
     * 检测参数是否合法
     */
    abstract protected function checkConfig();

    /**
     * 抽象方法，生成请求表单
     * @param \Common\Library\Pay\Pay\Param $param 请求参数
     * @return mixed
     */
    abstract protected function buildRequestForm(Param $param);
}