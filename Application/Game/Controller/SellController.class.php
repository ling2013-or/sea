<?php

namespace Game\Controller;

/**
 * 农场游戏会员出售功能管理
 * Class SellController
 * @package Game\Controller
 */
class SellController extends GameController
{

    /**
     * 出售仓库作物
     */
    public function repertory()
    {
        if (!isset($this->data['type'])) {
            $this->apiReturn(73001, '请选择要出售的分类');
        }

        $type = strtolower($this->data['type']);
        if ($type != 'crop' && $type != 'seed') {
            $this->apiReturn(73001, '请选择要出售的分类');
        }
        if ($type == 'crop') {
            $table_name = 'GameUserCrop';
            $log_type = 6;
        } else {
            $table_name = 'GameUserSeed';
            $log_type = 7;
        }

        // 返回数据
        $return = array();

        if (isset($this->data['crop_id']) && !empty($this->data['crop_id'])) {
            // 单个出售
            if (!isset($this->data['num']) || empty($this->data['num'])) {
                $this->apiReturn(73002, '请选择要卖出的数量');
            }

            $crop_id = intval($this->data['crop_id']);
            $num = intval($this->data['num']);

            // 检测果实是否存在
            $condition = array();
            $condition['user.user_id'] = $this->uid;
            $condition['user.crop_id'] = $crop_id;
            $condition['user.amount'] = array('GT', 0);

            $crop = M($table_name)
                ->alias('user')
                ->field('user.*,crop.crop_name,crop.plant_level,crop.sell_price,crop.buy_price')
                ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
                ->where($condition)
                ->find();

            if (!$crop) {
                $this->apiReturn(73003, '要卖出的物品不存在');
            }

            if ($crop['is_lock'] == 1) {
                $this->apiReturn(73004, '已锁定物品不能卖出');
            }

            if ($crop['amount'] < $num) {
                $this->apiReturn(73005, '卖出数量不能大于仓库数量');
            }

            $price = $type == 'crop' ? $crop['sell_price'] * $num : ceil($crop['buy_price'] * 0.5) * $num;

            $save = array('amount', array('EXP', 'amount - ' . $num));

            $ids = array($crop['id']);

            $data = array('amount' => $num, 'crop_id' => $crop_id, 'money' => $price);

            $return['crop_id'] = $crop_id;
            $return['crop_name'] = $crop['crop_name'];

        } else {
            // 全部出售
            $condition = array();
            $condition['user.user_id'] = $this->uid;
            $condition['user.is_lock'] = 0;
            $crop_list = M($table_name)
                ->alias('user')
                ->field('user.*,crop.crop_name,crop.plant_level,crop.sell_price,crop.buy_price')
                ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
                ->where($condition)
                ->select();

            // 统计价格
            if (!$crop_list) {
                $this->apiReturn(73008, '暂无可卖物品');
            }

            $ids = array();
            $price = 0;
            $data = array();
            foreach ($crop_list as $crop) {
                $ids[] = $crop['id'];
                $money = $type == 'crop' ? $crop['sell_price'] * $crop['amount'] : ceil($crop['buy_price'] * 0.5) * $crop['amount'];
                $price += $money;
                // TODO 日志
                $data[] = array('amount' => $crop['amount'], 'crop_id' => $crop['crop_id'], 'money' => $money);
            }
            $save = array('amount'=>0);
        }

        D('GameFarmLog')->add($log_type, $this->uid, $this->owner_id, $data);

        // 更新数据库    TODO 成功失败校验
        M('GameUserCrop')->where(array('id' => array('IN', $ids)))->save($save);
        M('GameUser')->where(array('uid' => $this->uid))->setInc('money', $price);

        $return['money'] = $price;
        $this->apiReturn(0, '卖出成功', $return);
    }
}