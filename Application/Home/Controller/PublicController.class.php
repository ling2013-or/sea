<?php
namespace Home\Controller;


/**
 * 用户登录
 * Class PublicController
 * @package Home\Controller
 */
class PublicController extends HomeController
{
    /**
     * 登录、验证用户名密码
     */
    public function login()
    {

        if (IS_POST) {
            $post = I('post.');
            if (!isset($post['username']) || empty($post['username'])) {
                $this->error('用户名不能为空');
            }

            if (!isset($post['password']) || empty($post['password'])) {
                $this->error('密码不能为空');
            }

            $username = trim($post['username']);
            $password = trim($post['password']);

            $condition = array();
            if (strpos($username, '@') === false) {
                // 手机号码|用户名登录
                if (preg_match('/^1\d{10}$/', $username)) {
                    $condition['user_phone'] = $username;
                } else {
                    $condition['user_name'] = $username;
                }
            } else {
                // 邮箱登录
                $condition['user_email'] = $username;
            }
            $Model = D('User');
            $user = $Model->field('uid,user_name,user_email,user_phone,user_pass,user_encrypt,user_avatar,real_name')->where($condition)->find();
            if (!$user) {
                $this->error('登录账号不存在');
            }
            //TODO 设备名称，ip
            $value = array(
                'uid' => $user['uid'] ? $user['uid'] : '',
                'user_name' => $username,
                'login_from' => $post['login_from'],
                'login_type' => 0,
                'type' => $_SERVER['HTTP_USER_AGENT'],
                'ip' => $post['ip'],
                'date_time' => NOW_TIME,
                'status' => 0,
            );

            // 检测用户密码
            if ($user['user_pass'] != $Model->hashPassword($password, $user['user_encrypt'])) {
                $value['detail'] = '用户名或密码不正确,密码为：' . $user['user_pass'];
                //记录操作日志
//                $this->log($value);
                $this->error('用户名或密码不正确');
            }

            // 生成json web token
            $data = array(
                'id' => $user['uid'],      // 用户id
                'user_name' => $user['user_name'],      // 用户名
                'user_phone' => $user['user_phone'],     // 用户手机号码
                'user_email' => $user['user_email'],     // 用户邮箱
                'real_name' => $user['real_name']     // 用户真实姓名
            );
            //记录session信息
            foreach ($data as $key => $val) {
                session($key, $val);
            }

            // 将token写入数据库
            $res = M('User')->where(array('uid' => $user['uid']))->save(array('last_login_time' => NOW_TIME));
            if (false === $res) {
                //记录操作日志
                $value['detail'] = '系统繁忙，请稍候重试';
                $this->success(-1, '系统繁忙，请稍候重试');
            }

            //记录操作日志
            $value['detail'] = '登录成功';
            $value['login_from'] = $this->from;
            $value['ip'] = get_client_ip();
            $data['user_phone'] = isset($data['user_phone']) ? substr_replace($user['user_phone'], '****', 3, 4) : '';
            $url = Cookie('__forward__') ? Cookie('__forward__') : U('Index/index');
            $this->success('登录成功', $url, $data);
        }
        $this->meta_title = '登录';
        $this->display();
    }

    /**
     * 首页展示
     */
    public function main()
    {
        $this->display();
    }


