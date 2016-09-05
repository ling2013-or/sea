<?php

namespace Game\Controller;

use Think\Model;

/**
 * 农场游戏农场操作日志模型
 * Class GameFarmLog
 * @package Game\Controller
 */
class GameFarmLog extends Model
{

    /**
     * 添加操作日志
     * @param int $type 操作类型
     * @param int $from_uid 操作人ID
     * @param int $farm_uid 所属农场用户ID
     * @param array $data 附属参数
     * @return bool
     */
    public function addLog($type, $from_uid, $farm_uid, $data = array())
    {
        switch ($type) {
            case 11:    // 购买狗狗
            case 10:    // 购买狗粮
            case 9:     // 购买化肥
            case 8:     // 购买种子
                $temp = array();
                $temp['farm_uid'] = $farm_uid;
                $temp['from_uid'] = $from_uid;
                $temp['type'] = $type;
                $temp['amount'] = $data['amount'];
                $temp['add_time'] = NOW_TIME;
                $temp['is_read'] = 0;
                $temp['crop_id'] = $data['crop_id'];
                $temp['money'] = $data['money'];
                $res = $this->add($temp);
                break;
            case 7:     // 卖种子, 批量添加
                $map = array();
                if (isset($data['amount'])) {
                    foreach ($data as $val) {
                        $temp = array();
                        $temp['farm_uid'] = $farm_uid;
                        $temp['from_uid'] = $from_uid;
                        $temp['type'] = 7;
                        $temp['amount'] = $val['amount'];
                        $temp['add_time'] = NOW_TIME;
                        $temp['is_read'] = 0;
                        $temp['crop_id'] = $val['crop_id'];
                        $temp['money'] = $data['money'];
                        $map[] = $temp;
                    }
                    $res = $this->addAll($map);
                } else {
                    $map['farm_uid'] = $farm_uid;
                    $map['from_uid'] = $from_uid;
                    $map['type'] = 7;
                    $map['amount'] = $data['amount'];
                    $map['add_time'] = NOW_TIME;
                    $map['is_read'] = 0;
                    $map['crop_id'] = $data['crop_id'];
                    $map['money'] = $data['money'];
                    $res = $this->add($map);
                }
                break;
            case 6:     // 卖果实, 批量添加
                $map = array();
                if (isset($data['amount'])) {
                    foreach ($data as $val) {
                        $temp = array();
                        $temp['farm_uid'] = $farm_uid;
                        $temp['from_uid'] = $from_uid;
                        $temp['type'] = 6;
                        $temp['amount'] = $val['amount'];
                        $temp['add_time'] = NOW_TIME;
                        $temp['is_read'] = 0;
                        $temp['crop_id'] = $val['crop_id'];
                        $temp['money'] = $data['money'];
                        $map[] = $temp;
                    }
                    $res = $this->addAll($map);
                } else {
                    $map['farm_uid'] = $farm_uid;
                    $map['from_uid'] = $from_uid;
                    $map['type'] = 6;
                    $map['amount'] = $data['amount'];
                    $map['add_time'] = NOW_TIME;
                    $map['is_read'] = 0;
                    $map['crop_id'] = $data['crop_id'];
                    $map['money'] = $data['money'];
                    $res = $this->add($map);
                }
                break;
            case 5:     // 种草
                $condition = array();
                $condition['from_uid'] = $from_uid;
                $condition['type'] = 5;
                $condition['farm_uid'] = $farm_uid;
                if ($info = $this->where($condition)->find()) {
                    $res = $this->where(array('id' => $info['id']))->save(array('add_time' => NOW_TIME));
                } else {
                    $data = array();
                    $data['farm_uid'] = $farm_uid;
                    $data['from_uid'] = $from_uid;
                    $data['type'] = 5;
                    $data['amount'] = 0;
                    $data['add_time'] = NOW_TIME;
                    $data['is_read'] = 0;
                    $data['crop_id'] = 0;
                    $data['money'] = 0;
                    $res = $this->add($data);
                }
                break;
            case 4:     // 狗咬日志
                $condition = array();
                $condition['from_uid'] = $from_uid;
                $condition['type'] = 4;
                $condition['farm_uid'] = $farm_uid;
                if ($info = $this->where($condition)->find()) {
                    $res = $this->where(array('id' => $info['id']))->save(array('add_time' => NOW_TIME, 'amount' => array('EXP', 'amount + 1'), 'money' => array('EXP', 'money + ' . $data['money'])));
                } else {
                    $rdata = array();
                    $rdata['farm_uid'] = $farm_uid;
                    $rdata['from_uid'] = $from_uid;
                    $rdata['type'] = 4;
                    $rdata['amount'] = 1;
                    $rdata['add_time'] = NOW_TIME;
                    $rdata['is_read'] = 0;
                    $rdata['crop_id'] = 0;
                    $rdata['money'] = $data['money'];
                    $res = $this->add($rdata);
                }
                break;
            case 3:     // 放虫
                $condition = array();
                $condition['from_uid'] = $from_uid;
                $condition['type'] = 3;
                $condition['farm_uid'] = $farm_uid;
                if ($info = $this->where($condition)->find()) {
                    $res = $this->where(array('id' => $info['id']))->save(array('add_time' => NOW_TIME));
                } else {
                    $data = array();
                    $data['farm_uid'] = $farm_uid;
                    $data['from_uid'] = $from_uid;
                    $data['type'] = 3;
                    $data['amount'] = 0;
                    $data['add_time'] = NOW_TIME;
                    $data['is_read'] = 0;
                    $data['crop_id'] = 0;
                    $data['money'] = 0;
                    $res = $this->add($data);
                }
                break;
            case 2:     // 除草，除虫，浇水
                $tend = array('weed' => 0, 'pest' => 1, 'water' => 2);
                switch ($tend[$data['type']]) {
                    case 0:
                        $counts = '1:0:0';
                        break;
                    case 1:
                        $counts = '0:1:0';
                        break;
                    case 2:
                        $counts = '0:0:1';
                        break;
                }
                $condition = array();
                $condition['from_uid'] = $from_uid;
                $condition['type'] = 2;
                $condition['farm_uid'] = $farm_uid;
                $condition['add_time'] = array('GT', NOW_TIME - 3600);
                $info = $this->where($condition)->find();
                if (is_array($info) && !empty($info)) {
                    if (false !== strpos($info['counts'], ':')) {
                        $counts = explode(':', $info['counts']);
                        $counts[$tend[$data['type']]]++;
                        $counts = implode(':', $counts);
                    }
                    $data = array();
                    $data['amount'] = array('EXP', 'amount + 1');
                    $data['counts'] = $counts;
                    $data['add_time'] = NOW_TIME;
                    $res = $this->where(array('id' => $info['id']))->save($data);
                } else {
                    $data = array();
                    $data['farm_uid'] = $farm_uid;
                    $data['from_uid'] = $from_uid;
                    $data['type'] = 2;
                    $data['amount'] = 1;
                    $data['counts'] = $counts;
                    $data['add_time'] = NOW_TIME;
                    $data['is_read'] = 0;
                    $data['crop_id'] = 0;
                    $data['money'] = 0;
                    $res = $this->add($data);
                }
                break;
        }

        return $res !== false ? true : false;
    }
}