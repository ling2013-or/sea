<?php
namespace Game\Controller;

class IndexController extends \Think\Controller
{
    public function index()
    {
        $this->isLogin();
    }

    public function _empty()
    {
        echo M('GameUserTask')->fetchSql(true)->add(array('user_id'=>1, 'task_id'=>2), array(), true);
    }
}