<?php

namespace Api\Controller;

/**
 * 会员操作管理
 * Class MemberController
 * @package Api\Controller
 */
class MemberController extends ApiController
{

    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->uid = $this->isLogin();
    }

    /**
     * 修改用户登录密码
     * user_pass_old 原密码
     * user_pass_new 新密码
     * user_pass_repeat 重复密码
     * 成功时 返回 {code: 0, status: true, msg: "修改登陆密码成功"}
     * 失败时 返回 {code: 41013, status: false, msg: "未知原因，操作失败"}
     * 失败时 返回 {code: 41003, status: false, msg: "原密码不能为空"}
     * 失败时 返回 {code: 41003, status: false, msg: "新密码不能为空"}
     * 失败时 返回 {code: 41005, status: false, msg: "原密码不正确"}
     * 失败时 返回 {code: 41010, status: false, msg: "新密码不合法"}
     * 失败时 返回 {code: 41012, status: false, msg: "两次密码不相同"}
     * 失败时 返回 {code: 41011, status: false, msg: "密码长度应在6~20个字符"}
     */
    public function password()
    {
        $uid = $this->isLogin(); // 获取用户id

        // 接收参数
        $user_pass_old = $this->data['user_pass_old'];
        $user_pass_new = $this->data['user_pass_new'];
        $user_pass_repeat = $this->data['user_pass_repeat'];

        $Model = D('User');
        $user = $Model->field('uid,user_name,user_email,user_phone,user_pass,user_encrypt,nick_name,farm_name')->where("uid=" . intval($uid))->find();
        // 原密码
        if (!isset($user_pass_old) || empty($user_pass_old)) {
            $this->apiReturn(41003, '原密码不能为空');
        }

        // 检测原密码是否正确
        if ($user['user_pass'] != $Model->hashPassword($user_pass_old, $user['user_encrypt'])) {
            $this->apiReturn(41005, '原密码不正确');
        }

        // 新密码
        if (!isset($user_pass_new) || empty($user_pass_new)) {
            $this->apiReturn(41003, '新密码不能为空');
        }
        // 新密码的合法性
        $data1 = $this->_checkkPassword($user_pass_new);
        if (!$data1['status']) {
            $this->apiReturn($data1['code'], '新' . $data1['msg']);
        }

        // 重复密码
        if (!isset($user_pass_repeat) || empty($user_pass_repeat)) {
            $this->apiReturn(41003, '重复密码不能为空');
        }
        // 重复密码的合法性
        $data2 = $this->_checkkPassword($user_pass_repeat);
        if (!$data2['status']) {
            $this->apiReturn($data2['code'], '重复' . $data2['msg']);
        }
        // 两次密码是否相同
        if ($user_pass_repeat != $user_pass_new) {
            $this->apiReturn(41012, "两次密码不相同"); //(LY) 自定义
        }

        // 产生新的加密后的密码
        $data3 = array();
        $data3['user_pass'] = $Model->hashPassword($user_pass_new, $user['user_encrypt']);
        // 更新密码
        $res = $Model->data($data3)->where("uid=" . intval($uid))->save();
        if ($res or ($res == 0)) {
            $this->apiReturn(0, "修改登陆密码成功");
        } else {
            $this->apiReturn(41013, "未知原因，操作失败"); //(LY) 自定义
        }
    }

    /*
    * 修改支付密码
    * pay_pass_old 原密码
    * pay_pass_new 新密码
    * pay_pass_repeat 重复密码
    * 成功时 返回 {code: 0, status: true, msg: "修改支付密码成功"}
    * 失败时 返回 {code: 41013, status: false, msg: "未知原因，操作失败"}
    * 失败时 返回 {code: 41003, status: false, msg: "原支付密码不能为空"}
    * 失败时 返回 {code: 41003, status: false, msg: "新支付密码不能为空"}
    * 失败时 返回 {code: 41005, status: false, msg: "原支付密码不正确"}
    * 失败时 返回 {code: 41010, status: false, msg: "新支付密码不合法"}
    * 失败时 返回 {code: 41012, status: false, msg: "两次密码不相同"}
    * 失败时 返回 {code: 41011, status: false, msg: "密码长度应在6~20个字符"}
    * 失败时 返回 {code: 41013, status: false, msg: "手机验证码不正确"} TODO
    */
    public function pay_pass()
    {
        $uid = $this->isLogin(); // 获取用户id
        // 接收参数
        $pay_pass_old = $this->data['pay_pass_old'];
        $pay_pass_new = $this->data['pay_pass_new'];
        $pay_pass_repeat = $this->data['pay_pass_repeat'];
        $pay_pass_key = $this->data['pay_pass_key'];

        // 查询用户信息
        $Model = D('User');
        $user = $Model->field('uid,user_name,user_email,user_phone,pay_pass,pay_encrypt,nick_name,farm_name')->where("uid=" . intval($uid))->find();

        //1第一次设置支付密码 2、快速修改支付密码（通过手机），3、通过原来的支付密码修改
        switch ($this->data['type']) {
            case 1:
                //未设置支付密码重新设置
                // 判断以前有没有过支付密码，如果没有，则不用比较原密码是否相同
                $oldPass = $user['pay_pass'];
                if ($oldPass) // 说明需要比较原支付密码
                {
                    if ($Model->hashPassword($pay_pass_old, $user['pay_encrypt']) != $oldPass) {
                        $this->apiReturn(41005, "原支付密码不正确");
                    }
                }
                break;
            case 2:
                //用户已验证手机号码，来修改支付密码
                //检测验证码是否为空
                if (!isset($this->data['code']) || empty($this->data['code'])) {
                    $this->apiReturn(41006, '验证码不能为空！');
                }
                //验证短信验证码是否正确
                //获取数据库中的验证码
                $getCode = $this->getCode($user['user_phone']);
                if (empty($getCode)) {
                    $this->apiReturn(41325, '请获取验证码');
                }
                $oldCode = $getCode['code'];
                //对比两次的验证码
                if ($this->data['code'] != $oldCode) {
                    $this->apiReturn(41327, '验证码错误，请重新输入');
                }
                $code_status = $this->updateCode($user['user_phone']);
                if(!$code_status['status']){
                    $this->apiReturn($code_status['code'],$code_status['msg']);
                }
                //验证密匙是否正确
                $check = $this->mobile_update($pay_pass_key);
                if (!$check['status']) {
                    $this->apiReturn($check['code'], $check['msg']);
                }
                break;
            case 3:
                //用户通过旧密码修改新的密码
                // 判断以前有没有过支付密码，如果没有，则不用比较原密码是否相同
                $oldPass = $user['pay_pass'];
                if ($oldPass) // 说明需要比较原支付密码
                {
                    if ($Model->hashPassword($pay_pass_old, $user['pay_encrypt']) != $oldPass) {
                        $this->apiReturn(41005, "原支付密码不正确");
                    }
                } else {
                    $this->apiReturn(41007, '您还未设置支付密码，请先设置。');
                }
                break;
            default:
                $this->apiReturn(-1, '非法请求');
                break;

        }

        // 新密码
        if (!isset($pay_pass_new) || empty($pay_pass_new)) {
            $this->apiReturn(41003, '新支付密码不能为空');
        }
        // 新密码的合法性
        $data1 = $this->_checkkPassword($pay_pass_new);
        if (!$data1['status']) {
            $this->apiReturn($data1['code'], '新支付' . $data1['msg']);
        }
        // 重复密码
        if (!isset($pay_pass_repeat) || empty($pay_pass_repeat)) {
            $this->apiReturn(41003, '重复密码不能为空');
        }
        // 重复密码的合法性
        $data2 = $this->_checkkPassword($pay_pass_repeat);
        if (!$data2['status']) {
            $this->apiReturn($data2['code'], '重复' . $data2['msg']);
        }

        // 两次密码是否相同
        if ($pay_pass_new != $pay_pass_repeat) {
            $this->apiReturn(41012, "两次密码不相同");
        }

        // TODO : 手机短信的验证

        // 产生新的加密后的密码
        $data3 = array();
        $data3['pay_encrypt'] = gen_random_string(6);
        $data3['pay_pass'] = $Model->hashPassword($pay_pass_new, $data3['pay_encrypt']);

        // 更新支付密码        
        $res = $Model->data($data3)->where("uid=" . intval($uid))->save();
        if ($res or ($res == 0)) {
            $this->apiReturn(0, "修改支付密码成功");
        } else {
            $this->apiReturn(41013, "未知原因，操作失败");
        }
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
     * 获取十分钟之内用户发送的最后一条验证码
     * @param string $mobile
     * @param string $type
     * @return string
     */
    private function getCode($mobile, $type = 'code')
    {
        $where = array();
        $where['mobile'] = $mobile;
        $where['type'] = $type;
        $where['time'] = array('egt', NOW_TIME - 600);
        $where['status'] = array('eq', 1);
        $model = M('SmsCode');
        $result = $model->where($where)->field('code')->order('time DESC')->limit(1)->find();

        return $result;
    }

    /**
     * 给用户所更换的手机号码发送短信验证码
     *
     * $check_new 是否需要验证新手机号码的合法性
     */
    private function mobile_update($k)
    {
        $msg = array();

        if (!isset($k) && empty($k)) {
            $msg = array('status' => false, 'code' => 50004, 'msg' => '用户没有验证原来的手机号码，非法请求');
            return $msg;
        }

        $key = base64_decode(trim($k));
        //注意当初的小标已经消失。
        $value = explode(',', $key);
        $if['key'] = $k;
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
            $msg = array('status' => true);
            return $msg;
        } else {
            $msg = array('status' => false, 'code' => 50004, 'msg' => '用户没有验证原来的手机号码/验证已过期');
            return $msg;
        }
    }

    /* 修改手机绑定
     * user_phone_new 新手机号
     * 成功时 返回 {code: 0, status: true, msg: "手机绑定成功"}
     * 失败时 返回 {code: 41013, status: false, msg: "未知原因，操作失败"}
     * 失败时 返回 {code: 41003, status: false, msg: "新手机号码不能为空"}
     * 失败时 返回 {code: 41014, status: false, msg: "新手机号码不合法"}
    */
    public function user_phone()
    {
        // TODO:发送短信验证码到旧手机

        $uid = $this->isLogin(); // 获取用户id

        // 接收参数
        $user_phone_new = $this->data['user_phone_new'];

        // 新手机号不能为空
        if (!isset($user_phone_new) || empty($user_phone_new)) {
            $this->apiReturn(41003, '新手机号码不能为空');
        }

        // 判断新手机号是否合法
        $result = preg_match("/^1\d{10}$/", $user_phone_new);
        if (!$result) {
            $this->apiReturn(41014, '新手机号码不合法'); //自定义
        }

        // 更新用户手机号码
        $data3 = array();
        $data3['user_phone'] = $user_phone_new;
        $res = M("user")->data($data3)->where("uid=" . intval($uid))->save();
        if ($res or ($res == 0)) {
            $this->apiReturn(0, "手机号码绑定成功");
        } else {
            $this->apiReturn(41013, "未知原因，操作失败");
        }
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

    /* 修改农场名
    ** farm_name 农场名
    */
    /*public function farm_name()
    {
        $farm_name = $this->data['farm_name'];

        $uid = $this->isLogin();

        // 农场名不能为空
        if (!isset($farm_name) || empty($farm_name)) {
            $this->apiReturn(41003, '农场名不能为空');
        }

        $res = M("user")->where( array("uid"=>$uid) )->save( array("farm_name"=>$farm_name) );

        if( $res==0 || $res )
        {
            $this->apiReturn(0, "更新农场名成功");
        }
        else
        {
            $this->apiReturn(-1, "服务器繁忙，请稍候重试");
        }
    }*/

    /* 修改用户基本信息：农场名、用户名、昵称、真实姓名、邮箱
    ** farm_name 农场名
    ** user_name 用户名
    ** nick_name 昵称
    ** real_name 真实姓名
    ** user_email 邮箱
    */
    public function user_info()
    {
        $uid = $this->isLogin();

        $data = array();

        $data['farm_name'] = $this->data['farm_name'];
        $data['user_name'] = $this->data['user_name'];
        $data['nick_name'] = $this->data['nick_name'];
        $data['real_name'] = $this->data['real_name'];
        $data['user_email'] = $this->data['user_email'];

        // 判断新手机号是否合法
        $result = preg_match("/^[\w-\.]+@\w+\.\w+$/", $data['user_email']);
        if ((!$result) and $data['user_email']) {
            $this->apiReturn(41014, '邮箱不合法');
        }

        /*用户名的唯一性*/
        $where = array();
        $where['user_name'] = $data['user_name'];
        $where['uid'] = array('neq', $uid);

        $isUniq = M('user')->where($where)->find();

        if ($isUniq) {
            $this->apiReturn(41088, '用户名已经存在！');
        }

        $res = M("user")->where(array("uid" => $uid))->save($data);

        if ($res == 0 || $res) {
            $this->apiReturn(0, "更新用户信息成功");
        } else {
            $this->apiReturn(-1, "服务器繁忙，请稍候重试");
        }
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


    /*上传用户头像*/
    public function upload_img()
    {
        if ($_FILES) {
            $uid = $this->isLogin();
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
//
                $this->apiReturn(0, "上传头像成功", array("avatar" => $imgUrl));
            }
        } else {
            $this->apiReturn(-1, "没有文件需要上传");
        }
    }
}