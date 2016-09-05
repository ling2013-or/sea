<?php
namespace Admin\Controller;

/**
 * 用户管理
 * Class GroupController
 * @package Admin\Controller
 */
class GroupController extends AdminController
{

    /**
     * 用户组列表
     */
    public function index()
    {
        //查询条件
        $map['status'] = array('IN',array(0,1));
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['group_name'] = array('like', '%' . I('name') . '%');
        }

        //数据分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('Group')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('Group')->field(true)->where($map)->limit($limit)->select();
        
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '用户组管理';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加用户组
     */
    public function add()
    {
        if (IS_POST) {
            $Group = M('Group');
            $data = $Group->create();
            if ($data['group_name'] == '') {
                $this->error('用户组名不能为空');
            }
            $Group->group_auth = implode(',',I('auth'));
            $Group->add_time = $Group->update_time = NOW_TIME;
            if ($Group->add()) {
                $this->success('新增成功', U('index'));
            } else {
                $this->error('新增失败');
            }

        } else {
            
            //查询网站所有权限
            $moduleList = M('ModuleAuth')->field(true)->where('status=1')->order('module_index')->select();
            $this->meta_title = '新增用户组';
            $this->assign('moduleList',$moduleList);
            $this->display();
        }  
    }

    /**
     * 编辑用户组
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Group = M('Group');
            $data = $Group->create();
            if ($data['group_name'] == '') {
                $this->error('用户组名不能为空');
            }
            $Group->update_time = NOW_TIME;
            $Group->group_auth = implode(',',I('auth'));
            if ($Group->save()) {
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('更新失败');
            }
        } else {
            $info = M('Group')->field(true)->find($id);
            if (!$info) {
                $this->error('获取用户组信息失败');
            }
            $groupAuth = explode(',',$info['group_auth']);
            
            //查询网站所有权限
            $moduleList = M('ModuleAuth')->field(true)->where('status=1')->order('module_index')->select();
            
            $this->meta_title = '编辑用户组';
            $this->assign('info', $info);
            $this->assign('moduleList',$moduleList);
            $this->assign('groupAuth',$groupAuth);
            $this->display();
        } 
    }

    /**
     * 删除用户组
     */
    public function del()
    {
        $id = intval(I('id'));
        if (!$id) {
            $this->error('请选择要操作的数据!');
        }

        //检验关联 判断是否可以被删除
        $adminmap['group_id'] =  $id;
        $adminmap['status'] = 1;
        $res = M('Admin')->field('admin_id')->where($adminmap)->select();
        if ($res) {
            $this->error('分组下存在管理员,不能删除');
        }

        //删除用户组
        $data['group_id'] = $id;
        $data['status'] = -1;
        if (M('Group')->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        } 
    }

    /**
     * 用户组管理员管理
     */
    public function admin()
    {
        if (IS_POST) {

            //获取群组ID
            $id = intval(I('id'));
            if (!$id) {
                $this->error('请选择要操作的数据!');
            }

            //获取成员列表
            $admin = array_unique((array)I('admin'));
            try {
                $m = M('Admin');
                $m->startTrans();

                //将当前组成员全部移出
                $map['group_id']  = $id;
                $data['group_id'] = 0;
                if ($m->where($map)->save($data) !== false) {

                    //重新添加管理员
                    $maps['admin_id'] = array('IN',$admin);
                    $datas['group_id'] = $id;
                    if ($m->where($maps)->save($datas) !== false) {
                        $m->commit();
                        $this->success('修改成功!');
                    }
                }
                throw new \Exception('修改失败!');
            } catch(\Exception $e) {
                $m->rollback();
                $this->error('修改失败!');
            }
        } else {

            //获取群组ID
            $id = intval(I('id'));
            if (!$id) {
                $this->error('请选择要操作的数据!');
            }

            //查询当前组所有管理员
            $map['group_id'] = $id;
            $map['status'] = array('neq', -1);
            $group_admin_list = M('Admin')->field('admin_id,admin_name')->where($map)->select();

            //查询网站所有管理员
            $maps['a.group_id'] = array('neq',$id);
            $maps['a.status'] = array('neq', -1);
            $admin_list = M('Admin')->alias('a')
                ->join('__GROUP__ g ON a.group_id=g.group_id','LEFT')
                ->field('a.admin_name,a.admin_id,g.group_name')
                ->where($maps)->select();

            $this->assign('group_admin_list',$group_admin_list);
            $this->assign('admin_list',$admin_list);
            $this->assign('id',$id);
            $this->display();
        }
    }

}