<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 活动
 * Class ArticleController
 * @package Home\Controller
 */

class ArticleController extends Controller
{

    /**
     * 活动列表
     */
    public function index()
    {
        $this->display();
    }
    
    /*文章详情*/   
    public function detail()
    {
        $this->display();
    }

    /*物流跟踪*/
    public function express_timeline()
    {
        $this->display();
    }


    /*单个产品展示*/
    public function good_detail()
    {
        $this->display();
    }
}