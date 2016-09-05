<?php

namespace Game\Controller;

/**
 * 农场游戏设置
 * Class SettingController
 * @package Game\Controller
 */
class SettingController extends GameController
{

    /**
     * 农场提示语配置
     */
    public function tips()
    {
        if (isset($this->data['confirm']) && $this->data['confirm'] == 1) {

            if (!isset($this->data['type'])) {
                $this->apiReturn(72501, '修改类型不能为空');
            }

            $tips = json_decode($this->farm_info['tips'], true);
            switch (strtolower($this->data['type'])) {
                case 'weed_help':
                    $tips['weed_help'] = isset($this->data['value']) && !empty($this->data['value']) ? htmlspecialchars($this->data['value']) : C('GAME_TIPS_WEED_HELP');
                    break;
                case 'weed_bad':
                    $tips['weed_bad'] = isset($this->data['value']) && !empty($this->data['value']) ? htmlspecialchars($this->data['value']) : C('GAME_TIPS_WEED_BAD');
                    break;
                case 'pest_help':
                    $tips['pest_help'] = isset($this->data['value']) && !empty($this->data['value']) ? htmlspecialchars($this->data['value']) : C('GAME_TIPS_PEST_HELP');
                    break;
                case 'pest_bad':
                    $tips['pest_bad'] = isset($this->data['value']) && !empty($this->data['value']) ? htmlspecialchars($this->data['value']) : C('GAME_TIPS_PEST_BAD');
                    break;
                case 'water':
                    $tips['water'] = isset($this->data['value']) && !empty($this->data['value']) ? htmlspecialchars($this->data['value']) : C('GAME_TIPS_HELP_WATER');
                    break;
                default:
                    $this->apiReturn(72502, '修改类型不存在');
                    break;
            }

            if (false !== M('GameFarm')->where(array('user_id' => $this->uid))->save(array('tips' => json_encode($tips)))) {
                $this->apiReturn(0, '修改成功', $tips);
            } else {
                $this->apiReturn(72503, '修改失败');
            }
        } else {
            $this->apiReturn(0, '修改成功', json_decode($this->farm_info['tips'], true));
        }
    }
}