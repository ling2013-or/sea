<?php

namespace Api\Controller;

/**
 * 用户收货地址管理
 * Class AddressController
 * @package Api\Controller
 */
class AddressController extends ApiController
{

    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();
        $this->uid = 1;
//        $this->uid = $this->isLogin();
    }


    /**
     * 用户收货地址列表
     */
    public function lists()
    {
        $condition = array();
        $condition['uid'] = $this->uid;
        $condition['status'] = 0;
        //获取默认收货地址
        if(isset($this->data['default']) && !empty($this->data['default'])){
            $condition['is_default'] = 1;
        }
        //获取指定的收货地址
        if(isset($this->data['id']) && !empty($this->data['id'])){
            $condition['id'] = $this->data['id'];
        }
        $list = M('UserAddress')->field('id,consignee,area_info,address,phone,is_default')->where($condition)->order('is_default DESC')->select();

        $this->apiReturn(0, '成功', $list);
    }

    /**
     * 添加收货地址
     */
    public function add()
    {
        // 检测参数
        if (!isset($this->data['consignee']) || empty($this->data['consignee'])) {
            $this->apiReturn(41411, '收货人姓名不能为空');
        }

        if (!isset($this->data['city_id']) || empty($this->data['city_id'])) {
            $this->apiReturn(41412, '城市ID不能为空');
        }

        if (!isset($this->data['area_id']) || empty($this->data['area_id'])) {
            $this->apiReturn(41413, '区域ID不能为空');
        }

        if (!isset($this->data['area_info']) || empty($this->data['area_info'])) {
            $this->apiReturn(41414, '区域详情不能为空');
        }

        if (!isset($this->data['address']) || empty($this->data['address'])) {
            $this->apiReturn(41415, '详细地址不能为空');
        }

        if (!isset($this->data['phone']) || empty($this->data['phone'])) {
            $this->apiReturn(41416, '手机号码不能为空');
        }

        if (!isset($this->data['province_id']) || empty($this->data['province_id'])) {
            $this->apiReturn(41423, '省份ID不能为空');
        }

        if (!preg_match('/^1[0-9]{10}$/', $this->data['phone'])) {
            $this->apiReturn(41417, '收货人手机号码格式不正确');
        }

        $is_default = isset($this->data['is_default']) && $this->data['is_default'] == 1 ? 1 : 0;

        $data = array();
        $data['uid'] = $this->uid;
        $data['consignee'] = htmlspecialchars($this->data['consignee']);
        $data['province_id'] = intval($this->data['province_id']);
        $data['city_id'] = intval($this->data['city_id']);
        $data['area_id'] = intval($this->data['area_id']);
        $data['area_info'] = htmlspecialchars($this->data['area_info']);
        $data['address'] = htmlspecialchars($this->data['address']);
        $data['phone'] = $this->data['phone'];
        $data['is_default'] = $is_default;
        $data['add_time'] = date("Y-m-d H:i:s",NOW_TIME);

        $Model = M('UserAddress');
        if ($is_default == 1) {
            $Model->where(array('uid' => $this->uid))->save(array('is_default' => 0));
        }
        if (!$Model->add($data)) {
            $this->apiReturn(-1, ' 添加收货地址失败');
        }

        $this->apiReturn(0, '添加收货地址成功');
    }

    /**
     * 获取收货地址详情
     */
    public function info()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(41421, '请选择要查看的收货地址');
        }

        $condition = array();
        $condition['id'] = intval($this->data['id']);
        $condition['uid'] = $this->uid;

        $info = M('UserAddress')->field('id,consignee,area_info,address,phone,zip_code,is_default,city_id,area_id')->where($condition)->find();
        if (!$info) {
            $this->apiReturn(41422, '该收货地址不存在');
        }

        $this->apiReturn(0, '成功', $info);

    }

    /**
     * 编辑收货地址
     */
    public function edit()
    {
        // 检测参数
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(41431, '请选择要编辑的收货地址');
        }

        if (!isset($this->data['consignee']) || empty($this->data['consignee'])) {
            $this->apiReturn(41432, '收货人姓名不能为空');
        }

        if (!isset($this->data['city_id']) || empty($this->data['city_id'])) {
            $this->apiReturn(41433, '城市ID不能为空');
        }

        if (!isset($this->data['area_id']) || empty($this->data['area_id'])) {
            $this->apiReturn(41434, '区域ID不能为空');
        }

        if (!isset($this->data['area_info']) || empty($this->data['area_info'])) {
            $this->apiReturn(41435, '区域详情不能为空');
        }

        if (!isset($this->data['address']) || empty($this->data['address'])) {
            $this->apiReturn(41436, '详细地址不能为空');
        }

        if (!isset($this->data['phone']) || empty($this->data['phone'])) {
            $this->apiReturn(41437, '手机号码不能为空');
        }

        if (!preg_match('/^1[0-9]{10}$/', $this->data['phone'])) {
            $this->apiReturn(41438, '收货人手机号码格式不正确');
        }

        $is_default = isset($this->data['is_default']) && $this->data['is_default'] == 1 ? 1 : 0;

        $data = array();
        $data['uid'] = $this->uid;
        $data['consignee'] = htmlspecialchars($this->data['consignee']);
        $data['province_id'] = intval($this->data['province_id']);
        $data['city_id'] = intval($this->data['city_id']);
        $data['area_id'] = intval($this->data['area_id']);
        $data['area_info'] = htmlspecialchars($this->data['area_info']);
        $data['address'] = htmlspecialchars($this->data['address']);
        $data['phone'] = $this->data['phone'];
        $data['is_default'] = $is_default;
        // $data['add_time'] = NOW_TIME;

        $Model = M('UserAddress');
        if ($is_default == 1) {
            $Model->where(array('uid' => $this->uid))->save(array('is_default' => 0));
        }

        $condition = array();
        $condition['id'] = intval($this->data['id']);
        $condition['uid'] = $this->uid;
        if (false === $Model->where($condition)->save($data)) {
            $this->apiReturn(-1, ' 更新收货地址失败');
        }

        $this->apiReturn(0, '更新收货地址成功');
    }

    /**
     * 删除收货地址
     */
    public function del()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(41441, '请选择要删除的收货地址');
        }
        $condition = array();
        $condition['id'] = intval($this->data['id']);
        $condition['uid'] = $this->uid;

        $res = M("UserAddress")->where($condition)->delete();

        if (false === $res) {
            $this->apiReturn(-1, ' 删除收货地址失败');
        }

        $this->apiReturn(0, '删除收货地址成功');
    }

    /**
     * 设置默认地址
     */
    public function def()
    {

        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(41451, '请选择要设置的收货地址');
        }

        $is_default = isset($this->data['is_default']) && $this->data['is_default'] == 1 ? 1 : 0;

        $condition = array();
        $condition['id'] = intval($this->data['id']);
        $condition['uid'] = $this->uid;

        $Model = M('UserAddress');
        if ($is_default == 1) {
            $Model->where(array('uid' => $this->uid))->save(array('is_default' => 0));
        }

        if (false === $Model->where($condition)->save(array('is_default' => $is_default))) {
            $this->apiReturn(-1, ' 设置失败');
        }

        $this->apiReturn(0, '设置成功');
    }
}