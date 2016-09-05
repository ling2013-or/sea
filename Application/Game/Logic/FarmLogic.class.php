<?php

namespace Game\Logic;

/**
 * 农场逻辑处理
 * Class FarmBadLogic
 * @package Game\Logic
 */
class FarmLogic
{

    /**
     * 农场升级奖励处理
     * @param int $uid 农场所属用户ID
     * @return mixed
     */
    protected function levelReward($uid)
    {
        // 重新获取农场信息
        $farm_info = M('GameFarm')->where(array('user_id' => $uid))->find();
        // 检测是否升级
        if ($farm_info['exp'] < $farm_info['levelup']) {
            return false;
        }

        // 获取当前等级
        $level = farm_exp_to_level($farm_info['exp']);
        // 获取升级奖励
        $reward = M('GameLevelReward')->where(array('level' => $level))->find();
        if (!$reward) {
            return false;
        }

        $tips = array(
            'title' => '升级奖励',
            'direction' => '这么快就升级到' . $reward['level'] . '级了啊？真是神速，奖励你' . $reward['depict'] . '，快到背包里看看吧。',
            'level' => $level,
            'item' => array(
                'type' => $reward['type'],
                'param' => $reward['param'],
                'num' => $reward['num'],
            ),
        );

        // 更新数据库
        switch ($reward['type']) {
            case 1:     // 种子
                // 获取种子信息
                $crop = get_crop_cache();

                // 检测种子仓库中是否存在该种子
                $condition = array('crop_id' => $reward['param'], 'user_id' => $uid);
                $Model = M('GameUserSeed');
                $check = $Model->where($condition)->find();
                if ($check) {
                    // 更新
                    $Model->where($condition)->setInc('amount', $reward['num']);
                } else {
                    // 获取种子详情
                    if (!isset($crop[$reward['param']])) {
                        // 此种子不存在
                        return false;
                    }
                    // 插入
                    $data = array(
                        'user_id' => $uid,
                        'crop_id' => $reward['param'],
                        'amount' => $reward['num'],
                    );
                    $Model->add($data);
                }
                break;
            case 2:     // 装饰
                $condition = array();
                $condition['user_id'] = $uid;
                $condition['de_id'] = $reward['param'];
                $condition['type'] = $reward['style'];
                $Model = M('GameUserDecorat');
                $check = $Model->where($condition)->find();

                $decorat_info = M('GameDecorat')->where(array('tool_id' => $reward['param']))->find();
                if ($decorat_info) {
                    return false;
                }

                if ($check) {
                    $data = array();
                    $data['status'] = 1;    // 装扮
                    if ($check['valid_time'] > 0) {
                        //检测当前装扮，如果已过期则重新购买
                        if (($check['valid_time'] + $check['add_time']) >= NOW_TIME) {
                            $data['valid_time'] = $check['valid_time'] + $decorat_info['valid_time'];
                        } else {
                            $data['valid_time'] = $decorat_info['valid_time'];
                            $data['add_time'] = NOW_TIME;
                        }
                        $Model->where(array('id' => $check['id']))->save($data);
                    }
                } else {
                    $data = array(
                        'user_id' => $uid,
                        'de_id' => $reward['param'],
                        'type' => $reward['type'],
                        'valid_time' => $decorat_info['valid_time'],
                        'add_time' => NOW_TIME,
                    );
                    $Model->add($data);
                }
                break;
            case 3:     // 化肥道具
                $condition = array('tool_id' => $reward['param'], 'user_id' => $uid, 'tool_type' => 1);
                $Model = M('GameUserTools');
                $check = $Model->where($condition)->find();
                if ($check) {
                    // 更新
                    $Model->where($condition)->setInc('num', $reward['num']);
                } else {
                    $tools_info = M('GameTools')->where(array('tool_id' => $reward['param'], 'type' => 1))->find();
                    if ($tools_info) {
                        // 此化肥不存在
                        return false;
                    }
                    // 插入
                    $data = array(
                        'user_id' => $uid,
                        'tool_id' => $reward['param'],
                        'num' => $reward['num'],
                        'type' => 1,
                    );
                    $Model->add($data);
                }
                break;
            default:
                return false;
        }

        // 更新用户下一级别经验
        $levelup = farm_level_to_exp($level + 1);
        M('GameFarm')->where(array('user_id' => $uid))->save(array('levelup' => $levelup));

        return $tips;
    }

    /**
     * 农场刷草、虫、旱
     * @param int $uid 农场所属用户ID
     * @param array $status 农场状态
     * @param int $time 上次操作时间
     * @param int $weather 当前天气
     * @return bool
     */
    public function bad($uid, $status, $time = 0, $weather = 1)
    {
        // 操作周期
        $limit_time = C('GAME_REFRESH_BAD_TIME') ? C('GAME_REFRESH_BAD_TIME') : 3600;

        $time = $time ? $time : M('GameFarm')->where(array('user_id' => $uid))->getField('rand_time');

        if (NOW_TIME - $time <= $limit_time) {
            return false;
        }

        M('GameFarm')->where(array('user_id' => $uid))->save(array('rand_time' => NOW_TIME));

        if (mt_rand(0, 100) > 20) {
            return false;
        }

        // 获取种子信息
        $crop = get_crop_cache();

        foreach ($status as $key => $val) {
            // 作物生长时间
            $plant_time = NOW_TIME - $val['plant_time'];

            if ($plant_time < $crop[$val['crop_id']]['five']) {
                // 刷草
                if ($val['weed_num'] == 0) {
                    $rand = mt_rand(1, 100);
                    if ($rand < 3) {
                        $val['weed_num'] = 3;
                    } elseif ($rand < 6) {
                        $val['weed_num'] = 2;
                    } elseif ($rand < 20) {
                        $val['weed_num'] = 1;
                    }
                }

                // 刷虫
                if ($val['pest_num'] == 0 && $plant_time > $crop[$val['crop_id']]['three']) {
                    $rand = mt_rand(1, 100);
                    if ($rand < 3) {
                        $val['pest_num'] = 3;
                    } elseif ($rand < 6) {
                        $val['pest_num'] = 2;
                    } elseif ($rand < 15) {
                        $val['pest_num'] = 1;
                    }
                }

                // 刷干旱
                if ($weather == 1 && $val['humidity'] == 1) {
                    if (mt_rand(0, 100) < 8) {
                        $val['humidity'] = 0;
                    }
                }
            }

            $status[$key] = $val;
        }

        M('GameFarm')->where(array('user_id' => $uid))->save(array('status' => json_encode($status)));
        return true;

    }
}