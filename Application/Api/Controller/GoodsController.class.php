<?php

namespace Api\Controller;

/**
 * 作物商城管理
 * 注：返回码说明：43201开始
 * Class GoodsController
 * @package Api\Controller
 */
class GoodsController extends ApiController
{

    /**
     * 农作物商城列表
     */
    public function lists()
    {
        //查询条件
        $m = M('Goods');
        if(isset($this->data['goods_type'])){
            switch($this->data['goods_type']){
                //产品
                case 'goods':
                    $map['goods_type'] = 0;
                    break;
                //套餐
                case 'package':
                    $map['goods_type'] = 1;
                    break;
                default:
                    //默认为套餐
                    $map['goods_type'] = 0;
                    break;
            }
        }else{
            $map['goods_type'] = 0;
        }
        //已上架商品
        $map['status'] = 1;

        //按照产品名称搜索
        if (isset($this->data['name']) && $this->data['name'] !== '') {
            $map['name'] = array('LIKE', '%' . $this->data['name'] . '%');
        }

        //按照浏览量搜索
        if (isset($this->data['view']) && !empty($this->data['view'])) {
            $map['browse'] = $this->data['view'];
        }

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        //获取总条数
        $count = $m->where($map)->count();

        //获取数据
        $res = $m->field('*')
            ->where($map)->order('goods_star DESC')
            ->limit($limit)->select();
        foreach ($res as $key => $val) {
            $res[$key]['picture_more'] = json_decode($val['picture_more'], true);
        }
        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $res ? $res : '',
        );

        //返回数据
        if ($res) {
            $this->apiReturn(0, '成功', $data);
        } else {
            $this->apiReturn(46301, '暂无产品信息');
        }


    }

    /**
     * 农作物商城详情
     */
    public function detail()
    {
        if (!isset($this->data['goods_id']) || empty($this->data['goods_id'])) {
            $this->apiReturn(43211, '请选择要查看的商品');
        }

        $condition = array();
        $condition['id'] = $this->data['goods_id'];
        $condition['status'] = 1;
        $info = M('Goods')->field('*')->where($condition)->find();

        if ($info) {
            $info['picture_more'] = json_decode($info['picture_more']);
            // 浏览数+1
            M('Goods')->where($condition)->setInc('browse');
        }
        //统计商品的好评率
        $info['statistics'] = $this->evaluate_static($this->data['goods_id']);
        $this->apiReturn(0, '成功', $info);
    }

    /**
     * 商品评价列表
     */
    public function commentlist()
    {
        if (!isset($this->data['goods_id']) || empty($this->data['goods_id'])) {
            $this->apiReturn(43221, '请选择商品');
        }

        $condition = array();
        $condition['t1.goods_id'] = $this->data['goods_id'];
        $condition['t1.status'] = 1;

        $type = isset($this->data['type']) ? intval($this->data['type']) : 0;
        switch ($type) {
            case 1:     // 好评
                $condition['t1.star'] = array('IN', '5,4');
                break;
            case 2:     // 中评
                $condition['t1.star'] = array('IN', '3,2');
                break;
            case 3:     //差评
                $condition['t1.star'] = array('IN', '1');
                break;
            default:
                break;
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
        $count = M('GoodsComment')->alias('t1')->where($condition)->count();

        // 查询字段
        $fields = 't1.id,t1.comment,t1.comment_time,t2.user_name,t2.user_avatar';
        $lists = M('GoodsComment')->alias('t1')->field($fields)
            ->join('__USER__ t2 ON t1.user_id = t2.uid', 'LEFT')
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
     * 杨智计划信息
     */
    public function goodsPlanList()
    {
//        $this->data['goods_id'] = 1;
        if (!isset($this->data['goods_id']) || empty($this->data['goods_id'])) {
            $this->apiReturn(43221, '请选择商品');
        }
        $condition = array();
        $condition['goods_id'] = $this->data['goods_id'];
        //获取数据
        $info = M('PlanSell')->field(true)->where($condition)->order('num ASC')->select();
        if (!$info) {
            $this->apiReturn(43222, '套餐不存在,请重新选择');
        }
        $this->apiReturn(0, '成功', $info);
    }


    /**
     * 统计产品的好评率以及产品的评论数量
     *
     * @param $id 产品ID
     * @return 返回统计结果
     */
    private function evaluate_static($id)
    {
        $condition = array();
        $condition['goods_id'] = $id;
        //统计好评
        $condition['star'] = array('gt', 2);
        $best = M('GoodsComment')->where($condition)->count();
        $condition['star'] = array('lt', 3);
        $bad = M('GoodsComment')->where($condition)->count();
        if ($best == 0 && $bad == 0) {
            $result['percentage'] = '100%';
            $result['total'] = 0;
        } else {
            $total = $best + $bad;
            $result['percentage'] = number_format(($best / $total) * 100, 2, '.', '');
            $result['total'] = $total;
        }
        return $result;
    }
}