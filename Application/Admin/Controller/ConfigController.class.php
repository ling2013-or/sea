<?php
namespace Admin\Controller;

/**
 * 后台配置控制器
 * Class ConfigController
 * @package Admin\Controller
 */
class ConfigController extends AdminController
{

    /**
     * 配置列表
     */
    public function index()
    {

        $map = array('status' => 1);
        if (isset($_GET['group'])) {
            $map['group'] = I('group', 0);
        }
        if (isset($_GET['name'])) {
            $map['name'] = array('like', '%' . (string)I('name') . '%');
        }

        $total = M('Config')->where($map)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('Config')->field(true)->where($map)->limit($limit)->order('sort,id')->select();
        $this->meta_title = '配置管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加配置
     */
    public function add()
    {
        if (IS_POST) {
            $Config = D('Config');
            $data = $Config->create();
            if ($data) {
                if ($Config->add()) {
                    S('DB_CONFIG_DATA', null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->display();
        }
    }

    /**
     * 编辑配置
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Config = D('Config');
            $data = $Config->create();
            if ($data) {
                if ($Config->save()) {
                    S('DB_CONFIG_DATA', null);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);
            if (false === $info) {
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 删除配置
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (M('Config')->where($map)->delete()) {
            S('DB_CONFIG_DATA', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 获取某个标签的配置参数
     */
    public function group()
    {
        $id = I('get.id', 1);
        $type = C('CONFIG_GROUP_LIST');
        $list = M("Config")->where(array('status' => 1, 'group' => $id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if ($list) {
            $this->assign('list', $list);
        }
        $this->assign('id', $id);
        $this->meta_title = $type[$id] . '设置';
        $this->display();
    }

    /**
     * 批量保存配置
     * @param   array   $config     配置文件
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA', null);
        $this->success('保存成功！');
    }
}