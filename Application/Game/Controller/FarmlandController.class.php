<?php

namespace Game\Controller;

/**
 * 农场管理
 * Class FarmlandController
 * @package Game\Controller
 */
class FarmlandController extends GameController
{
    /**
     * 开垦土地
     */
    public function reclaim()
    {
        // 检测用户土地是否开垦完成
        if ($this->farm_info['reclaim'] >= 24) {
            $this->apiReturn(72101, '农场土地已开垦完成');
        }

        // 获取当前用户开垦土地需等级及金币
        $land_info = M('GameFarmUpgrade')->field('level,money')->where(array('type' => 0, 'place' => $this->farm_info['reclaim']))->find();
        if (!$land_info) {
            $this->apiReturn(-1, '系统错误');
        }

        // 当前用户所拥有的金币
        $money = M('GameUser')->where(array('uid' => $this->uid))->getField('money');
        if ($money < $land_info['money']) {
            $this->apiReturn(72102, '金币不足，开垦土地需要金币' . $land_info['money']);
        }

        if (farm_exp_to_level($this->farm_info['exp']) < $land_info['level']) {
            $this->apiReturn(72103, '等级不足，开垦土地需满足' . $land_info['level'] . '级');
        }

        // 获取用户已经开垦土地数据
        $status = $this->farm_info['status'];

        // 删除不合理的农场土地
        foreach ($status as $place => $val) {
            if ($place >= $this->farm_info['reclaim']) {
                unset($status[$place]);
            }
        }

        $status[$this->farm_info['reclaim']] = array(
            'crop_id' => 0,
            'crop_status' => 0,
            'weed_num' => 0,
            'pest_num' => 0,
            'humidity' => 1,
            'health' => 100,
            'harvest_num' => 0,
            'output' => 0,
            'least_remain_output' => 0,
            'remain_output' => 0,
            'steal_record' => array(),
            'fertilize' => 0,
            'plant_time' => 0,
            'update_time' => NOW_TIME,
            'land_type' => 0
        );

        // 更新农场数据及花费金币
        M('GameUser')->where(array('uid' => $this->uid))->setDec('money', $land_info['money']);

        // 更新农场状态
        $data = array();
        $data['status'] = json_encode($status);
        $data['reclaim'] = array('EXP', 'reclaim + 1');
        M('GameFarm')->where(array('user_id' => $this->uid))->save($data);

        $return = array();
        $return['place'] = $this->farm_info['reclaim'];
        $return['reclaim'] = $this->farm_info['reclaim'] + 1;
        $return['money'] = $land_info['money'];

        $this->apiReturn(0, '开垦土地成功', $return);
    }

    /**
     * 土地升级===》升级红土地====》升级黑土地
     */
    public function upgrade()
    {
        // 检测升级土地类型
        if (!isset($this->data['type'])) {
            $this->apiReturn(72111, '升级土地参数错误');
        }

        $type = strtolower($this->data['type']);

        if ($type == 'red') {
            $this->_upgradeRedLand();
        } elseif ($type == 'black') {
            $this->_upgradeBlackLand();
        } else {
            $this->apiReturn(72111, '升级土地参数错误');
        }
    }

