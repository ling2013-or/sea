<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/18
 * Time: 22:19
 */

namespace Admin\Controller;

use Think\Controller;

class PlanController extends AdminController
{

    public function index()
    {
        $map = [];
        $map['status'] = array('eq', 0);
        if (isset($_GET['name']) && $_GET['name'] !== '') {
            $map['plan_name'] = array('like', '%' . (string)I('name') . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $total = M('GoodsPlan')->where($map)->count();
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        //数据列表
        $lists = M('GoodsPlan')->field(true)->where($map)->limit($limit)->order('status,create_time DESC')->select();

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '养殖方案列表';

        $this->assign('page', $p ? $p : '');
        $this->assign('today', NOW_TIME);
        $this->assign('lists', $lists);
        $this->display();
    }
} 