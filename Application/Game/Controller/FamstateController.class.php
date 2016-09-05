<?php

namespace Game\Controller;

/**
 * 农场土地操作管理
 * 注：全部是单一操作土地
 * Class FarmstateController
 * @package Game\Controller
 */
class FarmstateController extends GameController
{

    /**
     * 访问自己或者别人农场详情
     */
    public function info()
    {
        $data = array();

        // 用户资料
        $user_info = M('GameUser')->field(true)->where(array('uid' => $this->owner_id))->find();
        $data['username'] = isset($user_info['username']) && !empty($user_info['username']) ? $user_info['username'] : '农场玩家';

        // 农田参数
        foreach ($this->owner_farm['status'] as $key => $value) {
            // 修复可能出现的错误
            // TODO
        }

        // 装饰参数

        // 狗狗参数

        // 广告牌

        // 新手任务

        // 随机刷草、虫、旱
        D('Farm', 'Logic')->bad($this->owner_id, $this->owner_farm['status'], $this->owner_farm['rand_time'], $user_info['weather']);

        // 返回数据

    }

    /**
     * 农场操作
     */
    public function operate()
    {
        // 作物除草 clearweed

        // 作物杀虫 spraying

        // 作物施肥 fertilize

        // 作物浇水 water

        // 恶意放虫 pest

        // 恶意种草 scatterseed

        // 作物输出 getoutput

        // 收获作物 harvest

        // 播种作物 planting

        // 翻土作物 scarify

        // 偷取作物 scrounge
    }

