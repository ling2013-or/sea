<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 后台登录验证
 * Class LoginController
 * @package Admin\Controller
 */
class PublicController extends Controller
{
    public function index(){
        $this->display();
    }

    /**
     * 登录/验证
     * @access pulic
     * @return void
     */
    public function login()
    {
        if (IS_POST) {
            // 读取数据库配置文件
            $config = S('DB_CONFIG_DATA');
            if (!$config) {
                $config = api('Config/lists');

                S('DB_CONFIG_DATA', $config);
            }
            $smsAuth = $config['ADMIN_SMS_AUTH'];
            //获取输入参数
            $uname = I('uname');
            $upwd = I('upwd');
            $vcode = I('vcode');

            //空验证
            if (empty($vcode)) {
                $this->error(1);
            }
            if (empty($uname)) {
                $this->error(2);
            }
            if (empty($upwd)) {
                $this->error(3);
            }

            //验证验证码
            if (!verifycheck($vcode)) {
                $this->error(1);
            }

            //验证用户名
            $adminWhere['status'] = 1;
            $adminWhere['admin_name'] = $uname;
            $admin = M('Admin')->field(true)->where($adminWhere)->find();
            if (!$admin) {
                $this->error(2);
            }

            //验证账户密码（加密模式为密码md5后+盐参后再md5）;
            $upwd = md5($upwd);
            $inputpwd = md5($upwd . $admin['admin_salt']);
            $truepwd = $admin['admin_pwd'];
            if ($inputpwd != $truepwd) {
                $this->error(3);
            }

            //验证手机短信验证码是否正确
            if ($smsAuth == 1) {
                //手机短信验证码
                $scode = I('scode');
                //短信验证码验证
                if (!$scode) {
                    $this->error(5);
                }
                if ($scode != $_SESSION['scode']['code']) {
                    $this->error(5);
                } else if (empty($_SESSION['scode']['code'])) {
                    $this->error(6);
                } else {

                }
            }

            //查询用户组权限
            $groupWhere['status'] = 1;
            $groupWhere['group_id'] = $admin['group_id'];
            $group = M('Group')->field('group_auth')->where($groupWhere)->find();

            //查询模块列表
            if (!$group) {
                $this->error(4);
            } else {
                $authListId = array_unique(explode(',', $group['group_auth']));
                $authWhere['module_id'] = array('IN', $authListId);
                $authWhere['status'] = 1;
                $authList = M('ModuleAuth')->field(true)->where($authWhere)->order('module_index')->select();
                foreach ($authList as $value) {
                    //获取模块权限列表
                    $moduleAuth[] = $value['module_bind'];
                    //获取用户菜单
                    if ($value['is_menu'] == 1) {
                        $menu[] = $value;
                    }
                }

                //生成sidebar
                //一级菜单
                foreach ($menu as $key => $value) {
                    if ($value['parent_id'] == 0) {
                        $top[] = $value;
                        unset($value[$key]);
                    }
                }
                //二级菜单
                foreach ($top as $key => $value) {
                    foreach ($menu as $v) {
                        if ($v['parent_id'] == $value['module_id']) {
                            $child[] = $v;
                        }
                    }
                    $top[$key]['child'] = $child;
                    unset($child);
                }
                //拼接为导航
                $sidebar = '<ul class="nav nav-pills nav-stacked custom-nav">';
                foreach ($top as $value) {
                    if (empty($value['child'])) {
                        $sidebar .= '<li><a href="' . U($value['module_bind']) . '"><i class="' . $value['module_img'] . '"></i> ' . $value['module_name'] . '</a></li>';
                    } else {
                        $sidebar .= '<li class="menu-list"><a href="' . U($value['module_bind']) . '"><i class="' . $value['module_img'] . '"></i>  ' . $value['module_name'] . '</a><ul class="sub-menu-list">';
                        foreach ($value['child'] as $v) {
                            $sidebar .= '<li><a href="' . U($v['module_bind']) . '" target="main-body"> ' . $v['module_name'] . '</a></li>';
                        }
                        $sidebar .= '</ul></li>';
                    }
                }
                $sidebar .= '</ul>';

                unset($admin['admin_pwd']);
                unset($admin['admin_salt']);
                unset($_SESSION['scode']);
                session('admin', $admin);
                session('authList', $moduleAuth);
                session('sidebar', $sidebar);

                $this->success('登录成功...', U('Index/index'));
            }
        } else {
            if (is_login()) {
                $this->redirect('Index/index');
            } else {
                // 读取数据库配置文件
                $config = S('DB_CONFIG_DATA');
                if (!$config) {
                    $config = api('Config/lists');
                    S('DB_CONFIG_DATA', $config);
                }
                $this->assign('sms', $config['ADMIN_SMS_AUTH']);
                $this->display();
            }
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
//		session('admin', null);
        session('[destroy]');
        $this->success('您已退出登录', U('login'));
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

    /**
     * 给用户发送短信息
     */
    public function checkUser()
    {
        if (IS_POST) {
            //获取输入参数
            $uname = I('uname');
            $upwd = I('upwd');
            $vcode = I('vcode');


            //空验证
            if (!$vcode) {
                $this->error(1);
            }
            if (!$uname) {
                $this->error(2);
            }
            if (!$upwd) {
                $this->error(3);
            }


            //验证用户名
            $adminWhere['status'] = 1;
            $adminWhere['admin_name'] = $uname;
            //查询用户信息
            $admin = M('Admin')->field(true)->where($adminWhere)->find();
            if (!$admin) {
                $this->error(2);
            }

            //验证账户密码（加密模式为密码md5后+盐参后再md5）;
            $upwd = md5($upwd);
            $inputpwd = md5($upwd . $admin['admin_salt']);
            $truepwd = $admin['admin_pwd'];
            if ($inputpwd != $truepwd) {
                $this->error(3);
            }

            //获取用户的手机号码
            $phone = $admin['phone'];
            //获取用户的名称
            if ($admin['true_name']) {
                $name = $admin['true_name'];
            } else {
                $name = $admin['admin_name'];
            }

            //生成一个验证码
            if ($_SESSION['scode']['code']) {
                //一分钟之内只能发一次（同一个模块内）
                $res = intval(NOW_TIME) - intval($_SESSION['scode']['time']);
                if (abs($res) < 120) {
                    $this->error(6);
                }
                $_SESSION['scode']['time'] = NOW_TIME;
                $code = $_SESSION['scode']['code'];

            } else {
                $code = code(6);
                $arr = array('code' => $code, 'time' => NOW_TIME);
                $_SESSION['scode'] = $arr;
            }

            //获取短信模板配置
            //模板ID
            $tplAlias = 'code';
            //获取模板信息
            $smsTpl = D('SmsTpl');
            $tpList = $smsTpl->lists($tplAlias, 'alias');
            //模板参数
            $param = explode(',', $tpList['0']['param']);
            //模板数据
            $example = $tpList['0']['content'];

            $tpl = '';
            foreach ($param as $para) {
                if ($para == '{name}') {
                    $tpl = str_replace($para, $name, $example);
                }
                if ($para == '{code}') {
                    $tpl = str_replace($para, $code, $example);
                }

            }
            //组装信息内容
            $content = $tpl;
            //获取手机号码
            //发送duanxin
            //给用户发送信息
            $result = sendsms($phone, $content, '', 1);
            //TODO : 短信发送失败的处理结果，以及提示
            $this->success($code);
        }

    }

    /**
     * 创建验证码
     * @return string
     */
    public function createCode()
    {
        $str = "1,2,3,4,5,6,7,8,9,0";
        $list = explode(",", $str);
        $cmax = count($list) - 1;
        $verifyCode = '';
        for ($i = 0; $i < 6; $i++) {
            $randnum = mt_rand(0, $cmax);
            $verifyCode .= $list[$randnum];
        }
        return $verifyCode;
    }


}