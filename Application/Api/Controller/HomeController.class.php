<?php

namespace Api\Controller;

/**
 * 展示首页推荐产品
 * Class FeedbackController
 * @package Api\Controller
 */
class HomeController extends ApiController
{

    /**
     * 查询当前模块信息
     */
    public function index()
    {
        //获取首页轮播图片信息、活动页面信息
        $carousel = S('HOME_DATA_MORE');
        $model = D('Carousel');
        //轮播图
        $map = [];
        $map['model'] = 'Home/index';
        $map['status'] = 1;
        $map['type'] = 0;
        $carousel = $model->field('*')->where($map)->find();
        $carousel['img'] = json_decode($carousel['img'],true);
        //获取了轮播图片的个数
        $num = count($carousel);
        //活动图片
        $map['type'] = 1;
        $active = $model->field('*')->where($map)->select();

        $data = [
            'carousel' => $carousel,
            'num' => $num,
            'active' => $active,
        ];
        $this->apiReturn(0, '成功', $data);
    }
}