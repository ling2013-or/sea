<?php

namespace Api\Controller;

/**
 * 库存管理
 * Class StorageController
 * @package Api\Controller
 */
class StorageController extends ApiController
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
     * 查看库存列表
     *
     * name 种子名称搜索
     */
    public function lists()
    {
        $condition = array();
        $condition['storage.user_id'] = $this->uid;
        $condition['storage.total_weight'] = array('GT', 0);    // 总库存不能小于0

        //搜索
        if (isset($this->data['name']) && $this->data['name'] !== '') {
            $condition['storage.seed_name'] = array('LIKE', '%' . $this->data['name'] . '%');
        }

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
        // 获取总条数
        $count = M('UserStorage')
            ->alias('storage')
            ->where($condition)
            ->count();

        $field = 'storage.storage_id,storage.summary_id,storage.seed_id,storage.plan_id,storage.seed_name,storage.total_weight,storage.freeze_weight,storage.available_weight,plan.plan_sn,plan.plan_name';
        $lists = M('UserStorage')
            ->alias('storage')
            ->join('__PLAN_SELL__ AS plan ON storage.plan_id = plan.plan_id', 'LEFT')
            ->field($field)
            ->where($condition)
            ->order('storage.plan_id DESC')
            ->limit($limit)
            ->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 总库存列表
     */
    public function summary()
    {
        // TODO 搜索条件
        $condition = array();
        $condition['user_id'] = $this->uid;

        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
        // 获取总条数

        $count = M('UserStorageSummary')->where($condition)->count();

        $field = 'summary_id,seed_id,seed_name,total_weight,freeze_weight,available_weight';
        $lists = M('UserStorageSummary')->field($field)->where($condition)->limit($limit)->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 查看可兑换金币的库存
     */
    public function stock()
    {
        $condition = array();
        if (isset($this->data['cart_id'])) {
            $cart_ids = trim($this->data['card_ids'], '\t\n,');
            // 获取有效的订单
            $orderModel = D('Order');

            list($cart_list, $goods_total) = $orderModel->calcBuyList($cart_ids, $this->uid);
            if (!empty($cart_list)) {
                $seed_id_array = array();
                foreach ($cart_list as $info) {
                    if (!$info['state'] || !$info['storage_state']) {
                        $seed_id_array[] = $info['seed_id'];
                    }
                }
                $condition['storage.seed_id'] = array('NOT IN', $seed_id_array);
            }
        }

        // 可用库存必须大于0
        $condition['storage.user_id'] = $this->uid;
        $condition['storage.available_weight'] = array('GT', 0);
        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
        // 获取总条数
        $count = M('UserStorage')
            ->alias('storage')
            ->where($condition)
            ->count();

        $field = 'storage.storage_id,storage.summary_id,storage.seed_id,storage.plan_id,storage.seed_name,storage.total_weight,storage.freeze_weight,storage.available_weight,plan.plan_sn,plan.plan_name';
        $lists = M('UserStorage')
            ->alias('storage')
            ->join('__PLAN_SELL__ AS plan ON storage.plan_id = plan.plan_id', 'LEFT')
            ->field($field)
            ->where($condition)
            ->order('storage.plan_id DESC')
            ->limit($limit)
            ->select();

        if (is_array($lists) && !empty($lists)) {
            $PriceModel = D('MarketPrice');
            foreach ($lists as &$val) {
                // 价格
                $val['price'] = $PriceModel->getDayCropPrice($val['seed_id'], $this->uid);
                // 兑换总价格
                $val['amount'] = $val['price'] * $val['available_weight'];
            }
        }

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 现货转金币
     */
    public function money()
    {
        // 库存ID
        if (!isset($this->data['id']) && intval($this->data['id']) <= 0) {
            $this->apiReturn(46831, '请选择要兑换的库存');
        }

        // 兑换重量
        if (!isset($this->data['weight']) && floatval($this->data['weight']) <= 0) {
            $this->apiReturn(46832, '请选择要兑换的作物重量');
        }

        // 检测参数是否合法
        $condition = array();
        $condition['storage_id'] = intval($this->data['id']);
        $condition['user_id'] = $this->uid;
        $info = M('UserStorage')->field('available_weight')->where($condition)->find();
        if (!$info) {
            $this->apiReturn(46833, '商品库存不存在');
        }

        $weight = floatval($this->data['weight']);
        if ($info['available_weight'] < $weight) {
            $this->apiReturn(46834, '商品库存不足');
        }

        $price = D('MarketPrice')->getDayCropPrice($info['seed_id'], $this->uid);

        $Model = D('UserStorage');
        // TODO 处理库存，处理账户金额
        try {
            $Model->startTrans();
            if (!$Model->changeStorage('store_to_money', $this->uid, $info['seed_id'], $info['plan_id'], $weight)) {
                throw new \Exception('处理库存失败', 46835);
            }

            $data = array(
                'uid' => $this->uid,
                'user_name' => $this->user_name,
                'affect_money' => $price * $weight,
                'order_sn' => '',
                'seed_name' => $info['seed_name'],
                'price' => $price,
            );
            if (!D('UserAccount')->changeAccount('store_to_money', $data)) {
                throw new \Exception('兑换金币失败', 46836);
            }
            $Model->commit();
            $this->apiReturn(0, '兑换成功');
        } catch (\Exception $e) {
            $Model->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }
}