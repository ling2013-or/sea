<?php

namespace Game\Controller;

/**
 * 用户仓库管理
 * Class UsercropController
 * @package Game\Controller
 */
class RepertoryController extends GameController
{

    /**
     * 用户作物仓库列表
     */
    public function crop()
    {
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        $condition = array();
        $condition['user.user_id'] = $this->uid;
        $condition['user.amount'] = array('GT', 0);
        $count = M('GameUserCrop')
            ->alias('user')
            ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
            ->where($condition)
            ->count();

        $lists = M('GameUserCrop')
            ->alias('user')
            ->field('user.crop_id,user.is_lock,user.amount,crop.crop_name,crop.plant_level,crop.sell_price')
            ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
            ->where($condition)
            ->order('plant_level ASC')
            ->limit($limit)
            ->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );
        $this->apiReturn(0, 'success', $data);
    }

    /**
     * 用户种子列表
     */
    public function seed()
    {
        if (isset($this->data['page']) && intval($this->data['page']) > 0) {
            $this->page = intval($this->data['page']);
        }
        if (isset($this->data['page_size']) && intval($this->data['page_size']) > 0) {
            $this->page_size = intval($this->data['page_size']);
        }
        $limit = ($this->page - 1) * $this->page_size . ',' . $this->page_size;

        $condition = array();
        $condition['user.user_id'] = $this->uid;
        $condition['user.amount'] = array('GT', 0);
        $count = M('GameUserSeed')
            ->alias('user')
            ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
            ->where($condition)
            ->count();

        $lists = M('GameUserSeed')
            ->alias('user')
            ->field('user.crop_id,user.is_lock,user.amount,crop.crop_name,crop.plant_level,crop.buy_price,crop.growth_cycle')
            ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
            ->where($condition)
            ->order('plant_level ASC')
            ->limit($limit)
            ->select();

        $data = array(
            'page' => $this->page,
            'count' => $count,
            'list' => $lists ? $lists : '',
        );
        $this->apiReturn(0, 'success', $data);
    }

    /**
     * 种子，作物锁定
     */
    public function lock()
    {
        $type = array('seed', 'crop');
        if (!isset($this->data['type']) || !in_array(strtolower($this->data['type']), $type)) {
            $this->apiReturn(72611, '请选择要锁定的分类');
        }

        if(!isset($this->data['id']) || empty($this->data['id'])) {
            $this->apiReturn(72612, '请选择要锁定的物品');
        }

        $lock = isset($this->data['lock']) && intval($this->data['lock']) == 1 ? 1 : 0;

        $type = strtolower($this->data['type']);
        switch ($type) {
            case 'seed':
                $res  = M('GameUserSeed')->where(array('user_id'=>$this->uid, 'crop_id'=>$this->data['id']))->save(array('is_lock'=>$lock));
                break;
            case 'crop':
                $res  = M('GameUserCrop')->where(array('user_id'=>$this->uid, 'crop_id'=>$this->data['id']))->save(array('is_lock'=>$lock));
                break;
        }

        $str = $lock == 1 ? '锁定' : '解锁';
        if(false !== $res) {
            $this->apiReturn(0, $str . '成功');
        } else {
            $this->apiReturn(72613, $str . '失败');
        }
    }
}