<?php
namespace Admin\Controller;

/**
 * 存储方案管理
 * Class StoragePlanController
 * @package Admin\Controller
 */
class StorageplanController extends AdminController
{

    /**
     * 方案列表
     */
    public function index()
    {   
        //查询条件
        $map['status'] = 1;
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['storage_name'] = array('like', '%' . I('name') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('PlanStorage')->alias('p')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        
        //数据列表
        $lists = M('PlanStorage')->where($map)->limit($limit)->order('storage_id')->select();
        
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '方案列表';
        
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加存储方案
     */
    public function add()
    {
        if (IS_POST) {
            $Plan = M('PlanStorage');
            $data = $Plan->create();
            if (empty($data['storage_name'])) $this->error('方案名称不能为空');
            if (empty($data['storage_time'])) $this->error('存储时间不能为空');

            //过滤空内容
            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }

            $data['add_time'] = $data['update_time'] = NOW_TIME;
            if ($Plan->add($data)) {
                $this->success('新增成功', U('index'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $this->meta_title = '新增方案';
            $this->display();
        }  
    }

    /**
     * 编辑存储方案
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Plan = M('PlanStorage');
            $data = $Plan->create();
            if (empty($data['storage_name'])) $this->error('方案名称不能为空');
            if (empty($data['storage_time'])) $this->error('存储时间不能为空');

            //过滤空内容
            foreach ($data as $k => $v) {
                if ($v == '') {
                    unset($data[$k]);
                }
            }
            $data['update_time'] = time();
            if ($Plan->save($data)) {
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('更新失败');
            }
        } else {
            /* 获取数据 */
            $info = M('PlanStorage')->field(true)->find($id);
            if (!$info) {
                $this->error('获取方案信息失败');
            }

            $this->meta_title = '编辑方案';
            $this->assign('info', $info);
            $this->display();
        }  
    }

    /**
     * 删除方案
     */
    public function del()
    {
        $id = intval(I('id'));
        if (!$id) {
            $this->error('请选择要操作的数据!');
        }

        $map['status'] = array('IN',array(0,1));
        $maps[]['storage_id'] = array('LIKE',"%,".$id.",%");
        $maps[]['storage_id'] = $id;
        $maps[]['storage_id'] = array('LIKE',"%,".$id);
        $maps['_logic'] = 'OR';
        $map[] = $maps;

        //查看是否使用中
        $status = M('PlanSell')->where($map)->find();
        if ($status) {
            $this->error('方案已经被使用,不可以删除');
        }

        $data['storage_id'] = $id;
        $data['status'] = -1;
        if (M('PlanStorage')->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        } 
    }
}