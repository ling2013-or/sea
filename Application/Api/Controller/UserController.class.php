<?php

namespace Api\Controller;

/**
 * 用户处理管理控制器
 * Class UserController
 * @package Api\Controller
 */
class UserController extends ApiController
{
    /**
     * 会员登录
     * 支持手机号码，邮箱， 用户名登录（用户名、邮箱、手机号同时唯一）
     */
    public function login()
    {
        $this->data['username'] = 'liujianjian';
        $this->data['password'] = '111111';
        if (!isset($this->data['username']) || empty($this->data['username'])) {
            $this->apiReturn(41302, '用户名不能为空');
        }

        if (!isset($this->data['password']) || empty($this->data['password'])) {
            $this->apiReturn(41303, '密码不能为空');
        }

        $username = trim($this->data['username']);
        $password = trim($this->data['password']);

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
            $this->apiReturn(41304, '登录账号不存在');
        }
        //TODO 设备名称，ip
        $value = array(
            'uid' => $user['uid'] ? $user['uid'] : '',
            'user_name' => $username,
            'login_from' => $this->data['login_from'],
            'login_type' => 0,
            'type' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $this->data['ip'],
            'date_time' => NOW_TIME,
            'status' => 0,
        );

        // 检测用户密码
        if ($user['user_pass'] != $Model->hashPassword($password, $user['user_encrypt'])) {
            $value['detail'] = '用户名或密码不正确,密码为：' . $user['user_pass'];
            //记录操作日志
            $this->log($value);
            $this->apiReturn(41305, '用户名或密码不正确');
        }

        // 生成json web token
        $token = $this->jsonWebTokenEncode($user['uid']);
        $data = array(
            'token' => $token,
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
        $res = M('User')->where(array('uid' => $user['uid']))->save(array('login_token' => $token));
        if (false === $res) {
            //记录操作日志
            $value['detail'] = '系统繁忙，请稍候重试';
            $this->log($value);
            $this->apiReturn(-1, '系统繁忙，请稍候重试');
        }

        //记录操作日志
        $value['detail'] = '登录成功';
        $value['login_from'] = $this->from;
        $value['ip'] = get_client_ip();
        $this->log($value);
        $data['user_phone'] = isset($data['user_phone']) ? substr_replace($user['user_phone'], '****', 3, 4) : '';
        $this->apiReturn(0, '登录成功', $data);
    }

    //登录日志
    private function log($value = array())
    {
        $model = M('UserLogin');
        $model->add($value);
    }

    /**
     * 检测密码合法性
     */
    private function _checkkPassword($password)
    {
        $data = array();
        // 检测密码合法性
        if (!preg_match('/^.*([\W_a-zA-z0-9-])+.*$/i', $password)) {
            $data['status'] = false;
            $data['msg'] = "密码不合法";
            $data['code'] = 41310;
            return $data;
        }

        // 检测密码长度
        $len = strlen($password);
        if ($len < 6 || $len > 20) {
            $data['status'] = false;
            $data['msg'] = "密码长度应在6~20个字符";
            $data['code'] = 41311;
            return $data;
        }

        $data['status'] = true;
        $data['msg'] = "密码合法";
        return $data;
    }

