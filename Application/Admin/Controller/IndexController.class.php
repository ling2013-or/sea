<?php
namespace Admin\Controller;

/**
 * 后台管理平台
 * Class IndexController
 * @package Admin\Controller
 */
class IndexController extends AdminController
{
    public function index()
    {
//        C('SHOW_PAGE_TRACE', 0);    // 临时关闭trace
    	$this->display();
    }
}