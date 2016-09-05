<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Page;

/**
 * 农场信息控制器
 * Class FarmController
 * @package Admin\Controller
 */
class FarmController extends AdminController
{
    /**
     * 农场信息列表
     */
    public function farm()
    {
        $condition = array();
        $condition['farm.status'] = 1;
        $farm_name = I('farm_name', '', 'trim');
        if (!empty($farm_name)) {
            $condition['farm.farm_name'] = array('like', '%' . $farm_name . '%');
        }

        $owner_mobile = I('mobile', '', 'trim');
        if (!empty($owner_mobile)) {
            $condition['farm.owner_mobile'] = array('like', '%' . $owner_mobile . '%');
        }

        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('Farm')->alias('farm')->where($condition)->count();
        $page = new Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $lists = M('Farm')->alias('farm')
            ->join("__AREA__ p ON farm.province = p.area_id", 'LEFT')
            ->join("__AREA__ c ON farm.city = c.area_id", 'LEFT')
            ->join("__AREA__ a ON farm.county = a.area_id", 'LEFT')
            ->field('farm.*, p.area_name as province, c.area_name as city, a.area_name as area')
            ->where($condition)
            ->order('farm.farm_id DESC')
            ->limit($limit)
            ->select();

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '农场列表';

        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->display();
    }