    /**
     * 升级黑土地
     */
    private function _upgradeBlackLand()
    {
        // 获取用户金币
        $money = M('GameUser')->where(array('uid' => $this->uid))->getField('money');

        // 获取用户当前等级
        $level = farm_exp_to_level($this->farm_info['exp']);

        // 获取当前农场土地状态
        $status = $this->farm_info['status'];

        // 黑土地详情
        $black_land = json_decode($this->farm_info['black_land'], true);

        // 升级黑土地位置
        foreach ($status as $k => $val) {
            if (isset($val['land_type']) && $val['land_type'] == 1) {
                $place = $k;
                break;
            }
        }

        if (!isset($place)) {
            $this->apiReturn(72141, '暂无待升级黑土地的红土地');
        }

        // 获取当前用户升级红土地需等级及金币
        $up_land_info = M('GameFarmUpgrade')->field('level,money')->where(array('type' => 2, 'place' => $place))->find();
        if (!$up_land_info) {
            $this->apiReturn(-1, '系统错误');
        }

        // 所需金币
        $use_money = isset($black_land['cd']) && (NOW_TIME < $black_land['cd']) ? $up_land_info['money'] * C('GAME_UPGRADE_BLACK_CD_MONEY') : $up_land_info['money'];

        // 返回数据
        $return = array();
        $return['money'] = $use_money;
        $return['level'] = $up_land_info['level'];
        $return['cd'] = isset($black_land['cd']) ? $black_land['cd'] : 0;
        $return['land'] = $place + 1;
        $return['propotion'] = C('GAME_UPGRADE_BLACK_CD_MONEY');
        // 检测用户金币、等级是否充足
        if ($use_money > $money && $level < $up_land_info['level']) {
            $this->apiReturn(72142, '您的等级与金币均不足！', $return);
        } elseif ($use_money > $money) {
            $this->apiReturn(72143, '您的金币不足！', $return);
        } elseif ($level < $up_land_info['level']) {
            $this->apiReturn(72144, '您的等级不足！', $return);
        }

        if (isset($this->data['confirm']) && $this->data['confirm'] == 1) {
            // 升级黑土地
            $status[$place]['land_type'] = 2;

            // 扣除用户金币
            M('GameUser')->where(array('uid' => $this->uid))->setDec('money', $up_land_info['money']);
            // 更新农场数据
            $map = array();
            $map['status'] = json_encode($status);
            $black_land['number'] = isset($black_land['number']) ? $black_land['number'] + 1 : 1;
            $black_land['cd'] = NOW_TIME + C('GAME_UPGRADE_BLACK_CD');
            $map['black_land'] = json_decode($black_land);

            M('GameFarm')->where(array('user_id' => $this->uid))->save($map);
            $this->apiReturn(0, '升级黑土地成功', $return);
        } else {
            // 检测升级状态
            $this->apiReturn(0, 'success', $return);
        }
    }

