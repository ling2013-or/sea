<?php

namespace Game\Controller;

/**
 * 农场状态管理接口
 * Class StatusController
 * @package Game\Controller
 */
class StatusController extends GameController
{

    /**
     * 农场游戏好友可操作农场
     */
    public function filter()
    {
        $return = array();
        $return['status'] = array();
        if (!isset($this->data['friend_uids']) && empty($this->data['friend_uids'])) {
            $this->apiReturn(0, 'success', $return);
        }

        $friend_uids = explode(',', trim($this->data['friend_uids']));
        if (empty($friend_uids)) {
            $this->apiReturn(0, 'success', $return);
        }

        // 排除非好友
        $friend_uids = M('HomeFriend')->where(array('uid' => $this->uid, 'fuid' => array('IN', $friend_uids)))->getField('fuid', true);

        // 获取好友状态
        $lists = M('GameFarm')->where(array('user_id' => array('IN', $friend_uids)))->select();

        if (empty($lists)) {
            $this->apiReturn(0, 'success', $return);
        }

        $crop = get_crop_cache();

        foreach ($lists as $farm) {
            $status = json_decode($farm['status']);
            foreach ($status as $value) {
                $crop_id = intval($value['crop_id']);
                if ($crop_id > 0) {
                    $crop_time = $crop[$crop_id]['five'];
                    if (NOW_TIME - $value['plant_time'] >= $crop_time && $value['plant_time'] > 0) {
                        // 可收获
                        if ($value['remain_output'] > $value['least_remain_output']) {
                            // 成熟时间
                            if (!isset($value['steal_record'][$this->uid])) {
                                $harvest_time = $value['plant_time'] + $crop_time;
                                $return['status'][$farm['user_id']][1] = $harvest_time;
                            }
                        }
                    } elseif (NOW_TIME - $value['plant_time'] < $crop_time) {
                        // 可除草，杀虫
                        if ($value['weed_num'] > 0) {
                            $return['status'][$farm['user_id']][2] = 1;
                        }

                        if ($value['pest_num'] > 0) {
                            $return['status'][$farm['user_id']][3] = 1;
                        }
                    }
                }
            }
        }
        $this->apiReturn(0, 'success', $return);
    }
}