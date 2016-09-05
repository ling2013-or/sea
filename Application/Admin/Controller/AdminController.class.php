<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 后台公共配置文件
 * Class AdminController
 * @package Admin\Controller
 */
class AdminController extends Controller
{
    /**
     * 后台初始化
     */
    protected function _initialize()
    {
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID', is_login());
        if(!UID) {
            // 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }

        //获取当前页面地址
        $auth = CONTROLLER_NAME.'/'.ACTION_NAME;

        // 验证权限(如果是超管不验证)
        if (UID != C('USER_ADMINISTRATOR')) {
            if (!in_array($auth,session('authList'))) {
                $this->error('您暂时无权限访问');
            }
        }

        // 读取数据库配置文件
        $config = S('DB_CONFIG_DATA');
        if(!$config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        C($config); //添加配置
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
        D('AdminOperateLog')->record($message, 0);
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
        D('AdminOperateLog')->record($message, 1);
        parent::success($message, $jumpUrl, $ajax);
    }


    /**
     * 后台管理员操作日志列表
     */
    public function adminLog()
    {
        $where = array();
        if($_GET['query']){
            $map['t1.id'] = I('query');
            $map['t2.true_name'] = I('query');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        /* 时间段查询 */
        if(isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if($start_unixtime) {
                $where['t1.add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if(isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $where['t1.add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        // 只统计主表信息，账户金额不作为搜索条件
        $total = M('AdminOperateLog')->alias('t1')->join('__ADMIN__ t2 ON t1.uid = t2.admin_id','LEFT')->where($where)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;

        $field = 't1.*,t2.true_name';
        $lists = M('AdminOperateLog')->alias('t1')
            ->join('__ADMIN__ t2 ON t1.uid = t2.admin_id','LEFT')
            ->field($field)
            ->where($where)
            ->limit($limit)
            ->order('id DESC')
            ->select();
        $this->meta_title = '操作日志';
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 单图片上传
     *
     * 图片上传由 PICTURE_UPLOAD 配置
     *
     * @param string $path 上传图片的次级目录(图片分类目录)
     * @return string
     */
    public function uploadimg($path = 'default/')
    {
        $upload = new \Think\Upload(C('PICTURE_UPLOAD'));
        $upload->savePath = trim($path, '/') . '/';
        $upload->saveName = md5(uniqid($path, true));
        $info = $upload->upload();

        if ($info) {
            $root_path = '';
            foreach ($info as $file) {
                $root_path = $file['savepath'] . $file['savename'];
            }
            if ($root_path) {
                $img_path =  __ROOT__ . '/' . trim(C('PICTURE_UPLOAD.rootPath'), './') . '/' . $root_path;
                $this->ajaxReturn(array('status' => 1, 'info' => $img_path));
                //$this->success($img_path);
            }
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => $upload->getError()));
            //$this->error($upload->getError());
        }
        return false;
    }

}