    /**
     * 升级红土地
     */
    private function _upgradeRedLand()
    {
        // 获取用户金币
        $money = M('GameUser')->where(array('uid' => $this->uid))->getField('money');

        // 获取用户当前等级
        $level = farm_exp_to_level($this->farm_info['exp']);

        // 获取当前农场土地状态
        $status = $this->farm_info['status'];

        // 获取升级红土地的位置
        foreach ($status as $k => $val) {
            if (!isset($val['land_type']) || $val['land_type'] == 0) {
                $place = $k;
                break;
            }
        }

        if (!isset($place)) {
            $this->apiReturn(-1, '土地参数错误');
        }

        if ($this->farm_info['red_land'] >= 24) {
            $this->apiReturn(72121, '恭喜您！您已经把所有土地升级成了红土地！');
        }

        if ($this->farm_info['reclaim'] < 18) {
            $this->apiReturn(72122, '对不起，需要开满前18块普通土地才能升级红土地！');
        }

        if ($this->farm_info['red_land'] == $this->farm_info['reclaim'] && $this->farm_info['reclaim'] < 24) {
            $this->apiReturn(72123, '请先开垦土地再升级红土地');
        }

        // 获取当前用户升级红土地需等级及金币
        $up_land_info = M('GameFarmUpgrade')->field('level,money')->where(array('type' => 1, 'place' => $place))->find();
        if (!$up_land_info) {
            $this->apiReturn(-1, '系统错误');
        }

        // 升级红土地
        if (isset($this->data['confirm']) && $this->data['confirm'] == 1) {
            // 执行升级红土地
            if ($money < $up_land_info['money'] || $level < $up_land_info['level']) {
                $this->apiReturn(72136, '请不要用非法手段升级红土地！');
            }

            $status[$place]['land_type'] = 1;
            if ($status[$place]['output'] > 0) {
                $status[$place]['output'] = intval($status[$place]['output'] * C('GAME_LAND_RED_CROP'));
                $status[$place]['remain_output'] = intval($status[$place]['remain_output'] * C('GAME_LAND_RED_CROP'));
            }
            // 扣除用户金币
            M('GameUser')->where(array('uid' => $this->uid))->setDec('money', $up_land_info['money']);
            // 更新农场数据
            $map = array();
            $map['status'] = json_encode($status);
            $map['red_land'] = array('EXP', 'red_land + 1');
            M('GameFarm')->where(array('user_id' => $this->uid))->save($map);

            $data = array();
            $data['direction'] = '恭喜你，红土地升级成功！';
            $data['place'] = $place;
            $data['output'] = $status[$place]['output'];
            $data['leavings'] = $status[$place]['remain_output'];
            $data['money'] = $up_land_info['money'];
            $this->apiReturn(0, '恭喜你，红土地升级成功！', $data);
        } else {
            // 检测是否可以升级红土地
            if ($money >= $up_land_info['money'] && $level >= $up_land_info['level']) {
                $data = array();
                $data['direction'] = '<font size="14"><b>您的土地升级后，将成为肥沃的红土地：</b></font>';
                $data['direction'] .= '<br />1.珍贵的<font color="#FF6600">高级作物</font>，只能种在红土地上！';
                $data['direction'] .= '<br />2.普通作物种在红土地增产<font color="#FF6600">' . ((C('GAME_LAND_RED_CROP') - 1) * 100) . '%</font>！';
                $data['direction'] .= '<br />3.红土地上进行摘取，有更丰富的<font color="#FF6600">奖励</font>，更多有趣的杯具洗具！';
                $data['direction'] .= '<br /><b><font color="#399200">本次升级第<font color="#FF6600">' . ($place + 1) . '</font>块地，';
                $data['direction'] .= '需要等级<font color="#FF6600">' . $up_land_info['level'] . '</font>级，';
                $data['direction'] .= '金币<font color="#FF6600">' . $up_land_info['money'] . '</font>！</font></b>';
                $data['money'] = $up_land_info['money'];
                $data['redland'] = $place + 1;
                $this->apiReturn(0, 'success', $data);
            } else {
                if ($money < $up_land_info['money'] && $level < $up_land_info['level']) {
                    $message = '您的等级与金币均不足！';
                    $code = 72131;
                } elseif ($money < $up_land_info['money']) {
                    $message = '您的金币不足！';
                    $code = 72132;
                } else {
                    $message = '您的等级不足！';
                    $code = 72133;
                }
                $data = array();
                $data['direction'] = '<font size="14"><b>您的土地升级后，将成为肥沃的红土地：</b></font>';
                $data['direction'] .= '<br />1.珍贵的<font color="#FF6600">高级作物</font>，只能种在红土地上！';
                $data['direction'] .= '<br />2.普通作物种在红土地增产<font color="#FF6600">' . ((C('GAME_LAND_RED_CROP') - 1) * 100) . '%</font>！';
                $data['direction'] .= '<br />3.红土地上进行摘取，有更丰富的<font color="#FF6600">奖励</font>，更多有趣的杯具洗具！';
                $data['direction'] .= '<br /><b><font color="#399200">本次升级第<font color="#FF6600">' . ($place + 1) . '</font>块地，';
                $data['direction'] .= '对不起，本次升级需要等级<font color="#FF6600">' . $up_land_info['level'] . '</font>级，';
                $data['direction'] .= '金币<font color="#FF6600">' . $up_land_info['money'] . '</font>，' . $message . '</font></b>';
                $data['money'] = $up_land_info['money'];
                $data['redland'] = $place + 1;
                $data['level'] = $up_land_info['level'];
                $this->apiReturn($code, $message, $data);
            }
        }
    }
}