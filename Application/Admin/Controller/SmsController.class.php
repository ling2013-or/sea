<?php

namespace Admin\Controller;

use Think\Page;

/**
 * 短信模板服务
 * Class LoginController
 * @package Admin\Controller
 */
class SmsController extends AdminController
{
    /**
     * 手机短信服务列表
     * @access pulic
     * @return void
     */
    public function templete()
    {
        $where = array();
        //搜索
        if (isset($_GET['query'])) {
            $query = I('query');
            $map['id'] = array('eq', $query);
            $map['name'] = array('eq', $query);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        $condition = array();

        $title = I('title', '', 'trim');
        if (!empty($title)) {
            $condition['name'] = array('LIKE', '%' . $title . '%');
        }
        $alias = I('alias', '', 'trim,strtoupper');
        if (!empty($alias)) {
            $condition['alias'] = array('LIKE', '%' . $alias . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('SmsTpl')->where($condition)->count();
        //实例化分页类
        $page = new Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取标前缀
        $lists = M('SmsTpl')->field(true)->where($condition)->limit($limit)->select();
        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->meta_title = '模板列表';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 添加短信模板信息
     */
    public function tpladd()
    {
        if (IS_POST) {
            $Model = D('SmsTpl');
            if ($Model->create()) {
                if ($Model->add()) {
                    $this->success('添加短信模板成功', Cookie('__forward__'));
                } else {
                    $this->error('添加短信模板失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->meta_title = '添加模板';
            $this->display();
        }
    }

    /**
     * 编辑短信模板信息
     */
    public function tpledit()
    {
        $id = I('id', 0, 'intval');
        if (empty($id)) {
            $this->error('请选择要编辑的短信模板');
        }
        if (IS_POST) {
            $Model = D('SmsTpl');
            if ($Model->create()) {
                if (false !== $Model->where(array('id' => $id))->save()) {
                    $this->success('修改成功', Cookie('__forward__'));
                } else {
                    $this->error('编辑模板失败');
                }
            } else {
                $this->error($Model->getError());
            }
        } else {
            $info = M('SmsTpl')->where(array('id' => $id))->find();
            if (!$info) {
                $this->error('短信模板不存在');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑模板';
            $this->display();
        }
    }

    /**
     * 短信发送日志
     */
    public function log()
    {
        $condition = array();
        /* 时间段查询 */
        if (isset($_GET['start_time'])) {
            $start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['start_time']);
            $start_unixtime = $start_time ? strtotime($_GET['start_time']) : null;
            if ($start_unixtime) {
                $condition['add_time'][] = array('EGT', $start_unixtime);
            }
        }

        if (isset($_GET['end_time'])) {
            $end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $_GET['end_time']);
            $end_unixtime = $end_time ? strtotime($_GET['end_time']) : null;
            if ($end_unixtime) {
                $condition['add_time'][] = array('LT', $end_unixtime + 86400);
            }
        }

        // 用户名查询
        $mobile = I('mobile', '', 'trim');
        if (!empty($mobile)) {
            $map['mobile'] = array('like', '%' . $mobile . '%');
        }

        //分页
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        //统计总数
        $total = M('SmsLog')->where($condition)->count();
        //实例化分页类
        $page = new Page($total, $listRows);
        $p = $page->show();
        //每页显示数量个数
        $limit = $page->firstRow . ',' . $page->listRows;
        //获取标前缀
        $lists = M('SmsLog')->field(true)->where($condition)->order('id DESC')->limit($limit)->select();

        //获取当天的数据
        $day = $this->statistics(array('add_time' => array('BETWEEN', array(strtotime('today'), NOW_TIME))));

        //查询当月的数据
        $time = array('add_time' => array('BETWEEN', array(strtotime(date('Y-m-01 00:00:00')), NOW_TIME)));
        $month = $this->statistics($time);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->assign('days', $day);
        $this->assign('months', $month);
        $this->meta_title = '短信日志';
        $this->display();
    }

    /**
     * 统计短信日志的条数，返回对应条件的统计结果
     * @param array $where 判断条件
     * @return int
     */
    protected function statistics($where = array())
    {
        return M('SmsLog')->where($where)->count();
    }


}