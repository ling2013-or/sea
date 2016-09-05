<?php
namespace Admin\Controller;

/**
 * 种子管理
 * Class SeedController
 * @package Admin\Controller
 */
class SeedController extends AdminController
{

    /**
     * 种子列表
     */
    public function index()
    {
        //查询条件
        $map = array('status' => 1);
        $name = I('name', '', 'trim');
        if (!empty($name)) {
            $map['seed_name'] = array('like', '%' . $name . '%');
        }

        $sn = I('sn', '', 'trim');
        if (!empty($sn)) {
            $map['seed_sn'] = array('like', '%' . $sn . '%');
        }
        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('Seed')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('Seed')->field(true)->where($map)->limit($limit)->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '种子列表';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加种子
     */
    public function add()
    {
        if (IS_POST) {
            $Model = D('Seed');
            if ($Model->create()) {
                if($Model->add()) {
                    $this->success('添加种子信息成功', Cookie('__forward__'));
                } else {
                    $this->error('添加种子信息失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->meta_title = '新增种子';
            $this->display();
        }
    }

    /**
     * 编辑种子
     */
    public function edit()
    {
        if (IS_POST) {
            $id = I('seed_id', 0, 'intval');
            if (empty($id)) {
                $this->error('请选择要编辑的种子信息');
            }
            $Model = D('Seed');
            if ($Model->create()) {
                if(false !== $Model->where(array('seed_id'=>$id))->save()) {
                    $this->success('修改种子信息成功', Cookie('__forward__'));
                } else {
                    $this->error('修改种子信息失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $id = I('id', 0, 'intval');
            if (empty($id)) {
                $this->error('请选择要编辑的种子信息');
            }
            /* 获取数据 */
            $info = M('Seed')->field(true)->where(array('seed_id' => $id))->find();
            if (!$info) {
                $this->error('获取种子信息失败');
            }
            $info['seed_img'] = json_decode($info['seed_img'], true);
            $this->meta_title = '编辑种子';
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 删除种子
     * @param int $id 种子ID
     */
    public function del($id = 0)
    {
        if (!$id) {
            $this->error('请选择要操作的数据!');
        }

        $map['seed_id'] = $id;
        $map['status'] = array('IN', array(0, 1));
        //查看是否使用中
        $status = M('PlanSell')->where($map)->find();
        if ($status) {
            $this->error('方案已经被使用,不可以删除');
        }

        $data['seed_id'] = $id;
        $data['status'] = -1;
        if (false !== M('Seed')->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


}