<?php

namespace Api\Controller;

/**
 * 会员赠送管理
 * Class GiveController
 * @package Api\Controller
 */
class GiveController extends ApiController
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
     * 确认赠送
     */
    public function confirm()
    {
        if (!isset($this->data['seed_id']) || intval($this->data['seed_id']) <= 0) {
            $this->apiReturn(42201, '请选择要赠送的农作物');
        }

        if (!isset($this->data['plan_id']) || intval($this->data['plan_id']) <= 0) {
            $this->apiReturn(42202, '请选择要赠送农作物所属批次');
        }

        if (!isset($this->data['weight']) || floatval($this->data['weight']) <= 0) {
            $this->apiReturn(42203, '赠送重量不能小于0');
        }

        if (!isset($this->data['sendee']) || empty($this->data['sendee'])) {
            $this->apiReturn(42204, '请选择赠送人');
        }

        $seed_id = intval($this->data['seed_id']);
        $plan_id = intval($this->data['plan_id']);
        // 检测农作物是够存在
        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['seed_id'] = $seed_id;
        $condition['plan_id'] = $plan_id;
        $info = M('UserStorage')->field('seed_name,available_weight')->where($condition)->find();
        if (!$info) {
            $this->apiReturn(42205, '此农作物不存在');
        }

        $weight = floatval($this->data['weight']);
        if ($info['available_weight'] < $weight) {
            $this->apiReturn(42206, '赠送农作物库存不足');
        }

        // 检测接受赠送人是否存在
        $username = trim($this->data['sendee']);

        $condition = array();
        if (strpos($username, '@') === false) {
            // 手机号码|用户名登录
            $condition['user_name'] = $username;
            $condition['user_phone'] = $username;
            $condition['_logic'] = 'OR';
        } else {
            // 邮箱登录
            $condition['user_email'] = $username;
        }

        $user = M('User')->field('uid,user_name,user_email,user_phone')->where($condition)->find();
        if (!$user) {
            $this->apiReturn(42207, '接受赠送人不存在');
        }

        $Model = D('UserStorage');
        try {
            $Model->startTrans();

            if (!$Model->changeStorage('give_reduce', $this->uid, $seed_id, $plan_id, $weight)) {
                throw new \Exception($Model->getError(), $Model->getCode());
            }

            if (!$Model->changeStorage('give_add', $user['uid'], $seed_id, $plan_id, $weight)) {
                throw new \Exception($Model->getError(), $Model->getCode());
            }

            $data = array();
            $data['user_id'] = $this->uid;
            $data['user_name'] = $this->user_name;
            $data['seed_id'] = $seed_id;
            $data['plan_id'] = $plan_id;
            $data['seed_name'] = $info['seed_name'];
            $data['sendee_id'] = $user['uid'];
            $data['sendee_name'] = $user['user_name'];
            $data['weight'] = $weight;
            $data['info'] = isset($this->data['info']) ? htmlspecialchars($this->data['info']) : '';
            $data['add_time'] = NOW_TIME;
            $data['add_ip'] = get_client_ip();
            if (!M('UserGive')->add($data)) {
                throw new \Exception('添加赠送记录失败', 42208);
            }

            $Model->commit();
            $this->apiReturn(0, '赠送农作物成功');
        } catch (\Exception $e) {
            $Model->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }
}