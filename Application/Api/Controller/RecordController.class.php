<?php

namespace Api\Controller;

/**
 * 日志管理
 * Class RecordController
 * @package Api\Controller
 */
class RecordController extends ApiController
{

    /**
     * 查询限制
     * @var string
     */
    protected $limit = '';

    /**
     * 初始化
     * 检测会员是否登录
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->uid = $this->isLogin();

        //计算分页
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $this->limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;
    }

    /**
     * 库存日志列表
     */
    public function storage()
    {
        // 查询条件
        $condition = array();
        $condition['user_id'] = $this->uid;
    }

    /**
     * 赠送记录
     */
    public function give()
    {
        $condition = array();
        if (isset($this->data['type']) && $this->data['type'] == 'add') {
            // 赠送
            $condition['sendee_id'] = $this->uid;
        } else {
            // 接受赠送
            $condition['user_id'] = $this->uid;
        }

        $field = 'id,user_name,sendee_name,seed_name,weight,info,add_time';

        $count = M('UserGive')->where($condition)->count();

        $lists = M('UserGive')->field($field)->where($condition)->order('id DESC')->limit($this->limit)->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );

        $this->apiReturn(0, '成功', $data);

    }
}