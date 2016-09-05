<?php
namespace Admin\Controller;

/**
 * 摄像头管理
 * Class CameraController
 * @package Admin\Controller
 */
class CameraController extends AdminController
{
    /**
     * 摄像头管理列表
     */
    public function index()
    {
        // TODO 搜索条件
        $condition = array();
        $condition['camera.status'] = array('neq', -1);
        $title = I('title', '', 'trim');
        if (!empty($title)) {
            $condition['camera.title'] = array('LIKE', '%' . $title . '%');
        }

        $id = I('camera_id', '', 'trim');
        if (!empty($id)) {
            $condition['camera.camera_id'] = array('LIKE', '%' . $id . '%');
        }

        $total = M('Camera')
            ->alias('camera')
            ->join('__GOODS_ZONE__ AS zone ON zone.id = camera.zone_id', 'LEFT')
            ->where($condition)
            ->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('Camera')
            ->alias('camera')
            ->join('__GOODS_ZONE__ AS zone ON camera.zone_id = zone.id', 'LEFT')
            ->field('camera.*,zone.title as ztitle')
            ->where($condition)
            ->limit($limit)
            ->order('id DESC')
            ->select();
        $this->meta_title = '视频管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 摄像头添加
     */
    public function add()
    {
        if (IS_POST) {
            $Model = D('Camera');
            if ($Model->create()) {
                $res = $Model->add();
                if ($res) {
                    $this->success('添加摄像头成功', Cookie('__forward__'));
                } else {
                    $this->error('添加摄像头失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 获取分区列表
            $farm = M('GoodsZone')->field('id,title')->select();
            if (!$farm) {
                $this->error('请先添加分区', U('Goodszone/index'));
            }
            $this->meta_title = '添加监控';

            $this->assign('farm', $farm);
            $this->display();
        }
    }

    /**
     * 摄像头编辑
     */
    public function edit()
    {
        $id = I('id', 0, 'intval');
        if (empty($id)) {
            $this->error('请选择要编辑的摄像头');
        }
        // 检测是摄像头是否存在
        $info = M('Camera')->where(array('id' => $id))->find();
        if (!$info) {
            $this->error('请选择要编辑的摄像头');
        }
        if (IS_POST) {
            $Model = D('Camera');
            if ($data = $Model->create()) {

                $res = $Model->where(array('id' => $id))->save();
                if ($res) {
                    $this->success('编辑摄像头成功', Cookie('__forward__'));
                } else {
                    $this->error('编辑摄像头失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            // 获取分区列表
            $farm = M('GoodsZone')->field('id,title')->select();
            if (!$farm) {
                $this->error('请先添加分区', U('Goodszone/index'));
            }
//            dump($farm);die;
            $this->meta_title = '编辑监控';

            $this->assign('farm', $farm);
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 摄像头删除
     */
    public function del()
    {
        $id = I('id', 0, 'intval');
        //查看当前这个视频下面是否存在用户
        //查询当前视频有无用户正在使用
        $cmap = array();
        $cmap['orders.order_status'] = array('in', array(2, 3));
        $cmap['goods.camera_id'] = $id;
        $check = M('OrderGoods')->alias('goods')->join('__ORDER__ orders on goods.order_id = orders.order_id','RIGHT')->field('*')->where($cmap)->select();
        if ($check) {
            $this->error('当前摄像头已被使用使用');
        }
        $res = M('Camera')->where(array('id' => $id))->save(array('status' => -1));
        if (false === $res) {
            $this->error('删除摄像头失败');
        } else {
            $this->success('删除摄像头成功');
        }
    }

    /**
     * 修改摄像头状态
     */
    public function state()
    {
        $id = I('id', 0, 'intval');
        $status = I('status', 0, 'intval');
        if ($status == 0) {
            $status = 1;
        } else {
            $status = 0;
        }
        if ($status != 0) {
            //查询当前视频有无用户正在使用
            $cmap = array();
            $cmap['orders.order_status'] = array('in', array(2, 3));
            $cmap['goods.camera_id'] = $id;
            $check = M('OrderGoods')->alias('goods')->join('__ORDER__ orders on goods.order_id = orders.order_id','RIGHT')->field('*')->where($cmap)->select();
            if ($check) {
                $this->error('当前摄像头已被使用使用');
            }
        }

        $res = M('Camera')->where(array('id' => $id))->save(array('status' => $status));
        if (false === $res) {
            $this->error('更改摄像头状态失败');
        } else {
            $this->success('更改摄像头状态成功');
        }
    }

    /**
     * 检测摄像头覆盖量是否在分区的允许范围内
     * @param $id @分区ID
     * @param $num @当前摄像头覆盖量
     * @return bool
     */
    public function SumStock()
    {
        $model = M('Camera');
        $data = $model->create();
        if (isset($data['id'])) {
            $map['id'] = array('neq', $data['id']);
        }
        $id = $data['zone_id'];
        $num = $data['stock'];
        $map['status'] = array('neq', -1);
        $map['zone_id'] = $id;
        $info = $model->field('sum(stock) as total')->where($map)->find();
        $sum = isset($info['total']) ? $info['total'] : '';
        $zone = M('GoodsZone');
        $condition['id'] = $id;
        $result = $zone->field('total_stock')->where($condition)->find();
        $total = isset($result['total_stock']) ? $result['total_stock'] : '';
        $check = $num + $sum;
        $return = true;
        if ($check > $total) {
            $return = false;
        }
        return $return;
    }

    /**
     * 发布视频
     */
    public function release()
    {
        //获取当前订单的产品列表ID
        if (IS_POST) {
            $ids = I('goods_id', array());
            $camera_id = I('camera_id', 0);
            if (empty($camera_id)) $this->error('视频不存在', U('index'));
            if (empty($ids)) $this->error('请选择您要发布的对象');
            //修改每一个产品IDs
            $map['id'] = array('in', $ids);
            $map['camera_id'] = 0;
            $data = array('camera_id' => $camera_id);
            if (M('OrderGoods')->where($map)->save($data)) {
                $this->success('发布成功', U('index'));
            } else {
                $this->error('发布失败', U('index'));
            }

        } else {
            //查询出当前养殖方案的产品ID
            $id = I('id', '', 'intval');
            $zone = I('zid', '', 'intval');
            if (empty($id) || empty($zone)) $this->error('请按照正确的方式操作', U('index'));
            //获取视频详情(正常状态下的信息)
            $cmap = array();
            $cmap['zone_id'] = $zone;
            $cmap['id'] = $id;
            $cmap['status'] = 0;
            $info = M('Camera')->field('*')->where($cmap)->find();
            if (empty($info)) {
                $this->error('发布失败，请重新发布', U('index'));
            }
            //获取已付款订单以及已养殖订单中包含有单签产品信息的订单(已支付、养殖中)
            $map['orders.order_status'] = array('in', array(2, 3));
            $map['goods.zone_id'] = $zone;
            $map['goods.camera_id'] = 0;
            $field = 'orders.order_sn,goods.id,goods.goods_name,member.user_name';
            $list = M('OrderGoods')->alias('goods')->field($field)
                ->join('__ORDER__ orders on orders.order_id = goods.order_id', 'RIGHT')
                ->join('__USER__ member on member.uid = orders.uid', 'LEFT')
                ->where($map)
                ->select();

            $this->meta_title = '发布养殖方案';
            $this->assign('lists', $list);
            $this->assign('camera', $info);
            $this->display();
        }
    }
}