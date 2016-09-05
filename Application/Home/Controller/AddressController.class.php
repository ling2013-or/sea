<?php

namespace Home\Controller;

/**
 * 用户收货地址管理
 * Class AddressController
 * @package Api\Controller
 */
class AddressController extends HomeController
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
     * 用户收货地址列表
     */
    public function lists()
    {
        $condition = array();
        $condition['uid'] = $this->uid;
        $post = I('get.');
        //获取默认收货地址
        if (isset($post['default']) && !empty($post['default'])) {
            $condition['is_default'] = 1;
        }
        //获取指定的收货地址
        if (isset($post['id']) && !empty($post['id'])) {
            $condition['id'] = $post['id'];
        }
        $list = M('UserAddress')->field('id,consignee,area_info,address,phone,is_default')->where($condition)->order('is_default DESC')->select();
        $this->assign('lists', $list);
        $this->meta_title = '收货地址列表';
        $this->display();
    }

    /**
     * 添加收货地址
     */
    public function add()
    {
        if (IS_AJAX) {
            $post = I('post.');
            // 检测参数
            if (!isset($post['consignee']) || empty($post['consignee'])) {
                $this->error('收货人姓名不能为空');
            }

            if (!isset($post['city_id']) || empty($post['city_id'])) {
                $this->error('城市ID不能为空');
            }

            if (!isset($post['area_id']) || empty($post['area_id'])) {
                $this->error('区域ID不能为空');
            }

            if (!isset($post['area_info']) || empty($post['area_info'])) {
                $this->error('区域详情不能为空');
            }

            if (!isset($post['address']) || empty($post['address'])) {
                $this->error('详细地址不能为空');
            }

            if (!isset($post['phone']) || empty($post['phone'])) {
                $this->error('手机号码不能为空');
            }

            if (!isset($post['province_id']) || empty($post['province_id'])) {
                $this->error('省份ID不能为空');
            }

            if (!preg_match('/^1[0-9]{10}$/', $post['phone'])) {
                $this->error('收货人手机号码格式不正确');
            }

            $is_default = isset($post['is_default']) && $post['is_default'] == 1 ? 1 : 0;

            $data = array();
            $data['uid'] = $this->uid;
            $data['consignee'] = htmlspecialchars($post['consignee']);
            $data['province_id'] = intval($post['province_id']);
            $data['city_id'] = intval($post['city_id']);
            $data['area_id'] = intval($post['area_id']);
            $data['area_info'] = htmlspecialchars($post['area_info']);
            $data['address'] = htmlspecialchars($post['address']);
            $data['phone'] = $post['phone'];
            $data['is_default'] = $is_default;
            $data['add_time'] = date("Y-m-d H:i:s", NOW_TIME);

            $Model = M('UserAddress');
            if ($is_default == 1) {
                $Model->where(array('uid' => $this->uid))->save(array('is_default' => 0));
            }
            if (!$Model->add($data)) {
                $this->error(' 添加收货地址失败');
            }

            $this->success('添加收货地址成功');
        }
        $this->meta_title = '添加收货地址';
        $this->display();
    }

    /**
     * 获取收货地址详情
     */
    public function info()
    {
        $post = I('get.');
        if (!isset($post['id']) || empty($post['id'])) {
            $this->error('请选择要查看的收货地址');
        }

        $condition = array();
        $condition['id'] = intval($post['id']);
        $condition['uid'] = $this->uid;

        $info = M('UserAddress')->field('id,consignee,area_info,address,phone,zip_code,is_default,city_id,area_id')->where($condition)->find();
        return $info;

    }

    /**
     * 编辑收货地址
     */
    public function edit()
    {
        if (IS_POST) {
            $post = I('post.');
            // 检测参数
            if (!isset($post['id']) || empty($post['id'])) {
                $this->error('请选择要编辑的收货地址');
            }

            if (!isset($post['consignee']) || empty($post['consignee'])) {
                $this->error('收货人姓名不能为空');
            }

            if (!isset($post['city_id']) || empty($post['city_id'])) {
                $this->error('城市ID不能为空');
            }

            if (!isset($post['area_id']) || empty($post['area_id'])) {
                $this->error('区域ID不能为空');
            }

            if (!isset($post['area_info']) || empty($post['area_info'])) {
                $this->error('区域详情不能为空');
            }

            if (!isset($post['address']) || empty($post['address'])) {
                $this->error('详细地址不能为空');
            }

            if (!isset($post['phone']) || empty($post['phone'])) {
                $this->error('手机号码不能为空');
            }

            if (!preg_match('/^1[0-9]{10}$/', $post['phone'])) {
                $this->error('收货人手机号码格式不正确');
            }

            $is_default = isset($post['is_default']) && $post['is_default'] == 1 ? 1 : 0;

            $data = array();
            $data['uid'] = $this->uid;
            $data['consignee'] = htmlspecialchars($post['consignee']);
            $data['province_id'] = intval($post['province_id']);
            $data['city_id'] = intval($post['city_id']);
            $data['area_id'] = intval($post['area_id']);
            $data['area_info'] = htmlspecialchars($post['area_info']);
            $data['address'] = htmlspecialchars($post['address']);
            $data['phone'] = $post['phone'];
            $data['is_default'] = $is_default;
            // $data['add_time'] = NOW_TIME;

            $Model = M('UserAddress');
            if ($is_default == 1) {
                $Model->where(array('uid' => $this->uid))->save(array('is_default' => 0));
            }

            $condition = array();
            $condition['id'] = intval($post['id']);
            $condition['uid'] = $this->uid;
            if (false === $Model->where($condition)->save($data)) {
                $this->error(' 更新收货地址失败');
            }

            $this->success('更新收货地址成功');
        }
        //获取地址详情
        $info = $this->info();
        $this->assign('info', $info);
        $this->meta_title = '编辑收货地址';
        $this->display();
    }

    /**
     * 删除收货地址
     */
    public function del()
    {
        $post = I('post.');
        if (!isset($post['id']) || empty($post['id'])) {
            $this->error('请选择要删除的收货地址');
        }
        $condition = array();
        $condition['id'] = intval($post['id']);
        $condition['uid'] = $this->uid;

        $res = M("UserAddress")->where($condition)->delete();

        if (false === $res) {
            $this->error(' 删除收货地址失败');
        }

        $this->success('删除收货地址成功');
    }

    /**
     * 设置默认地址
     */
    public function def()
    {
        if (IS_AJAX) {
            $post = I('post.');
            if (!isset($post['id']) || empty($post['id'])) {
                $this->error('请选择要设置的收货地址');
            }

            $is_default = isset($post['is_default']) && $post['is_default'] == 1 ? 1 : 0;

            $condition = array();
            $condition['id'] = intval($post['id']);
            $condition['uid'] = $this->uid;

            $Model = M('UserAddress');
            if ($is_default == 1) {
                $Model->where(array('uid' => $this->uid))->save(array('is_default' => 0));
            }

            if (false === $Model->where($condition)->save(array('is_default' => $is_default))) {
                $this->error(' 设置失败');
            }

            $this->success(0, '设置成功');
        }
        $this->meta_title = '地址管理';
        $this->display();
    }
}