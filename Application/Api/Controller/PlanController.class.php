<?php
namespace Api\Controller;

/**
 * 购买方案管理
 */
class PlanController extends ApiController
{
    /**
     * 获取套餐信息
     *
     * 说明：
     * 1、不需要用户验证即可获取商城中的销售方案列表
     * 2、name     方案名称[搜索条件]
     * 3、sn       方案编号[搜索条件]
     * 4、seed_id  农作物ID[搜索条件]
     */
    public function lists()
    {
        //查询条件
        $m = M('Goods');
        //已上架商品
        $map['status'] = 1;
        $map['goods_type'] = 1;

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
            $this->apiReturn(46301, '暂无农作物信息');
        }
    }

    /**
     * 获取方案详情
     *
     * 说明：
     * 1、id   销售方案ID[搜索条件]
     */
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
