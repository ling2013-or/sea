<?php
namespace Home\Controller;


/**
 * 个人中心
 * Class PublicController
 * @package Home\Controller
 */
class UserController extends HomeController
{



    /**
     * 用户中心
     */
    public function userCenter()
    {
        //验证用户是否登录
        $this->uid = $this->isLogin();
        $map = [];
        $map['id'] = $this->uid;
        $model = M('User');
        $info = $model->field('*')->find($this->uid);
        if (empty($info)) {
            $this->error('用户信息不存在');
        }
        $lists = R('Address/lists');




        $this->meta_title = '用户中心';
//        echo "<pre>";
//        print_r($lists);die;
        $this->assign('info',$info);
        $this->assign('lists',$lists);
        $this->display();
    }







}