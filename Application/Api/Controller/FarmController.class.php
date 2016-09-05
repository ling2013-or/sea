<?php
namespace Api\Controller;

/**
 * 农场管理
 *
 * @package Api\Controller
 */
class FarmController extends ApiController
{
    /**
     * 获取农场列表
     *
     */
    public function lists()
    {
        //查询条件
        $m = M('Far m');
        $map['status'] = 1;

        //按照农场名称搜索
        if (isset($this->data['name']) && $this->data['name'] !== '') {
            $map['farm_name'] = array('LIKE', '%' . I('name') . '%');
        }

        //按照农场编号搜索
        if (isset($this->data['sn']) && $this->data['sn'] !== '') {
            $map['farm_sn'] = I('sn');
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
        $res = $m->field(true)->where($map)->limit($limit)->select();
        if ($res) {
            $data = array(
                'page' => $this->page,
                'count' => $count,
                'list' => $res ? $res : '',
            );
            $this->apiReturn(0,'成功',$data);
        } else {
            $this->apiReturn(-1,'暂无农场信息');
        }
    }

    /**
     * 获取农场详情
     *
     * 说明：
     * 1、farm_id  农场ID
     * 2、block_id 分区ID
     */
    public function info()
    {
        $farm_id = intval(I('farm_id'));
        $block_id = intval(I('block_id'));
        if (!$farm_id) {
            $this->apiReturn(46702,'农场ID不能为空');
        }
        if (!$block_id) {
            $this->apiReturn(46703,'农场分区ID不能为空');
        }

        $map['status']  = 1;
        $map['farm_id'] = $farm_id;
        $res = M('Farm')->alias('farm')
            ->field('farm.farm_id,farm.farm_sn,farm.farm_name,farm.farm_descript,farm.address,farm.add_time,p.area_name province,
            c.area_name city,a.area_name county')
            ->join("__AREA__ p ON farm.province = p.area_id", 'LEFT')
            ->join("__AREA__ c ON farm.city = c.area_id", 'LEFT')
            ->join("__AREA__ a ON farm.county = a.area_id", 'LEFT')
            ->where($map)->find();
        if ($res) {
            //查询使用该农作物已发布但未执行的方案
            $condition['b.status'] = 1;
            $condition['b.farm_id'] = $farm_id;
            $condition['b.block_id'] = $block_id;
            $block = M('FarmBlock')->alias('b')
                ->join('__FARM_TYPE__ t ON t.type_id=b.type_id','LEFT')
                ->field('b.block_id,b.block_sn,b.block_name,b.block_price,b.block_descript,b.block_state,
                        b.block_temp,b.area_total,b.area_used,b.seed,b.add_time,t.type_name')
                ->where($condition)->find();
            $res['block'] = $block;
            $this->apiReturn(0,'成功',$res);
        } else {
            $this->apiReturn(-1,'农场不存在');
        }
    }
}