<?php

namespace Game\Controller;

/**
 * 游戏商场管理
 * Class ShopController
 * @package Game\Controller
 */
class ShopController extends GameController
{

    /**
     * 商城信息列表列表
     */
    public function lists()
    {
        if (!isset($this->data['category'])) {
            $this->apiReturn(75101, '请选择要查看的商品分类');
        }
        $category_arr = array('seed', 'tool', 'decorat');
        $category = strtolower($this->data['type']);
        if (!in_array($category, $category_arr)) {
            $this->apiReturn(75101, '请选择要查看的商品分类');
        }

        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        if ($category == 'seed') {
            // 种子列表
            $condition = array();
            $condition['is_hidden'] = 0;
            $condition['status'] = 1;
            if (isset($this->data['type'])) {
                $type = intval($this->data['type']);
                switch ($type) {
                    case 0:     // 普通作物
                    case 1:     // 红土地作物
                    case 2:     // 黑土地作物
                    case 3:     // 有机作物
                        $condition['type'] = $type;
                        break;

                }
            }
            $field = 'crop_id,crop_name,plant_level,buy_price,growth_cycle,expect_revenue';
            $count = M('GameCrop')->where($condition)->count();

            $lists = M('GameCrop')->field($field)->where($condition)->order('plant_level ASC')->limit($limit)->select();
        } elseif ($category == 'tool') {
            // 道具列表
            $condition = array();
            $condition['status'] = 1;
            if (isset($this->data['type'])) {
                $type = intval($this->data['type']);
                switch ($type) {
                    case 1:     // 化肥
                    case 2:     // 狗粮
                    case 3:     // 狗狗
                        $condition['type'] = $type;
                        break;

                }
            }
            $field = array('tool_id,name,depict,price');

            $count = M('GameCrop')->where($condition)->count();
            $lists = M('GameCrop')->field($field)->where($condition)->order('type ASC')->limit($limit)->select();
        } else {
            // 装饰列表
            // TODO
        }

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );
        $this->apiReturn(0, 'success', $data);
    }

    /**
     * 商品详情
     */
    public function detail()
    {
        if (!isset($this->data['category'])) {
            $this->apiReturn(75101, '请选择要查看的商品分类');
        }
        $category_arr = array('seed', 'tool', 'decorat');
        $category = strtolower($this->data['type']);
        if (!in_array($category, $category_arr)) {
            $this->apiReturn(75101, '请选择要查看的商品分类');
        }

        if (!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(75111, '请选择择要查看的商品');
        }

        if ($category == 'seed') {
            // 种子详情
            $condition = array();
            $condition['crop_id'] = intval($this->data['id']);
            $condition['is_hidden'] = 0;
            $condition['status'] = 1;
            $field = 'crop_id,crop_name,plant_level,buy_price,growth_cycle,harvest_num,expect_revenue,expect_output,sell_price,exp,depict,type';
            $data = M('GameCrop')->field($field)->where($condition)->find();
            if(!$data) {
                $this->apiReturn(75112, '商品未找到');
            }
        } elseif ($category == 'tool') {
            // 道具详情
            $condition = array();
            $condition['status'] = 1;
            $condition['tool_id'] = intval($this->data['id']);
            $field = array('tool_id,name,depict,price');

            $data = M('GameCrop')->field($field)->where($condition)->find();
            if(!$data) {
                $this->apiReturn(75112, '商品未找到');
            }
        } else {
            // 装饰详情
            // TODO
        }

        $this->apiReturn(0, 'success', $data);
    }
}