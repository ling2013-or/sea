<?php
/**
 * Created by PhpStorm.
 * User: suntianqi
 * Date: 2016/7/28
 * Time: 10:15
 */

namespace Admin\Controller;

use Think\Controller;

class GoodszoneController extends AdminController
{

    /**
     * 养殖计划列表
     *
     * 状态为0[未发布] 和 1[已发布]
     * 只有已发布时才能设置收益
     */
    public function index()
    {
        $map['zone.status'] = array('neq', -1);
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['zone.title'] = array('like', '%' . (string)I('name') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('GoodsZone')->alias('zone')->join('__GOODS__ goods ON goods.id = zone.goods_id', 'LEFT')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('GoodsZone')->alias('zone')
            ->join('__GOODS__ goods ON goods.id = zone.goods_id', 'LEFT')
            ->field('zone.*,goods.name as goods_name')->where($map)->limit($limit)->order('status,title')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '分区列表管理';

        $this->assign('page', $p ? $p : '');
        $this->assign('today', NOW_TIME);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加方案
     */
    public function add()
    {
        if (IS_POST) {
            $Plan = D('GoodsZone');
            $data = $Plan->create();
            if (empty($data['title'])) $this->error('请填写分区标题');
            if (empty($data['total_stock'])) $this->error('请填写分区容量');
            if (empty($data['goods_id'])) $this->error('请选择产品信息');
            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }

            //添加时间
            $data['add_time'] = $data['update_time'] = NOW_TIME;
            //容量
            $data['real_stock'] = $data['total_stock'] = I('post.total_stock', 0, 'abs,intval');

            //创建计划任务
            if ($Plan->add($data)) {
                //更新产品的实际信息
                $sdata = array();
                $sdata['id'] = $data['goods_id'];
                $sdata['stock'] = array('exp', 'stock + ' . $data['real_stock']);
                $goods = M("Goods");
                $goods->save($sdata);
                $this->success('新增成功', U('index'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $map['status'] = array('neq', '-1');
            //查询可添加的产品养殖计划
            $list = M('Goods')->field('id,name')->where($map)->order('id')->select();
            $this->meta_title = '新增产品分区';
            $this->assign('goodsList', $list);
            $this->display();
        }
    }

    /**
     * 编辑方案
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Plan = D('GoodsZone');
            $data = $Plan->create();
            if (empty($data['title'])) $this->error('请填写方案标题');
            if (empty($data['goods_id'])) $this->error('请选择产品');

            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }

            //添加产品Id
            $id = $data['goods_id'];
            $res = M('Goods')->find($id);
            if (!$res) $this->error('请选择产品');
            $data['update_time'] = NOW_TIME;
            if ($Plan->save($data)) {
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('更新失败');
            }
        } else {
            //获取数据
            $info = M('GoodsZone')->field(true)->find($id);
            if (!$info) {
                $this->error('获取分区信息失败');
            }

            //查询方案是否可以修改
            if ($info['status'] != 0) {
                $this->error('分区不允许被修改');
            }

            $map['status'] = array('neq', -1);
            //查询产品列表
            $goodsList = M('Goods')->field('id,name')->where($map)->select();

            $this->meta_title = '编辑分区';
            $this->assign('info', $info);
            $this->assign('goodsList', $goodsList);
            $this->display();
        }
    }

    /**
     * 删除方案
     *
     * 仅可以删除未交易的方案
     */
    public function del()
    {
        $id = intval(I('id'));
        $data['plan_id'] = $id;
        $data['status'] = -1;
        $m = M('GoodsZone');
        $info = $m->find($id);
        if (!$info) {
            $this->error('未找到该方案');
        }

        //检测该分区是否还有用户的产品
        if ($info['total_stock'] != $info['real_stock']) {
            $this->error('请将原有用户移出');
        }

        //删除未发布方案
        if (($info['status'] == 0 || $info['status'] == 1) && $m->save($data)) {
            $this->success('删除成功');
        }


        $this->error('删除失败！');
    }


    /**
     * 编辑状态
     */
    public function dopush()
    {
        $model = M('GoodsZone');
        $id = intval(I('id'));
        $status = intval(I('status'));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = $id;
        //查看当前方案有没有被使用过
        $info = $model->field('*')->where($map)->find();
        if (empty($info)) {
            $this->error('当前分区不存在');
        }
        //更新方案状态为1
        $data['id'] = $id;
        $data['status'] = $status;
        $data['update_time'] = NOW_TIME;
        if ($info['real_stock'] == $info['total_stock']) {
            if ($model->save($data)) {
                $type = true;
                if ($status != 0) {
                    $type = false;
                }
                $this->updateGoodsStock($info['goods_id'], $type, $info['real_stock']);
                $this->success('修改成功');
            } else {
                $this->error('修改失败！');
            }
        } else {
            $this->error('请将原有的用户转移出当前分区');
        }

    }

    /**
     * 设置方案收益
     */
    public function docomplete()
    {
        $id = intval(I('id'));
        $income = floatval(I('income'));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        //更新income_real
        $data['plan_id'] = $id;
        $data['income_real'] = $income;
        if (M('PlanSell')->save($data)) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败！');
        }
    }

    /**
     * 更新产品的库存信息
     *
     * @param $id
     * @param $num
     * @param bool $type
     * @return bool
     */
    public function updateGoodsStock($id, $type = true, $num = 1)
    {
        $Model = M('Goods');
        $data['id'] = $id;
        $num = abs($num);
        if ($type) {
            $stock = 'stock +';
        } else {
            $stock = 'stock -';
        }
        $data['stock'] = array('exp', $stock . $num);
        if ($Model->save($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 方案编号生成
     * 0~2   01 种子方案 02 存储方案 03 折扣方案 04 销售方案  05分区
     * 3~8   年月日
     * 9~10  两位随机码
     * 11~15 时分秒的字符串
     */
    private function snmake()
    {
        static $index = 1;
        $static = sprintf("%02d", $index);
        $index++;
        $his = sprintf("%005d", time() - strtotime(date('Y-m-d')));
        $rand = sprintf("%002d", mt_rand(0, 99));
        return '05' . date('ymd') . $rand . $his . $static;
    }
} 