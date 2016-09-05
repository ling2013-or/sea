<?php

namespace Api\Controller;

/**
 * 区域管理，三级地区联动
 * Class AreaController
 * @package Api\Controller
 */
class AreaController extends ApiController
{

    /**
     * 区域列表
     */
    public function lists()
    {
        $area_id = isset($this->data['area_id']) && !empty($this->data['area_id']) ? intval($this->data['area_id']) : 0;

        $condition = array();
        $condition['area_parent_id'] = $area_id;

        $lists = M('Area')->field('area_id,area_name')->where($condition)->select();
        $data = $lists ? $lists : '';

        $this->apiReturn(0, '成功', $data);
    }
}