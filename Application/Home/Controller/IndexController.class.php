<?php
namespace Home\Controller;

//use Common\Library\Pay\Pay;
//use Common\Library\Pay\Pay\Param;
//use Think\Controller;

class IndexController extends HomeController
{
    /**
     * 查询当前模块信息
     */
    public function index()
    {
        $data = $this->getCarousel(CONTROLLER_NAME,ACTION_NAME);
        $this->meta_title = '首页';
        $this->assign('imgs',$data['carousel']['img']);
        $this->display();

    }


}