<?php

namespace Api\Controller;

/**
 * 运费管理
 * Class TransportController
 * @package Api\Controller
 */
class TransportController extends ApiController
{

    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->uid = $this->isLogin();
    }

    /**
     * 根据用户收获地址计算运费
     */
    public function calcfee()
    {
        if (!isset($this->data['address_id']) || empty($this->data['address_id'])) {
            $this->apiReturn(43401, '请选择收货地址');
        }

        if (!isset($this->data['weight']) || empty(floatval($this->data['weight']))) {
            $this->apiReturn(43402, '农作物重量不能为空');
        }

        $condition = array();
        $condition['uid'] = $this->uid;
        $address = M('UserAddress')->field(true)->where($condition)->find();

        $shipping_fee = D('Transport')->calcShippingFee(floatval($this->data['weight']), $address['area_id']);

        $data = array();
        $data['shipping_fee'] = $shipping_fee;

        $this->apiReturn(0, '计算成功', $data);
    }
}