<?php

namespace Api\Controller;

/**
 * 会员商场销售管理
 * Class SaleController
 * @package Api\Controller
 */
class SaleController extends ApiController
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
     * 销售列表
     */
    public function lists()
    {
        $condition = array();
        $condition['store_id'] = $this->uid;
        $condition['goods_status'] = array('NEQ', -1);
        // 筛选条件
        if (isset($this->data['type'])) {
            switch ($this->data['type']) {
                case 'up':      // 已上架农作物
                    $condition['goods_status'] = 1;
                    break;
                case 'down':    // 未上架农作物
                    $condition['goods_status'] = 0;
                    break;
            }
        }
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }

        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }

        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        // 获取总条数
        $count = M('Goods')->where($condition)->count();

        // 查询字段
        $field = 'id,seed_id,plan_id,goods_name,goods_price,goods_description,goods_image,goods_click,goods_stock,goods_add_time,goods_status';
        $lists = M('Goods')->field($field)->where($condition)->order('id DESC')->limit($limit)->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);
    }

    /**
     * 出售商品详情
     */
    public function detail()
    {
        if (!isset($this->data['goods_id']) || empty($this->data['goods_id'])) {
            $this->apiReturn(46211, '请选择要查看的商品');
        }

        $condition = array();
        $condition['id'] = $this->data['goods_id'];
        $condition['store_id'] = $this->uid;
        $condition['goods_status'] = array('NEQ', -1);
        $info = M('Goods')->field(true)->where($condition)->find();
        if (!$info) {
            $this->apiReturn(46212, '商品不存在');
        }

        $this->apiReturn(0, '成功', $info);
    }

    /**
     * 添加商品（按批次添加）
     */
    public function add()
    {
        // 按销售方案存储的库存ID
        if (!isset($this->data['storage_id']) || empty($this->data['storage_id'])) {
            $this->apiReturn(46221, '请选择要出售的库存农作物');
        }

        // 出售重量
        if (!isset($this->data['weight']) || floatval($this->data['weight']) <= 0) {
            $this->apiReturn(46222, '请填写要出售的重量');
        }

        // 价格
        if (!isset($this->data['price']) || floatval($this->data['price']) <= 0) {
            $this->apiReturn(46223, '请填写农作物出售价格');
        }

        // 标题
        if (!isset($this->data['title']) || empty($this->data['title'])) {
            $this->apiReturn(46224, '请填写出售商品标题');
        }

        // 描述
        if (!isset($this->data['description']) || empty($this->data['description'])) {
            $this->apiReturn(46225, '请填写出售商品描述');
        }

        // 检测合法性
        $condition = array();
        $condition['stock.storage_id'] = $this->data['storage_id'];
        $condition['stock.user_id'] = $this->uid;

        $field = 'stock.*,seed.seed_name,seed.seed_img,seed.seed_descript,seed.seed_usage';
        $info = M('UserStorage')
            ->alias('stock')
            ->join('__SEED__ AS seed ON stock.seed_id = seed.seed_id', 'LEFT')
            ->field($field)
            ->where($condition)
            ->find();
        if (!$info) {
            $this->apiReturn(46226, '此库存信息不存在');
        }

        // 检测出售重量是够合法
        $weight = floatval($this->data['weight']);
        if ($weight > $info['available_weight']) {
            $this->apiReturn(46227, '出售重量超出可用库存重量');
        }

        $data = array();
        $data['plan_id'] = $info['plan_id'];    // 销售方案ID
        $data['storage_id'] = $info['storage_id'];         // 库存ID
        $data['store_id'] = $this->uid;         // 出售会员ID
        $data['seed_id'] = $info['seed_id'];    // 种子/农作物ID
        $data['goods_name'] = htmlspecialchars($this->data['title']);   // 商品名称，用户自定义
        $data['goods_price'] = floatval($this->data['price']);      // 出售价格，单位：元/千克
        $data['goods_description'] = htmlspecialchars($this->data['description']);  // 商品描述
        $data['goods_body'] = $info['seed_usage'];  // 农作物描述
        $seedImgs = json_decode($info['seed_img']);
        if( $seedImgs and is_array($seedImgs) and (count($seedImgs)) )
        {
            $data['goods_image'] = $seedImgs[0];   // 商品封面图
        }
        
        $data['goods_image_more'] = $info['seed_img']; // 商品多图
        $data['goods_stock'] = $weight;     // 出售重量
        $data['goods_add_time'] = NOW_TIME;     // 添加时间
        $data['goods_status'] = isset($this->data['status']) && $this->data['status'] != 1 ? 0 : 1; // 商品状态

        // TODO 所属农场等
        $Model = D('UserStorage');

        try {
            $Model->startTrans();

            // 更改库存
            if(!$Model->sellChangeStorage('sell_freeze', $this->uid, $this->data['storage_id'], $weight)) {
                throw new \Exception($Model->getError(), $Model->getCode());
            }

            // 添加出售商品
            if (!M('Goods')->add($data)) {
                throw new \Exception('添加出售商品失败', 46228);
            }

            $Model->commit();
            $this->apiReturn(0, '添加出售商品成功');
        } catch (\Exception $e) {
            $Model->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 商品上架管理
     * 注：此操作不处理库存
     * 注：只要不是SQL错误，全部返回成功
     */
    public function up()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(46241, '请选择上架商品');
        }

        $condition = array();
        $condition['id'] = $this->data['id'];
        $condition['store_id'] = $this->uid;
        $condition['goods_status'] = 0;

        $res = M('Goods')->where($condition)->save(array('goods_status' => 1));
        if (false === $res) {
            $this->apiReturn(-1, '修改失败');
        }

        $this->apiReturn(0, '商品上架成功');
    }

    /**
     * 商品下架管理
     * 注：此操作不处理库存
     */
    public function down()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(46251, '请选择下架商品');
        }

        $condition = array();
        $condition['id'] = $this->data['id'];
        $condition['store_id'] = $this->uid;
        $condition['goods_status'] = 1;

        $res = M('Goods')->where($condition)->save(array('goods_status' => 0));
        if (false === $res) {
            $this->apiReturn(-1, '修改失败');
        }

        $this->apiReturn(0, '商品下架成功');
    }

    /**
     * 编辑商品
     */
    public function edit()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(46261, '请选择要修改的商品');
        }

        // 出售重量
        if (!isset($this->data['weight']) || floatval($this->data['weight']) <= 0) {
            $this->apiReturn(46262, '请填写要出售的重量');
        }

        // 价格
        if (!isset($this->data['price']) || floatval($this->data['price']) <= 0) {
            $this->apiReturn(46263, '请填写农作物出售价格');
        }

        // 标题
        if (!isset($this->data['title']) || empty($this->data['title'])) {
            $this->apiReturn(46264, '请填写出售商品标题');
        }

        // 描述
        if (!isset($this->data['description']) || empty($this->data['description'])) {
            $this->apiReturn(46265, '请填写出售商品描述');
        }

        // 获取商品信息
        $goodsModel = M('Goods');
        $condition = array();
        $condition['id'] = $this->data['id'];
        $condition['store_id'] = $this->uid;
        $condition['goods_status'] = array('NEQ', -1);

        $info = $goodsModel->field('id,plan_id,seed_id,goods_stock')->where($condition)->find();
        if (!$info) {
            $this->apiReturn(46266, '商品信息不存在');
        }

        $weight = floatval($this->data['weight']);

        // 如果没有修改库存，则只需变得商品信息即可
        if ($weight == $info['goods_stock']) {
            $data['goods_name'] = htmlspecialchars($this->data['title']);   // 商品名称，用户自定义
            $data['goods_price'] = floatval($this->data['price']);      // 出售价格，单位：元/千克
            $data['goods_description'] = htmlspecialchars($this->data['description']);  // 商品描述
            $data['goods_status'] = isset($this->data['status']) && $this->data['status'] != 1 ? 0 : 1; // 商品状态
            if (false === $goodsModel->where(array('id'=>$info['id']))->save($data)) {
                $this->apiReturn(-1, '商品修改失败');
            }
            $this->apiReturn(0, '商品修改成功');
        }

        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['plan_id'] = $info['plan_id'];
        $condition['seed_id'] = $info['seed_id'];

        $Model = D('UserStorage');
        $storage = $Model->field(true)->where($condition)->find();
        // TODO 库存处理
        if($weight > $info['goods_stock']) {
            // 增加库存处理
            if ($weight > $storage['available_weight']) {
                $this->apiReturn(46267, '出售重量超出可用库存重量');
            }
            $change_type = 'sell_freeze';
            $stock = $weight - $info['goods_stock'];
        } else {
            // 减少库存处理
            $change_type = 'sell_unfreeze';
            $stock =  $info['goods_stock'] - $weight;
        }

        $data = array();
        $data['goods_name'] = htmlspecialchars($this->data['title']);   // 商品名称，用户自定义
        $data['goods_price'] = floatval($this->data['price']);      // 出售价格，单位：元/千克
        $data['goods_description'] = htmlspecialchars($this->data['description']);  // 商品描述
        $data['goods_stock'] = $weight;     // 出售重量
        $data['goods_status'] = isset($this->data['status']) && $this->data['status'] != 1 ? 0 : 1; // 商品状态

        try {
            $Model->startTrans();

            // 更改库存
            if(!$Model->sellChangeStorage($change_type, $this->uid, $storage['storage_id'], $stock)) {
                throw new \Exception($Model->getError(), $Model->getCode());
            }

            // 修改出售商品
            if (!M('Goods')->where(array('id'=>$info['id']))->save($data)) {
                throw new \Exception('修改出售商品失败', 46268);
            }

            $Model->commit();
            $this->apiReturn(0, '修改出售商品成功');
        } catch (\Exception $e) {
            $Model->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 商品删除
     */
    public function del()
    {
        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(46281, '请选择删除的商品');
        }

        $goodsModel = M('Goods');

        // 获取商品详情
        $condition = array();
        $condition['id'] = $this->data['id'];
        $condition['store_id'] = $this->uid;
        $condition['goods_status'] = array('NEQ', -1);

        $info = $goodsModel->field('id,plan_id,seed_id,storage_id,goods_stock')->where($condition)->find();
        if (!$info) {
            $this->apiReturn(46282, '商品信息不存在');
        }

        // 检查剩余库存
        if ($info['goods_stock'] <= 0) {
            // 已出售完，直接删除即可
            if (false === $goodsModel->where(array('id' => $info['id']))->save(array('goods_status' => -1))) {
                $this->apiReturn(-1, '删除失败');
            }
            $this->apiReturn(0, '删除成功');
        }

        // 处理剩余库存
        $Model = D('UserStorage');
        try {
            $Model->startTrans();

            // 更改库存
            if(!$Model->sellChangeStorage('sell_unfreeze', $this->uid, $info['storage_id'], $info['goods_stock'])) {
                throw new \Exception($Model->getError(), $Model->getCode());
            }

            if (false === $goodsModel->where(array('id' => $info['id']))->save(array('goods_status' => -1))) {
                throw new \Exception('删除失败', -1);
            }

            $Model->commit();
            $this->apiReturn(0, '删除成功');
        } catch (\Exception $e) {
            $Model->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }

    }

}