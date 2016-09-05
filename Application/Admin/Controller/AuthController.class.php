<?php
namespace Admin\Controller;

/**
 * 权限管理
 *
 * 网站访问权限管理以及侧边栏菜单管理
 */
class AuthController extends AdminController
{

    /**
     * 权限列表
     */
    public function index()
    {
        //默认查询条件
        $map['status'] = 1;
        $map['parent_id'] = 0;
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            unset($map['parent_id']);
            $map['module_name'] = array('like', '%' . (string)I('name') . '%');
        }

        //查询子菜单
        $parent_id = I('parent_id', 0, 'intval');
        if ($parent_id) {
            $map['parent_id'] = $parent_id;
            //查询当前模块信息
            $module_info = M('ModuleAuth')->field('module_name,parent_id')->find($parent_id);
            $this->assign('info', $module_info);
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('ModuleAuth')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('ModuleAuth')->field(true)->where($map)->limit($limit)->order('is_menu DESC,module_index ASC')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '权限管理';
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加权限
     */
    public function add()
    {
        if (IS_POST) {
            $Auth = M('ModuleAuth');
            $data = $Auth->create();
            if (empty($data['module_bind'])) {
                $this->error('绑定地址不能为空');
            }
            $Auth->add_time = $Auth->update_time = NOW_TIME;
            if ($data) {
                if ($Auth->add()) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Auth->getError());
            }
        } else {
            //获取顶级菜单
            $map['status'] = 1;
            $map['is_menu'] = 1;
            $res = M('ModuleAuth')->field('module_id,module_name,parent_id')->where($map)->order('module_index')->select();
            foreach ($res as $key => $v) {
                if ($v['parent_id'] == 0) {
                    $top[] = $v;
                }
            }

            $this->meta_title = '新增权限';
            $this->assign('moduleIndex', $top);
            $this->display();
        }
    }

    /**
     * 编辑权限
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Auth = M('ModuleAuth');
            $data = $Auth->create();
            if (empty($data['module_bind'])) $this->error('绑定地址不能为空');
            $Auth->update_time = NOW_TIME;
            if ($data) {
                if ($Auth->save()) {
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Auth->getError());
            }
        } else {
            /* 获取数据 */
            $info = M('ModuleAuth')->field(true)->find($id);
            if (!$info) {
                $this->error('获取权限信息失败');
            }

            //获取顶级菜单
            $map['status'] = 1;
            $map['is_menu'] = 1;
            $res = M('ModuleAuth')->field('module_id,module_name,parent_id')->where($map)->order('module_index')->select();
            foreach ($res as $key => $v) {
                if ($v['parent_id'] == 0) {
                    $top[] = $v;
                }
            }

            $this->meta_title = '编辑权限';
            $this->assign('moduleIndex', $top);
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 删除权限
     */
    public function del()
    {
        $id = I('id', 0);
        if (!$id) {
            $this->error('请选择要操作的数据!');
        }

        $data['module_id'] = $id;
        $data['status'] = -1;
        if (M('ModuleAuth')->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}