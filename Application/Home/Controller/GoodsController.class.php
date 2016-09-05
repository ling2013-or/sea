<?php
/**
 * 产品展示
 * User: Administrator
 * Date: 2016/8/25
 * Time: 10:41
 */

namespace Home\Controller;


class GoodsController extends HomeController
{
    public function index()
    {
        $this->data = I('get.');
        //查询条件
        $m = M('Goods');
        //已上架商品
        $map['status'] = 1;
        $type = '';
        if (isset($this->data['goods_type']) && $this->data['goods_type'] == 'package') {
            $map['goods_type'] = 1;
            $type = 'package';
            $this->meta_title = '组合介绍';
        } elseif (!isset($this->data['goods_type']) || $this->data['goods_type'] == 'goods') {
            $map['goods_type'] = 0;
            $type = 'goods';
            $this->meta_title = '精品单品';
        } else {
            $this->error('非法请求');
        }

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


        //获取轮播图片
        $carousel = $this->getCarousel(CONTROLLER_NAME, $type);
        $this->assign('imgs',$carousel['carousel']['img']);
        $this->assign('goods', $carousel);
        $this->assign('page', $this->page);
        $this->assign('count', $count);
        $this->assign('lists', $res ? $res : '');
        $this->display();
    }

    public function detail()
    {
        $info = $this->goodsCommon();
        $this->assign('info', $info);
        $this->meta_title = '产品详情';
        $this->display();
    }

    public function goodsCommon(){
        $this->data = I('get.');
        if(IS_AJAX){
            $this->data = I('post.');
        }
        if (!isset($this->data['gid']) || empty($this->data['gid'])) {
            $this->error('请选择您要查看的产品',U('index'));
        }

        $condition = array();
        $condition['id'] = $this->data['gid'];
        $condition['status'] = 1;
        $info = M('Goods')->field('*')->where($condition)->find();

        if ($info) {
            $info['picture_more'] = json_decode($info['picture_more']);
            // 浏览数+1
            M('Goods')->where($condition)->setInc('browse');
        }
        //统计商品的好评率
        $info['statistics'] = $this->evaluate_static($this->data['gid']);
        $info['plans'] = $this->goodsPlan($this->data['gid']);
        return $info;
    }

    public function shop()
    {
        $this->display();
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
        $condition1 = array();
        $condition['goods_id'] = $id;
        $condition1['comm.goods_id'] = $id;
        //查询10条评价
        $list = M('GoodsComment')->alias('comm')->field('comm.*,u.user_name,t2.order_sn')
            ->join('__USER__ u ON u.uid = comm.user_id','LEFT')
            ->join('__ORDER__ t2 ON t2.order_id = comm.order_id','LEFT')
            ->where($condition1)->select();
        $result['lists'] = $list;
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

    /**
     * 获取当前产品的养殖方案信息
     * @param $id
     * @return mixed
     */
    public function goodsPlan($id){
        $condition = array();
        $condition['goods_id'] = $id;
        $condition['status'] = 0;
        //查询当前产品的样式方案信息
        $lists = M('PlanSell')->field('*')->where($condition)->select();
        return $lists;
    }



}