    /**
     * 农场添加
     */
    public function farmadd()
    {
        if (IS_POST) {
            $Model = D('Farm');
            if ($Model->create()) {
                if ($Model->add()) {
                    $this->success('添加农场成功', Cookie('__forward__'));
                } else {
                    $this->error('添加农场失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->meta_title = '添加农场';
            $this->display();
        }
    }

    /**
     * 农场信息修改
     */
    public function farmedit()
    {
        if (IS_POST) {
            $farm_id = I('post.farm_id', 0, 'intval,abs');
            if (empty($farm_id)) {
                $this->error('请选择要编辑的农场');
            }

            $Model = D('Farm');
            if ($Model->create()) {
                if (false === $Model->where(array('farm_id' => $farm_id))->save()) {
                    $this->error('编辑农场信息失败');
                } else {
                    $this->success('编辑农场信息成功', Cookie('__forward__'));
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $farm_id = I('farm_id', 0, 'intval,abs');
            if (empty($farm_id)) {
                $this->error('请选择要编辑的农场');
            }

            $info = M('Farm')->where(array('farm_id' => $farm_id))->find();
            if (!$info) {
                $this->error('要编辑的农场不存在');
            }
            $this->assign('info', $info);

            $this->meta_title = '编辑农场';
            $this->display();
        }
    }

    /**
     * 删除农场（假删除、修改状态值）
     */
    public function farmdel()
    {
        $farm_id = I('farm_id', 0, 'intval,abs');
        if (empty($farm_id)) {
            $this->error('请选择要删除的农场');
        }

        /**
         * 检测销售方案是否存在
         */
        $check = M('PlanSell')->field('plan_id')->where(array('farm_id' => $farm_id, 'status' => array('NEQ', -1)))->find();
        if ($check) {
            $this->error('此农场已经开始出售，禁止删除');
        }

        $data = array('status' => -1, 'update_time' => NOW_TIME);
        $result = M('Farm')->where(array('farm_id' => $farm_id))->save($data);
        if (false !== $result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 农场分区详情
     */
    public function block()
    {
        $farm_id = I('farm_id', 0, 'intval');
        if (empty($farm_id)) {
            $this->error('请选择要查看的农场');
        }

        $condition = array('block.farm_id' => $farm_id, 'block.status' => 1);
        $block_name = I('block_name', '', 'trim');
        if (!empty($block_name)) {
            $condition['block.block_name'] = array('like', '%' . $block_name . '%');
        }

        $block_sn = I('block_sn', '', 'trim');
        if (!empty($block_sn)) {
            $condition['block.block_sn'] = array('like', '%' . $block_sn . '%');
        }

        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('FarmBlock')->alias('block')->where($condition)->count();
        $page = new Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $lists = M('FarmBlock')
            ->alias('block')
            ->join('__FARM_TYPE__ AS type ON block.type_id = type.type_id')
            ->where($condition)
            ->order('block.block_id ASC')
            ->limit($limit)
            ->select();

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->assign('farm_id', $farm_id);
        $this->meta_title = '农场详情';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 添加农场分区
     */
    public function blockAdd()
    {
        // 检测农场信息是否存在
        $farm_id = I('farm_id', 0, 'intval');
        if (empty($farm_id)) {
            $this->error('此农场不存在');
        }
        $farm = M('Farm')->field('farm_id,farm_name')->where(array('farm_id' => $farm_id, 'status' => 1))->find();
        if (!$farm) {
            $this->error('此农场不存在');
        }

        if (IS_POST) {
            $Model = D('FarmBlock');
            if ($Model->create()) {
                if ($Model->add()) {
                    $this->success('添加成功', Cookie('__forward__'));
                } else {
                    $this->error('添加失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $type = M('FarmType')->where(array('status' => 1))->select();
            $this->assign('type', $type);
            $this->assign('farm', $farm);
            $this->meta_title = '添加分区';
            $this->display();
        }
    }

    /**
     * 编辑农场分区
     */
    public function blockedit()
    {
        // 检测农场信息是否存在
        $farm_id = I('farm_id', 0, 'intval');
        if (empty($farm_id)) {
            $this->error('此分区所属农场不存在');
        }
        $farm = M('Farm')->field('farm_id,farm_name')->where(array('farm_id' => $farm_id, 'status' => 1))->find();
        if (!$farm) {
            $this->error('此分区所属农场不存在');
        }

        $block_id = I('block_id', 0, 'intval');
        if (!$block_id) {
            $this->error('请选择要编辑的分区');
        }

        if (IS_POST) {
            $Model = D('FarmBlock');
            if ($Model->create()) {
                if (false !== $Model->where(array('block_id' => $block_id, 'farm_id' => $farm_id))->save()) {
                    $this->success('修改成功', Cookie('__forward__'));
                } else {
                    $this->error('修改失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $info = M('FarmBlock')->where(array('block_id' => $block_id, 'status' => 1, 'farm_id' => $farm_id))->find();
            if (!$info) {
                $this->error('农场分区不存在');
            }
            $type = M('FarmType')->where(array('status' => 1))->select();
            $this->meta_title = '编辑分区';
            $this->assign('info', $info);
            $this->assign('type', $type);
            $this->assign('farm', $farm);
            $this->display();
        }
    }

    /**
     * 删除农场分区（假删除、修改状态值）
     */
    public function blockdel()
    {
        $block_id = I('block_id', 0, 'intval');
        if (!$block_id) {
            $this->error('请选择要删除的分区');
        }

        $check = M('PlanSell')->field('plan_id')->where(array('farm_id' => $block_id, 'status' => array('NEQ', -1)))->find();
        if ($check) {
            $this->error('此农场分区已经开始出售，禁止删除');
        }

        $data = array('status' => -1, 'update_time' => NOW_TIME);
        $result = M('FarmBlock')->where(array('block_id' => $block_id))->save($data);
        if (false !== $result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 分区类别列表
     */
    public function type()
    {
        $lists = M('FarmType')->where(array('status' => 1))->order('type_id DESC')->select();
        $this->assign('lists', $lists);
        $this->meta_title = '分类列表';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 添加分区类别
     */
    public function typeadd()
    {
        if (IS_POST) {
            $type_name = I('type_name', '', 'trim,htmlspecialchars');
            if (empty($type_name)) {
                $this->error('分类名称不能为空');
            }
            $data = array();
            $data['type_name'] = $type_name;
            $data['add_time'] = NOW_TIME;
            $data['update_time'] = NOW_TIME;
            $data['status'] = 1;
            $result = M('FarmType')->add($data);
            if ($result) {
                $this->success('添加成功', Cookie('__forward__'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->meta_title = '添加分类';
            $this->display();
        }
    }

    /**
     * 修改分区类别
     */
    public function typeedit()
    {
        $type_id = I('type_id', 0, 'intval');
        if (empty($type_id)) {
            $this->error('请选择要编辑的分类');
        }
        if (IS_POST) {
            $type_name = I('type_name', '', 'trim,htmlspecialchars');
            if (empty($type_name)) {
                $this->error('分类名称不能为空');
            }
            $data = array();
            $data['type_name'] = $type_name;
            $data['update_time'] = NOW_TIME;
            $result = M('FarmType')->where(array('type_id' => $type_id))->save($data);
            if (false !== $result) {
                $this->success('修改成功', Cookie('__forward__'));
            } else {
                $this->error('修改失败');
            }
        } else {
            $info = M('FarmType')->where(array('type_id' => $type_id))->find();
            if (!$info) {
                $this->error('分类信息不存在，请重新选择');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑分类';
            $this->display();
        }
    }

    /**
     * 删除分区类别（假删除、修改状态值）
     */
    public function typedel()
    {
        $type_id = I('type_id', 0, 'intval');
        if (empty($type_id)) {
            $this->error('请选择要删除的分类');
        }
        /**
         * 检测是否使用
         */
        $check = M('farm_type') - field('type_id')->where(array('type_id' => $type_id, 'status' => 1))->find();
        if ($check) {
            $this->error('此分类已使用，禁止删除');
        }

        $data = array('status' => -1, 'update_time' => NOW_TIME);
        $result = M('FarmType')->where(array('type_id' => $type_id))->save($data);
        if (false !== $result) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 编辑农场支持的物流公司
     */
    public function express()
    {
        $farm_id = I('farm_id', 0, 'intval');
        if (empty($farm_id)) {
            $this->error('请选择要操作的农场');
        }

        // 获取农场详情
        $farm = M('Farm')->field(true)->where(array('farm_id' => $farm_id, 'status' => 1))->find();
        if (!$farm) {
            $this->error('农场不存在');
        }

        if (IS_POST) {
            //实例化一个model
            $express_id = I('auth', '', 'trim');
            $data = array();
            if (is_array($express_id) && !empty($express_id)) {
                $data['express'] = implode(',', $express_id);
                $express_name = M('Express')->where(array('id' => array('IN', $data['express'])))->getField('name', true);
                $data['express_name'] = implode(',', $express_name);
            } else {
                $data['express'] = '';
                $data['express_name'] = '';
            }
            $res = M('FarmExpress')->where(array('farm_id' => $farm_id))->save($data);
            if (false !== $res) {
                $this->success('修改成功', Cookie('__forward__'));
            } else {
                $this->error('修改失败');
            }

        } else {

            $info = M('FarmExpress')->field(true)->where(array('farm_id' => $farm_id))->find();
            if (!$info) {
                $info = array(
                    'farm_id' => $farm_id,
                );
                if (!M('FarmExpress')->add($info)) {
                    $this->error('系统错误，请稍后重试');
                }
            }
            $express_lists = M('Express')->field(true)->where(array('status' => 1))->select();

            $express = explode(',', $info['express']);

            $this->assign('farm', $farm);
            $this->assign('info', $info);
            $this->assign('express_lists', $express_lists);
            $this->assign('express', $express);
            $this->meta_title = '选择物流';
            $this->display();
        }
    }

    /**
     * 农场自己的发货地址列表
     */
    public function address()
    {
        $where = array();
        //搜索
        if (isset($_GET['query'])) {
            $query = I('query');
            $map['t1.id'] = array('eq', $query);
            $map['t1.telphone'] = array('eq', $query);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('Daddress')->alias('t1')->join('__FARM__ t2 ON t1.farm_id = t2.farm_id', 'LEFT')->where($where)->count();
        //实例化分页类
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取所有的服务地址
        $lists = M('Daddress')->alias('t1')->field('t1.*,t2.farm_name')
            ->join('__FARM__ t2 ON t1.farm_id = t2.farm_id', 'LEFT')
            ->where($where)
            ->order('t1.id ASC')
            ->limit($limit)
            ->select();
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '地址列表';
        $this->display();

    }


    /**
     * 添加农场发货地址
     */
    public function addDelivery()
    {
        if (IS_POST) {
            //实例化model
            $message = D('Daddress');
            //验证数据是否正常
            if ($message->create()) {
                $pname = get_area_name($_POST['p_id']);
                $city = get_area_name($_POST['city_id']);
                $area = get_area_name($_POST['area_id']);
                $address = $pname . '　' . $city . '　' . $area . '　' . $message->area_info;
                $message->address = $address;
                //在设置默认的发货地址之前，去掉以前的默认地址
                if ($_POST['is_defaule'] == 1) {
                    $map['farm_id'] = $_POST['farm_id'];
                    $default['is_defaule'] = 0;
                    //将当前农场之前的默认地址去除
                    M('Daddress')->where($map)->save($default);
                }
                //将数据插入数据库
                $message->add();
                $this->success('发货地址添加成功！', U('address'));
            } else {
                $this->error($message->getError());
            }

        }
        $where = array();
        $where['status'] = array('neq', '-1');
        //获取所有的农场信息
        $farms = M('Farm')->field('farm_name,farm_id')->where($where)->select();
        //获取地区信息
        $province = M('area')->where("area_deep=1")->select();
        $this->meta_title = '添加发货地址';
        $this->assign('province', $province);
        $this->assign('farms', $farms);
        $this->meta_title = '添加发货地址';
        $this->display();
    }

    /**
     * 编辑发货地址信息
     * @param string $id 地址ID
     */
    public function editDelivery($id = '')
    {
        if (IS_POST) {

            //实例化一个model
            $message = D('Daddress');
            $data = $message->create();
            //判断值得格式
            if ($data) {
                $pname = get_area_name($_POST['p_id']);
                $city = get_area_name($_POST['city_id']);
                $area = get_area_name($_POST['area_id']);
                $address = $pname . '　' . $city . '　' . $area . '　' . $message->area_info;
                $message->address = $address;
                //在设置默认的发货地址之前，去掉以前的默认地址
                if ($_POST['is_defaule'] == 1) {
                    $map['farm_id'] = $_POST['farm_id'];
                    $default['is_defaule'] = 0;
                    //将当前农场之前的默认地址去除
                    M('Daddress')->where($map)->save($default);
                }
                //更新数据库
                $message->save();
                $this->success('修改成功', U('address'));
            } else {
                $this->error($message->getError());
            }


        }

        $where = array();
        $where['status'] = array('neq', '-1');
        //获取所有的农场信息
        $farms = M('Farm')->field('farm_name,farm_id')->where($where)->select();
        //获取地区信息
        $province = M('area')->where("area_deep=1")->select();
        $city = M('area')->where(array('area_deep' => 2))->select();
        $area = M('area')->where(array('area_deep' => 3))->select();

        //通过article获取文章信息
        $article = D('Daddress');
        $result = $article->lists();

        //设置标题
        $this->meta_title = '编辑发货地址';
        $this->assign('list', $result['0']);
        $this->assign('province', $province);
        $this->assign('farms', $farms);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->meta_title = '编辑发货地址';
        $this->display();
    }

    /**
     * 设置默认发货地址
     */
    public function setDefault()
    {
        if (IS_POST) {
            //地址ID
            $id = $_POST['id'];
            $fid = $_POST['fid'];
            $is_defaule = $_POST['val'];
            //实例化一个model
            $data = array('is_defaule' => $is_defaule);
            $where['id'] = array('eq', $id);
            $where['farm_id'] = array('eq', $fid);
            $map['farm_id'] = array('eq', $fid);
            $default['is_defaule'] = 0;
            //判断值得格式
            if ($data) {
                //将当前农场之前的默认地址去除
                M('Daddress')->where($map)->save($default);
                //将数据全部设为0

                //更新数据库（设置新的默认地址）
                M('Daddress')->where($where)->save($data);
                $this->success('修改成功', U('address'));
            } else {
                $this->error(M('Daddress')->getError());
            }
        } else {
            $this->error('非法请求');
        }
    }
}