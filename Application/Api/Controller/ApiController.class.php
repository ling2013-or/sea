<?php

namespace Api\Controller;

use Common\Library\JWT\JWT;
use Think\Controller;

/**
 * 接口公共继承类
 * TODO  是否使用Restful模式
 * TODO  状态码说明  统一使用5位数字
 * Class ApiController
 * @package Api\Controller
 */
class ApiController extends Controller
{

    /**
     * 接受数据是否加密
     * @var bool
     */
    private $is_encrypt_post = false;

    /**
     * 单点登录是否开启
     * @var bool
     */
    private $is_mark = false;

    /**
     * 是否启用加密数据
     * @var bool
     */
    private $is_encrypt = false;

    /**
     * JWT加密凭证
     * @var string
     */
    protected $secret_key = 'dsTyZ1fmockBURwoLu3jtzKQ7Beq4Xye';

    /**
     * 传递数据中的data参数数据
     * @var array
     */
    protected $data = array();

    /**
     * 发送数据格式
     * @var string
     */
    private $type = 'JSON';

    /**
     * 数据来源
     * @var string
     */
    protected $from = 'app';

    /**
     * 会员UID
     * @var int
     */
    protected $uid = 0;

    /**
     * 会员用户名
     * @var string
     */
    protected $user_name = '';

    /**
     * 获取页码数
     * @var int
     */
    protected $page = 1;

    /**
     * 分页每页显示条数
     * @var int
     */
    protected $page_size = 10;

    /**
     * 初始化
     */
    protected function _initialize()
    {
        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        C($config); //添加配置
        $_POST['token'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJzZWEiLCJ1aWQiOiIzOSIsImlhdCI6MTQ3MTc2NTIxMiwibWFyayI6Ik1vemlsbGFcLzUuMCAoV2luZG93cyBOVCA2LjM7IFdPVzY0KSBBcHBsZVdlYktpdFwvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lXC81MC4wLjI2NjEuMTAyIFNhZmFyaVwvNTM3LjM2In0.6-0IUWT4KtAbjnHC0IRrgi-HBw9t3laANnsPUxMXMUE';
        $this->_receiveData();
    }

    /**
     * 处理接受的POST数据
     */
    private function _receiveData()
    {
        if ($this->is_encrypt_post) {
            $post = $GLOBALS['HTTP_RAW_POST_DATA'];
            // TODO 数据解密处理
            $this->data = $post;
        } else {
            $this->data = $_POST;
        }
    }

    /**
     * 检测用户是否登录
     * @access protected
     * @return  int   用户UID
     */
    protected function isLogin()
    {
        // 检测参数
        if (!isset($this->data['token']) || empty($this->data['token'])) {
            $this->apiReturn(40001, '登录超时');
        }

        // 验证token
        try {
            $data = JWT::decode($this->data['token'], $this->secret_key);
            if (!isset($data->uid)) {
                throw new \Exception('登录超时', 40001);
            }
            $uid = $data->uid;
            //设备号码判断
            if ($this->is_mark) {
                if ($data->mark != $_SERVER['HTTP_USER_AGENT']) {
                    //设备号码发生更改，登录时效
                    throw new \Exception('登录超时', 40001);
                }
            }

            // 检测用户ID是否存在
            $condition = array();
            $condition['uid'] = $uid;
            $condition['status'] = array('NEQ', -1);
            $user_info = M('User')->where($condition)->find();
            if (!$user_info) {
                throw new \Exception('用户不存在', 40002);
            }

            if ($user_info['status'] == 1) {
                throw new \Exception('用户已被锁定', 40003);
            }

            if ($user_info['status'] == 2) {
                throw new \Exception('用户已被冻结', 40004);
            }

            if (strcmp($this->data['token'], $user_info['login_token']) !== 0) {
                throw new \Exception('登录超时', 40001);
            }

            // TODO 是否刷新TOKEN保持登录
            $this->user_name = $user_info['user_name'];
            $this->uid = $uid;
            return $uid;

        } catch (\Exception $e) {
            $this->apiReturn(40001, $e->getMessage());
        }
    }

    /**
     * 用户登录后获取token
     * @access  protected
     * @param   int $uid 用户UID
     * @return string
     */
    protected function jsonWebTokenEncode($uid)
    {
        $data = array(
            'iss' => 'sea',
            'uid' => $uid,
            'iat' => NOW_TIME,
            'mark' => $_SERVER['HTTP_USER_AGENT'],
//            'exp'   => NOW_TIME + 86400 * 7,      // token过期时间
        );
        return JWT::encode($data, $this->secret_key);
    }

    /**
     * 接口数据返回
     * @param int $code 结果状态码 格式：三位代码区块 二位编码
     * @param string $info 返回说明
     * @param array $data 返回数据
     * @return  void
     */
    protected function apiReturn($code, $info = '', $data = array())
    {
        $map = array();
        $map['code'] = $code;
        $map['status'] = $code == 0 ? true : false;
        $map['msg'] = $info;
        if (!empty($data)) $data['token'] = $this->jsonWebTokenEncode($this->uid);
        if (!empty($data)) $map['data'] = $data;
        echo "<pre>";
        echo "<hr />";
        print_r($map);die;
        // TODO 接口调用日志
        if ($this->is_encrypt) {
            // TODO 加密数据处理
        }
        $this->ajaxReturn($map, $this->type);
    }

}