<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 18:27
 */

namespace Admin\Controller;

use Think\Page;
class UserAddressController extends AdminController{
    public function index(){
        $uid = I('uid',0,'intval');
        //获取用户的信息，以及地址信息
        $Model = D('UserAddress');
        $map = [];
        $map['uid'] = $uid;

        $total = $Model->where($map)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $condition = [];
        $condition['uid'] = $uid;
        $condition['status'] = array('neq',-1);
        $list = $Model->field('*')->where($condition)->order('is_default,id DESC')->limit($limit)->select();
        $this->meta_title = '养殖方案列表';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('page', $p ? $p : '');
        $this->assign('today',NOW_TIME);
        $this->assign('lists', $list);
        $this->display();

    }
} 