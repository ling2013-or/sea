<?php
namespace Common\Model;

use Think\Model;

/**
 * 会员管理模型
 * Class UserModel
 * @package Admin\Model
 */
class UserModel extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('user_name', '4,16', '用户名长度4-16个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('user_name', '/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/', '用户名不能存在特殊字符', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('user_name', 'checkUserName', '用户名被占用', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
        array('nick_name', '1,16', '昵称长度为1-16个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('farm_name', '1,16', '农场名称长度为1-16个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('user_pass', '6,16', '密码长度为6-16个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('user_pass', '6,16', '密码长度为6-16个字符', self::VALUE_VALIDATE, 'length', self::MODEL_UPDATE),
        array('pay_pass', '6,16', '支付密码长度为6-16个字符', self::VALUE_VALIDATE, 'length', self::MODEL_UPDATE),
        array('real_name', '1,16', '用户真实姓名长度为1-16个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('idcard', 'validation_filter_id_card', '身份证号码格式不正确', self::VALUE_VALIDATE, 'function', self::MODEL_BOTH),
        array('user_email', 'email', '邮箱格式不正确', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('user_email', '', '邮箱已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('user_phone', '/^1[0-9]{10}$/', '手机号码格式不正确', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('user_phone', '', '手机号码已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('user_name', 'checkPhoneName', '用户名不能为手机号码', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('status', 0, self::MODEL_INSERT, 'string'),
        array('reg_time', 'time', self::MODEL_INSERT, 'function'),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
        array('login_count', 0, self::MODEL_INSERT, 'string'),
    );

    /**
     * 检测用户名的合法性
     * @return bool
     */
    protected function checkUserName()
    {
        $username = I('post.user_name');
        $map = array(
            'user_name' => $username,
            'user_phone'=> $username,
        );
        $map['_logic'] = 'OR';
        $where['_complex'] = $map;
        $where['uid'] = array('neq', I('post.uid', 0, 'intval'));
        $res = $this->where($where)->find();
        return empty($res);
    }

    /**
     * 检测手机号用户名
     * @return bool
     */
    protected function checkPhoneName()
    {
        /**
         * 用户名不能为手机号码
         */
        $user_name = I('user_name');
        if(preg_match('/^1[0-9]{10}$/', $user_name)) {
            return false;
        }
        return true;
    }

    /**
     * 对明文密码，进行加密，返回加密后的密文密码
     * @param string $password 明文密码
     * @param string $encrypt 认证码
     * @return string 密文密码
     */
    public function hashPassword($password, $encrypt = "")
    {
        return md5($password . md5($encrypt));
    }

    /**
     * 添加用户
     * @return bool
     */
    public function addUser()
    {
        try {
            //启用事务处理
            $this->startTrans();
            if ($this->create()) {
                if($this->add()) {
                    $this->commit();
                    return true;
                } else {
                    throw new \Exception('添加会员失败');
                }
            } else {
                throw new \Exception($this->error);
            }
        } catch(\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 更改用户信息前调用
     * @param $data
     */
    protected function _before_write(&$data)
    {
        $user_pass = I('post.user_pass', '', 'trim');
        $pay_pass = I('post.pay_pass', '', 'trim');
        if($user_pass) {
            $data['user_encrypt'] = gen_random_string(6);
            $data['user_pass'] = $this->hashPassword($user_pass, $data['user_encrypt']);
        }

        if($pay_pass) {
            $data['pay_encrypt'] = gen_random_string(6);
            $data['pay_pass'] = $this->hashPassword($pay_pass, $data['user_encrypt']);
        }
    }

    /**
     * 添加会员前调用
     * @param $data
     * @param $options
     */
    protected function _before_insert(&$data,$options)
    {
        //生成密码
        $password = I('user_pass');
        $data['user_encrypt'] = gen_random_string(6);
        $data['user_pass'] = $this->hashPassword($password, $data['user_encrypt']);
    }

    /**
     * 添加会员成功后调用
     * @param $data
     * @param $options
     * @return  bool|int
     */
    protected function _after_insert($data, $options)
    {
        return M('UserAccount')->add(array('uid' => $data['uid']));
    }
}