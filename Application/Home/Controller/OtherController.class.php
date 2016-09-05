<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/24
 * Time: 10:52
 */

namespace Home\Controller;


class OtherController extends HomeController{

    /**
     * 公司简介
     */
    public function synopsis(){
        $this->meta_title = '公司简介';
        $this->display();
    }

    /**
     * 关于我们
     */
    public function connect(){
        //获取轮播图片
//        $carousel = $this->getCarousel(CONTROLLER_NAME, ACTION_NAME);
//        $this->assign('imgs',$carousel['carousel']['img']);
        $this->meta_title = '联系我们';
        $this->display();
    }

    /**
     * 来访地图
     */
    public function map(){
        //获取轮播图片
//        $carousel = $this->getCarousel(CONTROLLER_NAME, ACTION_NAME);
//        $this->assign('imgs',$carousel['carousel']['img']);
        $this->meta_title = '来访地图';
        $this->display();
    }

    public function privacy(){
        //获取轮播图片
//        $carousel = $this->getCarousel(CONTROLLER_NAME, ACTION_NAME);
//        $this->assign('imgs',$carousel['carousel']['img']);
        $this->meta_title = '会员福利';
        $this->display();
    }

} 