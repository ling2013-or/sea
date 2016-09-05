<?php

namespace Admin\Controller;

/**
 * 地区管理
 * Class AreaController
 * @package Admin\Controller
 */
class AreaController extends AdminController
{
    /**
     * 获取地区列表
     */
    public function lists()
    {
        $area_id = I('area_id', 0, 'intval');

        $condition = array();
        $condition['area_parent_id'] = $area_id;

        $lists = M('Area')->field('area_id,area_name')->where($condition)->select();
        $data = $lists ? $lists : '';
        $this->ajaxReturn($data);
    }
}