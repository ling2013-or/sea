<?php
namespace Api\Controller;

/**
 * 套餐管理
 *
 * @package Api\Controller
 */
class SeedController extends ApiController
{
    /**
     * 套餐列表
     *
     * 说明：
     * 1、不需要用户验证即可获取商城中的农作物列表
     * 2、name 种子名称[搜索条件]
     * 3、sn   种子编号[搜索条件]
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
     * 获取农作物详情
     *
     * 说明：
     * 1、id  种子ID[搜索条件]
     */
    public function info()
    {
        $seed_id = intval($this->data['id']);
        if ($seed_id == 0) {
            $this->apiReturn(46302,'农作物ID不能为空');
        }

        $map['status']  = 1;
        $map['seed_id'] = $seed_id;
        $res = M('Seed')->field('status,add_time,seed_price',true)->where($map)->find();
        if ($res) {
            //查询使用该农作物已发布但未执行的方案
            $condition['status'] = 1;
            $condition['seed_id'] = $seed_id;
            $plan = M('PlanSell')->where($condition)->count();
            $res['plan_num'] = $plan;
            $this->apiReturn(0,'成功',$res);
        } else {
            $this->apiReturn(46303,'农作物不存在或已被删除');
        }
    }
}