    /**
     * 注册
     */
    public function register()
    {
        if (IS_AJAX) {
            $post = I('post.');
            $username = $post['username'];
            $password = $post['password'];
            $pwd_repeat = $post['surePassword'];
            $code = $post['code'];
            $sms_code = $post['sms_code'];
            $mobile = $post['mobile'];
            //用户名、密码、验证码(短信验证)
            //用户名检测
            if (!isset($post['username']) || empty($username)) {
                $this->error('用户名不能为空');
            }
            //密码检测
            if (!isset($post['password']) || empty($password)) {
                $this->error('密码不能为空');
            }

            //验证码检测
            if (!isset($post['code']) || empty($code)) {
                $this->error('图片验证码不能为空');
            }

            //短信验证码检测
//            if (!isset($post['sms_code']) || empty($sms_code)) {
//                $this->error(41304, '短信验证码不能为空');
//            }

//            session('very_code');die;
            //验证 两个验证码的合法性
            if (!verifycheck($code)) {
                $this->error('图片验证码错误');
            }

//            if ($sms_code != $this->getCode($mobile)) {
//                $this->error(41309, '短信验证码错误');
//            }

            //检测两次密码是否一致
            if ($password != $pwd_repeat) {
                $this->error('两次密码不一致');
            }

            // 新密码的合法性
            $val = $this->_checkkPassword($password);
            if (!$val['status']) {
                $this->error($val['msg']);
            }

            //用户名称不能使手机号码
            $check_name = preg_match('/^1\d{10}/', $username);
            $check_email = preg_match(' /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $username);
            if ($check_name || $check_email) {
                $this->error('用户名格式错误！');
            }
            //验证用户名是否存在
            $model = D('User');
            $conditions = array();
            $conditions['user_name'] = $username;
            $conditions['user_phone'] = $mobile;
            $conditions['_logic'] = 'OR';
            if ($model->where($conditions)->select()) {
                $this->error($username . '用户名或手机已存在');
            }


            // 产生新的加密后的密码
            $encrypt = gen_random_string(6);
            $pwd = $model->hashPassword($password, $encrypt);
            //组装用户注册数据
            $value = array(
                'user_name' => $username,
                'user_pass' => $pwd,
                'user_encrypt' => $encrypt,
                'user_phone' => $mobile,
            );
            $user = M('User');
            //创建用户
            $uid = $user->add($value);
            if (false === $uid) {
                $this->error('系统繁忙，请稍候重试');
            }
            //创建用户之后再给新用户创建一个资金账号

            //TODO 增加数据
            $data = array(
                'account' => $username,
            );
            $this->success('注册成功！', U('Public/login'));
        }
        $this->meta_title = '注册';
        $this->display();
    }

    /**
     * 忘记密码
     */
    public function updatePwd()
    {
        if (IS_AJAX) {
            $this->isLogin();
            $post = I('post.');
            //获取原来的登录密码
            $old = trim($post['old']);
            $password = trim($post['password']);
            $repeat = trim($post['password_repeat']);
            //手机号码、密码
            //旧密码检测
            if (!isset($old) || empty($old)) {
                $this->error('旧密码不能为空');
            }
            //密码检测
            if (!isset($password) || empty($password)) {
                $this->error('新密码不能为空');
            }
            // 旧密码的合法性
            $val = $this->_checkkPassword($old);
            if (!$val['status']) {
                $this->error($val['msg']);
            }
            // 新密码的合法性
            $val = $this->_checkkPassword($password);
            if (!$val['status']) {
                $this->error($val['msg']);
            }
            // 重复密码
            if (!isset($repeat) || empty($repeat)) {
                $this->error('重复密码不能为空');
            }
            // 重复密码的合法性
            $data2 = $this->_checkkPassword($repeat);
            if (!$data2['status']) {
                $this->error('重复' . $data2['msg']);
            }

            // 两次密码是否相同
            if ($password != $repeat) {
                $this->error("两次密码不相同");
            }

            //验证旧密码是否正确
            $condition = array();
            $condition['uid'] = $this->uid;
            $Model = D('User');
            $user = $Model->field('user_name,user_email,user_phone,user_pass,user_encrypt,user_avatar')->where($condition)->find();
            //TODO 设备名称，ip
            $values = array(
                'uid' => $user['uid'] ? $user['uid'] : '',
                'user_name' => $user['user_name'],
                'login_type' => 0,
                'type' => 'operation',
                'ip' => $post['ip'],
                'date_time' => NOW_TIME,
                'status' => 0,
            );
            // 检测用户密码
            if ($user['user_pass'] != $Model->hashPassword($old, $user['user_encrypt'])) {
                $values['detail'] = '旧密码错误,密码为：' . $old;
                //记录操作日志
//                $this->log($values);
                $this->error('旧密码错误');
            }

            $model = D('User');
            // 产生新的加密后的密码
            $encrypt = $user['user_encrypt'];
            $pwd = $model->hashPassword($password, $encrypt);
            //组装用户注册数据
            $value = array(
                'user_pass' => $pwd,
                'user_encrypt' => $encrypt,
            );
            $where = array();
            $where['uid'] = $this->uid;
            //TODO 修改密码
            $result = $model->where($where)->save($value);

            if (false === $result) {
                //修改错误
                $this->error('系统繁忙，请稍候重试');
            } elseif ($result == 0) {
                //两次密码输入的一致，无需修改
                $this->error('新密码与旧密码过于相似，请重新修改!');
            }

            $this->success('密码修改成功！', U('User/userCenter'));
        }
    }


    /**
     * 会员特权
     */
    public function privacy()
    {
        $this->display();
    }


    /**
     * 生成验证码
     * @access pulic
     */
    public function vcode()
    {
        $Verify = new \Think\Verify();
        $Verify->fontSize = 20;
        $Verify->length = 4;
        $Verify->useNoise = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 150;
        $Verify->imageH = 50;
        $Verify->expire = 600;
        $Verify->entry();
    }


}