<?php

namespace Common\Library\Pay;

use Common\Library\Pay\Pay\Param;

    /**
     * 支付管理
     */
//    数据库
//    CREATE TABLE `think_pay` (
//    `out_trade_no` varchar(100) NOT NULL,
//    `money` decimal(10,2) NOT NULL,
//    `status` tinyint(1) NOT NULL DEFAULT '0',
//    `callback` varchar(255) NOT NULL,
//    `url` varchar(255) NOT NULL,
//    `param` text NOT NULL,
//    `create_time` int(11) NOT NULL,
//    `update_time` int(11) NOT NULL,
//    PRIMARY KEY (`out_trade_no`)
//    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
class Pay
{

    /**
     * 支付驱动实例
     * @var object
     */
    private $payer;

    /**
     * 配置参数
     * @var array
     */
    private $config;

    /**
     * 构造方法，用与构造支付实例
     * @param string $driver 要使用的支付驱动
     * @param array $config 支付配置
     */
    public function __construct($driver, $config = array())
    {
        // 配置
        $pos = strrpos($driver, '\\');
        $pos = $pos === false ? 0 : $pos + 1;

        // 支付类型
        $paytype = strtolower(substr($driver, $pos));

        // 通知地址 TODO
        $this->config['notify_url'] = U("Pay/notify", array('apitype' => $paytype, 'method' => 'notify'), false, true);

        // 回掉地址 TODO
        $this->config['return_url'] = U("Pay/notify", array('apitype' => $paytype, 'method' => 'return'), false, true);

        $config = array_merge($this->config, $config);

        // 设置支付驱动
        $class = strpos($driver, '\\') ? $driver : '\\Common\\Library\\Pay\\Pay\\Driver\\' . ucfirst(strtolower($driver));

        $this->setDriver($class, $config);

    }

    /**
     * 设置支付驱动
     * @param string $class 支付驱动
     * @param array $config 支付配置参数
     * @throws \Exception
     */
    public function setDriver($class, $config)
    {
        $this->payer = new $class($config);
        if (!$this->payer) {
            throw new \Exception("不存在支付驱动：{$class}");
        }
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $arguments);
        } elseif (!empty($this->payer) && $this->payer instanceof Pay\Driver && method_exists($this->payer, $method)) {
            return call_user_func_array(array(&$this->payer, $method), $arguments);
        }
    }

    public function buildRequestForm(Param $param)
    {
//        $this->payer->checkConfig();
        // 处理数据
        return $this->payer->buildRequestForm($param);
    }
}