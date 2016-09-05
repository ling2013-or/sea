<?php
namespace Admin\Controller;

/**
 * 养殖计划id
 * Class SellplanController
 * @package Admin\Controller
 */
class SellplanController extends AdminController
{

    /**
     * 养殖计划列表
     *
     * 状态为0[未发布] 和 1[已发布]
     * 只有已发布时才能设置收益
     */
    public function index()
    {
        $map['plan.status'] = array('neq', -1);
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['plan.title'] = array('like', '%' . (string)I('name') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('PlanSell')->alias('plan')->join('__GOODS__ goods ON goods.id = plan.goods_id', 'LEFT')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('PlanSell')->alias('plan')
            ->join('__GOODS__ goods ON goods.id = plan.goods_id', 'LEFT')
            ->field('plan.*,goods.name as goods_name')->where($map)->limit($limit)->order('status,title')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '养殖方案列表';
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
            $Plan = D('PlanSell');
            if (empty($data['begin_time'])) $this->error('请填写方案开始时间');
            $data = $Plan->create();
            if (empty($data['goods_id'])) $this->error('请选择产品模块');

            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }
            //添加编号
            $data['plan_sn'] = $this->snmake();
            //添加时间
            $data['add_time'] = $data['update_time'] = NOW_TIME;
            //开始时间
            $data['start_time'] = strtotime($data['start_time']);
            //创建计划任务
            if ($Plan->add($data)) {
                $this->success('新增成功', U('index'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $map['status'] = 0;
            //查询可添加的产品养殖计划
            $list = M('Goods')->field('id,name')->where($map)->order('id')->select();
            $this->meta_title = '新增养殖计划';
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
            $Plan = D('PlanSell');
            $data = $Plan->create();
            if (empty($data['title'])) $this->error('请填写方案标题');
            if (empty($data['goods_id'])) $this->error('请选择产品');
            if (!isset($data['pic'])) {
                $data['pic'] = '';
            }

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
            $info = M('PlanSell')->field(true)->find($id);
            if (!$info) {
                $this->error('获取方案信息失败');
            }

            //查询方案是否可以修改
            if ($info['status'] != 0) {
                $this->error('方案不允许被修改');
            }


            $map['status'] = array('neq', -1);
            //查询产品列表
            $goodsList = M('Goods')->field('id,name')->where($map)->select();
            $info['pic'] = json_decode($info['pic'], true);

            $this->meta_title = '编辑方案';
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
        $m = M('PlanSell');
        $info = $m->find($id);
        if (!$info) {
            $this->error('未找到该方案');
        }
        //检查当前方案是否已被占用
        $gmap = array();
        $gmap['goods.plan_id'] = array('like', '%|' . $id . '|%');
        $gmap['orders.order_status'] = array('in', array(2, 3));
        $check = M('OrderGoods')->alias('goods')
            ->join('__ORDER__ orders on orders.order_id = goods.order_id', 'RIGHT')
            ->field('*')->where($gmap)->select();
        if ($check) {
            $this->error('请先转移已使用当前方案的用户');
        }
        //删除未发布方案
        if ($info['status'] == 0 && $m->save($data)) {
            $this->success('删除成功');
        }

        //删除已经发布但没有订单的方案
        if ($info['status'] == 1) {
            $map['plan_id'] = $id;
            $map['status'] = 2;
            $res = M('SellOrder')->where($map)->find();
            if (!$res) {
                if ($m->save($data)) {
                    $this->success('删除成功');
                }
            }
        }

        $this->error('删除失败！');
    }

    /**
     * 获取农场分区
     */
    public function getblock()
    {
        if (IS_POST) {
            $farm_id = I('id');
            if ($farm_id) {
                $map['status'] = 1;
                $map['farm_id'] = $farm_id;
                $map['area_used'] = array('gt', 0);
                $blockList = M('FarmBlock')->field(true)->where($map)->select();
                if (empty($blockList)) {
                    $this->error('未找到数据,无法选择');
                } else {
                    $cameraMap['status'] = 1;
                    $cameraMap['farm_id'] = $farm_id;
                    $cameraList = M('Camera')->field('id,title')->where($cameraMap)->select();
                    $list['block'] = $blockList;
                    $list['camera'] = $cameraList;
                    $this->ajaxReturn($list);
                }
            } else {
                $this->error('找不到分区');
            }
        } else {
            $this->display('add');
        }
    }

    /**
     * 编辑状态
     */
    public function dopush()
    {
        $id = intval(I('id'));
        $status = intval(I('status'));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        if (!in_array($status, array(-1, 0, 1))) {
            $this->error('请选择要操作的数据!');
        }
        //查询当前方案是否已被占用
        if ($status != 0) {
            $gmap = array();
            $gmap['goods.plan_id'] = array('like', '%|' . $id . '|%');
            $gmap['orders.order_status'] = array('in', array(2, 3));
            $check = M('OrderGoods')->alias('goods')
                ->join('__ORDER__ orders on orders.order_id = goods.order_id', 'RIGHT')
                ->field('*')->where($gmap)->select();
            if (!empty($check)) {
                $this->error('请先转移已使用当前方案的用户');
            }
        }

        //更新方案状态为1
        $data['id'] = $id;
        $data['status'] = $status;
        if (M('PlanSell')->save($data)) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
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
     * 方案编号生成
     * 0~2   01 种子方案 02 存储方案 03 折扣方案 04 销售方案
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
        return '04' . date('ymd') . $rand . $his . $static;
    }

    /**
     * 发布养殖方案
     */
    public function release()
    {
        //获取当前订单的产品列表ID
        if (IS_POST) {
            $ids = I('goods_id', array());
            $plan_id = I('plan_id', 0);
            if ($ids && $plan_id) {
                //修改每一个产品IDs
                $map['id'] = array('in', $ids);
                $res = true;
                //便利获取每一个订单产品的信息
                foreach ($ids as $key => $val) {
                    $condition = array();
                    $condition['id'] = $val;
                    $info = M('OrderGoods')->field('*')->where($condition)->find();
                    $map = array();
                    $map['id'] = $val;
                    $data['plan_id'] = empty($info['plan_id']) ? '|' . $plan_id . '|' : $info['plan_id'] . $plan_id . '|';
                    $res = M('OrderGoods')->where($map)->save($data);
                }
                if ($res) {
                    $this->success('发布成功', U('index'));
                } else {
                    $this->error('发布失败', U('index'));
                }
            }

        } else {
            //查询出当前养殖方案的产品ID
            $id = I('id', '', 'intval');
            $goods_id = I('gid', '', 'intval');
            if (empty($id) || empty($goods_id)) $this->error('请按照正确的方式操作', U('index'));
            //todo 检查当前方案是否存在
            //获取当前养殖方案信息
            $pmap = array();
            $pmap['plan.id'] = $id;
            $pmap['plan.goods_id'] = $goods_id;
            $pmap['plan.status'] = 0;
            $info = M('PlanSell')->alias('plan')->join('__GOODS__ goods on goods.id = plan.goods_id')->field("plan.*,goods.name")->where($pmap)->find();
            if (empty($info)) {
                $this->error('当前方案已禁用或不存在,请重新选择');
            }
            //获取已付款订单以及已养殖订单中包含有单签产品信息的订单(已支付、养殖中)
            $map['orders.order_status'] = array('in', array(2, 3));
            $map['goods.goods_id'] = $goods_id;
            $map['goods.plan_id'] = array('notlike', '%|' . $id . '|%');
            $field = 'orders.order_sn,goods.id,goods.goods_name,member.user_name';
            $list = M('OrderGoods')->alias('goods')->field($field)
                ->join('__ORDER__ orders on orders.order_id = goods.order_id', 'RIGHT')
                ->join('__USER__ member on member.uid = orders.uid', 'LEFT')
                ->where($map)
                ->select();

            $this->meta_title = '发布养殖方案';
            $this->assign('lists', $list);
            $this->assign('plan_id', $id);
            $this->assign('goods_name', isset($info['name']) ? $info['name'] : '');
            $this->display();
        }
    }



}