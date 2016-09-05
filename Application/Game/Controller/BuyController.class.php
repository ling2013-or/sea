<?php

namespace Game\Controller;

/**
 * 农场游戏购买接口管理
 * Class BuyController
 * @package Game\Controller
 */
class BuyController extends GameController
{

    /**
     * 商场购买
     */
    public function shop()
    {
        if (!isset($this->data['category'])) {
            $this->apiReturn(75301, '请选择要购买的商品分类');
        }
        $category_arr = array('seed', 'tool', 'decorat');
        $category = strtolower($this->data['type']);
        if (!in_array($category, $category_arr)) {
            $this->apiReturn(75301, '请选择要购买的商品分类');
        }

        if (!isset($this->data['id']) || intval($this->data['id']) <= 0) {
            $this->apiReturn(75302, '请选择择要购买的商品');
        }
        $id = intval($this->data['id']);

        if (!isset($this->data['num']) || intval($this->data['num']) <= 0) {
            $this->apiReturn(75303, '请选择择要购买的数量');
        }
        $num = intval($this->data['num']);
        // 获取用户金币
        $user_money = M('GameUser')->where(array('uid' => $this->uid))->getField('money');
        $return = array();  // 返回数据
        if ($category == 'seed') {
            // 购买种子
            $condition = array();
            $condition['crop_id'] = intval($this->data['id']);
            $condition['is_hidden'] = 0;
            $condition['status'] = 1;
            $field = 'crop_id,crop_name,plant_level,buy_price';
            $info = M('GameCrop')->field($field)->where($condition)->find();
            if (!$info) {
                $this->apiReturn(75304, '商品未找到');
            }
            // 获取用户当前等级
            $user_level = farm_exp_to_level($this->farm_info['exp']);
            // 检测用户是否可以购买
            if ($user_level < $info['plant_level']) {
                $this->apiReturn(75305, '您的等级无法购买该种子');
            }

            $buy_money = $info['buy_price'] * $num;
            if ($user_money < $buy_money) {
                $this->apiReturn(75306, '您的金币不足');
            }

            // 更新用户数据库
            $seed = M('GameUserSeed')->where(array('user_id' => $this->uid, 'crop_id' => $id))->find();
            if ($seed) {
                M('GameUserSeed')->where(array('id' => $seed['id']))->setInc('amount', $num);
            } else {
                M('GameUserSeed')->add(array('user_id' => $this->uid, 'crop_id' => $id, 'is_lock' => 0, 'amount' => $num));
            }
            // 购买日志
            D('GameFarmLog')->addLog(8, $this->uid, $this->uid, array('amount' => $num, 'crop_id' => $id, 'money' => $buy_money));
            // 更新用户金币
            M('GameUser')->where(array('uid' => $this->uid))->setDec('money', $buy_money);

            $return['money'] = $buy_money;
        } elseif ($category == 'tool') {
            // 购买道具
            $condition = array();
            $condition['status'] = 1;
            $condition['tool_id'] = $id;
            $info = M('GameCrop')->where($condition)->find();
            if (!$info) {
                $this->apiReturn(75304, '商品未找到');
            }

            //
            if ($info['type'] == 3) {
                // 狗狗只能购买一只
                $buy_money = $info['price'];
            } else {
                $buy_money = $info['price'] * $num;
            }
            if ($user_money < $buy_money) {
                $this->apiReturn(75306, '您的金币不足');
            }

            $return['money'] = $buy_money;

            if ($info['type'] == 1) {
                // 化肥
                $amount = $info['sell_num'] * $num;
                // 更新用户数据库
                $seed = M('GameUserTools')->where(array('user_id' => $this->uid, 'tool_id' => $id))->find();
                if ($seed) {
                    M('GameUserSeed')->where(array('id' => $seed['id']))->setInc('num', $amount);
                } else {
                    M('GameUserSeed')->add(array('user_id' => $this->uid, 'tool_id' => $id, 'num' => $amount));
                }
                // 购买日志
                D('GameFarmLog')->addLog(9, $this->uid, $this->uid, array('amount' => $num, 'crop_id' => $id, 'money' => $buy_money));

            } elseif ($info['type'] == 2) {
                // 狗粮
                $effect_time = $info['effect_time'] * $num;
                $dog_feed_time = $this->farm_info['dog'] < NOW_TIME ? (NOW_TIME + $effect_time) : ($this->farm_info['dog'] + $effect_time);

                // 更新狗粮时间
                M('GameFarm')->where(array('user_id' => $this->uid))->save(array('dog' => $dog_feed_time));

                // 购买日志
                D('GameFarmLog')->addLog(10, $this->uid, $this->uid, array('amount' => $num, 'crop_id' => $id, 'money' => $buy_money));

            } elseif ($info['type'] == 3) {
                // TODO 狗狗有效期（以后再说）
                // 狗狗   只能购买一只
                // 检测是否已经购买过此狗
                if (M('GameUserGog')->where(array('user_id' => $this->uid, 'tool_id' => $id))->find()) {
                    $this->apiReturn(75307, '你已经拥有了这条狗了');
                }
                // 取消以默认狗狗
                M('GameUserGog')->where(array('user_id' => $this->uid))->save(array('status'=>0));
                $save = array();
                $save['tool_id'] = $id;
                $save['user_id'] = $this->uid;
                $save['status'] = 1;
                M('GameUserGog')->add($save);

                // 处理狗粮
                $effect_time = (int)C('GAME_BUY_DOG_FEED_TIME');
                if ($effect_time > 0) {
                    $dog_feed_time = $this->farm_info['dog'] < NOW_TIME ? (NOW_TIME + $effect_time) : ($this->farm_info['dog'] + $effect_time);
                    // 更新狗粮时间
                    M('GameFarm')->where(array('user_id' => $this->uid))->save(array('dog' => $dog_feed_time));
                }
                D('GameFarmLog')->addLog(11, $this->uid, $this->uid, array('amount' => 1, 'crop_id' => $id, 'money' => $buy_money));
            } else {
                // 暂无此商品
                $this->apiReturn(-1, '暂无此处理');
            }

            // 更新用户金币
            M('GameUser')->where(array('uid' => $this->uid))->setDec('money', $buy_money);
        } else {
            // 购买装饰
            // TODO
        }

        $this->apiReturn(0, '购买成功', $return);
    }
}