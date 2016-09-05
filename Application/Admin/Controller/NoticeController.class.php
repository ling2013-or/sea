<?php

namespace Admin\Controller;

/**
 * 后台通知消息管理
 * Class NoticeController
 * @package Admin\Controller
 */
class NoticeController extends AdminController
{

    /**
     * 通知消息统计
     */
    public function stats()
    {
        // 统计未完成订单

        // 统计未处理反馈
        $feedback = M('Feedback')->where(array('status'=>0))->count();
        $data = array(
            'feedback'  => $feedback,
            'total'     => $feedback,   // 总和
        );
        $this->ajaxReturn($data);
    }
}