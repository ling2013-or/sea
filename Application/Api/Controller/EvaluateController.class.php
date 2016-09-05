<?php

namespace Api\Controller;

/**
 * 会员评论管理
 * 编码区块433
 * Class EvaluateController
 * @package Api\Controller
 */
class EvaluateController extends ApiController
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
     * 会员添加评论
     */
    public function add()
    {

        if (!isset($this->data['order_id']) || empty($this->data['order_id'])) {
            $this->apiReturn(43321, '请选择要评论的订单');
        }

        $all = array();

        foreach($this->data['comment'] as $key=>$val){
            if (!isset($val['goods_id']) || empty($val['goods_id'])) {
                $this->apiReturn(43322, '请选择要评论的商品');
            }

            // 评论内容
            if (!isset($val['comment']) || empty($val['comment'])) {
                $this->apiReturn(43323, '评论内容不能为空');
            }

            // 评论长度检测
            $len = mb_strlen($val['comment']);
            if ($len < 4 || $len > 200) {
                $this->apiReturn(43324, '请输入4~200字符长度的评论长度');
            }

            // 检测用户是否有评论商品权限
            $condition = array();
            $condition['order_id'] = $this->data['order_id'];
            $condition['buyer_id'] = $this->uid;
            $condition['is_delete'] = 0;

            // TODO 未配送订单是否允许评论
//        $condition['is_shipping'] = 1;

            $info = M('Order')->field('order_status')->where($condition)->find();
            if (!$info) {
                $this->apiReturn(43325, '订单不存在');
            }
            if ($info['order_status'] == 4) {
                $this->apiReturn(43326, '订单已评价完成');
            }


            $condition = array();
            $condition['order_id'] = $this->data['order_id'];
            $condition['goods_id'] = $val['goods_id'];
//            dump($condition);die;
            $goods = M('OrderGoods')->where($condition)->find();
            if(!$goods) {
                $this->apiReturn(43327, '您没有购买此商品');
            }


            $data = array();
            //是否匿名评论
            if(!isset($val['anony']) || empty($val['annoy'])){
                $data['user_id'] = $this->uid;
            }
            $data['order_id'] = $this->data['order_id'];
            $data['goods_id'] = $val['goods_id'];
            $data['star'] = isset($val['star']) && in_array($val['star'], array('1', '2', '3', '4', '5')) ? $val['star'] : 5;
            // TODO 过滤评论非法字符
            $data['comment'] = htmlspecialchars($val['comment']);
            // TODO 品论是够开启审核
            if($data['status'] == 1) {
                M('Goods')->where(array('id'=>$info['goods_id']))->setInc('comment_num');
            }
            $data['comment_time'] = NOW_TIME;
            //查询该订单的产品是否已评论过
            if(M('GoodsComment')->field(true)->where($condition)->find()){
                $this->apiReturn(43306,'您已评论过此商品!');
            }
            $data['status'] = 1;    // 评论直接显示，不审核
            if (isset($val['images']) && !empty($val['images'])) {
                //监测数组中为空的数据
                $value = array();
                foreach($val['images'] as $img){
                    if($img){
                        $value[] = trim($img, ',');
                    }
                }
                if($value){
                    $data['comment_image'] = json_encode($value);
                }
            }
            //赋值给总数组，然后插入数据
           if($data){
               $all[] = $data;
           }
        }

        $commentModel = D('GoodsComment');
        if(empty($all)){
            $this->apiReturn(-1,'请求数据丢失');
        }
        try {

            $commentModel->startTrans();

            $res = $commentModel->createComment($all);
            if (!$res) {
                throw new \Exception($commentModel->getError(), $commentModel->getCode());
            }
            $result = $commentModel->updateState($this->data['order_id'],'success');
            if(!$result){
                throw new \Exception($commentModel->getError(), $commentModel->getCode());
            }
            $commentModel->commit();
            $this->apiReturn(0, '评价成功');
        } catch (\Exception $e) {
            $commentModel->rollback();
            $this->apiReturn($e->getCode(), $e->getMessage());
        }

    }


    /**
     * 会员评价列表
     */
    public function lists()
    {
        $condition = array();
        $condition['comment.user_id'] = $this->uid;

        // 评论状态
        $condition['comment.status'] = array('NEQ', -1);
        if (isset($this->data['status'])) {
            switch ($this->data['status']) {
                case 'wait':        // 等待审核中
                    $condition['comment.status'] = 0;
                    break;
                case 'normal':      // 正常显示（审核通过）
                    $condition['comment.status'] = 1;
                    break;
                case 'fail':        // 审核失败
                    $condition['comment.status'] = 2;
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
        $count = M('GoodsComment')
            ->alias('comment')
            ->join('__GOODS__ AS goods ON comment.goods_id = goods.id', 'LEFT')
            ->where($condition)
            ->count();

        // 查询字段
        $fields = 'comment.id,comment.order_id,comment.goods_id,comment.star,comment.comment,comment.comment_time,comment.comment_image,comment.status,goods.goods_name';
        $lists = M('GoodsComment')
            ->alias('comment')
            ->join('__GOODS__ AS goods ON comment.goods_id = goods.id', 'LEFT')
            ->field($fields)
            ->where($condition)
            ->order('id DESC')
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
     * 会员评价某作物订单详情列表
     */
    public function info()
    {
        if (isset($this->data['goods_id']) || empty($this->data['goods_id'])) {
            $this->apiReturn(43301, '请选择要查看的商品');
        }
        $condition = array();
        $condition['user_id'] = $this->uid;
        $condition['status'] = array('NEQ', -1);

        $info = M('GoodsComment')->field(true)->where($condition)->find();
        if (!$info) {
            $this->apiReturn(43302, '尚未评论');
        }
        $this->apiReturn(0, '商品评论', $info);
    }

    

}