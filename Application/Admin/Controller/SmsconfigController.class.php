<?php
namespace Admin\Controller;

/**
 * 短信接口配置管理
 * Class SmsconfigController
 * @package Admin\Controller
 */
class SmsconfigController extends AdminController
{
    /**
     * 配置列表
     */
    public function index()
    {
        $lists = M('SmsConfig')->field(true)->select();
        $this->meta_title = '短信接口配置管理';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 编辑短信接口
     */
    public function edit()
    {
        if(IS_POST) {
            $id = I('id', 0, 'intval');
            $data = array();
            $data['status'] = I('status', 0, 'intval');

            $config_array = explode(',', I('config_name'));
            if(is_array($config_array) && !empty($config_array)) {
                $config_info = array();
                foreach($config_array as $val) {
                    $config_info[$val] = I($val, '', 'trim');
                }
                $data['config'] = serialize($config_info);
            }
            $res = M('SmsConfig')->where(array('id'=>$id))->save($data);
            if(false === $res) {
                $this->error('修改短信接口失败');
            } else {
                $this->_cacheSmsConfig();
                $this->success('修改短信接口成功', Cookie('__forward__'));
            }
        } else {
            $id = I('id', 0, 'intval');
            $info = M('SmsConfig')->where(array('id'=>$id))->find();
            if(!$info) {
                $this->error('此短信接口不存在');
            }

            if($info['config'] != '') {
                $info['config'] = unserialize($info['config']);
            }

            $this->meta_title = '编辑短信接口';
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 设置默认发送短信接口
     */
    public function defset()
    {
        $id = I('id', 0, 'intval');
        // 检测是否存在
        $Model = M('SmsConfig');
        if(!$Model->where(array('id'=>$id))->find()) {
            $this->error('请选择要设置的短信接口');
        }

        // 将所使用的默认接口属性修改为 0
        $Model->where(1)->save(array('is_default'=>0));

        $res = $Model->where(array('id'=>$id))->save(array('is_default'=>1));
        if($res) {
            $this->_cacheSmsConfig();
            $this->success('设置默认短信接口成功');
        } else {
            $this->error('设置默认短信接口失败');
        }
    }

    /**
     * 缓存默认短信接口配置文件
     */
    private function _cacheSmsConfig()
    {
        $info = M('SmsConfig')->field('code,name,config')->where(array('is_default'=>1))->find();
        if($info) {
            if($info['config'] != '') {
                $info['config'] = unserialize($info['config']);
            }
            S('SMS_CONFIG', $info);
        } else {
            S('SMS_CONFIG', null);
        }
    }
}