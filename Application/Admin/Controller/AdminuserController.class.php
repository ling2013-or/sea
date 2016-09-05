<?php
namespace Admin\Controller;

/**
 * 管理员管理
 * Class AdminuserController
 * @package Admin\Controller
 */
class AdminuserController extends AdminController
{

    /**
     * 管理员列表
     */
    public function index()
    {
        //搜索条件
        $map['a.status'] = array('IN',array(0,1));
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['a.admin_name'] = array('like', '%' . I('name') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('Admin')->alias('a')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('Admin')->alias('a')->field('g.group_id,g.group_name,a.*')
        ->join('LEFT JOIN __GROUP__ g ON g.group_id=a.group_id')
        ->where($map)->limit($limit)->order('a.admin_id')->select();
        
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '管理员列表';

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function add()
    {
        if (IS_POST) {
            $Admin = M('Admin');

            //验证用户名是否已经存在
            $adminName = I('admin_name');
            if (empty($adminName)) $this->error('用户名不能为空');
            $res = $Admin->field('admin_id')->where("admin_name='{$adminName}'")->find();
            if ($res) $this->error('该用户已经存在!');

            //添加新用户
            $data = $Admin->create();
            $Admin->add_time = $Admin->update_time = time();
            
            //验证两次密码是否一致
            $newPwd = I('new_pwd');
            $vPwd = I('v_pwd');
            if ($newPwd===$vPwd && strlen($newPwd)>=6) {
                
                //生成salt
                $salt = substr(md5(uniqid()),8,16);
                $Admin->admin_salt = $salt;

                //更新密码和salt
                $Admin->admin_pwd = md5(md5($newPwd).$salt); 
            } else {
                $this->error('密码格式不正确');
            }

            //添加数据
            if ($data) {
                if ($Admin->add()) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Admin->getError());
            }
        } else {
            
            //查询管理组
            $map['status'] = array('IN',array(0,1));
            $group = M('Group')->field('group_name,group_id')->where($map)->select();
            $this->assign('group',$group);
            
            $this->meta_title = '新增管理员';
            $this->display();
        }  
    }

    /**
     * 编辑管理员
     * @param   int $id 待修改的ID
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $Admin = M('Admin');
            $data = $Admin->create();
            $data['update_time'] = time();

            
            //验证两次密码是否一致
            $newPwd = I('new_pwd');
            $vPwd = I('v_pwd');
            if ($newPwd) {
                if ($newPwd===$vPwd && strlen($newPwd)>=6) {
                    
                    //生成salt
                    $salt = substr(md5(uniqid()),8,16);
                    $data['admin_salt'] = $salt;

                    //更新密码和salt
                    $data['admin_pwd'] = md5(md5($newPwd).$salt); 
                } else {
                    $this->error('两次密码不一致或长度太短');
                }    
            }

            //获取输入的原密码
            /*$oldPwd = I('old_pwd');
            $id = I('admin_id');
            
            //验证原密码
            if ($oldPwd) {
                
                //验证两次密码长度和是否一致
                $newPwd = I('new_pwd');
                $vPwd = I('v_pwd');
                if ($newPwd===$vPwd && strlen($newPwd)>=6) {
                    $pwd = $Admin->field('admin_pwd,admin_salt')->find($id);
                    if(md5(md5($oldPwd).$pwd['admin_salt'])==$pwd['admin_pwd']){

                        //更新密码
                        $data['admin_pwd'] = md5(md5($newPwd).$pwd['admin_salt']);
                    }else{
                        $this->error('原密码输入错误');   
                    }
                }else{
                    $this->error('两次密码不一致或长度太短');
                }
            }*/

            //更新数据
            if ($data) {
                if ($Admin->save($data)) {
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Admin->getError());
            }
        } else {
            /* 获取数据 */
            $info = M('Admin')->field('admin_pwd,admin_salt',true)->find($id);
            if (!$info) {
                $this->error('获取管理员信息失败');
            }

            //查询管理组
            $map['status'] = array('IN',array(0,1));
            $group = M('Group')->field('group_name,group_id')->where($map)->select();

            $this->meta_title = '编辑管理员';
            $this->assign('group',$group);
            $this->assign('info', $info);
            $this->display();
        } 
    }

    /**
     * 删除管理员
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('admin_id' => array('in', $id));
        $data['status'] = -1;
        if (M('Admin')->where($map)->save($data)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        } 
    }

    /**
     * 启用/禁用管理员
     */
    public function operate()
    {
        $data['admin_id'] = intval(I('id'));
        $data['status'] = intval(I('status'));
        if (M('Admin')->save($data) !== false) {
            $this->success('修改成功!');
        } else {
            $this->error('修改失败!');
        }
    }

    /**
     * 图片上传
     */
    public function uploadImg()
    {
        $admin_id = I('uid',0);
        if ($admin_id) {
            $upload = new \Think\Upload(C('PICTURE_UPLOAD'));
            $upload->saveName = $admin_id;
            $upload->replace  = true;
            $info   =   $upload->upload();
            if(!$info) {
                $this->error($upload->getError());
            }else{
                foreach($info as $file){
                    $path = $file['savepath'].$file['savename'];
                }
                $imgUrl = __ROOT__.'/'.trim(C('PICTURE_UPLOAD.rootPath'),'./').'/'.$path;
                $this->ajaxReturn(array('status'=>1,'info'=>$imgUrl));
            }   
        } else {
            $this->ajaxReturn(array('status'=>0,'info'=>'未发现用户,上传失败!'));
        }
    }
}