    /**
     * 偷取作物
     */
    public function scrounge()
    {

        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72001, '请选择要操作的土地');
        }

        if (!$this->is_friend) {
            $this->apiReturn(72002, '只能偷取好友作物');
        }

        $place = abs(intval($this->data['place']));

        if (!isset($this->owner_farm['status'][$place])) {
            $this->apiReturn(72003, '此土地不存在');
        }

        $status = $this->farm_info['status'];

        // TODO 偷取作物时，自己金币限制
        $money = M('GameUser')->where(array('uid' => $this->uid))->getField('money');
        if ($money < 300) {
            $this->apiReturn(72004, '您身上少300　金币不能摘取！');
        }

        // 检测好友有没有种作物
        if (empty($status[$place]['crop_id'])) {
            $this->apiReturn(72005, '尚未种植作物');
        }
        // 获取作物详情
        $crop_info = M('GameCrop')->where(array('crop_id' => $status[$place]['crop_id']))->find();

        if ((NOW_TIME - $status[$place]['plant_time']) < $crop_info['five'] || $status[$place]['remain_output'] <= $status[$place]['least_remain_output']) {
            $this->apiReturn(72006, '没有菜可以摘取！');
        }

        // 检测是否已经偷取过作物
        if (in_array($this->uid, array_flip($status[$place]['steal_record']))) {
            $this->apiReturn(72006, '您已经摘取过此地作物');
        }

        $steal_record = $status[$place]['steal_record'];

        $farm_dog = json_decode($this->owner_farm['dog'], true);

        if ($this->owner_farm['dog'] > NOW_TIME) {
            // 狗狗工作
            // 获取狗狗品种
            $condition = array();
            $condition['user.user_id'] = $this->owner_id;
            $condition['user.status'] = 1;
            $condition['tool.type'] = 3;
            $dog_info = M('GameUserDog')->where($condition)->find();
            if (is_array($dog_info) && !empty($dog_info)) {
                // TODO
                if (10 - mt_rand(1, 10) < 2 + 4 * ($dog_info['chance'])) {
                    $dog_money = mt_rand(40, 80);
                    $steal_record[$this->uid] = 0;
                    // 狗咬日志
                    D('GameFarmLog')->addLog(4, $this->uid, $this->owner_id, array('money' => $dog_money));
                    $dog_str = "<font color='#369937'>【农场语录】<br>路边的野花不要采，别人的果实不要摘！</font><br>你在摘取过程中被他的狗狗发现，在逃跑过程中丢失<b><font color='#FF6600'> " . $dog_money . " </font></b>金币。";
                    $return = array();
                    $return['direction'] = $dog_str;
                    $return['place'] = $place;
                    $return['harvest'] = 0;
                    $return['money'] = $dog_money;
                    $this->apiReturn(72006, '被狗发现了', $return);
                }
            }
        }

        // 偷取个数
        $rand_number = mt_rand(1, 100);
        if ($rand_number <= 50) {
            $steal_record[$this->uid] = 1;
        } elseif ($rand_number <= 70) {
            $steal_record[$this->uid] = 2;
        } elseif ($rand_number <= 80) {
            $steal_record[$this->uid] = 3;
        } elseif ($rand_number <= 95) {
            $steal_record[$this->uid] = 4;
        } elseif ($rand_number <= 100) {
            $steal_record[$this->uid] = 5;
        }

        // 红土地悲喜 TODO 金币奖励/扣除数量
        if ($status[$place]['land_type'] == 1) {
            if ($rand_number < 20) {

            } elseif ($rand_number < 40) {

            }
        }

        // TODO
    }

    /**
     * 翻土
     */
    public function scarify()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72191, '请选择要操作的土地');
        }

        if ($this->is_friend) {
            $this->apiReturn(72192, '只能操作自己土地');
        }

        $place = abs(intval($this->data['place']));

        if (!isset($this->farm_info['status'][$place])) {
            $this->apiReturn(72193, '此土地不存在');
        }

        $status = $this->farm_info['status'];

        if (!isset($this->data['crop_status']) || (intval($this->data['crop_status']) == 7 && $status[$place]['crop_status'] < 7)) {
            $this->apiReturn(72194, '请求已经过期，请重进农场');
        }

        if ($status[$place]['crop_id'] <= 0) {
            $this->apiReturn(72195, '已经锄过这块地了哟');
        }

        $status[$place]['crop_id'] = 0;
        $status[$place]['crop_status'] = 0;
        $status[$place]['weed_num'] = 0;
        $status[$place]['pest_num'] = 0;
        $status[$place]['humidity'] = 1;
        $status[$place]['health'] = 100;
        $status[$place]['harvest_num'] = 0;
        $status[$place]['output'] = 0;
        $status[$place]['least_remain_output'] = 0;
        $status[$place]['remain_output'] = 0;
        $status[$place]['steal_record'] = array();
        $status[$place]['fertilize'] = 0;
        $status[$place]['weed'] = array();
        $status[$place]['pest'] = array();
        $status[$place]['land_type'] = intval($status[$place]['land_type']);
        $status[$place]['plant_time'] = 0;
        $status[$place]['update_time'] = 0;

        if ($status[$place]['crop_status'] == 7 && intval($this->data['crop_status']) == 7) {
            // 有经验
            $exp = C('GAME_SCARIFY_EXP');
            // 更新农场状态
            M('GameFarm')->where(array('user_id' => $this->uid))->save(array('status' => json_encode($status), 'exp' => array('EXP', 'exp + ' . $exp)));

            // 检测是否存在升级奖励
            $level_up = D('Farm', 'Logic')->levelReward($this->uid);
        } else {
            // 无经验
            $exp = 0;
            $level_up = false;
        }

        $data = array();        // 返回数据
        $data['place'] = $place;
        $data['exp'] = $exp;
        $data['level_up'] = $level_up;
        $this->apiReturn(0, '翻地成功', $data);
    }

    /**
     * 播种作物
     */
    public function planting()
    {

        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72181, '请选择要操作的土地');
        }

        if ($this->is_friend) {
            $this->apiReturn(72182, '只能操作自己土地');
        }

        $place = abs(intval($this->data['place']));

        if (!isset($this->farm_info['status'][$place])) {
            $this->apiReturn(72183, '此土地不存在');
        }

        $status = $this->farm_info['status'];
        if ($status[$place]['crop_id'] != 0) {
            $this->apiReturn(72184, '此土地已种植作物');
        }

        if (!isset($this->data['crop_id']) || empty($this->data['crop_id'])) {
            $this->apiReturn(72185, '请选择要种植的作物');
        }

        // 检测作物是否存在
        $condition = array();
        $condition['user.user_id'] = $this->uid;
        $condition['user.crop_id'] = $this->data['crop_id'];
        $condition['user.amount'] = array('GT', 0);
        $crop_info = M('GameUserCrop')
            ->alias('user')
            ->field('crop.*,user.amount')
            ->join('__GAME_CROP__ AS crop ON user.crop_id = crop.crop_id')
            ->where($condition)
            ->find();

        if ($crop_info) {
            $this->apiReturn(72186, '种子不存在');
        }

        if ($status[$place]['land_type'] == 0 && $crop_info['type'] == 1) {
            $this->apiReturn(72187, '红种子只能种在红土地上！');
        } elseif ($status[$place]['land_type'] != 2 && $crop_info['type'] == 2) {
            $this->apiReturn(72188, '黑种子只能种在黑土地上！');
        }

        $status[$place]['crop_id'] = $this->data['crop_id'];
        $status[$place]['crop_status'] = 1;
        $status[$place]['weed_num'] = 0;
        $status[$place]['pest_num'] = 0;
        $status[$place]['humidity'] = 1;
        $status[$place]['health'] = 100;
        $status[$place]['harvest_num'] = 0;
        $status[$place]['output'] = 0;
        $status[$place]['least_remain_output'] = 0;
        $status[$place]['remain_output'] = 0;
        $status[$place]['steal_record'] = array();
        $status[$place]['fertilize'] = 0;
        $status[$place]['weed'] = array();
        $status[$place]['pest'] = array();
        $status[$place]['land_type'] = intval($status[$place]['land_type']);
        $status[$place]['plant_time'] = NOW_TIME;
        $status[$place]['update_time'] = NOW_TIME;

        // 更新用户种子
        M('GameUserCrop')->where(array('user_id' => $this->uid))->setDec('amount');

        // 更新农场状态
        M('GameFarm')->where(array('user_id' => $this->uid))->save(array('status' => json_encode($status), 'exp' => array('EXP', 'exp + ' . C('GAME_PLANT_EXP'))));

        // 检测是否存在升级奖励
        $level_up = D('Farm', 'Logic')->levelReward($this->uid);

        $data = array();        // 返回数据
        $data['place'] = $place;
        if ($status[$place]['land_type'] == 1 && $crop_info['type'] != 1) {
            $data['direction'] = '红土地,普通作物将增产' . ((C('GAME_LAND_RED_CROP') - 1) * 100) . '%！';
        } elseif ($status[$place]['land_type'] == 2 && $crop_info['type'] != 2) {
            $data['direction'] = '黑土地,增产' . ((C('GAME_LAND_BLACK_CROP') - 1) * 100) . '%，加速' . (C('GAME_LAND_BLACK_DEC_TIME') * 100) . '%!';
        } else {
            $data['direction'] = '';
        }
        $data['exp'] = C('GAME_TEND_EXP');
        $data['level_up'] = $level_up;
        $this->apiReturn(0, '种植成功', $data);
    }

    /**
     * 收获作物
     */
    public function harvest()
    {

        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72171, '请选择要操作的土地');
        }

        if ($this->is_friend) {
            $this->apiReturn(72172, '只能收获自己种植的作物');
        }

        $place = abs(intval($this->data['place']));

        if (!isset($this->farm_info['status'][$place])) {
            $this->apiReturn(72173, '此土地不存在');
        }
        $status = $this->farm_info['status'];
        if ($status[$place]['crop_status'] != 6 || $status[$place]['remain_output'] < 1) {
            $this->apiReturn(72174, '这块地没东西可收获！', array('place' => $place, 'harvest' => 0));
        }

        $output = $status[$place]['remain_output'];
        $status[$place]['weed_num'] = 0;
        $status[$place]['pest_num'] = 0;
        $status[$place]['humidity'] = 1;
        $status[$place]['health'] = 100;
        $status[$place]['output'] = 0;
        $status[$place]['least_remain_output'] = 0;
        $status[$place]['remain_output'] = 0;
        $status[$place]['steal_record'] = array();
        $status[$place]['fertilize'] = 0;
        $status[$place]['plant_time'] = 0;
        $status[$place]['update_time'] = NOW_TIME;

        // 作物信息
        $crop_info = M('GameCrop')->field(true)->where(array('crop_id' => $status[$place]['crop_id']))->find();
        if ($status[$place]['harvest_num'] + 1 == $crop_info['harvest_num']) {
            $status[$place]['crop_status'] = 7;
            $status[$place]['harvest_num'] = 0;
        } else {
            $status[$place]['crop_status'] = 6;
            $status[$place]['harvest_num'] += 1;
            $status[$place]['plant_time'] = NOW_TIME - $crop_info['three'];
        }
        $status[$place]['land_type'] = intval($status[$place]['land_type']);

        // 红土地事件
        if ($status[$place]['land_type'] == 1) {
            // TODO
        }

        // 更新农场信息
        M('GameFarm')->where(array('user_id' => $this->uid))->save(array('exp' => array('EXP', 'exp + ' . $crop_info['exp']), 'status' => json_encode($status)));

        // 升级
        $level_up = D('Farm', 'Logic')->levelReward($this->uid);

        // 更新仓库
        $crop = M('GameFarmCrop')->where(array('user_id' => $this->uid, 'crop_id' => $status[$place]['crop_id']))->find();
        if ($crop) {
            M('GameFarmCrop')->where(array($crop['id']))->setInc('amount', $output);
        } else {
            $data = array();
            $data['user_id'] = $this->uid;
            $data['crop_id'] = $status[$place]['crop_id'];
            $data['amount'] = $output;
            M('GameFarmCrop')->add($data);
        }

        $return = array();
        $return['place'] = $place;
        $return['exp'] = $crop_info['exp'];
        $return['harvest'] = $output;
        $return['level_up'] = $level_up;
        $return['status'] = $status[$place];

        $this->apiReturn(0, '收获成功', $return);
    }

    /**
     * 作物输出
     */
    public function getoutput()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72161, '请选择要操作的土地');
        }

        $place = abs(intval($this->data['place']));


        if (!isset($this->owner_farm['status'][$place])) {
            $this->apiReturn(72162, '此土地不存在');
        }
        $status = $this->owner_farm['status'];

        if (empty($status[$place]['crop_id'])) {
            $this->apiReturn(72163, '土地尚未种植农作物');
        }
        // 种子信息
        $crop_info = M('GameCrop')->field(true)->where(array('crop_id' => $status[$place]['crop_id']))->find();

        $grow_time = NOW_TIME - $status[$place]['plant_time'];
        if (isset($status[$place]['land_type']) && $status[$place]['land_type'] == 2) {
            // 黑土地减少成熟时间
            $grow_time = NOW_TIME - $status[$place]['plant_time'] - C('GAME_LAND_BLACK_DEC_TIME') * $crop_info['growth_cycle'];
        }

        if ($grow_time < $crop_info['growth_cycle']) {
            $this->apiReturn(72164, '作物尚未成熟');
        }

        $status[$place]['crop_status'] = 6;
        $status[$place]['weed_num'] = 0;
        $status[$place]['pest_num'] = 0;
        $status[$place]['humidity'] = 1;
        $status[$place]['least_remain_output'] = floor($crop_info['expect_output'] * C('GAME_LEAST_REMAIN_OUTPUT'));
        $status[$place]['land_type'] = isset($status[$place]['land_type']) ? intval($status[$place]['land_type']) : 0;
        if ($status[$place]['land_type'] == 1 && $crop_info['type'] != 1) {
            $output = $crop_info['expect_output'] * C('GAME_LAND_RED_CROP');
        } elseif ($status[$place]['land_type'] == 2 && $crop_info['type'] != 2) {
            $output = $crop_info['expect_output'] * C('GAME_LAND_BLACK_CROP');
        } else {
            $output = $crop_info['expect_output'];
        }
        $status[$place]['output'] = $output;
        $status[$place]['remain_output'] = $output;

        // 更新作物生长状态
        $res = M('GameFarm')->where(array('user_id' => $this->uid))->save(array('status' => json_encode($status)));
        if (false === $res) {
            $this->apiReturn(72165, '更新数据失败');
        }
        $data = array(
            'place' => $place,
            'status' => $status[$place],
        );
        $this->apiReturn(0, '作物已成熟', $data);

    }

    /**
     * 恶意种草
     */
    public function scatterseed()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72151, '请选择要操作的土地');
        }

        if (!$this->is_friend) {
            $this->apiReturn(72152, '只能向好友土地上种草');
        }

        // 检查使坏次数
        if ($this->farm_info['bad_num'] < 1) {
            $this->apiReturn(72153, '您今天使坏的次数已达到' . C('GAME_BAD_NUM') . '次');
        }
        $place = abs(intval($this->data['place']));

        if (!isset($this->owner_farm['status'][$place])) {
            $this->apiReturn(72154, '此土地不存在');
        }
        $status = $this->owner_farm['status'];


        if ($status[$place]['weed_num'] > 3) {
            $this->apiReturn(72155, '这块土地无法种草啦！');
        }

        $status[$place]['weed_num'] += 1;
        $this->farm_info['bad_num'] -= 1;
        $status[$place]['weed'][$this->uid] = isset($status[$place]['weed'][$this->uid]) ? ($status[$place]['weed'][$this->uid] + 1) : 1;

        $return = array();
        $return['canbad'] = $this->farm_info['bad_num'];
        $return['direction'] = $this->owner_farm['tips']['weed_help'];
        $return['place'] = $place;
        $return['weed'] = $status[$place]['weed_num'];


        // 更新数据
        M('GameFarm')->where(array('user_id' => $this->uid))->save(array('bad_num' => $this->farm_info['bad_num']));
        M('GameFarm')->where(array('user_id' => $this->owner_id))->save(array('status' => json_encode($status)));

        // 种草日志
        D('GameFarmLog')->add(5, $this->uid, $this->owner_id);

        $this->apiReturn(0, $this->owner_farm['tips']['weed_help'], $return);
    }

    /**
     * 恶意放虫
     */
    public function pest()
    {

        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72141, '请选择要操作的土地');
        }

        if (!$this->is_friend) {
            $this->apiReturn(72142, '只能向好友土地上种草');
        }

        // 检查使坏次数
        if ($this->farm_info['bad_num'] < 1) {
            $this->apiReturn(72143, '您今天使坏的次数已达到' . C('GAME_BAD_NUM') . '次');
        }
        $place = abs(intval($this->data['place']));

        if (!isset($this->owner_farm['status'][$place])) {
            $this->apiReturn(72144, '此土地不存在');
        }
        $status = $this->owner_farm['status'];


        if ($status[$place]['pest_num'] > 3) {
            $this->apiReturn(72145, '这块土地无法放虫啦！');
        }

        $status[$place]['pest_num'] += 1;
        $this->farm_info['bad_num'] -= 1;
        $status[$place]['pest'][$this->uid] = isset($status[$place]['pest'][$this->uid]) ? ($status[$place]['pest'][$this->uid] + 1) : 1;

        $return = array();
        $return['canbad'] = $this->farm_info['bad_num'];
        $return['direction'] = $this->owner_farm['tips']['pest_help'];
        $return['place'] = $place;
        $return['pest'] = $status[$place]['pest_num'];


        // 更新数据
        M('GameFarm')->where(array('user_id' => $this->uid))->save(array('bad_num' => $this->farm_info['bad_num']));
        M('GameFarm')->where(array('user_id' => $this->owner_id))->save(array('status' => json_encode($status)));

        //放虫日志
        D('GameFarmLog')->add(3, $this->uid, $this->owner_id);

        $this->apiReturn(0, $this->owner_farm['tips']['pest_help'], $return);
    }

    /**
     * 作物浇水
     */
    public function water()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72131, '请选择要操作的土地');
        }

        $place = abs(intval($this->data['place']));

        if ($this->is_friend) {
            if (!isset($this->owner_farm['status'][$place])) {
                $this->apiReturn(72132, '此土地不存在');
            }
            $status = $this->owner_farm['status'];
        } else {
            if (!isset($this->farm_info['status'][$place])) {
                $this->apiReturn(72132, '此土地不存在');
            }
            $status = $this->farm_info['status'];
        }

        if ($status[$place]['humidity'] == 1) {
            $this->apiReturn(72133, '此土地不需要浇水');
        }

        // 更新状态
        $status[$place]['humidity'] = 1;

        // 更新用户经验，金币，以及状态

        M('GameFarm')->where(array('user_id' => $this->uid))->save(array('exp' => array('EXP', 'exp + ' . C('GAME_WATER_EXP'))));
        // 检测是否存在升级奖励
        $level_up = D('Farm', 'Logic')->levelReward($this->uid);

        // 更新用户金币
        M('GameUser')->where(array('uid' => $this->uid))->save(array('money' => array('EXP', 'money + ' . C('GAME_WATER_MONEY'))));

        M('GameFarm')->where(array('user_id' => $this->owner_id))->save(array('status' => json_encode($status)));

        if ($this->is_friend) {
            // 更新日志
            D('GameFarmLog')->addLog(2, $this->uid, $this->owner_id, array('type' => 'water'));
        }

        $data = array();
        $data['place'] = $place;
        $data['direction'] = $this->owner_farm['water_help'];
        $data['money'] = C('GAME_WATER_MONEY');
        $data['exp'] = C('GAME_WATER_EXP');
        $data['level_up'] = $level_up;
        $data['humidity'] = 1;

        $this->apiReturn(0, '浇水成功', $data);
    }

    /**
     * 作物施肥
     */
    public function fertilize()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72121, '请选择要操作的土地');
        }

        $place = abs(intval($this->data['place']));

        if (!isset($this->farm_info['status'][$place])) {
            $this->apiReturn(72122, '此土地不存在');
        }
        $status = $this->farm_info['status'];

        if ($this->is_friend) {
            $this->apiReturn(72123, '只能操作自己的土地');
        }

        // 检测作物是否需要施肥
        $info = $status[$place];
        if (empty($info['crop_id'])) {
            $this->apiReturn(72124, '不能对空土地进行操作');
        }

        if ($info['output'] > 0) {
            $this->apiReturn(72125, '作物已成熟，不需要施肥');
        }

        if (!isset($this->data['tool_id']) && empty($this->data['tool_id'])) {
            $this->apiReturn(72126, '请选择要使用的化肥');
        }

        $condition = array();
        $condition['user.user_id'] = $this->uid;
        $condition['user.tool_id'] = $this->data['tool_id'];
        $condition['user.num'] = array('GT', 0);
        $condition['tool.type'] = 1;
        $tools = M('GameUserTools')
            ->alias('user')
            ->field('tool.*,user.num')
            ->join('__GAME_TOOLS__ AS tool ON user.tool_id = tool.tool_id')
            ->where($condition)
            ->find();

        if ($tools) {
            $this->apiReturn(72127, '化肥未找到，请购买！');
        }

        // 作物已成长时间
        $grow_time = NOW_TIME - $status[$place]['plant_time'];
        $fertilize = 0;
        $crop_info = M('GameCrop')->field(true)->where(array('crop_id' => $status[$place]['crop_id']))->find();
        // 重新组合
        $crop_grow = array($crop_info['one'], $crop_info['two'], $crop_info['three'], $crop_info['four'], $crop_info['five'], $crop_info['six']);
        foreach ($crop_grow as $key => $time) {
            if ($time <= $grow_time) {
                $fertilize = $key + 1;
            }
        }

        if ($tools['used'] > 0) {
            if ($status[$place]['fertilize'] == ($fertilize + 1)) {
                $this->apiReturn(72128, '此阶段已使用过化肥');
            }
            $status[$place]['fertilize'] = $fertilize + 1;
        }

        $grow_time += $tools['effect'];
        if ($crop_grow[$fertilize] < $grow_time) {
            // 禁止跳阶段加速
            $grow_time = $crop_grow[$fertilize];
        }

        // 检测是否收获
        if ($grow_time >= $crop_info['growth_cycle']) {
            $status[$place]['crop_status'] = 6;
            $status[$place]['weed_num'] = 0;
            $status[$place]['pest_num'] = 0;
            $status[$place]['humidity'] = 1;
            $status[$place]['least_remain_output'] = floor($crop_info['expect_output'] * C('GAME_LEAST_REMAIN_OUTPUT'));
            $status[$place]['land_type'] = isset($status[$place]['land_type']) ? intval($status[$place]['land_type']) : 0;
            if ($status[$place]['land_type'] == 1 && $crop_info['type'] != 1) {
                $output = $crop_info['expect_output'] * C('GAME_LAND_RED_CROP');
            } elseif ($status[$place]['land_type'] == 2 && $crop_info['type'] != 2) {
                $output = $crop_info['expect_output'] * C('GAME_LAND_BLACK_CROP');
            } else {
                $output = $crop_info['expect_output'];
            }
            $status[$place]['output'] = $output;
            $status[$place]['remain_output'] = $output;
        }

        // 更新化肥数量
        M('GameUserTools')->where(array('user_id' => $this->uid, 'tool_id' => $tools['tool_id']))->setDec('num');

        // 更新作物生长状态
        M('GameFarm')->where(array('user_id' => $this->uid))->save(array('status' => json_encode($status)));

        $data = array(
            'place' => $place,
            'status' => $status[$place],
        );
        $this->apiReturn(0, '施肥成功', $data);
    }

    /**
     * 农场杀虫
     */
    public function spraying()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72111, '请选择要操作的土地');
        }

        $place = abs(intval($this->data['place']));

        $tend_num = $this->farm_info['farm_info'];

        if ($this->is_friend) {
            // 帮好友除草
            if (!isset($this->owner_farm['status'][$place])) {
                $this->apiReturn(72112, '此土地不存在');
            }
            $status = $this->owner_farm['status'];
        } else {
            if (!isset($this->farm_info['status'][$place])) {
                $this->apiReturn(72112, '此土地不存在');
            }
            $status = $this->farm_info['status'];
        }

        $info = $status[$place];
        // 获得当前土地上杂草数量
        if ($info['weed_num'] <= 0) {
            $this->apiReturn(72113, '这块地不需要杀虫啦！', array('place' => $place, 'pest' => 0));
        }

        if ($this->is_friend) {
            // 检测你在此地种草的数量
            $you_weed = array_key_exists($this->uid, $info['pest']) ? $info['pest'][$this->uid] : 0;
            if ($info['pest_num'] <= $you_weed) {
                $this->apiReturn(72114, '证据是不能毁灭的！', array('place' => $place, 'pest' => $info['pest_num']));
            }
        }

        foreach ($info['pest'] as $uid => $num) {
            if ($uid != $this->uid) {
                $info['pest'][$uid] -= 1;
                if ($info['pest'][$uid] < 1) {
                    unset($info['pest'][$uid]);
                }
                if (count($info['pest']) == 0) {
                    unset($info['pest']);
                }
                break;
            }
        }
        $info['pest_num'] -= 1;

        $data = array();        // 返回数据
        $data['place'] = $place;
        $data['money'] = C('GAME_TEND_MONEY');
        $data['pest'] = $info['pest_num'];
        if ($tend_num < 1) {
            // 限制除草
            $data['exp'] = 0;
            $data['level_up'] = false;
        } else {
            // 更新农场经验
            M('GameFarm')->where(array('user_id' => $this->uid))->save(array('exp' => array('EXP', 'exp + ' . C('GAME_TEND_EXP'))));
            // 检测是否存在升级奖励
            $level_up = D('Farm', 'Logic')->levelReward($this->uid);
            $data['exp'] = C('GAME_TEND_EXP');
            $data['level_up'] = $level_up;
        }

        // 更新用户金币
        M('GameUser')->where(array('uid' => $this->uid))->save(array('money' => array('EXP', 'money + ' . C('GAME_TEND_MONEY'))));

        // 更新农场状态
        $status[$place] = $info;
        M('GameFarm')->where(array('user_id' => $this->owner_id))->save(array('status' => json_encode($status)));

        if ($this->is_friend) {
            // 更新日志
            D('GameFarmLog')->addLog(2, $this->uid, $this->owner_id, array('type' => 'pest'));
        }

        // 返回结果
        $tips = json_decode($this->owner_farm['tips'], true);
        $this->apiReturn(0, $tips['pest_help'], $data);
    }

    /**
     * 农场除草
     */
    public function clearweed()
    {
        // 获取要操作的土地
        if (!isset($this->data['place'])) {
            $this->apiReturn(72101, '请选择要操作的土地');
        }

        $place = abs(intval($this->data['place']));

        $tend_num = $this->farm_info['farm_info'];

        if ($this->is_friend) {
            // 帮好友除草
            if (!isset($this->owner_farm['status'][$place])) {
                $this->apiReturn(72102, '此土地不存在');
            }
            $status = $this->owner_farm['status'];
        } else {
            if (!isset($this->farm_info['status'][$place])) {
                $this->apiReturn(72102, '此土地不存在');
            }
            $status = $this->farm_info['status'];
        }

        $info = $status[$place];
        // 获得当前土地上杂草数量
        if ($info['weed_num'] <= 0) {
            $this->apiReturn(72103, '这块地不需要除草啦！', array('place' => $place, 'weed' => 0));
        }

        if ($this->is_friend) {
            // 检测你在此地种草的数量
            $you_weed = array_key_exists($this->uid, $info['weed']) ? $info['weed'][$this->uid] : 0;
            if ($info['weed_num'] <= $you_weed) {
                $this->apiReturn(72104, '证据是不能毁灭的！', array('place' => $place, 'weed' => $info['weed_num']));
            }
        }

        foreach ($info['weed'] as $uid => $num) {
            if ($uid != $this->uid) {
                $info['weed'][$uid] -= 1;
                if ($info['weed'][$uid] < 1) {
                    unset($info['weed'][$uid]);
                }
                if (count($info['weed']) == 0) {
                    unset($info['weed']);
                }
                break;
            }
        }
        $info['weed_num'] -= 1;

        $data = array();        // 返回数据
        $data['place'] = $place;
        $data['money'] = C('GAME_TEND_MONEY');
        $data['weed'] = $info['weed_num'];
        if ($tend_num < 1) {
            // 限制除草
            $data['exp'] = 0;
            $data['level_up'] = false;
        } else {
            // 更新农场经验
            M('GameFarm')->where(array('user_id' => $this->uid))->save(array('exp' => array('EXP', 'exp + ' . C('GAME_TEND_EXP'))));
            // 检测是否存在升级奖励
            $level_up = D('Farm', 'Logic')->levelReward($this->uid);
            $data['exp'] = C('GAME_TEND_EXP');
            $data['level_up'] = $level_up;
        }

        // 更新用户金币
        M('GameUser')->where(array('uid' => $this->uid))->save(array('money' => array('EXP', 'money + ' . C('GAME_TEND_MONEY'))));

        // 更新农场状态
        $status[$place] = $info;
        M('GameFarm')->where(array('user_id' => $this->owner_id))->save(array('status' => json_encode($status)));

        if ($this->is_friend) {
            // 更新日志
            D('GameFarmLog')->addLog(2, $this->uid, $this->owner_id, array('type' => 'weed'));
        }

        // 返回结果
        $tips = json_decode($this->owner_farm['tips'], true);
        $this->ajaxReturn(0, $tips['weed_help'], $data);
    }
}