<?php
namespace Admin\Controller;

/**
 * 用户反馈管理
 * Class FeedbackController
 * @package Admin\Controller
 */
class FeedbackController extends AdminController
{
    /**
     * 反馈列表
     */
    public function index()
    {
        $map = array();
        $status = I('status', '');
        if($status != '') {
            $map['status'] = intval($status);
        }

        $username = I('username', '', 'trim');
        if(!empty($username)) {
            $map['user_name'] = array('like', '%' . (string)I('username') . '%');
        }

        $total = M('Feedback')->where($map)->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page = new \Think\Page($total, $listRows);
        $p = $page->show();
        $limit = $page->firstRow . ',' . $page->listRows;
        $lists = M('Feedback')->field(true)->where($map)->limit($limit)->order('id desc')->select();
        $this->meta_title = '反馈管理';
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('page', $p ? $p : '');
        $this->assign('total', $total);
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 更改反馈记录状态
     */
    public function state()
    {
        $id = I('id', 0, 'intval');
        if(empty($id)) {
            $this->error('请选择要处理的反馈');
        }

        $status = I('status');
        if($status != 1 && $status != 2) {
            $this->error('非法操作');
        }

        $res = M('Feedback')->where(array('id'=>$id))->save(array('status'=>$status));
        if($res) {
            $result = '';
            if($status == 1) {
                $result = '标记为处理中...';
            } elseif($status == 2) {
                $result = '标记为已处理...';
            }
            $this->_log($result);
            $this->success('更改反馈状态成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 记录处理日志
     * @param   string  $result     处理说明  TODO
     * @return  bool
     */
    protected function _log($result = '')
    {
        $data = array(
            'operate_id'    => UID,
            'operate_name'  => session('admin.admin_name'),
            'result'        => $result,
            'add_time'      => NOW_TIME,
            'add_ip'        => get_client_ip(),
        );
        return M('FeedbackLog')->add($data);
    }
}