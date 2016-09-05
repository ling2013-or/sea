<?php

namespace Api\Controller;

/**
 * 用户反馈管理
 * Class FeedbackController
 * @package Api\Controller
 */
class FeedbackController extends ApiController
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
     * 提交反馈建议
     */
    public function index()
    {
        if (!isset($this->data['content']) && empty($this->data['content'])) {
            $this->apiReturn(41101, '反馈内容不能为空');
        }

        // 获取用户名
        $username = M('User')->where(array('uid' => $this->uid))->getField('user_name');

        $data = array(
            'uid' => $this->uid,
            'user_name' => $username,
            'content'   => htmlspecialchars($this->data['content']),
            'status'    => 0,
            'add_time'  => NOW_TIME,
        );
        if(!M('Feedback')->add($data)) {
            $this->apiReturn(-1, '系统繁忙，请稍候重试');
        }

        $this->apiReturn(0, '反馈成功');
    }
}