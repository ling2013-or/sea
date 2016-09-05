<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 11:17
 */

namespace Home\Controller;


class ShopController extends HomeController
{
    public function _initialize(){
        parent::_initialize();
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REFERER'];
        $this->isLogin();
    }

    //记录用户所选产品信息
    public function add()
    {
        if (IS_POST) {
            //获取产品
            $post = I('post.');
            $id = $post['id'];
            $model = D('Goods');
            $info = D('Goods')->goodsInfo($id, $post['variety']);
            if (!$info) {
                $this->error($model->getError());
                return false;
            }
            $data = array(
                'goods_id' => $info['id'],
                'goods_price' => $info['price'],
                'goods_name' => $info['name'],
                'goods_cover' => $info['picture'],
                'goods_type' => $info['goods_type'],
                'goods_num' => $post['variety'],
                'zone_id' => $info['zone_id'],
                'total' => intval(abs($post['variety'])) * $info['price'],
                'extend_goods' => $info['extend']
            );
            session('cart', $data);
            $this->success('添加成功', U('order/address'));
        }
    }
} 