    /**
     * 正常注册
     */
    public function register()
    {
        $username = $this->data['username'];
        $password = $this->data['password'];
        $pwd_repeat = $this->data['pwd_repeat'];
        $code = $this->data['code'];
        $sms_code = $this->data['sms_code'];
        $mobile = $this->data['mobile'];
        //用户名、密码、验证码(短信验证)
        //用户名检测
        if (!isset($username) || empty($username)) {
            $this->apiReturn(41302, '用户名不能为空');
        }
        //密码检测
        if (!isset($password) || empty($password)) {
            $this->apiReturn(41303, '密码不能为空');
        }

        //验证码检测
        if (!isset($code) || empty($code)) {
            $this->apiReturn(41306, '图片验证码不能为空');
        }

        //短信验证码检测
        if (!isset($sms_code) || empty($sms_code)) {
            $this->apiReturn(41307, '短信验证码不能为空');
        }

        //验证 两个验证码的合法性
        if ($code != session('very_code')) {
            session('very_code', null);
            $this->apiReturn(41308, '图片验证码错误');
        }

        if ($sms_code != $this->getCode($mobile)) {
            $this->apiReturn(41309, '短信验证码错误');
        }

        //检测两次密码是否一致
        if ($password != $pwd_repeat) {
            $this->apiReturn(41311, '两次密码不一致');
        }

        // 新密码的合法性
        $val = $this->_checkkPassword($password);
        if (!$val['status']) {
            $this->apiReturn($val['code'], $val['msg']);
        }

        //用户名称不能使手机号码
        $check_name = preg_match('/^1\d{10}/', $username);
        $check_email = preg_match(' /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $username);
        if ($check_name || $check_email) {
            $this->apiReturn(41311, '用户名格式错误！');
        }
        //验证用户名是否存在
        $model = D('User');
        if ($model->where(array('user_name' => $username))->select()) {
            $this->apiReturn(41321, $username . '用户名已存在');
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
            $this->apiReturn(-1, '系统繁忙，请稍候重试');
        }
        //创建用户之后再给新用户创建一个资金账号

        //TODO 增加数据
        $data = array(
            'account' => $username,
        );
        $this->apiReturn(0, '注册成功！', $data);
    }

    /**
     * 根据用户名、手机号码修改密码
     */
    public function forget()
    {
        //按照手机号码找回密码
        //手机号、验证码(短信验证)
        $mobile = $this->data['mobile'];
        $code = $this->data['code'];
        $password = $this->data['password'];

        //密码不能为空
        $pwd_check = $this->_checkkPassword($password);
        if (empty($password) || !$pwd_check['status']) {
            $this->apiReturn(41313, '新密码不合法');
        }
        //手机号码检测
        if (!isset($mobile) || empty($mobile)) {
            $this->apiReturn(41307, '手机号码不能为空');
        }
        //手机号码正则验证
        $result = preg_match("/^1\d{10}$/", $mobile);
        if (!$result) {
            $this->apiReturn(41314, '新手机号码不合法'); //自定义
        }

        //查询当前用户是否存在
        $model = D('User');
        $check = $model->where(array('user_phone' => $mobile))->find();
        if (!$check) {
            $value2 = array('mobile' => $mobile);
            $this->apiReturn(41322, '手机号码不存在', $value2);
        }

        //验证码验证
        if (!isset($code) || empty($code)) {
            $this->apiReturn(41308, '密码不能为空');
        }

        //对比两次的验证码
        if ($code != $this->getCode($mobile)) {
            $this->apiReturn(41327, '验证码错误，请重新输入');
        }

        $data = array(
            'mobile' => $mobile,
            'session' => $_SESSION
        );

        //修改密码
        $map = array();
        $map['id'] = $check['id'];
        $model->where($map)->save(array('user_pass' => $model->hashPassword($password, $check['user_encrypt'])));

        //修改验证码状态(让验证码失效)
        $this->updateCode($mobile);
        $this->apiReturn(0, '校验成功！', $data);

    }

    /**
     * 获取十分钟之内用户发送的最后一条验证码
     * @param string $mobile
     * @param string $type
     * @return string
     */
    private function getCode($mobile, $type = 'sms_code')
    {
        $result = M('SmsCode')->field('*')->where(array('mobile' => $mobile, 'key_status' => 1, 'time' => array('lt', NOW_TIME + 10 * 60)))->order('time DESC')->find();
        if (empty($result)) {
            $this->apiReturn(41312, '验证码已失效，请重新获取');
        }
        return $result['code'];
    }

    /**
     * 修改验证码的状态
     * @param string $mobile 手机号码
     * @param int $id 验证码记录唯一ID
     * @param int $status 验证码状态  -1失效 1正常
     * @return array 返回修改结果
     */
    public function updateCode($mobile, $status = -1)
    {
        $where['mobile'] = $mobile;
        $value['status'] = $status;
        $model = M('SmsCode');
        //修改验证码状态
        $result = $model->where($where)->save($value);
        if (false === $result) {
            $data = array('status' => false, 'code' => 41326, 'msg' => '状态修改失败');
        } else {
            $data = array('status' => true, 'code' => 0, 'msg' => '修改成功');
        }
        return $data;
    }


    /**
     * 忘记密码第二步操作，修改密码
     */
    public function updatePwd()
    {
        $this->isLogin();
        //获取原来的登录密码
        $old = trim($this->data['old']);
        $password = trim($this->data['password']);
        $repeat = trim($this->data['password_repeat']);
        //手机号码、密码
        //旧密码检测
        if (!isset($old) || empty($old)) {
            $this->apiReturn(41303, '旧密码不能为空');
        }
        //密码检测
        if (!isset($password) || empty($password)) {
            $this->apiReturn(41303, '新密码不能为空');
        }
        // 旧密码的合法性
        $val = $this->_checkkPassword($old);
        if (!$val['status']) {
            $this->apiReturn($val['code'], $val['msg']);
        }
        // 新密码的合法性
        $val = $this->_checkkPassword($password);
        if (!$val['status']) {
            $this->apiReturn($val['code'], $val['msg']);
        }
        // 重复密码
        if (!isset($repeat) || empty($repeat)) {
            $this->apiReturn(41003, '重复密码不能为空');
        }
        // 重复密码的合法性
        $data2 = $this->_checkkPassword($repeat);
        if (!$data2['status']) {
            $this->apiReturn($data2['code'], '重复' . $data2['msg']);
        }

        // 两次密码是否相同
        if ($password != $repeat) {
            $this->apiReturn(41012, "两次密码不相同");
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
            'ip' => $this->data['ip'],
            'date_time' => NOW_TIME,
            'status' => 0,
        );
        // 检测用户密码
        if ($user['user_pass'] != $Model->hashPassword($old, $user['user_encrypt'])) {
            $values['detail'] = '旧密码错误,密码为：' . $old;
            //记录操作日志
            $this->log($values);
            $this->apiReturn(41305, '旧密码错误');
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
            $this->apiReturn(-1, '系统繁忙，请稍候重试');
        } elseif ($result == 0) {
            //两次密码输入的一致，无需修改
            $this->apiReturn('41329', '新密码与旧密码过于相似，请重新修改!');
        }

        $this->apiReturn(0, '密码修改成功！');

    }

    /**
     * 忘记密码第二步操作，修改密码
     */
    public function forget_edit()
    {
        //校验过期时间
        $return = $this->mobile_update(false);
        if (!$return['status']) {
            $this->apiReturn($return['code'], $return['msg']);
        }
        $password = $this->data['password'];
        $repeat = $this->data['password_repeat'];
        $mobile = $this->data['mobile'];
        //手机号码、密码
        //密码检测
        if (!isset($password) || empty($password)) {
            $this->apiReturn(41303, '密码不能为空');
        }
        // 新密码的合法性
        $val = $this->_checkkPassword($password);
        if (!$val['status']) {
            $this->apiReturn($val['code'], $val['msg']);
        }
        // 重复密码
        if (!isset($repeat) || empty($repeat)) {
            $this->apiReturn(41003, '重复密码不能为空');
        }
        // 重复密码的合法性
        $data2 = $this->_checkkPassword($repeat);
        if (!$data2['status']) {
            $this->apiReturn($data2['code'], '重复' . $data2['msg']);
        }

        // 两次密码是否相同
        if ($password != $repeat) {
            $this->apiReturn(41012, "两次密码不相同");
        }

        //手机号码检测
        if (!isset($mobile) || empty($mobile)) {
            $this->apiReturn(41307, '手机号码不能为空');
        }
        //手机号码正则验证
        $result = preg_match("/^1\d{10}$/", $mobile);
        if (!$result) {
            $this->apiReturn(41314, '新手机号码不合法'); //自定义
        }

        //验证手机号码是否存在
        $model = D('User');
        $check = $model->where(array('user_phone' => $mobile))->find();
        if (!$check) {
            $value2 = array('mobile' => $mobile);
            $this->apiReturn(41322, '该账户不存在，请输入正确的账户', $value2);
        }

        //uid ,新密码
        // 产生新的加密后的密码
        $encrypt = gen_random_string(6);
        $pwd = $model->hashPassword($password, $encrypt);
        //组装用户注册数据
        $value = array(
            'user_pass' => $pwd,
            'user_encrypt' => $encrypt,
        );
        $where = array();
        $where['mobile'] = $mobile;
        $where['uid'] = $check['uid'];
        //TODO 修改密码
        $result = $model->where($where)->save($value);
        $data = array(
            'mobile' => $mobile,
        );
        if (false === $result) {
            //修改错误
            $this->apiReturn(-1, '系统繁忙，请稍候重试');
        } elseif ($result == 0) {
            //两次密码输入的一致，无需修改
            $this->apiReturn('41329', '新密码与旧密码过于相似，请重新修改!', $data);
        }
        $this->apiReturn(0, '密码修改成功！', $data);

    }

    /**
     * 给用户所更换的手机号码发送短信验证码
     *
     * $check_new 是否需要验证新手机号码的合法性
     */
    private function mobile_update($check_new = true)
    {
        $msg = array();

        if (!isset($this->data['key']) && empty($this->data['key'])) {
            $msg = array('status' => false, 'code' => 50004, 'msg' => '用户没有验证原来的手机号码，非法请求');
            return $msg;
        }

        $key = base64_decode(trim($this->data['key']));
        //注意当初的小标已经消失。
        $value = explode(',', $key);
        $if['key'] = $this->data['key'];
        $if['status'] = -1;
        $if['key_status'] = 1;
        //检测一个小时之内发送的短信验证码
        $if['time'] = array('egt', intval(NOW_TIME - 3600));

        //查询当前手机号码是否发送过此条信息
        //修改验证码状态
        $check = M('SmsCode')->field('id,mobile')->where($if)->find();
        //修改验证码的状态
        $update = M('SmsCode')->where($if)->save(array('key_status' => -1));
        if ($check) {
            if ($check_new) {
                //检测新手机号码是否存在
                $user = M('User');
                $map = array();
                $map['user_phone'] = $this->data['mobile'];
                $check2 = $user->field('uid')->where($map)->find();
                if ($check2) {
                    $msg = array('status' => false, 'code' => 50005, 'msg' => '新绑定的手机号码已存在。');
                    return $msg;
                } else {
                    $msg = array('status' => true);
                    return $msg;
                }
            } else {
                $msg = array('status' => true);
                return $msg;
            }
        } else {
            $msg = array('status' => false, 'code' => 50004, 'msg' => '用户没有验证原来的手机号码/验证已过期');
            return $msg;
        }
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


    public function userInfo()
    {
        //todo 一定验证用户是否登录
        $this->isLogin();
//        echo $this->uid;die;
        $this->uid = 2;
        $map = [];
        $map['id'] = $this->uid;
        $model = M('User');
        $info = $model->field('*')->find($this->uid);
        if (empty($info)) {
            $this->apiReturn(50020, '用户信息不存在');
        }
        $value = [];
        $value['user_name'] = $info['user_name'];
        $value['real_name'] = $info['real_name'];
        $value['user_email'] = $info['user_email'];
        $value['user_phone'] = $info['user_phone'];
        $value['address'] = $info['address'];

        $this->apiReturn(0, '成功', $value);
    }

    /**
     * 获取当前用户的设备号
     * @return string
     */
    public function getSystem()
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (stristr($agent, 'iPad')) {
            $fb_fs = "iPad";
        } else if (preg_match('/Android (([0-9_.]{1,3})+)/i', $agent, $version)) {
            $fb_fs = "手机(Android " . $version[1] . ")";
        } else if (stristr($agent, 'Linux')) {
            $fb_fs = "电脑(Linux)";
        } else if (preg_match('/iPhone OS (([0-9_.]{1,3})+)/i', $agent, $version)) {
            $fb_fs = "手机(iPhone " . $version[1] . ")";
        } else if (preg_match('/Mac OS X (([0-9_.]{1,5})+)/i', $agent, $version)) {
            $fb_fs = "电脑(OS X " . $version[1] . ")";
        } else if (preg_match('/unix/i', $agent)) {
            $fb_fs = "Unix";
        } else if (preg_match('/windows/i', $agent)) {
            $fb_fs = "电脑(Windows)";
        } else {
            $fb_fs = "未知(Unknown)";
        }
        return $fb_fs;
    }

    /*上传用户头像*/
    public function upload_avatar()
    {
        $uid = $this->isLogin();
        if ($_FILES) {
            $upload = new \Think\Upload(C('PICTURE_UPLOAD'));
            // $upload->saveName = $name;
            $upload->replace = true;
            $info = $upload->upload();
            if (!$info) {
                $this->apiReturn('-1', $upload->getError());
            } else {
                foreach ($info as $file) {
                    $path = $file['savepath'] . $file['savename'];
                }
                $imgUrl = __ROOT__ . '/' . trim(C('PICTURE_UPLOAD.rootPath'), './') . '/' . $path;
                // $this->ajaxReturn(array('status'=>1,'info'=>$imgUrl));
                // 将头像地址写入数据表
                $res = M("user")->where(array("uid" => $uid))->save(array("user_avatar" => $imgUrl));
                if ($res == 0 || (!$res)) {
                    $this->apiReturn(-1, "系统繁忙，请稍候重试");
                }
                $this->apiReturn(0, "上传头像成功", array("avatar" => $imgUrl));
            }
        } else {
            $this->apiReturn(-1, "没有文件需要上传");
        }
    }

}