<?php

namespace Common\Library\Pay\Pay;

/**
 * 支付参数管理
 * Class Param
 * @package Common\Library\Pay\Pay
 */
class Param
{
    /**
     * 订单号
     * @access private
     * @var string
     */
    private $_orderNo;

    /**
     * 付款金额
     * @access private
     * @var float
     */
    private $_fee;

    /**
     * 订单名称
     * @access private
     * @var string
     */
    private $_title;

    /**
     * 订单描述
     * @access private
     * @var string
     */
    private $_body;

    /**
     * 回调方法
     * @access private
     * @var string
     */
    private $_callback;

    /**
     * 跳转地址
     * @access private
     * @var string
     */
    private $_url;

    /**
     * 订单的额外参数
     * @access private
     * @var array
     */
    private $_param;

    /**
     * 设置订单号
     * @param string $order_no 订单号
     * @return $this
     */
    public function setOrderNo($order_no)
    {
        $this->_orderNo = $order_no;
        return $this;
    }

    /**
     * 设置订单价格
     * @param  float $fee 订单价格
     * @return $this
     */
    public function setFee($fee)
    {
        $this->_fee = $fee;
        return $this;
    }

    /**
     * 设置订单名称
     * @param  string $title 订单名称
     * @return $this
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * 设置订单描述
     * @param  string $body 订单描述
     * @return $this
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * 设置支付完成后的后续操作接口
     * @param string $callback
     * @return $this
     */
    public function setCallback($callback) {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * 设置支付完成后的跳转地址
     * @param string $url
     * @return $this
     */
    public function setUrl($url) {
        $this->_url = $url;
        return $this;
    }

    /**
     * 设置订单的额外参数
     * @param string $param
     * @return $this
     */
    public function setParam($param) {
        $this->_param = $param;
        return $this;
    }

    /**
     * 获取订单号
     * @return string
     */
    public function getOrderNo() {
        return $this->_orderNo;
    }

    /**
     * 获取商品价格
     * @return float
     */
    public function getFee() {
        return $this->_fee;
    }

    /**
     * 获取商品名称
     * @return string
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * 获取支付完成后的后续操作接口
     * @return string
     */
    public function getCallback() {
        return $this->_callback;
    }

    /**
     * 获取支付完成后的跳转地址
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * 获取商品描述
     * @return string
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * 获取订单的额外参数
     * @return string
     */
    public function getParam() {
        return $this->_param;
    }
}