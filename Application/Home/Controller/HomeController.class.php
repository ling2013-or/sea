<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/24
 * Time: 9:53
 */

namespace Home\Controller;


use Think\Controller;

class HomeController extends Controller
{
    protected $uid;

    /**
     * 加载预处理
     */
    protected function _initialize(){
        //获取配置文件


    }

    /**
     * 判断用户是否登录
     */
    protected function isLogin(){
        if(!session('?id')){
            if(IS_AJAX){
                Cookie('__forward__', $_SERVER['HTTP_REFERER']);
            }else{
                Cookie('__forward__', $_SERVER['REQUEST_URI']);
            }
            $this->error('登录失效',U('Public/login'));
        }
        $this->uid = session('id');
        return $this->uid;
    }


    /**
     * 获取页面轮播图
     * @param $controller
     * @param $method
     * @return array|mixed
     */
    public function getCarousel($controller, $method)
    {
        //获取首页轮播图片信息、活动页面信息
        $data = S($controller . '__' . $method);
        $data = false;
        if (!$data) {
            $model = D('Carousel');
            //轮播图
            $map = [];
            $map['model'] = $controller . '/' . $method;;
            $map['status'] = 1;
            $map['type'] = 0;
            $carousel = $model->field('*')->where($map)->find();
            $carousel['img'] = json_decode($carousel['img'], true);
            //获取了轮播图片的个数
            $num = count($carousel);
            $data = [
                //轮播图
                'carousel' => $carousel,
                //轮播图数量
                'num' => $num,
            ];
            S('HOME_DATA_MORE', $data);
        }
        return $data;
    }


    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function error($message = '', $jumpUrl = '', $ajax = false)
    {
        parent::error($message, $jumpUrl, $ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function success($message = '', $jumpUrl = '', $ajax = false)
    {
        parent::success($message, $jumpUrl, $ajax);
    }

    /**
     * 检测密码合法性
     */
    protected function _checkkPassword($password)
    {
        $data = array();
        // 检测密码合法性
        if (!preg_match('/^.*([\W_a-zA-z0-9-])+.*$/i', $password)) {
            $data['status'] = false;
            $data['msg'] = "密码不合法";
            $data['code'] = 41010; //(LY)自定义
            return $data;
        }

        // 检测密码长度
        $len = strlen($password);
        if ($len < 6 || $len > 20) {
            $data['status'] = false;
            $data['msg'] = "密码长度应在6~20个字符";
            $data['code'] = 41011; //(LY)自定义
            return $data;
        }

        $data['status'] = true;
        $data['msg'] = "密码合法";
        return $data;
    }
} 